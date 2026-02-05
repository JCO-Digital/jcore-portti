## JCORE Portti

The `jcore-portti` plugin provides a portal block system for managing campaign content or other dynamic changing content within WordPress.

### Features

- **Campaign Content Post Type**: A dedicated post type (`jcore-portal-content`) for managing portal items.
- **Portal Slots**: A hierarchical taxonomy (`jcore-portal-slot`) used to categorize and target where content should appear.
- **Portal Slot Block**: A Gutenberg block that allows editors to place "slots" in layouts, which can then dynamically pull content based on the selected taxonomy terms.
- **Performance Optimized**: Uses modern WordPress block registration APIs (`wp_register_block_types_from_metadata_collection`) for better performance.
- **Multilingual Support**: Integrated with Polylang for translation support of campaign content.

### Development

The plugin uses a modern build process:

- Source files are located in `src/`.
- The block is built using `@wordpress/scripts`.
- Production builds generate a `blocks-manifest.php` for efficient registration.
