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

    // wp_send_json_success(['ga4_property_details' => $ga4_property_details, 'start_date' => $caveni_start_date,'end_date' => $caveni_end_date, 'total_users' => $total_users, 'total_impressions' => $total_impressions,'dates' => $dates, 'metric_total_users' => $metric_total_users, 'metric_total_impressions' => $metric_total_impressions, 'average_position' => $averagePosition, 'impression_data' => $impressionsData, 'engagement' => $engagementData, 'fetch_errors' => $fetch_errors, 'total_new_users' => $total_new_users, 'metric_total_new_users' => $metric_total_new_users, 'total_average_session_duration' => $total_avg_session_duration, 'metric_total_average_session_duration' => $metric_total_avg_session_duration, 'total_views_per_session' => $total_views_per_session, 'metric_total_views_per_session' => $metric_total_views_per_session, 'total_bounce_rate' => $total_bounce_rate, 'metric_total_bounce_rate' => $metric_total_bounce_rate, 'total_avg_search_position' => $total_avg_search_position, 'users_by_source' => $users_by_source]);
    // pre($_POST, 1);

    return [
        'ga4_property_details' => $ga4_property_details,
        'start_date' => $caveni_start_date,
        'end_date' => $caveni_end_date,
        'total_users' => $total_users,
        'total_impressions' => $total_impressions,
        'dates' => $dates,
        'metric_total_users' => $metric_total_users,
        'metric_total_impressions' => $metric_total_impressions,
        'average_position' => $averagePosition,
        'impression_data' => $impressionsData,
        'engagement' => $engagementData,
        'fetch_errors' => $fetch_errors,
        'total_new_users' => $total_new_users,
        'metric_total_new_users' => $metric_total_new_users,
        'total_average_session_duration' => $total_avg_session_duration,
        'metric_total_average_session_duration' => $metric_total_avg_session_duration,
        'total_views_per_session' => $total_views_per_session,
        'metric_total_views_per_session' => $metric_total_views_per_session,
        'total_bounce_rate' => $total_bounce_rate,
        'metric_total_bounce_rate' => $metric_total_bounce_rate,
        'total_avg_search_position' => $total_avg_search_position,
        'users_by_source' => $users_by_source
    ];    
}

