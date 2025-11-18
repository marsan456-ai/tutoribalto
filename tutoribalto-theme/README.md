# Tutoribalto Theme

Custom WordPress theme for Tutoribalto - Tutori ortopedici per cani.

## 📋 Overview

This is a fully custom WordPress theme built from scratch to replace the Basel child theme. It's optimized for WooCommerce B2C/B2B e-commerce with advanced multilingual support (WPML).

### Version
**1.0.0**

### Author
Tutoribalto Development Team

## ✨ Features

### Core Functionality
- ✅ **100% Custom Theme** - No parent theme dependencies
- ✅ **Object-Oriented Architecture** - Clean, modular PHP classes
- ✅ **Security Hardened** - Proper sanitization, validation, and nonce checks
- ✅ **Performance Optimized** - Lazy loading, conditional script loading
- ✅ **WCAG AA Accessible** - Semantic HTML, ARIA labels
- ✅ **Fully Responsive** - Mobile-first design

### E-Commerce Features
- 🛒 **WooCommerce Integration** - Full support for WC 7.0+
- 💰 **Dual Pricing System** - Retail and wholesale prices
- 🏢 **B2B Wholesale Module** - Veterinarian registration and approval
- 🌍 **Geo-Restrictions** - Block checkout for specific countries, redirect to dealers
- 📋 **Custom Checkout Validation** - Italian fiscal code, VAT number validation
- 🎁 **Upsell Popup** - Cleaning kit promotion after add-to-cart

### Multilingual
- 🌐 **5 Languages** - IT, EN, DE, FR, ES via WPML
- 🔄 **Dynamic Translations** - JavaScript and PHP localization
- 🎯 **WPML Integration** - Full compatibility with WPML

## 🗂️ Structure

```
tutoribalto-theme/
├── style.css                    # Theme header
├── functions.php                # Main orchestrator (< 100 lines)
├── screenshot.png               # Theme screenshot
│
├── assets/
│   ├── css/
│   │   ├── main.css            # Base styles
│   │   ├── woocommerce.css     # WooCommerce styles
│   │   └── admin.css           # Admin styles
│   ├── js/
│   │   ├── main.js             # Core JavaScript
│   │   ├── checkout.js         # Checkout logic
│   │   └── product-recommendations.js
│   └── images/
│
├── inc/
│   ├── class-theme-setup.php
│   ├── class-assets-manager.php
│   │
│   ├── woocommerce/
│   │   ├── class-wc-customizations.php
│   │   ├── class-wc-pricing.php
│   │   ├── class-wc-wholesale.php
│   │   ├── class-wc-checkout.php
│   │   ├── class-wc-geo-restrictions.php
│   │   └── class-wc-product-display.php
│   │
│   └── multilingual/
│       └── class-wpml-integration.php
│
├── templates/
│   ├── header.php
│   ├── footer.php
│   └── parts/
│       └── upsell-popup.php
│
├── woocommerce/              # WC template overrides
│   ├── checkout/
│   ├── single-product/
│   ├── myaccount/
│   └── emails/
│
└── languages/
    └── tutoribalto-theme.pot
```

## 📦 Requirements

- **WordPress** 6.0+
- **PHP** 8.0+
- **WooCommerce** 7.0+
- **WPML** (optional, for multilingual)
- **ACF** (optional, for custom fields)

## 🚀 Installation

1. **Upload theme:**
   ```bash
   cd wp-content/themes/
   git clone [repository-url] tutoribalto-theme
   ```

2. **Activate theme:**
   - Go to Appearance → Themes
   - Activate "Tutoribalto Theme"

3. **Configure menus:**
   - Appearance → Menus
   - Create/assign menus to:
     - Primary Menu
     - Menu di Destra
     - Footer Menu

4. **Configure WooCommerce:**
   - WooCommerce → Settings
   - Ensure Italy (IT) is primary country
   - Set currency to EUR

5. **WPML Configuration** (if using):
   - Install WPML + WooCommerce Multilingual
   - Configure languages: IT (default), EN, DE, FR, ES
   - String translation: Scan theme for translatable strings

## ⚙️ Configuration

### Wholesale Role

The theme creates a `wholesale_customer` role automatically. To approve wholesale users:

1. Go to Users → All Users
2. Find user with meta `wholesale_pending = 1`
3. Manually verify credentials
4. Delete `wholesale_pending` meta to approve

### Geo-Restrictions

Restricted countries are configured in `class-wc-geo-restrictions.php`:

