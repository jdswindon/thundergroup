<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wp_filesystem;
TVC_Admin_Helper::get_filesystem();
 // Exit if accessed directly

if (!class_exists('Tatvic_Category_Wrapper')) {
    require_once plugin_dir_path(__FILE__) . '../../../includes/setup/tatvic-category-wrapper.php';
}
if (!class_exists('TVCProductSyncHelper')) {
    require_once plugin_dir_path(__FILE__) . '../../../includes/setup/class-tvc-product-sync-helper.php';
}
$TVC_Admin_Helper = new TVC_Admin_Helper();
$category_wrapper_obj = new Tatvic_Category_Wrapper();
$TVC_Admin_Helper->need_auto_update_db();
// $feed_datas = $TVC_Admin_Helper->ee_get_results('ee_product_feed');
$subpage = isset($_GET['subpage']) ? sanitize_text_field($_GET['subpage']) : '';
if ($subpage === 'gmc') {
    $feed_data = $TVC_Admin_Helper->ee_get_results('ee_product_feed', 1);
} elseif ($subpage === 'microsoft') {
    $feed_data = $TVC_Admin_Helper->ee_get_results('ee_product_feed', 4);
} elseif ($subpage === 'tiktok') {
    $feed_data = $TVC_Admin_Helper->ee_get_results('ee_product_feed', 3);
} elseif ($subpage === 'meta') {
    $feed_data = $TVC_Admin_Helper->ee_get_results('ee_product_feed', 2);
}
if(!empty($feed_data))
{
    $TVC_Admin_Helper->get_feed_status();
}
$count_feed = count($feed_data);
$subscriptionId = $TVC_Admin_Helper->get_subscriptionId();
$site_url = "admin.php?page=conversios-google-shopping-feed";
$site_url_pmax = "admin.php?page=conversios-pmax";
$customApiObj = new CustomApi();
$google_detail = unserialize(get_option("ee_api_data"));
$googleDetail = $google_detail['setting'];
if (isset($googleDetail->id)) {
    $conv_data['subscription_id'] = $googleDetail->id;
}
$conv_data = $TVC_Admin_Helper->get_store_data();
$conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$total_products = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish']))->found_posts;
$ee_options = $TVC_Admin_Helper->get_ee_options_settings();

$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = esc_html($ee_options['google_merchant_id']);
}

$tiktok_business_account = '';
$tiktok_email = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = esc_html($ee_options['tiktok_setting']['tiktok_business_id']);
}
if (isset($ee_options['tiktok_setting']['tiktok_mail']) === TRUE && $ee_options['tiktok_setting']['tiktok_mail'] !== '') {
    $tiktok_email = esc_html($ee_options['tiktok_setting']['tiktok_mail']);
}
$facebook_business_account = '';
if (isset($ee_options['facebook_setting']['fb_business_id']) === TRUE && $ee_options['facebook_setting']['fb_business_id'] !== '') {
    $facebook_business_account = esc_html($ee_options['facebook_setting']['fb_business_id']);
}

$facebook_catalog_id = '';
if (isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE && $ee_options['facebook_setting']['fb_catalog_id'] !== '') {
    $facebook_catalog_id = esc_html($ee_options['facebook_setting']['fb_catalog_id']);
}

$microsoft_merchant_center_id = '';
if (isset($ee_options['microsoft_merchant_center_id']) === TRUE && $ee_options['microsoft_merchant_center_id'] !== '') {
    $microsoft_merchant_center_id = esc_html($ee_options['microsoft_merchant_center_id']);
}
$microsoft_catalog_id = '';
if (isset($ee_options['ms_catalog_id']) === TRUE && $ee_options['ms_catalog_id'] !== '') {
    $microsoft_catalog_id = esc_html($ee_options['ms_catalog_id']);
}

$not_connected_any_gmc = false;
if (
    $google_merchant_center_id === ''
    && $tiktok_business_account === ''
    && $facebook_catalog_id === ''
    && $microsoft_catalog_id === ''
) {
    //wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=feed_list"); //Odd
    //exit;
    $not_connected_any_gmc = true;
}


$google_ads_id = '';
$currency_symbol = '';
if (isset($ee_options['google_ads_id']) === TRUE && $ee_options['google_ads_id'] !== '') {
    $google_ads_id = esc_html($ee_options['google_ads_id']);
    //$PMax_Helper = new Conversios_PMax_Helper();
}

$googleConnect_url = '';
//$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR."includes/setup/json/countries.json");

$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

