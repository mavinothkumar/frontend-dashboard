=== Frontend Dashboard ===
Contributors: vinoth06, buffercode
Tags: dashboard, frontend dashboard, custom login, custom register, custom dashboard
Donate link: https://www.paypal.com/paypalme2/buffercode
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 3.0.18
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build custom frontend user dashboards with login, registration, user profiles, frontend post editing, and role-based permissions.

== Description ==
= Frontend Dashboard — Built for Modern WordPress Portals =

Frontend Dashboard provides a complete frontend portal for WordPress sites with modular controls for user management, post publishing, custom menus, and branding.

Take full control of the user experience by allowing members, authors, and customers to manage their profiles, submit posts, and navigate custom dashboards directly on the frontend — without ever accessing the `wp-admin` dashboard.

= Key Features =

* **Modern App Shell & Responsive Layout**: Clean, fast frontend dashboard interface powered by Tailwind CSS with responsive slide-over navigation.
* **Modern Rich Text & Block Editors**: Choose between **Editor.js** (block editor with headers, images, quotes, lists, tables), **TipTap** (WYSIWYG modern editor), or the classic WordPress Editor for frontend post publishing.
* **Custom Authentication Flow**: Custom Login, Registration, and Forgot Password pages with custom redirects before/after login, register, and logout.
* **WP Admin Restriction**: Restrict access to the WordPress backend (`/wp-admin/`) and admin toolbar based on user roles.
* **Custom User Roles**: Create, manage, and assign custom user roles with specific dashboard capabilities.
* **Custom Profile & Post Fields**: Create unlimited custom fields for user profiles and post types with role-based visibility and permissions.
* **Built-in Templates & Page Mappings**: Built-in modern dashboard templates, extra form fields, and page loaders merged directly into Core.
* **File Upload Controls**: Role-based permissions to allow or restrict media uploads in the frontend dashboard.
* **Frontend Post & Taxonomy Management**: Add, edit, and delete posts or custom post types and taxonomies with role permissions.
* **Add-ons & Extensions Hub**: Browse, install, and activate official Frontend Dashboard add-ons with 1-click management and real-time catalog updates.
* **System Status & Diagnostics**: Built-in system environment diagnostic tools, database health checks, and activity logging.
* **Theme Matching & Custom Styling**: Customize dashboard theme colors and UI options to seamlessly match your site design.