```php
$this->restricted_countries = array(
    'US' => array( 'dealer_name' => 'Balto USA', 'dealer_url' => 'https://baltousa.com/' ),
    'CA' => array( 'dealer_name' => 'Balto Canada', 'dealer_url' => 'https://baltocanada.com/' ),
    // ... more countries
);
```

To add/remove countries, filter:
```php
add_filter( 'tutoribalto_restricted_countries', 'my_custom_restrictions' );
```

### Custom Product IDs

Update product IDs in `class-wc-customizations.php`:

```php
private function get_excluded_product_ids( string $lang ): array {
    $base_ids = array( 47771, 48414, 48667, 48772 ); // Update these
    // ...
}
```

## 🧩 Key Classes

| Class | Purpose |
|-------|---------|
| `Tutoribalto_Theme_Setup` | Theme features, menus, widget areas |
| `Tutoribalto_Assets_Manager` | Enqueue CSS/JS with localization |
| `Tutoribalto_WC_Pricing` | Variable pricing + wholesale prices |
| `Tutoribalto_WC_Wholesale` | B2B registration and approval |
| `Tutoribalto_WC_Checkout` | Validation (fiscal code, VAT, email) |
| `Tutoribalto_WC_Geo_Restrictions` | Country-based checkout blocking |
| `Tutoribalto_WC_Product_Display` | Loop customizations, filters |
| `Tutoribalto_WPML_Integration` | Multilingual support |

## 🎨 Customization

### Adding New Languages

1. **Add translations in** `class-assets-manager.php`:
   ```php
   $strings = array(
       'pt' => array( 'from' => 'a partir de', ... ),
   );
   ```

2. **Update geo-restrictions** in `class-wc-geo-restrictions.php`

3. **Translate strings** via WPML String Translation

### Custom Hooks

```php
// Header search area
do_action( 'tutoribalto_header_search' );

// After wholesale registration
do_action( 'tutoribalto_wholesale_registration', $customer_id, $data, $password );

// Filter restricted countries
apply_filters( 'tutoribalto_restricted_countries', $countries );
```

## 🔒 Security

### Implemented Protections

- ✅ **Input Sanitization** - All `$_POST` data sanitized
- ✅ **Output Escaping** - `esc_html()`, `esc_url()`, `esc_attr()`
- ✅ **Nonce Verification** - AJAX requests verified
- ✅ **Prepared Statements** - Database queries use `$wpdb->prepare()`
- ✅ **Capability Checks** - Admin functions check `current_user_can()`
- ✅ **Validation** - Fiscal code checksum, VAT number Luhn algorithm

### Fiscal Code Validation

Italian fiscal code validation includes:
- Length check (16 characters)
- Format check (regex pattern)
- **Checksum validation** (official algorithm)

### VAT Number Validation

Italian Partita IVA validation includes:
- 11 digits format
- **Luhn algorithm variant** for checksum

## 🧪 Testing

### Manual Testing Checklist

- [ ] Install on fresh WordPress
- [ ] Activate theme (check for PHP errors)
- [ ] Test all 5 languages (WPML)
- [ ] Checkout as private customer (IT)
- [ ] Checkout as company (IT)
- [ ] Test geo-restrictions (US, CA, GB, etc.)
- [ ] Wholesale registration flow
- [ ] Upsell popup appears
- [ ] Variable product pricing displays correctly
- [ ] Mobile responsiveness

### Test Credentials

**Wholesale Test:**
- Username: `vet_test`
- Role: `wholesale_customer`
- Expected: See wholesale prices

**Geo-Restriction Test:**
- Country: `US`
- Expected: Checkout blocked, see Balto USA message

## 📝 Changelog

### Version 1.0.0 - 2025-01-17
- ✨ Initial release
- 🏗️ Complete rewrite from Basel child theme
- 🔧 Modular OOP architecture
- 🛡️ Security hardening (sanitization, validation)
- 🌐 Full WPML integration
- 🛒 WooCommerce B2C/B2B features
- 📱 Responsive design
- ♿ WCAG AA accessibility

## 🐛 Known Issues

None at this time.

## 📚 Documentation

- [WordPress Codex](https://codex.wordpress.org/)
- [WooCommerce Docs](https://woocommerce.com/documentation/)
- [WPML Docs](https://wpml.org/documentation/)

## 👥 Credits

**Developed by:** Tutoribalto Development Team
**Based on:** Original Basel child theme customizations
**Migrated & Optimized:** 2025

## 📄 License

This theme is licensed under the GPL v2 or later.

## 🆘 Support

For issues and questions:
1. Check this README
2. Review code comments (DocBlocks)
3. Contact development team

---

**Made with ❤️ for Tutoribalto**
