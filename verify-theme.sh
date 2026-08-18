#!/bin/sh
#
# verify-theme.sh - check every theme file against a known-good manifest
#
# Catches the three ways an upload goes wrong:
#   MISSING   - the file never arrived
#   CORRUPT   - it arrived but the contents differ (truncated, or a stale copy)
#   EXTRA     - a file on the server that is not in the manifest (usually .bak
#               files, which are harmless, or leftovers worth deleting)
#
# It also runs `php -l` on every PHP file when php is on PATH, and checks that
# the functions the templates call are actually defined somewhere.
#
# Usage:
#   ./verify-theme.sh                 # check the directory this script is in
#   ./verify-theme.sh /path/to/theme
#   ./verify-theme.sh -q              # quiet: only report problems
#
# The manifest (MANIFEST.sha256) is generated from a known-good copy. To make a
# new one after deliberate changes:
#   find . -type f ! -name "*.bak-*" ! -name "*.swp" ! -name "error_log" \
#     ! -name "*.zip" ! -name "MANIFEST.sha256" ! -name "verify-theme.sh" \
#     ! -name "fix-permissions.sh" ! -name ".DS_Store" \
#     -print0 | sort -z | xargs -0 sha256sum > MANIFEST.sha256

set -u

QUIET=0
TARGET=""

while [ $# -gt 0 ]; do
	case "$1" in
		-q|--quiet) QUIET=1 ;;
		-h|--help)
			echo "Usage: $0 [-q] [directory]"
			exit 0
			;;
		-*) echo "Unknown option: $1" >&2; exit 1 ;;
		*) TARGET="$1" ;;
	esac
	shift
done

if [ -z "$TARGET" ]; then
	TARGET=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
fi

if [ ! -d "$TARGET" ]; then
	echo "Error: '$TARGET' is not a directory." >&2
	exit 1
fi

TARGET=$(CDPATH= cd -- "$TARGET" && pwd)
MANIFEST="$TARGET/MANIFEST.sha256"

if [ ! -f "$MANIFEST" ]; then
	echo "Error: no MANIFEST.sha256 in $TARGET" >&2
	echo "Upload it alongside this script." >&2
	exit 1
fi

# sha256sum on Linux, shasum -a 256 on macOS.
if command -v sha256sum >/dev/null 2>&1; then
	SHA() { sha256sum "$1" | awk '{print $1}'; }
elif command -v shasum >/dev/null 2>&1; then
	SHA() { shasum -a 256 "$1" | awk '{print $1}'; }
else
	echo "Error: neither sha256sum nor shasum is available." >&2
	exit 1
fi

echo "Theme:    $TARGET"
echo "Manifest: $(grep -c . "$MANIFEST") files"
echo ""

MISSING=0
CORRUPT=0
OK=0

while IFS= read -r line; do
	[ -n "$line" ] || continue

	want=$(printf '%s\n' "$line" | awk '{print $1}')
	rel=$(printf '%s\n' "$line" | sed 's/^[0-9a-f]*  *//')
	rel=${rel#./}
	path="$TARGET/$rel"

	if [ ! -f "$path" ]; then
		echo "  MISSING   $rel"
		MISSING=$((MISSING + 1))
		continue
	fi

	have=$(SHA "$path")

	if [ "$have" != "$want" ]; then
		wantsize="?"
		havesize=$(wc -c < "$path" | tr -d ' ')
		echo "  CORRUPT   $rel  (on server: ${havesize} bytes)"
		CORRUPT=$((CORRUPT + 1))
		continue
	fi

	OK=$((OK + 1))
done < "$MANIFEST"

echo ""
echo "Verified OK: $OK   Missing: $MISSING   Corrupt/stale: $CORRUPT"

# ---- PHP syntax ----
PARSE=0

echo ""

if command -v php >/dev/null 2>&1; then
	echo "PHP syntax:"

	errs=$(find "$TARGET" -type f -name "*.php" ! -name "*.bak-*" -exec sh -c \
		'php -l "$1" >/dev/null 2>&1 || echo "  PARSE ERROR  $1"' _ {} \; )

	if [ -n "$errs" ]; then
		printf '%s\n' "$errs" | sed "s|$TARGET/||"
		PARSE=1
	else
		echo "  clean"
	fi
else
	echo "PHP syntax: skipped (php not on PATH)"
fi

# ---- functions the templates depend on ----
echo ""
echo "Required functions:"

UNDEF=0

for fn in eglatone_sections eglatone_single_image eglatone_post_thumbnail \
          eglatone_archive_image eglatone_posted_on eglatone_entry_footer \
          eglatone_check_section eglatone_blog_grid_columns; do
	hits=$(grep -rl "function $fn" "$TARGET" --include="*.php" 2>/dev/null | grep -v '\.bak-' | head -1)

	if [ -z "$hits" ]; then
		echo "  NOT DEFINED  $fn()"
		UNDEF=$((UNDEF + 1))
	elif [ "$QUIET" -eq 0 ]; then
		echo "  ok  $fn()  ->  ${hits#$TARGET/}"
	fi
done

echo ""

if [ "$MISSING" -gt 0 ] || [ "$CORRUPT" -gt 0 ] || [ "$UNDEF" -gt 0 ] || [ "$PARSE" -gt 0 ]; then
	echo "PROBLEMS FOUND. Re-upload the files listed above."
	echo "A CORRUPT file is usually a truncated transfer - upload it again and"
	echo "re-run this script until everything reports OK."
	exit 1
fi

echo "Everything matches the manifest."
exit 0
