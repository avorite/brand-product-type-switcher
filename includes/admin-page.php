<?php
/**
 * Admin page template
 *
 * @package Brand_Product_Type_Switcher
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap bpt-s-admin-wrap">
    <h1><?php esc_html_e('Brand Product Type Switcher', 'brand-product-type-switcher'); ?></h1>
    
    <div class="bpt-s-container">
        <div class="bpt-s-form-section">
            <h2><?php esc_html_e('Select Brands', 'brand-product-type-switcher'); ?></h2>
            <p class="description"><?php esc_html_e('Select the brands whose products you want to change.', 'brand-product-type-switcher'); ?></p>
            
            <div class="bpt-s-brands-container">
                <div class="bpt-s-brands-header">
                    <label>
                        <input type="checkbox" id="bpt-s-select-all-brands" />
                        <strong><?php esc_html_e('Select All', 'brand-product-type-switcher'); ?></strong>
                    </label>
                </div>
                <div id="bpt-s-brands-list" class="bpt-s-brands-list">
                    <p class="bpt-s-loading"><?php esc_html_e('Loading brands...', 'brand-product-type-switcher'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bpt-s-form-section">
            <h2><?php esc_html_e('Select Product Type', 'brand-product-type-switcher'); ?></h2>
            <p class="description"><?php esc_html_e('Choose the product type to switch to.', 'brand-product-type-switcher'); ?></p>
            
            <select id="bpt-s-product-type" class="bpt-s-select">
                <option value=""><?php esc_html_e('-- Select Product Type --', 'brand-product-type-switcher'); ?></option>
                <option value="simple"><?php esc_html_e('С возможностью купить (Simple)', 'brand-product-type-switcher'); ?></option>
                <option value="external"><?php esc_html_e('Товар ссылка (External/Affiliate product)', 'brand-product-type-switcher'); ?></option>
            </select>
        </div>
        
        <div class="bpt-s-form-section">
            <button type="button" id="bpt-s-submit-btn" class="button button-primary button-large">
                <?php esc_html_e('Save Changes', 'brand-product-type-switcher'); ?>
            </button>
        </div>
    </div>
    
    <div id="bpt-s-progress-section" class="bpt-s-progress-section" style="display: none;">
        <h2><?php esc_html_e('Processing Progress', 'brand-product-type-switcher'); ?></h2>
        
        <div class="bpt-s-progress-bar-container">
            <div class="bpt-s-progress-bar">
                <div id="bpt-s-progress-bar-fill" class="bpt-s-progress-bar-fill" style="width: 0%;"></div>
            </div>
            <div class="bpt-s-progress-text">
                <span id="bpt-s-progress-percent">0%</span>
                <span id="bpt-s-progress-count">(0 / 0)</span>
            </div>
        </div>
        
        <div class="bpt-s-stats">
            <div class="bpt-s-stat-item">
                <span class="bpt-s-stat-label"><?php esc_html_e('Success:', 'brand-product-type-switcher'); ?></span>
                <span id="bpt-s-stat-success" class="bpt-s-stat-value">0</span>
            </div>
            <div class="bpt-s-stat-item">
                <span class="bpt-s-stat-label"><?php esc_html_e('Errors:', 'brand-product-type-switcher'); ?></span>
                <span id="bpt-s-stat-errors" class="bpt-s-stat-value">0</span>
            </div>
        </div>
        
        <div class="bpt-s-logs-container">
            <h3><?php esc_html_e('Logs', 'brand-product-type-switcher'); ?></h3>
            <div id="bpt-s-logs" class="bpt-s-logs"></div>
        </div>
    </div>
</div>

