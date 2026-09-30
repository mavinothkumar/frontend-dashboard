=== Frontend Dashboard ===
Contributors: vinoth06, buffercode
Tags: dashboard, frontend dashboard, custom login, custom register, custom dashboard
Donate link: https://www.paypal.com/paypalme2/buffercode
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 3.0.3
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

= 3.0.3 =
Maintenance update: WordPress.org standards compliance, input unslashing/sanitization improvements, and cleanup.

= 3.0.2 =
Enhancement: Modernized Extensions Hub, modular hook architecture, and performance optimizations.

= 3.0.1 =
Minor update: Security escaping and WordPress standards compliance fixes.

= 3.0.0 =
Major release: Modern App Shell UI redesign, built-in block & rich text editors (Editor.js & TipTap), integrated templates & extra field support, and full compatibility with WordPress 6.7 and PHP 8.x. Legacy templates, extra fields, and pages add-ons are now part of Core.
