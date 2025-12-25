<?php
/**
 * Plugin Name: Brand Product Type Switcher
 * Plugin URI: https://github.com/avorite/brand-product-type-switcher
 * Description: Allows bulk switching of product types (Simple/External) for products by brand with progress tracking and logging.
 * Version: 1.0.0
 * Author: Maxim Shiyan
 * Author URI: https://github.com/avorite
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: brand-product-type-switcher
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BPT_S_VERSION', '1.0.0');
define('BPT_S_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BPT_S_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BPT_S_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Brand_Product_Type_Switcher {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance of this class
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'check_woocommerce'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_bpt_s_get_brands', array($this, 'ajax_get_brands'));
        add_action('wp_ajax_bpt_s_switch_product_types', array($this, 'ajax_switch_product_types'));
        add_action('wp_ajax_bpt_s_get_progress', array($this, 'ajax_get_progress'));
        add_action('wp_ajax_bpt_s_process_batch', array($this, 'ajax_process_batch'));
    }
    
    /**
     * Check if WooCommerce is active
     */
    public function check_woocommerce() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e('Brand Product Type Switcher requires WooCommerce to be installed and active.', 'brand-product-type-switcher'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('Brand Product Type Switcher', 'brand-product-type-switcher'),
            __('Brand Type Switcher', 'brand-product-type-switcher'),
            'manage_woocommerce',
            'brand-product-type-switcher',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts($hook) {
        if ('woocommerce_page_brand-product-type-switcher' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'bpt-s-admin-style',
            BPT_S_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            BPT_S_VERSION
        );
        
        wp_enqueue_script(
            'bpt-s-admin-script',
            BPT_S_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            BPT_S_VERSION,
            true
        );
        
        wp_localize_script('bpt-s-admin-script', 'bptS', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bpt_s_nonce'),
            'strings' => array(
                'processing' => __('Processing...', 'brand-product-type-switcher'),
                'completed' => __('Completed!', 'brand-product-type-switcher'),
                'error' => __('Error occurred', 'brand-product-type-switcher'),
                'noBrandsSelected' => __('Please select at least one brand.', 'brand-product-type-switcher'),
                'noProductTypeSelected' => __('Please select a product type.', 'brand-product-type-switcher'),
            )
        ));
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include BPT_S_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
    /**
     * AJAX: Get brands
     */
    public function ajax_get_brands() {
        check_ajax_referer('bpt_s_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'brand-product-type-switcher')));
        }
        
        $brands = $this->get_brands();
        wp_send_json_success($brands);
    }
    
    /**
     * Get all brands
     */
    private function get_brands() {
        $brands = array();
        
        // Check if product_brand taxonomy exists
        if (!taxonomy_exists('product_brand')) {
            return $brands;
        }
        
        $terms = get_terms(array(
            'taxonomy' => 'product_brand',
            'hide_empty' => false,
        ));
        
        if (is_wp_error($terms) || empty($terms)) {
            return $brands;
        }
        
        foreach ($terms as $term) {
            $product_count = $this->get_brand_product_count($term->term_id);
            $brands[] = array(
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $product_count,
            );
        }
        
        return $brands;
    }
    
    /**
     * Get product count for brand
     */
    private function get_brand_product_count($term_id) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $term_id,
                ),
            ),
        );
        
        $query = new WP_Query($args);
        return $query->found_posts;
    }
    
    /**
     * AJAX: Switch product types
     */
    public function ajax_switch_product_types() {
        check_ajax_referer('bpt_s_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'brand-product-type-switcher')));
        }
        
        $brand_ids = isset($_POST['brand_ids']) ? array_map('intval', $_POST['brand_ids']) : array();
        $product_type = isset($_POST['product_type']) ? sanitize_text_field($_POST['product_type']) : '';
        
        if (empty($brand_ids)) {
            wp_send_json_error(array('message' => __('No brands selected.', 'brand-product-type-switcher')));
        }
        
        if (empty($product_type) || !in_array($product_type, array('simple', 'external'))) {
            wp_send_json_error(array('message' => __('Invalid product type.', 'brand-product-type-switcher')));
        }
        
        // Initialize processing session
        $session_id = $this->initialize_processing($brand_ids, $product_type);
        
        wp_send_json_success(array(
            'message' => __('Processing initialized.', 'brand-product-type-switcher'),
            'session_id' => $session_id,
        ));
    }
    
    /**
     * Initialize processing session
     */
    private function initialize_processing($brand_ids, $product_type) {
        $session_id = $this->get_session_id($brand_ids, $product_type);
        
        // Get all product IDs for selected brands
        $product_ids = $this->get_products_by_brands($brand_ids);
        
        // Store session data
        $session_data = array(
            'brand_ids' => $brand_ids,
            'product_type' => $product_type,
            'product_ids' => $product_ids,
            'total' => count($product_ids),
            'processed' => 0,
            'success' => 0,
            'errors' => 0,
            'logs' => array(),
            'start_time' => current_time('mysql'),
            'status' => 'processing',
        );
        
        update_option('bpt_s_session_' . $session_id, $session_data, false);
        
        return $session_id;
    }
    
    /**
     * AJAX: Process batch
     */
    public function ajax_process_batch() {
        check_ajax_referer('bpt_s_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'brand-product-type-switcher')));
        }
        
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => __('Session ID required.', 'brand-product-type-switcher')));
        }
        
        $result = $this->process_batch($session_id, $offset);
        wp_send_json_success($result);
    }
    
    /**
     * Process batch of products
     */
    private function process_batch($session_id, $offset) {
        $session_data = get_option('bpt_s_session_' . $session_id);
        
        if (!$session_data) {
            return array(
                'error' => true,
                'message' => __('Session not found.', 'brand-product-type-switcher'),
            );
        }
        
        $product_ids = $session_data['product_ids'];
        $product_type = $session_data['product_type'];
        $batch_size = 5; // Process 5 products at a time
        $batch = array_slice($product_ids, $offset, $batch_size);
        
        if (empty($batch)) {
            // Processing complete
            $session_data['status'] = 'completed';
            $session_data['end_time'] = current_time('mysql');
            update_option('bpt_s_session_' . $session_id, $session_data, false);
            
            return array(
                'completed' => true,
                'processed' => $session_data['processed'],
                'total' => $session_data['total'],
                'success' => $session_data['success'],
                'errors' => $session_data['errors'],
                'logs' => array_slice($session_data['logs'], -10),
            );
        }
        
        // Process batch
        foreach ($batch as $product_id) {
            $result = $this->switch_product_type($product_id, $product_type);
            
            $session_data['processed']++;
            
            if ($result['success']) {
                $session_data['success']++;
                $log_message = sprintf(
                    __('Product #%d (%s) switched to %s successfully.', 'brand-product-type-switcher'),
                    $product_id,
                    isset($result['product_name']) ? $result['product_name'] : 'Unknown',
                    $product_type
                );
                // Only add if not already in logs (prevent duplicates)
                if (!in_array($log_message, $session_data['logs'])) {
                    $session_data['logs'][] = $log_message;
                }
            } else {
                $session_data['errors']++;
                $log_message = sprintf(
                    __('Product #%d: %s', 'brand-product-type-switcher'),
                    $product_id,
                    $result['message']
                );
                // Only add if not already in logs (prevent duplicates)
                if (!in_array($log_message, $session_data['logs'])) {
                    $session_data['logs'][] = $log_message;
                }
            }
        }
        
        update_option('bpt_s_session_' . $session_id, $session_data, false);
        
        $progress = 0;
        if ($session_data['total'] > 0) {
            $progress = round(($session_data['processed'] / $session_data['total']) * 100);
        }
        
        return array(
            'completed' => false,
            'offset' => $offset + $batch_size,
            'processed' => $session_data['processed'],
            'total' => $session_data['total'],
            'success' => $session_data['success'],
            'errors' => $session_data['errors'],
            'progress' => $progress,
            'logs' => array_slice($session_data['logs'], -10),
        );
    }
    
    /**
     * Switch product type
     */
    private function switch_product_type($product_id, $new_type) {
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return array(
                'success' => false,
                'message' => __('Product not found.', 'brand-product-type-switcher'),
            );
        }
        
        // Save Product URL and button text if they exist
        // Check both from product object and meta directly to ensure we don't lose data
        $product_url = '';
        $button_text = '';
        
        // Try to get from product object first (works for External products)
        if ($product->is_type('external')) {
            $product_url = $product->get_product_url();
            $button_text = $product->get_button_text();
        }
        
        // Also check meta directly as backup (works for all product types)
        $meta_url = get_post_meta($product_id, '_product_url', true);
        $meta_button_text = get_post_meta($product_id, '_button_text', true);
        
        // Use meta values if product object values are empty
        if (empty($product_url) && !empty($meta_url)) {
            $product_url = $meta_url;
        }
        if (empty($button_text) && !empty($meta_button_text)) {
            $button_text = $meta_button_text;
        }
        
        // Get current type
        $current_type = $product->get_type();
        
        // If already the correct type, skip
        if ($current_type === $new_type) {
            return array(
                'success' => true,
                'message' => __('Product already has this type.', 'brand-product-type-switcher'),
                'product_name' => $product->get_name(),
            );
        }
        
        // Change product type
        $term_result = wp_set_object_terms($product_id, $new_type, 'product_type');
        
        if (is_wp_error($term_result)) {
            return array(
                'success' => false,
                'message' => sprintf(__('Failed to set product type: %s', 'brand-product-type-switcher'), $term_result->get_error_message()),
            );
        }
        
        // Reload product to get updated type
        wc_delete_product_transients($product_id);
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return array(
                'success' => false,
                'message' => __('Failed to reload product after type change.', 'brand-product-type-switcher'),
            );
        }
        
        // Verify the type was actually changed
        $actual_type = $product->get_type();
        if ($actual_type !== $new_type) {
            return array(
                'success' => false,
                'message' => sprintf(__('Product type mismatch. Expected: %s, Got: %s', 'brand-product-type-switcher'), $new_type, $actual_type),
            );
        }
        
        // Restore Product URL and button text if switching to External
        // Always preserve URL and button text in meta, regardless of product type
        if ($new_type === 'external') {
            if (!empty($product_url)) {
                $product->set_product_url($product_url);
            }
            if (!empty($button_text)) {
                $product->set_button_text($button_text);
            }
        } else {
            // Even when switching to Simple, preserve URL in meta for future use
            if (!empty($product_url)) {
                update_post_meta($product_id, '_product_url', $product_url);
            }
            if (!empty($button_text)) {
                update_post_meta($product_id, '_button_text', $button_text);
            }
        }
        
        // Save product
        $save_result = $product->save();
        
        if (!$save_result) {
            return array(
                'success' => false,
                'message' => __('Failed to save product after type change.', 'brand-product-type-switcher'),
            );
        }
        
        return array(
            'success' => true,
            'message' => __('Product type switched successfully.', 'brand-product-type-switcher'),
            'product_name' => $product->get_name(),
        );
    }
    
    /**
     * Get products by brands
     */
    private function get_products_by_brands($brand_ids) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'fields' => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $brand_ids,
                    'operator' => 'IN',
                ),
            ),
        );
        
        $query = new WP_Query($args);
        return $query->posts;
    }
    
    /**
     * Get session ID
     */
    private function get_session_id($brand_ids, $product_type) {
        return md5(implode(',', $brand_ids) . $product_type . time());
    }
    
    /**
     * AJAX: Get progress
     */
    public function ajax_get_progress() {
        check_ajax_referer('bpt_s_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Insufficient permissions.', 'brand-product-type-switcher')));
        }
        
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => __('Session ID required.', 'brand-product-type-switcher')));
        }
        
        $session_data = get_option('bpt_s_session_' . $session_id);
        
        if (!$session_data) {
            wp_send_json_error(array('message' => __('Session not found.', 'brand-product-type-switcher')));
        }
        
        $progress = 0;
        if ($session_data['total'] > 0) {
            $progress = round(($session_data['processed'] / $session_data['total']) * 100);
        }
        
        wp_send_json_success(array(
            'progress' => $progress,
            'processed' => $session_data['processed'],
            'total' => $session_data['total'],
            'success' => $session_data['success'],
            'errors' => $session_data['errors'],
            'logs' => array_slice($session_data['logs'], -50), // Last 50 log entries
            'status' => isset($session_data['status']) ? $session_data['status'] : 'processing',
        ));
    }
}


// Initialize plugin
Brand_Product_Type_Switcher::get_instance();

