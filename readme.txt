=== Frontend Dashboard ===
Contributors: vinoth06, buffercode
Tags: dashboard, frontend dashboard, custom login, custom register, custom roles, custom profile, custom post type, custom taxonomies, custom dashboard
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7DHAEMST475BY
Requires at least: 4.6
Tested up to: 5.0.3
Stable tag: 1.3.3
License: GPL V3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Frontend Dashboard is bundled with huge list of custom features which can easily customise the User profile, Posts, Login, Register, Custom roles on custom front page.

== Description ==
Frontend Dashboard is bundled with the huge list of custom features which can easily customise the User profile, Posts, Login, Register, Custom roles on the custom front page.

1. Create custom User role
2. Custom Login, Register and Forgot Password
3. User roles to manage the Posts in frontend dashboard
4. Create custom user profiles with variety of form data
5. Create multiple dashboard menus with user role management
6. Enable/Disable the post default fields.
7. Show user role based custom profile page
8. Restrict the number of posts to show in the custom profile page.
9. Manage custom Post type and Taxonomies
10. Customize templates

= Frontend Dashboard Plugins List =
* [Frontend Dashboard Pages](https://buffercode.com/plugin/frontend-dashboard-pages)
* [Frontend Dashboard Extra](https://buffercode.com/plugin/frontend-dashboard-extra)
* [Frontend Dashboard Captcha](https://buffercode.com/plugin/frontend-dashboard-captcha)
* [Frontend Dashboard Templates](https://buffercode.com/plugin/frontend-dashboard-templates)
* [Frontend Dashboard Custom Post and Taxonomies](https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies)

= Videos =

For more video : [Frontend Dashboard](https://buffercode.com/category/name/frontend-dashboard)

* [How to create custom login for Frontend Dashboard](https://buffercode.com/post/how-to-create-custom-login-for-frontend-dashboard-wordpress-plugin)

* [How to create Dashboard for Frontend Dashboard](https://buffercode.com/post/how-to-create-dashboard-for-frontend-dashboard-wordpress-plugin)

* [How to set Redirect on Login for Frontend Dashboard](https://buffercode.com/post/how-to-set-redirect-on-login-for-frontend-dashboard-wordpress-plugin)

* [How to set Widget for Frontend Dashboard](https://buffercode.com/post/how-to-set-widget-for-frontend-dashboard-wordpress-plugin)

* [How to Create Custom User Role in Frontend Dashboard](https://buffercode.com/post/how-to-create-custom-user-role-in-frontend-dashboard-wordpress-plugin)

* [How to create a page for Users, based on their User Role](https://buffercode.com/post/how-to-create-a-page-for-users-based-on-their-user-role)

* [How new user can select user role on registration](https://buffercode.com/post/how-new-user-can-select-user-role-on-registration)

* [How to customise the layout colours in Frontend Dashboard](https://buffercode.com/post/how-to-customise-the-layout-colours-in-frontend-dashboard)

* [How to manage post options in Frontend Dashboard](https://buffercode.com/post/how-to-manage-post-options-in-frontend-dashboard)

* [How to create custom menu in Frontend Dashboard](https://buffercode.com/post/how-to-create-custom-menu-in-frontend-dashboard)

For more video : [Frontend Dashboard](https://buffercode.com/category/name/frontend-dashboard)


== Installation ==
1. Upload the “frontend-dashboard” directory to the plugins directory.
2. Go to the plugins setting page and activate “Frontend Dashboard”
3. Go to Frontend Dashboard | Frontend Dashboard | Check for your settings
4. Do save.

== Frequently Asked Questions ==
= How to create all in one login page [login, register, forgot password and dashboard] =

1. First we need to create a new page for custom login.

2. Please go to Admin Dashboard | Pages | Add New Pages

3. Give appropriate title

4. Add shortcode in content area [fed_login]

5. Change Page Attributes Template to FED Login [In Right Column]

6. Publish the page.

7. Navigate to Frontend Dashboard | Frontend Dashboard | Login |  Settings

8. Change the Login Page URL to newly created custom login page.

9. Save the settings.

= How to create single page for login, register and forgot password =

1. Please go to Admin Dashboard | Pages | Add New Pages

2. Give appropriate title [As we are creating for Login Page]

3. Add shortcode in content area [fed_login_only]

4. Change Page Attributes Template to FED Login [In Right Column]

5. For Register and Forgot Password, create the pages similar to above-mentioned instruction and add the shortcode for Register [fed_register_only] and for Forgot password [fed_forgot_password_only]

6. Publish the page.

= How to set Widget =

1.	Navigate to Appearance | Widgets

2.	Add the “Text Widget” to the appropriate location.

3.	Add shortcode "[fed_login]" in to the textarea [without double quotation]

4.	Save the settings.

= How to create the dashboard page =

1. Please go to Admin Dashboard | Pages | Add New Pages

2. Give appropriate title

3. Add shortcode in content area [fed_dashboard]

4. Change Page Attributes Template to FED Login [In Right Column]

5. Publish the page.

Then Please go to Frontend Dashboard | Frontend Dashboard | Login (Tab) | Settings (Tab) | Please change the appropriate pages for the settings.


= Redirect on Login =

1. Navigate to Frontend Dashboard | Frontend Dashboard | Login |  Settings

2. Change the "Redirect After Logged in URL" to your desired page.

3. Change the "Redirect After Logged out URL" to your desired page.

4. Save the settings.


= How to create dashboard menu =

1. Please go to Frontend Dashboard | Dashboard Menu

2. Click Add New Menu

3. Fill the input as per your requirement and select the appropriate user roles to view the menu.

4. Click Add New Menu


= How to add new custom user profile =

1. Please go to Frontend Dashboard | User Profile

2. Click Add New Extra User Profile Field

3. Select the required input type from the dropdown ‘Add New Profile Field’

4. Fill the input fields and submit to save


= How to add new custom post field =

1. Please go to Frontend Dashboard | Post Fields

2. Click Add New Extra User Post Field

3. Select the required input type from the dropdown ‘Add New Post Field’

4. Fill the input fields and submit to save

= How to Create Custom User Role =

1. Navigate to Frontend Dashboard | Frontend Dashboard | User | Add/Delete Custom Role

2. Enter the Role Name and press tab to generate the Role slug automatically.

3. Click Add.

= How to create a page for User Role =

1. Create a new page, Pages | Add New

2. Add fed_user role=user_role] in the content area, the user_role may be a default or custom user role, eg subscriber, editor.

3. Change the Page Attributes | Template | to FED Dashboard

4. Click Publish

5. Now you can have that particular user role page in that created page URL.

= List of shortcodes =

1. [fed_login] to generate login, registration, and reset forms

2. [fed_login_only] to show only login page

3. [fed_register_only] to show only register page

4. [fed_forgot_password_only] to generate the forgot password page

5. [fed_dashboard] to generate the dashboard page

6. [fed_user role=user_role] to generate the role based user page

== Changelog ==
= 1.3.3 [04-Feb-2019] =

* Bug fixes: Unnecessary assets loads, now the assets will load only in the Frontend Dashboard Shortcodes.

= 1.3.2 [10-Jan-2019] =

* Bug fixes: Admin Menu not saving.

= 1.3.1 [09-Jan-2019] =

* Bug fixes: Admin User Profile Menu not sorting in order
* Bug fixes: Frontend Dashboard menu not sorting in order
* New: Drag and Drop to sort the menu items in Dashboard Menu, User Profile and Post Fields

= 1.2.14 [23-May-2018] =

* Bug fixes: Reset Password not working when Login only selected.

* Update for FED Extra Plugin: Now the Admin bar can be enable or disable based on the User Role.

= 1.2.13 [13-Apr-2018] =

* Bug fixes: updated to support custom post/taxonomies add-on.

= 1.2.12 [31-March-2018] =

* Bug fixes: Post custom field value not saving.

= 1.2.11 [03-Feb-2018] =

* Bug fixes: Values not storing on custom field while registration

= 1.2.10 [29-January-2018] =

* Bug fixes for multiple user in single page on Shortcode.

= 1.2.9.3 [25-January-2018] =

* Bug: Post section checkbox not saving.

= 1.2.9.2 [24-January-2018] =

* Bug: Admin profile not showing properly

= 1.2.9.1 [20-January-2018] =

* Bug: Frontend Dashboard menu not loading proper items.

= 1.2.9 [16-January-2018] =

* Bug: Support to Template and Custom Post

= 1.2.8 [12-January-2018] =

* Bug: Post field not able to delete

= 1.2.7.5 [28-December-2017] =

* Bug: Dashboard redirect after wp admin restrict.
* Bug: Minor bug fixes

= 1.2.7.2 [27-December-2017] =

* More translation strings added
* Bug fixes.

= 1.2.6 [23-December-2017] =

* New: .POT Language file added
* New:  Translation added for Hebrew - Thanks to @Ronena100
* Removed: Now Minimum WordPress version required is 4.6 and above

= 1.2.4 [22-December-2017] =

* Bug: Reset password not working

= 1.2.3 [20-December-2017] =

* Bug: No proper redirect for Restrict WP Admin Area
* Bug: fixed and enhanced supportive plugins.

= 1.2.2 [16-December-2017] =
* Add Register and Forgot password link for login only page
* Added Login link for Register page

= 1.2.1 [08-December-2017] =
* Bug: Fixed on post meta error if no custom post meta added

= 1.2 [06-December-2017] =
* Completely changed the frontend dashboard from AJAX request to normal page reload request.

= 1.1.5.2 =
* Bug: Collapse menu not working properly

= 1.1.5.1 =
* Bug: Improper styling

= 1.1.5 =
* Bug: Updated admin footer text shown in all plugin page.
* Bug: WP Admin area restriction block ajax request.
* Enhanced: Frontend Dashboard for mobile view.
* Enhanced: Code refactored

= v1.1.4.9 [16-November-2017] =
* Support 4.9
* Bug: File loading problem

= v1.1.4.8.1 [15-November-2017] =
* New: Restrict user role(s) to access the WP Admin area.
* Bug: Missing file fixed

= v1.1.4.7.1 [09-November-2017] =
* Bug: Action and Filter hooks with same name conflict.
* Enhanced: Admin dashboard settings | login paged refactored

= v1.1.4.7 [07-November-2017] =
* Enhanced: Dashboard main menu collapse/un collapse icon added
* Enhanced: Admin Dashboard settings changed
* Bug: Disabled menu slug on edit
* Bug: Post page custom fields alignment fixed


= v1.1.4.6.1 [02-November-2017] =
* New: Added Register Redirect after registration
* New: Post content can be disabled
* Bug: While Login and Register loading icon missing
* Bug: Email not sending to new register
* Bug: Input type number can't able to add more than 6 six digits
* Enhanced: Dashboard Settings
* Enhanced: Dashboard Main Menu
* Enhanced: Add-Ons

= v1.1.4.5 [16-October-2017] =
* New action hooks added
* Bug fixes

= v1.1.4.4 [09-October-2017] =
* Main menu search box added to search the menu by its name
* Login failed and more bug fixes


= v1.1.4.3 [27-September-2017] =
* Customize the Frontend Dashboard Layout Color
* Bug fixes


= v1.1.4.2 [17-September-2017] =
* Major Bug fixes
* Document updated

= v1.1.4 [10-September-2017] =
* Plugin page update
* Bug fixes
* Added new filter and action hooks

= v1.1.3 [06-September-2017] =
* Plugin page added
* Bug fixes
* Added new filter and action hooks

= v1.1.2 [18-August-2017] =
* Changed the way of representing the Radio and Select input field.
* Bug fixes
* Added few filters.

= v1.1.1 [17-August-2017] =
* Collapse/Expand the frontend dashboard menu
* Bug fixes
* Refactored for developers comfort

= v1.1 [11-August-2017] =
* Added more filter and action hooks for developers
* Minor: Bug fixed

= v1.0 [04-August-2017] =
* Public release

== Upgrade Notice ==
= 1.3.3 [04-Feb-2019] =

* Bug fixes: Unnecessary assets loads, now the assets will load only in the Frontend Dashboard Shortcodes.

== Screenshots ==
1. Frontend Dashboard Settings | Login | Settings
2. Frontend Dashboard Settings | Login | Register
3. Frontend Dashboard Settings | Post | Settings
4. Frontend Dashboard Settings | Post | Dashboard Settings
5. Frontend Dashboard Settings | Post | Menu
6. Frontend Dashboard Settings | Post | Permission
7. Frontend Dashboard Settings | User | Add/Delete Custom Role
8. Frontend Dashboard Settings | User Profile Layout | Settings
9. Dashboard Menu
10. User Profile
11. Dashboard
12. Input fields
13. All in one Login
14. Login Only
15. Register Only
16. Reset Password
17. User Profiles
18. User Profile
19. Frontend Dashboard Settings | User Profile Layout | Color
20. Frontend Dashboard Settings | Login | Restrict WP Admin Area
