<?php

/**
 * TVC Ajax File Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 */
if (!defined('ABSPATH')) {
  exit;
}


if (!class_exists('TVC_Ajax_File')) :
  /**
   * Ajax File Class
   */
  class TVC_Ajax_File extends TVC_Ajax_Calls
  {
    private $apiDomain;
    public function __construct()
    {
      parent::__construct();
      $this->apiDomain = TVC_API_CALL_URL;
      // hooks
      add_action('wp_ajax_tvc_call_domain_claim', array($this, 'tvc_call_domain_claim'));
      add_action('wp_ajax_tvc_call_site_verified', array($this, 'tvc_call_site_verified'));
      add_action('wp_ajax_conv_get_microsoft_ads_conversion', array($this, 'conv_get_microsoft_ads_conversion'));
      add_action('wp_ajax_tvc_call_add_survey', array($this, 'tvc_call_add_survey'));
      add_action('wp_ajax_conv_save_pixel_data', array($this, 'conv_save_pixel_data'));
      add_action('wp_ajax_save_feed_data', [$this, 'save_feed_data']);
      add_action('wp_ajax_get_feed_data_by_id', [$this, 'get_feed_data_by_id']);
      add_action('wp_ajax_ee_duplicate_feed_data_by_id', [$this, 'ee_duplicate_feed_data_by_id']);
      add_action('wp_ajax_ee_get_product_details_for_table', [$this, 'ee_get_product_details_for_table']);
      add_action('wp_ajax_ee_delete_feed_data_by_id', [$this, 'ee_delete_feed_data_by_id']);
      add_action('wp_ajax_ee_delete_feed_gmc', [$this, 'ee_delete_feed_gmc']);
      add_action('wp_ajax_ee_get_product_status', [$this, 'ee_get_product_status']);
      add_action('wp_ajax_ee_feed_wise_product_sync_batch_wise', [$this, 'ee_feed_wise_product_sync_batch_wise']);
      add_action('init_feed_wise_product_sync_process_scheduler_ee', [$this, 'ee_call_start_feed_wise_product_sync_process']);
      add_action('auto_feed_wise_product_sync_process_scheduler_ee', [$this, 'ee_call_auto_feed_wise_product_sync_process']);
      add_action('wp_ajax_get_tiktok_business_account', [$this, 'get_tiktok_business_account']);
      add_action('wp_ajax_get_tiktok_user_catalogs', [$this, 'get_tiktok_user_catalogs']);
      add_action('wp_ajax_ee_getCatalogId', [$this, 'ee_getCatalogId']);
      add_action('wp_ajax_conv_create_microsoft_ads_conversion', [$this, 'conv_create_microsoft_ads_conversion']);
      add_action('wp_ajax_conv_save_microsoft_ads_conversion', [$this, 'savemicrosoftadsconversions']);
      add_action('wp_ajax_get_fb_catalog_data', array($this, 'get_fb_catalog_data'));
      add_action('wp_ajax_get_analytics_account_list', array($this, 'get_analytics_account_list'));
      add_action('wp_ajax_get_analytics_web_properties', array($this, 'get_analytics_web_properties'));
      add_action('wp_ajax_list_google_merchant_account', array($this, 'list_google_merchant_account'));
      add_action('wp_ajax_list_microsoft_merchant_account', array($this, 'list_microsoft_merchant_account'));
      add_action('wp_ajax_list_microsoft_catalog_account', array($this, 'list_microsoft_catalog_account'));
      add_action('wp_ajax_create_google_merchant_center_account', array($this, 'create_google_merchant_center_account'));
      add_action('wp_ajax_create_microsoft_merchant_center_account', array($this, 'create_microsoft_merchant_center_account'));
      add_action('wp_ajax_save_merchant_data', array($this, 'save_merchant_data'));
      add_action('wp_ajax_list_microsoft_ads_account', array($this, 'list_microsoft_ads_account'));
      add_action('wp_ajax_list_microsoft_ads_subaccount', array($this, 'list_microsoft_ads_subaccount'));
      add_action('wp_ajax_list_microsoft_ads_get_UET_tag', array($this, 'list_microsoft_ads_get_UET_tag'));
      add_action('wp_ajax_create_microsoft_ads_UET_tag', array($this, 'create_microsoft_ads_UET_tag'));
      add_action('wp_ajax_conv_create_bing_account', array($this, 'conv_create_bing_account'));
      add_action('wp_ajax_set_email_configurationGA4', array($this, 'set_email_configurationGA4'));
      add_action('wp_ajax_get_ga4_general_grid_reports', array($this, 'get_ga4_general_grid_reports'));
      add_action('wp_ajax_get_ga4_page_report', array($this, 'get_ga4_page_report'));
      add_action('wp_ajax_get_general_donut_reports', array($this, 'get_general_donut_reports'));
      add_action('wp_ajax_get_realtime_report', array($this, 'get_realtime_report'));
      add_action('wp_ajax_get_general_audience_report', array($this, 'get_general_audience_report'));
      add_action('wp_ajax_get_daily_visitors_report', array($this, 'get_daily_visitors_report'));
      add_action('wp_ajax_get_demographic_ga4_reports', array($this, 'get_demographic_ga4_reports'));
      add_action('wp_ajax_conv_create_ga4_custom_dimension', array($this, 'conv_create_ga4_custom_dimension'));
    }

    // Save data in ee_options
    public function conv_save_data_eeoption($data)
    {
      $ee_options = unserialize(get_option('ee_options'));
      foreach ($data['conv_options_data'] as $key => $conv_options_data) {
        if ($key == "conv_selected_events") {
          continue;
        }
        $key_name = $key;
        $key_name_arr = array();
        $key_name_arr["measurement_id"] = "gm_id";
        $key_name_arr["property_id"] = "ga_id";
        if (key_exists($key_name, $key_name_arr)) {
          $ee_options[$key_name_arr[$key_name]] = sanitize_text_field($conv_options_data);
        } else {

          if (is_array($conv_options_data)) {
            $posted_arr = $conv_options_data;
            $posted_arr_temp = [];
            if (!empty($posted_arr)) {
              $arr = $posted_arr;
              array_walk($arr, function (&$value) {
                $value = sanitize_text_field($value);
              });
              $posted_arr_temp = $arr;
              $ee_options[$key_name] = $posted_arr_temp;
            }
          } else {
            $ee_options[$key_name] = sanitize_text_field($conv_options_data);
          }
        }
      }
      update_option('ee_options', serialize($ee_options));
      //echo '<pre>'; print_r(unserialize(get_option('ee_options'))); echo '</pre>'; exit('ohh');
    }

    // Save data in ee_options
    public function conv_save_data_eeapidata($data)
    {
      $eeapidata = unserialize(get_option('ee_api_data'));
      $eeapidata_settings = $eeapidata['setting'];
      if (empty($eeapidata_settings)) {
        $eeapidata_settings = new stdClass();
      }

      foreach ($data['conv_options_data'] as $key => $conv_options_data) {
        if ($key == "conv_selected_events") {
          continue;
        }

        $key_name = $key;

        if (is_array($conv_options_data)) {
          $posted_arr = $conv_options_data;
          $posted_arr_temp = [];
          if (!empty($posted_arr)) {
            $arr = $posted_arr;
            array_walk($arr, function (&$value) {
              $value = sanitize_text_field($value);
            });
            $posted_arr_temp = $arr;
            $eeapidata_settings->$key_name = $posted_arr_temp;
          }
        } else {
          $eeapidata_settings->$key_name = sanitize_text_field($conv_options_data);
          if ($key_name == "google_merchant_center_id") {
            $eeapidata_settings->google_merchant_id = sanitize_text_field($conv_options_data);
          }
        }
      }
      $eeapidata['setting'] = $eeapidata_settings;
      update_option('ee_api_data', serialize($eeapidata));
    }

    //Save data in middleware
    public function conv_save_data_middleware($postDataFull = array())
    {
      $postData = $postDataFull['conv_options_data'];
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $google_detail = $TVC_Admin_Helper->get_ee_options_data();
      try {
        $url = $this->apiDomain . '/customer-subscriptions/update-detail';
        $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
        $data = array();
        foreach ($postData as $key => $value) {
          $data[$key] = sanitize_text_field((isset($value)) ? $value : '');
        }
        $data['store_id'] = $google_detail['setting']->store_id;
        $data["subscription_id"] = $google_detail['setting']->id;
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = wp_remote_request(esc_url_raw($url), $args);
      } catch (Exception $e) {
        return $e->getMessage();
      }
    }

    // Save data in ee_convnotices
    public function conv_save_eeconvnotice($data)
    {
      $ee_eeconvnotice = get_option('ee_convnotice', array());
      $keyname = sanitize_text_field($data['conv_options_data']);
      $ee_eeconvnotice[$keyname] = "yes";
      update_option('ee_convnotice', $ee_eeconvnotice);
    }


    // All new functions for new UIUX
    public function conv_save_pixel_data()
    {
      if (
        isset($_POST['pix_sav_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])), 'pix_sav_nonce_val')
      ) {
        $post = array(
          "conv_options_data" => "",
          "conv_options_type" => "",
          "conv_tvc_data" => "",
          "update_site_domain" => "",
          "customer_subscription_id" => "",
          "conv_catalogData" => "",
        );
        $post = array_intersect_key($_POST, $post);

        $TVC_Admin_Helper = new TVC_Admin_Helper();
        if (isset($_POST['conv_options_type']) && in_array("eeoptions", $_POST['conv_options_type'])) {
          $this->conv_save_data_eeoption($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("middleware", $_POST['conv_options_type'])) {
          $this->conv_save_data_middleware($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("eeapidata", $_POST['conv_options_type'])) {
          $this->conv_save_data_eeapidata($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("eeapidata", $_POST['conv_options_type'])) {
          if (isset($_POST['update_site_domain']) && $_POST['update_site_domain'] === 'update') {
            $post['conv_options_data']['is_site_verified'] = '0';
            $post['conv_options_data']['is_domain_claim'] = '0';
          }
          $this->conv_save_data_eeapidata($post);
        }
        if (isset($_POST['conv_options_data']['ga_GMC']) && $_POST['conv_options_data']['ga_GMC'] == '1' && isset($_POST['conv_options_data']['merchant_id'])) {
          $api_obj = new CustomApi();
          $postData = [
            'subscription_id' => isset($_POST['conv_options_data']['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['subscription_id'])) : '',
            'merchant_id' => isset($_POST['conv_options_data']['merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['merchant_id'])) : '',
            'account_id' => isset($_POST['conv_options_data']['google_merchant_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_merchant_id'])) : '',
            'adwords_id' => isset($_POST['conv_options_data']['google_ads_id']) ? sanitize_text_field(wp_unslash($_POST['conv_options_data']['google_ads_id'])) : '',
            'caller' => "conv_save_pixel_data"
          ];
          $api_obj->linkGoogleAdsToMerchantCenter($postData);
        }
        if (in_array("eeselectedevents", $_POST['conv_options_type']) && isset($_POST["conv_options_data"]["conv_selected_events"]['ga'])) {
          $selectedevents = is_array($_POST["conv_options_data"]["conv_selected_events"]['ga']) ? array_map('sanitize_text_field', wp_unslash($_POST["conv_options_data"]["conv_selected_events"]['ga'])) : sanitize_text_field(wp_unslash($_POST["conv_options_data"]["conv_selected_events"]['ga']));
          $selectedevents['ga'] = $selectedevents;
          update_option("conv_selected_events", serialize($selectedevents));
        }
        if (isset($_POST['conv_options_type']) && in_array("tiktokmiddleware", $_POST['conv_options_type'])) {
          $this->conv_save_tiktokmiddleware($post);
        }
        if (isset($_POST['conv_options_type']) && in_array("tiktokcatalog", $_POST['conv_options_type'])) {
          $this->conv_save_tiktokcatalog($post);
        }

        if (isset($_POST['conv_options_type']) && in_array("facebookmiddleware", $_POST['conv_options_type'])) {
          $this->conv_save_facebookmiddleware($_POST);
        }
        if (isset($_POST['conv_options_type']) && in_array("facebookcatalog", $_POST['conv_options_type'])) {
          $this->conv_save_facebookcatalog($_POST);
        }

        if (!array_key_exists("conv_onboarding_done_step", $_POST['conv_options_data'])) {
          if (
            isset($_POST['conv_options_data']) &&
            (
              array_key_exists("microsoft_ads_manager_id", $_POST['conv_options_data'])
              || array_key_exists("microsoft_ads_subaccount_id", $_POST['conv_options_data'])
              || array_key_exists("microsoft_ads_pixel_id", $_POST['conv_options_data'])
              || array_key_exists("microsoft_merchant_center_id", $_POST['conv_options_data'])
              || array_key_exists("ms_catalog_id", $_POST['conv_options_data'])
            )
            &&
            (
              !empty($_POST['conv_options_data']['microsoft_ads_manager_id'])
              || !empty($_POST['conv_options_data']['microsoft_ads_subaccount_id'])
              || !empty($_POST['conv_options_data']['microsoft_ads_pixel_id'])
              || !empty($_POST['conv_options_data']['microsoft_merchant_center_id'])
              || !empty($_POST['conv_options_data']['ms_catalog_id'])
            )
          ) {
            $this->conv_save_microsoft($_POST['conv_options_data']);
          }
        }


        if (isset($_POST['conv_options_type']) && in_array("eeconvnotice", $_POST['conv_options_type'])) {
          $this->conv_save_eeconvnotice($post);
        }
        $caller = "conv_save_pixel_data";
        $TVC_Admin_Helper->update_app_status($caller);
        $TVC_Admin_Helper->update_subscription_details_api_to_db();
        echo "1";
      } else {
        echo "0";
      }
      exit;
    }
    // All new functions for new UIUX End

    public function conv_create_ga4_custom_dimension()
    {
      $nonce = isset($_POST['pix_sav_nonce']) ? sanitize_text_field(wp_unslash($_POST['pix_sav_nonce'])) : '';

      if (!wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        wp_send_json_error(['message' => 'Invalid nonce.']);
      }

      $api_obj = new CustomApi();
      $caller = "conv_create_ga4_custom_dimension";
      $api_obj->gaDimension($caller);
      if (!empty($_POST['non_woo_tracking']) && $_POST['non_woo_tracking'] == '1') {
        $non_woo_data = array(
          'conv_track_page_scroll' => '1',
          'conv_track_file_download' => '1',
          'conv_track_author' => '1',
          'conv_track_signup' => '1',
          'conv_track_signin' => '1',
        );
        $data = get_option('ee_options');
        $data = $data ? maybe_unserialize($data) : array();
        $updated_data = array_merge($data, $non_woo_data);
        update_option('ee_options', maybe_serialize($updated_data));
      }
      $additional_data = array(
        'conv_track_page_scroll' => '1',
        'conv_track_file_download' => '1',
        'conv_track_author' => '1',
        'conv_track_signin' => '1',
        'conv_track_signup' => '1',
        'caller' => 'conv_create_ga4_custom_dimension',
      );
      $api_obj->additional_dimensions($additional_data);
      wp_send_json_success(['message' => 'GA4 Custom Dimension created successfully.']);
    }

    public function tvc_call_add_survey()
    {
      if (isset($_POST['tvc_call_add_survey']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tvc_call_add_survey'])), 'tvc_call_add_survey-nonce')) {
        if (!class_exists('CustomApi')) {
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
        }
        $customObj = new CustomApi();
        unset($_POST['action']);
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : "";
        $radio_option_val = isset($_POST['radio_option_val']) ? sanitize_text_field(wp_unslash($_POST['radio_option_val'])) : "";
        $other_reason = isset($_POST['other_reason']) ? sanitize_text_field(wp_unslash($_POST['other_reason'])) : "";
        $site_url = isset($_POST['site_url']) ? sanitize_text_field(wp_unslash($_POST['site_url'])) : "";
        $plugin_name = isset($_POST['plugin_name']) ? sanitize_text_field(wp_unslash($_POST['plugin_name'])) : "";

        $post = array(
          "customer_id" => $customer_id,
          "subscription_id" => $subscription_id,
          "radio_option_val" => $radio_option_val,
          "other_reason" => $other_reason,
          "site_url" => $site_url,
          "plugin_name" => $plugin_name
        );
        $caller = "tvc_call_add_survey";
        echo wp_json_encode($customObj->add_survey_of_deactivate_plugin($caller, $post));
      } else {
        echo wp_json_encode(array('error' => true, "is_connect" => false, 'message' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      // IMPORTANT: don't forget to exit
      exit;
    }

    public function conv_get_microsoft_ads_conversion()
    {
      $nonce = isset($_POST['TVCNonce']) ? filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW) : '';

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        $tag_id = isset($_POST['tag_id']) ? sanitize_text_field(wp_unslash($_POST['tag_id'])) : '';
        $caller = "get_microsoft_ads_conversion";
        if ($customer_id != "") {
          $response = $customApiObj->get_microsoft_conversion_list($caller, $customer_id, $account_id, $tag_id);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              echo wp_json_encode($response);
              exit;
            }
          }
        }
      }
      // IMPORTANT: don't forget to exit
      wp_die(0);
    }

    public function conv_create_microsoft_ads_conversion()
    {
      $nonce = filter_input(INPUT_POST, 'TVCNonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'con_get_conversion_list-nonce')) {        //$TVC_Admin_Helper = new TVC_Admin_Helper();
        $customApiObj = new CustomApi();
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '';
        $tag_id = isset($_POST['tag_id']) ? sanitize_text_field(wp_unslash($_POST['tag_id'])) : '';
        $conversionCategory = isset($_POST['conversionCategory']) ? sanitize_text_field(wp_unslash($_POST['conversionCategory'])) : '';
        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $action_value = isset($_POST['action_value']) ? sanitize_text_field(wp_unslash($_POST['action_value'])) : '';
        $caller = "create_microsoft_ads_conversion";
        if ($customer_id != "") {
          $response = $customApiObj->conv_create_microsoft_ads_conversion($caller, $customer_id, $account_id, $tag_id, $conversionCategory, $name, $action_value);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              echo wp_json_encode($response);
              exit;
            }
          }
        }
      }
    }
    public function savemicrosoftadsconversions()
    {
      $nonce = filter_input(INPUT_POST, 'CONVNonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'conv_save_microsoft_ads_conversion-nonce')) {
        $ee_options = unserialize(get_option('ee_options'));
        if (isset($_POST['clearmicrosoftadsconversions']) && sanitize_text_field(wp_unslash($_POST['clearmicrosoftadsconversions'])) == "yes") {
          unset($ee_options["microsoft_ads_conversions"]);
          update_option('ee_options', serialize($ee_options));
          $microsoft_ads_conversions_tracking = 0;
          update_option('microsoft_ads_conversions_tracking', sanitize_text_field($microsoft_ads_conversions_tracking));
          $googleDetail_setting["microsoft_ads_conversions_tracking"] = sanitize_text_field($microsoft_ads_conversions_tracking);
        } else {
          $ee_options_microsoft_ads_conversions = $ee_options["microsoft_ads_conversions"] ?? [];

          if (!empty($_POST['category'])) {
            $categories = json_decode(stripslashes($_POST['category']), true);
            if (is_array($categories)) {
              foreach ($categories as $category => $value) {
                $cleanCategory = sanitize_text_field(wp_unslash($category));
                $ee_options_microsoft_ads_conversions[$cleanCategory] = "1";
              }
            }
          }

          $ee_options["microsoft_ads_conversions"] = $ee_options_microsoft_ads_conversions;
          update_option('ee_options', serialize($ee_options));

          if (!empty($ee_options_microsoft_ads_conversions)) {
            update_option('microsoft_ads_conversions_tracking', "1");
          }
        }
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $caller = "save_microsoft_ads_conversions";
        $TVC_Admin_Helper->update_app_status($caller);
        die('1');
      } else {
        die('Security nonce not matched');
      }
    }

    public function tvc_call_site_verified()
    {
      $nonce = filter_input(INPUT_POST, 'SiteVerifiedNonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'tvc_call_site_verified-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $tvc_rs = [];
        $merchant_id = isset($_POST['merchant_id']) ? sanitize_text_field($_POST['merchant_id']) : '';
        $account_id  = isset($_POST['account_id']) ? sanitize_text_field($_POST['account_id']) : '';
        $tvc_rs = $TVC_Admin_Helper->call_site_verified($merchant_id, $account_id);
        if (isset($tvc_rs['error']) && $tvc_rs['error'] == 1) {
          echo wp_json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
        } else {
          echo wp_json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
        }
        exit;
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        exit;
      }
    }
    public function tvc_call_domain_claim()
    {
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'apiDomainClaimNonce', FILTER_UNSAFE_RAW), 'tvc_call_domain_claim-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $merchant_id = isset($_POST['merchant_id']) ? sanitize_text_field($_POST['merchant_id']) : '';
        $account_id  = isset($_POST['account_id']) ? sanitize_text_field($_POST['account_id']) : '';
        $tvc_rs = $TVC_Admin_Helper->call_domain_claim($merchant_id, $account_id);
        if (isset($tvc_rs['error']) && $tvc_rs['error'] == 1) {
          echo wp_json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
        } else {
          echo wp_json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
        }
        exit;
      } else {
        echo wp_json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        exit;
      }
    }

    /**
     * function to get Product status by feed_id
     * Hook used wp_ajax_ee_get_product_status
     * Request Post
     * API call to get product status
     */
    public function ee_get_product_status()
    {
      $nonce = filter_input(INPUT_POST, 'conv_licence_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_licence-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        $merchantId = $TVC_Admin_Helper->get_merchantId();
        $data = array(
          "store_id" => $google_detail['setting']->store_id,
          "subscription_id" => $google_detail['setting']->id,
          "store_feed_id" => isset($_POST['feed_id']) ? sanitize_text_field(wp_unslash($_POST['feed_id'])) : '',
          "product_ids" => isset($_POST['product_list']) ? sanitize_text_field(wp_unslash($_POST['product_list'])) : '',
          "channel" => isset($_POST['channel_id']) ? sanitize_text_field(wp_unslash($_POST['channel_id'])) : '',
          "merchant_id" => $google_detail['setting']->google_merchant_id,
          "catalog_id" => isset($_POST['catalog_id']) ? sanitize_text_field(wp_unslash($_POST['catalog_id'])) : '',
          "tiktok_business_id" => isset($_POST['tiktok_business_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_business_id'])) : '',
          "tiktok_catalog_id" => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
          "ms_store_id" => isset($_POST['ms_store_id']) ? sanitize_text_field(wp_unslash($_POST['ms_store_id'])) : '',
          "ms_catalog_id" => isset($_POST['ms_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['ms_catalog_id'])) : '',
          "caller" => "ee_get_product_status"
        );
        if (!isset($_POST['product_list']) || (isset($_POST['product_list']) && sanitize_text_field(wp_unslash($_POST['product_list']))) == '') {
          echo wp_json_encode('Product does not exists');
          exit;
        }
        $CustomApi = new CustomApi();
        // $response = $CustomApi->getProductStatusByFeedId($data); 
        $response = $CustomApi->getProductStatusByChannelId($data);
        if (isset($response->errors)) {
          echo wp_json_encode($response->errors = 'Product does not exists');
        } else {
          echo wp_json_encode(isset($response->data->products) ? $response->data->products : 'Product not synced');
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Save and Update Feed data
     * Hook used wp_ajax_save_feed_data
     * Request Post
     * DB used ee_product_feed
     * Schedule cron set_recurring_auto_sync_product_feed_wise on update for conditions
     */
    public function save_feed_data()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $productFilter = isset($_POST['productData']) && $_POST['productData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['productData']))) : '';
        $conditionFilter = isset($_POST['conditionData']) && $_POST['conditionData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionData']))) : '';
        $valueFilter = isset($_POST['valueData']) && $_POST['valueData'] != '' ? explode(',', sanitize_text_field(wp_unslash($_POST['valueData']))) : '';
        $filters = array();
        if (!empty($productFilter)) {
          foreach ($productFilter as $key => $val) {
            $filters[$key]['attr'] = sanitize_text_field($val);
            $filters[$key]['condition'] = sanitize_text_field($conditionFilter[$key]);
            $filters[$key]['value'] = sanitize_text_field($valueFilter[$key]);
          }
        }

        $cat_data = isset($_POST['cat_data']) ? $_POST['cat_data'] : "";
        $mappedCatsDB = [];
        parse_str($cat_data, $formArrayCat);
        if (!empty($formArrayCat)) {
          foreach ($formArrayCat as $key => $value) {
            $formArrayCat[$key] = $value;
          }
          foreach ($formArrayCat as $key => $value) {
            if (preg_match("/^category-name-/i", $key)) {
              if ($value != '') {
                $keyArray = explode("name-", $key);
                $mappedCatsDB[$keyArray[1]]['name'] = sanitize_text_field($value);
              }
              unset($formArrayCat[$key]);
            } else if (preg_match("/^category-/i", $key)) {
              if ($value != '' && $value > 0) {
                $keyArray = explode("-", $key);
                $mappedCats[$keyArray[1]] = $value;
                $mappedCatsDB[$keyArray[1]]['id'] = sanitize_text_field($value);
              }
              unset($formArrayCat[$key]);
            }
          }
          update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
        }

        $attr_data = isset($_POST['attr_data']) ? $_POST['attr_data'] : "";
        parse_str($attr_data, $formArrayAttr);
        if (!empty($formArrayAttr)) {
          foreach ($formArrayAttr as $key => $value) {
            if ($key == 'additional_attr_') {
              $additional_attr = $value;
              unset($formArrayAttr['additional_attr_']);
            }
            if ($key == 'additional_attr_value_') {
              $additional_attr_value = $value;
              unset($formArrayAttr['additional_attr_value_']);
            }
            if (is_array($value) !== 1) {
              $formArrayAttr[$key] = sanitize_text_field($value);
            }
          }
          unset($formArrayAttr['additional_attr_']);
          unset($formArrayAttr['additional_attr_value_']);
          if (isset($additional_attr)) {
            foreach ($additional_attr as $key => $value) {
              $formArrayAttr[$value] = $additional_attr_value[$key];
            }
          }
          foreach ($formArrayAttr as $key => $value) {
            $mappedAttrs[$key] = sanitize_text_field($value);
          }

          //If additional_attr_value_ 
          unset($mappedAttrs['additional_attr_value_']);
          update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));
        }

        $channel_id = array();
        if (isset($_POST['google_merchant_center']) && $_POST['google_merchant_center'] == 1) {
          $channel_id['google_merchant_center'] = sanitize_text_field(wp_unslash($_POST['google_merchant_center']));
        }
        if (isset($_POST['tiktok_id']) && sanitize_text_field(wp_unslash($_POST['tiktok_id'])) == 3) {
          $channel_id['tiktok_id'] = sanitize_text_field(wp_unslash($_POST['tiktok_id']));
        }
        if (isset($_POST['fb_catalog_id']) && $_POST['fb_catalog_id'] == 2) {
          $channel_id['fb_catalog_id'] = sanitize_text_field(wp_unslash($_POST['fb_catalog_id']));
        }
        if (isset($_POST['microsoft_merchant_center']) && $_POST['microsoft_merchant_center'] == 4) {
          $channel_id['microsoft_merchant_center'] = sanitize_text_field(wp_unslash($_POST['microsoft_merchant_center']));
        }

        $channel_ids = implode(',', $channel_id);

        $tiktok_catalog_id = '';
        if (isset($_POST['tiktok_catalog_id']) === TRUE && $_POST['tiktok_catalog_id'] !== '') {
          $tiktok_catalog_id = sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id']));
        }
        /**
         * Check catalog id available
         */
        if (isset($_POST['tiktok_catalog_id']) === TRUE && sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) === 'Create New') {
          /**
           * Create catalog id
           */
          //$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries_currency.json");

          $wp_filesystem = TVC_Admin_Helper::get_filesystem();
          $getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries_currency.json");

          $contData = json_decode($getCountris);
          $currency_code = '';
          foreach ($contData as $key => $data) {
            if (isset($_POST['target_country']) && $data->countryCode === $_POST['target_country']) {
              $currency_code = $data->currencyCode;
            }
          }
          $customer['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer['business_id'] = isset($_POST['tiktok_business_account']) ? sanitize_text_field(wp_unslash($_POST['tiktok_business_account'])) : '';
          $customer['catalog_name'] = isset($_POST['feedName']) ? sanitize_text_field(wp_unslash($_POST['feedName'])) : '';
          $customer['region_code'] = isset($_POST['target_country']) ? sanitize_text_field(wp_unslash($_POST['target_country'])) : '';
          $customer['currency'] = sanitize_text_field($currency_code);
          $customer['caller'] = 'save_feed_data';
          $customObj = new CustomApi();
          $result = $customObj->createCatalogs($customer);
          if (isset($result->error_data) === TRUE) {
            foreach ($result->error_data as $key => $value) {
              echo wp_json_encode(array("error" => true, "message" => $value->errors[0], "errorType" => "tiktok"));
              exit;
            }
          }

          if (isset($result->status) === TRUE && $result->status === 200) {
            $tiktok_catalog_id = $result->data->catalog_id;
            $values = array();
            $place_holders = array();
            global $wpdb;
            $ee_tiktok_catalog = esc_sql($wpdb->prefix . "ee_tiktok_catalog");
            if (isset($_POST['target_country']) && isset($tiktok_catalog_id) && isset($_POST['feedName'])) {
              array_push($values, esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))), esc_sql($tiktok_catalog_id), esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))), gmdate('Y-m-d H:i:s', current_time('timestamp')));
            }
            $place_holders[] = "('%s', '%s', '%s','%s')";
            $query = "INSERT INTO `$ee_tiktok_catalog` (country, catalog_id, catalog_name, created_date) VALUES ";
            $query .= implode(', ', $place_holders);
            $wpdb->query($wpdb->prepare($query, $values));

            /***Store Catalog data Middleware *****/
            //$this->storeNewCatalogMiddleware();
          }
        }

        if (isset($_POST['edit']) && $_POST['edit'] != '') {
          $next_schedule_date = NULL;
          as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['edit']))));
          if (isset($_POST['autoSync']) && isset($_POST['is_mapping_update']) && $_POST['autoSync'] != 0 && $_POST['is_mapping_update'] == 1) {
            $last_sync_date = isset($_POST['last_sync_date']) ? sanitize_text_field(wp_unslash($_POST['last_sync_date'])) : '';
            $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . (isset($_POST['autoSyncIntvl']) ? absint(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : 0) . 'day', strtotime($last_sync_date)));
            // add scheduled cron job
            $autoSyncIntvl = isset($_POST['autoSyncIntvl']) ? absint(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : 0;
            $time_space = strtotime($autoSyncIntvl . " days", 0);
            $timestamp = strtotime($autoSyncIntvl . " days");
            as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['edit']))), "product_sync");
          }
          $profile_data = array(
            'feed_name' => isset($_POST['feedName']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))) : '',
            'channel_ids' => isset($channel_ids) ? esc_sql(sanitize_text_field($channel_ids)) : '',
            'auto_sync_interval' => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
            'auto_schedule' => isset($_POST['autoSync']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSync']))) : '',
            'updated_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
            'next_schedule_date' => $next_schedule_date,
            'target_country' => isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '',
            'tiktok_catalog_id' => isset($tiktok_catalog_id) ? esc_sql(sanitize_text_field($tiktok_catalog_id)) : '',
            'IncProductVar' => isset($_POST['IncProductVar']) ? intval($_POST['IncProductVar']) : 1,
            'IncDefProductVar' => isset($_POST['IncDefProductVar']) ? intval($_POST['IncDefProductVar']) : 0,
            'IncLowestPriceProductVar' => isset($_POST['IncLowestPriceProductVar']) ? intval($_POST['IncLowestPriceProductVar']) : 0,
            "filters" => wp_json_encode($filters),
            'categories' => wp_json_encode($mappedCatsDB),
            'attributes' => wp_json_encode($mappedAttrs)
          );

          if (isset($_POST['is_mapping_update']) && $_POST['is_mapping_update'] != 1) {
            $profile_data['status'] = strpos($channel_ids, '1') !== false ? esc_sql('Draft') : '';
            $profile_data['fb_status'] = strpos($channel_ids, '2') !== false ? esc_sql('Draft') : '';
            $profile_data['tiktok_status'] = strpos($channel_ids, '3') !== false ? esc_sql('Draft') : '';
            $profile_data['ms_status'] = strpos($channel_ids, '4') !== false ? esc_sql('Draft') : '';
          }
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field(wp_unslash($_POST['edit']))));
          $result = array(
            'id' => sanitize_text_field(wp_unslash($_POST['edit'])),
          );
          echo wp_json_encode($result);
        } else {
          $profile_data = array(
            'feed_name' => isset($_POST['feedName']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['feedName']))) : '',
            'channel_ids' => isset($channel_ids) ? esc_sql(sanitize_text_field($channel_ids)) : '',
            'auto_sync_interval' => isset($_POST['autoSyncIntvl']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSyncIntvl']))) : '',
            'auto_schedule' => isset($_POST['autoSync']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['autoSync']))) : '',
            'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
            'status' => isset($channel_ids) && strpos(sanitize_text_field($channel_ids), '1') !== false ? esc_sql('Draft') : '',
            'target_country' => isset($_POST['target_country']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['target_country']))) : '',
            'tiktok_catalog_id' => isset($tiktok_catalog_id) ? esc_sql(sanitize_text_field($tiktok_catalog_id)) : '',
            'fb_status' => isset($channel_ids) && strpos($channel_ids, '2') !== false ? esc_sql('Draft') : '',
            'tiktok_status' => isset($channel_ids) && strpos(sanitize_text_field($channel_ids), '3') !== false ? esc_sql('Draft') : '',
            'ms_status' => isset($channel_ids) && strpos($channel_ids, '4') !== false ? esc_sql('Draft') : '',
            'IncProductVar' => isset($_POST['IncProductVar']) ? intval($_POST['IncProductVar']) : 1,
            'IncDefProductVar' => isset($_POST['IncDefProductVar']) ? intval($_POST['IncDefProductVar']) : 0,
            'IncLowestPriceProductVar' => isset($_POST['IncLowestPriceProductVar']) ? intval($_POST['IncLowestPriceProductVar']) : 0,
            "filters" => wp_json_encode($filters),
            'categories' => wp_json_encode($mappedCatsDB),
            'attributes' => wp_json_encode($mappedAttrs)
          );
          $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s"));
          $result = $TVC_Admin_DB_Helper->tvc_get_last_row("ee_product_feed", array("id"));
          echo wp_json_encode($result);
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to get Feed data by id
     * Hook used wp_ajax_get_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     */
    public function get_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array(
          'id',
          'feed_name',
          'channel_ids',
          'auto_sync_interval',
          'auto_schedule',
          'status',
          'is_mapping_update',
          'last_sync_date',
          'target_country',
          'tiktok_catalog_id',
          'IncProductVar',
          'IncDefProductVar',
          'IncLowestPriceProductVar',
          'categories',
          'attributes',
          'filters'
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        echo wp_json_encode($result);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Duplicate Feed data by id
     * Hook used wp_ajax_ee_duplicate_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     */
    public function ee_duplicate_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array(
          'feed_name',
          'channel_ids',
          'auto_sync_interval',
          'auto_schedule',
          'categories',
          'attributes',
          'filters',
          'include_product',
          'exclude_product',
          'total_product',
          'target_country',
          'tiktok_catalog_id',
          'IncProductVar',
          'IncDefProductVar',
          'IncLowestPriceProductVar',
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $profile_data = array(
          'feed_name' => esc_sql('Copy of - ' . $result[0]['feed_name']),
          'channel_ids' => esc_sql($result[0]['channel_ids']),
          'auto_sync_interval' => esc_sql($result[0]['auto_sync_interval']),
          'auto_schedule' => esc_sql($result[0]['auto_schedule']),
          'filters' => $result[0]['filters'],
          'categories' => $result[0]['categories'],
          'attributes' => $result[0]['attributes'],
          'include_product' => esc_sql($result[0]['include_product']),
          'exclude_product' => esc_sql($result[0]['exclude_product']),
          'created_date' => esc_sql(gmdate('Y-m-d H:i:s', current_time('timestamp'))),
          'status' => esc_sql('Draft'),
          'target_country' => esc_sql($result[0]['target_country']),
          'tiktok_catalog_id' => esc_sql($result[0]['tiktok_catalog_id']),
          'tiktok_status' => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : '',
          'IncProductVar' => esc_sql($result[0]['IncProductVar']),
          'IncDefProductVar' => esc_sql($result[0]['IncDefProductVar']),
          'IncLowestPriceProductVar' => esc_sql($result[0]['IncLowestPriceProductVar']),
        );

        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%s", "%s"));
        echo wp_json_encode(array("error" => false, "message" => esc_html__("Duplicate Feed created successfully", "enhanced-e-commerce-for-woocommerce-store")));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to Delete Feed and product from GMC
     * Hook used wp_ajax_ee_delete_feed_data_by_id
     * Request Post
     * DB used ee_product_feed
     * Delete by id
     * Unschedule set_recurring_auto_sync_product_feed_wise cron 
     * Api Call to delete product from GMC 
     */
    public function ee_delete_feed_data_by_id()
    {
      $nonce = filter_input(INPUT_POST, 'conv_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_onboarding_nonce')) {
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        if (isset($_POST['id'])) {
          $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['id'])));
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Id is missing.", "enhanced-e-commerce-for-woocommerce-store")));
          exit;
        }
        $filed = array('exclude_product', 'status', 'include_product', 'tiktok_status', 'fb_status', 'ms_status', 'is_mapping_update');
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        // if ($result[0]['status'] === 'Synced' || $result[0]['tiktok_status'] === 'Synced' || $result[0]['fb_status'] === 'Synced' || $result[0]['ms_status'] === 'Synced' ) {
        if (isset($_POST['id'])) {
          as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['id']))));
        }
        /**
         * Api call to delete GMC product
         */
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();
        $merchantId = $TVC_Admin_Helper->get_merchantId();
        $data = array(
          "merchant_id" => $merchantId,
          "store_id" => $google_detail['setting']->store_id,
          "store_feed_id" => (isset($_POST['id']) ? sanitize_text_field(wp_unslash($_POST['id'])) : ''),
          "product_ids" => '',
          "caller" => "ee_delete_feed_data_by_id"
        );
        $CustomApi = new CustomApi();
        $response = $CustomApi->delete_from_channels($data);
        $TVC_Admin_Helper->plugin_log("Delete Feed from GMC" . wp_json_encode($response), 'product_sync');
        // }
        $soft_delete_id = array('status' => 'Deleted', 'tiktok_status' => 'Deleted', 'fb_status' => 'Deleted', 'ms_status' => 'Deleted', 'is_delete' => esc_sql(1), 'auto_schedule' => 0);
        if (isset($_POST['id'])) {
          $TVC_Admin_DB_Helper->tvc_delete_row("ee_product_feed", array("id" => sanitize_text_field(wp_unslash($_POST['id']))));
        }
        echo wp_json_encode(array("error" => false, "message" => esc_html__("Feed Deleted Successfully.", "enhanced-e-commerce-for-woocommerce-store")));
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to delete Product by product id from GMC
     * Hook used wp_ajax_ee_delete_feed_gmc
     * DB used ee_product_feed
     * Request Post product id and feedId
     * Api Call to delete product from GMC
     */
    public function ee_delete_feed_gmc()
    {
      if (isset($_POST['conv_onboarding_nonce']) && wp_verify_nonce($_POST['conv_onboarding_nonce'], 'conv_onboarding_nonce')) {
        $CONV_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $where = 'id = ' . esc_sql(sanitize_text_field($_POST['feed_id']));
        $filed = array('exclude_product', 'status', 'include_product', 'total_product', 'product_id_prefix', 'tiktok_catalog_id');
        $result = $CONV_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $totProdRem = $result[0]['total_product'] - 1;
        if ($result[0]['exclude_product'] != '' && $_POST['product_ids'] != '') {
          $allExclude = $result[0]['exclude_product'] . ',' . trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids'])));
          $profile_data = array(
            'exclude_product' => esc_sql($allExclude),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        } else if ($result[0]['include_product'] != '' && $_POST['product_ids'] != '') {
          $include_product = explode(',', $result[0]['include_product']);
          if (($key = array_search(trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids']))), $include_product)) !== false) {
            unset($include_product[$key]);
          }
          $all_include = implode(',', $include_product);
          $profile_data = array(
            'include_product' => esc_sql($all_include),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        } else {
          $profile_data = array(
            'exclude_product' => esc_sql(trim(str_replace($result[0]['product_id_prefix'], '', sanitize_text_field($_POST['product_ids'])))),
            'total_product' => $totProdRem >= 0 ? $totProdRem : 0,
          );
          $CONV_Admin_DB_Helper->tvc_update_row("ee_product_feed", $profile_data, array("id" => sanitize_text_field($_POST['feed_id'])));
        }
        $CONV_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $CONV_Admin_Helper->get_ee_options_data();
        $merchantId = $CONV_Admin_Helper->get_merchantId();

        $microsoft_catalog_id = '';
        $fb_catalog_id = '';
        $tiktok_catalog_id = '';

        $data = array(
          "merchant_id"     => $merchantId,
          "store_id"        => $google_detail['setting']->store_id,
          "store_feed_id"   => sanitize_text_field($_POST['feed_id']),
          "product_ids"     => sanitize_text_field($_POST['product_ids'])
        );
        if (!empty($result[0]) && isset($result[0]['tiktok_catalog_id'])) {
          $tiktok_catalog_id = sanitize_text_field($result[0]['tiktok_catalog_id']);
        }
        $tvc_admin_helper = new TVC_Admin_Helper();
        $ee_options = $tvc_admin_helper->get_ee_options_settings();
        if (!empty($ee_options['ms_catalog_id'])) {
          $microsoft_catalog_id = esc_html($ee_options['ms_catalog_id']);
        }
        $subscriptionId = $tvc_admin_helper->get_subscriptionId();
        $customApiObj = new CustomApi();
        $googledetail = $customApiObj->getGoogleAnalyticDetail($caller, $subscriptionId);
        if (!empty($googledetail->data->facebook_setting->fb_catalog_id)) {
          $fb_catalog_id = sanitize_text_field($googledetail->data->facebook_setting->fb_catalog_id);
        }
        $tiktok_business_id = isset($ee_options['tiktok_setting']['tiktok_business_id']) ? $ee_options['tiktok_setting']['tiktok_business_id'] : '';
        $data['tiktok_catalog_id'] = $tiktok_catalog_id;
        $data['tiktok_business_id'] = $tiktok_business_id;
        $data['ms_catalog_id']     = $microsoft_catalog_id;
        $data['catalog_id']     = $fb_catalog_id;
        $data["store_id"] = $google_detail['setting']->store_id;
        $data["caller"] = "ee_delete_feed_gmc";
        /**
         * Api Call to delete product from GMC
         */
        $convCustomApi = new CustomApi();
        $response = $convCustomApi->delete_from_channels($data);
        echo json_encode($response);
        exit;
      } else {
        echo json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * function to show Feed wise woocommerce product data
     * Hook used wp_ajax_ee_get_product_details_for_table
     * Request Post
     * DB used Woo commerce db
     */
    public function ee_get_product_details_for_table()
    {
      $nonce = filter_input(INPUT_POST, 'product_details_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_product_details-nonce')) {
        $products_per_page = isset($_POST['length']) ? sanitize_text_field(absint($_POST['length'])) : 10;
        $page_number = isset($_POST['start']) ? sanitize_text_field(absint($_POST['start'])) : 1;
        $search = isset($_POST['searchName']) ? sanitize_text_field(wp_unslash($_POST['searchName'])) : '';
        $productSearch = isset($_POST['productData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['productData']))) : array();
        $conditionSearch = isset($_POST['conditionData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['conditionData']))) : array();
        $valueSearch = isset($_POST['valueData']) ? explode(',', sanitize_text_field(wp_unslash($_POST['valueData']))) : array();
        $in_category_ids = array();
        $not_in_category_ids = array();
        $stock_status_to_fetch = array();
        $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = array();
        // if (isset($_POST['searchName'])) {
        //   array_push($search, sanitize_text_field(wp_unslash($_POST['searchName'])));
        // }

        /*******************All filters mapping *****************/
        foreach ($productSearch as $key => $value) {
          switch ($value) {
            case 'product_cat':
              if ($conditionSearch[$key] == "=") {
                array_push($in_category_ids, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($not_in_category_ids, $valueSearch[$key]);
              }
              break;
            case '_stock_status':
              if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "=") {
                array_push($stock_status_to_fetch, $valueSearch[$key]);
              } else if (!empty($conditionSearch[$key]) && $conditionSearch[$key] == "!=") {
                array_push($not_stock_status_to_fetch, $valueSearch[$key]);
              }
              break;
            case 'ID':
              if ($conditionSearch[$key] == "=") {
                array_push($product_ids_to_include, $valueSearch[$key]);
              } else if ($conditionSearch[$key] == "!=") {
                array_push($product_ids_to_exclude, $valueSearch[$key]);
              }
              break;
          }
        }
        $tax_query = array();
        if (!empty($in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $in_category_ids,
            'operator' => 'IN', // Retrieve products in any of the specified categories
          );
        }
        if (!empty($not_in_category_ids)) {
          $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $not_in_category_ids,
            'operator' => 'NOT IN', // Exclude products in any of the specified categories
          );
        }
        if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
          $tax_query = array('relation' => 'AND');
        }
        $meta_query = array();
        if (!empty($stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $stock_status_to_fetch,
            'compare' => 'IN', // Include products with these stock statuses
          );
        }

        // Add not_stock_status_to_fetch condition
        if (!empty($not_stock_status_to_fetch)) {
          $meta_query[] = array(
            'key'     => '_stock_status',
            'value'   => $not_stock_status_to_fetch,
            'compare' => 'NOT IN', // Exclude products with these stock statuses
          );
        }
        if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
          $meta_query = array('relation' => 'AND');
        }
        if (!isset($_POST['productData']) || $_POST['productData'] == "") {
          $pagination_count = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 's' => $search]))->found_posts;
          wp_reset_query();
        } else {
          $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            's'              => $search,
            'tax_query'      => $tax_query, // Dynamic tax query
            'meta_query'     => $meta_query,
            'post__not_in'   => $product_ids_to_exclude,
            'post__in'       => $product_ids_to_include,
          );

          $pagination_count  = (new WP_Query($args))->found_posts;
          wp_reset_query();
        }
        $p_id = isset($_POST['p_id']) ? sanitize_text_field(wp_unslash($_POST['p_id'])) : '';

        $args = array(
          'post_type'      => 'product',
          'posts_per_page' => $products_per_page,
          'post_status'    => 'publish',
          'offset'         => $page_number,
          'orderby'        => 'ID',
          'order'          => 'DESC',
          's'              => $search,
          'tax_query'      => $tax_query, // Dynamic tax query
          'meta_query'     => $meta_query,
          'post__not_in'   => $product_ids_to_exclude,
          'post__in'       => $product_ids_to_include,
        );
        $products = new WP_Query($args);
        $syncProductList = array();
        if ($products->have_posts()) {
          while ($products->have_posts()) {
            $products->the_post();
            $product_id =  get_the_ID();
            $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
            // Get product availability (stock status)
            $product_availability = get_post_meta($product_id, '_stock_status', true);

            // Get product quantity
            $product_quantity = get_post_meta($product_id, '_stock', true);
            $product_image_id = get_post_thumbnail_id($product_id);
            $product_image_src = wp_get_attachment_image_src($product_image_id, 'full');
            $product_image_url = isset($product_image_src[0]) ? $product_image_src[0] : "";
            $product_regular_price = get_post_meta($product_id, '_regular_price', true);
            $product_sale_price = get_post_meta($product_id, '_sale_price', true);
            $product_sku = get_post_meta($product_id, '_sku', true);
            if ($p_id == '_sku') {
              $proId = $product_sku;
            } elseif ($p_id == 'ID') {
              $proId = sanitize_text_field($product_id);
            } else {
              $proId = sanitize_text_field($product_id);
            }
            if ($proId == '') {
              $proId = sanitize_text_field($product_id);
            }
            $without_prefix = $proId;
            if (isset($_POST['prefix']) && !empty($_POST['prefix'])) {
              $proId = sanitize_text_field(wp_unslash($_POST['prefix'])) . $proId;
            }
            $type = get_post_meta($product_id, '_product_type', true);;

            $categories = '';
            foreach ($product_categories as $term) {
              $categories .= '<label class="fs-12 fw-400 defaultPointer">' . $term . '</label><br/>';
            }

            $syncProductList[] = array(
              'checkbox' => '<input class="checkbox" hidden type="checkbox" name="attrProduct"  id="attr_' . esc_html($product_id) . '" checked value="' . esc_html($proId) . '">
                                <div class="form-check form-check-custom">
                                <input class="form-check-input checkbox fs-17 syncProduct syncProduct_' . esc_html($without_prefix) . '" name="syncProduct" type="checkbox" value="' . esc_html($product_id) . '" id="sync_' . esc_html($product_id) . '" checked>
                                </div>',
              'product' => '<div class="d-flex flex-row bd-highlight">
                                <div class="p-2 pt-0 ps-0 bd-highlight image ">
                                  <img class="rounded image-w-h" src="' . esc_url($product_image_url) . '" />
                                </div>
                                <div class="p-3 pt-0 pb-0 bd-highlight">
                                <div class="text-truncate text-dark fs-12 fw-400" style="max-width: 200px;">' . sprintf(esc_html('%s'), esc_html(get_the_title())) . '</div>
                                <div class="fs-12 fw-400">Price: ' . get_woocommerce_currency_symbol() . " " . $product_regular_price . '</div>
                                <div class="fs-12 fw-400">Sale Price: ' . get_woocommerce_currency_symbol() . " " . $product_sale_price . '</div>
                                <div class="fs-12 fw-400">Product ID: ' . esc_html($product_id) . '</div>
                                <!--<div class="mt-1 text-dark"><abbr title="Get More Information" style="cursor: pointer;">More Info<abbr>
                                </div>-->
                                </div>
                                </div>',
              'category' => $categories,
              'availability' => '<label class="fs-12 fw-400 ' . esc_attr(ucfirst($product_availability)) . '">' . esc_html(ucfirst($product_availability)) . '</label>',
              'quantity' => '<label class="fs-12 fw-400">' . esc_html($product_quantity ? $product_quantity : '-') . '</label>',
              'channelstatus' => '<div class="channelStatus_ channelStatus_' . $proId . '"><div>
                  <button type="button" class="rounded-pill approved fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 approvedChannel"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Approved <span class="badge bg-light rounded-circle fs-7 approved-text ms-2 margin-badge approved_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden approvedDivContent">
                      <div class="card custom-width rounded-5">
                          <div class="card-header bg-white channel_logo_' . $proId . '">                        
  
                          </div>
                      </div>
                  </div>
              </div>
              <div>
                  <button type="button"
                      class="rounded-pill pending fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 pendingIssues"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Pending&nbsp; <span class="badge bg-light rounded-circle fs-7 pending-text ms-2 margin-badge pending_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden pendingDivContent">
                      <div class="card rounded-5">
                          <div class="card-header bg-warning-soft text-white">Pending Issues</div>
                          <div class="card-body pending_issue_text_' . $proId . '">
                              
                          </div>
                      </div>
                  </div>
              </div>
  
              <div>
                  <button type="button"
                      class="rounded-pill rejected fs-7 ps-3 pe-0 pt-0 pb-0 mb-2 rejectIssues"
                      data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover"
                      data-bs-trigger="hover focus">
                      Rejected <span class="badge bg-light rounded-circle fs-7 rejected-text ms-2 margin-badge rejected_count_' . $proId . '"
                          style="top:0px;">0</span>
                  </button>
                  <div class="hidden rejectDivContent">
                      <div class="card rounded-5">
                          <div class="card-header bg-danger-soft text-white">Rejected Issues</div>
                          <div class="card-body rejected_issue_text_' . $proId . '">                        
                          </div>
                      </div>
                  </div>
              </div></div>',
              'action' => '<div class="fs-12 channel_' . $type . '_' . $proId . '" id="channel_action_' . $proId . '"></div><div class="innerSpinner action_" id="action_' . $product_id . '"><div class="call_both_verification-spinner tvc-nb-spinner"></div><p class="centered">Fetching...</p></div>',
            );
          }
        }
        wp_reset_postdata();
        $result = array(
          'draw' => isset($_POST['draw']) ? sanitize_text_field(wp_unslash($_POST['draw'])) : '',
          'recordsTotal' => sanitize_text_field($pagination_count),
          'recordsFiltered' => sanitize_text_field($pagination_count),
          'data' => $syncProductList
        );

        echo wp_json_encode($result);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /************************************ All function for Feed Wise Product Sync Start ******************************************************************/
    /**
     * Ajax Call Feed wise product sync
     * Store category/attribute into options
     * Store Feed setting data into DB
     * initiated by ajax
     * Database Table used `ee_product_feed` 
     */
    function ee_feed_wise_product_sync_batch_wise()
    {
      $w_cat_id = $g_cat_id = '';
      $nonce = filter_input(INPUT_POST, 'conv_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'conv_ajax_product_sync_bantch_wise-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->plugin_log("Start", 'product_sync');
        $conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        try {
          $product_batch_size = isset($_POST['product_batch_size']) ? sanitize_text_field(wp_unslash($_POST['product_batch_size'])) : "25"; // barch size for inser product in GMC
          $product_id_prefix = isset($_POST['product_id_prefix']) ? sanitize_text_field(wp_unslash($_POST['product_id_prefix'])) : "";
          $feedId = isset($_POST['feedId']) ? sanitize_text_field(wp_unslash($_POST['feedId'])) : "";
          $data = isset($_POST['conv_data']) ? wp_unslash($_POST['conv_data']) : "";
          wp_parse_str($data, $formArray);
          $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();

          // foreach ($formArray as $key => $value) {
          //   if (preg_match("/^category-/i", $key)) {
          //     if ($value != '' && $value > 0) {
          //       $keyArray = explode("-", $key);
          //       if (in_array($keyArray[1], $selecetedCat)) {
          //         $w_cat_id = $keyArray[1];
          //         $g_cat_id = $value;
          //       }
          //     }
          //     unset($formArray[$key]);
          //   }
          // }
          // Batch settings
          $conv_additional_data['is_mapping_update'] = true;
          $conv_additional_data['is_process_start'] = false;
          $conv_additional_data['is_auto_sync_start'] = false;
          $conv_additional_data['product_sync_batch_size'] = sanitize_text_field($product_batch_size);
          $conv_additional_data['product_id_prefix'] = sanitize_text_field($product_id_prefix);
          $conv_additional_data['product_sync_alert'] = sanitize_text_field("Product sync settings updated successfully");
          $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
          $google_detail = $TVC_Admin_Helper->get_ee_options_data();
          $CustomApi = new CustomApi();
          if (!class_exists('TVCProductSyncHelper')) {
            include ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
          }
          $TVCProductSyncHelper = new TVCProductSyncHelper();
          $TVC_Admin_Helper->plugin_log("wooww 2411 Update Product Feed Table", 'product_sync');
          //Update Product Feed Table          
          $key = "id";
          $val = isset($_POST['feedId']) ? sanitize_text_field(wp_unslash($_POST['feedId'])) : '';
          $where = '`id` = ' . esc_sql(filter_input(INPUT_POST, 'feedId'));
          $filed = [
            'id',
            'channel_ids',
            'auto_sync_interval',
            'auto_schedule',
            'categories',
            'attributes',
            'filters',
            'include_product',
            'exclude_product',
            'product_id_prefix',
            'tiktok_catalog_id'
          ];
          $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
          if ($result) {
            $mappedAttrs = json_decode(stripslashes($result[0]['attributes']), true);
            $feed_MappedCat = json_decode(stripslashes($result[0]['categories']), true);
            $filters = json_decode(stripslashes($result[0]['filters']), true);
            $filtersjson = wp_json_encode($filters);
            $feed_MappedCatjson = wp_json_encode($feed_MappedCat);
            $mappedAttrsjson = wp_json_encode($mappedAttrs);

            //add/update data in default profile
            $profile_data = array("profile_title" => esc_sql("Default"), "g_attribute_mapping" => wp_json_encode($mappedAttrs), "update_date" => gmdate('Y-m-d H:i:s', current_time('timestamp')));
            if ($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") == 0) {
              $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s", "%s"));
            } else {
              $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
            }

            /***Single product sync for already synced product feed ******/
            if (isset($_POST['inculdeExtraProduct']) && $_POST['inculdeExtraProduct'] != '') {
              global $wpdb;
              $product_batch_size = (isset($conv_additional_data['product_sync_batch_size']) && $conv_additional_data['product_sync_batch_size']) ? $conv_additional_data['product_sync_batch_size'] : 100;
              $tvc_currency = sanitize_text_field($TVC_Admin_Helper->get_woo_currency());
              $merchantId = sanitize_text_field($TVC_Admin_Helper->get_merchantId());
              $accountId = sanitize_text_field($TVC_Admin_Helper->get_main_merchantId());
              $subscriptionId = sanitize_text_field(sanitize_text_field($TVC_Admin_Helper->get_subscriptionId()));
              $product_batch_size = esc_sql(intval($product_batch_size));
              if (isset($_POST['inculdeExtraProduct'])) {
                $products[0]['w_product_id'] = sanitize_text_field(wp_unslash($_POST['inculdeExtraProduct']));
              }
              $tiktok_catalog_id = '';
              $tiktok_business_id = sanitize_text_field($TVC_Admin_Helper->get_tiktok_business_id());
              $object = array(
                '0' => (object) array(
                  'w_product_id' => isset($_POST['inculdeExtraProduct']) ? sanitize_text_field(wp_unslash($_POST['inculdeExtraProduct'])) : '',
                  'w_cat_id' => $w_cat_id,
                  'g_cat_id' => $g_cat_id
                )
              );
              //map each product with category and attribute
              $p_map_attribute = $TVCProductSyncHelper->conv_get_feed_wise_map_product_attribute($object, $tvc_currency, $merchantId, $feedId, $product_batch_size, $mappedAttrs, $product_id_prefix);
              $TVC_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
              //update ee_product_sync_data
              if (isset($p_map_attribute['valid_products'])) {
                $TVC_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], sanitize_text_field(wp_unslash($_POST['feedId']))); //Add data in sync product database
              }
              if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
                $ee_options = $TVC_Admin_Helper->get_ee_options_settings();
                // call product sync API
                $data = [
                  'merchant_id' => sanitize_text_field($accountId),
                  'account_id' => sanitize_text_field($merchantId),
                  'subscription_id' => sanitize_text_field($subscriptionId),
                  'store_feed_id' => isset($_POST['feedId']) ? sanitize_text_field(wp_unslash($_POST['feedId'])) : '',
                  'is_on_gmc' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '1') !== false ? true : false,
                  'is_on_microsoft' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '4') !== false ? true : false,
                  'is_on_tiktok' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '3') !== false ? true : false,
                  'tiktok_catalog_id' => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
                  'tiktok_business_id' => sanitize_text_field($tiktok_business_id),
                  'is_on_facebook' => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? true : false,
                  'business_id' =>  isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? sanitize_text_field($ee_options['facebook_setting']['fb_business_id']) : '',
                  'catalog_id' =>  isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? sanitize_text_field($ee_options['facebook_setting']['fb_catalog_id']) : '',
                  'ms_catalog_id' =>  strpos($_POST['channel_ids'], '4') !== false ? $ee_options['ms_catalog_id'] : '',
                  'ms_store_id' =>  strpos($_POST['channel_ids'], '4') !== false ? $ee_options['microsoft_merchant_center_id'] : '',
                  'entries' => $p_map_attribute['items']
                ];
                /**************************** API Call to GMC ****************************************************************************/

                $response = $CustomApi->feed_wise_products_sync($data, 'call_by_includes/data/class-tvc-ajax-file.php 2447');
                $TVC_Admin_Helper->plugin_log("woow 2477 callback-feed_wise_products_sync()", 'product_sync');

                $endTime = new DateTime();
                $startTime = new DateTime();
                $diff = $endTime->diff($startTime);
                $responseData['time_duration'] = $diff;
                update_option("ee_prod_response", serialize($responseData));

                //echo '<pre>'; print_r($response); echo '</pre>';
                if ($response->error == false) {
                  if (isset($_POST['feedId'])) {
                    $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['feedId'])));
                  } else {
                    $where = '';
                  }
                  $filed = ['total_product'];
                  $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
                  $totalProduct = 0;
                  if ($result[0]['total_product'] !== NULL) {
                    $totalProduct = $result[0]['total_product'] + 1;
                  }
                  $feed_data = array(
                    "exclude_product" => isset($_POST['exclude']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                    "include_product" => isset($_POST['include']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['include']))) : '',
                    "product_sync_alert" => NULL,
                    'total_product' => $totalProduct,
                  );
                  $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));

                  $syn_data = array(
                    'status' => 1
                  );
                  if (isset($_POST['feedId'])) {
                    $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_data", $syn_data, array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                  }
                  $sync_message = esc_html__("By ajax Initiated, products are being synced to Merchant Center. Do not refresh..", "enhanced-e-commerce-for-woocommerce-store");
                  $sync_message = sprintf(esc_html('%s'), esc_html($sync_message));
                  $sync_progressive_data = array("sync_message" => $sync_message);
                  echo wp_json_encode(array('status' => 'success', "sync_progressive_data" => $sync_progressive_data));
                  exit;
                } else {
                  $TVC_Admin_Helper->plugin_log("woow 2518 Error in Sync", 'product_sync');
                  return wp_json_encode(array('error' => true, 'message' => esc_attr('Error in Sync...')));
                }
              } else {
                $TVC_Admin_Helper->plugin_log("woow 2522 Error in Sync", 'product_sync');
                return wp_json_encode(array('error' => true, 'message' => esc_attr('Error in Sync woow-2523...')));
              }
            } else {

              $TVC_Admin_Helper->plugin_log("woow 2527 Update feed data in DB start", 'product_sync');
              /*******Update feed data in DB start**********************/
              $feed_data = array(
                "categories" => $feed_MappedCatjson,
                "attributes" => $mappedAttrsjson,
                "filters" => $filtersjson,
                "include_product" => esc_sql(sanitize_text_field(wp_unslash($_POST['include']))),
                "exclude_product" => isset($_POST['exclude']) && $_POST['exclude'] != '' ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                "is_mapping_update" => true,
                "is_process_start" => false,
                "is_auto_sync_start" => false,
                "product_sync_batch_size" => esc_sql($product_batch_size),
                "product_id_prefix" => esc_sql($product_id_prefix),
                "product_sync_alert" => sanitize_text_field("Product sync settings updated successfully"),
                "status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '1') !== false ? esc_sql('In Progress') : null,
                "is_default" => esc_sql(0),
                "fb_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '2') !== false ? esc_sql('In Progress') : null,
                "tiktok_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '3') !== false ? esc_sql('In Progress') : null,
                "ms_status" => isset($_POST['channel_ids']) && strpos(sanitize_text_field(wp_unslash($_POST['channel_ids'])), '4') !== false ? esc_sql('In Progress') : null,
              );
              $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
              /*******Update feed data in DB end**********************/

              // if $feed_MappedCat['condition'] is not set then set 'new' as default
              if (!isset($feed_MappedCat['condition'])) {
                $feed_MappedCat['condition'] = 'new';
              }

              /*******Update feed data in laravel start**********************/
              $feed_data_api = array(
                "store_id" => $google_detail['setting']->store_id,
                "store_feed_id" => sanitize_text_field(wp_unslash($_POST['feedId'])),
                "map_categories" => $feed_MappedCatjson,
                "map_attributes" => $mappedAttrsjson,
                "filter" => $filtersjson,
                "include" => isset($_POST['include']) ? esc_sql(sanitize_text_field(wp_unslash($_POST['include']))) : '',
                "exclude" => isset($_POST['exclude']) && $_POST['exclude'] != '' ? esc_sql(sanitize_text_field(wp_unslash($_POST['exclude']))) : '',
                "channel_ids" => isset($_POST['channel_ids']) ? sanitize_text_field(wp_unslash($_POST['channel_ids'])) : '',
                "interval" => isset($_POST['autoSyncInterval']) ? sanitize_text_field(wp_unslash($_POST['autoSyncInterval'])) : '',
                "tiktok_catalog_id" => isset($_POST['tiktok_catalog_id']) ? sanitize_text_field(wp_unslash($_POST['tiktok_catalog_id'])) : '',
                "caller" => "ee_feed_wise_product_sync_batch_wise",
              );
              $TVC_Admin_Helper->plugin_log("mapping saved and product sync process scheduled", 'product_sync'); // Add logs
              $TVC_Admin_Helper->plugin_log("sending data to api" . wp_json_encode($feed_data_api), 'feed_data_api');
              $CustomApi = new CustomApi();
              $CustomApi->ee_create_product_feed($feed_data_api);
              /*******Update feed data in laravel End *********************/

              /********Manual Product sync Start ******************/
              if (isset($_POST['feedId'])) {
                as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                $isSyncComplete = $TVCProductSyncHelper->manualProductSync(sanitize_text_field(wp_unslash($_POST['feedId'])));
              } else {
                $isSyncComplete = '';
              }
              if ($isSyncComplete['status'] === 'success') {

                if (isset($_POST['feedId'])) {
                  $where = '`id` = ' . esc_sql(sanitize_text_field(wp_unslash($_POST['feedId'])));
                } else {
                  $where = '';
                }
                $filed = ['auto_sync_interval', 'auto_schedule'];
                $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
                $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
                $next_schedule_date = NULL;
                if ($result[0]['auto_schedule'] == 1) {
                  $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
                  $time_space = strtotime($result[0]['auto_sync_interval'] . " days", 0);
                  $timestamp = strtotime($result[0]['auto_sync_interval'] . " days");
                  $TVC_Admin_Helper->plugin_log("recurring cron set", 'product_sync'); // Add logs 
                  as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => sanitize_text_field(wp_unslash($_POST['feedId']))), "product_sync");
                }

                $feed_data = array(
                  "product_sync_alert" => NULL,
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "last_sync_date" => esc_sql($last_sync_date),
                  "next_schedule_date" => $next_schedule_date,
                );
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
              } else {
                $feed_data = array(
                  "product_sync_alert" => $isSyncComplete['message'],
                  "is_process_start" => false,
                  "is_auto_sync_start" => false,
                  "is_mapping_update" => false,
                );
                $TVC_Admin_Helper->plugin_log("error-1", 'product_sync'); // Add logs
                $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
                echo wp_json_encode(array("error" => true, "message" =>  $isSyncComplete['message']));
                exit;
              }

              /********Manual Product sync End ******************/
            }
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Feed data is missing.", "enhanced-e-commerce-for-woocommerce-store")));
            exit;
          }
          $sync_message = esc_html__("Via Ajax Initiated, products are being synced to Merchant Center, Do not refresh..", "enhanced-e-commerce-for-woocommerce-store");
          $sync_message = sprintf(esc_html('%s'), esc_html($sync_message));
          $sync_progressive_data = array("sync_message" => $sync_message);
          echo wp_json_encode(array('status' => 'success', "sync_progressive_data" => $sync_progressive_data));
          exit;
        } catch (Exception $e) {
          $TVC_Admin_Helper->plugin_log("woow 2627 catch method", 'product_sync');
          $conv_additional_data['product_sync_alert'] = $e->getMessage();
          $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
          $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
          $feed_data = array(
            "product_sync_alert" => $e->getMessage(),
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
          );
          if (isset($_POST['feedId'])) {
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => sanitize_text_field(wp_unslash($_POST['feedId']))));
          }
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    /**
     * Cron used for Feed wise product sync
     * Store data into Database 
     * hook used init_feed_wise_product_sync_process_scheduler
     * initiated by init_feed_wise_product_sync_process_scheduler_ee hook
     * Database Table used `ee_prouct_pre_sync_data` 
     * parameter int $feedId
     */
    function ee_call_start_feed_wise_product_sync_process($feedId)
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $TVC_Admin_Helper->plugin_log("Process to store data into ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      try {
        $subscriptionId = $TVC_Admin_Helper->get_subscriptionId();
        $customApiObj = new CustomApi();
        $caller = "ee_call_start_feed_wise_product_sync_process";
        // $googledetail = $customApiObj->getGoogleAnalyticDetail($caller, $subscriptionId);
        // $googleDetail = $googledetail->data;
        $ee_options = $TVC_Admin_Helper->get_ee_options_settings();

        global $wpdb;
        $where = '`id` = ' . esc_sql($feedId);
        $filed = ['feed_name', 'channel_ids', 'auto_sync_interval', 'auto_schedule', 'categories', 'attributes', 'filters', 'include_product', 'exclude_product', 'is_mapping_update', 'tiktok_catalog_id'];
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $gmc_id = isset($ee_options['google_merchant_center_id']) === TRUE ? $ee_options['google_merchant_center_id'] : '';
        if (strpos($result[0]['channel_ids'], '1') && ($gmc_id == '' || $gmc_id == null)) {
          $feed_data = array(
            "product_sync_alert" => 'GMC Id missing',
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
            "status" => strpos($result[0]['channel_ids'], '1') !== false ? esc_sql('Draft') : null,
            "fb_status" => strpos($result[0]['channel_ids'], '2') !== false ? esc_sql('Draft') : null,
            "tiktok_status" => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : null,
            "ms_status" => strpos($result[0]['channel_ids'], '4') !== false ? esc_sql('Draft') : null,
          );
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          $TVC_Admin_Helper->plugin_log('GMC Id missing', 'product_sync');
          exit;
        }
        if (strpos($result[0]['channel_ids'], '3') && ($result[0]['tiktok_catalog_id'] == '' || $result[0]['tiktok_catalog_id'] == null)) {
          $feed_data = array(
            "product_sync_alert" => 'Tiktok Catalog missing',
            "is_process_start" => false,
            "is_auto_sync_start" => false,
            "is_mapping_update" => false,
            "status" => strpos($result[0]['channel_ids'], '1') !== false ? esc_sql('Draft') : null,
            "tiktok_status" => strpos($result[0]['channel_ids'], '3') !== false ? esc_sql('Draft') : null,
          );
          $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          $TVC_Admin_Helper->plugin_log('Tiktok Catalog missing', 'product_sync');
          exit;
        }
        if (!empty($result) && isset($result) && $result[0]['is_mapping_update'] == '1') {
          $prouct_pre_sync_table = esc_sql("ee_prouct_pre_sync_data");
          if ($TVC_Admin_DB_Helper->tvc_row_count($prouct_pre_sync_table) > 0) {
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($wpdb->prefix . $prouct_pre_sync_table);
          }

          $product_db_batch_size = 200; // batch size to insert in database
          $batch_count = 0;
          $values = array();
          $place_holders = array();

          if ($result) {
            $TVC_Admin_Helper->plugin_log("Fetched feed data by ID", 'product_sync'); // Add logs       
            $filters = json_decode($result[0]['filters']);
            $filters_count = is_array($filters) ? count($filters) : '';
            $categories = json_decode($result[0]['categories']);
            $attributes = json_decode($result[0]['attributes']);
            $include = $result[0]['include_product'] != '' ? explode(",", $result[0]['include_product']) : '';
            $exclude = explode(",", $result[0]['exclude_product']);

            $in_category_ids = array();
            $not_in_category_ids = array();
            $stock_status_to_fetch = array();
            $not_stock_status_to_fetch = $product_ids_to_exclude = $product_ids_to_include = $search = array();
            $product_count = 0;
            if ($filters_count != '') {
              for ($i = 0; $i < $filters_count; $i++) {
                switch ($filters[$i]->attr) {
                  case 'product_cat':
                    if ($filters[$i]->condition == "=") {
                      array_push($in_category_ids, $filters[$i]->value);
                    } else if ($filters[$i]->condition == "!=") {
                      array_push($not_in_category_ids, $filters[$i]->value);
                    }
                    break;
                  case '_stock_status':
                    if (!empty($filters[$i]->condition) && $filters[$i]->condition == "=") {
                      array_push($stock_status_to_fetch, $filters[$i]->value);
                    } else if (!empty($filters[$i]->condition) && $filters[$i]->condition == "!=") {
                      array_push($not_stock_status_to_fetch, $filters[$i]->value);
                    }
                    break;
                  case 'ID':
                    if ($filters[$i]->condition == "=") {
                      array_push($product_ids_to_include, $filters[$i]->value);
                    } else if ($filters[$i]->condition == "!=") {
                      array_push($product_ids_to_exclude, $filters[$i]->value);
                    }
                    break;
                }
              }
            }

            if ($include == '') {
              $tax_query = array();
              if (!empty($in_category_ids)) {
                $tax_query[] = array(
                  'taxonomy' => 'product_cat',
                  'field'    => 'term_id',
                  'terms'    => $in_category_ids,
                  'operator' => 'IN', // Retrieve products in any of the specified categories
                );
              }
              if (!empty($not_in_category_ids)) {
                $tax_query[] = array(
                  'taxonomy' => 'product_cat',
                  'field'    => 'term_id',
                  'terms'    => $not_in_category_ids,
                  'operator' => 'NOT IN', // Exclude products in any of the specified categories
                );
              }
              if (!empty($in_category_ids) && !empty($not_in_category_ids)) {
                $tax_query = array('relation' => 'AND');
              }
              $meta_query = array();
              if (!empty($stock_status_to_fetch)) {
                $meta_query[] = array(
                  'key'     => '_stock_status',
                  'value'   => $stock_status_to_fetch,
                  'compare' => 'IN', // Include products with these stock statuses
                );
              }

              // Add not_stock_status_to_fetch condition
              if (!empty($not_stock_status_to_fetch)) {
                $meta_query[] = array(
                  'key'     => '_stock_status',
                  'value'   => $not_stock_status_to_fetch,
                  'compare' => 'NOT IN', // Exclude products with these stock statuses
                );
              }
              if (!empty($stock_status_to_fetch) && !empty($not_stock_status_to_fetch)) {
                $meta_query = array('relation' => 'AND');
              }
              if (empty($tax_query) && empty($meta_query) && empty($product_ids_to_exclude) && empty($product_ids_to_include)) {
                $count = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 's' => $search]))->found_posts;
                wp_reset_query();
              } else {
                $args = array(
                  'post_type'      => 'product',
                  'post_status'    => 'publish',
                  's'              => $search,
                  'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
                  'meta_query'     => $meta_query,
                  'post__not_in'   => $product_ids_to_exclude,
                  'post__in'       => $product_ids_to_include,
                );

                $count = (new WP_Query($args))->found_posts;
                wp_reset_query();
              }

              $allowed_count = 200;
              if ($count <= $allowed_count) {
                $args = array(
                  'post_type'      => 'product',
                  'posts_per_page' => 200,
                  'post_status'    => 'publish',
                  'offset'         => 0,
                  's'              => $search,
                  'tax_query'      => $tax_query, // Retrieve products in any of the specified categories          
                  'meta_query'     => $meta_query,
                  'post__not_in'   => $product_ids_to_exclude,
                  'post__in'       => $product_ids_to_include,
                );
                $products  = new WP_Query($args);
                if ($products->have_posts()) {
                  $all_cat = array();
                  foreach ($categories as $cat_key => $cat_val) {
                    $all_cat[$cat_key] = $cat_key;
                  }
                  while ($products->have_posts()) {
                    $products->the_post();
                    $product_id =  get_the_ID();
                    if (!in_array($product_id, $exclude)) {
                      $product_count++;
                      $have_cat = false;
                      $cat_id = 0;         
                      $cat_matched_id = 0;
                      $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
                      foreach ($product_categories as $term) {
                        $cat_id = $term->term_id;
                        if (isset($all_cat[$cat_id]) && $term->term_id == $all_cat[$cat_id]) {
                          $cat_matched_id = $term->term_id;
                          $have_cat = true;
                        }
                      }
                      if ($have_cat == true) {
                        array_push($values, esc_sql($product_id), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                        $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      } else {
                        array_push($values, esc_sql($product_id), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                        $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      }
                    }
                  }
                  $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                  $query .= implode(', ', $place_holders);
                  $wpdb->query($wpdb->prepare($query, $values));
                  wp_reset_postdata();
                  as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
                }
              } else {
                $allowed_count = 200;
                $page_number = 0;
                while ($count > 0) {
                  $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => $allowed_count,
                    'post_status'    => 'publish',
                    'offset'         => $page_number,
                    's'              => $search,
                    'tax_query'      => $tax_query, // Dynamic tax query
                    'meta_query'     => $meta_query,
                    'post__not_in'   => $product_ids_to_exclude,
                    'post__in'       => $product_ids_to_include,
                  );

                  $products  = new WP_Query($args);

                  if ($products->have_posts()) {
                    $all_cat = array();
                    foreach ($categories as $cat_key => $cat_val) {
                      $all_cat[$cat_key] = $cat_key;
                    }
                    $batch_count = 0;
                    while ($products->have_posts()) {
                      $products->the_post();
                      $product_id =  get_the_ID();
                      if (!in_array($product_id, $exclude)) {
                        $product_count++;
                        $batch_count++;
                        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
                        foreach ($product_categories as $term) {
                          $cat_id = $term->term_id;
                          if ($term->term_id == $all_cat[$cat_id]) {
                            $cat_matched_id = $term->term_id;
                            $have_cat = true;
                          }
                        }
                        if ($have_cat == true) {
                          array_push($values, esc_sql($product_id), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                          $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                        } else {
                          array_push($values, esc_sql($product_id), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                          $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                        }
                      }
                    }
                    $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                    $query .= implode(', ', $place_holders);
                    $wpdb->query($wpdb->prepare($query, $values));
                    $batch_count = 0;
                    $values = array();
                    $place_holders = array();
                  }
                  $page_number =  $page_number + 200;
                  $count = $count - $allowed_count;
                  wp_reset_postdata();
                }
                $TVC_Admin_Helper->plugin_log("All Data stored in ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
                as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
              }
            } else {
              $TVC_Admin_Helper->plugin_log("Only include product", 'product_sync'); // Add logs               
              foreach ($include as $val) {
                $allResult[]['ID'] = $val;
              }

              if (!empty($allResult)) {
                $all_cat = array();

                foreach ($categories as $cat_key => $cat_val) {
                  $all_cat[$cat_key] = $cat_key;
                }
                //$product_count = 0;
                $a = 0;
                foreach ($allResult as $postvalue) {
                  $have_cat = false;
                  if (!in_array($postvalue['ID'], $exclude)) {
                    $terms = get_the_terms(sanitize_text_field($postvalue['ID']), 'product_cat');
                    foreach ($terms as $key => $term) {
                      $cat_id = $term->term_id;
                      if ($term->term_id == $all_cat[$cat_id]) {
                        $cat_matched_id = $term->term_id;
                        $have_cat = true;
                      }
                    }

                    if ($have_cat == true) {
                      $product_count++;
                      $batch_count++;
                      array_push($values, esc_sql($postvalue['ID']), esc_sql($cat_matched_id), esc_sql($categories->$cat_matched_id->id), 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                      $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                      $query .= implode(', ', $place_holders);
                      $wpdb->query($wpdb->prepare($query, $values));
                    } else {
                      $product_count++;
                      array_push($values, esc_sql($postvalue['ID']), esc_sql($cat_id), '', 1, gmdate('Y-m-d H:i:s', current_time('timestamp')), $feedId);
                      $place_holders[] = "('%d', '%d', '%d', '%d', '%s', '%d')";
                      $query = "INSERT INTO `$wpdb->prefix$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date, feedId) VALUES ";
                      $query .= implode(', ', $place_holders);
                      $wpdb->query($wpdb->prepare($query, $values));
                    }
                  }
                } //end product list loop

                $TVC_Admin_Helper->plugin_log("All Data stored in ee_prouct_pre_sync_data table at " . gmdate('Y-m-d H:i:s', current_time('timestamp')) . " feed Id " . $feedId, 'product_sync'); // Add logs 
                as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
              } // end products if
            }

            $TVC_Admin_Helper->plugin_log("is_process_start", 'product_sync'); // Add logs
            $feed_data = array(
              "total_product" => $product_count,
              "is_process_start" => true,
              "product_sync_alert" => sanitize_text_field("Product sync process is ready to start"),
            );
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
          } else {
            $TVC_Admin_Helper->plugin_log("Data is missing for feed id = " . $feedId, 'product_sync'); // Add logs 
          }
        } else {
          $TVC_Admin_Helper->plugin_log("Empty result for feed id = " . $feedId, 'product_sync'); // Add logs 
        }
      } catch (Exception $e) {
        $feed_data = array(
          "product_sync_alert" => $e->getMessage(),
          "is_process_start" => false,
          "is_auto_sync_start" => false,
          "is_mapping_update" => false,
        );
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
        $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }

      return true;
    }

    /**
     * Cron used for Feed wise product sync
     * Store data into Database 
     * hook used auto_feed_wise_product_sync_process_scheduler_ee
     * initiated by init_feed_wise_product_sync_process_scheduler hook
     * Database Table used `ee_prouct_pre_sync_data`, `conv_product_sync_data`
     * parameter int $feedId
     */
    function ee_call_auto_feed_wise_product_sync_process($feedId)
    {
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $TVC_Admin_Helper->plugin_log("EE Feed wise product sync process Start", 'product_sync');
      $conv_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
      $conv_additional_data['product_sync_alert'] = NULL;
      $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      $feed_data = array(
        "product_sync_alert" => NULL,
      );
      $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));

      try {
        global $wpdb;
        $where = '`id` = ' . esc_sql($feedId);
        $filed = array(
          'feed_name',
          'channel_ids',
          'is_process_start',
          'auto_sync_interval',
          'auto_schedule',
          'categories',
          'attributes',
          'filters',
          'include_product',
          'exclude_product',
          'is_mapping_update'
        );
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        $TVC_Admin_Helper->plugin_log("EE auto feed wise product sync process start", 'product_sync');
        if (!empty($result) && isset($result[0]['is_process_start']) && $result[0]['is_process_start'] == true) {
          $TVC_Admin_Helper->plugin_log("EE call_batch_wise_auto_sync_product_feed", 'product_sync');
          if (!class_exists('TVCProductSyncHelper')) {
            include ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
          }
          $TVCProductSyncHelper = new TVCProductSyncHelper();
          $response = $TVCProductSyncHelper->call_batch_wise_auto_sync_product_feed_ee($feedId);
          if (!empty($response) && isset($response['message'])) {
            $TVC_Admin_Helper->plugin_log("EE Batch wise auto sync process response " . $response['message'], 'product_sync');
          }

          $tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
          $total_pending_pro = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) as a from {$wpdb->prefix}ee_prouct_pre_sync_data where `feedId` = %d AND `status` = 0", $feedId));
          if ($total_pending_pro == 0) {
            // Truncate pre sync table
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);

            $conv_additional_data['is_process_start'] = false;
            $conv_additional_data['is_auto_sync_start'] = true;
            $conv_additional_data['product_sync_alert'] = NULL;
            $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
            $last_sync_date = gmdate('Y-m-d H:i:s', current_time('timestamp'));
            $next_schedule_date = NULL;
            as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
            if ($result[0]['auto_schedule'] == 1) {
              $next_schedule_date = gmdate('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
              // add scheduled cron job      
              /***
               * Add recurring cron here
               *  
               * */
              $time_space = strtotime($result[0]['auto_sync_interval'] . " days", 0);
              $timestamp = strtotime($result[0]['auto_sync_interval'] . " days");
              //as_schedule_single_action($next_schedule_date, 'set_recurring_auto_sync_product_feed_wise', array("feedId" => $feedId));
              as_schedule_recurring_action(esc_attr($timestamp), esc_attr($time_space), 'init_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId), "product_sync");
            }
            $feed_data = array(
              "product_sync_alert" => NULL,
              "is_process_start" => false,
              "is_auto_sync_start" => true,
              "last_sync_date" => esc_sql($last_sync_date),
              "next_schedule_date" => $next_schedule_date,
            );
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
            $TVC_Admin_Helper->plugin_log("EE product sync process done", 'product_sync');
          } else {
            // add scheduled cron job            
            as_schedule_single_action(time() + 5, 'auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
            $TVC_Admin_Helper->plugin_log("EE recall product sync process", 'product_sync');
          }
        } else {
          // add scheduled cron job
          as_unschedule_all_actions('auto_feed_wise_product_sync_process_scheduler_ee', array("feedId" => $feedId));
        }
        echo wp_json_encode(array('status' => 'success', "message" => esc_html__("Feed wise product sync process started successfully", "enhanced-e-commerce-for-woocommerce-store")));
        return true;
      } catch (Exception $e) {
        $feed_data = array(
          "product_sync_alert" => $e->getMessage(),
        );
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
        $conv_additional_data['product_sync_alert'] = $e->getMessage();
        $TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
        $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
        return true;
      }
    }


    /**
     * Function used for to get TikTok Business Account by subcription id
     * hook used wp_ajax_get_tiktok_business_account
     * Type POST
     * parameter $subcriptionid
     */
    function get_tiktok_business_account()
    {
      $nonce = filter_input(INPUT_POST, 'conversios_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        if (isset($_POST['subscriptionId']) === TRUE && $_POST['subscriptionId'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = sanitize_text_field(wp_unslash($_POST['subscriptionId']));
          $customObj = new CustomApi();
          $caller = 'get_tiktok_business_account';
          $result = $customObj->get_tiktok_business_account($caller, $customer_subscription_id);
          $tikTokData = [];
          if (isset($result->status) && $result->status === 200 && is_array($result->data) && $result->data != '') {
            foreach ($result->data as $value) {
              if ($value->bc_info->status === 'ENABLE') {
                $tikTokData[$value->bc_info->bc_id] = $value->bc_info->name;
              }
            }

            echo wp_json_encode(array("error" => false, "data" => $tikTokData));
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
          }
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }

    function get_tiktok_user_catalogs()
    {
      $nonce = filter_input(INPUT_POST, 'conversios_onboarding_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {        // Initialize as an array instead of a string
        $customer_subscription_id = [];

        // Get sanitized input fields
        $subscription_id = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
        $business_id = isset($_POST['business_id']) ? sanitize_text_field(wp_unslash($_POST['business_id'])) : '';

        // Check if the necessary data is available
        if ($subscription_id !== '' && $business_id !== '') {
          // Assign values to the array
          $customer_subscription_id['customer_subscription_id'] = $subscription_id;
          $customer_subscription_id['business_id'] = $business_id;

          // Call the Custom API
          $customObj = new CustomApi();
          $caller = 'get_tiktok_user_catalogs';
          $result = $customObj->get_tiktok_user_catalogs($caller, $customer_subscription_id);

          // Process the result
          $tikTokData = [];
          if (isset($result->status) && $result->status === 200 && is_array($result->data) && !empty($result->data)) {
            foreach ($result->data as $key => $value) {
              $tikTokData[$value->catalog_conf->country][$value->catalog_id] = $value->catalog_name;
            }

            // Sort the catalogs by country
            foreach ($tikTokData as &$subArray) {
              arsort($subArray);
            }

            // Return success response
            echo wp_json_encode(array("error" => false, "data" => $tikTokData));
          } else {
            echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
          }
        } else {
          echo wp_json_encode(array("error" => true, "message" => esc_html__("Error: Business Account not found", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }


    public function conv_save_tiktokmiddleware($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && $post['conv_options_data']['tiktok_setting']['tiktok_business_id'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_subscription_id['business_id'] = $post['conv_options_data']['tiktok_setting']['tiktok_business_id'];
          $customObj = new CustomApi();
          $caller = 'conv_save_tiktokmiddleware';
          $result = $customObj->store_business_center($caller, $customer_subscription_id);
          return $result;
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        wp_die();
      }
    }
    public function conv_save_tiktokcatalog($post)
    {
      $catArr = [];
      $i = 0;
      $values = array();
      $place_holders = array();

      foreach ($post['conv_catalogData'] as $key => $value) {
        $catArr[$i]["region_code"] = $key;
        $catArr[$i++]["catalog_id"] = $value[0];
        array_push($values, esc_sql($key), esc_sql($value[0]), esc_sql($value[1]), gmdate('Y-m-d H:i:s', current_time('timestamp')));
        $place_holders[] = "('%s', '%s', '%s','%s')";
      }

      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      global $wpdb;
      $ee_tiktok_catalog = esc_sql($wpdb->prefix . "ee_tiktok_catalog");

      if ($TVC_Admin_DB_Helper->tvc_row_count("ee_tiktok_catalog") > 0) {
        $TVC_Admin_DB_Helper->tvc_safe_truncate_table($ee_tiktok_catalog);
      }
      //Insert tiktok catalog data into db
      $query = "INSERT INTO `$ee_tiktok_catalog` (country, catalog_id, catalog_name, created_date) VALUES ";
      $query .= implode(', ', $place_holders);
      $wpdb->query($wpdb->prepare($query, $values));
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && $post['conv_options_data']['tiktok_setting']['tiktok_business_id'] !== '') {
          $customer_subscription_id['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_subscription_id['business_id'] = $post['conv_options_data']['tiktok_setting']['tiktok_business_id'];
          $customer_subscription_id['catalogs'] = $catArr;
          $customer_subscription_id['caller'] = 'conv_save_tiktokcatalog';
          $customObj = new CustomApi();
          $result = $customObj->store_user_catalog($customer_subscription_id);
          return $result;
        }
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
        wp_die();
      }
    }

    public function ee_getCatalogId()
    {
      $nonce = filter_input(INPUT_POST, 'conv_country_nonce', FILTER_UNSAFE_RAW);

      if ($nonce && wp_verify_nonce($nonce, 'conv_country_nonce')) {
        if (isset($_POST['countryCode']) === TRUE && $_POST['countryCode'] !== '') {
          $country_code = sanitize_text_field(wp_unslash($_POST['countryCode']));
          $country_code = esc_sql($country_code); // extra escaping for DB safety
          global $wpdb;
          $table_name = esc_sql($wpdb->prefix . 'ee_tiktok_catalog');
          $query = $wpdb->prepare(
            "SELECT catalog_id FROM `{$table_name}` WHERE `country` = %s",
            $country_code
          );
          $result = $wpdb->get_results($query, ARRAY_A);
          $catalog_id['catalog_id'] = (!empty($result[0]['catalog_id']))
            ? $result[0]['catalog_id']
            : '';
          echo wp_json_encode(array("error" => false, "data" => $catalog_id));
        }
      } else {
        echo wp_json_encode(array(
          "error" => true,
          "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")
        ));
      }
      exit;
    }
    public function get_fb_catalog_data()
    {
      $nonce = filter_input(INPUT_POST, 'fb_business_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'fb_business_nonce')) {
        $data = array(
          "customer_subscription_id" => isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '',
          "business_id" => isset($_POST['fb_business_id']) ? sanitize_text_field(wp_unslash($_POST['fb_business_id'])) : '',
          "caller" => "get_fb_catalog_data",
        );
        /**
         * Api Call to store user FB Business
         */
        $convCustomApi = new CustomApi();
        // $response = $convCustomApi->storeUserBusiness($data);
        $result = $convCustomApi->getCatalogList($data);
        // $response->CatalogList = $result->data;
        echo wp_json_encode($result->data);
      } else {
        echo wp_json_encode(array("error" => true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      exit;
    }
    public function conv_save_facebookmiddleware($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && isset($post['conv_options_data']['facebook_setting']['fb_business_id']) && $post['conv_options_data']['facebook_setting']['fb_business_id'] !== '') {
          $customer_data['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_data['business_id'] = $post['conv_options_data']['facebook_setting']['fb_business_id'];
          $customer_data['caller'] = 'conv_save_facebookmiddleware';
          $customObj = new CustomApi();
          $result = $customObj->storeUserBusiness($customer_data);
          return $result;
        }
      }
    }

    public function conv_save_facebookcatalog($post)
    {
      $nonce = filter_input(INPUT_POST, 'pix_sav_nonce', FILTER_UNSAFE_RAW);
      if ($nonce && wp_verify_nonce($nonce, 'pix_sav_nonce_val')) {
        if (isset($post['customer_subscription_id']) === TRUE && $post['customer_subscription_id'] !== '' && isset($post['conv_options_data']['facebook_setting']['fb_business_id']) && $post['conv_options_data']['facebook_setting']['fb_business_id'] !== '') {
          $customer_data['customer_subscription_id'] = isset($_POST['customer_subscription_id']) ? sanitize_text_field(wp_unslash($_POST['customer_subscription_id'])) : '';
          $customer_data['business_id'] = $post['conv_options_data']['facebook_setting']['fb_business_id'];
          $customer_data['catalog_id'] = $post['conv_options_data']['facebook_setting']['fb_catalog_id'];
          $customer_data['caller'] = 'conv_save_facebookcatalog';
          $customObj = new CustomApi();
          $result = $customObj->storeUserCatalog($customer_data);
          return $result;
        }
      }
    }

    public function conv_save_microsoft($post)
    {
      $customer_data = [];
      if (isset($post['microsoft_ads_manager_id']) && $post['microsoft_ads_manager_id'] !== '') {
        $customer_data['customer_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_manager_id']));
      }
      if (isset($post['microsoft_ads_subaccount_id']) && $post['microsoft_ads_subaccount_id'] !== '') {
        $customer_data['account_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_subaccount_id']));
      }
      if (isset($post['microsoft_ads_pixel_id']) && $post['microsoft_ads_pixel_id'] !== '') {
        $customer_data['pixel_id'] = sanitize_text_field(wp_unslash($post['microsoft_ads_pixel_id']));
      }
      if (isset($post['microsoft_merchant_center_id']) && $post['microsoft_merchant_center_id'] !== '') {
        $customer_data['mmc_id'] = sanitize_text_field(wp_unslash($post['microsoft_merchant_center_id']));
      }
      if (isset($post['ms_catalog_id']) && $post['ms_catalog_id'] !== '') {
        $customer_data['catalog_id'] = sanitize_text_field(wp_unslash($post['ms_catalog_id']));
      }

      if (!empty($customer_data)) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();

        $customObj = new CustomApi();

        if (isset($post['subscription_id']) && $post['subscription_id'] !== '') {
          $customer_data['customer_subscription_id'] = sanitize_text_field(wp_unslash($post['subscription_id']));
        }
        if (isset($google_detail['setting']->store_id) && $google_detail['setting']->store_id !== '') {
          $customer_data['store_id'] = sanitize_text_field(wp_unslash($google_detail['setting']->store_id));
        }
        $customer_data['caller'] = 'conv_save_microsoft';
        $result = $customObj->updateMicrosoftDetail($customer_data);
        return $result;
      }
    }

    public function object_value($obj, $key)
    {
      if (!empty($obj) && $key && isset($obj->$key)) {
        return $obj->$key;
      }
    }
    public function get_analytics_account_list()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      $customObj = new CustomApi();
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
        $from_data = array("page" => isset($_POST['page']) ? sanitize_text_field(wp_unslash($_POST['page'])) : '');
        $caller = 'get_analytics_account_list';
        echo wp_json_encode($customObj->getAnalyticsAccountList($caller, $from_data));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function get_analytics_web_properties()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      $customObj = new CustomApi();
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
        $form_data = array(
          "type" => isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '',
          "account_id" => isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '',
          "caller" => 'get_analytics_web_properties'
        );
        echo wp_json_encode($customObj->getAnalyticsWebProperties($form_data));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_google_merchant_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
        $customApiObj = new CustomApi();
        $caller = 'list_google_merchant_account';
        echo wp_json_encode($customApiObj->listMerchantCenterAccount($caller));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_microsoft_merchant_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));

        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : "";
        $subaccount_id = isset($_POST['subaccount_id']) ? sanitize_text_field(wp_unslash($_POST['subaccount_id'])) : "";
        $customApiObj = new CustomApi();
        $caller = "list_microsoft_merchant_account";
        echo wp_json_encode($customApiObj->listMerchantCenterAccountMicrosoft($caller, $subaccount_id, $account_id, $subscription_id));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_microsoft_catalog_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : "";
        $subaccount_id = isset($_POST['subaccount_id']) ? sanitize_text_field(wp_unslash($_POST['subaccount_id'])) : "";
        $microsoft_merchant_center_id = isset($_POST['microsoft_merchant_center_id']) ? sanitize_text_field(wp_unslash($_POST['microsoft_merchant_center_id'])) : "";
        $customApiObj = new CustomApi();
        $caller = "list_microsoft_catalog_account";
        echo wp_json_encode($customApiObj->listMerchantCatalogAccountMicrosoft($caller, $subaccount_id, $account_id, $subscription_id, $microsoft_merchant_center_id));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function create_google_merchant_center_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
        $customApiObj = new CustomApi();
        $caller = "create_google_merchant_center_account";
        $google_detail = $customApiObj->getGoogleAnalyticDetail($caller, $tvc_data->subscription_id);
        $from_data = array(
          "store_name" => isset($_POST['store_name']) ? sanitize_text_field(wp_unslash($_POST['store_name'])) : '',
          "website_url" => isset($_POST['website_url']) ? sanitize_text_field(wp_unslash($_POST['website_url'])) : '',
          "customer_id" => isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '',
          "adult_content" => isset($_POST['adult_content']) ? sanitize_text_field(wp_unslash($_POST['adult_content'])) : '',
          "country" => isset($_POST['country']) ? sanitize_text_field(wp_unslash($_POST['country'])) : '',
          "email_address" => isset($_POST['email_address']) ? sanitize_text_field(wp_unslash($_POST['email_address'])) : '',
          "caller" => "create_google_merchant_center_account"
        );
        echo wp_json_encode($customApiObj->createMerchantAccount($from_data));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function create_microsoft_merchant_center_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $customApiObj = new CustomApi();
        $from_data = array(
          "customer_subscription_id" => isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : '',
          "customer_id" => isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '',
          "account_id" => isset($_POST['subaccount_id']) ? sanitize_text_field(wp_unslash($_POST['subaccount_id'])) : '',
          "store_name" => isset($_POST['store_name']) ? sanitize_text_field(wp_unslash($_POST['store_name'])) : '',
          "store_url" => isset($_POST['store_url']) ? sanitize_text_field(wp_unslash($_POST['store_url'])) : '',
          "notification_email" => isset($_POST['notification_email']) ? sanitize_text_field(wp_unslash($_POST['notification_email'])) : '',
          "notification_language" => isset($_POST['notification_language']) ? sanitize_text_field(wp_unslash($_POST['notification_language'])) : '',
          "caller" => "create_microsoft_merchant_center_account"
        );
        echo wp_json_encode($customApiObj->createMerchantAccountMicrosoft($from_data));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function save_merchant_data()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
        $customApiObj = new CustomApi();
        $merchant_id  = isset($_POST['merchant_id']) ? sanitize_text_field(wp_unslash($_POST['merchant_id'])) : '';
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : '';
        $google_merchant_center_id = isset($_POST['google_merchant_center_id']) ? sanitize_text_field(wp_unslash($_POST['google_merchant_center_id'])) : '';
        $website_url = isset($_POST['website_url']) ? sanitize_text_field(wp_unslash($_POST['website_url'])) : '';
        $customer_id = isset($_POST['customer_id']) ? sanitize_text_field(wp_unslash($_POST['customer_id'])) : '';

        $save_data = array(
          "merchant_id" => $merchant_id,
          "subscription_id" => $subscription_id,
          "google_merchant_center_id" => $google_merchant_center_id,
          "website_url" => $website_url,
          "customer_id" => $customer_id,
          "caller" => "save_merchant_data",
        );
        $result_merchant = $customApiObj->saveMechantData($save_data);
        $result_linkAd = '';
        $adwords_id = isset($_POST['adwords_id']) ? sanitize_text_field(wp_unslash($_POST['adwords_id'])) : '';
        if ($adwords_id != '') {
          $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
          $tvc_data = json_decode(str_replace("&quot;", "\"", $data));
          $from_data = array(
            "merchant_id" => $merchant_id,
            "account_id" => isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : '',
            "adwords_id" => $adwords_id,
            "subscription_id" => $subscription_id,
            "caller" => "save_merchant_data"
          );
          $result_linkAd = $customApiObj->linkGoogleAdsToMerchantCenter($from_data);
        }
        echo wp_json_encode(array('result_merchant' => $result_merchant, 'result_linkAd' => $result_linkAd));
        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_microsoft_ads_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $tvc_data =  json_decode(str_replace("&quot;", "\"", $data));
        $caller = "list_microsoft_ads_account";
        /*customApiObj = new CustomApi();
        $google_detail = $customApiObj->getGoogleAnalyticDetail($tvc_data->subscription_id);
        $access_token = isset($google_detail->data->access_token) ? base64_encode($google_detail->data->access_token) : '';
        $refresh_token = isset($google_detail->data->refresh_token) ? base64_encode($google_detail->data->refresh_token) : '';*/

        $customApiObj = new CustomApi();
        echo wp_json_encode($customApiObj->getMicrosoftAdsAccountList($caller, (array)$tvc_data));

        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_microsoft_ads_subaccount()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : "";
        $tvc_data =  json_decode(str_replace("&quot;", "\"", $data));
        $caller = "list_microsoft_ads_subaccount";
        /*customApiObj = new CustomApi();
        $google_detail = $customApiObj->getGoogleAnalyticDetail($tvc_data->subscription_id);
        $access_token = isset($google_detail->data->access_token) ? base64_encode($google_detail->data->access_token) : '';
        $refresh_token = isset($google_detail->data->refresh_token) ? base64_encode($google_detail->data->refresh_token) : '';*/

        $customApiObj = new CustomApi();
        echo wp_json_encode($customApiObj->getMicrosoftAdsSubAccountList($caller, (array)$tvc_data, $account_id));

        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function list_microsoft_ads_get_UET_tag()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $data = isset($_POST['tvc_data']) ? sanitize_text_field(wp_unslash($_POST['tvc_data'])) : "";
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : "";
        $subaccount_id = isset($_POST['subaccount_id']) ? sanitize_text_field(wp_unslash($_POST['subaccount_id'])) : "";
        $tvc_data =  json_decode(str_replace("&quot;", "\"", $data));

        /*customApiObj = new CustomApi();
        $google_detail = $customApiObj->getGoogleAnalyticDetail($tvc_data->subscription_id);
        $access_token = isset($google_detail->data->access_token) ? base64_encode($google_detail->data->access_token) : '';
        $refresh_token = isset($google_detail->data->refresh_token) ? base64_encode($google_detail->data->refresh_token) : '';*/
        $caller = "list_microsoft_ads_get_UET_tag";
        $customApiObj = new CustomApi();
        echo wp_json_encode($customApiObj->getMicrosoftAdsGetUET($caller, (array)$tvc_data, $account_id, $subaccount_id));

        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function create_microsoft_ads_UET_tag()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $account_id = isset($_POST['account_id']) ? sanitize_text_field(wp_unslash($_POST['account_id'])) : "";
        $subaccount_id = isset($_POST['subaccount_id']) ? sanitize_text_field(wp_unslash($_POST['subaccount_id'])) : "";
        $caller = "create_microsoft_ads_UET_tag";
        $customApiObj = new CustomApi();
        echo wp_json_encode($customApiObj->CreateMicrosoftAdsUET($caller, $subscription_id, $account_id, $subaccount_id));

        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function conv_create_bing_account()
    {
      $nonce = isset($_POST['conversios_onboarding_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_onboarding_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_onboarding_nonce')) {
        $subscription_id = isset($_POST['subscription_id']) ? sanitize_text_field(wp_unslash($_POST['subscription_id'])) : "";
        $account_name = isset($_POST['account_name']) ? sanitize_text_field(wp_unslash($_POST['account_name'])) : "";
        $currency_code = isset($_POST['currency_code']) ? sanitize_text_field(wp_unslash($_POST['currency_code'])) : "";
        $time_zone = isset($_POST['time_zone']) ? sanitize_text_field(wp_unslash($_POST['time_zone'])) : "";
        $tax_info_key = isset($_POST['tax_info_key']) ? sanitize_text_field(wp_unslash($_POST['tax_info_key'])) : "";
        $tax_info_val = isset($_POST['tax_info_val']) ? sanitize_text_field(wp_unslash($_POST['tax_info_val'])) : "";
        $sub_account_name = isset($_POST['sub_account_name']) ? sanitize_text_field(wp_unslash($_POST['sub_account_name'])) : "";
        $market_country = isset($_POST['market_country']) ? sanitize_text_field(wp_unslash($_POST['market_country'])) : "";
        $market_language = isset($_POST['market_language']) ? sanitize_text_field(wp_unslash($_POST['market_language'])) : "";
        $bussiness_name = isset($_POST['bussiness_name']) ? sanitize_text_field(wp_unslash($_POST['bussiness_name'])) : "";
        $address_1 = isset($_POST['line1']) ? sanitize_text_field(wp_unslash($_POST['line1'])) : "";
        $address_2 = isset($_POST['line2']) ? sanitize_text_field(wp_unslash($_POST['line2'])) : "";
        $city = isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : "";
        $state = isset($_POST['state']) ? sanitize_text_field(wp_unslash($_POST['state'])) : "";
        $postal_code = isset($_POST['postal_code']) ? sanitize_text_field(wp_unslash($_POST['postal_code'])) : "";
        $caller = "conv_create_bing_account";
        $customApiObj = new CustomApi();
        echo wp_json_encode($customApiObj->CreateMicrosoftAdsAccount($caller, $subscription_id, $account_name, $currency_code, $time_zone, $tax_info_key, $tax_info_val, $sub_account_name, $market_country, $market_language, $bussiness_name, $address_1, $address_2, $city, $state, $postal_code));

        wp_die();
      } else {
        echo esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store");
      }
    }
    public function set_email_configurationGA4()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $is_disabled = isset($_POST['is_disabled']) ? sanitize_text_field(wp_unslash($_POST['is_disabled'])) : "";
        $custom_email = isset($_POST['custom_email']) ? sanitize_text_field(wp_unslash($_POST['custom_email'])) : "";
        $email_frequency = isset($_POST['email_frequency']) ? sanitize_text_field(wp_unslash($_POST['email_frequency'])) : "";
        $save_email_bydefault = isset($_POST['save_email_bydefault']) ? sanitize_text_field(wp_unslash($_POST['save_email_bydefault'])) : "";
        $options = get_option("ee_options");
        $customApiObj = new CustomApi();
        if ($options) {
          $options = is_array($options) ? $options : unserialize($options);
          if (!isset($options['save_email_bydefault'])) {
            $options['save_email_bydefault'] = $save_email_bydefault;
            update_option('ee_options', serialize($options));
          }
        }
        $caller = 'set_email_configurationGA4';
        if ($is_disabled != "" && $custom_email != "" && $email_frequency != "") {
          $api_rs = $customApiObj->set_email_configurationGA4($caller, $is_disabled, $custom_email, $email_frequency);
          echo wp_json_encode($api_rs);
        } else {
          echo wp_json_encode(array('error' => true, 'errors' => esc_html__("Invalid required fields", "enhanced-e-commerce-for-woocommerce-store")));
        }
      } else {
        echo wp_json_encode(array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store")));
      }
      wp_die();
    }
    public function get_ga4_general_grid_reports()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $datediff = isset($_POST['datediff']) ? intval(wp_unslash($_POST['datediff'])) : "44";
        $old_end_date = sanitize_text_field(gmdate("Y-m-d", strtotime("-1 days", strtotime($start_date))));
        $old_start_date = sanitize_text_field(gmdate("Y-m-d", strtotime("-" . $datediff . " days", strtotime($old_end_date))));
        $customApiObj = new CustomApi();
        $caller = "get_ga4_general_grid_reports";
        $api_rs_present = $customApiObj->ga4_general_grid_report($caller, $start_date, $end_date, $domain);
        if (isset($api_rs_present->error) && $api_rs_present->error == '') {
          if (isset($api_rs_present->data) && $api_rs_present->data != "") {
            //call for past data
            $api_rs_past = $customApiObj->ga4_general_grid_report($caller, $old_start_date, $old_end_date, $domain);

            if (isset($api_rs_past->error) && $api_rs_past->error == '') {
              if (isset($api_rs_past->data) && $api_rs_past->data != "") {
                $return = array('error' => false, 'data_present' => $api_rs_present->data, 'data_past' => $api_rs_past->data, 'errors' => '');
              }
            } else {
              $return = array('error' => false, 'data_present' => $api_rs_present->data, 'errors' => '');
            }
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs_present->message) ? $api_rs_present->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_ga4_page_report()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $limit = isset($_POST['limit']) ? sanitize_text_field(wp_unslash($_POST['limit'])) : "10000";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $customApiObj = new CustomApi();
        $caller = "get_ga4_page_report";
        $api_rs = $customApiObj->ga4_page_report($caller, $start_date, $end_date, $domain, $limit);

        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data) && $api_rs->data != "") {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_general_donut_reports()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $report_name = isset($_POST['report_name']) ? sanitize_text_field(wp_unslash($_POST['report_name'])) : "";
        $customApiObj = new CustomApi();
        $caller = "get_general_donut_reports";
        $api_rs = $customApiObj->ga4_general_donut_report($caller, $start_date, $end_date, $domain, $report_name);

        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data) && $api_rs->data != "") {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_realtime_report()
    {
      $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $customApiObj = new CustomApi();
        $caller = "get_realtime_report";
        $api_rs = $customApiObj->ga4_realtime_report($caller, $domain);
        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data)) {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_general_audience_report()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $customApiObj = new CustomApi();
        $caller = "get_general_audience_report";
        $api_rs = $customApiObj->ga4_general_audience_report($caller, $start_date, $end_date, $domain);

        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data) && $api_rs->data != "") {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_daily_visitors_report()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $customApiObj = new CustomApi();
        $caller = "get_daily_visitors_report";
        $api_rs = $customApiObj->ga4_general_daily_visitors_report($caller, $start_date, $end_date, $domain);

        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data) && $api_rs->data != "") {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
    public function get_demographic_ga4_reports()
    {
      $nonce = isset($_POST['conversios_nonce']) ? sanitize_text_field(wp_unslash($_POST['conversios_nonce'])) : "";
      if ($nonce && wp_verify_nonce($nonce, 'conversios_nonce')) {
        $domain = isset($_POST['domain']) ? sanitize_text_field(wp_unslash($_POST['domain'])) : "";
        $start_date = str_replace(' ', '', (isset($_POST['start_date'])) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : "");
        if ($start_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $start_date);
          $start_date = $date->format('Y-m-d');
        }
        $start_date == (false !== strtotime($start_date)) ? gmdate('Y-m-d', strtotime($start_date)) : gmdate('Y-m-d', strtotime('-1 month'));

        $end_date = str_replace(' ', '', (isset($_POST['end_date'])) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : "");
        if ($end_date != "") {
          $date = DateTime::createFromFormat('d-m-Y', $end_date);
          $end_date = $date->format('Y-m-d');
        }
        $end_date == (false !== strtotime($end_date)) ? gmdate('Y-m-d', strtotime($end_date)) : gmdate('Y-m-d', strtotime('now'));

        $start_date = sanitize_text_field($start_date);
        $end_date = sanitize_text_field($end_date);
        $report_name = isset($_POST['report_name']) ? sanitize_text_field(wp_unslash($_POST['report_name'])) : "";
        $customApiObj = new CustomApi();
        $caller = "get_demographic_ga4_reports";
        $api_rs = $customApiObj->ga4_demographics_report($caller, $start_date, $end_date, $domain, $report_name);

        if (isset($api_rs->error) && $api_rs->error == '') {
          if (isset($api_rs->data) && $api_rs->data != "") {
            $return = array('error' => false, 'data' => $api_rs->data);
          }
        } else {
          $return = array('error' => true, 'errors' => isset($api_rs->message) ? $api_rs->message : '');
        }
      } else {
        $return = array('error' => true, 'errors' => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store"));
      }
      echo wp_json_encode($return);
      wp_die();
    }
  }
  function enhancad_get_plugin_image($relative_path, $alt = 'Image', $class = '', $style = '', $id = '')
  {
    $image_url = esc_url(ENHANCAD_PLUGIN_URL . $relative_path);
    $alt_attr = ' alt="' . esc_attr($alt) . '"';
    $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
    $id_attr = $id ? ' id="' . esc_attr($id) . '"' : '';
    $style_attr = $style ? ' style="' . esc_attr($style) . '"' : '';

    // Return the escaped <img> tag
    return '<' . 'img src="' . $image_url . '"' . $alt_attr . $class_attr . $style_attr . $id_attr . '>';
  }
// End of TVC_Ajax_File_Class
endif;
$tvcajax_file_class = new TVC_Ajax_File();
