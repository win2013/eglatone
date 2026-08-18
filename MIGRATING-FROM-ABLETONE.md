# Migrating from Abletone to Eglatone

Eglatone is the renamed fork of your customised Abletone theme. Same layout,
same features, new identity — and, importantly, no longer overwritten when
Catch Themes ships an Abletone update.

Two things change under the hood, and both need migrating:

| What | Abletone | Eglatone |
|---|---|---|
| Theme folder | `themes/abletone/` | `themes/eglatone/` |
| Customizer option row | `theme_mods_abletone` | `theme_mods_eglatone` |
| Every setting key inside it | `abletone_hero_option` | `eglatone_hero_option` |
| Standalone options | `abletone_jsonld_schema` | `eglatone_jsonld_schema` |
| Additional CSS post slug | `abletone` | `eglatone` |
| Text domain | `abletone` | `eglatone` |
| Function prefix (59 of them) | `abletone_*` | `eglatone_*` |
| CSS classes | `.abletone-hero` | `.eglatone-hero` |

A plain option copy handles the row rename and **silently loses every setting**,
because the keys inside changed too. The included script does both.

---

## Steps

### 1. Back up

```bash
wp db export backup-before-eglatone.sql
```

Or take whatever backup your host offers. This is a database change; a backup
costs nothing and makes the whole thing reversible in one command.

### 2. Upload the new theme

Upload `eglatone.zip` via **Appearance → Themes → Add New → Upload Theme**,
or extract it into `wp-content/themes/` so you end up with:

```
wp-content/themes/abletone/    <- leave it in place for now
wp-content/themes/eglatone/    <- new
```

**Do not activate it yet.**

### 3. Migrate the settings

From your WordPress root (where `wp-config.php` lives):

```bash
wp eval-file migrate-eglatone.php            # dry run - reports, writes nothing
wp eval-file migrate-eglatone.php --apply    # commit
```

The dry run prints exactly what it will do:

```
theme_mods_abletone  ->  theme_mods_eglatone
  settings renamed to the new prefix : 34
  core settings carried across       : 6
  menu locations preserved           : 2
```

### 4. Activate

**Appearance → Themes → Eglatone → Activate.**

### 5. Check

- **Appearance → Menus** — Primary and Social still assigned
- **Customize → Theme Options** — Achievements Hero, Press Mentions, News
  Ticker, Homepage Layout all still populated
- **Settings → Schema (JSON-LD)** — your Person schema is still there
- The homepage — hero, press strip, ticker, blog grid

### 6. Fix permissions and verify

```bash
cd wp-content/themes/eglatone
chmod +x *.sh
./fix-permissions.sh
./verify-theme.sh
```

### 7. Only then, remove the old theme

Once you are satisfied, delete `wp-content/themes/abletone/`. Keep the database
rows — they cost nothing and are your undo.

---

## If WP-CLI is not available

Drop `eglatone-migrate.php` into `wp-content/mu-plugins/` (create the folder if
it does not exist), load any admin page once, and check the notice at the top.
Then **delete the file**. It is a one-shot migration, not something to leave
installed.

Pure SQL cannot do this job: the setting keys live inside a PHP-serialised
array, so they have to be unserialised, renamed and re-serialised. That needs
PHP, which is why there is no `.sql` fallback.

---

## Rolling back

Nothing is deleted by the migration. To go back:

1. **Appearance → Themes → Abletone → Activate**

That is the whole rollback. `theme_mods_abletone` was never modified.

---

## Licensing

Eglatone is a derivative work of Abletone by Catch Themes, distributed under the
GNU GPL v2 or later. The GPL explicitly permits forking, renaming and
redistributing — including commercially — provided your version stays GPL and
you do not claim endorsement by or affiliation with the original author.

Attribution is recorded in `style.css` and `readme.txt`. The Catch Themes
branding, support links and "Upgrade to Pro" upsell have been removed, since
advertising another vendor's product from a fork would be misleading.

Third-party bundled resources (Owl Carousel, Font Awesome, TGM Plugin
Activation, jquery.matchHeight) keep their own licences, listed in `readme.txt`.
