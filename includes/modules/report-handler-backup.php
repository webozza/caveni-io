<?php

function generate_seo_report($caveni_client_id_new, $caveni_start_date, $caveni_end_date) {

    $caveni_client_id = 0;

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
        $caveni_client_id = $caveni_client_id_new;
    }

    $fetch_errors = []; // Initialize an array to store errors

    // Fetch totalUsers
    try {
        $total_users = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "totalUsers");
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (totalUsers): ' . $e->getMessage());
        $fetch_errors['totalUsers'] = 'Failed to fetch total users: ' . $e->getMessage();
        $total_users = []; // Use a safe fallback
    }

    // Fetch impressions
    try {
        $total_impressions = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "organicGoogleSearchImpressions");
        // $total_impressions = get_gsc_total_impressions($caveni_client_id, $caveni_start_date, $caveni_end_date);
    } catch (Exception $e) {
        error_log('GA4 Error (impressions): ' . $e->getMessage());
    
        $errorMessage = $e->getMessage();
    
        // Remove any prefixes (like "GA4 API Error: ") if present
        $jsonStart = strpos($errorMessage, '{');
        if ($jsonStart !== false) {
            $errorMessage = substr($errorMessage, $jsonStart); // Extract JSON part only
        }
    
        // Attempt to decode the JSON
        $decodedError = json_decode($errorMessage, true);
    
        if (isset($decodedError['error']['message'])) {
            $fetch_errors['impressions'] = $decodedError['error']['message']; // Extract only the message
        } else {
            // Fallback to original error message if JSON decoding fails
            $fetch_errors['impressions'] = "Unknown API error: " . $errorMessage;
        }
    
        $total_impressions = []; // Use a safe fallback
    }
            

    // Fetch new users
    try {
        $total_new_users = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "newUsers");
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (new users): ' . $e->getMessage());
        $fetch_errors['newUsers'] = 'Failed to fetch new users: ' . $e->getMessage();
        $total_new_users = []; // Use a safe fallback
    }

    // Fetch avg. session duration
    try {
        $total_avg_session_duration = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "averageSessionDuration");
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (avg. session duration): ' . $e->getMessage());
        $fetch_errors['averageSessionDuration'] = 'Failed to fetch avg. session duration: ' . $e->getMessage();
        $total_avg_session_duration = []; // Use a safe fallback
    }

    // Fetch views per session
    try {
        $total_views_per_session = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "screenPageViewsPerSession");
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (views per session): ' . $e->getMessage());
        $fetch_errors['screenPageViewsPerSession'] = 'Failed to fetch views per session: ' . $e->getMessage();
        $total_views_per_session = []; // Use a safe fallback
    }

    // Fetch bounce rate
    try {
        $total_bounce_rate = get_ga4_total_users($caveni_client_id, $caveni_start_date, $caveni_end_date, "bounceRate");
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (bounce rate): ' . $e->getMessage());
        $fetch_errors['bounceRate'] = 'Failed to fetch bounce rate: ' . $e->getMessage();
        $total_bounce_rate = []; // Use a safe fallback
    }

    // Fetch avg. position for keywords
    try {
        $total_avg_search_position = get_gsc_data($caveni_client_id, $caveni_start_date, $caveni_end_date, ['query'], 1000);
    } catch (Exception $e) {
        error_log('GA4 Error (avg. position): ' . $e->getMessage());
    
        $errorMessage = $e->getMessage();
    
        // Remove any prefixes (like "GA4 API Error: ") if present
        $jsonStart = strpos($errorMessage, '{');
        if ($jsonStart !== false) {
            $errorMessage = substr($errorMessage, $jsonStart); // Extract JSON part only
        }
    
        // Attempt to decode the JSON
        $decodedError = json_decode($errorMessage, true);
    
        if (isset($decodedError['error']['message'])) {
            $fetch_errors['keywordPosition'] = $decodedError['error']['message']; // Extract only the message
        } else {
            // Fallback to original error message if JSON decoding fails
            $fetch_errors['keywordPosition'] = "Unknown API error: " . $errorMessage;
        }
    
        $total_avg_search_position = []; // Use a safe fallback
    }

    // Fetch users by source
    try {
        $users_by_source = get_ga4_users_by_source($caveni_client_id, $caveni_start_date, $caveni_end_date);
    } catch (Exception $e) {
        // Log the error and store it in $fetch_errors
        error_log('GA4 Error (users by source): ' . $e->getMessage());
        $fetch_errors['usersBySource'] = 'Failed to fetch users by source: ' . $e->getMessage();
        $users_by_source = []; // Use a safe fallback
    }
    
    $dates = [];
    $metric_total_users = [];
    $metric_total_impressions = [];
    $metric_total_new_users = [];
    $metric_total_avg_session_duration = [];
    $metric_total_views_per_session = [];
    $metric_total_bounce_rate = [];

    // Combine all dates from $total_users and $total_impressions
    $all_dates = array_merge(array_keys($total_users), array_keys($total_impressions), array_keys($total_new_users), array_keys($total_avg_session_duration), array_keys($total_views_per_session), array_keys($total_bounce_rate));

    // Ensure dates are unique and sorted
    $unique_sorted_dates = array_unique($all_dates);
    sort($unique_sorted_dates);

    // Populate metrics aligned with the unique sorted dates
    foreach ($unique_sorted_dates as $date) {
        // Reformat the date to "d M" (e.g., "22 Jan")
        $formatted_date = DateTime::createFromFormat('Ymd', $date)->format('d M');
        $dates[] = $formatted_date;

        // Use 0 if a date is missing in either $total_users or $total_impressions
        $metric_total_users[] = $total_users[$date] ?? 0;
        $metric_total_impressions[] = $total_impressions[$date] ?? 0;
        $metric_total_new_users[] = $total_new_users[$date] ?? 0;
        $metric_total_avg_session_duration[] = $total_avg_session_duration[$date] ?? 0;
        $metric_total_views_per_session[] = $total_views_per_session[$date] ?? 0;
        $metric_total_bounce_rate[] = $total_bounce_rate[$date] ?? 0;
    }
    
    // Average Position (SEO)
    $averagePosition = [];
    $current_data = $total_avg_search_position['current'];
    $previous_data = $total_avg_search_position['previous'];

    // Iterate through current period data and compare with previous period
    foreach ($current_data as $keyword => $current_metrics) {
        $current_avg_position = $current_metrics['position'] / $current_metrics['count'];

        // Default values for trend and percentage change
        $trend = 'pre_high';
        $formatted_percentage_change = 'New';

        // Check if the keyword exists in the previous period
        if (isset($previous_data[$keyword])) {
            $previous_metrics = $previous_data[$keyword];
            $previous_avg_position = $previous_metrics['position'] / $previous_metrics['count'];

            // Determine the trend and percentage change
            if ($previous_avg_position > 0) {
                $percentage_change = (($current_avg_position - $previous_avg_position) / $previous_avg_position) * 100;
                $trend = $current_avg_position < $previous_avg_position ? 'pre_high' : 'pre_low';
                $formatted_percentage_change = number_format(abs($percentage_change), 1) . '%';
            }
        }

        $averagePosition[] = [
            'keyword' => $keyword,
            'avg_position' => number_format($current_avg_position, 2),
            'trend' => $trend,
            'percentage_change' => $formatted_percentage_change,
        ];
    }

    // Sort $averagePosition by average position from highest to lowest
    usort($averagePosition, function ($a, $b) {
        return $a['avg_position'] <=> $b['avg_position'];
    });

    // Impressions (SEO)
    $impressionsData = [];
    $current_data = $total_avg_search_position['current'];
    $previous_data = $total_avg_search_position['previous'];

    // Iterate through current period data and compare with previous period
    foreach ($current_data as $keyword => $current_metrics) {
        $current_impressions = $current_metrics['impressions'];

        // Default values for trend and percentage change
        $trend = 'pre_high';
        $formatted_percentage_change = 'New';

        // Check if the keyword exists in the previous period
        if (isset($previous_data[$keyword])) {
            $previous_metrics = $previous_data[$keyword];
            $previous_impressions = $previous_metrics['impressions'];

            // Determine the trend and percentage change
            if ($previous_impressions > 0) {
                $percentage_change = (($current_impressions - $previous_impressions) / $previous_impressions) * 100;
                $trend = $current_impressions > $previous_impressions ? 'pre_high' : 'pre_low';
                $formatted_percentage_change = number_format(abs($percentage_change), 1) . '%';
            }
        }

        $impressionsData[] = [
            'keyword' => $keyword,
            'impressions' => number_format($current_impressions),
            'trend' => $trend,
            'percentage_change' => $formatted_percentage_change,
        ];
    }

    // Sort $impressionsData by impressions from highest to lowest
    usort($impressionsData, function ($a, $b) {
        return $b['impressions'] <=> $a['impressions'];
    });

    // Function to convert seconds into hh:mm:ss format
    function formatDuration($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remaining_seconds = $seconds % 60;
        return sprintf("%dh %02dm %02ds", $hours, $minutes, $remaining_seconds);
    }

    // For Top Metrics - New Users
    $total_entries_users = count($metric_total_new_users);
    $midpoint_users = (int)($total_entries_users / 2);

    $previous_half_users = array_slice($metric_total_new_users, 0, $midpoint_users);
    $current_half_users = array_slice($metric_total_new_users, $midpoint_users);

    $previous_total_users = array_sum($previous_half_users);
    $current_total_users = array_sum($current_half_users);

    if ($previous_total_users > 0) {
        $percentage_change_users = (($current_total_users - $previous_total_users) / $previous_total_users) * 100;
    } else {
        $percentage_change_users = 0;
    }

    $trend_users = $current_total_users > $previous_total_users ? 'pre_high' : 'pre_low';
    $formatted_percentage_change_users = number_format(abs($percentage_change_users), 1) . '%';

    // For Top Metrics - Avg. Session Duration
    $total_entries_duration = count($metric_total_avg_session_duration);
    $midpoint_duration = (int)($total_entries_duration / 2);

    $previous_half_duration = array_slice($metric_total_avg_session_duration, 0, $midpoint_duration);
    $current_half_duration = array_slice($metric_total_avg_session_duration, $midpoint_duration);

    $previous_total_duration = array_sum($previous_half_duration);
    $current_total_duration = array_sum($current_half_duration);

    $previous_average_duration = $midpoint_duration > 0 ? $previous_total_duration / $midpoint_duration : 0;
    $current_average_duration = $midpoint_duration > 0 ? $current_total_duration / $midpoint_duration : 0;

    if ($previous_average_duration > 0) {
        $percentage_change_duration = (($current_average_duration - $previous_average_duration) / $previous_average_duration) * 100;
    } else {
        $percentage_change_duration = 0;
    }

    $trend_duration = $current_average_duration > $previous_average_duration ? 'pre_high' : 'pre_low';
    $formatted_percentage_change_duration = number_format(abs($percentage_change_duration), 1) . '%';
    $formatted_current_duration = formatDuration(round($current_average_duration));

    // For Top Metrics - Views per Session
    $total_entries_views = count($metric_total_views_per_session);
    $midpoint_views = (int)($total_entries_views / 2);

    $previous_half_views = array_slice($metric_total_views_per_session, 0, $midpoint_views);
    $current_half_views = array_slice($metric_total_views_per_session, $midpoint_views);

    $previous_total_views = array_sum($previous_half_views);
    $current_total_views = array_sum($current_half_views);

    $previous_average_views = $midpoint_views > 0 ? $previous_total_views / $midpoint_views : 0;
    $current_average_views = $midpoint_views > 0 ? $current_total_views / $midpoint_views : 0;

    if ($previous_average_views > 0) {
        $percentage_change_views = (($current_average_views - $previous_average_views) / $previous_average_views) * 100;
    } else {
        $percentage_change_views = 0;
    }

    $trend_views = $current_average_views > $previous_average_views ? 'pre_high' : 'pre_low';
    $formatted_percentage_change_views = number_format(abs($percentage_change_views), 1) . '%';

    $formatted_current_average_views = number_format($current_average_views, 2);

    // For Top Metrics - Bounce Rate
    $total_entries_bounce_rate = count($metric_total_bounce_rate);
    $midpoint_bounce_rate = (int)($total_entries_bounce_rate / 2);

    $previous_half_bounce_rate = array_slice($metric_total_bounce_rate, 0, $midpoint_bounce_rate);
    $current_half_bounce_rate = array_slice($metric_total_bounce_rate, $midpoint_bounce_rate);

    $previous_total_bounce_rate = array_sum($previous_half_bounce_rate);
    $current_total_bounce_rate = array_sum($current_half_bounce_rate);

    // Fix: Calculate proper average instead of scaling up sum
    $previous_average_bounce_rate = $midpoint_bounce_rate > 0 ? ($previous_total_bounce_rate / $midpoint_bounce_rate) * 100 : 0;
    $current_average_bounce_rate = $midpoint_bounce_rate > 0 ? ($current_total_bounce_rate / $midpoint_bounce_rate) * 100 : 0;

    if ($previous_average_bounce_rate > 0) {
        $percentage_change_bounce_rate = (($current_average_bounce_rate - $previous_average_bounce_rate) / $previous_average_bounce_rate) * 100;
    } else {
        $percentage_change_bounce_rate = 0;
    }

    $trend_bounce_rate = $current_average_bounce_rate > $previous_average_bounce_rate ? 'pre_low' : 'pre_high';
    $formatted_percentage_change_bounce_rate = number_format(abs($percentage_change_bounce_rate), 1) . '%';
    $formatted_current_average_bounce_rate = number_format($current_average_bounce_rate, 2) . '%';

    // Update Engagement Data
    $engagementData = [
        ['New Users', number_format($current_total_users), $trend_users, $formatted_percentage_change_users, '#E0F7FA'],
        ['Session Duration', $formatted_current_duration, $trend_duration, $formatted_percentage_change_duration, '#FBE9E7'],
        ['Views per Session', $formatted_current_average_views, $trend_views, $formatted_percentage_change_views, '#E8F5E9'],
        ['Bounce Rate', $formatted_current_average_bounce_rate, $trend_bounce_rate, $formatted_percentage_change_bounce_rate, '#FFF8E1'], // Light yellow
    ];

    $ga4_property_details = get_ga4_property_details($caveni_client_id);

    wp_send_json_success(['ga4_property_details' => $ga4_property_details, 'start_date' => $caveni_start_date,'end_date' => $caveni_end_date, 'total_users' => $total_users, 'total_impressions' => $total_impressions,'dates' => $dates, 'metric_total_users' => $metric_total_users, 'metric_total_impressions' => $metric_total_impressions, 'average_position' => $averagePosition, 'impression_data' => $impressionsData, 'engagement' => $engagementData, 'fetch_errors' => $fetch_errors, 'total_new_users' => $total_new_users, 'metric_total_new_users' => $metric_total_new_users, 'total_average_session_duration' => $total_avg_session_duration, 'metric_total_average_session_duration' => $metric_total_avg_session_duration, 'total_views_per_session' => $total_views_per_session, 'metric_total_views_per_session' => $metric_total_views_per_session, 'total_bounce_rate' => $total_bounce_rate, 'metric_total_bounce_rate' => $metric_total_bounce_rate, 'total_avg_search_position' => $total_avg_search_position, 'users_by_source' => $users_by_source]);
    pre($_POST, 1);
}

