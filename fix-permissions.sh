#!/bin/sh
#
# fix-permissions.sh - reset WordPress theme file permissions
#
# Files      -> 644 (owner read/write, everyone else read)
# Directories-> 755 (owner all, everyone else read/traverse)
#
# Anything stricter (600 / 700) stops the web server reading the file, which
# shows up as a 403 on CSS/JS or a blank/unstyled page.
#
# Usage:
#   ./fix-permissions.sh                  # fix the directory the script sits in
#   ./fix-permissions.sh /path/to/theme   # fix a specific directory
#   ./fix-permissions.sh -n               # dry run: report, change nothing
#   ./fix-permissions.sh -c               # only the files changed for the
#                                         #   homepage layout work
#
# Works on macOS and Linux, sh/bash/zsh.

set -eu

DRY_RUN=0
CHANGED_ONLY=0
TARGET=""

usage() {
	echo "Usage: $0 [-n] [-c] [directory]"
	echo "  -n  dry run - list what would change, change nothing"
	echo "  -c  only the files touched by the homepage layout work"
	echo "  -h  this help"
	exit "${1:-0}"
}

while [ $# -gt 0 ]; do
	case "$1" in
		-n|--dry-run) DRY_RUN=1 ;;
		-c|--changed-only) CHANGED_ONLY=1 ;;
		-h|--help) usage 0 ;;
		-*) echo "Unknown option: $1" >&2; usage 1 ;;
		*) TARGET="$1" ;;
	esac
	shift
done

# Default to the directory this script lives in.
if [ -z "$TARGET" ]; then
	TARGET=$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)
fi

if [ ! -d "$TARGET" ]; then
	echo "Error: '$TARGET' is not a directory." >&2
	exit 1
fi

TARGET=$(CDPATH= cd -- "$TARGET" && pwd)

# Refuse to run somewhere catastrophic.
case "$TARGET" in
	/|/usr|/etc|/var|/home|/Users)
		echo "Error: refusing to run against '$TARGET'." >&2
		exit 1
		;;
esac

# Sanity check: this should look like a WordPress theme.
if [ ! -f "$TARGET/style.css" ] && [ "$CHANGED_ONLY" -eq 0 ]; then
	echo "Warning: no style.css in '$TARGET' - is this really a theme directory?" >&2
	printf "Continue anyway? [y/N] "
	read -r reply
	case "$reply" in
		y|Y|yes|YES) ;;
		*) echo "Aborted."; exit 1 ;;
	esac
fi

echo "Target: $TARGET"
[ "$DRY_RUN" -eq 1 ] && echo "Mode:   DRY RUN (nothing will be changed)"
echo ""

# The files changed for the homepage layout work, relative to the theme root.
CHANGED_FILES="
header.php
functions.php
index.php
inc/template-functions.php
inc/schema-jsonld.php
inc/customizer/customizer.php
inc/customizer/homepage-layout.php
inc/ticker.php
inc/hero.php
template-parts/service/display-service.php
template-parts/service/content-service.php
template-parts/featured-content/display-featured.php
template-parts/ticker/display-ticker.php
template-parts/content/content-single.php
assets/css/homepage-layout.css
assets/js/homepage-layout.js
"

perms_of() {
	# Portable "print octal permissions of $1".
	if stat -f '%Lp' "$1" >/dev/null 2>&1; then
		stat -f '%Lp' "$1"        # BSD / macOS
	else
		stat -c '%a' "$1"         # GNU / Linux
	fi
}

fix_one() {
	path="$1"
	want="$2"

	[ -e "$path" ] || { echo "  MISSING  $path"; MISSING=$((MISSING + 1)); return; }

	have=$(perms_of "$path")

	if [ "$have" = "$want" ]; then
		SKIPPED=$((SKIPPED + 1))
		return
	fi

	echo "  $have -> $want  ${path#$TARGET/}"

	if [ "$DRY_RUN" -eq 0 ]; then
		chmod "$want" "$path"
	fi

	CHANGED=$((CHANGED + 1))
}

CHANGED=0
SKIPPED=0
MISSING=0

if [ "$CHANGED_ONLY" -eq 1 ]; then
	echo "Fixing the homepage layout files only:"

	# Their parent directories need to be traversable too.
	for d in inc inc/customizer assets assets/css assets/js \
	         template-parts template-parts/service template-parts/featured-content \
	         template-parts/ticker template-parts/content; do
		[ -d "$TARGET/$d" ] && fix_one "$TARGET/$d" 755
	done

	for f in $CHANGED_FILES; do
		fix_one "$TARGET/$f" 644
	done
else
	echo "Fixing directories to 755:"
	find "$TARGET" -type d ! -path '*/.git/*' ! -name '.git' | while IFS= read -r d; do
		have=$(perms_of "$d")
		[ "$have" = "755" ] && continue
		echo "  $have -> 755  ${d#$TARGET/}"
		[ "$DRY_RUN" -eq 0 ] && chmod 755 "$d"
	done

	echo ""
	echo "Fixing files to 644:"
	find "$TARGET" -type f ! -path '*/.git/*' | while IFS= read -r f; do
		have=$(perms_of "$f")
		[ "$have" = "644" ] && continue
		echo "  $have -> 644  ${f#$TARGET/}"
		[ "$DRY_RUN" -eq 0 ] && chmod 644 "$f"
	done
fi

echo ""

if [ "$CHANGED_ONLY" -eq 1 ]; then
	echo "Changed: $CHANGED   Already correct: $SKIPPED   Missing: $MISSING"

	if [ "$MISSING" -gt 0 ]; then
		echo ""
		echo "WARNING: $MISSING file(s) listed above are missing from this directory."
		echo "They did not transfer. Upload them before reloading the site -"
		echo "a missing inc/schema-jsonld.php will fatal on require."
	fi
else
	echo "Done."
fi

if [ "$DRY_RUN" -eq 1 ]; then
	echo ""
	echo "Dry run - nothing was changed. Re-run without -n to apply."
fi
