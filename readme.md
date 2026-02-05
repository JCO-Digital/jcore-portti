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

#### Build Commands

```bash
# Install dependencies
pnpm install

# Development build with watch
pnpm start

# Production build
pnpm build
```

### License

GPL-2.0-or-later
