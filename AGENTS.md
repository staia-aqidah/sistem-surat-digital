# IAI Surat Digital - WordPress Plugin

## Plugin Structure
- Main file: `iai-surat.php`
- Core functionality: `includes/` directory
- Letter configurations: `config/` directory
- Assets: `assets/` directory
- Generated letters: `letters/` directory

## Key Features
- Shortcode `[form_surat]` renders letter selection form
- Form submission with nonce verification (24-hour validity)
- Print view: `?ks_print={token}`
- PDF generation: `?ks_pdf={token}`
- AJAX nonce refresh: `wp_ajax_ks_refresh_nonce`
- Rate limiting: 10 submissions/IP/5min
- Prevents WP canonical redirects for print/PDF

## Development Notes
- WordPress plugin - requires WP installation to test
- Follows WP plugin development conventions
- No build/test scripts - manual testing in WP environment
- Uses transients for rate limiting
- Output buffering for form rendering (`ob_start()`/`ob_get_clean()`)

## Important Conventions
- Nonce action: `'ks_form'`
- AJAX action: `'ks_refresh_nonce'`
- GET params: `ks_print` (preview), `ks_pdf` (download)
- Form field prefix: `ks_fields[field_name]`
- Hidden fields: `ks_submit`, `ks_jenis`