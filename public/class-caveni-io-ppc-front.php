<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io_Ppc_Front
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $caveni_io    The ID of this plugin.
     */
    private $caveni_io;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $caveni_io       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct($caveni_io, $version)
    {
        $this->caveni_io = $caveni_io;
        $this->version   = $version;
    }



    /**
     * Perform  the of shortcode helpdesk.
     *
     * @since    1.0.0
     * @param    array $attributes Paramter of shortcode array.
     */

    public function perform_shortcode_action($attributes, $req_data)
    {
        
        return $this->display_marketing_data($attributes);
    }

    /**
     * Add  the scripts to ppc page.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /* Enque js for highchart */
        wp_register_script($this->caveni_io . "-datepicker", CAVENI_IO_URL . '/public/js/jquery.periodpicker.full.min.js', array('jquery'), $this->version, true);
        wp_register_script($this->caveni_io . "jquery-popper-min", 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js', array('jquery'), $this->version, false);
        wp_register_script($this->caveni_io . "jquery-highchart", 'https://code.highcharts.com/highcharts.js', array('jquery'), $this->version, false);
        wp_register_script($this->caveni_io . "jquery-accessibility", 'https://code.highcharts.com/modules/accessibility.js', array('jquery'), $this->version, false);
        wp_register_script($this->caveni_io . "jquery-highchart-more", 'https://code.highcharts.com/highcharts-more.js', array('jquery'), $this->version, false);
        wp_register_script($this->caveni_io . '-apexcharts', CAVENI_IO_URL . '/public/js/apexcharts.min.js', array('jquery'), $this->version, true);
        wp_register_script($this->caveni_io . '-select2', CAVENI_IO_URL . '/public/js/select2.min.js', array('jquery'), $this->version, true);
        wp_register_script($this->caveni_io . '-ppc', CAVENI_IO_URL . '/public/js/caveni-ppc.js', array('jquery'), $this->version, true);
        wp_localize_script(
            $this->caveni_io . '-ppc',
            'caveniPpc',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security_get_data' => wp_create_nonce('security_get_data_action'),
            ]
        );
    }

    public function display_marketing_data($attributes)
    {
        wp_enqueue_script($this->caveni_io . '-datepicker');
        wp_enqueue_script($this->caveni_io . 'jquery-popper-min');
        wp_enqueue_script($this->caveni_io . 'jquery-highchart');
        wp_enqueue_script($this->caveni_io . 'jquery-accessibility');
        wp_enqueue_script($this->caveni_io . 'jquery-highchart-more');
        wp_enqueue_script($this->caveni_io . '-apexcharts');
        wp_enqueue_script($this->caveni_io . '-select2');
        wp_enqueue_script($this->caveni_io . '-ppc');

        ob_start();
    
        include_once CAVENI_IO_PATH . 'public/partials/marketing/ppc.php';

        return ob_get_clean();
    }

    public function caveni_get_ppc_data_callback()
    {
        check_ajax_referer('security_get_data_action', 'caveni_nonce');
        
        $caveni_client_id = 0;
        $caveni_start_date = 0;
        $caveni_end_date = 0;

        // Check if the current user is logged in
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();

            // Get all roles of the current user
            $current_user_roles = array_map('get_all_role_titles', $current_user->roles);

            // Check if the current user has the 'Client' role
            if (in_array('Client', $current_user_roles)) {
                $caveni_client_id = $current_user->ID; // Set to current user's ID if they are a Client
            }
        }

        // Get all users with roles
        $args = array(
            'role__in' => array('Client'), // Only fetch users with the 'Client' role
        );
        $users = get_users($args);

        $clients = array();
        foreach ($users as $user) {
            $roles = array_map('get_all_role_titles', $user->roles);

            // Include only users whose roles include 'Client'
            if (in_array('Client', $roles)) {
                $clients[] = array(
                    'ID'           => $user->ID,
                    'display_name' => $user->display_name,
                    'email'        => $user->user_email,
                    'roles'        => implode(', ', $roles),
                );
            }
        }

        // If `$caveni_client_id` is not already set by the logged-in user
        if ($caveni_client_id === 0) {
            if (isset($_POST['caveni_client_id'])) {
                $caveni_client_id = $_POST['caveni_client_id']; // Use POST value if available
            } elseif (!empty($clients)) {
                $caveni_client_id = $clients[0]['ID']; // Default to the first Client in the list
            }
        }

        if( isset($_POST['caveni_start_date']) && isset($_POST['caveni_end_date'])) {
            $caveni_start_date = $_POST['caveni_start_date'];
            $caveni_end_date = $_POST['caveni_end_date'];
        } else {
            $caveni_start_date = "60daysAgo";
            $caveni_end_date = "yesterday";
        }

        $fetch_ppc_errors = []; // Initialize an array to store errors

        // Fetch total ad clicks
        try {
            $total_ad_clicks = get_ga4_google_ads_all_campaigns($caveni_client_id, $caveni_start_date, $caveni_end_date, "advertiserAdImpressions");
        } catch (Exception $e) {
            // Log the error and store it in $fetch_ppc_errors
            error_log('GA4 Error (adsClicks): ' . $e->getMessage());
            $fetch_ppc_errors['adsClicksData'] = 'Failed to fetch adsClicks: ' . $e->getMessage();
            $total_ad_clicks = []; // Use a safe fallback
        }

        // Fetch total conversions
        try {
            $total_conversions = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "conversions");
        } catch (Exception $e) {
            // Log the error and store it in $fetch_ppc_errors
            error_log('GA4 Error (Conversions): ' . $e->getMessage());
            $fetch_ppc_errors['conversionsData'] = 'Failed to fetch Conversions: ' . $e->getMessage();
            $total_conversions = []; // Use a safe fallback
        }

        // Fetch campaign data
        try {
            $campaign_data = get_ga4_google_ads_campaigns($caveni_client_id, $caveni_start_date, $caveni_end_date);
        } catch (Exception $e) {
            // Log the error and store it in $fetch_ppc_errors
            error_log('GA4 Error (Campaign Data): ' . $e->getMessage());
            $fetch_ppc_errors['campaignData'] = 'Failed to fetch Campaign Data: ' . $e->getMessage();
            $campaign_data = []; // Use a safe fallback
        }

        // Fetch ad group data
        try {
            $adgroup_data = get_ga4_google_ads_ad_groups($caveni_client_id, $caveni_start_date, $caveni_end_date);
        } catch (Exception $e) {
            // Log the error and store it in $fetch_ppc_errors
            error_log('GA4 Error (adgroup Data): ' . $e->getMessage());
            $fetch_ppc_errors['adgroupData'] = 'Failed to fetch adgroup Data: ' . $e->getMessage();
            $adgroup_data = []; // Use a safe fallback
        }

        // Fetch total ad cost
        $total_ad_cost = get_ga4_google_ads_total_cost($caveni_client_id, $caveni_start_date, $caveni_end_date);

        // Fetch total impressions from ads
        $total_ad_impressions = get_ga4_google_ads_total_impressions($caveni_client_id, $caveni_start_date, $caveni_end_date);

        // Fetch ppc keywords
        $google_ads_keywords = get_ga4_google_ads_keywords($caveni_client_id, $caveni_start_date, $caveni_end_date);

        // Check analytics acc currency

        $dates = [];
        $metric_total_conversions = [];
        $metric_total_ad_clicks = [];

        // Combine all dates from $total_users and $total_impressions
        $all_dates = array_merge(array_keys($total_conversions), array_keys($total_ad_clicks));

        // Ensure dates are unique and sorted
        $unique_sorted_dates = array_unique($all_dates);
        sort($unique_sorted_dates);

        // Populate metrics aligned with the unique sorted dates
        foreach ($unique_sorted_dates as $date) {
            // Reformat the date to "d M" (e.g., "22 Jan")
            $formatted_date = DateTime::createFromFormat('Ymd', $date)->format('d M');
            $dates[] = $formatted_date;

            // Use 0 if a date is missing in either $total_users or $total_impressions
            $metric_total_conversions[] = $total_conversions[$date] ?? 0;
            $metric_total_ad_clicks[] = $total_ad_clicks[$date] ?? 0;
        }         

        $ga4_property_details = get_ga4_property_details($caveni_client_id);

        wp_send_json_success(['ga4_property_details' => $ga4_property_details,'fetch_ppc_errors' => $fetch_ppc_errors, 'start_date' => $caveni_start_date,'end_date' => $caveni_end_date, 'metric_total_conversions' => $metric_total_conversions, 'total_conversions' => $total_conversions, 'dates' => $dates, 'total_ad_clicks' => $total_ad_clicks, 'metric_total_ad_clicks' => $metric_total_ad_clicks, 'campaign_data' => $campaign_data, 'adgroup_data' => $adgroup_data, 'total_ad_cost' => $total_ad_cost, 'total_ad_impressions' => $total_ad_impressions, 'google_ads_keywords' => $google_ads_keywords]);
        pre($_POST, 1);
    }

}