$contData = json_decode($getCountris);
$data = unserialize(get_option('ee_options'));
$g_mail = get_option('ee_customer_gmail');
$ms_mail = get_option('ee_customer_msmail');
$tvc_data['g_mail'] = "";
if ($g_mail) {
    $tvc_data['g_mail'] = sanitize_email($g_mail);
}
$fb_mail = isset($ee_options['facebook_setting']['fb_mail']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_mail']) : '';
$fb_business_id = isset($ee_options['facebook_setting']['fb_business_id']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_business_id']) : '';
$fb_catalog_id = isset($ee_options['facebook_setting']['fb_catalog_id']) === TRUE ? esc_html($ee_options['facebook_setting']['fb_catalog_id']) : '';
$gmcAttributes = $TVC_Admin_Helper->get_gmcAttributes();
// $ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
// $tempAddAttr = $ee_mapped_attrs;
$tvc_ProductSyncHelper = new TVCProductSyncHelper();
$wooCommerceAttributes = array_map("unserialize", array_unique(array_map("serialize", $tvc_ProductSyncHelper->wooCommerceAttributes())));
$category = $TVC_Admin_Helper->get_tvc_product_cat_list_with_name();
$attr = '';
$condition = '';
$value = '';
$edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
$filterDataForJS = [];
if ($edit_id > 0) {
    $where = '`id` = ' . esc_sql($edit_id);
    $fields = ['id', 'feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'product_id_prefix', 'target_country', 'tiktok_catalog_id', 'product_sync_batch_size', 'is_mapping_update', 'last_sync_date', 'filters'];
    $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $fields);
    if (empty($result) || empty($result[0])) {
        die('Bad Request!!!!');
    }
    $ee_mapped_attrs = json_decode(stripslashes($result[0]['attributes']), true);
    $ee_prod_mapped_cats = json_decode(stripslashes($result[0]['categories']), true);
    $tempAddAttr = $ee_mapped_attrs;
    $filters = isset($result[0]['filters']) && $result[0]['filters'] !== '' ? json_decode(stripslashes($result[0]['filters'])) : '';
    if ($filters !== '') {
        $filterAttributes = ['product_cat' => 'Category', 'ID' => 'Product Id', '_stock_status' => 'Stock Status', 'main_image' => 'Main Image'];
        $count = 0;

        foreach ($filters as $val) {
            if ($val->attr == '_sku' || $val->attr == '_regular_price' || $val->attr == '_sale_price' || $val->attr == 'post_content' || $val->attr == 'post_excerpt' || $val->attr == 'post_title') {
                continue;
            }

            $attr .= $attr === '' ? $val->attr : ',' . $val->attr;
            $condition .= $condition === '' ? $val->condition : ',' . $val->condition;
            $value .= $value === '' ? $val->value : ',' . $val->value;

            $termitem = '';
            $eachValue = $val->value;
            $displayValue = $val->value; // For JavaScript

            if ($val->attr === 'product_cat') {
                $termitem = get_term_by('id', $val->value, 'product_cat');
                if ($termitem) {
                    $eachValue = $termitem->name;
                    $displayValue = $termitem->name; // Use name for display
                }
            }

            // Prepare data for JavaScript with proper display values
            $filterDataForJS[] = [
                'attr' => $val->attr,
                'condition' => $val->condition,
                'value' => $val->value, // Keep original value for form submission
                'display_value' => $displayValue // Add display value
            ];
        }
    }
} else {
    $ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
    $tempAddAttr = $ee_mapped_attrs;
    $ee_prod_mapped_cats = unserialize(get_option('ee_prod_mapped_cats'));
}
?>
<style>
    .errorInput {
        border: 1.3px solid #ef1717 !important;
        padding: 0px;
        border-radius: 6px;
    }

    .dataTables_length,
    .dataTables_info {
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .dataTables-search,
    .dataTables-paging {
        float: right;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .paginate_button {
        position: relative;
        /*display: block; */
        color: #0d6efd;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        font-size: 12px;
        padding: 0.375rem 0.75rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

    .dataTables_paginate {
        margin-top: 10px !important;
    }

    button:disabled {
        color: #131212;
        border-color: #cccccc;
    }

    button:disabled:hover {
        color: #131212;
        border-color: #cccccc;
    }

    .get-started-card {
        max-width: 600px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #e1e1e1;
        border-radius: 12px;
        padding: 30px 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-family: Arial, sans-serif;
        text-align: center;
    }

    .get-started-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .get-started-icon {
        font-size: 30px;
    }

    .get-started-header h2 {
        font-size: 24px;
        color: #222;
        margin: 0;
    }

    .get-started-message {
        font-size: 16px;
        color: #444;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .get-started-actions {
        margin-bottom: 20px;
    }

    .get-started-btn {
        background-color: #0073aa;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 15px;
        border-radius: 6px;
        cursor: pointer;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
    }

    .get-started-btn:hover {
        background-color: #005f8d;
    }

    .get-started-link {
        display: inline-block;
        font-size: 14px;
        color: #0073aa;
        text-decoration: none;
        margin-top: 5px;
    }

    .get-started-link:hover {
        text-decoration: underline;
    }

    .create-btn-wrapper {
        margin-top: 20px;
    }

    .get-started-secondary-btn {
        background-color: #007bff;
        /* blue */
        color: #fff;
        /* white text */
        font-weight: bolder;
        border: 1px solid #007bff;
        padding: 10px 20px;
        font-size: 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    /* Disabled state */
    .get-started-secondary-btn:disabled {
        background-color: #eee;
        color: #aaa;
        border-color: #ddd;
        cursor: not-allowed;
    }


    .nav-tab-active {
        background-color: #f1f1f1 !important;
        border-bottom: 6px solid #f1f1f1 !important;
        /* matches background to hide line */
    }

    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .custom-modal-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
        text-align: center;
    }

    .custom-modal-actions {
        margin-top: 20px;
    }

    .custom-modal-actions button {
        margin: 0 10px;
        padding: 8px 16px;
        cursor: pointer;
    }

    .convcreatefeedtable tr {
        background: #f8f8f8;
        border: 1px solid #ddd;
        padding: 0.35em;
    }

    .convcreatefeedtable th,
    .convcreatefeedtable td {
        padding: 0.55rem 0.8rem;
        text-align: left;
    }

    .convcreatefeedtable th {
        font-size: 14px;
        letter-spacing: 0.1em;
    }

    .convcreatefeedtable {
        padding-right: 1%;
        width: 74%;
    }

    tr {
        font-size: 14px;
        color: #5f6368;
        text-align: start;
    }

    /* .form-check .form-check-input{
        height: 1.5em !important;
        width: 3em;
    } */
    .nav-tab.disabled-tab {
        pointer-events: none;
        /* color: #ccc; */
        cursor: not-allowed;
    }

    .back-link {
        color: #0d6efd;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .back-link:hover {
        color: #0a58ca;
        /* darker shade on hover */
        /* show underline on hover */
    }
</style>
<!-- Tabs for all channels  -->
<div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2 mt-1 height-2">
    <div class="indeterminate"></div>
</div>
<div class="channel-setup-parent">
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a href="<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=gmc')); ?>" class="nav-tab <?php echo (!isset($_GET['subpage']) || $_GET['subpage'] === 'gmc') ? 'nav-tab-active' : ''; ?>">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" alt="GMC" style="width: 30px; vertical-align: middle;">
                Google Merchant Center
            </a>

            <a href="<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=microsoft')); ?>" class="nav-tab <?php echo (isset($_GET['subpage']) && $_GET['subpage'] === 'microsoft') ? 'nav-tab-active' : ''; ?>">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms-logo.png'); ?>" alt="Microsoft" style="width: 30px; vertical-align: middle; margin-right: 5px;">
                Microsoft Merchant Center
            </a>

            <a href="<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=tiktok')); ?>" class="nav-tab <?php echo (isset($_GET['subpage']) && $_GET['subpage'] === 'tiktok') ? 'nav-tab-active' : ''; ?>">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" alt="TikTok" style="width: 28px; vertical-align: middle; margin-right: 5px;">
                TikTok Business Center
            </a>

            <a href="<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=meta')); ?>" class="nav-tab <?php echo (isset($_GET['subpage']) && $_GET['subpage'] === 'meta') ? 'nav-tab-active' : ''; ?>">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_meta_logo.png'); ?>" alt="Meta" style="width: 30px; vertical-align: middle; margin-right: 5px;">
                Meta Business Center
            </a>
        </h2>
    </div>

    <!-- Tabs for all channels end-->
    <!-- Details card for All channels -->
    <?php if (isset($_GET['subpage']) && $_GET['subpage'] == 'gmc') { ?>
        <?php if ($google_merchant_center_id != "" && $g_mail != "") { ?>
            <div class="gmcdetails" style="padding: 16px 11px;background-color: #f0f0f1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Successfully logged in with:</strong>
                        <span style="margin-left: 6px;"><?php echo !empty($g_mail) ? esc_attr($g_mail) : '-'; ?></span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Merchant ID:</strong>
                        <?php echo !empty($google_merchant_center_id) ? esc_attr($google_merchant_center_id) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Status:</strong>
                        <span style="margin-left: 6px; color: <?php echo !empty($google_merchant_center_id) ? 'green' : 'red'; ?>;">
                            <?php echo !empty($google_merchant_center_id) ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div style="text-align: right; margin-bottom: 10px; margin-right: 30px;">
                        <button id="opengmcsettings" style="padding: 4px 7px; background-color: #1967D2; border: none; color: white; border-radius: 4px; cursor: pointer;">
                            Edit Details
                        </button>
                    </div>
                </div>
            </div>
            <div class="create-feed-section d-none" style="text-align: center; background-color: #f0f0f1;">
                <div>
                    <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_feed_not_created.png'); ?>" alt="Create Feed" style="max-width: 200px; margin-bottom: 20px; margin-top: 40px;" />
                    <p class="adt-tw-text-center adt-tw-text-gray-500 adt-tw-text-base adt-tw-font-medium adt-tw-italic" style="font-style: italic; font-size: 16px; color: #6b7280;">
                        Oops! No feeds found yet — start by creating one.
                    </p>
                    <div class="get-started-card">
                        <div class="get-started-header">
                            <div class="get-started-icon">🚀</div>
                            <h2>Let's Get Started!</h2>
                        </div>
                        <p class="get-started-message">
                            Ready to create your first product feed? Just click the button below to begin. It's quick, easy, and powerful.
                        </p>
                        <div class="create-btn-wrapper">
                            <button class="get-started-secondary-btn"
                                name="create_new_feed"
                                id="create_new_feed"
                                <?php echo $not_connected_any_gmc ? 'disabled' : ''; ?>>
                                🛠️ Create New Feed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="gmcnotconfigured" style="width: 95.5%; margin: 0 auto; padding: 30px 20px; text-align: center;">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_not_configured.png'); ?>" alt="Setup Required" style="margin-bottom: 20px; height: 200px;" />
                <p style="font-size: 16px; color: #555;">Google Merchant Center is not configured yet.</p>
                <button id="opengmcsettings" style="padding: 10px 20px; background-color: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer;">
                    Configure Now
                </button>
            </div>
        <?php } ?>
        <?php require_once(ENHANCAD_PLUGIN_DIR . '/admin/partials/singlepixelsettings/gmcsettings.php'); ?>
    <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] == 'microsoft') { ?>
        <?php if ($microsoft_catalog_id != "" && $ms_mail != "") { ?>
            <div class="mmcdetails" style="padding: 16px 11px;background-color: #f0f0f1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Successfully logged in with:</strong>
                        <span style="margin-left: 6px;"><?php echo !empty($ms_mail) ? esc_attr($ms_mail) : '-'; ?></span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Catalog ID:</strong>
                        <?php echo !empty($microsoft_catalog_id) ? esc_attr($microsoft_catalog_id) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Status:</strong>
                        <span style="margin-left: 6px; color: <?php echo !empty($microsoft_catalog_id) ? 'green' : 'red'; ?>;">
                            <?php echo !empty($microsoft_catalog_id) ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div style="text-align: right; margin-bottom: 10px; margin-right: 30px;">
                        <button id="openmmcsettings" style="padding: 4px 7px; background-color: #1967D2; border: none; color: white; border-radius: 4px; cursor: pointer;">
                            Edit Details
                        </button>
                    </div>
                </div>
            </div>
            <div class="create-feed-section d-none" style="text-align: center; background-color: #f0f0f1;">
                <div>
                    <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_feed_not_created.png'); ?>" alt="Create Feed" style="max-width: 200px; margin-bottom: 20px; margin-top: 40px;" />
                    <p class="adt-tw-text-center adt-tw-text-gray-500 adt-tw-text-base adt-tw-font-medium adt-tw-italic" style="font-style: italic; font-size: 16px; color: #6b7280;">
                        Oops! No feeds found yet — start by creating one.
                    </p>
                    <div class="get-started-card">
                        <div class="get-started-header">
                            <div class="get-started-icon">🚀</div>
                            <h2>Let's Get Started!</h2>
                        </div>
                        <p class="get-started-message">
                            Ready to create your first product feed? Just click the button below to begin. It's quick, easy, and powerful.
                        </p>
                        <div class="create-btn-wrapper">
                            <button class="get-started-secondary-btn"
                                name="create_new_feed"
                                id="create_new_feed"
                                <?php echo $not_connected_any_gmc ? 'disabled' : ''; ?>>
                                🛠️ Create New Feed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="mmcnotconfigured" style="width: 95.5%; margin: 0 auto; padding: 30px 20px; text-align: center;">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_not_configured.png'); ?>" alt="Setup Required" style="margin-bottom: 20px; height: 200px;" />
                <p style="font-size: 16px; color: #555;">Microsoft Merchant Center is not configured yet.</p>
                <button id="openmmcsettings" style="padding: 10px 20px; background-color: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer;">
                    Configure Now
                </button>
            </div>
        <?php } ?>
        <?php require_once(ENHANCAD_PLUGIN_DIR . '/admin/partials/singlepixelsettings/mmcsettings.php'); ?>
    <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] == 'tiktok') { ?>
        <?php if ($tiktok_email != "" && $tiktok_business_account != "") { ?>
            <div class="tiktokdetails" style="padding: 16px 11px;background-color: #f0f0f1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Successfully logged in with:</strong>
                        <span style="margin-left: 6px;"><?php echo !empty($tiktok_email) ? esc_attr($tiktok_email) : '-'; ?></span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Business ID:</strong>
                        <?php echo !empty($tiktok_business_account) ? esc_attr($tiktok_business_account) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Status:</strong>
                        <span style="margin-left: 6px; color: <?php echo !empty($tiktok_business_account) ? 'green' : 'red'; ?>;">
                            <?php echo !empty($tiktok_business_account) ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div style="text-align: right; margin-bottom: 10px; margin-right: 30px;">
                        <button id="opentiktoksettings" style="padding: 4px 7px; background-color: #1967D2; border: none; color: white; border-radius: 4px; cursor: pointer;">
                            Edit Details
                        </button>
                    </div>
                </div>
            </div>
            <div class="create-feed-section d-none" style="text-align: center; background-color: #f0f0f1;">
                <div>
                    <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_feed_not_created.png'); ?>" alt="Create Feed" style="max-width: 200px; margin-bottom: 20px; margin-top: 40px;" />
                    <p class="adt-tw-text-center adt-tw-text-gray-500 adt-tw-text-base adt-tw-font-medium adt-tw-italic" style="font-style: italic; font-size: 16px; color: #6b7280;">
                        Oops! No feeds found yet — start by creating one.
                    </p>
                    <div class="get-started-card">
                        <div class="get-started-header">
                            <div class="get-started-icon">🚀</div>
                            <h2>Let's Get Started!</h2>
                        </div>
                        <p class="get-started-message">
                            Ready to create your first product feed? Just click the button below to begin. It's quick, easy, and powerful.
                        </p>
                        <div class="create-btn-wrapper">
                            <button class="get-started-secondary-btn"
                                name="create_new_feed"
                                id="create_new_feed"
                                <?php echo $not_connected_any_gmc ? 'disabled' : ''; ?>>
                                🛠️ Create New Feed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="tiktoknotconfigured" style="width: 95.5%; margin: 0 auto; padding: 30px 20px; text-align: center;">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_not_configured.png'); ?>" alt="Setup Required" style="margin-bottom: 20px; height: 200px;" />
                <p style="font-size: 16px; color: #555;">Tiktok Business Catalog Is Not Configured Yet.</p>
                <button id="opentiktoksettings" style="padding: 10px 20px; background-color: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer;">
                    Configure Now
                </button>
            </div>
        <?php } ?>
        <?php require_once(ENHANCAD_PLUGIN_DIR . '/admin/partials/singlepixelsettings/tiktokBusinessSettings.php'); ?>
    <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] == 'meta') { ?>
        <?php if ($fb_mail != "" && $fb_catalog_id != "") { ?>
            <div class="metadetails" style="padding: 16px 11px;background-color: #f0f0f1;">
                <div style="display: flex; flex-wrap: wrap; align-items: center;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Successfully logged in with:</strong>
                        <span style="margin-left: 6px;"><?php echo !empty($fb_mail) ? esc_attr($fb_mail) : '-'; ?></span>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Business ID:</strong>
                        <?php echo !empty($fb_catalog_id) ? esc_attr($fb_catalog_id) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Status:</strong>
                        <span style="margin-left: 6px; color: <?php echo !empty($fb_catalog_id) ? 'green' : 'red'; ?>;">
                            <?php echo !empty($fb_catalog_id) ? 'Connected' : 'Disconnected'; ?>
                        </span>
                    </div>
                    <div style="text-align: right; margin-bottom: 10px; margin-right: 30px;">
                        <button id="openmetasettings" style="padding: 4px 7px; background-color: #1967D2; border: none; color: white; border-radius: 4px; cursor: pointer;">
                            Edit Details
                        </button>
                    </div>
                </div>
            </div>
            <div class="create-feed-section d-none" style="text-align: center; background-color: #f0f0f1;">
                <div>
                    <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_feed_not_created.png'); ?>" alt="Create Feed" style="max-width: 200px; margin-bottom: 20px; margin-top: 40px;" />
                    <p class="adt-tw-text-center adt-tw-text-gray-500 adt-tw-text-base adt-tw-font-medium adt-tw-italic" style="font-style: italic; font-size: 16px; color: #6b7280;">
                        Oops! No feeds found yet — start by creating one.
                    </p>
                    <div class="get-started-card">
                        <div class="get-started-header">
                            <div class="get-started-icon">🚀</div>
                            <h2>Let's Get Started!</h2>
                        </div>
                        <p class="get-started-message">
                            Ready to create your first product feed? Just click the button below to begin. It's quick, easy, and powerful.
                        </p>
                        <div class="create-btn-wrapper">
                            <button class="get-started-secondary-btn"
                                name="create_new_feed"
                                id="create_new_feed"
                                <?php echo $not_connected_any_gmc ? 'disabled' : ''; ?>>
                                🛠️ Create New Feed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="metanotconfigured" style="width: 95.5%; margin: 0 auto; padding: 30px 20px; text-align: center;">
                <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/conv_not_configured.png'); ?>" alt="Setup Required" style="margin-bottom: 20px; height: 200px;" />
                <p style="font-size: 16px; color: #555;">Meta Business Catalog Is Not Configured Yet.</p>
                <button id="openmetasettings" style="padding: 10px 20px; background-color: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer;">
                    Configure Now
                </button>
            </div>
        <?php } ?>
        <?php require_once(ENHANCAD_PLUGIN_DIR . '/admin/partials/singlepixelsettings/metasettings.php'); ?>
    <?php } ?>
</div>
<!-- Details card for All channels end-->
<div class="container-fluid px-50 pb-0">
    <div class="d-flex pb-0">
        <div class="m-0 p-0">
            <!-- <div class="conv-heading-box">
                <h3 class="">
                    <?php esc_html_e("Feed Management", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span class="fw-400 fs-14 text-secondary">
                    <?php
                    printf(
                        /* translators: %s: Total number of product */
                        esc_html__('View and manage all your product feeds in one place. Easily track feed status, sync schedules, and performance across channels.', "enhanced-e-commerce-for-woocommerce-store"),
                        esc_html(number_format_i18n($total_products))
                    );
                    ?>
                </span>
            </div> -->
        </div>
    </div>
</div>
<div class="container-fluid p-3 pb-2 channel-setup-parent">
    <nav class="navbar navbar-light bg-white shadow-sm d-none" style="opacity:0;">
        <div class="col-12 col-md-12 col-sm-12">
            <div class="row">
                <div class="col-8 col-md-8 col-sm-8 ps-3">
                    <input type="search" class="form-control border from-control-width empty" placeholder="Search..." aria-label="Search" name="search_feed" id="search_feed" aria-controls="feed_list_table">
                </div>
                <div class="col-4 d-flex justify-content-end">
                    <div id="create_new_feed_div" class="d-flex align-items-center">
                        <button class="btn btn-soft-primary fs-14 me-2" name="create_new_feed" id="create_new_feed" <?php echo $not_connected_any_gmc ? 'disabled' : '' ?>>Create New Feed</button>
                        <?php if ($not_connected_any_gmc) : ?>
                            <span class="material-symbols-outlined fs-6 me-1" data-bs-toggle="tooltip" data-bs-placement="right" title="For create new feed, GMC/FB/Tiktok any one need to setup first">
                                info
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <input type="hidden" id="feedCount" name="feedCount" value="<?php echo !empty($feed_data) ? count($feed_data) : 0; ?>">
    <div class="table-responsive shadow-sm d-none feedlisttable" style="border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <?php // echo '<pre>'; print_r($feed_data); echo '</pre>'; // wow 
        ?>
        <table class="table" id="feed_list_table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" class="text-start" style="width:13%">
                        <?php esc_html_e("FEED NAME", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("COUNTRY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:15%">
                        <?php esc_html_e("TOTAL PRODUCTS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("AUTO SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:10%">
                        <?php esc_html_e("CREATED", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:12%">
                        <?php esc_html_e("LAST SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:12%">
                        <?php esc_html_e("NEXT SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:5%">
                        <?php esc_html_e("STATUS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-center" style="width:3%">
                        <?php esc_html_e("MORE", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body" class="table-body">
                <?php

                // echo '<pre>'; print_r($feed_data); echo '</pre>'; wow


                $feedIdArr = [];
                if (empty($feed_data) === FALSE) {
                    foreach ($feed_data as $value) {
                        $channel_count = count(explode(',', $value->channel_ids));
                        $channel_id = explode(',', $value->channel_ids);
                        if ($value->status == 'Synced') {
                            array_push($feedIdArr, $value->id);
                        }

                ?>
                        <tr class="height" style="<?php echo $value->is_delete === '1' ? 'color: #708581; opacity: 0.5;' : ''; ?>">
                            <td class="align-middle text-start">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span style="cursor: no-drop;">
                                        <?php echo esc_html($value->feed_name); ?>
                                    </span>
                                <?php } else { ?>
                                    <span>
                                        <a title="Go to feed wise product list" href="<?php echo esc_url($site_url . '&tab=product_list&id=' . $value->id . '&from=' . $subpage); ?>"><?php echo esc_html($value->feed_name); ?></a>
                                    </span>
                                <?php } ?>

                            </td>
                            <td class="align-middle text-center">
                                <?php
                                foreach ($contData as $key => $country) {
                                    if ($value->target_country === $country->code) { ?>
                                        <?php echo esc_html($country->name); ?>
                                <?php }
                                }
                                ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo esc_html(number_format_i18n($value->total_product ? $value->total_product : 0)); ?>
                            </td>
                            <td class="align-middle text-center">
                                <span class="dot <?php echo $value->auto_schedule === '1' ? 'dot-green' : 'dot-red'; ?>"></span>
                                <span>
                                    <?php echo $value->auto_schedule === '1' ? 'Yes' : 'No'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->auto_sync_interval !== 0 && $value->auto_schedule === '1' ? 'Every ' . esc_html($value->auto_sync_interval) . ' Days' : ' '; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort='" <?php echo esc_html(strtotime($value->created_date)) ?> "'>
                                <span>
                                    <?php echo esc_html(date_format(date_create($value->created_date), "d M Y")); ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo esc_html(date_format(date_create($value->created_date), "H:i a")); ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort='" <?php echo esc_html(strtotime($value->last_sync_date ?? '0000-00-00 00:00:00')) ?> "'>
                                <span>
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center" data-sort="<?php echo isset($value->next_schedule_date) && $value->next_schedule_date !== '0000-00-00 00:00:00' ? esc_html(strtotime($value->next_schedule_date)) : ''; ?>">
                                <span>
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span class="badgebox rounded-pill  fs-10 deleted">
                                        Deleted
                                    </span>
                                    <?php } else {
                                    $draft = 0;
                                    $inprogress = 0;
                                    $synced = 0;
                                    $failed = 0;
                                    switch ($value->status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->tiktok_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->fb_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    switch ($value->ms_status) {
                                        case 'Draft':
                                            $draft++;
                                            break;

                                        case 'In Progress':
                                            $inprogress++;
                                            break;

                                        case 'Synced':
                                            $synced++;
                                            break;

                                        case 'Failed':
                                            $failed++;
                                            break;
                                    }

                                    if ($draft !== 0) { ?>
                                        <div class="badgebox draft" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Draft'); ?>
                                        </div>
                                        <input type="hidden" class="draftGmcImg" value="<?php echo $value->status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftTiktokImg" value="<?php echo $value->tiktok_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftfbImg" value="<?php echo $value->fb_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftmsImg" value="<?php echo $value->ms_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($inprogress !== 0) { ?>
                                        <div class="badgebox inprogress" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('In Progress'); ?>
                                        </div>
                                        <input type="hidden" class="inprogressGmcImg" value="<?php echo $value->status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressTiktokImg" value="<?php echo $value->tiktok_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressfbImg" value="<?php echo $value->fb_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressmsImg" value="<?php echo $value->ms_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($synced !== 0) { ?>
                                        <div class="badgebox xyz synced" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Synced'); ?>
                                        </div>
                                        <input type="hidden" class="syncedGmcImg" value="<?php echo $value->status == 'Synced' ? "<img class='synced-status xyz-s' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedTiktokImg" value="<?php echo $value->tiktok_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedfbImg" value="<?php echo $value->fb_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedmsImg" value="<?php echo $value->ms_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                    <?php }
                                    if ($failed !== 0) { ?>
                                        <div class="badgebox failed" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Failed'); ?>
                                        </div>
                                        <input type="hidden" class="failedGmcImg" value="<?php echo $value->status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedTiktokImg" value="<?php echo $value->tiktok_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedfbImg" value="<?php echo $value->fb_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/fb_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedmsImg" value="<?php echo $value->ms_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms_channel_logo.svg") . "' />" : '' ?>">
                                <?php }
                                } //end if 
                                ?>
                            </td>
                            <td class="align-middle">
                                <div class="dropdown position-static">
                                    <?php if ($value->is_delete === '1') { ?>
                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="pointer-events: none;">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                    <?php } else { ?>
                                        <button class="btn p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark bg-white">
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12" onclick="editFeed(<?php echo esc_html($value->id); ?>)">Edit</a>
                                            </li>
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12 " onclick="duplicateFeed(<?php echo esc_html($value->id); ?>)">Duplicate</a>
                                            </li>
                                            <li class="mb-0 pointer">
                                                <a class="dropdown-item text-secondary fs-12"
                                                    onclick="deleteFeed(<?php echo esc_js($value->id); ?>, <?php echo esc_js($channel_count); ?>)">
                                                    Delete
                                                </a>
                                            </li>
                                        </ul>
                                    <?php } //end if
                                    ?>
                                </div>
                            </td>
                        </tr>
                <?php } //end foreach
                } //end if
                $feedIdString = implode(",", $feedIdArr);
                ?>
            </tbody>
        </table>
        
    </div>
    <small class="fw-400 text-secondary d-none totalproductscount">
        <i><?php
            printf(
                /* translators: %s: Total number of product */
                esc_html__('You have total %s products in your WooCommerce store', "enhanced-e-commerce-for-woocommerce-store"),
                esc_html(number_format_i18n($total_products))
            );
            ?></i>
    </small>
</div>
<!-- Modal -->
<div class="wrap feedFormarea d-none">
    <div class="mb-1">
        <a href="#" class="resetbtn back-link d-inline-flex align-items-center">
            ←<span>Back to feed list</span>
        </a>
    </div>
    <h2 class="nav-tab-wrapper">
        <a href="#pills-enter-feed-details" class="nav-tab nav-tab-active disabled-tab">
            Feed Details
        </a>
        <a href="#pills-map-product-attribute" class="nav-tab disabled-tab">
            Attribute Mapping
        </a>
        <a href="#pills-map-product-category" class="nav-tab disabled-tab">
            Category Mapping
        </a>
        <a href="#pills-map-filters" class="nav-tab disabled-tab">
            Filters
        </a>
    </h2>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pills-enter-feed-details">
            <div class="mt-4 feedFormarea d-none mx-3">
                <form id="feedForm" class="feed-table-form">
                    <table class="table table-bordered table-sm convcreatefeedtable">
                        <tbody>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Feed Name", "enhanced-e-commerce-for-woocommerce-store"); ?><span class="text-danger">*</span></th>
                                <td class="text-start">
                                    <input type="text" class="form-control" name="feedName" id="feedName" placeholder="e.g. New Summer Collection">
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?><span class="text-danger">*</span></th>
                                <td class="text-start">
                                    <select class="select2 form-select form-select-sm mb-3" aria-label="form-select-sm example" style="width: 100%" name="target_country" id="target_country">
                                        <option value="">Select Country</option>
                                        <?php
                                        foreach ($contData as $key => $value) {
                                        ?>
                                            <option value="<?php echo esc_attr($value->code) ?>">
                                                <?php echo esc_html($value->name) ?></option>"
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <div class="tiktok_catalog_message text-muted" style="display:none; margin-top: 5px;">
                                        <?php echo esc_html("You do not have a catalog associated with the selected target country. Don't worry, we will create a new catalog for you."); ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color">
                                    <?php esc_html_e("Auto Sync", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </th>
                                <td class="text-start">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" style="height: 1.5em !important; width: 3em;" type="checkbox" name="autoSync" id="autoSync" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Auto Sync Interval", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                <td class="text-start">
                                    <input type="text" class="form-control-sm" readonly name="autoSyncIntvl" id="autoSyncIntvl" size="3" value="25">
                                    <span class="ms-2"><?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                    <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=innersetting_pfm&utm_campaign=feedpopup&plugin_name=aio" class="ms-2">
                                        <b>Upgrade To Pro</b>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Include Product Variations", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                <td class="text-start">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" style="height: 1.5em !important; width: 3em;" type="checkbox" name="IncProductVar" id="IncProductVar" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Only Include Default Product Variations", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                <td class="text-start">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" style="height: 1.5em !important; width: 3em;" type="checkbox" name="IncDefProductVar" id="IncDefProductVar">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-start align-middle font-weight-400 text-color"><?php esc_html_e("Only Include Lowest Price Product Variation", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                <td class="text-start">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" style="height: 1.5em !important; width: 3em;" type="checkbox" name="IncLowestPriceProductVar" id="IncLowestPriceProductVar">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button type="reset" class="resetbtn btn btn-secondary btn-sm" style="margin-right: 10px;"><?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                    <button type="button" class="btn btn-primary btn-sm" disabled id="goToAttributes"><?php esc_html_e("Next", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                </td>
                                <td>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-3">
                        <input type="hidden" id="edit" name="edit">
                        <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" name="url" id="url">
                        <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                        <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                        <?php if ($subpage === 'microsoft' && !empty($microsoft_merchant_center_id)) : ?>
                            <input type="hidden" name="mmc_id" value="<?php echo esc_attr($microsoft_merchant_center_id); ?>">
                        <?php elseif ($subpage === 'gmc' && !empty($google_merchant_center_id)) : ?>
                            <input type="hidden" name="gmc_id" value="<?php echo esc_attr($google_merchant_center_id); ?>">
                        <?php elseif ($subpage === 'tiktok' && !empty($tiktok_business_account)) : ?>
                            <input type="hidden" name="tiktok_id" id="tiktok_id" value="<?php echo esc_attr($tiktok_business_account); ?>">
                        <?php elseif ($subpage === 'meta' && !empty($facebook_business_account)) : ?>
                            <input type="hidden" name="fb_id" value="<?php echo esc_attr($ee_options['facebook_setting']['fb_catalog_id'] ?? ''); ?>">
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-map-product-attribute" role="tabpanel" aria-labelledby="pills-map-product-attribute-tab" style="width: 70%;">
            <div class="conv-light-grey-bg rounded-top" style="margin-top: 10px;">
                <div style="width:99%;">
                    <div class="row" style="background: #e4e4e4;padding-bottom: 10px;">
                        <div class="col-6 p-2 ps-4">
                            <?php if (isset($_GET['subpage']) && $_GET['subpage'] === 'gmc') { ?>
                                <span class="ps-0 fs-14 fw-normal text-grey">
                                    <img
                                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" />
                                    <?php esc_html_e("Google Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </span>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'microsoft') { ?>
                                <span class="ps-0 fs-14 fw-normal text-grey">
                                    <img
                                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms-logo.png'); ?>" />
                                    <?php esc_html_e("Microsoft Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </span>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'tiktok') { ?>
                                <span class="ps-0 fs-14 fw-normal text-grey">
                                    <img
                                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" />
                                    <?php esc_html_e("Tiktok Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </span>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'meta') { ?>
                                <span class="ps-0 fs-14 fw-normal text-grey">
                                    <img
                                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_meta_logo.png'); ?>" />
                                    <?php esc_html_e("Meta Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </span>
                            <?php }  ?>
                        </div>
                        <div class="col-6 p-2">
                            <span class="fs-14 fw-normal text-grey">
                                <img
                                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                                <?php esc_html_e("WooCommerce Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-2 attributeDiv rounded-bottom border-end border-bottom border-start"
                style="position: relative;">
                <!-- <div class="mb-2 attributeDiv rounded-bottom border-end border-bottom border-start" style="overflow-y: scroll; overflow-x: hidden; max-height:450px; position: relative"> -->
                <form id="attribute_mapping" class="row">
                    <?php foreach ($gmcAttributes as $key => $attribute) {
                        if (is_array($tempAddAttr) && !empty($tempAddAttr)) {
                            unset($tempAddAttr[$attribute["field"]]);
                        }

                        $sel_val = ""; ?>
                        <div class="row mt-1 attributehoverEffect">
                            <div class="col-6 P-2 PS-4 d-flex align-items-center">
                                <span class="ps-3 font-weight-400 text-color" style="font-size: 17px;">
                                    <?php echo esc_attr($attribute["field"]) . " " . (isset($attribute["required"]) && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : ""); ?>
                                    <span class="material-symbols-outlined fs-6 pointer" data-bs-toggle="tooltip"
                                        data-bs-placement="right"
                                        title="<?php echo (isset($attribute['desc']) ? esc_attr($attribute['desc']) : ''); ?>">
                                        info
                                    </span>
                                </span>
                                <div class="float-end mt-2 mx-auto">
                                    <?php
                                    if ($attribute["field"] == 'id') { ?>
                                        <input type="text" class="form-control" name="product_id_prefix"
                                            id="product_id_prefix" placeholder="Add Prefix" value="">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-6 mt-2">
                                <?php
                                $ee_select_option = $TVC_Admin_Helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
                                $require = (isset($attribute['required']) && $attribute['required']) ? true : false;
                                $sel_val_def = (isset($attribute['wAttribute'])) ? $attribute['wAttribute'] : "";
                                if ($attribute["field"] === 'link') {
                                    "product link";
                                } else if ($attribute["field"] === 'shipping') {
                                    $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                    $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', esc_html__('Add shipping flat rate', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require);
                                } else if ($attribute["field"] === 'tax') {
                                    $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? esc_attr($ee_mapped_attrs[$attribute["field"]]) : esc_attr($sel_val_def);
                                    $TVC_Admin_Helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                                } else if ($attribute["field"] === 'content_language') {
                                    $TVC_Admin_Helper->tvc_language_select($attribute["field"], 'content_language', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), 'en', $require);
                                } else if ($attribute["field"] === 'target_country') {
                                    $TVC_Admin_Helper->tvc_countries_select($attribute["field"], 'target_country', esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $require);
                                } else {
                                    if (isset($attribute['fixed_options']) && $attribute['fixed_options'] !== "") {
                                        $ee_select_option_t = explode(",", $attribute['fixed_options']);
                                        $ee_select_option = [];
                                        foreach ($ee_select_option_t as $o_val) {
                                            $ee_select_option[]['field'] = esc_attr($o_val);
                                        }
                                        $sel_val = $sel_val_def;
                                        $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                    } else {
                                        $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                        $TVC_Admin_Helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'enhanced-e-commerce-for-woocommerce-store'), $sel_val, $require, $ee_select_option);
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Additional attributes section -->
                    <div class="col-12 additinal_attr_main_div">
                        <?php
                        $cnt = 0;
                        if (!empty($tempAddAttr)) {
                            $additionalAttribute = array(
                                'condition',
                                'shipping_weight',
                                'product_weight',
                                'gender',
                                'sizes',
                                'color',
                                'age_group',
                                'additional_image_links',
                                'sale_price_effective_date',
                                'material',
                                'pattern',
                                'product_types',
                                'availability_date',
                                'expiration_date',
                                'adult',
                                'ads_redirect',
                                'shipping_length',
                                'shipping_width',
                                'shipping_height',
                                'custom_label_0',
                                'custom_label_1',
                                'custom_label_2',
                                'custom_label_3',
                                'custom_label_4',
                                'mobile_link',
                                'energy_efficiency_class',
                                'is_bundle',
                                'loyalty_points',
                                'unit_pricing_measure',
                                'unit_pricing_base_measure',
                                'promotion_ids',
                                'shipping_label',
                                'excluded_destinations',
                                'included_destinations',
                                'tax_category',
                                'multipack',
                                'installment',
                                'min_handling_time',
                                'max_handling_time',
                                'min_energy_efficiency_class',
                                'max_energy_efficiency_class',
                                'identifier_exists',
                                'cost_of_goods_sold'
                            );
                            $count_arr = count($additionalAttribute);
                            foreach ($tempAddAttr as $key => $value) {
                                $options = '<option>Please Select Attribute</option>';
                                foreach ($additionalAttribute as $val) {
                                    $selected = "";
                                    $disabled = "";
                                    if ($val == $key) {
                                        $selected = "selected";
                                    } else {
                                        if (array_key_exists($val, $tempAddAttr)) {
                                            $disabled = "disabled";
                                        }
                                    }

                                    $options .= '<option value="' . $val . '" ' . $selected . ' ' . $disabled . '>' . esc_html($val) . '</option>';
                                }
                                $option1 = '<option>Please Select Attribute</option>';
                                $fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
                                if (in_array($key, $fixed_att_select_list)) {
                                    if ($key == 'gender') {
                                        $gender = ['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'];
                                        foreach ($gender as $genKey => $genVal) {
                                            $selected = "";
                                            if ($genKey == $value) {
                                                $selected = "selected";
                                            }
                                            $option1 .= '<option value="' . $genKey . '" ' . $selected . '>' . esc_html($genVal) . '</option>';
                                        }
                                    }
                                    if ($key == 'condition') {
                                        $conArr = ['new' => 'New', 'refurbished' => 'Refurbished', 'used' => 'Used'];
                                        foreach ($conArr as $conKey => $conVal) {
                                            $selected = "";
                                            if ($conKey == $value) {
                                                $selected = "selected";
                                            }
                                            $option1 .= '<option value="' . $conKey . '" ' . $selected . '>' . esc_html($conVal) . '</option>';
                                        }
                                    }
                                    if ($key == 'age_group') {
                                        $ageArr = ['newborn' => 'Newborn', 'infant' => 'Infant', 'toddler' => 'Toddler', 'kids' => 'Kids', 'adult' => 'Adult'];
                                        foreach ($ageArr as $ageKey => $ageVal) {
                                            $selected = "";
                                            if ($ageKey == $value) {
                                                $selected = "selected";
                                            }
                                            $option1 .= '<option value="' . $ageKey . '" ' . $selected . '>' . $ageVal . '</option>';
                                        }
                                    }
                                    if ($key == 'adult' || $key == 'is_bundle' || $key == 'identifier_exists') {
                                        $boolArr = ['yes' => 'Yes', 'no' => 'No'];
                                        foreach ($boolArr as $boolKey => $boolVal) {
                                            $selected = "";
                                            if ($boolKey == $value) {
                                                $selected = "selected";
                                            }
                                            $option1 .= '<option value="' . $boolKey . '" ' . $selected . '>' . esc_html($boolVal) . '</option>';
                                        }
                                    }
                                } else {
                                    foreach ($wooCommerceAttributes as $valattr) {
                                        $selected = "";
                                        if ($valattr['field'] == $value) {
                                            $selected = "selected";
                                        }
                                        $option1 .= '<option value="' . $valattr['field'] . '" ' . $selected . '>' . $valattr['field'] . '</option>';
                                    }
                                }
                        ?>
                                <div class="row mt-1 attributehoverEffect additinal_attr_div m-0 p-0">
                                    <div class="col-6 mt-2">
                                        <select style="width:98%" id="<?php echo esc_attr($cnt++) ?>"
                                            name="additional_attr_[]"
                                            class="selectAttr additinal_attr fw-light text-secondary fs-6 form-control form-select-sm">
                                            <?php
                                            echo wp_kses($options, array(
                                                "option" => array(
                                                    'value' => array(),
                                                    'selected' => array(),
                                                ),
                                            ));
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-5 mt-2" style="padding-left: 0px">
                                        <select style="width:98%" id="" name="additional_attr_value_[]"
                                            class="selectAttr additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm">
                                            <?php
                                            echo wp_kses($option1, array(
                                                "option" => array(
                                                    'value' => array(),
                                                    'selected' => array(),
                                                ),
                                            ));
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-1 mt-2">
                                        <span
                                            class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2 pointer"
                                            title="Add Additional Attribute"
                                            style="cursor: pointer; margin-right:35px;">
                                            delete
                                        </span>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                    </div>
                </form>
                <div class="row add_additional_attr_div m-0 p-0 mb-2">
                    <div class="add_additional_attr_div mt-2" style="display: flex; justify-content: start">
                        <button type="button" class="fs-12 btn btn-soft-primary add_additional_attr pointer"
                            title="Add Attribute"> Add Attributes
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-start mb-2">
                <button type="button" class="btn btn-secondary btn-sm" id="goBackToFeedDetails" style="margin-left: 10px; margin-right: 10px;"><?php esc_html_e("Back", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                <button type="button" class="btn btn-primary btn-sm" disabled id="goToCategory"><?php esc_html_e("Next", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
            </div>
        </div>
        <div class="tab-pane fade" id="pills-map-product-category" role="tabpanel" aria-labelledby="pills-map-product-category-tab">
            <div class="conv-light-grey-bg rounded-top" style="margin-top: 10px;">
                <table style="width: 64%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 10px;">
                                <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" alt="Woo Logo" style="vertical-align: middle;" />
                                <span style="font-size: 14px; font-weight: normal; color: #6c757d; margin-left: 8px;">
                                    <?php esc_html_e("WooCommerce Product Category", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </span>
                            </th>
                            <?php if (isset($_GET['subpage']) && $_GET['subpage'] === 'gmc') { ?>
                                <th style="text-align: left; padding: 10px;">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" alt="Conversios Logo" style="vertical-align: middle;" />
                                    <span style="font-size: 14px; font-weight: normal; color: #6c757d; margin-left: 8px;">
                                        <?php esc_html_e("Google Product Category", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </span>
                                </th>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'microsoft') { ?>
                                <th style="text-align: left; padding: 10px;">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/ms-logo.png'); ?>" alt="Conversios Logo" style="vertical-align: middle;" />
                                    <span style="font-size: 14px; font-weight: normal; color: #6c757d; margin-left: 8px;">
                                        <?php esc_html_e("Microsoft Product Category", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </span>
                                </th>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'tiktok') { ?>
                                <th style="text-align: left; padding: 10px;">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" alt="Conversios Logo" style="vertical-align: middle;" />
                                    <span style="font-size: 14px; font-weight: normal; color: #6c757d; margin-left: 8px;">
                                        <?php esc_html_e("Tiktok Product Category", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </span>
                                </th>
                            <?php } else if (isset($_GET['subpage']) && $_GET['subpage'] === 'meta') { ?>
                                <th style="text-align: left; padding: 10px;">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_meta_logo.png'); ?>" alt="Conversios Logo" style="vertical-align: middle;" />
                                    <span style="font-size: 14px; font-weight: normal; color: #6c757d; margin-left: 8px;">
                                        <?php esc_html_e("Meta Product Category", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </span>
                                </th>
                            <?php }  ?>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="mb-2 rounded-bottom"
                style="width: 69% ; position: relative; margin-left: 10px;">
                <form id="category_mapping" action="">
                    <?php
                    $category_html = $category_wrapper_obj->category_table_content(0, 0, 'mapping', $ee_prod_mapped_cats);
                    echo wp_kses(
                        $category_html,
                        array(
                            "div" => array(
                                'class' => array(),
                                'style' => array(),
                                'id' => array(),
                                'title' => array(),
                            ),
                            "button" => array(
                                'type' => array(),
                                'class' => array(),
                                'style' => array(),
                                'id' => array(),
                                'title' => array(),
                            ),
                            "select" => array(
                                'name' => array(),
                                'class' => array(),
                                'id' => array(),
                                'style' => array('display'),
                                'catid' => array(),
                                'onchange' => array(),
                                'iscategory' => array(),
                                'tabindex' => array(),
                            ),
                            "option" => array(
                                'value' => array(),
                                'selected' => array(),
                            ),
                            "span" => array(
                                'class' => array(),
                                'style' => array(),
                                'id' => array(),
                                'title' => array(),
                                'data-bs-toggle' => array(),
                                'data-bs-placement' => array(),
                                'data-cat-id' => array(),
                                'data-id' => array(),
                            ),
                            "input" => array(
                                'type' => array(),
                                'name' => array(),
                                'class' => array(),
                                'id' => array(),
                                'placeholder' => array(),
                                'style' => array(),
                                'value' => array(),
                            ),
                            "label" => array(
                                'class' => array(),
                                'id' => array(),
                                'style' => array(),
                            ),
                            "small" => array(),
                        )
                    );
                    ?>
                </form>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="goBackToAttribute" style="margin-left: 10px ; margin-right: 10px;"><?php esc_html_e("Back", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
            <button type="button" class="btn btn-primary btn-sm" id="goToFilters"><?php esc_html_e("Next", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
        </div>
        <div class="tab-pane fade" id="pills-map-filters" role="tabpanel" aria-labelledby="pills-map-filters-tab" style="width: 70%;">
            <div class="alert mb-3 ms-2" role="alert"
                style="background:white; color:#0a4b78; margin:15px 0; max-width:100%;">
                <p class="mb-0" style="font-size: inherit;">
                    Filter products by Category, Product ID, Stock Status, or Main Image
                    to create cleaner feeds.<br>
                    Helping you show only the right products to customers and improve ad performance.
                </p>
            </div>
            <form id=" filterForm" style="margin-top:10px;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e("Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                            <th><?php esc_html_e("Condition", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                            <th><?php esc_html_e("Value", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="filterTableBody">
                        <!-- Empty tbody - no default row -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-start m-1 gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" id="goBackToCategory"><?php esc_html_e("Back", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="filterSubmit">
                        <?php esc_html_e("Save & next", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary addButton">
                        <span class="material-symbols-outlined align-middle">add_circle</span>
                        <?php esc_html_e("Add Filter", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-light btn-sm border-primary text-primary" id="filterReset">
                        <?php esc_html_e("Clear Filter", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
                <input type="hidden" id="strProData" value="<?php echo esc_html(sanitize_text_field($attr)); ?>">
                <input type="hidden" id="strConditionData" value="<?php echo esc_html(sanitize_text_field($condition)); ?>">
                <input type="hidden" id="strValueData" value="<?php echo esc_html(sanitize_text_field($value)); ?>">
                <input type="hidden" id="savedFilters" value='<?php echo esc_attr(json_encode($filterDataForJS)); ?>'>
            </form>
            <div id="filterError" class="text-danger mt-2" style="display:none;"></div>
        </div>
    </div>
</div>
<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 px-3">

            </div>
            <div class="modal-body text-center p-0">
                <?php echo wp_kses(
                    enhancad_get_plugin_image('/admin/images/logos/error_logo.png', '', '', 'width:184px;'),
                    array(
                        'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                        ),
                    )
                ); ?>
                <h3 class="fw-normal pt-3">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg px-3"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white dismissErrorModal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Success Save Modal 2 -->
<div class="modal fade" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-600">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center px-5">
                <div class="success-round d-flex rounded-circle justify-content-center align-items-center border-radius">
                    <span class="material-symbols-outlined text-white  fww-bold">check</span>
                </div>
                <h2 class="fw-normal pt-3 text-dark"><?php esc_html_e("Successful!", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                <h3 class="leave-a-review fw-normal mb-4 text-dark" style="display:none">
                    <?php esc_html_e("How did you like our feed creation? Any feedback is appreciated! ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <a target="_blank" href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/?rate=5#rate-response" class="conv-link-blue">Leave a Review</a>
                </h3>
                <span id="conv_save_success_txt" class="mb-1 d-flex justify-content-center text-dark fs-16 px-2"></span>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 mb-1 modalFooterSuccess w-100" style="display:flex; justify-content: center">
                <button class="btn fs-20 fw-normal w-100 text-white dismissModal" data-bs-dismiss="modal" style="background-color: #209365;">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="pp-modal onbrd-popupwrp" id="tvc_google_signin" tabindex="-1" role="dialog">
    <div class="onbrdppmain" role="document">
        <div class="onbrdnpp-cntner acccretppcntnr">
            <div class="onbrdnpp-hdr">
                <div class="ppclsbtn clsbtntrgr">
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/close-icon.png'),
                        array(
                            'img' => array(
                                'src' => true,
                                'alt' => true,
                                'class' => true,
                                'style' => true,
                            ),
                        )
                    ); ?>
                </div>
            </div>
            <div class="onbrdpp-body">
                <div class="h6 py-2 px-1" style="background: #d7ffd7;">Please use Chrome browser to configure the plugin if you face any issues during setup.</div>
                <div class="google_signin_sec_left">
                    <div class="google_connect_url google-btn">
                        <div class="google-icon-wrapper">
                            <?php echo wp_kses(
                                enhancad_get_plugin_image('/admin/images/g-logo.png'),
                                array(
                                    'img' => array(
                                        'src' => true,
                                        'alt' => true,
                                        'class' => true,
                                        'style' => true,
                                    ),
                                )
                            ); ?>
                        </div>
                        <p class="btn-text">
                            <b><?php esc_html_e("Sign in with google", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                        </p>
                    </div>
                    <p><?php esc_html_e("Make sure you sign in with the google email account that has all privileges to access google analytics, google ads and google merchant center account that you want to configure for your store.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                </div>
                <div class="google_signin_sec_right">
                    <h6><?php esc_html_e("Why do I need to sign in with google?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h6>
                    <p><?php esc_html_e("When you sign in with Google, we ask for limited programmatic access for your accounts in order to automate below features for you:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("1. Google Analytics:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To give you option to select GA accounts, to show actionable google analytics reports in plugin dashboard and to link your google ads account with google analytics account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("2. Google Ads:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate dynamic remarketing, conversion and enhanced conversion tracking and to create performance campaigns if required.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <p><strong><?php esc_html_e("3. Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store"); ?></strong><?php esc_html_e("To automate product feed using content api and to set up your GMC account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>

<div id="customDeleteModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-box">
        <h3>Confirm Feed Deletion</h3>
        <p id="customDeleteMessage"></p>
        <div class="custom-modal-actions">
            <button id="cancelDeleteBtn">Cancel</button>
            <button id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>
<?php
$fpath = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json';
$filesystem = $wp_filesystem;
$str = $filesystem->get_contents($fpath);
$str = json_decode($str);
?>

<!-- Success Save Modal End -->
<script>
    const urlParams = new URLSearchParams(window.location.search);
    const subpage = urlParams.get('subpage');
    var cat_json = <?php echo wp_json_encode($str) ?>;
    let filters = jQuery('#savedFilters').val();
    let savedFilters = [];
    
    jQuery(".notice.e-notice, .notice-error, .notice-error, .notice-warning, .notice-success, .notice-info").hide();

    jQuery(document).ready(function(jQuery) {
        jQuery(".nav-tab-wrapper a").on("click", function() {
            // Hide only the tab content, keep the tabs visible
            jQuery("#wpbody-content").children().not(".nav-tab-wrapper").remove();

            // Optional: instead of blank, show loader
            jQuery("#wpbody-content").append("<div style='padding:50px;text-align:center;'>Loading...</div>");
        });
    });


    try {
        savedFilters = JSON.parse(filters);
        if (Array.isArray(savedFilters)) {
            savedFilters.forEach(filter => {
                // Use display_value if available, otherwise use value
                let displayValue = filter.display_value || filter.value;

                let newRow = `
            <tr class="filterRow">
                <td>
                    <select class="form-select product" name="product[]" style="width:100%">
                        <option value="0">Select Attribute</option>
                        <option value="product_cat" ${filter.attr === 'product_cat' ? 'selected' : ''}>Category</option>
                        <option value="ID" ${filter.attr === 'ID' ? 'selected' : ''}>Product ID</option>
                        <option value="_stock_status" ${filter.attr === '_stock_status' ? 'selected' : ''}>Stock Status</option>
                        <option value="main_image" ${filter.attr === 'main_image' ? 'selected' : ''}>Main Image</option>
                    </select>
                </td>
                <td class="conditionDiv">
                    <select class="form-select condition" name="condition[]" style="width:100%">
                        <option value="0">Select Condition</option>
                        <option value="=" ${filter.condition === '=' ? 'selected' : ''}>=</option>
                        <option value="!=" ${filter.condition === '!=' ? 'selected' : ''}>!=</option>
                        <option value="in" ${filter.condition === 'in' ? 'selected' : ''}>IN</option>
                        <option value="not in" ${filter.condition === 'not in' ? 'selected' : ''}>NOT IN</option>
                    </select>
                </td>
                <td class="textValue">
                    <input type="text" class="form-control value" placeholder="Add value" name="value[]" 
                           value="${displayValue}" data-original-value="${filter.value}">
                </td>
                <td class="text-center align-middle">
                    <span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>
                </td>
            </tr>`;
                jQuery('#filterTableBody').append(newRow);

                // After appending, trigger the product change to convert field type if needed
                let $row = jQuery('#filterTableBody tr.filterRow').last();
                let $productSelect = $row.find('.product');
                let productValue = $productSelect.val();

                // If it's a dropdown field type, convert the input to select
                if (productValue === 'product_cat' || productValue === '_stock_status' || productValue === 'main_image') {
                    let $valueContainer = $row.find('.textValue');
                    let currentValue = filter.value;

                    if (productValue === 'product_cat') {
                        var category = <?php echo json_encode($category); ?>;
                        let option = '<option value="0">Select Category</option>';
                        jQuery.each(category, function(key, value) {
                            let selected = (key == currentValue) ? 'selected' : '';
                            option += '<option value="' + key + '" ' + selected + '>' + value + '</option>';
                        });
                        $valueContainer.empty();
                        var html = '<select class="category" name="value[]" style="width:100%">' + option + '</select>';
                        $valueContainer.append(html);
                    } else if (productValue === '_stock_status') {
                        $valueContainer.empty();
                        var html = '<select class="category" name="value[]" style="width:100%">' +
                            '<option value="0">Select Stock Status</option>' +
                            '<option value="instock" ' + (currentValue === 'instock' ? 'selected' : '') + '>In Stock</option>' +
                            '<option value="outofstock" ' + (currentValue === 'outofstock' ? 'selected' : '') + '>Out Of Stock</option>' +
                            '</select>';
                        $valueContainer.append(html);
                    } else if (productValue === 'main_image') {
                        $valueContainer.empty();
                        var html = '<select class="category" name="value[]" style="width:100%">' +
                            '<option value="0">Select Image State</option>' +
                            '<option value="EXISTS" ' + (currentValue === 'EXISTS' ? 'selected' : '') + '>Not Empty</option>' +
                            '</select>';
                        $valueContainer.append(html);
                    }
                }
            });
        } else {
            console.warn("Saved filters is not an array:", savedFilters);
        }
    } catch (e) {
        console.error("Invalid saved filters JSON:", e);
    }

    jQuery(document).on('click', '.deleteButton', function() {
        jQuery(this).closest('tr').remove();
    });

    function switchToTab(tabId) {
        jQuery('.nav-tab').removeClass('nav-tab-active');
        jQuery('.tab-pane').removeClass('show active');

        // Activate correct tab and pane
        jQuery('.nav-tab-wrapper a[href="' + tabId + '"]').addClass('nav-tab-active');
        jQuery(tabId).addClass('show active');

        // Re-initialize Select2 in the newly activated tab
        jQuery(tabId).find('select').each(function() {
            const $select = jQuery(this);

            // Destroy old Select2 instance if exists
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Initialize Select2
            $select.select2({
                dropdownParent: jQuery(tabId),
                width: 'style' // use 'resolve' or '100%' if needed
            });
        });
    }

    jQuery('#goToAttributes').on('click', function() {
        switchToTab('#pills-map-product-attribute');
    });
    jQuery('#goToCategory').on('click', function() {
        switchToTab('#pills-map-product-category');
    });
    jQuery('#goToFilters').on('click', function() {
        switchToTab('#pills-map-filters');
    });
    jQuery('#goBackToAttribute').on('click', function() {
        switchToTab('#pills-map-product-attribute');
    });
    jQuery('#goBackToFeedDetails').on('click', function() {
        switchToTab('#pills-enter-feed-details');
    });
    jQuery('#goBackToCategory').on('click', function() {
        jQuery('#filterError').hide();
        switchToTab('#pills-map-product-category');
    });
    jQuery(document).ready(function(jQuery) {
        jQuery('.nav-tab-wrapper a').on('click', function(e) {
            jQuery('.nav-tab').removeClass('nav-tab-active');
            jQuery(this).addClass('nav-tab-active');

            jQuery('.tab-pane').removeClass('show active');
            jQuery(jQuery(this).attr('href')).addClass('show active');

            jQuery('select').select2();
        });
    });
    jQuery(document).on('click', '.select2-selection.select2-selection--single', function(e) {
        var iscatMapped = jQuery(this).parent().parent().prev().attr('iscategory')
        var selectId = jQuery(this).parent().parent().prev().attr('id')
        var toAppend = '';
        if (iscatMapped == 'false') {
            jQuery(this).parent().parent().prev().attr('iscategory', 'true')
            jQuery.each(cat_json, function(i, o) {
                toAppend += '<option value="' + o.id + '">' + o.name + '</option>';
            });
            jQuery('#' + selectId).append(toAppend)
            jQuery('#' + selectId).select2()
            jQuery('#' + selectId).select2('open')
        }
    });
    jQuery(document).ready(function() {
        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        // for GMC --------------------------------------
        jQuery('#opengmcsettings').on('click', function() {
            jQuery('.gmcdetails').addClass('d-none');
            jQuery('.gmcnotconfigured').addClass('d-none');
            jQuery('.create-feed-section').addClass('d-none');
            jQuery('.gmcsettingscard').removeClass('d-none').hide().slideDown(300);
            jQuery('.feedlisttable').addClass('d-none');
            jQuery('.totalproductscount').addClass('d-none');

             <?php if ((isset($_GET['subscription_id']) === TRUE && esc_attr(sanitize_text_field(wp_unslash($_GET['subscription_id']))) !== '') || (empty($google_merchant_center_id) && !empty($cust_g_email))) { ?>
                list_google_merchant_account(tvc_data);
            <?php } ?>
        });

        jQuery('#closeButtongmc').on('click', function(e) {
            e.preventDefault();
            jQuery('.gmcsettingscard').addClass('d-none');
            <?php if ($google_merchant_center_id === '' && $g_mail === '') { ?>
                jQuery('.gmcnotconfigured').removeClass('d-none');
            <?php } else { ?>
                jQuery('.gmcnotconfigured').removeClass('d-none');
                jQuery('.gmcdetails').removeClass('d-none');
                <?php if ($count_feed <= 0) { ?>
                    jQuery('.create-feed-section').removeClass('d-none');
                <?php } else { ?>
                    jQuery('.feedlisttable').removeClass('d-none');
                    jQuery('.totalproductscount').removeClass('d-none');
                <?php }  ?>
            <?php } ?>
        });
        <?php if ($subpage == 'gmc') { ?>
            <?php if ($google_merchant_center_id != '' && $g_mail != '' && $count_feed <= 0) { ?>
                jQuery('.create-feed-section').removeClass('d-none');
            <?php } else if ($google_merchant_center_id != '' && $g_mail != '' && $count_feed > 0) { ?>
                jQuery('.feedlisttable').removeClass('d-none');
                jQuery('.totalproductscount').removeClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            <?php } ?>
        <?php } else if ($subpage == "microsoft") {  ?>
            <?php if ($microsoft_catalog_id != '' && $ms_mail != '' && $count_feed <= 0) { ?>
                jQuery('.create-feed-section').removeClass('d-none');
            <?php } else if ($microsoft_catalog_id != '' && $ms_mail != '' && $count_feed > 0) { ?>
                jQuery('.feedlisttable').removeClass('d-none');
                jQuery('.totalproductscount').removeClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            <?php } ?>
        <?php } else if ($subpage == "tiktok") {  ?>
            <?php if ($tiktok_business_account != '' && $tiktok_email != '' && $count_feed <= 0) { ?>
                jQuery('.create-feed-section').removeClass('d-none');
            <?php } else if ($tiktok_business_account != '' && $tiktok_email != '' && $count_feed > 0) { ?>
                jQuery('.feedlisttable').removeClass('d-none');
                jQuery('.totalproductscount').removeClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            <?php } ?>
        <?php } else if ($subpage == "meta") {  ?>
            <?php if ($fb_catalog_id != '' && $fb_mail != '' && $count_feed <= 0) { ?>
                jQuery('.create-feed-section').removeClass('d-none');
            <?php } else if ($fb_catalog_id != '' && $fb_mail != '' && $count_feed > 0) { ?>
                jQuery('.feedlisttable').removeClass('d-none');
                jQuery('.totalproductscount').removeClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            <?php } ?>
        <?php } ?>

        // jQuery('.create-feed-section').addClass('d-none');
        jQuery(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const subscription_id = urlParams.get('subscription_id');
            const g_mail = urlParams.get('g_mail');

            if (subscription_id && g_mail) {
                jQuery('.gmcsettingscard').removeClass('d-none');
                jQuery('.gmcdetails').addClass('d-none');
                jQuery('.gmcnotconfigured').addClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            }
        });

        // for MMC --------------------------------------
        jQuery('#openmmcsettings').on('click', function() {
            jQuery('.mmcdetails').addClass('d-none');
            jQuery('.mmcnotconfigured').addClass('d-none');
            jQuery('.create-feed-section').addClass('d-none');
            jQuery('.mmcsettingscard').removeClass('d-none').hide().slideDown(300);
            jQuery('.feedlisttable').addClass('d-none');
            jQuery('.totalproductscount').addClass('d-none');
        });

        jQuery('#closeButtonmmc').on('click', function(e) {
            e.preventDefault();
            jQuery('.mmcsettingscard').addClass('d-none');
            <?php if ($microsoft_catalog_id === '' && $ms_mail === '') { ?>
                jQuery('.mmcnotconfigured').removeClass('d-none');
            <?php } else { ?>
                jQuery('.mmcnotconfigured').removeClass('d-none');
                jQuery('.mmcdetails').removeClass('d-none');
                <?php if ($count_feed <= 0) { ?>
                    jQuery('.create-feed-section').removeClass('d-none');
                <?php } else { ?>
                    jQuery('.feedlisttable').removeClass('d-none');
                    jQuery('.totalproductscount').removeClass('d-none');
                <?php }  ?>
            <?php } ?>
        });

        // for Tiktok --------------------------------------
        jQuery('#opentiktoksettings').on('click', function() {
            jQuery('.tiktokdetails').addClass('d-none');
            jQuery('.tiktoknotconfigured').addClass('d-none');
            jQuery('.create-feed-section').addClass('d-none');
            jQuery('.tiktoksettingscard').removeClass('d-none').hide().slideDown(300);
            jQuery('.feedlisttable').addClass('d-none');
            jQuery('.totalproductscount').addClass('d-none');
        });

        jQuery('#closeButtontiktok').on('click', function(e) {
            e.preventDefault();
            jQuery('.tiktoksettingscard').addClass('d-none');
            <?php if ($tiktok_business_account === '' && $tiktok_email === '') { ?>
                jQuery('.tiktoknotconfigured').removeClass('d-none');
            <?php } else { ?>
                jQuery('.tiktoknotconfigured').removeClass('d-none');
                jQuery('.tiktokdetails').removeClass('d-none');
                <?php if ($count_feed <= 0) { ?>
                    jQuery('.create-feed-section').removeClass('d-none');
                <?php } else { ?>
                    jQuery('.feedlisttable').removeClass('d-none');
                    jQuery('.totalproductscount').removeClass('d-none');
                <?php }  ?>
            <?php } ?>
        });

        jQuery(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const subscription_id = urlParams.get('subscription_id');
            const tiktok_mail = urlParams.get('tiktok_mail');

            if (subscription_id && tiktok_mail) {
                jQuery('.tiktoksettingscard').removeClass('d-none');
                jQuery('.tiktokdetails').addClass('d-none');
                jQuery('.tiktoknotconfigured').addClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            }
        });

        // for Facebook --------------------------------------
        jQuery('#openmetasettings').on('click', function() {
            jQuery('.metadetails').addClass('d-none');
            jQuery('.metanotconfigured').addClass('d-none');
            jQuery('.create-feed-section').addClass('d-none');
            jQuery('.metasettingscard').removeClass('d-none').hide().slideDown(300);
            jQuery('.feedlisttable').addClass('d-none');
            jQuery('.totalproductscount').addClass('d-none');
        });

        jQuery('#closeButtonmeta').on('click', function(e) {
            e.preventDefault();
            jQuery('.metasettingscard').addClass('d-none');
            <?php if ($fb_catalog_id === '' && $fb_mail === '') { ?>
                jQuery('.metanotconfigured').removeClass('d-none');
            <?php } else { ?>
                jQuery('.metanotconfigured').removeClass('d-none');
                jQuery('.metadetails').removeClass('d-none');
                <?php if ($count_feed <= 0) { ?>
                    jQuery('.create-feed-section').removeClass('d-none');
                <?php } else { ?>
                    jQuery('.feedlisttable').removeClass('d-none');
                    jQuery('.totalproductscount').removeClass('d-none');
                <?php }  ?>
            <?php } ?>
        });

        jQuery(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const subscription_id = urlParams.get('subscription_id');
            const g_mail = urlParams.get('g_mail');
            const subpage = urlParams.get('subpage');

            if (subscription_id && g_mail && subpage === 'meta') {
                jQuery('.metasettingscard').removeClass('d-none');
                jQuery('.metadetails').addClass('d-none');
                jQuery('.metanotconfigured').addClass('d-none');
                jQuery('.create-feed-section').addClass('d-none');
            }
        });



        // -------------------------------------------------

        <?php if ($google_merchant_center_id != '' && $g_mail != '' && $count_feed <= 0) { ?>
            jQuery('.create-feed-section').removeClass('d-none');
        <?php } else if ($google_merchant_center_id != '' && $g_mail != '' && $count_feed > 0) { ?>
            jQuery('.feedlisttable').removeClass('d-none');
            jQuery('.totalproductscount').removeClass('d-none');
            jQuery('.create-feed-section').addClass('d-none');
        <?php } ?>

        /*********************Card Popover  End**************************************************************************/
        /*********************Custom DataTable for Search functionality Start*********************************************/
        jQuery('#feed_list_table').DataTable({
            // order: [
            //     [6, 'desc']
            // ],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12't>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            rowReorder: true,
            columnDefs: [{
                    orderable: true,
                    targets: 1
                },
                {
                    orderable: true,
                    targets: 2
                },
                {
                    orderable: true,
                    targets: 4
                },
                {
                    orderable: true,
                    targets: 5
                },
                {
                    orderable: true,
                    targets: 6
                },
                {
                    orderable: true,
                    targets: 7
                },
                {
                    orderable: true,
                    targets: 8
                },
                {
                    orderable: false,
                    targets: '_all'
                },

            ],

            initComplete: function() {
                jQuery('#search_feed').on('input', function() {
                    jQuery('#feed_list_table').DataTable().search(jQuery(this).val()).draw();
                });
            }
        });

        
        jQuery('#create_new_feed_div').insertAfter('#feed_list_table_filter');
        jQuery('#feed_list_table_filter').insertAfter('#feed_list_table_length');
        jQuery('#feed_list_table_filter').parent().addClass('d-flex align-items-center');
        jQuery('#create_new_feed_div').parent().addClass('d-flex align-items-center justify-content-end');


        /*********************Custom DataTable for Search functionality End***********************************************/
        /****************Create Feed call start********************************/
        jQuery('#create_new_feed, .create_new_feed').on('click', function(events) {
            //jQuery('#gmc_id').attr('disabled', false);
            //jQuery('#tiktok_id').attr('disabled', false);
            jQuery('#target_country').attr('disabled', false);
            jQuery('#autoSyncIntvl').attr('disabled', false);
            jQuery("#feedForm")[0].reset();
            jQuery('#feedType').text('Create New Feed');
            jQuery('#submitFeed').text('Create and Next');
            jQuery('#edit').val('');
            jQuery('#tiktok_id').val('');
            jQuery('.tiktok_catalog_id').empty();
            jQuery('.tiktok_catalog_id').removeClass('text-danger');
            jQuery('.feedFormarea').removeClass('d-none');
            jQuery('.channel-setup-parent').addClass('d-none');
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            jQuery('.select2').select2({
                dropdownParent: jQuery("#feedForm")
            });
            var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
            if (tiktok_business_account !== '' && jQuery('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val());
            }
        });
        /****************Create Feed call end***********************************/
        /****************Feed Name error dismissed start************************/
        jQuery(document).on('input', '#feedName', function(e) {
            e.preventDefault();
            jQuery('#feedName').css('margin-left', '0px');
            jQuery('#feedName').css('margin-right', '0px');
            jQuery('#feedName').removeClass('errorInput');
        });
        /****************Feed Name error dismissed end**************************/
        /****************Submit Feed call start*********************************/
        jQuery(document).on('click', '#submitFeed', function(e) {
            e.preventDefault();
            let feedName = jQuery('#feedName').val();
            if (feedName === '') {
                jQuery('#feedName').css('margin-left', '0px');
                jQuery('#feedName').css('margin-right', '0px');
                jQuery('#feedName').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    jQuery('#feedName').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }

            let autoSyncIntvl = jQuery('#autoSyncIntvl').val();
            if (autoSyncIntvl === '') {
                jQuery('#autoSyncIntvl').css('margin-left', '0px');
                jQuery('#autoSyncIntvl').css('margin-right', '0px');
                jQuery('#autoSyncIntvl').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    jQuery('#autoSyncIntvl').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }

            let target_country = jQuery('#target_country').find(":selected").val();
            if (target_country === "") {
                jQuery('.select2-selection').css('border', '1px solid #ef1717');
                return false;
            }
            jQuery('#submitFeed').addClass("disabledsection");
            save_feed_data();
        });

        jQuery(".resetbtn").on('click', function() {
            // Hide feed form, show channel setup section
            // jQuery('.feedFormarea').addClass('d-none');
            // jQuery('.channel-setup-parent').removeClass('d-none');
            jQuery(this).prop('disabled', true);
            // Remove 'edit' from URL and reload the page
            const url = new URL(window.location.href);
            url.searchParams.delete('edit');
            window.location.href = url.toString();
        });



        /****************Submit Feed call end***********************************/
        /********************Modal POP up validation on click remove**********************************/
        jQuery(document).on('click', '#gmc_id', function(e) {
            jQuery('.errorChannel').css('color', '');
        });
        jQuery(document).on('click', '#tiktok_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        jQuery(document).on('click', '#fb_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        jQuery(document).on('click', '#mmc_id', function(e) {
            jQuery('.errorChannel').css('border', '');
        });
        /********************Modal POP up validation on click remove end **********************************/
        /****************Get tiktok catalog id on target country change ***************************************/
        jQuery(document).on('change', '#target_country', function(e) {
            var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
            jQuery('.select2-selection').css('border', '1px solid #c6c6c6');
            let target_country = jQuery('#target_country').find(":selected").val();
            jQuery('#tiktok_id').empty();
            jQuery('.tiktok_catalog_id').empty()
            if (target_country !== "" && tiktok_business_account !== "") {
                getCatalogId(target_country);
            }
        });
        /****************Get tiktok catalog id on target country change end ***************************************/
        /************************************* Auto Sync Toggle Button Start*************************************************************************/
        jQuery(document).on('change', '#autoSync', function() {
            var autoSync = jQuery('input#autoSync').is(':checked');
            if (autoSync) {
                jQuery('#autoSyncIntvl').attr('disabled', false);
            } else {
                jQuery('#autoSyncIntvl').attr('disabled', true);
                jQuery('#autoSyncIntvl').val(25);
                jQuery('#autoSyncIntvl').removeClass('errorInput');
            }

        });
        /************************************* Auto Sync Toggle Button End*************************************************************************/
        /****************Get tiktok catalog id on check box change ***************************************/
        jQuery(document).on('change', '#tiktok_id', function() {
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_id').val('');
            if (jQuery('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val())
            }
        });
        /****************Get tiktok catalog id on check box change end ***************************************/
    });
    /*************************************Process Loader Start*************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }

    function conv_change_loadingbar_modal(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue_modal").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
            jQuery('#submitFeed').attr('disabled', true);
        } else {
            jQuery("#loadingbar_blue_modal").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
            jQuery('#submitFeed').attr('disabled', false);
        }
    }
    /*************************************Process Loader End*************************************************************************/
    /*************************************Restrict Zero start*************************************************************************/
    function removeZero() {
        var val = jQuery("#autoSyncIntvl").val();
        if (val === '0') {
            jQuery("#autoSyncIntvl").val('')
        }
    }

    function selectSubCategory(thisObj) {
        selectId = thisObj.id;
        wooCategoryId = jQuery(thisObj).attr("catid");
        var selvalue = jQuery('#' + selectId).find(":selected").val();
        var seltext = jQuery('#' + selectId).find(":selected").text();
        jQuery("#category-" + wooCategoryId).val(selvalue);
        jQuery("#category-name-" + wooCategoryId).val(seltext);
    }
    /*************************************Restrict Zero  End*************************************************************************/
    /*************************************Save Feed Data Start*************************************************************************/
    function save_feed_data(google_merchant_center_id, catalog_id) {
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        let edit = jQuery('#edit').val();
        var data = {
            action: "save_feed_data",
            feedName: jQuery('#feedName').val(),
            google_merchant_center: jQuery('input[name="gmc_id"]').val() === undefined ? '' : '1',
            fb_catalog_id: jQuery('input[name="fb_id"]').val() === undefined ? '' : '2',
            tiktok_id: jQuery('input[name="tiktok_id"]').val() === undefined ? '' : '3',
            microsoft_merchant_center: jQuery('input[name="mmc_id"]').val() === undefined ? '' : '4',
            tiktok_catalog_id: jQuery('#tiktok_id').val(),
            autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
            autoSyncIntvl: '25',
            edit: edit,
            last_sync_date: jQuery('#last_sync_date').val(),
            is_mapping_update: jQuery('#is_mapping_update').val(),
            target_country: jQuery('#target_country').find(":selected").val(),
            customer_subscription_id: "<?php echo esc_js($subscriptionId) ?>",
            tiktok_business_account: "<?php echo esc_js($tiktok_business_account) ?>",
            IncProductVar: jQuery('#IncProductVar').is(':checked') ? '1' : '0',
            IncDefProductVar: jQuery('#IncDefProductVar').is(':checked') ? '1' : '0',
            IncLowestPriceProductVar: jQuery('#IncLowestPriceProductVar').is(':checked') ? '1' : '0',
            cat_data: jQuery("#category_mapping").find("input[value!=''], select:not(:empty), input[type='number']").serialize(),
            attr_data: jQuery("#attribute_mapping").find("input[value!=''], select:not(:empty), input[type='number']").serialize(),
            productData: jQuery("#strProData").val(),
            conditionData: jQuery("#strConditionData").val(),
            valueData: jQuery("#strValueData").val(),
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar_modal('show');
            },
            error: function(err, status) {
                conv_change_loadingbar_modal('hide');
                jQuery('#convCreateFeedModal').modal('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function(response) {
                if (response.id) {
                    var feedurl = "<?php echo esc_url_raw($site_url . '&tab=product_list&id='); ?>" + response.id + "&from=" + subpage;
                    location.href = feedurl

                } else if (response.errorType === 'tiktok') {
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('.tiktok_catalog_id').html(response.message);
                    jQuery('.tiktok_catalog_id').addClass('text-danger');
                    jQuery("#filterError")
                        .html('<ul class="woocommerce-error" role="alert"><li>The selected target country is not supported by TikTok. Please select a valid country to continue.</li></ul>')
                        .show();
                } else {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
                conv_change_loadingbar_modal('hide');
            }
        });

    }

    function updateUrlWithEdit(id) {
        const url = new URL(window.location.href);
        url.searchParams.set('edit', id);
        window.location.href = url.toString();
    }

    jQuery(document).ready(function() {
        const edit_url = new URL(window.location.href);
        const editId = edit_url.searchParams.get('edit');
        if (editId) {
            jQuery('#gmc_id').attr('disabled', false);
            jQuery('#target_country').attr('disabled', false);
            var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
            var data = {
                action: "get_feed_data_by_id",
                id: editId,
                conv_onboarding_nonce: conv_onboarding_nonce
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    conv_change_loadingbar('show');
                },
                error: function(err, status) {
                    conv_change_loadingbar('hide');
                    jQuery("#conv_save_error_txt").html('Error occured.');
                    jQuery("#conv_save_error_modal").modal("show");
                },
                success: function(response) {
                    jQuery('#feedName').val(response[0].feed_name);
                    jQuery('#last_sync_date').val(response[0].last_sync_date);
                    jQuery('#is_mapping_update').val(response[0].is_mapping_update);
                    jQuery('#autoSyncIntvl').val(response[0].auto_sync_interval);

                    if (response[0].target_country) {
                        jQuery('#target_country').val(response[0].target_country);
                    }
                    if (response[0].IncProductVar === '1') {
                        jQuery('input#IncProductVar').prop('checked', true);
                    } else {
                        jQuery('input#IncProductVar').prop('checked', false);
                    }
                    if (response[0].IncDefProductVar === '1') {
                        jQuery('input#IncDefProductVar').prop('checked', true);
                    }
                    if (response[0].IncLowestPriceProductVar === '1') {
                        jQuery('input#IncLowestPriceProductVar').prop('checked', true);
                    }
                    if (response[0].auto_schedule === '1') {
                        jQuery('input#autoSync').prop('checked', true);
                        jQuery('#autoSyncIntvl').attr('disabled', false);
                    } else {
                        jQuery('input#autoSync').prop('checked', false);
                        jQuery('#autoSyncIntvl').attr('disabled', true);
                    }
                    jQuery('#gmc_id').prop("checked", false);
                    jQuery('#gmc_id').attr('disabled', false);
                    jQuery('#tiktok_id').prop("checked", false);
                    jQuery('#tiktok_id').attr('disabled', false);
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('#fb_id').prop("checked", false);
                    jQuery('#fb_id').attr('disabled', false);
                    jQuery('#mmc_id').prop("checked", false);
                    jQuery('#mmc_id').attr('disabled', false);
                    //jQuery('#fb_id').prop("checked", false);
                    var tiktok_business_account = "<?php echo esc_js($tiktok_business_account) ?>";
                    var google_merchant_center_id = "<?php echo esc_js($google_merchant_center_id) ?>";
                    var facebook_business_account = "<?php echo esc_js($facebook_business_account) ?>";
                    var microsoft_merchant_center_id = "<?php echo esc_js($microsoft_merchant_center_id) ?>";
                    if (tiktok_business_account == "") {
                        jQuery('#tiktok_id').attr('disabled', true);
                        jQuery('#tiktok_id').attr('checked', false);
                    }
                    if (google_merchant_center_id == "") {
                        jQuery('#gmc_id').attr('disabled', true);
                        jQuery('#gmc_id').attr('checked', false);
                    }
                    if (facebook_business_account == "") {
                        jQuery('#fb_id').attr('disabled', true);
                        jQuery('#fb_id').attr('checked', false);
                    }
                    if (microsoft_merchant_center_id == "") {
                        jQuery('#mmc_id').attr('disabled', true);
                        jQuery('#mmc_id').attr('checked', false);
                    }
                    channel_id = response[0].channel_ids.split(",");
                    jQuery.each(channel_id, function(index, val) {
                        if (val === '1') {
                            jQuery('#gmc_id').prop("checked", true);
                        }
                        if (val === '3') {
                            jQuery('#tiktok_id').prop("checked", true);
                            jQuery('#tiktok_id').val(response[0].tiktok_catalog_id);
                            jQuery('.tiktok_catalog_id').html(response[0].tiktok_catalog_id)
                        }
                        if (val == '2') {
                            jQuery('#fb_id').prop("checked", true);
                        }
                        if (val === '4') {
                            jQuery('#mmc_id').prop("checked", true);
                        }
                    });
                    if (response[0].is_mapping_update == '1') {
                        jQuery('#gmc_id').attr('disabled', true);
                        jQuery('#fb_id').attr('disabled', true);
                        jQuery('#tiktok_id').attr('disabled', true);
                        jQuery('#mmc_id').attr('disabled', true);
                        // jQuery('#target_country').attr('disabled', true);
                    }
                    jQuery('#edit').val(response[0].id);
                    jQuery('#centered').html();
                    jQuery('#submitFeed').text('Update Feed');
                    jQuery('#feedType').text('Edit Feed - ' + response[0].feed_name);
                    conv_change_loadingbar('hide');
                    jQuery('#target_country').select2({
                        dropdownParent: jQuery("#convCreateFeedModal")
                    });
                    jQuery('.feedFormarea').removeClass('d-none');
                    jQuery('.channel-setup-parent').addClass('d-none');
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                    validateStep('step1');
                    // Reinitialize Select2 elements after DOM updates
                    setTimeout(function() {
                        jQuery('select.select2').each(function() {
                            if (jQuery(this).hasClass('select2-hidden-accessible')) {
                                jQuery(this).select2('destroy');
                            }
                            jQuery(this).select2();
                        });
                    }, 100);
                }
            });

        }
    });

    function editFeed($id) {
        updateUrlWithEdit($id);
    }
    /*************************************Edit Feed Data End****************************************************************************/
    /*************************************Duplicate Feed Data Start*********************************************************************/
    function duplicateFeed($id) {
        var feed_count = jQuery('#feedCount').val();
        var conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>"
        var data = {
            action: "ee_duplicate_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar('show');
            },
            error: function(err, status) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function(response) {
                conv_change_loadingbar('hide');
                if (response.error === false) {
                    jQuery("#conv_save_success_txt").html(response.message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        location.reload(true);
                    }, 2000);
                } else {
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
            }
        });
    }
    /*************************************Duplicate Feed Data End*********************************************************************/
    /*************************************DELETE Feed Data Start**********************************************************************/
    function deleteFeed(feedId, channelCount) {
        // Dynamic message based on channel count
        let message = '';
        if (channelCount > 1) {
            message = "This feed is linked to multiple channels. Deleting it will remove it from all of them. Do you still want to proceed?";
        } else {
            message = "Deleting this feed will remove its products from your Merchant Center account, affecting your campaigns.";
        }

        // Set message and store feed ID in confirm button
        document.getElementById("customDeleteMessage").innerText = message;
        document.getElementById("confirmDeleteBtn").setAttribute("data-feed-id", feedId);

        // Show modal
        document.getElementById("customDeleteModal").style.display = "flex";
    }

    // Modal button listeners
    document.getElementById("cancelDeleteBtn").addEventListener("click", function() {
        document.getElementById("customDeleteModal").style.display = "none";
    });

    document.getElementById("confirmDeleteBtn").addEventListener("click", function() {
        const feedId = this.getAttribute("data-feed-id");
        const conv_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conv_onboarding_nonce')); ?>";

        var data = {
            action: "ee_delete_feed_data_by_id",
            id: feedId,
            conv_onboarding_nonce: conv_onboarding_nonce
        };

        // Close modal
        document.getElementById("customDeleteModal").style.display = "none";

        // Start AJAX
        conv_change_loadingbar('show');
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_success_txt").html(response.message);
                jQuery("#conv_save_success_modal").modal("show");
                setTimeout(function() {
                    location.reload(true);
                }, 1000);
            },
            error: function() {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error in Deleting Feed.');
                jQuery("#conv_save_error_modal").modal("show");
            }
        });
    });

    /*************************************Delete Feed Data End*************************************************************************/
    /*************************************Save Feed Data End***************************************************************************/
    function conv_change_loadingbar_header(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue_header").removeClass('d-none');
            jQuery("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue_header").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    /*************************Create Super AI Feed Start ************************************************************************/
    /*************************Slider animation start ************************************************************************/
    jQuery(document).on('click', '.toggleOpen', function() {
        jQuery('.toggleSpan').show(300);
    })
    jQuery(document).on('click', '.toggleClose', function() {
        jQuery('.toggleSpan').hide(300);
    })
    /*************************Slider animation end ************************************************************************/
    /*************************************Get saved catalog id by country code start **************************************************/
    function getCatalogId($countryCode) {
        var conv_country_nonce = "<?php echo esc_js(wp_create_nonce('conv_country_nonce')); ?>";
        var data = {
            action: "ee_getCatalogId",
            countryCode: $countryCode,
            conv_country_nonce: conv_country_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar_modal('show');
            },
            error: function(err, status) {
                //conv_change_loadingbar_modal('hide');
            },
            success: function(response) {
                jQuery('.tiktok_catalog_id').empty();
                jQuery('#tiktok_id').empty();
                jQuery('.tiktok_catalog_id').removeClass('text-danger');

                if (response.error == false) {
                    if (response.data.catalog_id !== '') {
                        jQuery('#tiktok_id').val(response.data.catalog_id);
                        jQuery('.tiktok_catalog_id').text(response.data.catalog_id);
                        jQuery('.tiktok_catalog_message').hide();
                    } else {
                        jQuery('#tiktok_id').val('Create New');
                        jQuery('.tiktok_catalog_id').text(
                            'You do not have a catalog associated with the selected target country. Do not worry we will create a new catalog for you.'
                        );

                        if (typeof subpage !== "undefined" && subpage === 'tiktok') {
                            jQuery('.tiktok_catalog_message').show();
                        } else {
                            jQuery('.tiktok_catalog_message').hide();
                        }
                    }
                }

                conv_change_loadingbar_modal('hide');
            }

        });
    }
    /*************************************Get saved catalog id by country code End ****************************************************/
</script>
<script>
    /*********************************** Pmax Campaign related code start *************************************************************/
    jQuery(".google_connect_url").on("click", function() {
        const w = 600;
        const h = 650;
        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document
            .documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document
            .documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left = (width - w) / 2 / systemZoom + dualScreenLeft;
        const top = (height - h) / 2 / systemZoom + dualScreenTop;
        var url = '<?php echo esc_url($googleConnect_url); ?>';
        url = url.replace(/&amp;/g, '&');
        url = url.replaceAll('&#038;', '&');
        const newWindow = window.open(url, "newwindow", config = `scrollbars=yes,
                            width=${w / systemZoom}, 
                            height=${h / systemZoom}, 
                            top=${top}, 
                            left=${left},toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,directories=no,status=no
                            `);
        if (window.focus) newWindow.focus();
    });
    jQuery(document).on('click', '.signinWithGoogle', function() {
        jQuery('#tvc_google_signin').addClass('showpopup');
        jQuery('body').addClass('scrlnone');
    });

    jQuery(".clsbtntrgr").on("click", function() {
        jQuery(this).closest('.pp-modal').removeClass('showpopup');
        jQuery('body').removeClass('scrlnone');
    });
    
    /*********************************** Pmax Campaign related code End ***************************************************************/
    jQuery(document).on('change', '.additinal_attr', function() {
        var fixed_att_select_list = ["gender", "age_group", "condition", "adult", "is_bundle", "identifier_exists"];
        var attr = jQuery(this).val();
        if (jQuery.inArray(attr, fixed_att_select_list) !== -1) {
            var option1 = '<option value="">Please Select Attribute</option>';
            if (attr == 'gender') {
                option1 += '<option value="male">Male</option><option value="female">Female</option><option value="unisex">Unisex</option>'
            }
            if (attr == 'condition') {
                option1 += '<option value="new">New</option><option value="refurbished">Refurbished</option><option value="used">Used</option>'
            }
            if (attr == 'age_group') {
                option1 += '<option value="newborn">Newborn</option><option value="infant">Infant</option><option value="toddler">Toddler</option><option value="kids">Kids</option><option value="adult">Adult</option>'
            }
            if (attr == 'adult' || attr == 'is_bundle' || attr == 'identifier_exists') {
                option1 += '<option value="yes">Yes</option><option value="no">No</option>'
            }
            jQuery(this).parent().next().find('.additional_attr_value').html(option1)
        } else {
            var wooCommerceAttributes = <?php echo wp_json_encode($wooCommerceAttributes); ?>;
            var option1 = '<option value="">Please Select Attribute</option>';
            jQuery.each(wooCommerceAttributes, function(index, value) {
                option1 += '<option value="' + value.field + '">' + value.field + '</option>'
            });
            jQuery(this).parent().next().find('.additional_attr_value').html(option1)
        }
    })
    jQuery(document).on('change', '.additional_attr_value', function() {
        jQuery(this).parent().removeClass('errorInput');
    });

    var selected = Array();
    var cnt = <?php echo esc_js($cnt) ?>;
    jQuery(document).on('click', '.add_additional_attr', function() {
        var additionalAttribute = [{
                "field": "condition"
            }, {
                "field": "shipping_weight"
            }, {
                "field": "product_weight"
            },
            {
                "field": "gender"
            }, {
                "field": "sizes"
            }, {
                "field": "color"
            }, {
                "field": "age_group"
            },
            {
                "field": "additional_image_links"
            }, {
                "field": "sale_price_effective_date"
            },
            {
                "field": "material"
            }, {
                "field": "pattern"
            }, {
                "field": "availability_date"
            }, {
                "field": "expiration_date"
            },
            {
                "field": "product_types"
            }, {
                "field": "ads_redirect"
            }, {
                "field": "adult"
            }, {
                "field": "shipping_length"
            },
            {
                "field": "shipping_width"
            }, {
                "field": "shipping_height"
            }, {
                "field": "custom_label_0"
            }, {
                "field": "custom_label_1"
            },
            {
                "field": "custom_label_2"
            }, {
                "field": "custom_label_3"
            }, {
                "field": "custom_label_4"
            }, {
                "field": "mobile_link"
            },
            {
                "field": "energy_efficiency_class"
            }, {
                "field": "is_bundle"
            }, {
                "field": "promotion_ids"
            }, {
                "field": "loyalty_points"
            },
            {
                "field": "unit_pricing_measure"
            }, {
                "field": "unit_pricing_base_measure"
            }, {
                "field": "shipping_label"
            },
            {
                "field": "excluded_destinations"
            }, {
                "field": "included_destinations"
            }, {
                "field": "tax_category"
            },
            {
                "field": "multipack"
            }, {
                "field": "installment"
            }, {
                "field": "min_handling_time"
            }, {
                "field": "max_handling_time"
            },
            {
                "field": "min_energy_efficiency_class"
            }, {
                "field": "max_energy_efficiency_class"
            }, {
                "field": "identifier_exists"
            },
            {
                "field": "cost_of_goods_sold"
            }
        ];

        var count = Object.keys(additionalAttribute).length;
        var option = '<option value="">Please Select Attribute</option>';
        jQuery.each(additionalAttribute, function(index, value) {
            /*****Check for selected option to disabled start*******/
            var disabled = "";
            if (jQuery.inArray(value.field, selected) !== -1) {
                disabled = "disabled";
            }
            /*****Check for selected option to disabled end*******/
            option += '<option value="' + value.field + '" ' + disabled + '>' + value.field + '</option>'
        });
        var wooCommerceAttributes = <?php echo wp_json_encode($wooCommerceAttributes); ?>;
        var option1 = '<option value="">Please Select Attribute</option>';
        jQuery.each(wooCommerceAttributes, function(index, value) {
            option1 += '<option value="' + value.field + '">' + value.field + '</option>'
        });

        var html = '';
        html += '<div class="row mt-1 attributehoverEffect additinal_attr_div m-0 p-0" ><div class="col-6 mt-2">';
        html += '<select style="width:98%" id="' + cnt++ + '" name="additional_attr_[]" class="selectAttr additinal_attr fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">';
        html += option;
        html += '</select></div>';
        html += '<div class="col-5 mt-2" style="padding-left: 0px;">';
        html += '<select style="width:98%" id="" name="additional_attr_value_[]" class="selectAttr additional_attr_value fw-light text-secondary fs-6 form-control form-select-sm select2 select2-hidden-accessible">';
        html += option1;
        html += '</select></div>';
        html += '<div class="col-1 mt-2">';
        html += '<span class="material-symbols-outlined text-danger remove_additional_attr fs-5 mt-2 pointer" title="Add Additional Attribute" style="cursor: pointer; margin-right:35px;">';
        html += 'delete';
        html += '</span>';
        html += '</div></div>';
        jQuery('.additinal_attr_main_div').append(html)
        jQuery('.selectAttr').select2()
        jQuery('.add_additional_attr')[0].scrollIntoView(true)
        var div_count = jQuery('.additinal_attr_div').length;
        if (count == div_count) {
            jQuery('.add_additional_attr').addClass('d-none')
        }
        validateStep('step2');
    });
    jQuery(document).on('click', '.remove_additional_attr', function() {
        jQuery('.remove_additional_attr *').addClass('disabled');
        //get deleted selected tag value
        var deleted = jQuery(this).parent().parent('.additinal_attr_div').find('.additinal_attr').find(':selected').val()
        if (deleted != '') {
            //Remove value from array
            selected = jQuery.grep(selected, function(value) {
                return value != deleted
            });
            //Enable deleted value to other selecet tag
            jQuery(".additinal_attr option").each(function() {
                var $thisOption = jQuery(this)
                var valueToCompare = deleted
                if ($thisOption.val() == valueToCompare) {
                    $thisOption.removeAttr("disabled")
                }
            });
        }

        jQuery(this).parent().parent('.additinal_attr_div').remove()
        jQuery('.add_additional_attr').removeClass('d-none')
        jQuery('.remove_additional_attr *').removeClass('disabled')
        validateStep('step2');
    });
    jQuery(document).on('change', '.additional_attr_value', function() {
        jQuery(this).parent().find('.select2-selection--single').removeClass('errorInput');
    });
    jQuery(document).on('change', '.additinal_attr', function() {
        selected = []
        jQuery(this).parent().find('.select2-selection--single').removeClass('errorInput');
        var sel = jQuery(this).find(":selected").val()
        var id = jQuery(this).attr("id")
        //All empty select add more used, it will add disable attribute to selected value
        jQuery(".additinal_attr:not(#" + id + ") option").each(function() {
            var $thisOption = jQuery(this)
            var valueToCompare = sel
            if ($thisOption.val() == valueToCompare) {
                $thisOption.attr("disabled", "disabled")
            }
        });
        var attr_choices = jQuery(".additinal_attr option:selected")
        jQuery(attr_choices).each(function(i, v) {
            selected.push(attr_choices.eq(i).val())
        })
        disableOptions()
    });

    function disableOptions() {
        jQuery('.additinal_attr *').removeAttr("disabled");
        jQuery(selected).each(function(i, v) {
            jQuery(".additinal_attr option").each(function() {
                var $thisOption = jQuery(this)
                var valueToCompare = v
                if (jQuery(this).parent().find(':selected').val() != v) {
                    if ($thisOption.val() == valueToCompare) {
                        $thisOption.attr("disabled", "disabled")
                    }
                }
            })
        })
    }

    function getConditionDropDown(val = '', condition = '') {
        let conditionOption = '<select class="condition" name="condition[]" style="width: 100%"><option value="0">Select Conditions</option>';
        if (val != '0') {
            if (val != '' || condition != '') {
                switch (val) {
                    case 'product_cat':
                    case 'ID':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") + ' > = </option>' +
                            '<option value="!=" ' + ((condition == "!=") ? "selected" : "") + ' > != </option>';
                        break;
                    case '_stock_status':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") + ' > = </option>' +
                            '<option value="!=" ' + ((condition == "!=") ? "selected" : "") + ' > != </option>';
                        break;
                    case 'main_image':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") + ' > = </option>';
                        break;
                }
            }
        }
        conditionOption += '</select>';
        return conditionOption;
    }
    // Master validation function
    function validateStep(step) {
        let isValid = true;

        switch (step) {
            case 'step1': // Feed details
                let feedName = jQuery('#feedName').val().trim();
                let target_country = jQuery('#target_country').find(":selected").val();

                if (feedName === '' || target_country === '') {
                    isValid = false;
                }

                jQuery('#goToAttributes').prop('disabled', !isValid);
                break;

            case 'step2': // Attributes
                isValid = true; // assume valid

                jQuery(".additinal_attr").each(function() {
                    if (this.selectedIndex === 0) {
                        isValid = false;
                        return false; // break loop early
                    }
                });

                jQuery(".additional_attr_value").each(function() {
                    if (this.selectedIndex === 0) {
                        isValid = false;
                        return false; // break loop early
                    }
                });

                jQuery('#goToCategory').prop('disabled', !isValid);
                break;
        }
    }

    // Attach validation events
    jQuery(document).ready(function() {
        // Step 1: Feed details
        jQuery(document).on('input change', '#feedName, #target_country', function() {
            validateStep('step1');
        });

        // Step 2: Attributes
        jQuery(document).on('change', '.additinal_attr, .additional_attr_value, .remove_additional_attr', function() {
            validateStep('step2');
        });

        jQuery(document).on('click', ' .remove_additional_attr', function() {
            validateStep('step2');
        });

        // Run once on page load (in case fields are pre-filled)
        validateStep('step1');
        validateStep('step2');
    });

    jQuery(document).on('click', '#filterSubmit', function(event) {
        jQuery("#goBackToCategory, .addButton, #filterReset, #filterSubmit").prop("disabled", true);

        let product = jQuery("select[name='product[]'] option:selected").map(function() {
            return jQuery(this).val();
        }).get();
        let producttext = jQuery("select[name='product[]'] option:selected").map(function() {
            return jQuery(this).text();
        }).get();
        let condition = jQuery("select[name='condition[]'] option:selected").map(function() {
            return jQuery(this).val();
        }).get();
        let value = jQuery("input[name='value[]']").map(function() {
            return jQuery(this).val();
        }).get();
        let seltext = jQuery("select[name='value[]'] option:selected, .category option:selected").map(function() {
            return jQuery(this).text();
        }).get();
        let selVal = jQuery("select[name='value[]'] option:selected, .category option:selected").map(function() {
            return jQuery(this).val() ? jQuery(this).val() : '';
        }).get();

        // === FILTER VALIDATION ONLY ===
        let errorMessages = [];
        let hasError = false;
        let filterValidation = false;

        // Clear old error styles
        jQuery("#filterTableBody").find('.errorInput').removeClass('errorInput');
        jQuery("#filterTableBody").find('.select2-selection').css('border', '');
        jQuery("#filterTableBody").find('.value').css('border', '');
        jQuery("#filterTableBody").find('.category').siblings('.select2').find('.select2-selection').css('border', '');

        jQuery("#filterTableBody tr.filterRow").each(function(index) {
            let $row = jQuery(this);
            let attr = $row.find('.product').val();
            let condition = $row.find('.condition').val();

            // Check for both input (.value) and select (.category) field types
            let valueInput = $row.find('.value').val();
            let valueSelect = $row.find('.category').val();
            let value = valueInput || valueSelect; // Use whichever has a value

            let rowHasError = false;

            if (attr === "0") {
                $row.find('.product').siblings('.select2').find('.select2-selection').css('border', '1px solid #ef1717');
                rowHasError = true;
            }
            if (condition === "0") {
                $row.find('.condition').siblings('.select2').find('.select2-selection').css('border', '1px solid #ef1717');
                rowHasError = true;
            }
            if (value === "0" || !value) {
                // Apply error styling to both possible field types
                $row.find('.value').css('border', '1px solid #ef1717');
                $row.find('.category').siblings('.select2').find('.select2-selection').css('border', '1px solid #ef1717');
                rowHasError = true;
            }

            if (rowHasError) {
                filterValidation = true;
            }
        });

        if (filterValidation) {
            errorMessages.push('• Complete all filter fields (Product, Condition, and Value)');
            hasError = true;
        }

        if (hasError || filterValidation) {
            let errorDiv = jQuery('#validationErrors');
            if (errorDiv.length === 0) {
                errorDiv = jQuery('<div id="validationErrors" class="alert alert-danger" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 500px;"></div>');
                jQuery('body').append(errorDiv);
            }

            errorDiv.html('<strong>Please complete the following:</strong><br>' + errorMessages.join('<br>'));
            errorDiv.show();

            setTimeout(function() {
                errorDiv.fadeOut();
            }, 5000);

            // RE-ENABLE BUTTONS when validation fails
            jQuery("#goBackToCategory, .addButton, #filterReset, #filterSubmit").prop("disabled", false);

            jQuery('[data-bs-target="#pills-map-filters"]').trigger('click');
            return false; // stop execution if invalid
        }

        // ====== PROCESS FILTERS ONLY IF VALID ======
        let flag = 0;
        let valFlag = 0;
        let prodData = [];
        let conditionData = [];
        let valueData = [];

        jQuery('#addFiltersCard').empty();

        jQuery.each(product, function(i, val) {
            if (val != "0" && condition[i] != "0") {
                let currentValue = '';
                let currentValueText = '';

                if (val === 'product_cat' || val === '_stock_status' || val === 'main_image') {
                    currentValue = selVal[flag];
                    currentValueText = seltext[flag];
                    flag++;
                } else {
                    currentValue = value[valFlag];
                    currentValueText = value[valFlag];
                    valFlag++;
                }

                if (currentValue != "" && currentValue != "0") {
                    prodData[i] = val;
                    conditionData[i] = condition[i];
                    valueData[i] = currentValue;

                    let newCard = '<div class="btn-group border rounded mt-1 me-1 removecardThis" >' +
                        '<button class="btn btn-light btn-sm text-secondary fs-12 ps-1 pe-1 pt-0 pb-0" type="button" value="' + i + '">' +
                        producttext[i] + ' <b>' + condition[i] + '</b> ' + currentValueText + '</button>' +
                        '<button type="button" class="btn btn-sm btn-grey onhover-close pt-0 pb-0" style="cursor: pointer;">' +
                        '<span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">close</span></button></div>';

                    jQuery('#addFiltersCard').append(newCard);
                }
            }
        });

        jQuery('#strProData').val(prodData.filter(Boolean).join(','));
        jQuery('#strConditionData').val(conditionData.filter(Boolean).join(','));
        jQuery('#strValueData').val(valueData.filter(Boolean).join(','));

        jQuery('#excludeProductFromSync').val('');
        jQuery('#includeProductFromSync').val('');
        jQuery('#selectAllunchecked').val('');
        jQuery('#includeExtraProductForFeed').val('');
        jQuery('#allFilters').empty();
        // jQuery("#filterForm")[0].reset();
        jQuery(".product").select2('val', '0');
        jQuery('#filterDelete').addClass('disabled');
        jQuery('#filterModal').modal('hide');

        if (typeof table !== 'undefined') {
            table.draw();
        }

        save_feed_data();
    });


    /*********************Add Filter Show Start***********************************************************************/
    jQuery('.addFilter').on('click', function(events) {
        let attr = jQuery('#strProData').val();
        let condition = jQuery('#strConditionData').val();
        let value = jQuery('#strValueData').val();
        var a = 0;
        if (attr != '' && condition != '' && value != '') {
            let attrArry = attr.split(",");
            let conditionArry = condition.split(",");
            let valueArry = value.split(",");
            jQuery('#allFilters').empty()
            jQuery.each(attrArry, function(i, value) {
                if (a == 0) {
                    a = 1;
                    jQuery('select[name="product[]"]').val(value).trigger('change');
                    jQuery('select[name="condition[]"]').val(conditionArry[i]).trigger('change');
                    if (value === 'product_cat' || value === '_stock_status' || value === 'main_image') {
                        jQuery('select[name="value[]"]').val(valueArry[i]).trigger('change');
                    } else {
                        jQuery('input[name="value[]"]').val(valueArry[i]);
                    }

                } else {
                    var conditionDropDown = getConditionDropDown(value, conditionArry[i]);
                    if (value === 'product_cat') {
                        var category = <?php echo json_encode($category); ?>;
                        let option = '<option value="0">Select Category</option>';
                        jQuery.each(category, function(key, values) {
                            option += '<option value="' + key + '" ' + ((key == valueArry[i]) ? "selected" : "") + '>' + values + '</option>';
                        });
                        var html = '<select class="select2" name="value[]">' +
                            option +
                            '</select>';
                    } else if (value === '_stock_status') {
                        let option = '<option value="0">Select Stock Status</option>' +
                            '<option value="instock" ' + (("instock" == valueArry[i]) ? "selected" : "") + '>In Stock</option>' +
                            '<option value="outofstock" ' + (("outofstock" == valueArry[i]) ? "selected" : "") + '>Out Of Stock</option>';
                        html = '<select class="select2" name="value[]">' + option + '</select>';
                    } else if (value === 'main_image') {
                        let option = '<option value="0">Select Image State</option>' +
                            '<option value="EXISTS" ' + (("EXISTS" == valueArry[i]) ? "selected" : "") + '>Not Empty</option>';
                        html = '<select class="select2" name="value[]">' + option + '</select>';
                    } else {
                        var html = '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" value="' + valueArry[i] + '">';
                    }
                }
            });
        }
        jQuery('.product, .condition, .category').select2({
            dropdownParent: jQuery("#pills-map-filters"),
            width: 'resolve'
        });
    });
    /*********************Add Filter Show End************************************************************************/

    /**************** get dependent dropdown product change start******************************************************/
    jQuery(document).on('change', '.product', function(event) {
        var changeValue = jQuery(this).val();
        var $row = jQuery(this).closest('.filterRow, tr'); // Support both div and table row structure

        // Find condition div - could be next sibling or specific container
        var $conditionContainer = $row.find('.conditionDiv');
        if ($conditionContainer.length === 0) {
            $conditionContainer = jQuery(this).parent().parent().children('div').eq(1);
        }

        // Find value container
        var $valueContainer = $row.find('.textValue');
        if ($valueContainer.length === 0) {
            $valueContainer = jQuery(this).parent().parent().children('.textValue');
        }

        $conditionContainer.empty();
        var conditionDropDown = getConditionDropDown(changeValue);
        $conditionContainer.append(conditionDropDown);

        if (changeValue === 'product_cat') {
            var category = <?php echo json_encode($category); ?>;
            let option = '<option value="0">Select Category</option>';
            jQuery.each(category, function(key, value) {
                option += '<option value="' + key + '">' + value + '</option>';
            });
            $valueContainer.empty();
            var html = '<select class="category" name="value[]" style="width:100%">' +
                option +
                '</select>';
            $valueContainer.append(html);
        } else if (changeValue === '_stock_status') {
            $valueContainer.empty();
            var html = '<select class="category" name="value[]" style="width:100%">' +
                '<option value="0">Select Stock Status</option>' +
                '<option value="instock">In Stock</option>' +
                '<option value="outofstock">Out Of Stock</option>' +
                '</select>';
            $valueContainer.append(html);
        } else if (changeValue === 'main_image') {
            $valueContainer.empty();
            var html = '<select class="category" name="value[]" style="width:100%">' +
                '<option value="0">Select Image State</option>' +
                '<option value="EXISTS">Not Empty</option>' +
                '</select>';
            $valueContainer.append(html);
        } else {
            $valueContainer.empty();
            var html = '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" >';
            $valueContainer.append(html);
        }

        // Reinitialize select2
        jQuery('.product, .condition, .category').select2({
            dropdownParent: jQuery("#pills-map-filters"),
            width: 'resolve'
        });
    });
    /**************** get dependent dropdown product change end**********************************************************/

    /**************** Add more filter Start**************************************************************************/
    jQuery(document).on("click", ".addButton", function(event) {
        var newRow = '<tr class="filterRow">' +
            '<td>' +
            '<select class="form-select product" name="product[]" style="width:100%">' +
            '<option value="0">Select Attribute</option>' +
            '<option value="product_cat">Category</option>' +
            '<option value="ID">Product Id</option>' +
            '<option value="_stock_status">Stock Status</option>' +
            '<option value="main_image">Main Image</option>' +
            '</select>' +
            '</td>' +
            '<td class="conditionDiv">' +
            '<select class="form-select condition" name="condition[]" style="width:100%" >' +
            '<option value="0">Select Conditions</option>' +
            '</select>' +
            '</td>' +
            '<td class="textValue">' +
            '<input type="text" class="form-control value" placeholder="Add value" name="value[]">' +
            '</td>' +
            '<td class="text-center align-middle">' +
            '<span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>' +
            '</td>' +
            '</tr>';
        jQuery('#filterTableBody').append(newRow);

        jQuery('.condition').select2({
            dropdownParent: jQuery("#pills-map-filters")
        });
        jQuery('.product').select2({
            dropdownParent: jQuery("#pills-map-filters")
        });
    });
    /**************** Add more filter End******************************************************************************/

    /**************** Reset Modal filter Start ***************************************/
    jQuery(document).on('click', '#filterReset', function(event) {
        jQuery('#allFilters').empty();
        jQuery('#filterTableBody').empty(); // Also clear table body if it exists
        jQuery("#filterForm")[0].reset();
        jQuery(".product").select2('val', '0');
        jQuery('.product').select2({
            dropdownParent: jQuery("#pills-map-filters")
        });
        jQuery('.condition').select2({
            dropdownParent: jQuery("#pills-map-filters")
        });
    });
    /**************** Reset Modal filter End ******************************************/

    /**************** Delete Add more filed column start*****************************************************************/
    jQuery("body").on("click", ".deleteButton", function() {
        jQuery(this).closest(".filterRow").remove();
    });

    /**************** Remove filter card start*****************************************************************/
    jQuery("body").on("click", ".removecard", function() {
        jQuery(this).closest(".removecardThis").remove();

        // Update hidden fields after removing a card
        var remainingCards = [];
        var remainingConditions = [];
        var remainingValues = [];

        jQuery('.removecardThis').each(function() {
            var buttonValue = jQuery(this).find('button').first().attr('value');
            // You would need to track and rebuild the arrays here
            // This is a simplified version - you may need to enhance this
        });
    });
    /**************** Remove filter card end*******************************************************************/
    jQuery(document).ready(function($) {
        jQuery(document).on('click', '.copy-down', function() {
            let row = jQuery(this).closest('.row');
            let currentSelect = row.find('select.categorySelect');

            if (currentSelect.length) {
                let selectedVal = currentSelect.val();
                let selectedText = currentSelect.find("option:selected").text();

                if (!selectedVal || selectedVal === "0") {
                    alert("Please select a valid category before copying!");
                    return;
                }

                // Loop through all rows below and copy value
                row.nextAll('.row').find('select.categorySelect').each(function() {
                    let targetSelect = jQuery(this);

                    // If target select doesn’t have this value, append it
                    if (targetSelect.find("option[value='" + selectedVal + "']").length === 0) {
                        targetSelect.append(
                            jQuery("<option>", {
                                value: selectedVal,
                                text: selectedText
                            })
                        );
                    }

                    // Set the value and trigger change (important for Select2 UI)
                    targetSelect.val(selectedVal).trigger('change');
                });

				alert("Category '" + selectedText + "' copied to all below rows!");
            }
        });

		// Copy mapping only to descendant child rows of this parent category
		jQuery(document).on('click', '.copy-children', function() {
			let row = jQuery(this).closest('.catTermId');
			function getDepth($row) {
				let d = parseInt(String($row.attr('data-depth') || '').trim(), 10);
				if (!isNaN(d)) { return d; }
				let labelText = $row.find('.shop-category label').text() || '';
				let matches = (labelText.match(/—/g) || []).length; // count em-dash
				return matches;
			}
			let parentDepth = getDepth(row);
			let currentSelect = row.find('select.categorySelect');

			if (currentSelect.length) {
				let selectedVal = currentSelect.val();
				let selectedText = currentSelect.find("option:selected").text();

				if (!selectedVal || selectedVal === "0") {
					alert("Please select a valid category before copying!");
					return;
				}

				let copiedCount = 0;
				row.nextAll().each(function() {
					let $el = jQuery(this);
					if (!$el.hasClass('catTermId')) {
						return; // skip non-category rows
					}
					let depth = getDepth($el);
					if (depth <= parentDepth) {
						return false; // reached sibling or higher level; stop
					}
					let targetSelect = $el.find('select.categorySelect');
					if (targetSelect.length) {
						if (targetSelect.find("option[value='" + selectedVal + "']").length === 0) {
							targetSelect.append(jQuery("<option>", { value: selectedVal, text: selectedText }));
						}
						targetSelect.val(selectedVal).trigger('change');
						copiedCount++;
					}
				});

				if (copiedCount > 0) {
					alert("Category '" + selectedText + "' copied to " + copiedCount + " child row(s)!");
				} else {
					alert("No child rows found to copy to.");
				}
			}
		});
    });
    var target = document.getElementById('filterError');

    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (jQuery('#filterError').is(':visible')) {
                jQuery("#goBackToCategory, .addButton, #filterReset, #filterSubmit").prop("disabled", false);
            }
        });
    });

    observer.observe(target, {
        childList: true,
        subtree: true
    });
</script>