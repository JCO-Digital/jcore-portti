# JCORE Portti implementation plan

The plugin provides a dynamic content injection system using a "Slot" and "Content" architecture. It allows administrators to define areas in the layout (Slots) and schedule specific content to appear in those areas based on rules.

## Block: Portal Slot

The `portal-slot` block serves as a placeholder where dynamic campaign content will be rendered.

### Attributes

- `slotId` (string): The slug of the `jcore-portal-slot` taxonomy term this block belongs to.
- `fallback` (string): Optional HTML/InnerBlocks to show if no matching content is found.

### Editor Functionality

- A dropdown or combobox to select from existing "Portal Slots" (taxonomy terms).
- A link to manage Portal Slots in the admin.
- Preview mode: Option to select a specific "Campaign Content" to preview how it looks in the slot.

### Frontend Rendering (`render.php`)

1. Determine current page path.
2. Query `jcore-portal-content` items assigned to the selected `slotId`.
3. Filter results based on:
    - **Date**: Current time must be between `start_date` and `end_date`.
    - **Path**: Match the `route_path` against the current request URI (supporting wildcards or exact matches).
4. Sort by **Priority** (High > Medium > Low) and then by **Date** (Newest first).
5. Output the content of the top-matching post.

## Content: Campaign Content (CPT)

This is implemented as the `jcore-portal-content` custom post type.

### Taxonomy: Portal Slots

- `jcore-portal-slot`: Hierarchical taxonomy used to categorize content into specific layout areas.

### Metadata / Additional Fields

The following fields should be implemented using a Metabox (or registered in REST for Block Editor sidebar):

- **Start Date** (`_jcore_portti_start_date`): ISO datetime string.
- **End Date** (`_jcore_portti_end_date`): ISO datetime string.
- **Route Path** (`_jcore_portti_route_path`): String (e.g., `/products/*` or `/shop`).
- **Priority** (`_jcore_portti_priority`): Select field with values `high`, `medium`, `low`. Default: `medium`.

## Logic & Implementation Details

### Query Logic

The selection logic should be encapsulated in a helper class or function to ensure consistency:

```php
function get_active_portal_content( $slot_slug ) {
    // 1. WP_Query for jcore-portal-content with the specific taxonomy term.
    // 2. Filter by meta_query for date range (if set).
    // 3. Post-processing to match the route_path.
    // 4. Sort by priority map (high=10, medium=5, low=1).
}
```

### Routing Match

- Exact match: `/contact` only matches `/contact`.
- Wildcard match: `/products/*` matches `/products/item-a` and `/products/item-b`.
- Empty path: Matches all pages (Global content for that slot).

### Asset Management

- Ensure styles for the Campaign Content are loaded even if the content is injected dynamically.
- Support for Block styles within the Campaign Content CPT.
