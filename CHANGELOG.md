# Changelog

### 0.3.2 (2026-10-08)

#### Bug Fixes

- logic: remove fallback for empty request uri in path parsing (ff0c889)
- use the plugin text domain and call block hooks unconditionally (ea3e55b)

#### Maintenance

- align tooling and release pipeline with jcore-turva (3255a45)

### v0.3.1 (2026-02-09)

#### Bug Fixes

- logic: Add better slash handling to paths. (c1e4a70)
- normalize path and hash from json values (51300c6)

#### Refactor

- portal: update matching logic and content retrieval signature (ab7f759)
- logic: use options array for get_active_portal_content (f042a2f)

#### Build System

- makefile: add start and stop targets (6d65b05)

## v0.3.0 (2026-02-05)

#### Features

- portal-slot: add rotate option to allow items to loop when stack runs out (aa4f49c)
- logic: add rotation and paging to get_active_portal_content (d5ea646)

## v0.2.0 (2026-02-05)

#### Features

- activation: add activation hook to create default portal slot on plugin activation (6ba902b)
- sidebar: add option to target specific post or page (de6405a)
- portal-slot: add maxItems attribute to control item count (07402c5)
- logic: support returning multiple portal content items (041fdd4)
- post-type: Enable custom fields for portti post type (c563033)
- portal-slot: Improve editor preview functionality (f10d403)
- campaign: Add toggle and date pickers for campaign dates (ebca0e9)
- portti: Add content selection logic (8b08238)
- portal-slot: Introduce slot and preview settings (3c4c2cd)
- post-type: Add campaign settings meta fields (0d8ca67)

#### Bug Fixes

- portal-slot: enable saving of inner blocks in portal-slot block (c1abedf)
- post-type: set hierarchical to false for custom post type registration (36fd905)
- editor: update PluginDocumentSettingPanel import path (707acfa)

#### Documentation

- readme: expand plugin overview and usage instructions (fecb320)
- plan: update portal slot plan with new fields and logic (ba7cf6d)
- Add return types to function docblocks (834c035)
- logic: Add docblock for match_route (6e3cff9)
- Add JCORE Portti implementation plan (c3304d4)
- Update readme with features and dev info (d43c21e)

#### Styles

- whitespace: Improve code formatting (0a0580c)
- format .wp-env.json with consistent indentation (79fd622)
- format: Update string literals to use single quotes (a03c2fe)

#### Build System

- composer: update wordpress-stubs to v6.9.1 (8ecca21)
- webpack: Add campaign content sidebar entry point (8e3e7c4)
- package: Remove experimental-modules flag (ed2dd25)

#### Continuous Integration

- workflows: fix indentation in GitHub Actions yaml files (2023efa)

#### Maintenance

- build artifacts need to be commited for build system to work. (0e9a18d)
- scripts: remove version and postversion scripts from package.json (196f50a)
- update pnpm lock (45e04fb)
- cleanup: Remove unused portal slot block build files (276e86d)
- gitignore: Add build directory (64d7c0c)

#### Misc

- First version of portal block. (a8f5f28)
- Initial Commit (77bc278)

