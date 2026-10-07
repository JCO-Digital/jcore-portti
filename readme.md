## JCORE Portti

The `jcore-portti` plugin provides a portal block system for managing campaign content or other dynamic changing content within WordPress. It uses a "Slot" and "Content" architecture that allows administrators to define areas in layouts (Slots) and schedule specific content to appear in those areas based on configurable rules.

### Features

- **Campaign Content Post Type**: A dedicated post type (`jcore-portal-content`) for managing portal items with full block editor support.
- **Portal Slots Taxonomy**: A hierarchical taxonomy (`jcore-portal-slot`) used to categorize and target where content should appear.
- **Portal Slot Block**: A Gutenberg block (`jco/portal-slot`) that allows editors to place "slots" in layouts, which dynamically pull content based on the selected taxonomy terms.
- **Scheduling Support**: Configure start and end dates for campaign content to automatically show/hide based on time.
- **Targeting Options**: Target content to specific pages/posts or use route path patterns with wildcard support.
- **Priority System**: Set content priority (High, Medium, Low) to control which content displays when multiple items match.
- **Fallback Content**: Support for fallback/inner block content when no matching campaign content is found.
- **Performance Optimized**: Uses modern WordPress block registration APIs (`wp_register_block_types_from_metadata_collection`) for better performance.
- **Multilingual Support**: Integrated with Polylang for translation support of campaign content.

### How It Works

1. **Create Portal Slots**: Define taxonomy terms representing areas in your layout (e.g., "Homepage Hero", "Sidebar Banner").
2. **Place Portal Slot Blocks**: Add the Portal Slot block to your templates/pages and select which slot it represents.
3. **Create Campaign Content**: Create content items, assign them to slots, and configure targeting rules.
4. **Automatic Display**: The plugin automatically displays the appropriate content based on your rules.

### Campaign Content Settings

Each campaign content item can be configured with:

| Setting                | Description                                                                                                        |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------ |
| **Start Date**         | Optional. Content will only display after this date/time.                                                          |
| **End Date**           | Optional. Content will stop displaying after this date/time.                                                       |
| **Selected Page/Post** | Target a specific page or post. Takes precedence over route path.                                                  |
| **Route Path**         | URL pattern matching. Supports exact paths (`/shop`) or wildcards (`/products/*`). Leave empty to match all pages. |
| **Priority**           | High, Medium, or Low. Higher priority content displays first when multiple items match.                            |

### Portal Slot Block Attributes

| Attribute  | Type   | Default | Description                                 |
| ---------- | ------ | ------- | ------------------------------------------- |
| `slotId`   | string | `""`    | The slug of the portal slot taxonomy term.  |
| `maxItems` | number | `1`     | Maximum number of content items to display. |

### Content Selection Logic

When rendering a portal slot, the plugin:

1. Queries all published campaign content assigned to the slot.
2. Filters by date range (if start/end dates are set).
3. Matches against the current page using selected post ID or route path patterns.
4. Sorts results by specificity (specific post > route path), then priority, then date.
5. Returns up to `maxItems` matching content items.

### Route Path Matching

- **Exact match**: `/contact` only matches the `/contact` page.
- **Wildcard match**: `/products/*` matches `/products/item-a`, `/products/item-b`, etc.
- **Global match**: Empty path matches all pages (useful for site-wide campaigns).

### Requirements

- WordPress 6.7 or higher
- PHP 8.2 or higher

### Development

The plugin uses a modern build process:

- Source files are located in `src/`.
- Block source: `src/portal-slot/`
- Campaign content sidebar: `src/campaign-content-sidebar/`
- The blocks are built using `@wordpress/scripts`.
- Production builds generate a `blocks-manifest.php` for efficient registration.

- `build/` is not committed. It is built in CI and shipped in the release zip.

#### Getting started

Requires Node 22+, [pnpm](https://pnpm.io/) and [Composer](https://getcomposer.org/). [WP-CLI](https://wp-cli.org/) is only needed for the translation scripts.

```bash
pnpm install
composer install
pnpm build
```

Then run a throwaway WordPress with the plugin mounted:

```bash
pnpm playground
```

This serves [WordPress Playground](https://wordpress.org/playground/) on <http://localhost:8883> from `.wp/blueprint.json`, logged in as `admin` / `password`.

#### Scripts

| Command                                  | What it does                                              |
| ---------------------------------------- | --------------------------------------------------------- |
| `pnpm build`                             | Build the blocks and editor sidebar into `build/`.        |
| `pnpm start`                             | Same, in watch mode.                                      |
| `pnpm check`                             | Everything CI lints: ESLint, Stylelint and PHPCS.         |
| `pnpm lint:js` / `lint:css` / `lint:php` | One linter at a time.                                     |
| `pnpm format`                            | Format `src/` with `wp-scripts format`.                   |
| `composer lint:fix`                      | Fix what PHPCBF can fix.                                  |
| `pnpm i18n`                              | Regenerate the POT, the MO files and the JS translations. |
| `pnpm playground`                        | Serve the plugin in WordPress Playground.                 |

A `Makefile` wraps the same scripts. `make ci` is the entry point the shared publish workflow calls.

#### Releases

Merging to `main` runs `.github/workflows/release.yml`. It lints and builds, then [foonver](https://github.com/foonly/foonver) bumps the version from the conventional commit messages, syncs it into `jcore-portti.php` and updates `CHANGELOG.md`. The reusable publish workflow from [jcore-update](https://github.com/JCO-Digital/jcore-update) builds the zip, creates the GitHub release, notifies the update API and pushes to the dist repository. Installed sites pick up new versions through the bundled jcore-update client.

### License

GPL-2.0-or-later
