# Autonomous AI Development & Quality Protocol for Frontend Dashboard & Add-ons

You are developing the WordPress plugin **Frontend Dashboard** and its suite of add-ons:
- `frontend-dashboard` (Core)
- `frontend-dashboard-membership`
- `frontend-dashboard-for-woocommerce`
- `frontend-dashboard-custom-post`
- `frontend-dashboard-captcha`
- `frontend-dashboard-notification`
- `frontend-dashboard-social-chat`
- `frontend-dashboard-social-connect`
- `frontend-dashboard-templates`
- `frontend-dashboard-user-management`
- `fed-pay-per-post`
- `gate-fuz`

As an AI coding assistant, you MUST follow this protocol without exception. You are responsible for writing secure, standard-compliant, self-verified code.

---

## 1. WordPress Security Standards (The "Rule of 4")

Every endpoint, form handler, AJAX action, or REST API callback MUST follow the **Rule of 4**:

1. **User Capability Check**:
   Always verify the current user has the correct permissions before doing anything.
   ```php
   if ( ! current_user_can( 'manage_options' ) ) {
       wp_send_json_error( __( 'Unauthorized access', 'frontend-dashboard' ), 403 );
   }
   ```
2. **Nonce Verification**:
   Frontend Dashboard uses `'fed_nonce'` as the standard action. Always verify using FED's helper or middleware:
   ```php
   $nonce = isset( $_REQUEST['fed_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['fed_nonce'] ) ) : '';
   if ( ! \FED\Http\Security::verifyNonce( $nonce, 'fed_nonce' ) ) {
       wp_send_json_error( __( 'Invalid security token', 'frontend-dashboard' ), 403 );
   }
   ```
3. **Strict Input Sanitization**:
   Never trust `$_POST`, `$_GET`, or `$_REQUEST`. Always sanitize:
   - Strings: `sanitize_text_field( wp_unslash( $_POST['field'] ) )`
   - Numbers: `absint( $_POST['id'] )` or `intval( ... )`
   - Keys / Slugs: `sanitize_key( $_POST['slug'] )`
   - HTML content: `wp_kses_post( wp_unslash( $_POST['content'] ) )`
4. **Strict Output Escaping**:
   Never `echo` or print variables without an escaping function:
   - Plain text: `esc_html( $var )` or `esc_html__( 'Text', 'frontend-dashboard' )`
   - Attributes: `esc_attr( $var )` or `esc_attr__( 'Text', 'frontend-dashboard' )`
   - URLs: `esc_url( $var )`
   - Rich HTML: `wp_kses_post( $var )`
   - JSON: `wp_json_encode( $var )`

---

## 2. WordPress Core API Best Practices

- **File operations**: Never use raw PHP `unlink()`, `file_get_contents()`, `file_put_contents()`, or `curl`. Always use WordPress APIs (`wp_delete_file()`, `wp_remote_get()`, `$wp_filesystem`).
- **Database operations**: Never concatenate variables into SQL strings. Always use `$wpdb->prepare()`.
- **Internationalization (i18n)**:
  - Use textdomain `'frontend-dashboard'` (or matching addon textdomain).
  - When using placeholders (`%s`, `%d`), ALWAYS include a translators comment directly above the line:
    ```php
    /* translators: %s: Menu label */
    printf( esc_html__( 'Menu item %s was saved.', 'frontend-dashboard' ), esc_html( $label ) );
    ```

---

## 3. Mandatory Autonomous Verification Protocol

Whenever you write, edit, or refactor code in any plugin or add-on:

1. **Auto-Format Code**:
   Run:
   ```cmd
   D:\localserver\fed\check-quality.bat <plugin-slug> format
   ```
   (e.g. `D:\localserver\fed\check-quality.bat frontend-dashboard format`)
2. **Run WordPress Standards & Security Audit**:
   Run:
   ```cmd
   "D:\localhost-setup\wp-cli\wp.bat" plugin check <plugin-slug> --include-low-severity-warnings --include-low-severity-errors
   ```
3. **Run Zero-Blindspot Browser & Debug Log Sentinel**:
   Run `php check-debug-log.php verify` (or e2e test suite) to guarantee:
   - Zero JavaScript console errors or unhandled page exceptions.
   - Zero failed HTTP/AJAX requests (4xx / 5xx).
   - Zero PHP warnings, notices, fatal errors, or database failures in `wp-content/debug.log`.
4. **Zero-Defect Rule**:
   - If `wp plugin check`, `phpcs`, or the Sentinel test reports any **ERRORS** or **WARNINGS** on lines you touched, you MUST fix them autonomously before concluding.
   - Do NOT ask the user to fix styling, escaping, or translation warnings—fix them yourself.

---

## 4. Documentation Synchronization

Whenever you add a new setting, feature, shortcode, or filter to Frontend Dashboard or any addon:
- Update the documentation in the companion docs project: `D:\localserver\fed-faq`.
- Follow the existing Astro / Starlight documentation structure.
