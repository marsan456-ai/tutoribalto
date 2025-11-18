# Tutoribalto Theme - Technical Overview

## 🏗️ Architecture

### Design Patterns

- **OOP (Object-Oriented Programming)** - All functionality encapsulated in classes
- **Singleton Pattern** - Each class instantiated once in `functions.php`
- **Hook Architecture** - WordPress actions/filters for extensibility
- **PSR-4 Autoloading** - Custom autoloader for clean class loading

### Code Organization

```
inc/
├── Core Classes (Theme Setup, Assets)
├── woocommerce/ (E-commerce functionality)
└── multilingual/ (WPML integration)
```

**Separation of Concerns:**
- Each class handles ONE specific responsibility
- No hardcoded values (constants or options)
- No mixing of business logic and presentation

## 🔧 Key Improvements from Basel Child Theme

### Security ✅

| Before (Basel Child) | After (Tutoribalto Theme) |
|---------------------|---------------------------|
| Direct `$_POST` access | `sanitize_text_field()` + `wp_unslash()` |
| No nonce verification | AJAX nonce checks |
| Direct DB queries | `$wpdb->prepare()` statements |
| Basic CF validation | **Full checksum validation** |
| No VAT validation | **Luhn algorithm validation** |

### Performance ⚡

| Before | After |
|--------|-------|
| 1625+ line functions.php | Modular files (~200 lines each) |
| Multiple `setInterval()` | Event-driven architecture |
| Inline scripts everywhere | Conditional loading |
| Translations via JS | PHP localization |
| No caching | Transient API support |

### Maintainability 🛠️

| Before | After |
|--------|-------|
| Hardcoded IDs | Constants/filters |
| Commented code | Clean, active code |
| No documentation | DocBlocks everywhere |
| Mixed conventions | PSR standards |

## 📊 Validation Algorithms

### Italian Fiscal Code (Codice Fiscale)

**Algorithm Implemented:**
```
1. Length check: 16 characters
2. Format check: [A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]
3. Checksum calculation:
   - Odd positions (1,3,5...): Use odd_chars lookup table
   - Even positions (2,4,6...): Use even_chars lookup table
   - Sum all values, mod 26, convert to letter
   - Compare with character at position 16
```

**Reference:** Official Italian Revenue Agency algorithm

### Italian VAT (Partita IVA)

**Algorithm Implemented:**
```
1. Format check: 11 digits
2. Luhn algorithm variant:
   - Even positions: multiply by 1
   - Odd positions: multiply by 2, if > 9 subtract 9
   - Sum all, check if divisible by 10
```

**Reference:** Italian tax code validation standard

## 🔌 Extensibility

### Custom Hooks Added

```php
// Extend wholesale registration
add_action('tutoribalto_wholesale_registration', 'my_custom_handler', 10, 3);

// Modify restricted countries
add_filter('tutoribalto_restricted_countries', 'my_country_list');

// Hook into header search
add_action('tutoribalto_header_search', 'my_search_widget');
```

### Filterable Values

```php
// Content width
apply_filters('tutoribalto_content_width', 1200);

// Restricted countries
apply_filters('tutoribalto_restricted_countries', $countries);

// WPML translated IDs
apply_filters('wpml_object_id', $id, $type, true, $lang);
```

## 🎯 WooCommerce Integration

### Hook Priority Strategy

| Hook | Priority | Reason |
|------|----------|--------|
| `wp_enqueue_scripts` | 1000 | Load after parent theme |
| `woocommerce_checkout_process` | 10 | Standard priority |
| `woocommerce_created_customer` | 10 | Before other plugins |
| `wp_footer` | 99 | Load upsell last |

### Template Override Strategy

Only override when absolutely necessary:
- ✅ `checkout/form-checkout.php` - Custom layout
- ✅ `single-product/meta.php` - Hide for specific categories
- ❌ `archive-product.php` - Use hooks instead

## 🌐 Multilingual Strategy

### Three-Level Translation

1. **PHP Translation Functions** - `__()`, `_e()`, `esc_html__()`
2. **WPML String Translation** - `icl_register_string()`
3. **JavaScript Localization** - `wp_localize_script()`

### Language Detection Flow

```
1. Check ICL_LANGUAGE_CODE constant (WPML)
2. Fallback to 'it' if not defined
3. Load corresponding translations from array
4. Pass to JavaScript via wp_localize_script
```

## 📦 Asset Loading Strategy

### CSS Loading Order

```
1. main.css (base styles)
2. woocommerce.css (conditional: only WC pages)
3. style.css (theme stylesheet, required by WP)
```

### JavaScript Loading Order

```
1. jQuery (WordPress core)
2. main.js (theme functionality)
3. checkout.js (conditional: checkout page)
4. product.js (conditional: product pages)
```

### Versioning

All assets use `TUTORIBALTO_THEME_VERSION` constant for cache busting.

## 🔐 Security Best Practices

