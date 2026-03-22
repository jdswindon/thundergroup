<?php
if (!defined('ABSPATH')) {
  exit;
}

class CustomApi
{
  private $apiDomain;
  private $token;
  protected $TVC_Admin_Helper;
  public $subscriptionId;
  private $customerId;
  private $merchantId;
  private $mcamerchantId;

  public function __construct()
  {
    $this->apiDomain = TVC_API_CALL_URL;
    $this->token = 'MTIzNA==';
    $wp_filesystem = TVC_Admin_Helper::get_filesystem();
    $merchantInfo = json_decode($wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . 'includes/setup/json/merchant-info.json'), true);
    $this->mcamerchantId = sanitize_text_field($merchantInfo['merchantId']);
  }

  public function get_subscriptionId()
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $ee_options_settings = $TVC_Admin_Helper->get_ee_options_settings();
    return $this->subscriptionId = (isset($ee_options_settings['subscription_id'])) ? $ee_options_settings['subscription_id'] : "";
  }
  public function conv_get_store_id()
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $store_id = $google_detail['setting']->store_id;
    if (!empty($store_id)) {
      return $store_id;
    } else {
      $TVC_Admin_Helper->update_subscription_details_api_to_db();
      $google_detail_new = $TVC_Admin_Helper->get_ee_options_data();
      return $google_detail_new['setting']->store_id;
    }
  }
  public function tc_wp_remot_call_post($url, $args)
  {
    try {
      if (!empty($args)) {
        // Send remote request
        $args['timeout'] = "1000";
        $request = wp_remote_post($url, $args);

        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);

        $response_message = wp_remote_retrieve_response_message($request);
        $response_body = json_decode(wp_remote_retrieve_body($request));

        if ((isset($response_body->error) && $response_body->error == '')) {
          if (isset($response_body->data) && $response_body->data != '') {
            return new WP_REST_Response($response_body->data);
          } elseif (isset($response_body->message) && $response_body->message != '') {
            return new WP_REST_Response($response_body->message);
          } else {
            return new WP_REST_Response($response_body);
          }
        } else {
          return new WP_Error($response_code, $response_message, $response_body);
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function update_app_status($caller, $status = 1)
  {
    try {
      $subscription_id = $this->get_subscriptionId();
      if ($subscription_id != "") {
        $url = $this->apiDomain . '/customer-subscriptions/update-app-status';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );

        $options = unserialize(get_option('ee_options'));
        $fb_pixel_enable = "0";
        if (isset($options['fb_pixel_id']) && $options['fb_pixel_id'] != "") {
          $fb_pixel_enable = "1";
        }
        $woocomm_version = "0";
        if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
          global $woocommerce;
          $woocomm_version = $woocommerce->version;
        }
        $store_country = get_option('woocommerce_default_country');
        $store_country = explode(":", $store_country);

        $attributes = unserialize(get_option('ee_prod_mapped_attrs'));
        $categories = unserialize(get_option('ee_prod_mapped_cats'));
        $countAttribute = is_array($attributes) ? count($attributes) : 0;
        $countCategories = is_array($categories) ? count($categories) : 0;

        $postData = array(
          "subscription_id" => $subscription_id,
          "domain" => esc_url(get_site_url()),
          "app_status_data" => array(
            "app_settings" => array(
              "app_status" => sanitize_text_field($status),
              "fb_pixel_enable" => $fb_pixel_enable,
              "app_verstion" => PLUGIN_TVC_VERSION,
              "domain" => esc_url(get_site_url()),
              "product_settings" => unserialize(get_option('ee_options')),
              "attributeMapping" => $countAttribute,
              "categoryMapping" => $countCategories,
              "update_date" => gmdate("Y-m-d")
            ),
            "store" => array(
              "country" => isset($store_country[0]) ? $store_country[0] : "",
              "state" => isset($store_country[1]) ? $store_country[1] : ""
            ),
            "walk_through" => isset($options['walk_through']) ? $options['walk_through'] : "",
            "woocomm_version" => $woocomm_version,
          ),
          "caller" => sanitize_text_field($caller)
        );
        if ($postData['subscription_id'] == '' || $postData['domain'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        wp_remote_post(esc_url_raw($url), $args);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function app_activity_detail($caller, $status)
  {
    try {
      $subscription_id = $this->get_subscriptionId();
      if (isset($subscription_id) && $status != "") {
        $url = $this->apiDomain . '/customer-subscriptions/app_activity_detail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData = array(
          "subscription_id" => $subscription_id,
          "domain" => esc_url(get_site_url()),
          "app_status" => sanitize_text_field($status),
          "app_data" => array(
            "app_version" => PLUGIN_TVC_VERSION,
            "app_id" => CONV_APP_ID,
          ),
          "caller" => sanitize_text_field($caller)
        );
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getGoogleAnalyticDetail($caller, $subscription_id = null)
  {
    try {

      $url = $this->apiDomain . '/customer-subscriptions/subscription-detail';
      $header = array(
        "Authorization: Bearer " . $this->token,
        "Content-Type" => "application/json"
      );
      $ee_options_data = unserialize(get_option('ee_options'));
      if ($subscription_id == null && isset($ee_options_data['subscription_id'])) {
        $subscription_id = sanitize_text_field($ee_options_data['subscription_id']);
      }
      $data = [
        'subscription_id' => sanitize_text_field($subscription_id),
        'domain' => get_site_url(),
        'caller' => sanitize_text_field($caller)
      ];
      if ($subscription_id == "") {
        $return = new \stdClass();
        $return->error = true;
        return $return;
      }
      if ($data['subscription_id'] == '' || $data['domain'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $return = new \stdClass();
      if (isset($result->status)) {
        if ($result->status == 200) {
          $return->status = $result->status;
          $return->data = $result->data;
          $return->error = false;
          return $return;
        } else {
          $return->error = true;
          $return->data = $result->data;
          $return->status = $result->status;
          return $return;
        }
      } else {
        $return->error = true;
        $return->data = 'something went wrong please try again';
        $return->status = '404';
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function add_survey_of_deactivate_plugin($caller, $postData)
  {
    try {
      $url = $this->apiDomain . "/customersurvey";
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $postData['caller'] = $caller;
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $args = array(
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($postData)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);

      $return = new \stdClass();
      if ($result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = $result->data;
        $return->status = $result->status;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function active_licence_Key($caller, $licence_key, $subscription_id)
  {
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/licence/activation";
      $data = [
        'key' => sanitize_text_field($licence_key),
        'domain' => get_site_url(),
        'subscription_id' => sanitize_text_field($subscription_id),
        'app_id' => 1,
        'caller' => sanitize_text_field($caller)
      ];
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($response->error) && $response->error == '')) {
        //$return->status = $result->status;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        if (isset($response->data)) {
          $return->error = false;
          $return->data = $response->data;
          $return->message = $response->message;
        } else {
          $return->error = true;
          $return->data = [];
          if (isset($response->errors->key[0])) {
            $return->message = $response->errors->key[0];
          } else {
            $return->message = esc_html__("Check your entered licese key.", "enhanced-e-commerce-for-woocommerce-store");
          }
        }
        return $return;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_microsoft_conversion_list($caller, $customer_id, $account_id, $tag_id)
  {
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/microsoft/getConversionGoals";
      $subscription_id = $this->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'account_id' => sanitize_text_field($account_id),
        'subscription_id' => sanitize_text_field($subscription_id),
        'tag_id' => sanitize_text_field($tag_id),
        'caller' => sanitize_text_field($caller)
      ];
      if ($data['subscription_id'] == '' || $data['account_id'] == '' || $data['customer_id'] == '' || $data['tag_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function conv_create_microsoft_ads_conversion($caller, $customer_id, $account_id, $tag_id, $conversionCategory, $name, $action_value)
  {
    $currency_code = "";
    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      $currency_code = get_woocommerce_currency();
    }
    try {
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $url = $this->apiDomain . "/microsoft/createConversionGoals";
      $subscription_id = $this->get_subscriptionId();
      $data = [
        'customer_id' => sanitize_text_field($customer_id),
        'account_id' => sanitize_text_field($account_id),
        'tag_id' => sanitize_text_field($tag_id),
        'conversion_category' => sanitize_text_field($conversionCategory),
        'name' => sanitize_text_field($name),
        'subscription_id' => $subscription_id,
        'currency_code' => $currency_code,
        'action_value' => $action_value,
        'caller' => sanitize_text_field($caller)
      ];
      if ($data['subscription_id'] == '' || $data['name'] == '' || $data['account_id'] == '' || $data['customer_id'] == '' || $data['tag_id'] == '' || $data['action_value'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $request = wp_remote_post(esc_url_raw($url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->errors = $result->errors;
        //$return->error = $result->data;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function products_sync($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          if (in_array($key, array("merchant_id", "account_id", "subscription_id"))) {
            $postData[$key] = sanitize_text_field($value);
          }
        }
      }
      $postData['store_id'] = $this->conv_get_store_id();
      $postData['subscription_id'] = $this->get_subscriptionId();
      $url = $this->apiDomain . "/products/batch";
      if ($postData['store_id'] == '' || $postData['subscription_id'] == '' || $postData['store_feed_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $postData;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->error = false;
        $return->products_sync = count($response->data->entries);
        return $return;
      } else {
        $return->error = true;
        $return->arges =  $args;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getSyncProductList($postData)
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          $postData[$key] = sanitize_text_field($value);
        }
      }
      $url = $this->apiDomain . "/products/list";
      $postData["maxResults"] = 50;
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));

      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->error = false;
        $return->data = $response->data;
        $return->message = $response->message;
        return $return;
      } else {
        $return->status = $response_code;
        $return->error = true;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function siteVerificationToken($postData)
  {
    try {
      $url = $this->apiDomain . '/gmc/site-verification-token';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'website' => sanitize_text_field($postData['website_url']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'method' => sanitize_text_field($postData['method']),
        'store_id' => $this->conv_get_store_id(),
        'subscription_id' => $this->get_subscriptionId(),
        'caller' => sanitize_text_field($postData['caller'])
      ];
      if ($data['website'] == '' || $data['merchant_id'] == '' || $data['account_id'] == '' || $data['store_id'] == '' || $data['subscription_id'] == '' || $data['method'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
      $return = new \stdClass();
      if (isset($result->status) && $result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        $return->data = (isset($result->data) ? $result->data : null);
        $return->status = (isset($result->status) ? $result->status : null);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function siteVerification($postData)
  {
    try {
      $url = $this->apiDomain . '/gmc/site-verification';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'website' => esc_url_raw($postData['website_url']),
        'subscription_id' => $this->get_subscriptionId(),
        'account_id' => sanitize_text_field($postData['account_id']),
        'method' => sanitize_text_field($postData['method']),
        'store_id' => $this->conv_get_store_id(),
        'caller' => sanitize_text_field($postData['caller'])
      ];
      if ($data['website'] == '' || $data['merchant_id'] == '' || $data['account_id'] == '' || $data['store_id'] == '' || $data['subscription_id'] == '' || $data['method'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {

        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function claimWebsite($postData)
  {
    try {
      $url = $this->apiDomain . '/gmc/claim-website';
      $data = [
        'merchant_id' => sanitize_text_field($postData['merchant_id']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'website' => esc_url_raw($postData['website_url']),
        'subscription_id' => $this->get_subscriptionId(),
        'store_id' => $this->conv_get_store_id(),
        'caller' => sanitize_text_field($postData['caller'])
      ];
      if ($data['merchant_id'] == '' || $data['account_id'] == '' || $data['store_id'] == '' || $data['subscription_id'] == '' || $data['website'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));

      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {

        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        if (is_array($result->errors)) {
          if (count($result->errors) != count($result->errors, COUNT_RECURSIVE)) {
            $return->errors = implode("&", array_map(function ($a) {
              return implode("~", $a);
            }, $result->errors));
          } else {
            $return->errors = implode(" ", $result->errors);
          }
        } else {
          $return->errors = $result->errors;
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getProductStatusByChannelId($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/products/status-list';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['store_id'] == '' || $data['subscription_id'] == '' || $data['store_feed_id'] == '' || $data['channel'] == '' || $data['product_ids'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        //echo '<pre>'; print_r($args); print_r($result); echo '</pre>'; exit('wow'); // woow on syncu propduct status (ee_get_product_status())
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_feed_status_by_store_id($data)
  {
    try {
      $subscription_id = $this->get_subscriptionId();
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/feed-list';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['store_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function delete_from_channels($data)
  {
    try {
      $subscription_id = $this->get_subscriptionId();
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/batch';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data['store_id'] = $this->conv_get_store_id();
        $data['subscription_id'] = $this->get_subscriptionId();
        if ($data['store_id'] == '' || $data['subscription_id'] == '' || $data['store_feed_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'DELETE',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        $return = new \stdClass();
        if ($result->status == 200) {
          $return->status = $result->status;
          $return->data = $result->data;
          $return->error = false;
          return $return;
        } else {
          $return->error = true;
          $return->data = $result->data;
          $return->status = $result->status;
          return $return;
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ee_create_product_feed($data)
  {
    try {
      $subscription_id = $this->get_subscriptionId();
      if (isset($subscription_id) && $data != "") {
        $url = $this->apiDomain . '/products/feed';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['store_id'] == '' || $data['channel_ids'] == '' || $data['store_feed_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function feed_wise_products_sync($postData, $callby = 'no-reference-give')
  {
    try {
      if (!empty($postData)) {
        foreach ($postData as $key => $value) {
          if (in_array($key, array("merchant_id", "account_id", "subscription_id", "store_feed_id", "is_on_gmc", "is_on_microsoft", "is_on_facebook", "is_on_tiktok"))) {
            $postData[$key] = sanitize_text_field($value);
          }
        }
      }
      $postData['store_id'] = $this->conv_get_store_id();
      $postData['subscription_id'] = $this->get_subscriptionId();
      if ($postData['store_id'] == '' || $postData['subscription_id'] == '' || $postData['store_feed_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $postData;
        $return->status = 400;
        return $return;
      }
      $url = $this->apiDomain . "/products/batch-all";
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($postData)
      );
      $request = wp_remote_post(esc_url_raw($url), $args);

      //echo '<pre>'.$callby; print_r(json_decode(wp_remote_retrieve_body($request))); echo '</pre>'; // 


      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->error = false;
        return $return;
      } else {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->plugin_log("woow 1232 error-feed_wise_products_sync():" . json_encode($response), 'product_sync');
        $return->error = true;
        $return->arges =  $args;
        if (isset($response->errors)) {
          foreach ($response->errors as $err) {
            $return->message = $err;
            break;
          }
        }
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_tiktok_business_account($caller, $postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/getBusinessCenter';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData['caller'] = sanitize_text_field($caller);
        if ($postData['customer_subscription_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function get_tiktok_user_catalogs($caller, $postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/getUserCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $postData['caller'] = sanitize_text_field($caller);
        if ($postData['customer_subscription_id'] == '' || $postData['business_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function store_business_center($caller, $postData)
  {
    $postData['store_id'] = $this->conv_get_store_id();
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/storeBusinessCenter';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($postData['customer_subscription_id'] == '' || $postData['business_id'] == '' || $postData['store_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $postData['caller'] = sanitize_text_field($caller);
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function store_user_catalog($postData)
  {
    $postData['store_id'] = $this->conv_get_store_id();
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/storeUserCatalog';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($postData['customer_subscription_id'] == '' || $postData['business_id'] == '' || $postData['store_id'] == '' || $postData['catalogs'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function createCatalogs($postData)
  {
    try {
      if ($postData != "") {
        $url = $this->apiDomain . '/tiktok/createCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($postData['customer_subscription_id'] == '' || $postData['business_id'] == '' || $postData['region_code'] == '' || $postData['catalog_name'] == '' || $postData['currency'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $postData;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  public function getUserBusinesses($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/getUserBusinesses';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['customer_subscription_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        $dataResult = array();
        if (isset($result->data)) {
          foreach ($result->data as $val) {
            $dataResult["$val->id"] = $val->id . '-' . $val->name;
          }
        }

        return $dataResult;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getCatalogList($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/getUserCatalogs';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['customer_subscription_id'] == '' || $data['business_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function storeUserBusiness($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/storeUserBusiness';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data['store_id'] = $this->conv_get_store_id();
        if ($data['customer_subscription_id'] == '' || $data['store_id'] == '' || $data['business_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function storeUserCatalog($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/facebook/storeUserCatalog';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        $data['store_id'] = $this->conv_get_store_id();
        if ($data['customer_subscription_id'] == '' || $data['store_id'] == '' || $data['business_id'] == '' || $data['catalog_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function updateMicrosoftDetail($data)
  {
    try {
      if (isset($data)) {
        $url = $this->apiDomain . '/microsoft/updateMicrosoftDetail';
        $header = array(
          "Authorization: Bearer " . $this->token,
          "Content-Type" => "application/json"
        );
        if ($data['customer_subscription_id'] == '' || $data['store_id'] == '') {
          $return = new \stdClass();
          $return->error = true;
          $return->conv_param_error = 'Required parameters are missing.';
          $return->conv_data = $data;
          $return->status = 400;
          return $return;
        }
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $result = $this->tc_wp_remot_call_post(esc_url_raw($url), $args);
        return $result;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_page_report($caller, $from_date = '', $to_date = '', $domain = '', $limit = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-pages-report';
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'dimension' => 'pagePath',
        'limit' => $limit,
        'orderbymetric' => 'screenPageViews',
        'offset' => '0',
        'store_id' => $this->conv_get_store_id(),
        'caller' => sanitize_text_field($caller)
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['start_date'] == '' || $data['end_date'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_general_donut_report($caller, $from_date = '', $to_date = '', $domain = '', $report_name = '')
  {
    try {
      $endpoint = "get-ga-devicebreakdown-report";
      if ($report_name == "conv_users_chart") {
        $endpoint = "get-ga-users-report";
      }
      $url = $this->apiDomain . '/ga-general-reports/' . $endpoint;
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'store_id' => $this->conv_get_store_id(),
        'caller' => sanitize_text_field($caller)
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['start_date'] == '' || $data['end_date'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_demographics_report($caller, $from_date = '', $to_date = '', $domain = '', $report_name = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-demographics-reports';
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'dimension' => $report_name,
        'limit' => 5,
        'caller' => $caller,
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['start_date'] == '' || $data['end_date'] == '' || $data['dimension'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_general_audience_report($caller, $from_date = '', $to_date = '', $domain = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-audience-report';
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'caller' => $caller,
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['start_date'] == '' || $data['end_date'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_general_daily_visitors_report($caller, $from_date = '', $to_date = '', $domain = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-daily-visitors-report';
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'caller' => $caller,
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['start_date'] == '' || $data['end_date'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_realtime_report($caller, $domain = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-realtime-reports';
      $data = [
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'caller' => $caller,
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function ga4_general_grid_report($caller, $from_date = '', $to_date = '', $domain = '')
  {
    try {
      $url = $this->apiDomain . '/ga-general-reports/get-ga-grid-report';
      $data = [
        'start_date' => sanitize_text_field($from_date),
        'end_date' => sanitize_text_field($to_date),
        'subscription_id' => sanitize_text_field($this->get_subscriptionId()),
        'domain' => $domain,
        'caller' => $caller,
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['end_date'] == '' || $data['start_date'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      $args = array(
        'timeout' => 10000,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ((isset($result->error) && $result->error == '')) {
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function set_email_configurationGA4($caller, $is_disabled, $custom_email = '', $email_frequency = '')
  {
    try {
      $data = array('is_disabled' => $is_disabled, 'subscription_id' => sanitize_text_field($this->get_subscriptionId()), 'custom_email' => $custom_email, 'emailFrequency' => $email_frequency, 'caller' => $caller);
      $curl_url = $this->apiDomain . '/actionable-dashboard/update-ga4-email-schedule';
      $header = array(
        "Authorization: Bearer $this->token",
        "Content-Type" => "application/json"
      );
      if ($data['subscription_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url_raw($curl_url), $args);
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == false) {
        $return->error = false;
        $return->message = esc_attr($response->message);
        $return->data = $response->data;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $response->errors;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getCampaigns($caller)
  {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->merchantId = sanitize_text_field($this->TVC_Admin_Helper->get_merchantId());
    $this->customerId = sanitize_text_field($this->TVC_Admin_Helper->get_currentCustomerId());
    try {
      $url = $this->apiDomain . '/campaigns/list';

      $data = [
        'merchant_id' => sanitize_text_field($this->merchantId),
        'customer_id' => sanitize_text_field($this->customerId),
        'caller' => $caller
      ];
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($data)
      );

      // Send remote request
      $request = wp_remote_post(esc_url_raw($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));

      if ((isset($response_body->error) && $response_body->error == '')) {

        return new WP_REST_Response(
          array(
            'status' => $response_code,
            'message' => esc_attr($response_message),
            'data' => $response_body->data
          )
        );
      } else {
        return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getAnalyticsAccountList($caller, $postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/google-analytics/ga-account-list';
      $max_results = 100;
      $page = (isset($postData['page']) && sanitize_text_field($postData['page']) > 1) ? sanitize_text_field($postData['page']) : "1";
      if ($page > 1) {
        //set index
        $page = (($page - 1) * $max_results) + 1;
      }
      $data = [
        'page' => sanitize_text_field($page),
        'max_results' => sanitize_text_field($max_results),
        'store_id' => $this->conv_get_store_id(),
        'subscription_id' => $google_detail['setting']->id,
        'caller' => $caller
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = isset($response->data) ? $response->data : "";
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getAnalyticsWebProperties($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/google-analytics/wep-details/account-id';
      $data = [
        'type' => sanitize_text_field($postData['type']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'caller' => sanitize_text_field($postData['caller']),
        'store_id' => $this->conv_get_store_id(),
        'subscription_id' => $google_detail['setting']->id
      ];
      if ($data['subscription_id'] == '' || $data['store_id'] == '' || $data['account_id'] == '' || $data['type'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json'
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = ($response->data) ? $response->data : "";
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function listMerchantCenterAccount($caller)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $data["store_id"] = $this->conv_get_store_id();
    $data["subscription_id"] = $google_detail['setting']->id;
    $data['caller'] = $caller;
    try {
      $url = $this->apiDomain . '/gmc/user-merchant-center/list';
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      if ($data['subscription_id'] == '' || $data['store_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);
      $return = new \stdClass();
      if (isset($result->status) && $result->status == 200) {
        $return->status = $result->status;
        $return->data = isset($result->data) ? $result->data : '';
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = isset($result->data) ? $result->data : '';
        $return->status = isset($result->status) ? $result->status : '';
        $return->errors = wp_json_encode($result->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function listMerchantCenterAccountMicrosoft($caller, $account_id, $subaccount_id, $subscription_id)
  {
    try {
      $url = $this->apiDomain . '/microsoft/getStores';
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      $data = [
        'customer_subscription_id' => sanitize_text_field($subscription_id),
        'customer_id' => sanitize_text_field($subaccount_id),
        'account_id' => sanitize_text_field($account_id),
        'caller' => $caller
      ];
      if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '' || $data['account_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);
      $return = new \stdClass();
      if (isset($result->status) && $result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = isset($result->data) ? $result->data : '';
        $return->status = isset($result->status) ? $result->status : '';
        $return->errors = wp_json_encode($result->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function listMerchantCatalogAccountMicrosoft($caller, $account_id, $subaccount_id, $subscription_id, $microsoft_merchant_center_id)
  {
    try {
      $url = $this->apiDomain . '/microsoft/getCatalogs';
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      $data = [
        'customer_subscription_id' => sanitize_text_field($subscription_id),
        'customer_id' => sanitize_text_field($subaccount_id),
        'account_id' => sanitize_text_field($account_id),
        'caller' => $caller,
        'merchant_id' => sanitize_text_field($microsoft_merchant_center_id)
      ];
      if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '' || $data['account_id'] == '' || $data['merchant_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);
      $return = new \stdClass();
      if (isset($result->status) && $result->status == 200) {
        $return->status = $result->status;
        $return->data = isset($result->data) ? $result->data : '';
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = isset($result->data) ? $result->data : '';
        $return->status = isset($result->status) ? $result->status : '';
        $return->errors = wp_json_encode($result->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function createMerchantAccount($postData)
  {
    try {
      $url = $this->apiDomain . '/gmc/create';
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $data = [
        'merchant_id' => sanitize_text_field($this->mcamerchantId), //'256922349',
        'name' => sanitize_text_field($postData['store_name']),
        'website_url' => esc_url(sanitize_text_field($postData['website_url'])),
        'customer_id' => sanitize_text_field($postData['customer_id']),
        'adult_content' => isset($postData['adult_content']) && sanitize_text_field($postData['adult_content']) == 'true' ? true : false,
        'country' => sanitize_text_field($postData['country']),
        'caller' => sanitize_text_field($postData['caller']),
        'users' => [
          [
            "email_address" => sanitize_email($postData['email_address']), //"sarjit@pivotdrive.ca"
            "admin" => true
          ]
        ],
        'business_information' => [
          'address' => [
            'country' => sanitize_text_field($postData['country'])
          ]
        ]
      ];
      if ($data['merchant_id'] == '' || $data['name'] == '' || $data['website_url'] == '' || $data['customer_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $args['timeout'] = "1000";
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));
      if ((isset($response_body->error) && $response_body->error == '') || (!isset($response_body->error))) {
        //create merchant account admin notices
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $link_title = "Create Performance max campaign now.";
        $content = "Create your first Google Ads performance max campaign using the plugin and get $500 as free credits.";
        $status = "1";
        $created_merchant_id = $response_body->account->id;
        $link = "admin.php?page=conversios-pmax";
        $TVC_Admin_Helper->tvc_add_admin_notice("created_merchant_account", $content, $status, $link_title, $link, $created_merchant_id, "", "7", "created_merchant_account");
        return $response_body;
      } else {
        $return = new \stdClass();
        $return->error = true;
        $return->errors = isset($response_code->errors) ? wp_json_encode($response_code->errors) : '';
        //$return->data = $result->data;
        $return->status = $response_code;
        return $return;
        //return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function createMerchantAccountMicrosoft($postData)
  {
    try {
      $url = $this->apiDomain . '/microsoft/createStore';
      $header = array(
        "Authorization: Bearer MTIzNA==",
        "Content-Type" => "application/json"
      );
      $data = [
        'customer_subscription_id' => isset($postData['customer_subscription_id']) ? sanitize_text_field($postData['customer_subscription_id']) : '',
        'customer_id' => isset($postData['customer_id']) ? sanitize_text_field($postData['customer_id']) : '',
        'account_id' => isset($postData['account_id']) ? sanitize_text_field($postData['account_id']) : '',
        'store_name' => isset($postData['store_name']) ? sanitize_text_field($postData['store_name']) : '',
        'store_url' => isset($postData['store_url']) ? sanitize_url($postData['store_url'], array('http', 'https')) : '',
        'notification_email' => isset($postData['notification_email']) ? sanitize_text_field($postData['notification_email']) : '',
        'country' => isset($postData['country']) ? sanitize_text_field($postData['country']) : '',
        'caller' => isset($postData['caller']) ? sanitize_text_field($postData['caller']) : '',
      ];
      if ($data['customer_id'] == '' || $data['customer_subscription_id'] == '' || $data['account_id'] == '' || $data['store_name'] == '' || $data['store_url'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $args['timeout'] = "1000";
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response_body = json_decode(wp_remote_retrieve_body($request));

      //echo '<pre>'; print_r($args); print_r($response_body); echo '</pre>';

      if ((isset($response_body->error) && $response_body->error == '')) {
        //create merchant account admin notices
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $link_title = "Create Microsoft Ads - Performance max campaign now.";
        $content = "";
        $status = "1";
        $created_merchant_id = isset($response_body->data->merchantId) ? $response_body->data->merchantId : null;
        $link = "admin.php?page=conversios-pmax&campaign=microsoft";
        $TVC_Admin_Helper->tvc_add_admin_notice("created_merchant_account", $content, $status, $link_title, $link, $created_merchant_id, "", "7", "created_merchant_account");
        return $response_body;
      } else {
        $return = new \stdClass();
        $return->error = true;
        if (isset($response_body->errors)) {
          $return->errors = wp_json_encode($response_body->errors);
        } else {
          $return->errors = isset($response_code->errors) ? wp_json_encode($response_code->errors) : '';
        }
        //$return->data = $result->data;
        $return->status = $response_code;
        return $return;
        //return new WP_Error($response_code, $response_message, $response_body);
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function saveMechantData($postData = array())
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/customer-subscriptions/update-detail';
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      $data = [
        'merchant_id' => sanitize_text_field(($postData['merchant_id'] == 'NewMerchant') ? $this->mcamerchantId : $postData['merchant_id']),
        'subscription_id' => sanitize_text_field((isset($postData['subscription_id'])) ? $postData['subscription_id'] : ''),
        'google_merchant_center_id' => sanitize_text_field((isset($postData['google_merchant_center'])) ? $postData['google_merchant_center'] : ''),
        'website_url' => sanitize_text_field($postData['website_url']),
        'customer_id' => sanitize_text_field($postData['customer_id']),
        'caller' => sanitize_text_field($postData['caller']),
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['store_id'] == '' || $data['subscription_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);
      $return = new \stdClass();
      if ($result->status == 200) {
        $return->status = $result->status;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->data = $result->data;
        $return->status = $result->status;
        $return->errors = wp_json_encode($result->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function linkGoogleAdsToMerchantCenter($postData)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    try {
      $url = $this->apiDomain . '/adwords/link-ads-to-merchant-center';
      $data = [
        'merchant_id' => sanitize_text_field(($postData['merchant_id']) == 'NewMerchant' ?  $this->mcamerchantId : $postData['merchant_id']),
        'account_id' => sanitize_text_field($postData['account_id']),
        'adwords_id' => sanitize_text_field($postData['adwords_id']),
        'subscription_id' => sanitize_text_field($postData['subscription_id']),
        'caller' => sanitize_text_field($postData['caller']),
        'store_id' => $this->conv_get_store_id()
      ];
      if ($data['merchant_id'] == '' || $data['account_id'] == '' || $data['adwords_id'] == '' || $data['store_id'] == '' || $data['subscription_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer $this->token",
          'Content-Type' => 'application/json'
        ),
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );

      // Send remote request
      $request = wp_remote_post(esc_url($url), $args);
      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $result = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if ($response_code == 200) {
        $return->status = $response_code;
        $return->data = $result->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        $return->errors = $result->errors;
        $return->status = $response_code;
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getMicrosoftAdsAccountList($caller, $postData = array())
  {

    try {

      $url = $this->apiDomain . '/microsoft/getManagerAccounts';
      //$refresh_token = sanitize_text_field(base64_decode($this->refresh_token));
      $data = [
        'customer_subscription_id' => sanitize_text_field((isset($postData['subscription_id'])) ? $postData['subscription_id'] : ''),
        'caller' => $caller,
      ];
      if ($data['customer_subscription_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
          //'RefreshToken' => $refresh_token
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->data = $response->data;
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getMicrosoftAdsSubAccountList($caller, $postData, $account_id)
  {

    try {

      $url = $this->apiDomain . '/microsoft/getAccounts';
      //$refresh_token = sanitize_text_field(base64_decode($this->refresh_token));
      $data = [
        'customer_subscription_id' => sanitize_text_field((isset($postData['subscription_id'])) ? $postData['subscription_id'] : ''),
        'customer_id' => sanitize_text_field($account_id),
        'caller' => $caller,
      ];
      if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
          //'RefreshToken' => $refresh_token
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->data = $response->data;
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function getMicrosoftAdsGetUET($caller, $postData, $account_id, $subaccount_id)
  {

    try {

      $url = $this->apiDomain . '/microsoft/getUetTagsByIds';
      //$refresh_token = sanitize_text_field(base64_decode($this->refresh_token));
      $data = [
        'customer_subscription_id' => sanitize_text_field((isset($postData['subscription_id'])) ? $postData['subscription_id'] : ''),
        'customer_id' => sanitize_text_field($account_id),
        'account_id' => sanitize_text_field($subaccount_id),
        'caller' => $caller,
      ];
      if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '' || $data['account_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
          //'RefreshToken' => $refresh_token
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->data = $response->data;
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function CreateMicrosoftAdsUET($caller, $subscription_id, $account_id, $subaccount_id)
  {

    try {

      $url = $this->apiDomain . '/microsoft/createUetTags';
      //$refresh_token = sanitize_text_field(base64_decode($this->refresh_token));
      $data = [
        'customer_subscription_id' => sanitize_text_field($subscription_id),
        'customer_id' => sanitize_text_field($account_id),
        'account_id' => sanitize_text_field($subaccount_id),
        'caller' => $caller,
      ];
      if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '' || $data['account_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
          //'RefreshToken' => $refresh_token
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->data = $response->data;
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function CreateMicrosoftAdsAccount($caller, $subscription_id, $account_name, $currency_code, $time_zone, $tax_info_key, $tax_info_val, $sub_account_name, $market_country, $market_language, $bussiness_name, $address_1, $address_2, $city, $state, $postal_code)
  {

    try {

      $url = $this->apiDomain . '/microsoft/customerSignup';
      //$refresh_token = sanitize_text_field(base64_decode($this->refresh_token));
      $data = [
        'customer_subscription_id' => sanitize_text_field($subscription_id),
        'caller' => $caller,
        'account' => [
          'name' => sanitize_text_field($account_name),
          'currency_code' => sanitize_text_field($currency_code),
          'time_zone' => sanitize_text_field($time_zone),
          'tax_info_key' => sanitize_text_field($tax_info_key),
          'tax_info_val' => sanitize_text_field($tax_info_val),
        ],
        'customer' => [
          'name' => sanitize_text_field($sub_account_name),
          'market_country' => sanitize_text_field($market_country),
          'market_language' => sanitize_text_field($market_language)
        ],
        'address' => [
          'name' => sanitize_text_field($bussiness_name),
          'line1' => sanitize_text_field($address_1),
          'line2' => sanitize_text_field($address_2),
          'city' => sanitize_text_field($city),
          'state' => sanitize_text_field($state),
          'postal_code' => sanitize_text_field($postal_code),
          'country_code' => sanitize_text_field($market_country)
        ]
      ];
      // if ($data['customer_subscription_id'] == '' || $data['customer_id'] == '' || $data['account_id'] == '') {
      //   $return = new \stdClass();
      //   $return->error = true;
      //   $return->conv_param_error = 'Required parameters are missing.';
      //   $return->conv_data = $data;
      //   $return->status = 400;
      //   return $return;
      // }
      $args = array(
        'timeout' => 300,
        'headers' => array(
          'Authorization' => "Bearer MTIzNA==",
          'Content-Type' => 'application/json',
          //'RefreshToken' => $refresh_token
        ),
        'body' => wp_json_encode($data)
      );
      $request = wp_remote_post(esc_url($url), $args);

      //echo '<pre>'; print_r($data); echo '</pre>';

      // Retrieve information
      $response_code = wp_remote_retrieve_response_code($request);
      $response_message = wp_remote_retrieve_response_message($request);
      $response = json_decode(wp_remote_retrieve_body($request));
      $return = new \stdClass();
      if (isset($response->error) && $response->error == '') {
        $return->status = $response_code;
        $return->data = $response->data;
        $return->error = false;
        return $return;
      } else {
        $return->error = true;
        //$return->data = $response->data;
        $return->status = $response_code;
        $return->errors = wp_json_encode($response->errors);
        return $return;
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function gaDimension($caller)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $data["store_id"] = $this->conv_get_store_id();
    $data["subscription_id"] = $google_detail['setting']->id;
    $data['caller'] = $caller;
    try {
      $url = $this->apiDomain . '/google-analytics/dimensions/insert';
      if ($data['subscription_id'] == '' || $data['store_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $data;
        $return->status = 400;
        return $return;
      }
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);
      $return = new \stdClass();
      return $return;
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function additional_dimensions($data)
  {
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $google_detail = $TVC_Admin_Helper->get_ee_options_data();
    $formatted_data = array(
      "subscription_id" => intval($google_detail['setting']->id),
      "store_id" => $this->conv_get_store_id(),
      "caller" => intval($data['caller']),
      "additional_dimension" => array(
        "conv_track_page_scroll" => intval($data['conv_track_page_scroll']),
        "conv_track_file_download" => intval($data['conv_track_file_download']),
        "conv_track_author" => intval($data['conv_track_author']),
        "conv_track_signin" => intval($data['conv_track_signin']),
        "conv_track_signup" => intval($data['conv_track_signup']),
      )
    );
    try {
      $url = $this->apiDomain . '/google-analytics/additional/dimensions/insert';
      if ($formatted_data['subscription_id'] == '' || $formatted_data['store_id'] == '') {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $formatted_data;
        $return->status = 400;
        return $return;
      }
      $header = array("Authorization: Bearer MTIzNA==", "Content-Type" => "application/json");
      $args = array(
        'timeout' => 300,
        'headers' => $header,
        'method' => 'POST',
        'body' => wp_json_encode($formatted_data)
      );
      $result = $this->tc_wp_remot_call_post(esc_url($url), $args);


      $return = new \stdClass();
      return $return;
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
  public function createNewSubscription()
  {
    try {
      $conv_originalsiteurl = "";
      if (is_multisite()) {
        $conv_originalsiteurl = get_site_url();
      } else {
        remove_all_filters('option_siteurl');
        $conv_originalsiteurl = get_option('siteurl');
      }
      $eeOptionsSettings = unserialize(get_option('ee_options'));
      if (!is_array($eeOptionsSettings)) {
        $eeOptionsSettings = array();
      }
      $eeApiSettings = unserialize(get_option('ee_api_data'));
      $apiDomain = TVC_API_CALL_URL_TEMP . '/api/v2';
      $current_user = wp_get_current_user();
      if (! ($current_user instanceof WP_User) || empty($current_user->user_email)) {
        return;
      }
      // Update token to subs
      $url = $apiDomain . '/customer-subscriptions/update-token';
      $header = array("Authorization: Bearer MTIzNA==", "content-type: application/json");
      $postData = [
        'app_id' =>  CONV_APP_ID,
        'platform_id' => 1,
        'domain' =>  $conv_originalsiteurl ?: '',
        'gmail' => !empty($current_user->user_email) ? $current_user->user_email : '',
        'caller' => "create_new_subscription",
        'first_name' => "",
        'last_name' => "",
      ];
      $postData['current_subscription_id'] = $eeApiSettings['setting']->subscription_id ?? "";
      $postData['current_store_id'] = $eeApiSettings['setting']->store_id ?? "";

      // Validate
      if (
        empty($postData['domain']) ||
        empty($postData['gmail'])
      ) {
        $return = new \stdClass();
        $return->error = true;
        $return->conv_param_error = 'Required parameters are missing.';
        $return->conv_data = $postData;
        $return->status = 400;
        return $return;
      }


      $args = array(
        'headers' => $header,
        'method' => 'POST',
        "timeout" => 1000,
        'body' => $postData
      );
      $request = wp_remote_post(esc_url_raw($url), $args);
      $updatetokenResponse = json_decode(wp_remote_retrieve_body($request));

      if (
        isset($updatetokenResponse->error) &&
        $updatetokenResponse->error == true &&
        isset($updatetokenResponse->errors) &&
        is_object($updatetokenResponse->errors) &&
        isset($updatetokenResponse->errors->domain[0]) &&
        $updatetokenResponse->errors->domain[0] === 'This domain is blocked'
      ) {
        update_option('conv_localhost_error', true);
      }

    
      if (isset($updatetokenResponse->error) && $updatetokenResponse->error == true) {
        return;
      }

      if (isset($updatetokenResponse->error) && $updatetokenResponse->error == false && $updatetokenResponse->data) {
        $postData = [];
        $domain = $updatetokenResponse->data->domain;
        $storeId = $updatetokenResponse->data->store_id;
        $subscriptionId = $updatetokenResponse->data->customer_subscription_id;

        $postData = [
          'tracking_option' => $eeApiSettings['setting']->tracking_option ?? "",
          'measurement_id' => $eeApiSettings['setting']->measurement_id ?? "",
          'link_google_analytics_with_google_ads' => $eeApiSettings['setting']->link_google_analytics_with_google_ads ?? "",
          'ga4_analytic_account_id' => $eeApiSettings['setting']->ga4_analytic_account_id ?? "",
          'property_id' => $eeApiSettings['setting']->property_id ?? "",
          'ua_analytic_account_id' => $eeApiSettings['setting']->ua_analytic_account_id ?? "",
          'google_ads_id' => $eeApiSettings['setting']->google_ads_id ?? "",
          'google_merchant_center_id' => $eeApiSettings['setting']->google_merchant_center_id ?? "",
          'merchant_id' => $eeApiSettings['setting']->merchant_id ?? "",
          'website_url' => $eeApiSettings['setting']->website_url ?? "",
          'enhanced_e_commerce_tracking' => $eeApiSettings['setting']->enhanced_e_commerce_tracking ?? "",
          'user_time_tracking' => $eeApiSettings['setting']->user_time_tracking ?? "",
          'add_gtag_snippet' => $eeApiSettings['setting']->add_gtag_snippet ?? "",
          'client_id_tracking' => $eeApiSettings['setting']->add_gtag_snippet ?? "",
          'exception_tracking' => $eeApiSettings['setting']->exception_tracking ?? "",
          'enhanced_link_attribution_tracking' => $eeApiSettings['setting']->enhanced_link_attribution_tracking ?? "",
          'remarketing_tags' => $eeApiSettings['setting']->remarketing_tags ?? "",
          'dynamic_remarketing_tags' => $eeApiSettings['setting']->dynamic_remarketing_tags ?? "",
          'google_ads_conversion_tracking' => $eeApiSettings['setting']->google_ads_conversion_tracking ?? "",
          'caller' => "create_new_subscription",
        ];

        //update subscription
        $url = $apiDomain . '/customer-subscriptions/update-detail';
        $data = array();
        foreach ($postData as $key => $value) {
          $data[$key] = sanitize_text_field((isset($value)) ? $value : '');
        }
        $data['store_id'] = $storeId;
        $data["subscription_id"] = $subscriptionId;
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => $data
        );

        $this->tc_wp_remot_call_post(esc_url_raw($url), $args);

        update_option("conv_active_domain", base64_encode($domain));

        $eeOptionsSettings['subscription_id'] = $subscriptionId;
        update_option("ee_options", serialize($eeOptionsSettings));

        if (!isset($eeApiSettings['setting']) || !is_object($eeApiSettings['setting'])) {
          $eeApiSettings['setting'] = new stdClass();
        }

        $eeApiSettings['setting']->store_id = $storeId;
        $eeApiSettings['setting']->subscription_id = $subscriptionId;
        update_option("ee_api_data", serialize($eeApiSettings));

        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status("create_new_subscription");
        $TVC_Admin_Helper->app_activity_detail("create_new_subscription", "activate");

        //Get subscription details
        $url = $apiDomain . '/customer-subscriptions/subscription-detail';
        $postData = [
          'subscription_id' => $subscriptionId,
          'domain' => $conv_originalsiteurl,
          'app_id' => CONV_APP_ID,
          'platform_id' => 1,
          'caller' => "create_new_subscription",
        ];
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          "timeout" => 1000,
          'body' => $postData
        );
        $request = wp_remote_post(esc_url_raw($url), $args);
        $detailResponse = json_decode(wp_remote_retrieve_body($request));
        $eeapidata = array("setting" => $detailResponse->data);
        update_option("ee_api_data", serialize($eeapidata));
        if (CONV_IS_WC) {
          $feed_data_list = $TVC_Admin_Helper->ee_get_results('ee_product_feed');
          $delay_minutes = 5;
          if (!empty($feed_data_list) && is_array($feed_data_list)) {
            as_unschedule_all_actions('init_feed_wise_product_sync_process_scheduler_ee');
            foreach ($feed_data_list as $feed) {
              $delay_seconds = $delay_minutes * 60;
              $store_feed_id   = $feed->id;
              $channel_ids     = isset($feed->channel_ids) ? $feed->channel_ids : '';
              $interval        = isset($feed->auto_sync_interval) ? $feed->auto_sync_interval : '';
              $mappedAttrsjson = isset($feed->attributes) ? $feed->attributes : '';
              $feed_MappedCatjson = isset($feed->categories) ? $feed->categories : '';
              $filtersjson     = isset($feed->filters) ? $feed->filters : '';
              $feed_data_api = array(
                "store_id"         => $storeId,
                "store_feed_id"    => $store_feed_id,
                "map_categories"   => $feed_MappedCatjson,
                "map_attributes"   => $mappedAttrsjson,
                "filter"           => $filtersjson,
                "include"          => !empty($feed->include_product) ? $feed->include_product : '',
                "exclude"          => !empty($feed->exclude_product) ? $feed->exclude_product : '',
                "channel_ids"      => $channel_ids,
                "interval"         => $interval,
                "tiktok_catalog_id" => isset($feed->tiktok_catalog_id) ? $feed->tiktok_catalog_id : '',
                "caller"           => "create_new_subscription"
              );

              $TVC_Admin_Helper->plugin_log("Sending feed ID {$store_feed_id} data to API", 'product_sync');
              $this->ee_create_product_feed($feed_data_api);
              $TVC_Admin_Helper->plugin_log("Feed {$store_feed_id} created successfully in middleware. Scheduling sync after {$delay_minutes} minutes.", 'product_sync');
              as_schedule_single_action(
                time() + $delay_seconds,
                'init_feed_wise_product_sync_process_scheduler_ee',
                array("feedId" => $store_feed_id),
                "product_sync"
              );
              $delay_minutes += 10;
            }
            $TVC_Admin_Helper->plugin_log("All feeds processed successfully", 'product_sync');
          } else {
            $TVC_Admin_Helper->plugin_log("No feeds found in DB to sync", 'product_sync');
          }
        }
      }
    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
}
