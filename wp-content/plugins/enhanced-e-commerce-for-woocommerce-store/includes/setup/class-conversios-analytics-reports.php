<?php
$TVC_Admin_Helper = new TVC_Admin_Helper();
$ee_options = unserialize(get_option('ee_options'));
$sch_email_toggle_check = isset($ee_options['sch_email_toggle_check']) ? sanitize_text_field($ee_options['sch_email_toggle_check']) : '1';
$sch_custom_email = isset($ee_options['sch_custom_email']) ? sanitize_text_field($ee_options['sch_custom_email']) : '';
$sch_email_frequency = isset($ee_options['sch_email_frequency']) ? sanitize_text_field($ee_options['sch_email_frequency']) : 'Weekly';
$g_mail = get_option('ee_customer_gmail');
$ga4_measurement_id = isset($ee_options['gm_id']) && $ee_options['gm_id'] != "" ? $ee_options['gm_id'] : "";
$ga4_analytic_account_id = isset($ee_options['ga4_analytic_account_id']) && $ee_options['ga4_analytic_account_id'] != "" ? $ee_options['ga4_analytic_account_id'] : "";
$google_ads_id = isset($ee_options['google_ads_id']) && $ee_options['google_ads_id'] != "" ? $ee_options['google_ads_id'] : "";
$last_fetched_prompt_date = isset($ee_options['last_fetched_prompt_date']) && $ee_options['last_fetched_prompt_date'] != "" ? $ee_options['last_fetched_prompt_date'] : "";
$ecom_reports_ga_currency = isset($ee_options['ecom_reports_ga_currency']) ? sanitize_text_field($ee_options['ecom_reports_ga_currency']) : '';
$ecom_reports_gads_currency = isset($ee_options['ecom_reports_gads_currency']) ? sanitize_text_field($ee_options['ecom_reports_gads_currency']) : '';
$connect_url = $TVC_Admin_Helper->get_custom_connect_url_wizard(admin_url() . 'admin.php?page=conversios-analytics-reports');
$subpage = (isset($_GET["subpage"]) && $_GET["subpage"] != "") ? sanitize_text_field(wp_unslash($_GET['subpage'])) : "ga4general";

$options = get_option("ee_options");
if ($options) {
    $options = is_array($options) ? $options : unserialize($options);
    if (!isset($options['save_email_bydefault'])) {
        $options['save_email_bydefault'] = null;
        update_option('ee_options', serialize($options));
    }
}
$report_settings_arr = array("ga4ecommerce", "gads", "ga4general");
if ($subpage == "ga4ecommerce") {
    $ga4page_cls = "btn-outline-primary";
    $gadspage_cls = "btn-outline-secondary alt-btn-reports";
    $ga4general_cls = "btn-outline-secondary alt-btn-reports";
} else if ($subpage == "gads") {
    $ga4page_cls = "btn-outline-secondary alt-btn-reports";
    $gadspage_cls = "btn-outline-primary";
    $ga4general_cls = "btn-outline-secondary alt-btn-reports";
} else if ($subpage == "ga4general") {
    $ga4page_cls = "btn-outline-secondary alt-btn-reports";
    $gadspage_cls = "btn-outline-secondary alt-btn-reports";
    $ga4general_cls = "btn-outline-primary";
}
if (isset($_GET['subscription_id']) && isset($_GET['g_mail'])) {
    $g_mail = sanitize_email($_GET['g_mail']);
    update_option('ee_customer_gmail', $g_mail);
}
?>
<style>
    ol,
    ul {
        padding-left: 0rem;
    }

    .big-checkbox {
        transform: scale(1.3);
        /* makes checkbox bigger */
        margin-right: 8px;
    }

    #configurationMessage {
        font-size: 13px;
        padding: 4px 8px;
        margin: 5px 0;
        line-height: 1.3;
    }