### Input Validation

```php
// Always sanitize
$value = isset($_POST['field'])
    ? sanitize_text_field(wp_unslash($_POST['field']))
    : '';

// Validate format
if (!preg_match('/^[A-Z0-9]+$/', $value)) {
    // Invalid
}

// Use WC notice for errors
wc_add_notice(__('Error message', 'tutoribalto-theme'), 'error');
```

### Output Escaping

```php
// HTML content
echo esc_html($text);

// Attributes
echo '<a href="' . esc_url($url) . '" title="' . esc_attr($title) . '">';

// JavaScript data
wp_localize_script('handle', 'objectName', array(
    'value' => esc_js($value)
));
```

### Nonce Verification

```php
// Enqueue
wp_localize_script('script', 'data', array(
    'nonce' => wp_create_nonce('tutoribalto_nonce')
));

// Verify
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tutoribalto_nonce')) {
    wp_die('Security check failed');
}
```

## ⚙️ Configuration Management

### Theme Constants

```php
TUTORIBALTO_THEME_VERSION    // Theme version
TUTORIBALTO_THEME_DIR        // Theme directory path
TUTORIBALTO_THEME_URI        // Theme directory URI
TUTORIBALTO_THEME_INC        // Inc directory path
TUTORIBALTO_THEME_ASSETS     // Assets directory URI
TUTORIBALTO_MIN_PHP_VERSION  // Minimum PHP version
TUTORIBALTO_MIN_WP_VERSION   // Minimum WP version
TUTORIBALTO_MIN_WC_VERSION   // Minimum WC version
```

### Product IDs Management

**Recommended:** Use options or filter instead of hardcoding:

```php
// In class
private function get_cleaning_kit_id(): int {
    return (int) get_option('tutoribalto_cleaning_kit_id', 47771);
}

// Set via admin or plugin
update_option('tutoribalto_cleaning_kit_id', 12345);
```

## 🧪 Testing Recommendations

### Unit Testing (Future)

Recommended structure with PHPUnit:
```
tests/
├── test-wc-pricing.php
├── test-wc-checkout.php
└── test-wpml-integration.php
```

### Manual Testing Scenarios

1. **Wholesale Flow**
   - Register as veterinarian
   - Verify admin notification sent
   - Check `wholesale_pending` meta
   - Approve user
   - Login and verify wholesale prices

2. **Geo-Restriction**
   - Add product to cart
   - Change country to US
   - Attempt checkout
   - Verify error message displayed
   - Check dealer link works

3. **Fiscal Code Validation**
   - Enter valid CF: `RSSMRA80A01H501U`
   - Verify accepted
   - Enter invalid CF: `ABCDEF12G34H567I`
   - Verify rejected with correct error

4. **Multilingual**
   - Switch to each language
   - Verify form labels translate
   - Verify product prices display
   - Verify geo-restriction messages translate

## 📈 Performance Metrics

### Goals

- **Time to First Byte (TTFB):** < 200ms
- **First Contentful Paint (FCP):** < 1.5s
- **Largest Contentful Paint (LCP):** < 2.5s
- **Cumulative Layout Shift (CLS):** < 0.1

### Optimization Techniques Used

- ✅ Conditional script loading
- ✅ Lazy loading (images via WP core)
- ✅ No `setInterval()` on page load
- ✅ Event delegation over multiple listeners
- ✅ Debouncing user input validation

## 🔄 Migration Path from Basel Child

### Step-by-Step Migration

1. **Backup current site** (database + files)
2. **Install Tutoribalto Theme** alongside Basel
3. **Configure menus** in new theme
4. **Test on staging** environment
5. **Update product IDs** if different
6. **Activate theme** on production
7. **Monitor** for 24-48 hours
8. **Deactivate Basel** if no issues

### Data Migration Notes

- ✅ All WooCommerce data preserved
- ✅ WPML translations preserved
- ✅ User roles/meta preserved
- ⚠️ Custom CSS may need adjustment
- ⚠️ Widget areas need reconfiguration

## 🆘 Troubleshooting

### Common Issues

**Issue:** White screen after activation
**Solution:** Check PHP version (must be 8.0+), check error logs

**Issue:** WooCommerce styles broken
**Solution:** Clear cache, regenerate CSS in WC settings

**Issue:** Wholesale prices not showing
**Solution:** Verify user has `wholesale_customer` role, check `_wholesale_price` meta on variations

**Issue:** Geo-restrictions not working
**Solution:** Verify country code matches (IT not Italy), check filter `tutoribalto_restricted_countries`

**Issue:** WPML strings not translating
**Solution:** Scan theme for strings (WPML → String Translation → Scan)

## 📞 Support Contacts

For technical support:
- Code issues: Review inline DocBlocks
- WooCommerce issues: Check WC logs (WC → Status → Logs)
- WPML issues: WPML support portal

---

**Last Updated:** 2025-01-17
**Theme Version:** 1.0.0