function generate_ppc_report($caveni_client_id_new, $caveni_start_date, $caveni_end_date) {
        
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

// PDF GENERATION NEEDED FUNCTIONS
function writeImage($mpdf, $imageSrc) {
    $mpdf->WriteHTML('<img src="' . $imageSrc . '" class="centered-image">');
}

function writeTable($mpdf, $data, $columns, $valueKey) {
    $mpdf->WriteHtml('<table class="caveni-pdf-table"><thead><tr>');

    // Table Headers
    foreach ($columns as $column) {
        $mpdf->WriteHtml('<th>' . htmlspecialchars($column) . '</th>');
    }
    $mpdf->WriteHtml('</tr></thead><tbody>');

    // Table Rows
    foreach ($data as $row) {
        $trend = isset($row['trend']) ? $row['trend'] : '';
        $percentage_change = isset($row['percentage_change']) ? $row['percentage_change'] : '0%';
        $indicator_img = ($trend === 'pre_low') ? $GLOBALS['indicator_down_img'] : $GLOBALS['indicator_up_img'];
        $trend_class = ($trend === 'pre_low') ? 'indicator-down' : 'indicator-up';

        $mpdf->WriteHtml('<tr>');
        $mpdf->WriteHtml('<td>' . htmlspecialchars($row['keyword'] ?? $row['source']) . '</td>'); // 'Keyword' or 'Source'
        $mpdf->WriteHtml('<td>' . htmlspecialchars($row[$valueKey]) . '</td>'); // Main value (users, impressions, avg_position)

        // Trend Indicator
        $mpdf->WriteHtml('
            <td>
                <div class="indicator-container ' . $trend_class . '">
                    <img src="' . $indicator_img . '" alt="trend">
                    <span>' . htmlspecialchars($percentage_change) . '</span>
                </div>
            </td>
        ');

        $mpdf->WriteHtml('</tr>');
    }

    $mpdf->WriteHtml('</tbody></table>');
}

function writeTablePPC($mpdf, $data, $title) {
    $mpdf->WriteHtml('<table class="caveni-pdf-table"><thead><tr>');

    // Table Headers
    $mpdf->WriteHtml('<th>' . htmlspecialchars('Dimension') . '</th>');
    $mpdf->WriteHtml('<th>' . htmlspecialchars('IMPR.') . '</th>');
    $mpdf->WriteHtml('<th>' . htmlspecialchars('Cost') . '</th>');
    
    $mpdf->WriteHtml('</tr></thead><tbody>');

    // Table Rows
    foreach ($data as $row) {
        $dimension = htmlspecialchars($row['campaign'] ?? $row['ad_group'] ?? $row['keyword'] ?? 'N/A');
        $impressions = number_format($row['impressions'] ?? 0);
        $cost = $GLOBALS['accCurrency'] . number_format($row['cost'] ?? 0, 0);

        $mpdf->WriteHtml('<tr>');
        $mpdf->WriteHtml('<td>' . $dimension . '</td>');
        $mpdf->WriteHtml('<td>' . $impressions . '</td>');
        $mpdf->WriteHtml('<td>' . $cost . '</td>');
        $mpdf->WriteHtml('</tr>');
    }

    $mpdf->WriteHtml('</tbody></table>');
}

function caveni_generate_report() {
    check_ajax_referer('caveni_reports_nonce', 'security');

    if (empty($_POST['report_html'])) {
        wp_send_json_error(['message' => 'No report HTML received.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'caveni_client_reports';

    // ✅ Sanitize Report HTML
    $report_html = stripslashes($_POST['report_html']);
    $report_html = html_entity_decode($report_html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $report_html = preg_replace('/\\\\n|\\\\r/', '', $report_html);
    $report_html = str_replace(['\\\"', '\\\\'], ['"', ''], $report_html);
    $report_html = trim($report_html);

    // ✅ Report Metadata
    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $client_id = $_POST['client'];
    $service = $_POST['service'] ?: "General";
    $company_name = get_user_meta($client_id, 'company', true) ?: "Unknown Company";
    $created_date = date("F j, Y");
    $top_metrics_image = $_POST['top_metrics_image'] ?? '';
    $caveni_chart_image_seo = $_POST['caveni_chart_image_seo'] ?? '';
    $avg_position_image = $_POST['avg_position_image'] ?? '';
    $impression_by_keyword_image = $_POST['impression_by_keyword_image'] ?? '';
    $users_by_source_image = $_POST['users_by_source_image'] ?? '';

    if($service == "SEO") {
        // SEO - Avg Position Data
        $raw_data_avg_position = stripslashes($_POST['api_data_avg_position']);
        $api_data_avg_position = json_decode($raw_data_avg_position, true);
        $api_data_avg_position = array_slice($api_data_avg_position, 0, 100);

        // SEO - Impressions by Keyword
        $raw_data_impressions_by_keyword = stripslashes($_POST['api_data_impressions_by_keyword']);
        $api_data_impressions_by_keyword = json_decode($raw_data_impressions_by_keyword, true);
        $api_data_impressions_by_keyword = array_slice($api_data_impressions_by_keyword, 0, 100);

        // SEO - Users by source
        $raw_data_users_by_source = stripslashes($_POST['api_data_users_by_source']);
        $api_data_users_by_source = json_decode($raw_data_users_by_source, true);
        $api_data_users_by_source = array_slice($api_data_users_by_source, 0, 100);
    }

    if ($service == "PPC") {
        // PPC - Campaign Overview
        $raw_data_campaign_overview = stripslashes($_POST['api_data_campaign_overview']);
        $api_data_campaign_overview = json_decode($raw_data_campaign_overview, true);
        $api_data_campaign_overview = array_slice($api_data_campaign_overview, 0, 100);
    
        // PPC - Ad Groups Overview
        $raw_data_ad_groups_overview = stripslashes($_POST['api_data_ad_groups_overview']);
        $api_data_ad_groups_overview = json_decode($raw_data_ad_groups_overview, true);
        $api_data_ad_groups_overview = array_slice($api_data_ad_groups_overview, 0, 100);
    
        // PPC - Keywords Overview
        $raw_data_keywords_overview = stripslashes($_POST['api_data_keywords_overview']);
        $api_data_keywords_overview = json_decode($raw_data_keywords_overview, true);
        $api_data_keywords_overview = array_slice($api_data_keywords_overview, 0, 100);
    }

    // Image assets
    $indicator_down_img = CAVENI_IO_URL . 'public/images/decrease-indicator.png';
    $indicator_up_img = CAVENI_IO_URL . 'public/images/increase-indicator.png';

    // ✅ File Paths
    $upload_dir = wp_upload_dir();
    $pdf_dir = trailingslashit($upload_dir['basedir']) . 'caveni-reports/';
    $pdf_url = trailingslashit($upload_dir['baseurl']) . 'caveni-reports/';

    if (!file_exists($pdf_dir)) {
        mkdir($pdf_dir, 0755, true);
    }

    $filename = 'Caveni-' . $service . '-Report-' . str_replace(' ', '-', $company_name) . '-' . time() . '.pdf';
    $pdf_path = $pdf_dir . $filename;
    $pdf_download_url = $pdf_url . $filename;

    try {
        // ✅ Initialize mPDF with Poppins Font Support
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [336, 190],
            'tempDir' => CAVENI_IO_PATH . 'tmp',
            'setAutoTopMargin' => 'stretch',
            'setAutoBottomMargin' => 'stretch',
            'default_font' => 'poppins',
            'default_font_size' => 14,
            'customFonts' => [
                'poppins' => [
                    'R' => 'Poppins-Regular.ttf',
                    'B' => 'Poppins-Bold.ttf',
                    'I' => 'Poppins-Italic.ttf',
                    'BI' => 'Poppins-BoldItalic.ttf'
                ]
            ]
        ]);

        // ✅ Load CSS
        $css_files = [
            CAVENI_IO_PATH . 'public/css/caveni-io-public.css',
            CAVENI_IO_PATH . 'public/css/caveni-io-seo.css'
        ];

        $css_content = '';
        foreach ($css_files as $css_file) {
            if (file_exists($css_file)) {
                $css_content .= file_get_contents($css_file);
            }
        }

        // ✅ Add Poppins Font in CSS
        $poppins_font_css = "
            @font-face {
                font-family: 'Poppins';
                src: url('". CAVENI_IO_URL ."public/fonts/poppins/Poppins-Regular.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
            @font-face {
                font-family: 'Poppins';
                src: url('". CAVENI_IO_URL ."public/fonts/poppins/Poppins-Bold.ttf') format('truetype');
                font-weight: bold;
                font-style: normal;
            }
            @font-face {
                font-family: 'Poppins';
                src: url('". CAVENI_IO_URL ."public/fonts/poppins/Poppins-Italic.ttf') format('truetype');
                font-weight: normal;
                font-style: italic;
            }
            @font-face {
                font-family: 'Poppins';
                src: url('". CAVENI_IO_URL ."public/fonts/poppins/Poppins-BoldItalic.ttf') format('truetype');
                font-weight: bold;
                font-style: italic;
            }
            body {
                font-family: 'Poppins', sans-serif;
                font-size: 14px;
            }
        ";

        $mpdf->WriteHTML('<style>' . $poppins_font_css . '</style>', \Mpdf\HTMLParserMode::HEADER_CSS);

        // ✅ 1st Page: Cover Page
        $background_image = CAVENI_IO_URL . 'public/images/caveni-report-cover.jpg';
        $mpdf->SetMargins(0, 0, 0);
        $mpdf->AddPage();
        $mpdf->Image($background_image, 0, 0, 336, 190, 'jpg', '', true, false);

        // ✅ Title Section
        $mpdf->SetXY(0, 65); // Move title lower for better centering
        $mpdf->SetFont('poppins', 'B', 42);
        $mpdf->Cell(336, 20, $service . " Report For " . $company_name, 0, 1, 'C');

        $mpdf->SetXY(0, 105); // Move subtitle lower
        $mpdf->SetFont('poppins', 'R', 22);
        $mpdf->Cell(336, 20, "Created By Caveni On " . $created_date, 0, 1, 'C');


        // ✅ 2nd Page: Report Content
        $mpdf->AddPage();
        $mpdf->SetDefaultBodyCSS('background', '#0e1424');

        $mpdf->WriteHTML('<img src="' . $top_metrics_image . '" style="width:100%; display:block; margin: 0 auto;">');

        $mpdf->WriteHTML('
            <div style="margin-top: 20px;">
                <img src="' . $caveni_chart_image_seo . '" style="width:100%; display:block; margin: 0 auto;">
            </div>
        ');

        // ✅ 3rd Page: Report Content
        $mpdf->AddPage();
        $mpdf->SetDefaultBodyCSS('background', '#fff');

        // Common CSS
        $mpdf->WriteHTML('
            <style>
                .centered-image {
                    width: 360px;
                    display: block;
                    margin: 0 auto;
                }
                .caveni-pdf-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-family: "Poppins", sans-serif;
                    font-size: 14px;
                }
                .caveni-pdf-table th {
                    color: #48465b;
                    font-size: 14px;
                    font-weight: bold;
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #eee;
                }
                .caveni-pdf-table td {
                    padding: 12px;
                    color: #48465b;
                    font-size: 14px;
                    font-weight: 400;
                    border-bottom: 1px solid #eee;
                }
                .indicator-container {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: 3px 8px;
                    border-radius: 10px;
                    font-size: 10px;
                    font-weight: 500;
                    min-width: 67px;
                    height: 25px;
                    line-height: 1.2;
                    text-align: center;
                    white-space: nowrap;
                    box-sizing: border-box;
                    gap: 4px;
                }
                .indicator-container img {
                    width: 12px;
                    height: 12px;
                    display: inline-block;
                    vertical-align: middle;
                    margin-right: 4px;
                }
                .indicator-container span {
                    display: inline-block;
                    padding: 0 4px;
                    font-size: 10px;
                    font-weight: 600;
                }
                .indicator-up {
                    background-color: #e6faf4;
                    color: #0dcd94;
                    border: 1px solid #b3e5c7;
                }
                .indicator-down {
                    background-color: #ffe9ec;
                    color: #f7284a;
                    border: 1px solid #f5b3bc;
                }
            </style>
        ');

        if($service == "SEO") {
            // ✅ **SEO - Avg Position**
            writeImage($mpdf, $avg_position_image);
            writeTable($mpdf, $api_data_avg_position, ['Keyword', 'Avg Position', 'VS Prev'], 'avg_position');
            $mpdf->AddPage();

            // ✅ **SEO - Impressions by Keyword**
            writeImage($mpdf, $impression_by_keyword_image);
            writeTable($mpdf, $api_data_impressions_by_keyword, ['Keyword', 'Impressions', 'VS Prev'], 'impressions');
            $mpdf->AddPage();

            // ✅ **SEO - Users by Source**
            writeImage($mpdf, $users_by_source_image);
            writeTable($mpdf, $api_data_users_by_source, ['Source', 'Users', 'VS Prev'], 'users');
        }

        if($service == "PPC") {
            // ✅ PPC - Campaigns
            writeImage($mpdf, $avg_position_image);
            writeTablePPC($mpdf, $api_data_campaign_overview, 'Campaigns');
            $mpdf->AddPage();

            // ✅ PPC - Ad Groups
            writeImage($mpdf, $impression_by_keyword_image);
            writeTablePPC($mpdf, $api_data_ad_groups_overview, 'Ad Groups');
            $mpdf->AddPage();

            // ✅ PPC - Keywords
            writeImage($mpdf, $users_by_source_image);
            writeTablePPC($mpdf, $api_data_keywords_overview, 'Keywords');
        }


        // ✅ Save PDF
        $mpdf->Output($pdf_path, 'F');

        // Save to server
        // ✅ If $report_type == "Send", store and email the report
        if ($report_type === "Send") {
            // ✅ Get Client Email
            $client_data = get_userdata($client_id);
            if (!$client_data) {
                wp_send_json_error(['message' => 'Client not found.']);
                return;
            }
            $client_email = $client_data->user_email;

            // ✅ Store Report in Database
            $wpdb->insert(
                $table_name,
                [
                    'client_id'     => $client_id,
                    'pdf_reference' => $pdf_download_url,
                    'created_at'    => current_time('mysql')
                ],
                ['%d', '%s', '%s']
            );

            // ✅ Send Email with PDF Attachment
            $subject = "Your Caveni {$service} Report is Ready!";
            $message = "Hello,\n\nYour Caveni {$service} report is now available. You can download it from the link below:\n\n{$pdf_download_url}\n\nBest regards,\nCaveni Team";

            $headers = ['Content-Type: text/plain; charset=UTF-8'];
            $attachments = [$pdf_path];

            wp_mail($client_email, $subject, $message, $headers, $attachments);
        }

        // ✅ Return PDF URL
        wp_send_json_success([
            'message'    => 'Report generated successfully.',
            'report_url' => $pdf_download_url,
            'report_html_in_pdf' => $report_html
        ]);

    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Failed to generate PDF: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_caveni_generate_report', 'caveni_generate_report');
add_action('wp_ajax_nopriv_caveni_generate_report', 'caveni_generate_report');

function caveni_fetch_seo_data() {
    check_ajax_referer('caveni_reports_nonce', 'security');

    $client_id = $_POST['client'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $service = $_POST['service'];

    if (defined('CAVENI_ENABLE_DUMMY_DATA') && CAVENI_ENABLE_DUMMY_DATA === true) {
        include(CAVENI_IO_PATH . 'includes/modules/dummy-data.php');
    } else {
        if ($service === "SEO") {
            $seo_report_data = generate_seo_report($client_id, $start_date, $end_date);
        } elseif ($service === "PPC") {
            $ppc_report_data = generate_ppc_report($client_id, $start_date, $end_date);
        }
    }
    
    $response_data = ($service === "SEO") ? $seo_report_data : ($service === "PPC" ? $ppc_report_data : []);
    
    wp_send_json_success($response_data);    
}
add_action('wp_ajax_caveni_fetch_seo_data', 'caveni_fetch_seo_data');
add_action('wp_ajax_nopriv_caveni_fetch_seo_data', 'caveni_fetch_seo_data');

function caveni_delete_report() {
    check_ajax_referer('caveni_reports_nonce', 'security');

    if (empty($_POST['report_id'])) {
        wp_send_json_error(['message' => 'Invalid request.']);
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'caveni_client_reports';
    $report_id = intval($_POST['report_id']);

    // ✅ Fetch report details before deleting
    $report = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $report_id));

    if (!$report) {
        wp_send_json_error(['message' => 'Report not found.']);
        return;
    }

    // ✅ Extract the PDF file path
    $pdf_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $report->pdf_reference);

    // ✅ Delete report from database
    $delete = $wpdb->delete($table_name, ['id' => $report_id], ['%d']);

    if ($delete) {
        // ✅ Delete the actual PDF file if it exists
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }

        wp_send_json_success(['message' => 'Report deleted successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete report.']);
    }
}
add_action('wp_ajax_caveni_delete_report', 'caveni_delete_report');
add_action('wp_ajax_nopriv_caveni_delete_report', 'caveni_delete_report'); // Allow non-logged-in users if needed


