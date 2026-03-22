<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              conversios.io
 * @since             1.0.0
 * @package           Enhanced E-commerce for Woocommerce store
 *
 * @wordpress-plugin
 * Plugin Name:       Conversios.io - All-in-one Google Analytics, Pixels and Product Feed Manager for WooCommerce
 * Plugin URI:        https://www.conversios.io/
 * Description:       Track ecommerce events and conversions for GA4 and for the ad channels like Google Ads, Facebook, Tiktok, Snapchat and more. Automate end to end server side tracking. Create quality feeds for google shopping, tiktok, facebook and more. Leverage data driven decision making by enhanced ecommerce reporting and AI powered insights to increase sales.
 * Version:           7.2.15
 * Author:            Conversios
 * Author URI:        https://conversios.io
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       enhanced-e-commerce-for-woocommerce-store
 * Domain Path:       /languages
 * WC requires at least: 3.5.0
 * WC tested up to: 8.8.3
 */

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

// Check if ABSPATH is defined and not null
if (!defined('ABSPATH') || ABSPATH === null) {
    trigger_error('ABSPATH is not defined or is null', E_USER_ERROR);
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

function is_EeAioPro_active()
{
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    return is_plugin_active('enhanced-e-commerce-pro-for-woocommerce-store/enhanced-ecommerce-pro-google-analytics.php');
}

add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

function conv_onactive_redirect()
{
    if (get_transient('_conversios_activation_redirect')) {
        delete_transient('_conversios_activation_redirect');

        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        wp_safe_redirect(admin_url('admin.php?page=conversios'));
        exit;
    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-activator.php
 */

function activate_enhanced_ecommerce_google_analytics($plugin)
{
    if (is_EeAioPro_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_safe_redirect(esc_url(site_url() . "/wp-admin/plugins.php?convmsg=pro_already_installed"));
        exit;
    } else if (is_multisite() && is_network_admin()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_safe_redirect(esc_url(site_url() . "/wp-admin/network/plugins.php?convmsg=pro_already_installed"));
        exit;
    } else {
        require_once plugin_dir_path(__FILE__) . 'includes/class-enhanced-ecommerce-google-analytics-activator.php';
        Enhanced_Ecommerce_Google_Analytics_Activator::activate();
        set_transient('_conversios_activation_redirect', 1, 999);
    }
}

add_action('admin_init', 'conv_onactive_redirect', 999);


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-enhanced-ecommerce-google-analytics-deactivator.php
 */
function deactivate_enhanced_ecommerce_google_analytics()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-enhanced-ecommerce-google-analytics-deactivator.php';
    Enhanced_Ecommerce_Google_Analytics_Deactivator::deactivate();
    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        wp_clear_scheduled_hook('tvc_add_cron_interval_for_product_sync');
        as_unschedule_all_actions('ee_auto_product_sync_check');
        as_unschedule_all_actions('auto_feed_wise_product_sync_process_scheduler_ee');
        as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee');
    }
}
register_activation_hook(__FILE__, 'activate_enhanced_ecommerce_google_analytics');
register_deactivation_hook(__FILE__, 'deactivate_enhanced_ecommerce_google_analytics');


if (is_EeAioPro_active()) {
    return;
}


define('PLUGIN_TVC_VERSION', '7.2.15');
$fullName = plugin_basename(__FILE__);
$dir = str_replace('/enhanced-ecommerce-google-analytics.php', '', $fullName);

//APP ID
if (!defined('CONV_APP_ID')) {
    define('CONV_APP_ID', 1);
}
//Screen ID
if (!defined('CONV_SCREEN_ID')) {
    define('CONV_SCREEN_ID', 'conversios_page_');
}
//Top Menu
if (!defined('CONV_TOP_MENU')) {
    define('CONV_TOP_MENU', 'Conversios');
}
//Menu Slug
if (!defined('CONV_MENU_SLUG')) {
    define('CONV_MENU_SLUG', 'conversios');
}

if (!defined('ENHANCAD_PLUGIN_NAME')) {
    define('ENHANCAD_PLUGIN_NAME', $dir);
}
// Store the directory of the plugin
if (!defined('ENHANCAD_PLUGIN_DIR')) {
    define('ENHANCAD_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
// Store the url of the plugin
if (!defined('ENHANCAD_PLUGIN_URL')) {
    define('ENHANCAD_PLUGIN_URL', plugins_url() . '/' . ENHANCAD_PLUGIN_NAME);
}

if (!defined('TVC_API_CALL_URL')) {
    define('TVC_API_CALL_URL', 'https://connect.conversios.io/laravelapi/public/api/v1');
}
if (!defined('TVC_API_CALL_URL_TEMP')) {
    define('TVC_API_CALL_URL_TEMP', 'https://connect.conversios.io/laravelapi/public');
}

if (!defined('TVC_AUTH_CONNECT_URL')) {
    define('TVC_AUTH_CONNECT_URL', 'conversios.io');
}

if (!class_exists('TVC_Admin_Helper')) {
    include(ENHANCAD_PLUGIN_DIR . '/admin/class-tvc-admin-helper.php');
}

if (!defined('CONV_IS_WC')) {
    if (class_exists('WooCommerce') || is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        define('CONV_IS_WC', 1);
    } else {
        define('CONV_IS_WC', 0);
    }
}

if (!defined('CONV_LOG')) {
    define('CONV_LOG', ENHANCAD_PLUGIN_DIR . 'logs/');
}

add_action('upgrader_process_complete', 'tvc_upgrade_function', 10, 2);
function tvc_upgrade_function($upgrader_object, $options)
{
    $fullName = plugin_basename(__FILE__);
    if ($options['action'] == 'update' && $options['type'] == 'plugin' && is_array($options['plugins'])) {
        foreach ($options['plugins'] as $each_plugin) {
            if ($each_plugin == $fullName) {
                $TVC_Admin_Helper = new TVC_Admin_Helper();
                $caller = "tvc_upgrade_function";
                $TVC_Admin_Helper->update_app_status($caller);
            }
        }
    }
}
// On Plugin update
function my_plugin_update_db()
{
    $current_version = get_option('ee_conv_plugin_version');
    $new_version = '7.2.7'; // Update this whenever you change the DB schema

    if ($current_version !== $new_version) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ee_product_feed';

        // Check if table exists before proceeding
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) !== $table_name) {
            return;
        }

        // Column name => column definition
        $columns = [
            'ms_status' => "VARCHAR(200) DEFAULT NULL",
            'IncProductVar' => "VARCHAR(200) DEFAULT 1",
            'IncDefProductVar' => "VARCHAR(200) DEFAULT 0",
            'IncLowestPriceProductVar' => "VARCHAR(200) DEFAULT 0",
        ];

        foreach ($columns as $column_name => $column_definition) {
            $exists = $wpdb->get_results(
                $wpdb->prepare("SHOW COLUMNS FROM `$table_name` LIKE %s", $column_name)
            );

            if (empty($exists)) {
                $wpdb->query("ALTER TABLE `$table_name` ADD `$column_name` $column_definition");
            }
        }

        // Finally update the version
        update_option('ee_conv_plugin_version', $new_version);
    }
}
add_action('plugins_loaded', 'my_plugin_update_db');

