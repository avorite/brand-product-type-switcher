# GitHub Setup Instructions

## Initial Setup

1. **Create a new repository on GitHub**
   - Go to https://github.com/new
   - Repository name: `brand-product-type-switcher`
   - Description: "WordPress plugin for bulk switching WooCommerce product types by brand"
   - Choose Public or Private
   - **Do NOT** initialize with README, .gitignore, or license (we already have these)

2. **Initialize Git in the plugin directory**

```bash
cd /home/avorite/projects/hbrdevv.wpengine.com.25.11.2025/wp-content/plugins/brand-product-type-switcher
git init
git add .
git commit -m "Initial commit: Brand Product Type Switcher v1.0.0"
```

3. **Add remote and push**

```bash
git remote add origin https://github.com/avorite/brand-product-type-switcher.git
git branch -M main
git push -u origin main
```

## Plugin Header

The plugin header in `brand-product-type-switcher.php` is already updated with:
- Plugin URI: https://github.com/avorite/brand-product-type-switcher
- Author: Maxim Shiyan
- Author URI: https://github.com/avorite

## Repository Settings

### Topics/Labels (recommended)
Add these topics to your repository:
- `wordpress`
- `wordpress-plugin`
- `woocommerce`
- `product-type`
- `bulk-edit`
- `brand-management`

### Description
Use this description:
```
WordPress plugin for bulk switching WooCommerce product types (Simple/External) by brand with progress tracking and logging. Preserves Product URL during type switching.
```

## Release Setup

### Create Release v1.0.0

1. Go to **Releases** → **Create a new release**
2. Tag: `v1.0.0`
3. Release title: `Brand Product Type Switcher v1.0.0`
4. Description:

```markdown
## 🎉 Initial Release

### Features
- Bulk product type switching by brand
- Real-time progress tracking
- Detailed logging
- Product URL preservation
- Batch processing with delays

### Requirements
- WordPress 5.0+
- WooCommerce 3.0+
- PHP 7.4+
```

5. Attach the plugin zip file (optional but recommended)

## Creating Plugin Zip

To create a distributable zip file:

```bash
cd /home/avorite/projects/hbrdevv.wpengine.com.25.11.2025/wp-content/plugins
zip -r brand-product-type-switcher.zip brand-product-type-switcher/ -x "*.git*" "*.DS_Store"
```

## Future Updates

When making updates:

```bash
git add .
git commit -m "Description of changes"
git push origin main
```

Then create a new release tag:

```bash
git tag -a v1.0.1 -m "Version 1.0.1"
git push origin v1.0.1
```