= Official Add-ons & Extensions =
* [Frontend Dashboard Custom Post and Taxonomies](https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies) (Free)
* [Frontend Dashboard Notification](https://buffercode.com/plugin/frontend-dashboard-notification) (Free)
* [Frontend Dashboard Captcha](https://buffercode.com/plugin/frontend-dashboard-captcha) (Free)
* [Frontend Dashboard Social Chat](https://buffercode.com/plugin/frontend-dashboard-social-chat) (Free)

*Note: Frontend Dashboard Templates, Extra, and Pages have been merged directly into Core in v3.0.0.*

== Installation ==

1. Upload the `frontend-dashboard` directory to your `/wp-content/plugins/` directory, or upload `frontend-dashboard.zip` via **WordPress Admin → Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **Frontend Dashboard → Settings** to configure your login, registration, and dashboard pages.
4. Save settings and assign your custom dashboard pages.

== Frequently Asked Questions ==

= How do I create a custom login, register, and forgot password page? =

1. Go to **Admin Dashboard → Pages → Add New**.
2. Title the page (e.g., "Login").
3. Add the shortcode `[fed_login]` into the page content.
4. Set the Page Template to **FED Login** (in the right-hand Page Attributes panel).
5. Publish the page.
6. Navigate to **Frontend Dashboard → Settings → Login → Settings** and select your newly created page as the Login Page URL.
7. Save your settings.

= Can I separate Login, Register, and Forgot Password onto different pages? =

Yes! Create individual pages and use dedicated shortcodes:
* Login only: `[fed_login_only]`
* Register only: `[fed_register_only]`
* Forgot Password only: `[fed_forgot_password_only]`

= How do I create the main user dashboard page? =

1. Go to **Admin Dashboard → Pages → Add New**.
2. Enter a title (e.g., "User Dashboard").
3. Add the shortcode `[fed_dashboard]` into the page content.
4. Set the Page Template to **FED Dashboard** under Page Attributes.
5. Publish the page.
6. Go to **Frontend Dashboard → Settings → Login → Settings** and set the "Redirect After Logged in URL" or dashboard page.

= Which rich text editors are supported for frontend post publishing? =

Frontend Dashboard v3 supports:
* **Editor.js**: A modern block-based editor supporting headings, images, lists, quotes, and tables.
* **TipTap**: A fast, extensible WYSIWYG rich text editor with interactive bubble menus.
* **Classic WP Editor**: The familiar WordPress TinyMCE visual/text editor.

You can configure editor preferences under **Frontend Dashboard → Settings → Post → Settings**.

= How do I add custom user profile fields? =

1. Go to **Frontend Dashboard → User Profile**.
2. Click **Add New Extra User Profile Field**.
3. Select the desired field type from the dropdown (text, textarea, select, checkbox, radio, date, etc.).
4. Configure the field label, meta key, and role permissions.
5. Click **Save**.

= How do I add custom post fields? =

1. Go to **Frontend Dashboard → Post Fields**.
2. Click **Add New Extra User Post Field**.
3. Select the input type, enter field settings, and specify which post types and user roles can view or edit the field.
4. Click **Save**.

= How do I create custom dashboard menus? =

1. Go to **Frontend Dashboard → Dashboard Menu**.
2. Click **Add New Menu**.
3. Configure the menu title, icon, URL, and user roles permitted to access it.
4. Click **Add New Menu**.

= How can I retrieve custom fields in theme templates? =

* To retrieve user custom fields:
  `$user_meta = get_user_meta( $user_id, 'your_custom_field_key', true );`
* To retrieve post custom fields:
  `$post_meta = get_post_meta( $post_id, 'your_custom_field_key', true );`

= Where are Payments and Invoices configured? =

Payment Gateways, Invoices & Billing profiles, and Transaction Ledgers are now dedicatedly powered and managed by the **GateFuz** plugin. The legacy Payments menu in Frontend Dashboard has been deprecated and centralized into GateFuz (under **GateFuz > Gateways** and **GateFuz > Invoices**). All existing company invoice settings (`fed_invoice_settings`) and transaction tables are automatically preserved and loaded by GateFuz with zero data loss.

= List of Available Shortcodes =

* `[fed_login]` — Complete authentication portal (login, registration, password reset).
* `[fed_login_only]` — Standalone login form.
* `[fed_register_only]` — Standalone registration form.
* `[fed_forgot_password_only]` — Standalone password recovery form.
* `[fed_dashboard]` — Main frontend user dashboard portal.
* `[fed_user role="user_role"]` — Role-specific user directory / profile page.
* `[fed_transactions]` — Frontend user transaction and payment history.
* `[fed_list_taxonomy taxonomy="category"]` — Displays taxonomy terms in a list layout.

For more documentation and FAQs, visit [https://faq.frontenddashboard.com](https://faq.frontenddashboard.com).

== Screenshots ==

1. Frontend Dashboard Control Center.
2. User Profile fields
3. Frontend Dashboard Settings — Register
4. Frontend Dashboard Settings — Restrict Username
5. Frontend Dashboard Settings — Restrict WP Admin area
6. Post Form fields
7. Login, Register page
8. Frontend Dashboard


== Changelog ==

= 3.0.18 (2026-10-19) =
* Feature: Universal Client-Side PDF First-Page Preview Rendering. When WordPress core does not generate thumbnail images for PDF attachments (such as hosting and local environments without Ghostscript or ImageMagick), Frontend Dashboard custom media picker asynchronously renders a crisp first-page thumbnail in the browser using Mozilla PDF.js. Displays document cover previews across media grid cards, single attachment inspection details, and multi-selection trays with an overlay badge.
* Fix: Enhanced `MediaController.php` attachment formatting with multi-tier fallback to resolve generated preview images from attachment metadata sizes, `wp_prepare_attachment_for_js()`, or post thumbnail IDs.
* Feature: Post Submission Workflow: Automatically redirect frontend authors and users directly to the post list view upon successful post submission or review request.
* Compatibility: Fully compatible with Frontend Dashboard Custom Post and Taxonomies v3.0.6, GateFuz v1.1.3, and Frontend Dashboard Pay Per Post v3.0.0.

= 3.0.17 (2026-10-19) =
* Fix: Resolved PDF Embedder preview thumbnail display in the custom media picker. When WordPress or plugins generate image previews for PDF documents, the custom media picker now retrieves and displays the rendered preview image consistently with the WordPress core Media Library instead of falling back to a generic document placeholder icon.
* Fix: Resolved PHP warning `Undefined variable $hasFullQuery` in `DashboardRoutes.php` by properly defining `$has_full_query` for menu request validation and applying defensive parameter sanitization across fallback dashboard routes.
* Fix: Resolved PDF Embedder compatibility regression where inserting a PDF via Frontend Dashboard custom media picker was inserting a plain file link instead of the `[pdf-embedder url="..."]` shortcode. Ensured `window.frontend_dashboard` is localized on the enqueued Vite bundle, synchronized media configuration and nonces across Classic Visual (TinyMCE) and Code (Quicktags/Textarea) modes, added a fail-safe server-side shortcode fallback when PDF Embedder is active, and automatically normalized raw viewer wrapper HTML back to shortcode syntax to prevent attribute stripping on save.
* Enhancement: Added `fed_is_system_menu` filter and locked system menu protection in Dashboard Menu manager (`?page=fed_dashboard_menu`), ensuring extension modules like Frontend Dashboard Pay Per Post ("My Submissions") and Frontend Dashboard Membership ("My Membership") are locked against accidental deletion while remaining fully customizable for title/label, icon, drag-and-drop order sorting, and role permissions.
* Fix: Resolved menu label spacing in Dashboard Menu manager, ensuring menu names with spaces (e.g. "My Membership", "Custom Menus", "My Submissions") preserve user-entered spacing without being trimmed or combined into single words.
* Fix: Prevented modal closing (Close 'X' button, Cancel button, backdrop click, Escape key) while save or update is in progress, adding in-modal spinner and loading state.
* Enhancement: Persisted success toast notifications across page reloads via session storage so confirmation messages display reliably after updating menu details.
* Enhancement: Added fallback support for `menu_tab` parameter in dashboard URL router (`DashboardRoutes.php`).
* Fix: Enhanced `PaymentGatewayManager::getEnabledGateways()` to verify gateway configuration state (`isConfigured()`) alongside enabled status (`isEnabled()`), ensuring unconfigured or inactive payment gateways are excluded from frontend checkout screens.
* Compatibility: Fully compatible with GateFuz v1.1.3 and Frontend Dashboard Pay Per Post v3.0.0.

= 3.0.16 (2026-10-17) =
* Fix: Corrected mobile top navigation header alignment where the hamburger menu toggle and dashboard brand title were pushed into the center of the mobile screen due to WordPress `wpautop` injecting phantom paragraph tags into flex containers; enforced strict left-aligned flex containers, auto margins, and automatic tag pruning.
* Fix: Resolved vertical spacing distortion and gap in the mobile drawer brand header between the Dashboard title and subtitle caused by rogue `<br>` and `<p>` tags injected by WordPress `wpautop`; enforced rigid text resets, whitespace pruning, and scoped paragraph margin resets.
* Fix: Corrected frontend login, logout, and registration notification modal layout, centering icon badges defensively and preventing external addon styles or theme flex-shrink rules from misaligning the icon to the left.
* Enhancement: Modernized notification modal appearance with soft ambient icon halo rings, polished typography hierarchy, and an integrated progress bar track for automated redirects.
* Enhancement: Centralized Payment Gateways & Invoices into GateFuz: Deprecated the legacy built-in Payments menu in Frontend Dashboard in favor of the dedicated GateFuz payment gateway engine. When GateFuz is active, legacy Payments submenus are suppressed and bookmarks to `?page=fed_payments` seamlessly redirect to GateFuz Invoices (`?page=gate-fuz-invoices`). All existing company billing profiles and invoice settings remain fully preserved.

= 3.0.15 (2026-10-16) =
* Fix: Resolved header breadcrumb text unexpectedly displaying centered in the dashboard canvas by neutralizing rogue empty paragraph tags generated by WordPress `wpautop`, enforcing strict left-aligned flex containers, and removing conflicting spacing classes.
* Fix: Corrected vertical misalignment in the sidebar brand header between the dashboard icon and site title text, adding defensive vertical centering and typography resets against theme style conflicts.
* Enhancement: Connected the Dashboard Brand Logo (configured under Frontend Dashboard > Settings > Dashboard > Settings) to display automatically centered above all frontend authentication forms (`[fed_login]`, `[fed_login_only]`, `[fed_register_only]`, and `[fed_forgot_password_only]`), with smart fallback to theme custom logo and developer filters (`fed_brand_logo_url`, `fed_brand_logo_html`).
* Fix: Restored full compatibility with PDF Embedder plugin, routing custom media modal insertion through WordPress native `media_send_to_editor` pipeline.
* Enhancement: Introduced extensible media picker developer API (`window.FedMedia`, `fed_send_media_to_editor`, `fed_media_format_attachment`, and toolbar action hooks) for seamless third-party plugin integrations.

= 3.0.14 (2026-10-15) =
* Fix: Resolved category and hierarchical taxonomy selection not updating when creating or editing posts from Frontend Dashboard view.
* Fix: Preserved associative array term IDs in select field options builder, ensuring taxonomy term IDs are saved instead of term names.
* Fix: Guaranteed taxonomy term assignment across user roles (Subscribers, Authors, Contributors) by ensuring terms in tax_input are explicitly set after post insertion.

= 3.0.13 (2026-10-14) =
* Fix: Resolved `Unauthorized class execution` error on Payments Dashboard (`?page=fed_payments&menu=dashboard`) by registering payment controller methods in the authorized action hooks map.
* Fix: Eliminated raw `// phpcs:ignore` comment leak in payment layout output and cleaned embedded comments inside database query strings.
* Enhancement: Removed legacy Membership Pro admin menu (`?page=fed_membership_pro`) and deprecated mock upsell teaser classes to streamline admin navigation.

= 3.0.12 (2026-10-11) =
* Fix: Resolved syntax error (`Uncaught SyntaxError: Invalid or unexpected token`) caused by WordPress `wptexturize` formatting ampersands in inline script tags, by moving all mobile drawer controllers to the external enqueued frontend script.
* Fix: Prevented mobile hamburger header from appearing on desktop viewports (>= 1024px) by enforcing strict scoped desktop display rules.
* Fix: Resolved mobile drawer click collision and double-toggle race condition with debounce throttling and native event delegation.
* Enhancement: Optimized responsive layout for ultra-small mobile screens (320px width), fixing avatar sizing and enabling responsive stacking for the profile identity card to prevent horizontal text clipping.
* Enhancement: Added defensive left-alignment CSS resets to prevent parent theme styles from centering dashboard breadcrumbs, titles, and headers.

= 3.0.11 (2026-10-10) =
* Fix: Resolved mobile layout display regression on mobile viewports (< 1024px) where the desktop sidebar width was forcing side-by-side horizontal compression.
* Feature: Added responsive mobile navigation top bar with hamburger menu toggle, off-canvas slide-over drawer navigation, and dismissible backdrop.
* Enhancement: Optimized mobile viewport padding across main dashboard cards, ensuring clean full-width content rendering across all mobile phone and tablet screen sizes.
* Fix: Resolved a JavaScript reference error (`emptyState` variable) in the media modal that prevented the single-item Attachment Details sidebar from displaying the Alt Text, Title, Caption, and Description fields.
* Enhancement: Multi-select thumbnail badges now display clean document indicators, and selecting a single item reliably populates all metadata and display settings with live auto-saving.

= 3.0.9 (2026-10-09) =
* Enhancement: Upgraded upload queue with individual animated progress bars, live transfer percentages, file badges, and concurrent upload tracking (Thanks to Muhammad Umer).
* Enhancement: Added full Attachment Details sidebar enabling live editing of Alt Text, Title, Caption, Description, Alignment (none/left/center/right), Link To (none/file/page/custom URL), and Size (full/large/medium/thumbnail) before inserting into the editor.
* Fix: Resolved filter dropdown performance and media filtering: enforced strict client-side type isolation so PDFs are completely excluded when the Images filter is active, added AbortController request cancellation, and bypassed disk I/O with cached metadata.
* Fix: Resolved media library empty state overlapping the dropzone on the Upload tab, and prevented initial modal open from flashing "No media files found" while loading.
* Enhancement: Enhanced "Load More Media" pagination with inline loading indicators and item deduplication without blanking out the grid.
* Enhancement: Enabled automatic AJAX persistence for attachment Alt text, Title, Caption, and Description edits in the media modal.

= 3.0.8 =
* Security: Enforced strict capability and authorization checks across all AJAX, admin-post, and API request dispatchers.
* Security: Replaced generic prefix callable matching with strict allowlists mapped to required capabilities (`manage_options`, `edit_posts`, etc.).
* Security: Hardened user registration against privilege escalation by preventing elevated role assignments and enforcing WordPress `users_can_register` option.
* Security: Added explicit capability checks across all admin setting handlers, invoice operations, and role management functions (Thanks to Đỗ Trung Kiên - patchstack.com).
* Fix: Modernized Classic Editor media modal with support for multi-upload, multi-select media insertion, and non-image previews (PDFs, docs, audio, and video) (Thanks to Muhammad Umer - https://github.com/mavinothkumar/frontend-dashboard/issues/16).
* Fix: Prevented smart quote (`”`) conversion and attribute corruption during post publishing and updates, keeping image tags and attributes fully preserved across save and reload cycles.

= 3.0.7 =
* Fix: Resolved "Sorry, you are not allowed to access this page" permission error by correcting Add-ons marketplace URL to `fed_plugin_pages` in compatibility notices.
* Enhancement: Registered `fed_addons` as a fallback submenu alias to guarantee seamless access on legacy or bookmarked URLs.
* Fix: Restricted frontend stylesheet and script loading strictly to Frontend Dashboard screens to prevent CSS collisions (including table.fixed overrides) on third-party pages.

= 3.0.6 =
* Security: Enforced strict capability and permission checks on REST API post creation, editing, and publishing endpoints (credit: Ali Hidayat).

= 3.0.5 =
* Security: Fixed user registration parameter handling to prevent unauthorized user updates and privilege escalation.
* Security: Hardened AJAX / Post API dispatch routing and function execution handlers with strict allowlist and capability checks.

= 3.0.4 =
* Feature: Introduced "More WordPress Plugins by BufferCode" section in Add-ons marketplace to showcase standalone ecosystem plugins (AdFuz & GateFuz).
* Feature: Added support for WordPress 6.5+ native plugin dependency declarations (`required_plugins`) to dynamically separate official add-ons from standalone ecosystem plugins.
* Enhancement: Streamlined Add-ons marketplace grid with unified 4-column responsive card layouts and dedicated ecosystem filter tab.
* Fix: Resolved "Class FEDInstallAddons does not exist" error during extension 1-click install and activation.
* Fix: Loaded missing module dependencies into core bootstrapper including orders, pro plugin placeholders, and standalone shortcodes.
* Enhancement: Hardened AJAX extension installer with explicit capability checks and clean JSON error response handling.

= 3.0.3 =
* Fix: WordPress.org plugin review standards and code quality compliance improvements.
* Fix: Addressed input unslashing, sanitization, and nonce verification checks across admin and frontend requests.
* Fix: Removed obsolete array_column polyfill and auto_update_plugin hook alteration.
* Enhancement: Added comprehensive `.gitignore` rules for modern development workflows.

= 3.0.2 =
* Enhancement: Extended hook architecture to support modular extension registration, automated update checks, and decoupled addon management.
* Enhancement: Modernized Extensions Hub interface with smart section categorization, live search filtering, native confirmation dialogs, and real-time status feedback.
* Performance: Optimized admin menu filter pipeline and background cron worker workflows.

= 3.0.1 =
* Fix: WordPress.org plugin review and security compliance improvements.

= 3.0.0 =
* Major Release: Complete frontend architecture redesign with Tailwind CSS and modern App Shell layout.
* Feature: Integrated modern rich text editors — Editor.js (block editor) and TipTap (WYSIWYG editor) alongside classic WP Editor.
* Feature: Merged Templates, Extra Form Fields, and Custom Pages directly into Core.
* Feature: Brand new Add-ons & Extensions marketplace with 1-click install, activation, and real-time catalog caching.
* Feature: System Status & Diagnostic tools with Database health checks and comprehensive Activity/Audit Logging.
* Feature: Enhanced role-based admin bar controls, custom menu management, and advanced redirection settings.
* Enhancement: Streamlined responsive mobile navigation with slide-over drawer menus.
* Security: Comprehensive nonce validation, strict capability checks, and robust data sanitization across all AJAX endpoints.
* Compatibility: Fully tested and compatible with WordPress 6.7 and PHP 8.0 / 8.1 / 8.2 / 8.3.

More Changelogs:
https://faq.frontenddashboard.com/changelog/overview/

== Upgrade Notice ==

= 3.0.17 =
Resolves undefined route query warnings in DashboardRoutes, restores PDF Embedder shortcode insertion compatibility from the custom media picker across all editor modes, and ensures unconfigured or inactive payment gateways are excluded from frontend checkout screens. Fully compatible with GateFuz v1.1.3 and Frontend Dashboard Pay Per Post v3.0.0.

= 3.0.16 =
Fixes frontend authentication notification modal UI and centering, enhances modal progress track styling, and centralizes payment gateways, invoices, and billing profiles into the dedicated GateFuz engine while deprecating the legacy FED Payments menu.

= 3.0.15 =
Fixes dashboard breadcrumb alignment and sidebar brand icon positioning, restores Brand Logo display on login and registration pages, restores PDF Embedder plugin compatibility, and provides an extensible media modal developer API.

= 3.0.14 =
Fixes post category and hierarchical taxonomy persistence, resolves multi-select array serialization, and guarantees term assignment across non-admin user roles.

= 3.0.13 =
Resolves Payments Dashboard controller authorization and cleans deprecated upsell classes.

= 3.0.12 =
Fixes mobile hamburger drawer responsiveness and wptexturize script syntax errors, prevents mobile header from showing on desktop viewports, and optimizes layout for 320px mobile screens.

= 3.0.10 =
Fixes Attachment Details sidebar rendering in the media modal when a single media file is selected, ensuring Alt Text, Title, Caption, and Description fields display and save properly.

= 3.0.9 =
Media modal improvements: Individual upload progress bars, full attachment details editing panel (Alt text, Title, Caption, Alignment, Link To, Size), instant filter dropdown performance, strict media type filtering, view isolation, and smooth "Load More Media" pagination.

= 3.0.8 =
Security and feature update: Fixed missing authorization and privilege escalation vulnerabilities across AJAX dispatchers (Thanks to Đỗ Trung Kiên - patchstack.com), upgraded Classic Editor media modal with multi-upload, multi-select, and file previews (Thanks to Muhammad Umer - https://github.com/mavinothkumar/frontend-dashboard/issues/16), and fixed post content smart quote formatting. Upgrade immediately.

= 3.0.7 =
Maintenance and compatibility update: Fixes Add-ons navigation access link, registers fed_addons alias, and prevents global CSS collisions.

= 3.0.6 =
Security update: Fixed REST API post endpoints with strict capability and permission verification. Upgrade immediately.

= 3.0.5 =
Security update: Fixed user registration parameter handling and hardened API/AJAX function routing with strict allowlists. Upgrade immediately.

= 3.0.4 =
Maintenance & feature update: Added BufferCode ecosystem plugins showcase (AdFuz & GateFuz) in Add-ons hub, fixed extension installation and activation class loader issue, registered missing module dependencies, and enhanced AJAX error handling.

= 3.0.3 =
Maintenance update: WordPress.org standards compliance, input unslashing/sanitization improvements, and cleanup.

= 3.0.2 =
Enhancement: Modernized Extensions Hub, modular hook architecture, and performance optimizations.

= 3.0.1 =
Minor update: Security escaping and WordPress standards compliance fixes.

= 3.0.0 =
Major release: Modern App Shell UI redesign, built-in block & rich text editors (Editor.js & TipTap), integrated templates & extra field support, and full compatibility with WordPress 6.7 and PHP 8.x. Legacy templates, extra fields, and pages add-ons are now part of Core.
