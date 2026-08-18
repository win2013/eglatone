#!/bin/sh
#
# check-press-links.sh - validate the Press Mentions strip
#
# For every entry in the "Publications" setting it checks:
#   - the article URL responds (not 404 / dead / redirect loop)
#   - the logo image responds AND is actually an image
#   - the logo is hosted on your own domain, not hotlinked from someone else's
#
# Reads the setting straight from WordPress via WP-CLI. If WP-CLI is not
# available it falls back to scraping the rendered homepage.
#
# Usage:
#   ./check-press-links.sh                      # WP-CLI, run from the WP root
#   ./check-press-links.sh https://example.com  # scrape a live page instead
#
# Run from the WordPress install directory (where wp-config.php lives).

set -u

SITE="${1:-}"
OWN_DOMAIN="${OWN_DOMAIN:-edwinhernandez.com}"

if ! command -v curl >/dev/null 2>&1; then
	echo "Error: curl is required." >&2
	exit 1
fi

TMP="./.press-check.$$"
trap 'rm -f "$TMP" "$TMP.urls"' EXIT

# ------------------------------------------------------------------ gather
if [ -z "$SITE" ] && command -v wp >/dev/null 2>&1 && wp core is-installed >/dev/null 2>&1; then
	echo "Source: WP-CLI (theme mod eglatone_press_items)"
	wp theme mod get eglatone_press_items --format=json 2>/dev/null \
		| sed 's/^"//; s/"$//; s/\\n/\n/g; s/\\"/"/g; s/\\\//\//g' > "$TMP"

	if [ ! -s "$TMP" ]; then
		echo "  (setting is empty - the theme is using its built-in defaults)"
		echo "  Falling back to scraping the homepage."
		SITE=$(wp option get siteurl 2>/dev/null)
	fi
fi

if [ -n "$SITE" ] || [ ! -s "$TMP" ]; then
	[ -z "$SITE" ] && { echo "Error: pass a site URL, e.g. $0 https://example.com" >&2; exit 1; }

	echo "Source: scraping $SITE"

	curl -sL --max-time 20 -A 'Mozilla/5.0 press-link-check' "$SITE" \
		| tr '<' '\n' \
		| grep -iE 'a class="press-link"|img ' \
		| sed -n 's/.*href="\([^"]*\)".*/LINK \1/p; s/.*src="\([^"]*\)".*/IMG \1/p' \
		> "$TMP.urls"

	# Rebuild pseudo-entries: an IMG line belongs to the LINK line before it.
	awk '
		/^LINK/ { link = $2; next }
		/^IMG/  { if (link != "") { print "scraped | " $2 " | " link; link = "" } }
	' "$TMP.urls" > "$TMP"
fi

if [ ! -s "$TMP" ]; then
	echo "Nothing to check - no press entries found." >&2
	exit 1
fi

# ------------------------------------------------------------------- check
status_of() {
	curl -sL -o /dev/null -w '%{http_code}' --max-time 15 \
		-A 'Mozilla/5.0 press-link-check' "$1" 2>/dev/null
}

ctype_of() {
	curl -sIL -o /dev/null -w '%{content_type}' --max-time 15 \
		-A 'Mozilla/5.0 press-link-check' "$1" 2>/dev/null
}

echo ""
printf '%-26s %-6s %-6s %s\n' "PUBLICATION" "LINK" "LOGO" "NOTES"
printf '%s\n' "-------------------------------------------------------------------"

PROBLEMS=0
COUNT=0

while IFS= read -r line; do
	[ -n "$line" ] || continue

	name=$(printf '%s' "$line" | awk -F'|' '{print $1}' | sed 's/^ *//; s/ *$//')
	img=$(printf  '%s' "$line" | awk -F'|' '{print $2}' | sed 's/^ *//; s/ *$//')
	url=$(printf  '%s' "$line" | awk -F'|' '{print $3}' | sed 's/^ *//; s/ *$//')

	[ -n "$name$img$url" ] || continue
	COUNT=$((COUNT + 1))

	notes=""

	if [ -n "$url" ]; then
		lcode=$(status_of "$url")
		case "$lcode" in
			2*|3*) ;;
			000)   notes="$notes link-unreachable"; PROBLEMS=$((PROBLEMS + 1)) ;;
			*)     notes="$notes link-HTTP-$lcode"; PROBLEMS=$((PROBLEMS + 1)) ;;
		esac
	else
		lcode="none"
		notes="$notes NO-LINK(not-clickable)"
		PROBLEMS=$((PROBLEMS + 1))
	fi

	if [ -n "$img" ]; then
		icode=$(status_of "$img")

		case "$icode" in
			2*|3*)
				ct=$(ctype_of "$img")
				case "$ct" in
					image/*) ;;
					*) notes="$notes not-an-image($ct)"; PROBLEMS=$((PROBLEMS + 1)) ;;
				esac
				;;
			000) notes="$notes image-unreachable"; PROBLEMS=$((PROBLEMS + 1)) ;;
			*)   notes="$notes image-HTTP-$icode"; PROBLEMS=$((PROBLEMS + 1)) ;;
		esac

		case "$img" in
			*"$OWN_DOMAIN"*) ;;
			http*) notes="$notes HOTLINKED" ;;
		esac
	else
		icode="none"
		notes="$notes no-logo(shows-text)"
	fi

	[ -n "$notes" ] || notes="ok"

	printf '%-26.26s %-6s %-6s %s\n' "$name" "$lcode" "$icode" "$notes"
done < "$TMP"

echo ""
echo "Checked $COUNT entries. Problems: $PROBLEMS"

if [ "$PROBLEMS" -gt 0 ]; then
	echo ""
	echo "NO-LINK        entry has no article URL, so it renders as plain text"
	echo "HOTLINKED      logo loads from someone else's server and can vanish;"
	echo "               upload it to your Media Library and use that URL"
	echo "link-HTTP-4xx  the article moved or was removed"
	exit 1
fi

echo "All press links and logos resolve."
exit 0