// Caveni Reporting Ajax Handler with mPDF PDF Generation
function caveni_generate_report() {
    check_ajax_referer('caveni_reports_nonce', 'security');

    if (empty($_POST['report_html'])) {
        wp_send_json_error(['message' => 'No report HTML received.']);
        return;
    }

    $report_html = stripslashes($_POST['report_html']);
    if (empty(trim($report_html))) {
        wp_send_json_error(['message' => 'Report HTML is empty after stripping slashes.']);
        return;
    }

    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $client_id = $_POST['client'];
    $service = $_POST['service'] ?: "General"; // Fallback if service is empty
    $company_name = get_user_meta($client_id, 'company', true) ?: "Unknown Company";
    $created_date = date("F j, Y"); // Example: March 7, 2025

    generate_seo_report($client_id, $start_date, $end_date);

    $upload_dir = wp_upload_dir();
    $pdf_dir = trailingslashit($upload_dir['basedir']) . 'caveni-reports/';
    $pdf_url = trailingslashit($upload_dir['baseurl']) . 'caveni-reports/';

    if (!file_exists($pdf_dir)) {
        mkdir($pdf_dir, 0755, true);
    }

    $filename = 'Caveni-Report-' . time() . '.pdf';
    $pdf_path = $pdf_dir . $filename;
    $pdf_download_url = $pdf_url . $filename;

    try {
        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => sys_get_temp_dir(),
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch'
        ]);

        // ✅ 1️⃣ First Page: Background Covers FULL Page
        $background_image = CAVENI_IO_URL . 'public/images/caveni-report-cover.jpg';

        // Add first page with NO margins & full background coverage
        $mpdf->SetMargins(0, 0, 0);
        $mpdf->AddPage();

        // Set full-page background image (no gaps)
        $mpdf->Image($background_image, 0, 0, 210, 297, 'jpg', '', true, false);

        // Use Poppins font (if available in mPDF)
        $mpdf->SetFont('Poppins', 'B', 26);

        // Centered Text (Horizontally & Vertically)
        $mpdf->SetY(120);
        $mpdf->Cell(0, 20, strtoupper($service) . " REPORT For " . $company_name, 0, 1, 'C');

        $mpdf->SetFont('Poppins', '', 18);
        $mpdf->Cell(0, 20, "Created By Caveni On " . $created_date, 0, 1, 'C');

        // ✅ 2️⃣ Second Page: Report Content (NO BACKGROUND IMAGE)
        $mpdf->AddPage(); // Ensures new page with no background
        $mpdf->SetDefaultBodyCSS('background', 'none'); // Removes background for the second page

        $mpdf->WriteHTML('
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Caveni Report</title>
                <style>
                    @page { margin: 20mm; }
                    body { font-family: "Poppins", Arial, sans-serif; font-size: 14px; }
                    h1, h2, h3 { color: #333; }
                    .report-container { padding: 20px; border: 1px solid #ddd; }
                    img { max-width: 100%; height: auto; }
                </style>
            </head>
            <body>
                <div class="report-container">' . $report_html . '</div>
            </body>
            </html>
        ');

        // Save PDF
        $mpdf->Output($pdf_path, 'F');

        wp_send_json_success([
            'message'    => 'Report generated successfully.',
            'report_url' => $pdf_download_url
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Failed to generate PDF: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_caveni_generate_report', 'caveni_generate_report');
add_action('wp_ajax_nopriv_caveni_generate_report', 'caveni_generate_report');

