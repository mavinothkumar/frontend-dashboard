=== Frontend Dashboard ===
Contributors: vinoth06, buffercode
Tags: dashboard, frontend dashboard, custom login, custom register, custom dashboard
Donate link: https://www.paypal.com/paypalme2/buffercode
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 3.0.11
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

= 3.0.11 (2026-10-10) =
* Fix: Resolved mobile layout display regression on mobile viewports (< 1024px) where the desktop sidebar width was forcing side-by-side horizontal compression.
* Feature: Added responsive mobile navigation top bar with hamburger menu toggle, off-canvas slide-over drawer navigation, and dismissible backdrop.
* Enhancement: Optimized mobile viewport padding across main dashboard cards, ensuring clean full-width content rendering across all mobile phone and tablet screen sizes.

= 3.0.10 (2026-10-09) =
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