//add_action('action_scheduler_init', 'conv_clear_ut_cron', 1);
add_action('admin_init', 'conv_clear_ut_cron', 1);
//add_action('init', 'conv_clear_ut_cron', 1);

function conv_clear_ut_cron() {
    if ( get_option('conv_ut_cleanup') === 'yes' ) {
        return;
    }

    // Prevent the old scheduler from re-creating jobs by removing the flag it checks
    delete_option('ee_ut');

    // 1) Try Action Scheduler API unschedule/delete (best-effort)
    if ( function_exists('as_unschedule_all_actions') ) {
        @as_unschedule_all_actions('ee_ut_cron');
    }

    // Helper to get IDs from as_get_scheduled_actions across AS versions
    $conv_get_as_ids = function($args) {
        if ( ! function_exists('as_get_scheduled_actions') ) {
            return array();
        }

        $args_local = $args;
        $args_local['return'] = 'ids';
        $ids = @as_get_scheduled_actions($args_local);

        if ( empty($ids) ) {
            // older AS versions sometimes accept a second param 'ids'
            $try = @as_get_scheduled_actions($args, 'ids');
            if ( ! empty($try) ) {
                $ids = $try;
            }
        }

        // If objects returned, convert to IDs
        if ( ! empty($ids) && is_array($ids) && is_object(reset($ids)) ) {
            $converted = array();
            foreach ($ids as $obj) {
                if ( is_object($obj) ) {
                    if ( method_exists($obj, 'get_id') ) {
                        $converted[] = (int) $obj->get_id();
                    } elseif ( isset($obj->action_id) ) {
                        $converted[] = (int) $obj->action_id;
                    } elseif ( isset($obj->ID) ) {
                        $converted[] = (int) $obj->ID;
                    }
                }
            }
            $ids = $converted;
        }

        return is_array($ids) ? array_map('intval', $ids) : array();
    };

    // If ActionScheduler store exists, cancel + delete via API (safe)
    if ( function_exists('as_get_scheduled_actions') && class_exists('ActionScheduler') ) {
        $store = ActionScheduler::store();
        $statuses = array('pending','complete','failed');

        foreach ($statuses as $status) {
            $offset = 0;
            do {
                $ids = $conv_get_as_ids(array(
                    'hook'     => 'ee_ut_cron',
                    'status'   => $status,
                    'per_page' => 100,
                    'offset'   => $offset,
                ));

                if ( empty($ids) ) {
                    break;
                }

                foreach ($ids as $aid) {
                    $aid = (int) $aid;
                    try { $store->cancel_action($aid); } catch (Exception $e) {}
                    try { $store->delete_action($aid); } catch (Exception $e) {}
                }

                $offset += count($ids);
            } while (true);
        }
    }

    // 2) DB-level fallback / hard delete (in safe chunks)
    global $wpdb;
    $conv_actions_table = $wpdb->prefix . 'actionscheduler_actions';
    $conv_logs_table    = $wpdb->prefix . 'actionscheduler_logs';
    $conv_claims_table  = $wpdb->prefix . 'actionscheduler_claims';

    $exists = $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $conv_actions_table) );
    if ( $exists === $conv_actions_table ) {
        $limit = 200;
        $offset = 0;
        do {
            $conv_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT action_id FROM {$conv_actions_table} WHERE hook = %s LIMIT %d, %d",
                'ee_ut_cron',
                $offset,
                $limit
            ) );

            if ( empty($conv_ids) ) {
                break;
            }

            $conv_ids_in = implode(',', array_map('intval', $conv_ids));

            if ( $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $conv_logs_table) ) === $conv_logs_table ) {
                $wpdb->query( "DELETE FROM {$conv_logs_table} WHERE action_id IN ({$conv_ids_in})" );
            }

            if ( $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $conv_claims_table) ) === $conv_claims_table ) {
                $wpdb->query( "DELETE FROM {$conv_claims_table} WHERE action_id IN ({$conv_ids_in})" );
            }

            // Delete actions in chunks
            $wpdb->query( "DELETE FROM {$conv_actions_table} WHERE action_id IN ({$conv_ids_in})" );

            $offset += $limit;
        } while ( true );
    }

    // 3) Final safety: remove historical rows that maybe reference hook in other places (rare)
    // (optional) remove any residual actions by hook text, using prepare
    if ( $exists === $conv_actions_table ) {
        $wpdb->query( $wpdb->prepare("DELETE FROM {$conv_actions_table} WHERE hook = %s", 'ee_ut_cron') );
    }

    // 4) Remove old option (defensive) and mark cleanup done
    update_option('conv_ut_cleanup', 'yes');
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-enhanced-ecommerce-google-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_enhanced_ecommerce_google_analytics()
{
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }
    $plugin = new Enhanced_Ecommerce_Google_Analytics();
    $plugin->run();
}
add_action('init', 'run_enhanced_ecommerce_google_analytics');