</style>
<div id="conv-report-main-div" class="container-fluid conv_report_mainbox p-4">

    <div class="row">
        <div class="d-flex">
            <div class="conv_pageheading d-flex align-items-end">
                <h2>
                    <?php esc_html_e("Analytics reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </h2>
                <h5 id="conv_pdf_logo" class="d-none ms-2">by <?php echo wp_kses(
                                                                    enhancad_get_plugin_image('/admin/images/logo.png', '', '', 'width:120px;'),
                                                                    array(
                                                                        'img' => array(
                                                                            'src' => true,
                                                                            'alt' => true,
                                                                            'class' => true,
                                                                            'style' => true,
                                                                        ),
                                                                    )
                                                                ); ?></h5>
            </div>
            <div class="ms-auto p-2 bd-highlight">
                <div id="reportrange" class="dshtpdaterange upgradetopro_badge d-flex" popupopener="generalreport">
                    <div class="dateclndicn">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/claendar-icon.png'),
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
                    <span class="daterangearea report_range_val"></span>
                    <div class="careticn">
                        <?php echo wp_kses(
                            enhancad_get_plugin_image('/admin/images/caret-down.png'),
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
            </div>
        </div>

        <div class="d-flex">

            <div class="conv_pageheading">
                <a href="admin.php?page=conversios-analytics-reports" class="btn <?php echo esc_attr($ga4general_cls); ?> bg-white me-3">
                    <?php esc_html_e("General Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($ga4page_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Ecommerce Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($gadspage_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Google Ads Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
                <a class="btn <?php echo esc_attr($gadspage_cls); ?> bg-white me-3" data-bs-toggle="modal" data-bs-target="#upgradetopromodalotherReports">
                    <?php esc_html_e("Facebook (Meta) Reports", "enhanced-e-commerce-for-woocommerce-store") ?>
                </a>
            </div>
            <?php if ($ga4_measurement_id != "" && $g_mail != "") { ?>
                <div id="conv_report_opright" class="ms-auto p-2 bd-highlight d-flex">
                    <h4 class="conv-link-blue d-flex pe-2" data-bs-toggle="modal" data-bs-target="#schedule_email_modal">
                        <span class="material-symbols-outlined conv-link-blue pe-1">check_circle</span>
                        <?php esc_html_e("Schedule Email", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </h4>
                    <h4 class="conv-link-blue d-flex" data-bs-toggle="modal" data-bs-target="#convpdflogoModal">
                        <span class="material-symbols-outlined conv-link-blue pe-1">cloud_download</span>
                        <?php esc_html_e("Download PDF", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </h4>
                </div>
            <?php } ?>
        </div>

        <?php if ($subpage == "ga4general" && (empty($g_mail) || empty($ga4_analytic_account_id))) { ?>
            <div class="alert alert-info mt-4 w-100" role="alert">
                <div class="mx-auto" style="max-width: 600px;">
                    <h5 class="alert-heading">Connect Google Analytics to View Reports</h5>
                    <p>To view reports in the plugin, please connect your Google account and complete the Google Analytics setup:</p>
                    <ol class="ms-0">
                        <li>Click the button below to start the connection.</li>
                        <li>Authorize access through the Google authentication screen.</li>
                        <li>Select your <strong>Google Analytics Account ID</strong>.</li>
                        <li>Choose your <strong>Measurement ID</strong> and click <strong>Save</strong>.</li>
                        <li>After saving, a success message will appear. Click <strong>"View Reports"</strong> to access your analytics.</li>
                    </ol>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#prega4AuthModal">
                            Click here to connect Google
                        </button>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="gadetails" style="padding: 16px 11px;background-color: #f0f0f1;font-size: medium;">
                <div style="display: flex; flex-wrap: wrap; align-items: center;">
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Google Analytics Account ID :</strong>
                        <?php echo !empty($ga4_analytic_account_id) ? esc_attr($ga4_analytic_account_id) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Google Analytics Measurement ID :</strong>
                        <?php echo !empty($ga4_measurement_id) ? esc_attr($ga4_measurement_id) : '-'; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-bottom: 10px; margin-right: 30px;">
                        <strong>Status:</strong>
                        <span style="margin-left: 6px; color: <?php echo !empty($ga4_measurement_id) ? 'green' : 'red'; ?>;">
                            <?php echo !empty($ga4_measurement_id) ? 'Connected' : 'Not Connected'; ?>
                        </span>
                    </div>
                    <div style="text-align: right; margin-bottom: 10px; margin-right: 30px;">
                        <button id="opengasettings" style="padding: 4px 7px; background-color: #1967D2; border: none; color: white; border-radius: 4px; cursor: pointer;">
                            Edit Details
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="modal fade" id="prega4AuthModal" tabindex="-1" aria-labelledby="prega4AuthModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow rounded-3">
                    <div class="modal-header border-bottom-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left Column: Google Button -->
                            <div class="col-md-5 d-flex align-items-center justify-content-center mb-4 mb-md-0">
                                <?php if ($g_mail == "") { ?>
                                    <button id="googleSignInBtn" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 shadow-sm google_connect_url">
                                        <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/g-logo.png'); ?>" alt="Google Logo" width="20" height="20">
                                        <span class="fw-semibold">Sign in with Google</span>
                                    </button>
                                <?php  } else { ?>
                                    <button id="googleSignInBtn" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 shadow-sm google_connect_url">
                                        <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/g-logo.png'); ?>" alt="Google Logo" width="20" height="20">
                                        <span class="fw-semibold">Reauthorize</span>
                                    </button>
                                <?php } ?>
                            </div>

                            <!-- Right Column: Why we need it -->
                            <div class="col-md-7">
                                <p class="mb-2 h4"><strong>Why do we need your permission?</strong></p>
                                <ul class="mb-0">
                                    <li class="pt-2"><strong>Access to Google Analytics 4 (GA4):</strong> We use your GA4 data to generate intelligent profit predictions based on your traffic, conversion, and revenue metrics.</li>
                                    <li class="pt-2">Your data is used only to show you insights <br> we never store or share your analytics data with anyone.</li>
                                    <li class="pt-2">You can revoke access at any time by visiting your <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener noreferrer">Google account permissions</a>.</li>
                                </ul>

                                <p class="text-danger text-fw-bold">Please use Chrome browser if you face any issues during setup.</p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ga4Modal" tabindex="-1" aria-labelledby="ga4ModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false"> <!-- prevents closing on outside click or Esc -->
            <div class="modal-dialog modal-dialog-centered mt-5">
                <div class="modal-content shadow rounded-4 border-0">

                    <!-- Header with Logo + Close button -->
                    <div class="modal-header border-bottom-0 flex-column text-center bg-light pt-4 pb-3 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                        <img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_ganalytics_logo.png'); ?>"
                            alt="GA4 Logo" width="48" height="48" class="mb-2">
                        <h5 class="modal-title fw-semibold" id="ga4ModalLabel">Connect Your GA4 Property</h5>
                    </div>

                    <div class="modal-body px-4 pt-4">
                        <div id="ga4ErrorMessage" class="alert alert-danger d-none" role="alert"></div>
                        <div style="display: flex; align-items: center; margin-bottom: 10px; justify-content: center;" class="alert alert-info">
                            <strong>Successfully logged in with:</strong>
                            <span style="margin-left: 6px;"><?php echo !empty($g_mail) ? esc_attr($g_mail) : '-'; ?></span>
                            <span class="conv-link-blue ps-0 ms-2 tvc_google_signinbtn">
                                <?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </span>
                        </div>
                        <div id="configurationMessage"
                            class="alert alert-danger d-none p-1 small mx-1 mb-4"
                            role="alert">
                            To view reports, please configure the following and save.
                        </div>
                        <form id="ga4SelectionForm" style="margin: 0px 30px;">

                            <!-- GA4 Account -->
                            <div class="mb-4 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <label for="ga4_analytic_account_id" class="form-label fw-bolder">GA4 Account</label>
                                    <select class="form-select" id="ga4_analytic_account_id" name="ga4_account">
                                        <option value="">-- Select Account --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- GA4 Property -->
                            <div class="mb-4 d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <label for="measurement_id" class="form-label fw-bolder">GA4 Measurement ID</label>
                                    <select class="form-select" id="measurement_id" name="ga4_property">
                                        <option value="">-- Select Measurement ID --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Confirmation Checkbox -->
                            <div class="mb-3 form-check ms-1">
                                <input type="checkbox" class="form-check-input big-checkbox" id="ga4midconfirm">
                                <label class="form-check-label" for="ga4midconfirm" id="ga4ConfirmLabel">
                                    Events will be tracked using the selected GA4 Measurement ID, and the in-built reports will reflect data from the same GA4 account.
                                </label>
                            </div>

                        </form>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4">
                        <button id="savereportsettings" type="button" class="btn btn-primary w-100 py-2" disabled>
                            <span class="d-inline-flex align-items-center">
                                Save
                                <div class="spinner-border text-light spinner-border-sm ms-2 d-none" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const accountSelect = document.getElementById("ga4_analytic_account_id");
                const propertySelect = document.getElementById("measurement_id");
                const confirmCheckbox = document.getElementById("ga4midconfirm");
                const saveButton = document.getElementById("savereportsettings");

                function toggleSaveButton() {
                    const accountSelected = accountSelect.value.trim() !== "";
                    const propertySelected = propertySelect.value.trim() !== "";
                    const confirmed = confirmCheckbox.checked;

                    saveButton.disabled = !(accountSelected && propertySelected && confirmed);
                }

                accountSelect.addEventListener("change", toggleSaveButton);
                propertySelect.addEventListener("change", toggleSaveButton);
                confirmCheckbox.addEventListener("change", toggleSaveButton);
            });
        </script>


        <?php
        if (in_array($subpage, $report_settings_arr)) {
            require_once(ENHANCAD_PLUGIN_DIR . "admin/partials/reports/" . $subpage . '.php');
        }
        ?>
        <!-- All report section -->

    </div>
</div>
</div>
<!-- logo modal -->
<div class="modal fade" id="convpdflogoModal" tabindex="-1" aria-labelledby="convpdflogoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="convpdflogoModalLabel">Download Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Size Message -->
            <div class="alert alert-success text-center text-underline text-success">
                <a href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=modal_setlogo&utm_campaign=upgrade" target="_blank">
                    Upgrade to Premium to set your logo in report PDF
                </a>
            </div>

            <!-- Modal Body -->
            <div class="modal-body d-flex justify-content-center align-items-center flex-column disabledsection">
                <!-- Image Preview Container -->
                <div id="image-preview-container" class="border d-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 36px; background-color: #f8f9fa;">
                    <span id="no-image-text" class="text-muted small">No image selected</span>
                    <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/claendar-icon.png', 'Selected Media Preview', 'd-none img-fluid', 'max-width: 120px; max-height: 36px;', 'selected-media-preview'),
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

                <!-- Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <button id="select-media-button" class="btn btn-outline-primary me-2">
                        <i class="bi bi-upload"></i> Select Logo
                    </button>
                    <input type="hidden" id="attachment-id" name="attachment_id" value="">
                    <button id="save-logo-button" class="btn btn-success">
                        <i class="bi bi-save"></i> Save
                    </button>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" id="conv-download-pdf" class="btn btn-primary w-100">
                    <i class="bi bi-file-earmark-pdf"></i>Download Now
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Schedule Email Modal box -->
<div class="modal email-modal fade" id="schedule_email_modal" tabindex="-1" aria-labelledby="schedule_email_modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div id="loadingbar_blue" class="progress-materializecss" style="display: none;">
            <div class="indeterminate"></div>
        </div>
        <div class="modal-content">
            <div class="modal-body">
                <div class="scheduleemail-box">
                    <h2><?php esc_html_e("Smart Emails", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                    <p>
                        <?php esc_html_e("Schedule your Google Analytics 4 Insight Report email for", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <br>
                        <?php esc_html_e("data-driven insights", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <?php
                    if ($sch_email_toggle_check == '0') { //enabled
                        $switch_cls = 'convEmail_default_cls_enabled';
                        $switch_checked = 'checked';
                        $txtcls = "form-fields-dark";
                    } else { //disabled
                        $switch_cls = 'convEmail_default_cls_disabled';
                        $switch_checked = '';
                        $txtcls = "form-fields-light";
                    } ?>
                    <div class="schedule-formbox">
                        <div class="toggle-switch">
                            <div class="form-check form-switch">
                                <div class="form-check form-switch">
                                    <label id="email_toggle_btnLabel" for="email_toggle_btn" class="form-check-input switch <?php echo esc_attr($switch_cls); ?>" role="switch">
                                        <input id="email_toggle_btn" type="checkbox" class="<?php echo esc_attr($switch_cls); ?>" <?php echo esc_attr($switch_checked); ?>>
                                        <div></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-wholebox">
                            <div class="form-box">
                                <label for="custom_email" class="form-label llabel"><?php esc_html_e("Email address", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                <input type="email" class="form-control icontrol <?php echo esc_attr($txtcls); ?>" id="custom_email" aria-describedby="emailHelp" placeholder="user@gmail.com" value="<?php echo esc_attr($g_mail); ?>" disabled readonly>
                            </div>
                            <div class="form-box">
                                <h5>
                                    <?php esc_html_e("To get emails on your alternate address. ", "enhanced-e-commerce-for-woocommerce-store"); ?><a style="color:  #1085F1;cursor: pointer;" href="https://www.conversios.io/pricing/?utm_source=EE+Plugin+User+Interface&amp;utm_medium=dashboard&amp;utm_campaign=Upsell+at+Conversios" target="_blank"><?php esc_html_e("Upgrade To Pro", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </h5>
                            </div>
                            <div class="form-box">
                                <label for="email_frequency" class="form-label llabel">
                                    <?php esc_html_e("Email Frequency", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </label>
                                <input type="text" class="form-control icontrol <?php echo esc_attr($txtcls); ?>" id="email_frequency" value="<?php echo esc_attr($sch_email_frequency); ?>" disabled readonly>
                                <div id="email_frequency_arrow" class="down-arrow"></div>
                            </div>

                            <div class="form-box">
                                <h5>
                                    <?php esc_html_e("By default, you will receive a Weekly report in your email inbox.", "enhanced-e-commerce-for-woocommerce-store"); ?><br><?php esc_html_e("To get report ", "enhanced-e-commerce-for-woocommerce-store"); ?><strong>Daily</strong>
                                    . <a href="https://www.conversios.io/pricing/?utm_source=EE+Plugin+User+Interface&amp;utm_medium=dashboard&amp;utm_campaign=Upsell+at+Conversios" target="_blank" style="color:  #1085F1;"><?php esc_html_e("Upgrade To Pro", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </h5>
                            </div>
                            <div class="form-box">
                                <div class="save">
                                    <button id="schedule_email_save_config" class="btn  save-btn"><?php esc_html_e("Save", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                </div>
                            </div>
                            <div class="form-box">
                                <div class="save">
                                    <span id="err_sch_msg" style="display: none;color: red;position: absolute;top: -9px;"><?php esc_html_e("Something went wrong, please try again later.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                </div>
                            </div>

                            <div id="schedule_email_alert" class="d-none">
                                <div class="alert alert-info" role="alert">
                                    <div id="schedule_email_alert_msg"></div>
                                    <div role="button" class="fw-bold pt-3" data-bs-dismiss="modal">Click here to close
                                        the popup</div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!--schedule modal end-->


<div class="modal fade" id="upgradetopromodalotherReports" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="position:relative;border-radius:16px;">
            <div class="modal-body p-4 pb-0">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img width="200" height="200"
                        src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/upgrade-pro-reporting.png'); ?>" />
                    <h2 class="text-fw-bold">Upgrade to Pro Now</h2>
                    <span class="text-secondary text-center">Unlock this premium report with our <span
                            class="fw-bold">Pro version!</span> Upgrade now for comprehensive insights and advanced
                        analytics.</span>
                </div>
            </div>
            <div class="border-0 pb-4 mb-1 pt-4 d-flex flex-row justify-content-center align-items-center p-2">
                <a class="btn bg-white text-black m-auto w-100 mx-2 ms-4 p-2" style="border: 1px solid black;" data-bs-dismiss="modal">
                    <?php esc_html_e("Close", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </a>
                <a id="upgradetopro_modal_link" class="btn conv-yellow-bg m-auto w-100 mx-2 me-4 p-2"
                    href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=modal_popup&utm_campaign=upgrade"
                    target="_blank">
                    <?php esc_html_e("Upgrade Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        var start = moment().subtract(45, 'days');
        var end = moment().subtract(1, 'days');
        var start_date = "";
        var end_date = "";
        <?php if (!$ga4_measurement_id == "" && !empty($g_mail)) { ?>
            cb(start, end);
        <?php } ?>

        const url = window.location.href;
        const params = new URLSearchParams(window.location.search);
        let storedMeasurementId = "<?php echo esc_js($ga4_measurement_id); ?>";
        if (params.has("subscription_id") && params.has("g_mail")) {
            const subscriptionId = params.get("subscription_id");
            const gMail = params.get("g_mail");
            const myModal = new bootstrap.Modal(document.getElementById("ga4Modal"));
            myModal.show();
            list_analytics_account();
        }
    });

    function showGA4ModalInfo(message) {
        jQuery("#ga4Modal .modal-body").prepend(
            '<div class="ga4-info-box alert alert-info rounded-3 py-2 px-3 mb-3">' + message + '</div>'
        );
    }

    jQuery('#measurement_id').on('change', function() {
        let selectedValue = jQuery(this).val().trim();
        if (selectedValue) {
            jQuery('#ga4ConfirmLabel').html(
                `Events will be tracked using the selected GA4 Measurement ID - <strong>${selectedValue}</strong>, and the in-built reports will reflect data from the same GA4 account.`
            );
        } else {
            jQuery('#ga4ConfirmLabel').html(
                `Events will be tracked using the selected GA4 Measurement ID, and the in-built reports will reflect data from the same GA4 account.`
            );
        }
    });


    jQuery(document).on("click", "#opengasettings", function(e) {
        e.preventDefault();
        jQuery("#ga4Modal").modal("show");
        list_analytics_account();
    });
    jQuery(document).on("click", ".tvc_google_signinbtn", function(e) {
        jQuery("#ga4Modal").modal("hide");
        e.preventDefault();
        jQuery("#prega4AuthModal").modal("show");
    });

    jQuery(document).on('change', '#ga4_analytic_account_id', function() {
        let accountId = jQuery(this).val();

        if (accountId) {
            jQuery("#measurement_id").html('<option>Loading...</option>');
            list_analytics_web_properties("GA4", accountId);
        } else {
            jQuery("#measurement_id").html('<option value="">-- Select Property --</option>');
        }
    });

    function showGA4ModalError(message) {
        jQuery("#ga4Modal .modal-body").prepend(
            '<div class="ga4-error-box alert alert-danger rounded-2 py-1 px-2 mb-3">' + message + '</div>'
        );
    }

    function list_analytics_account(page = 1) {
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery("#ga4_analytic_account_id").html('<option>Loading...</option>');
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_analytics_account_list",
                page: page,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                // console.log(response);
                if (response && response.error == false) {
                    var error_msg = 'null';
                    if (response?.data?.items.length > 0) {
                        jQuery('#ga4_analytic_account_id').html('<option value="">-- Select Account --</option>');
                        var AccOptions = '';
                        response.data.items.forEach(function(item) {
                            AccOptions += '<option value="' + item.id + '">' + item.name + ' - ' + item.id + '</option>';
                        });
                        jQuery('#ga4_analytic_account_id').append(AccOptions);
                    } else {
                        showGA4ModalError("There are no Google Analytics accounts associated with this email.");
                    }

                } else if (response && response.error == true && response.error != undefined) {
                    const errors = response.errors;
                    showGA4ModalError(errors);
                    var error_msg = errors;
                } else {
                    showGA4ModalError("There are no Google Analytics accounts associated with this email.");
                }
                jQuery("#tvc-ga4-acc-edit-acc_box")?.removeClass('tvc-disable-edits');
                jQuery(".conv-enable-selection").removeClass('disabled');
            }
        });
    }

    function list_analytics_web_properties(type, account_id) {
        jQuery("#measurement_id").html('<option>Loading...</option>');

        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "get_analytics_web_properties",
                account_id: account_id,
                type: type,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                if (response && response.error == false) {
                    if (type === "GA4") {
                        jQuery('#measurement_id').empty();

                        if (response?.data?.wep_measurement?.length > 0) {
                            let streamOptions = '<option value="">-- Select Measurement Id --</option>';
                            response.data.wep_measurement.forEach(function(item) {
                                streamOptions += `<option value="${item.measurementId}">
                                ${item.measurementId} - ${item.displayName}
                            </option>`;
                            });
                            jQuery('#measurement_id').html(streamOptions).prop("disabled", false);
                        } else {
                            jQuery('#measurement_id')
                                .html('<option value="">No GA4 Property Found</option>')
                                .prop("disabled", true);

                            showGA4ModalError("There are no Google Analytics 4 Properties associated with this analytics account.");
                        }
                    }
                } else if (response && response.error === true) {
                    const errors = response.errors || "Something went wrong";
                    showGA4ModalError(errors);
                } else {
                    jQuery('#measurement_id')
                        .html('<option value="">No Properties Found</option>')
                        .prop("disabled", true);

                    showGA4ModalError("No properties found for this account.");
                }
            },
            error: function() {
                jQuery('#measurement_id')
                    .html('<option value="">Request Failed</option>')
                    .prop("disabled", true);

                showGA4ModalError("Failed to fetch GA4 properties. Please try again.");
            }
        });
    }

    function checkGA4Fields() {
        var ga4Account = jQuery("#ga4_analytic_account_id").val();
        var ga4Property = jQuery("#measurement_id").val();

        // Invalid values include: empty, null, undefined, "Loading..."
        var invalid = ["", null, undefined, "Loading..."];

        if (!invalid.includes(ga4Account) && !invalid.includes(ga4Property)) {
            jQuery("#savereportsettings").removeClass("disabled");
        } else {
            jQuery("#savereportsettings").addClass("disabled");
        }
    }
    jQuery('#ga4_analytic_account_id').on('change', checkGA4Fields);
    jQuery('#measurement_id').on('change', checkGA4Fields);

    jQuery("#savereportsettings").on("click", function() {
        checkGA4Fields();
        jQuery("#savereportsettings").addClass("disabled");
        var ga4Account = jQuery("#ga4_analytic_account_id").val();
        var ga4Property = jQuery("#measurement_id").val();

        if (!ga4Account || !ga4Property) {
            alert("Please select both Account and Property to continue.");
            return;
        }
        var selected_vals = {
            ga4_analytic_account_id: ga4Account,
            measurement_id: ga4Property,
        };
        // Show spinner
        jQuery("#savereportsettings .spinner-border").removeClass("d-none");

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "conv_save_pixel_data",
                pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                conv_options_data: selected_vals,
                conv_options_type: ["eeoptions", "eeapidata", "middleware"],
            },
            success: function(response) {
                jQuery("#savereportsettings .spinner-border").addClass("d-none");

                if (response == "0" || response == "1") {
                    alert("GA4 settings saved successfully!");
                    jQuery("#ga4Modal").modal("hide");
                    jQuery("body").append(`
                <div class="conv-fullscreen-loader" 
                     style="position:fixed; top:0; left:0; width:100%; height:100%;
                            background:rgba(255,255,255,0.8); z-index:9999;
                            display:flex; justify-content:center; align-items:center;">
                    <div class="spinner-border text-primary" style="width:4rem; height:4rem;"></div>
                </div>
            `);
                    // Wait for createDimension to finish, then reload
                    createDimension().then(() => {
                        let url = new URL(window.location.href);
                        url.searchParams.delete("subscription_id");
                        url.searchParams.delete("g_mail");
                        window.location.href = url.toString();
                    });
                } else {
                    alert("Failed to save settings. Please try again.");
                }
            },
            error: function() {
                jQuery("#savereportsettings .spinner-border").addClass("d-none");
                alert("Something went wrong. Please try again.");
            },
        });
    });

    function createDimension() {
        return jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "conv_create_ga4_custom_dimension",
                pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                ga_cid: "1",
                non_woo_tracking: "1"
            },
            success: function(response) {
                console.log('Custom Dimension created successfully');
            }
        });
    }

    jQuery(".google_connect_url").on("click", function() {
        const w = 800;
        const h = 650;
        const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
        const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

        const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        const systemZoom = width / window.screen.availWidth;
        const left = (width - w) / 2 / systemZoom + dualScreenLeft;
        const top = (height - h) / 2 / systemZoom + dualScreenTop;
        var url = '<?php echo esc_url($connect_url); ?>';

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
    // Schedule email
    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    function save_local_data(email_toggle_check, custom_email, email_frequency) {
        var selected_vals = {};
        selected_vals['sch_email_toggle_check'] = email_toggle_check;
        selected_vals['sch_custom_email'] = custom_email;
        selected_vals['sch_email_frequency'] = email_frequency;
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "conv_save_pixel_data",
                pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                conv_options_data: selected_vals,
                conv_options_type: ["eeoptions"]
            },
            beforeSend: function() {},
            success: function(response) {
                console.log('Email setting saved in db');
            }
        });
    }
    jQuery(document).ready(function() {
        jQuery("#navbarSupportedContent ul li").removeClass("rich-blue");
        jQuery('#navbarSupportedContent ul > li').eq(0).addClass('rich-blue');

        var save_email_bydefault = '<?php echo esc_js($options["save_email_bydefault"] ?? ""); ?>';
        if (save_email_bydefault === "") {
            let email_toggle_check = '0'; //default
            let custom_email = '<?php echo esc_attr($g_mail); ?>';
            let email_frequency = "Weekly";
            let email_frequency_final = "7_day";
            var data = {
                "action": "set_email_configurationGA4",
                "is_disabled": email_toggle_check,
                "custom_email": custom_email,
                "email_frequency": email_frequency_final,
                "save_email_bydefault": "1",
                "conversios_nonce": '<?php echo esc_js(wp_create_nonce('conversios_nonce')); ?>'
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    jQuery("#loadingbar_blue").show();
                },
                success: function(response) {
                    if (response.error == false) {
                        jQuery("#err_sch_msg").hide();
                        jQuery("#loadingbar_blue").hide();
                        if (email_toggle_check == "0") {
                            jQuery("#schedule_email_alert_msg").html(
                                "Successfully subscribed to receive analytics reports in your email");
                        } else {
                            jQuery("#schedule_email_alert_msg").html("Successfully Unsubscribed");
                        }

                        jQuery("#schedule_email_alert").removeClass("d-none");

                        jQuery('#sch_ack_msg').show();
                        //local storage
                        save_local_data(email_toggle_check, custom_email, email_frequency);
                        if (email_toggle_check == '0') {
                            jQuery('#schedule_form_btn_set').show();
                            jQuery('#schedule_form_btn_raw').hide();
                        } else {
                            jQuery('#schedule_form_btn_set').hide();
                            jQuery('#schedule_form_btn_raw').show();
                        }
                    } else {
                        jQuery("#err_sch_msg").show();
                        jQuery("#loadingbar_blue").hide();
                    }
                    setTimeout(
                        function() {
                            jQuery("#sch_ack_msg").hide();
                        }, 8000);
                }
            });
        }
    });
    /*schedule email form submit event listner*/
    jQuery("#schedule_email_save_config").on("click", function() {
        let email_toggle_check = '0'; //default
        if (jQuery("#email_toggle_btn").prop("checked")) {
            email_toggle_check = '0'; //enabled
        } else {
            email_toggle_check = '1'; //disabled
        }
        let custom_email = '<?php echo esc_attr($g_mail); ?>';
        let email_frequency = "Weekly";
        let email_frequency_final = "7_day";
        var data = {
            "action": "set_email_configurationGA4",
            "is_disabled": email_toggle_check,
            "custom_email": custom_email,
            "email_frequency": email_frequency_final,
            "conversios_nonce": '<?php echo esc_js(wp_create_nonce('conversios_nonce')); ?>'
        };
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                jQuery("#loadingbar_blue").show();
            },
            success: function(response) {
                if (response.error == false) {
                    jQuery("#err_sch_msg").hide();
                    jQuery("#loadingbar_blue").hide();
                    if (email_toggle_check == "0") {
                        jQuery("#schedule_email_alert_msg").html(
                            "Successfully subscribed to receive analytics reports in your email");
                    } else {
                        jQuery("#schedule_email_alert_msg").html("Successfully Unsubscribed");
                    }

                    jQuery("#schedule_email_alert").removeClass("d-none");

                    jQuery('#sch_ack_msg').show();
                    //local storage
                    save_local_data(email_toggle_check, custom_email, email_frequency);
                    if (email_toggle_check == '0') {
                        jQuery('#schedule_form_btn_set').show();
                        jQuery('#schedule_form_btn_raw').hide();
                    } else {
                        jQuery('#schedule_form_btn_set').hide();
                        jQuery('#schedule_form_btn_raw').show();
                    }
                } else {
                    jQuery("#err_sch_msg").show();
                    jQuery("#loadingbar_blue").hide();
                }
                setTimeout(
                    function() {
                        jQuery("#sch_ack_msg").hide();
                    }, 8000);
            }
        });
    });
    jQuery("#sch_ack_msg_close").on("click", function() {
        jQuery("#sch_ack_msg").hide();
    });
    jQuery('#email_toggle_btn').change(function() {
        if (jQuery(this).prop("checked")) {
            jQuery("#email_toggle_btnLabel").addClass("convEmail_default_cls_enabled");
            jQuery("#email_toggle_btnLabel").removeClass("convEmail_default_cls_disabled");
            jQuery("#email_frequency,#custom_email").attr("style", "color: #2A2D2F !important");
            jQuery("#schedule_email_save_config").html('Save Changes');
        } else {
            jQuery("#email_toggle_btnLabel").addClass("convEmail_default_cls_disabled");
            jQuery("#email_toggle_btnLabel").removeClass("convEmail_default_cls_enabled");
            jQuery("#email_frequency,#custom_email").attr("style", "color: #94979A !important");
            jQuery("#schedule_email_save_config").html('Save Changes');
        }
    });
    jQuery(function() {
        jQuery('#conv-download-pdf').click(function() {
            jQuery("#conv_report_opright").addClass("d-none");
            jQuery("#conv-download-pdf").addClass("disabledsection");
            jQuery("#conv_pdf_logo").removeClass('d-none');
            const element = document.getElementById('conv-report-main-div');
            const watermarkURL = "<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logo.png'); ?>";

            html2canvas(element, {
                scale: 2,
                useCORS: true,
            }).then(function(canvas) {
                const imgData = canvas.toDataURL('image/jpeg');
                const {
                    jsPDF
                } = window.jspdf;

                const canvasWidth = canvas.width;
                const canvasHeight = canvas.height;

                const pdfWidth = (canvasWidth * 25.4) / 96; // Convert canvas width from px to mm
                const pdfHeight = (canvasHeight * 25.4) / 96;

                const pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);

                // Add the main content image
                pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);

                // Load the watermark image and add it to the center
                const watermark = new Image();
                watermark.src = watermarkURL;
                watermark.onload = function() {
                    const wmWidth = pdfWidth * 0.7; // 50% of PDF width
                    const wmHeight = (watermark.height / watermark.width) * wmWidth;
                    const wmX = (pdfWidth - wmWidth) / 1.3; // Center horizontally
                    const wmY = (pdfHeight - wmHeight) / 1.6; // Center vertically

                    pdf.setGState(new pdf.GState({
                        opacity: 0.1
                    })); // Set low opacity
                    pdf.addImage(watermark, 'PNG', wmX, wmY, wmWidth, wmHeight, undefined, 'NONE', 45);
                    pdf.save('ConversiosGA4Report.pdf');
                    jQuery("#conv_pdf_logo").addClass('d-none');
                };
            });
            jQuery("#conv-download-pdf").removeClass("disabledsection");
            jQuery("#conv_report_opright").removeClass("d-none");
        });
    });
</script>