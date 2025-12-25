# Brand Product Type Switcher

![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![WooCommerce](https://img.shields.io/badge/WooCommerce-3.0%2B-green)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/License-GPL%20v2%2B-red)

A powerful WordPress plugin for bulk switching product types (Simple/External) for WooCommerce products organized by brand. Features real-time progress tracking, detailed logging, and preserves Product URL during type switching.

## 🚀 Features

- **Bulk Product Type Switching**: Change product types for multiple brands at once
- **Brand Selection**: Easy checkbox interface to select multiple brands
- **Product Type Options**:
  - С возможностью купить (Simple)
  - Товар ссылка (External/Affiliate product)
- **Progress Tracking**: Real-time progress bar with percentage and count
- **Detailed Logging**: Complete log of all operations with success/error indicators
- **Product URL Preservation**: Automatically preserves Product URL when switching types
- **Batch Processing**: Processes products in batches with delays to prevent server overload
- **Statistics**: Shows success and error counts during processing
- **WordPress Codex Compliant**: Follows WordPress coding standards and best practices

## 📋 Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.4 or higher
- `product_brand` taxonomy (custom taxonomy for products)

## 📦 Installation

For detailed installation instructions, see [INSTALLATION.md](INSTALLATION.md).

### Quick Installation

**Manual Installation:**
1. Download or clone this repository
2. Upload the `brand-product-type-switcher` folder to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **WooCommerce → Brand Type Switcher** in the admin menu

**Via Git:**
```bash
cd wp-content/plugins
git clone https://github.com/avorite/brand-product-type-switcher.git
```

## 🎯 Usage

1. **Navigate to Admin Page**
   - Go to **WooCommerce → Brand Type Switcher** in your WordPress admin

2. **Select Brands**
   - Check the brands you want to modify
   - Use "Select All" to select all brands at once
   - Each brand shows the number of products it contains

3. **Choose Product Type**
   - Select from the dropdown:
     - **С возможностью купить (Simple)**: Regular purchasable products
     - **Товар ссылка (External/Affiliate product)**: External/affiliate products

4. **Start Processing**
   - Click "Save Changes" button
   - Watch the progress bar and logs in real-time
   - The process runs in batches with automatic delays

5. **Review Results**
   - Check the statistics (Success/Errors)
   - Review the detailed logs
   - Processing completes automatically

## 🔧 How It Works

### Product Type Switching

When switching product types:
- **Simple → External**: Product URL is preserved if it exists
- **External → Simple**: Product URL is saved before switching and can be restored later
- **Same Type**: Products already of the target type are skipped

### Batch Processing

- Processes 5 products at a time
- 500ms delay between batches
- Prevents server overload on large product catalogs
- Real-time progress updates

### Session Management

- Each processing session is tracked with a unique ID
- Session data stored in WordPress options table
- Progress can be monitored via AJAX polling

## 📁 File Structure

```
brand-product-type-switcher/
├── brand-product-type-switcher.php  # Main plugin file
├── README.md                         # This file
├── includes/
│   └── admin-page.php               # Admin page template
└── assets/
    ├── css/
    │   └── admin.css               # Admin styles
    └── js/
        └── admin.js                # Admin JavaScript
```

## 🛠️ Development

### Code Standards

This plugin follows:
- WordPress Coding Standards
- WordPress Plugin API best practices
- WooCommerce integration standards
- PSR-12 PHP coding standards (where applicable)

### Hooks and Filters

The plugin uses standard WordPress hooks:
- `admin_menu` - Adds admin menu
- `admin_enqueue_scripts` - Enqueues scripts/styles
- `wp_ajax_*` - AJAX handlers

### AJAX Endpoints

- `bpt_s_get_brands` - Get list of brands
- `bpt_s_switch_product_types` - Initialize processing
- `bpt_s_process_batch` - Process batch of products
- `bpt_s_get_progress` - Get processing progress

## 🔒 Security

- All AJAX requests use nonce verification
- Capability checks (`manage_woocommerce`)
- Input sanitization and validation
- Output escaping
- Direct file access prevention

## 📝 Changelog

### 1.0.0
- Initial release
- Brand selection interface
- Product type switching (Simple/External)
- Progress tracking
- Detailed logging
- Product URL preservation

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Maxim Shiyan

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🐛 Troubleshooting

### Brands Not Loading
- Ensure `product_brand` taxonomy exists
- Check that brands have products assigned
- Verify WooCommerce is active

### Processing Stops
- Check browser console for JavaScript errors
- Verify server can handle AJAX requests
- Check WordPress debug log

### Product URL Not Preserved
- Ensure products have Product URL set before switching
- Check WooCommerce product meta
- Verify product type is correctly set

## 📞 Support

For support, please open an issue on [GitHub](https://github.com/avorite/brand-product-type-switcher) or contact the plugin author.

## 🙏 Credits

Developed by **Maxim Shiyan** following WordPress Codex standards and WooCommerce best practices.

**Author:** Maxim Shiyan  
**GitHub:** [@avorite](https://github.com/avorite)

---

**Made with ❤️ for WordPress & WooCommerce**

