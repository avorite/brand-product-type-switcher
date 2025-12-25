# Installation and Quick Start Guide

## Installation

### Method 1: Manual Installation

1. Download the plugin from GitHub or as a ZIP file
2. Extract the `brand-product-type-switcher` folder
3. Upload the folder to your WordPress installation at `/wp-content/plugins/`
4. Activate the plugin through the **Plugins** menu in WordPress
5. Navigate to **WooCommerce → Brand Type Switcher** in the admin menu

### Method 2: Via Git
 
```bash
cd wp-content/plugins
git clone https://github.com/avorite/brand-product-type-switcher.git
```

Then activate the plugin through the WordPress admin panel.

## Quick Start

### Step 1: Select Brands
- Check the boxes next to the brands you want to modify
- Use "Select All" to select all brands at once
- Each brand displays the number of products it contains

### Step 2: Choose Product Type
Select from the dropdown menu:
- **Simple** - Regular purchasable products
- **External/Affiliate product** - External/affiliate products

### Step 3: Save Changes
- Click the **"Save Changes"** button
- Product processing will begin automatically

### Step 4: Monitor Progress
- Watch the progress bar (percentage completion)
- View statistics (successful/errors)
- Read operation logs in real-time
- Processing completes automatically

## Key Features

✅ **Product URL Preservation**: Product URLs are automatically preserved and restored when switching product types

✅ **Batch Processing**: Products are processed in batches of 5 with a 500ms delay between batches to prevent server overload

✅ **Real-time Progress**: See percentage completion and number of processed products

✅ **Detailed Logging**: All operations are logged with success or error indicators

✅ **Statistics**: Track successful operations and errors during processing

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.4 or higher
- `product_brand` taxonomy must exist (custom taxonomy for products)

## Troubleshooting

### Brands Not Loading
- Ensure the `product_brand` taxonomy exists
- Verify that brands have products assigned
- Check that WooCommerce is active

### Processing Stops
- Check browser console for JavaScript errors
- Verify server can handle AJAX requests
- Check WordPress debug log for PHP errors

### Product URL Not Preserved
- Ensure products have Product URL set before switching
- Check WooCommerce product meta fields
- Verify product type is correctly set

## Support

For support, please:
- Open an issue on [GitHub](https://github.com/avorite/brand-product-type-switcher)
- Check the [README.md](README.md) for detailed documentation
- Review the troubleshooting section above

## Additional Resources

- [Full Documentation](README.md)
- [GitHub Repository](https://github.com/avorite/brand-product-type-switcher)
- [GitHub Setup Guide](GITHUB_SETUP.md)

---

**Need help?** Check the [README.md](README.md) for comprehensive documentation or open an issue on GitHub.

