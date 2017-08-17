# frontend-dashboard
Frontend Dashboard is bundled with huge list of custom features which can easily customise the User profile, Posts, Login, Register, Custom roles on custom front page.

[Help | Frontend Dashboard Home](https://buffercode.com/plugin/frontend-dashboard)

### Description
Frontend Dashboard is bundled with the huge list of custom features which can easily customise the User profile, Posts, Login, Register, Custom roles on the custom front page.

1. Create custom User role
2. Custom Login, Register and Forgot Password
3. User roles to manage the Posts in frontend dashboard
4. Create custom user profiles with variety of form data
5. Create multiple dashboard menus with user role management
6. Enable/Disable the post default fields.
7. Show user role based custom profile page
8. Restrict the number of posts to show in the custom profile page.

### Installation
1. Upload the “frontend-dashboard” directory to the plugins directory.
2. Go to the plugins setting page and activate “Frontend Dashboard”
3. Go to Frontend Dashboard | Frontend Dashboard | Check for your settings
4. Do save.

### Frequently Asked Questions
**How to create all in one login page [login, register, forgot password and dashboard]**
1. Please go to Admin Dashboard | Pages | Add New Pages
2. Give appropriate title
3. Add shortcode in content area [fed_login]
4. Change Page Attributes Template to FED Login [In Right Column]
5. Publish the page.

**How to create single page for login, register and forgot password**
1. Please go to Admin Dashboard | Pages | Add New Pages
2. Give appropriate title [As we are creating for Login Page]
3. Add shortcode in content area [fed_login_only]
4. Change Page Attributes Template to FED Login [In Right Column]
5. For Register and Forgot Password, create the pages similar to above-mentioned instruction and add the shortcode for Register [fed_register_only] and for Forgot password [fed_forgot_password_only]
6. Publish the page.

**How to create the dashboard page**
1. Please go to Admin Dashboard | Pages | Add New Pages
2. Give appropriate title
3. Add shortcode in content area [fed_dashboard]
4. Change Page Attributes Template to FED Login [In Right Column]
5. Publish the page.

Then Please go to Frontend Dashboard | Frontend Dashboard | Login (Tab) | Settings (Tab) | Please change the appropriate pages for the settings.


**How to create dashboard menu**
1. Please go to Frontend Dashboard | Dashboard Menu
2. Click Add New Menu
3. Fill the input as per your requirement and select the appropriate user roles to view the menu.
4. Click Add New Menu


**How to add new custom user profile**
1. Please go to Frontend Dashboard | User Profile
2. Click Add New Extra User Profile Field
3. Select the required input type from the dropdown ‘Add New Profile Field’
4. Fill the input fields and submit to save


**How to add new custom post field**
1. Please go to Frontend Dashboard | Post Fields
2. Click Add New Extra User Post Field
3. Select the required input type from the dropdown ‘Add New Post Field’
4. Fill the input fields and submit to save


**List of Shortcodes**
1. [fed_login] to generate login, registration, and reset forms
2. [fed_login_only] to show only login page
3. [fed_register_only] to show only register page
4. [fed_forgot_password_only] to generate the forgot password page
5. [fed_dashboard] to generate the dashboard page
6. [fed_user role=user_role] to generate the role based user page

### Changelog

**v1.1.1 [17-August-2017]**
* Collapse/Expand the frontend dashboard menu
* Bug fixes
* Refactored for developers comfort

**v1.1 [11-August-2017]**
* Added more filter and action hooks for developers
* Minor: Bug fixed


**v1.0 [04-August-2017]**
- Public release

### Upgrade Notice
**v1.1.1 [17-August-2017]**
* Collapse/Expand the frontend dashboard menu
* Bug fixes
* Refactored for developers comfort