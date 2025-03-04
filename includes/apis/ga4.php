<?php

require_once CAVENI_IO_PATH . '/vendor/autoload.php';
use Google\Client;
use Google\Service\Analytics;

/**
 * Fetch total active users for the past 60 days from GA4.
 *
 * @return array Total users for each day in the past 60 days.
 * @throws Exception If an error occurs during the API call.
 */

function get_ga4_total_users($caveni_client_id, $start_date = null, $end_date = null, $metric_name) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date range if not provided
    $start_date = $start_date ?: '60daysAgo';
    $end_date = $end_date ?: 'yesterday';

    // **Check if start_date is in YYYY-MM-DD format**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Convert to timestamps
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
    
        // Ensure we include the full range
        $days_difference = (($end_timestamp - $start_timestamp) / (60 * 60 * 24)) + 1;
    
        // Extend the start date backwards
        $new_start_date = date('Y-m-d', strtotime("-{$days_difference} days", $start_timestamp));
        $start_date = $new_start_date; // Set the new start date
    }    

    // Fetch GA4 Property ID from the first client
    $users = get_users();

    $clients = array();
    foreach ($users as $user) {
        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = get_all_role_titles($role);
        }

        // Check if 'Client' exists in the roles array
        if (in_array('Client', $roles)) {
            $clients[] = array(
                'ID'           => $user->ID,
                'display_name' => $user->display_name,
                'email'        => $user->user_email,
                'roles'        => implode(', ', $roles)
            );
        }
    }

    // Run the API request
    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        // Initialize Google Client
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        // Initialize Analytics Data API
        $analytics = new Google\Service\AnalyticsData($client);

        if($metric_name == "advertiserAdCost") {
            $dimensions = [
                ['name' => 'date'],
                ['name' => 'sessionDefaultChannelGroup'],
            ];
        } else {
            $dimensions = [
                ['name' => 'date'],
            ];
        }

        // Create API request
        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => [
                ['name' => $metric_name],
            ],
            'dateRanges' => [
                ['startDate' => $start_date, 'endDate' => $end_date],
            ],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        // Process the response
        $total_users = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $date = $row->getDimensionValues()[0]->getValue(); // Format: YYYYMMDD
                // $metric_value = intval($row->getMetricValues()[0]->getValue());
                $metric_value = floatval($row->getMetricValues()[0]->getValue());
                $total_users[$date] = $metric_value;
            }
        }
        return $total_users;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// GSC
function get_gsc_data($caveni_client_id, $start_date = null, $end_date = null, $dimensions = ['query'], $row_limit = 1000) {
    $settings = get_option('caveni_settings');
    $gsc_api_creds = $settings['ga4_credentials'];

    $property_url = get_user_meta($caveni_client_id, 'gsc_property_url', true);

    if (!$gsc_api_creds) {
        throw new Exception('GSC credentials are missing!');
    }

    $gsc_api_creds = json_decode($gsc_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GSC credentials JSON!');
    }

    // Split the date range
    $date_ranges = splitDateRange($start_date, $end_date, get_ga4_property_timezone($caveni_client_id));

    try {
        // Initialize Google Client
        $client = new Google\Client();
        $client->setAuthConfig($gsc_api_creds);
        $client->addScope('https://www.googleapis.com/auth/webmasters');

        // Initialize Search Console API
        $searchConsole = new Google\Service\Webmasters($client);

        // Function to fetch data for a given date range
        $fetchData = function ($start, $end) use ($searchConsole, $property_url, $dimensions, $row_limit) {
            $request = new Google\Service\Webmasters\SearchAnalyticsQueryRequest();
            $request->setStartDate($start);
            $request->setEndDate($end);
            $request->setDimensions($dimensions);
            $request->setRowLimit($row_limit);

            $response = $searchConsole->searchanalytics->query($property_url, $request);

            $data = [];
            if ($response->getRows()) {
                foreach ($response->getRows() as $row) {
                    $keyword = $row->getKeys()[0];
                    $clicks = $row->getClicks();
                    $impressions = $row->getImpressions();
                    $ctr = $row->getCtr();
                    $position = $row->getPosition();

                    if (!isset($data[$keyword])) {
                        $data[$keyword] = [
                            'clicks' => 0,
                            'impressions' => 0,
                            'ctr' => 0,
                            'position' => 0,
                            'count' => 0,
                        ];
                    }

                    $data[$keyword]['clicks'] += $clicks;
                    $data[$keyword]['impressions'] += $impressions;
                    $data[$keyword]['ctr'] += $ctr;
                    $data[$keyword]['position'] += $position;
                    $data[$keyword]['count'] += 1;
                }
            }

            return $data;
        };

        // Fetch current and previous period data
        $current_data = $fetchData($date_ranges['current']['start'], $date_ranges['current']['end']);
        $previous_data = $fetchData($date_ranges['previous']['start'], $date_ranges['previous']['end']);

        return [
            'current' => $current_data,
            'previous' => $previous_data,
        ];
    } catch (Exception $e) {
        throw new Exception('GSC API Error: ' . $e->getMessage());
    }
}

// Get Users By Source and Compare This Period with Previous Period
function get_ga4_users_by_source($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date range if not provided
    $start_date = $start_date ?: '60daysAgo';
    $end_date = $end_date ?: 'yesterday';

    // Get GA4 property timezone
    $timezone = get_ga4_property_timezone($caveni_client_id);

    // Split date range into current and previous periods
    $date_ranges = splitDateRange($start_date, $end_date, $timezone);

    // Fetch GA4 Property ID from the client
    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        // Initialize Google Client
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        // Initialize Analytics Data API
        $analytics = new Google\Service\AnalyticsData($client);

        // Helper function to fetch users by source
        $fetchUsersBySource = function ($startDate, $endDate) use ($analytics, $ga4_property_id) {
            $request = new Google\Service\AnalyticsData\RunReportRequest([
                'dimensions' => [
                    ['name' => 'sessionSource'], // Source dimension
                ],
                'metrics' => [
                    ['name' => 'totalUsers'], // Total users metric
                ],
                'dateRanges' => [
                    ['startDate' => $startDate, 'endDate' => $endDate],
                ],
            ]);

            $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

            $data = [];
            if ($response->getRows()) {
                foreach ($response->getRows() as $row) {
                    $source = $row->getDimensionValues()[0]->getValue(); // Source
                    $total_users = intval($row->getMetricValues()[0]->getValue()); // Total users
                    $data[$source] = $total_users;
                }
            }

            return $data;
        };

        // Fetch data for current and previous periods
        $current_data = $fetchUsersBySource($date_ranges['current']['start'], $date_ranges['current']['end']);
        $previous_data = $fetchUsersBySource($date_ranges['previous']['start'], $date_ranges['previous']['end']);

        // Prepare the result
        $result = [];
        foreach ($current_data as $source => $current_users) {
            $previous_users = isset($previous_data[$source]) ? $previous_data[$source] : 0;

            // Calculate the percentage change
            if ($previous_users > 0) {
                $percentage_change = (($current_users - $previous_users) / $previous_users) * 100;
                $trend = $current_users > $previous_users ? 'pre_high' : 'pre_low';
                $formatted_percentage_change = number_format(abs($percentage_change), 1) . '%';
            } elseif ($current_users > 0 && $previous_users == 0) {
                // If there were no users in the previous period, consider it a significant increase
                $trend = 'pre_high';
                $formatted_percentage_change = '100%+';
            } else {
                $trend = 'pre_low';
                $formatted_percentage_change = '0.0%';
            }

            $result[] = [
                'source' => $source,
                'users' => number_format($current_users),
                'trend' => $trend,
                'percentage_change' => $formatted_percentage_change,
            ];
        }

        // Sort result by total users in descending order
        usort($result, function ($a, $b) {
            return $b['users'] <=> $a['users'];
        });

        return $result;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}


/* ---- GOOGLE ADS ----- */
// Get campaigns and their respective costs and impressions
function get_ga4_google_ads_campaigns($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // **If "YYYY-MM-DD" format is used, do NOT double the period, just use it as is**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Do nothing, use start_date and end_date as provided.
    } elseif (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsCampaignName']];
        $metrics = [
            ['name' => 'advertiserAdCost'],
            ['name' => 'advertiserAdImpressions'],
        ];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $campaign_data = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $campaign_name = $row->getDimensionValues()[0]->getValue();
                $ad_cost = floatval($row->getMetricValues()[0]->getValue());
                $impressions = intval($row->getMetricValues()[1]->getValue());

                // Exclude non-Google Ads sources
                $excluded_sources = ['(direct)', '(not set)', '(organic)', '(referral)'];
                if (!in_array(strtolower($campaign_name), array_map('strtolower', $excluded_sources))) {
                    $campaign_data[] = [
                        'campaign' => $campaign_name,
                        'cost' => $ad_cost,
                        'impressions' => $impressions,
                    ];
                }
            }
        }

        return $campaign_data;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get ad groups and their respective costs and impressions
function get_ga4_google_ads_ad_groups($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // **If "YYYY-MM-DD" format is used, do NOT double the period, just use it as is**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Do nothing, use start_date and end_date as provided.
    } elseif (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsAdGroupName']];
        $metrics = [
            ['name' => 'advertiserAdCost'],
            ['name' => 'advertiserAdImpressions'],
        ];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $ad_group_data = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $ad_group_name = $row->getDimensionValues()[0]->getValue();
                $ad_cost = floatval($row->getMetricValues()[0]->getValue());
                $impressions = intval($row->getMetricValues()[1]->getValue());

                // Exclude non-Google Ads sources
                $excluded_sources = ['(direct)', '(not set)', '(organic)', '(referral)'];
                if (!in_array(strtolower($ad_group_name), array_map('strtolower', $excluded_sources))) {
                    $ad_group_data[] = [
                        'ad_group' => $ad_group_name,
                        'cost' => $ad_cost,
                        'impressions' => $impressions,
                    ];
                }
            }
        }

        return $ad_group_data;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get campaign types and their respective costs and impressions
function get_ga4_google_ads_campaign_types($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // Convert NdaysAgo to an integer and halve it
    if (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsCampaignType']];
        $metrics = [
            ['name' => 'advertiserAdCost'],
            ['name' => 'advertiserAdImpressions'],
        ];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $campaign_type_data = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $campaign_type = $row->getDimensionValues()[0]->getValue();
                $ad_cost = floatval($row->getMetricValues()[0]->getValue());
                $impressions = intval($row->getMetricValues()[1]->getValue());

                // Exclude non-Google Ads sources
                $excluded_sources = ['(direct)', '(not set)', '(organic)', '(referral)'];
                if (!in_array(strtolower($campaign_type), array_map('strtolower', $excluded_sources))) {
                    $campaign_type_data[] = [
                        'campaign_type' => $campaign_type,
                        'cost' => $ad_cost,
                        'impressions' => $impressions,
                    ];
                }
            }
        }

        return $campaign_type_data;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get the google ads click data
function get_ga4_google_ads_all_campaigns($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    if (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        // do nothing...
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);

        // Calculate the range duration (number of days)
        $days_difference = ($end_timestamp - $start_timestamp) / (60 * 60 * 24) + 1;

        // Mirror the exact same duration backwards
        $previous_start_date = date('Y-m-d', strtotime("-{$days_difference} days", $start_timestamp));
        $previous_end_date = date('Y-m-d', strtotime("-1 day", $start_timestamp));

        // Override the start_date and end_date with the doubled range
        $start_date = $previous_start_date;
        $end_date = $end_date;
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsCampaignName'], ['name' => 'date']];
        $metrics = [['name' => 'advertiserAdClicks']];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $campaign_clicks = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $campaign_name = $row->getDimensionValues()[0]->getValue();
                $date = $row->getDimensionValues()[1]->getValue();
                $clicks = intval($row->getMetricValues()[0]->getValue());
                
                if (!isset($campaign_clicks[$date])) {
                    $campaign_clicks[$date] = 0;
                }
                
                $campaign_clicks[$date] += $clicks;
            }
        }

        return $campaign_clicks;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get total ad cost
function get_ga4_google_ads_total_cost($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // **If "YYYY-MM-DD" format is used, do NOT double the period, just use it as is**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Do nothing, use start_date and end_date as provided.
    } elseif (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsCampaignName']];
        $metrics = [['name' => 'advertiserAdCost']];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $total_cost = 0;
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $total_cost += floatval($row->getMetricValues()[0]->getValue());
            }
        }

        return $total_cost;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get total impressions across all campaigns for the current period
function get_ga4_google_ads_total_impressions($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // **If "YYYY-MM-DD" format is used, do NOT double the period, just use it as is**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Do nothing, use start_date and end_date as provided.
    } elseif (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        $dimensions = [['name' => 'sessionGoogleAdsCampaignName']];
        $metrics = [['name' => 'advertiserAdImpressions']];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $total_impressions = 0;
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $total_impressions += intval($row->getMetricValues()[0]->getValue());
            }
        }

        return $total_impressions;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get google ads keywords and their respective costs and impressions
function get_ga4_google_ads_keywords($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials'];

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $start_date = $start_date ?: '60daysAgo'; // Default to last 60 days
    $end_date = $end_date ?: 'yesterday';

    // **If "YYYY-MM-DD" format is used, do NOT double the period, just use it as is**
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        // Do nothing, use start_date and end_date as provided.
    } elseif (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = max(1, floor($daysAgo / 2)) . 'daysAgo';
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    try {
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds);
        $client->addScope('https://www.googleapis.com/auth/analytics.readonly');

        $analytics = new Google\Service\AnalyticsData($client);

        // Fetch keyword data
        $dimensions = [['name' => 'sessionGoogleAdsKeyword']];
        $metrics = [
            ['name' => 'advertiserAdImpressions'], // Total Impressions
            ['name' => 'advertiserAdCost'], // Cost
        ];

        $request = new Google\Service\AnalyticsData\RunReportRequest([
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'dateRanges' => [['startDate' => $start_date, 'endDate' => $end_date]],
            'keepEmptyRows' => true,
        ]);

        $response = $analytics->properties->runReport('properties/' . $ga4_property_id, $request);

        $keyword_data = [];
        if ($response->getRows()) {
            foreach ($response->getRows() as $row) {
                $keyword = $row->getDimensionValues()[0]->getValue();
                $impressions = intval($row->getMetricValues()[0]->getValue());
                $cost = floatval($row->getMetricValues()[1]->getValue());

                // Exclude '(not set)' and empty keywords
                if (!empty($keyword) && strtolower($keyword) !== '(not set)') {
                    $keyword_data[] = [
                        'keyword' => $keyword,
                        'impressions' => $impressions,
                        'cost' => $cost,
                    ];
                }
            }
        }

        return $keyword_data;
    } catch (Exception $e) {
        throw new Exception('GA4 API Error: ' . $e->getMessage());
    }
}

// Get impression data from GSC
function get_gsc_total_impressions($caveni_client_id, $start_date = null, $end_date = null) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = $settings['ga4_credentials']; // ✅ Use GA4 credentials

    if (!$ga4_api_creds) {
        throw new Exception('GA4 credentials are missing!');
    }

    $ga4_api_creds = json_decode($ga4_api_creds, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    // Default date handling
    $end_date = $end_date ?: 'yesterday';
    
    // Convert relative date (NdaysAgo) to absolute date
    if (preg_match('/(\d+)daysAgo/', $start_date, $matches)) {
        $daysAgo = intval($matches[1]);
        $start_date = date('Y-m-d', strtotime("-{$daysAgo} days"));
    } else {
        $start_date = date('Y-m-d', strtotime($start_date));
    }

    // Convert end date if it's a relative term
    if ($end_date === 'yesterday') {
        $end_date = date('Y-m-d', strtotime('-1 day'));
    } elseif ($end_date === 'today') {
        $end_date = date('Y-m-d');
    } else {
        $end_date = date('Y-m-d', strtotime($end_date));
    }

    // Fetch GSC property URL
    $gsc_property_url = get_user_meta($caveni_client_id, 'gsc_property_url', true); // ✅ Fixed this

    try {
        // Initialize Google Client
        $client = new Google\Client();
        $client->setAuthConfig($ga4_api_creds); // ✅ Uses GA4 Credentials
        $client->addScope('https://www.googleapis.com/auth/webmasters.readonly');

        // Initialize Search Console API
        $searchConsole = new Google\Service\SearchConsole($client);

        // Prepare the API request
        $request = new Google\Service\SearchConsole\SearchAnalyticsQueryRequest();
        $request->setStartDate($start_date);
        $request->setEndDate($end_date);
        $request->setDimensions(['date']);
        $request->setRowLimit(10000); // Adjust if needed

        // Fetch impressions
        $response = $searchConsole->searchanalytics->query($gsc_property_url, $request); // ✅ Uses gsc_property_url

        $total_impressions = [];
        if (!empty($response->getRows())) {
            foreach ($response->getRows() as $row) {
                $date = str_replace('-', '', $row->keys[0]); // Convert YYYY-MM-DD to YYYYMMDD
                $impressions = intval($row->impressions); // Fetch total impressions

                $total_impressions[$date] = $impressions;
            }
        }

        return $total_impressions;
    } catch (Exception $e) {
        throw new Exception('GSC API Error: ' . $e->getMessage());
    }
}

// Get GA4 Property Details and Check if Google Ads is Linked
function get_ga4_property_details($caveni_client_id) {
    $settings = get_option('caveni_settings');
    $ga4_api_creds = json_decode($settings['ga4_credentials'] ?? '', true);

    if (!$ga4_api_creds || json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid GA4 credentials JSON!');
    }

    $ga4_property_id = get_user_meta($caveni_client_id, 'ga4_property_id', true);

    if (!$ga4_property_id) {
        throw new Exception('GA4 property ID is missing!');
    }

    $access_token = get_google_access_token($ga4_api_creds);
    if (!$access_token) {
        return 'Error: Failed to retrieve access token.';
    }

    // Fetch GA4 property details
    $property_url = "https://analyticsadmin.googleapis.com/v1beta/properties/{$ga4_property_id}";
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Accept' => 'application/json'
        ]
    ];

    $property_response = wp_remote_get($property_url, $args);
    if (is_wp_error($property_response)) {
        return 'Error: ' . $property_response->get_error_message();
    }

    $property_body = wp_remote_retrieve_body($property_response);
    $property_data = json_decode($property_body, true);

    if (isset($property_data['error'])) {
        return 'Error: ' . $property_data['error']['message'];
    }

    // ✅ Fetch Google Ads Links for this GA4 property
    $ads_links_url = "https://analyticsadmin.googleapis.com/v1beta/properties/{$ga4_property_id}/googleAdsLinks";
    $ads_response = wp_remote_get($ads_links_url, $args);
    if (is_wp_error($ads_response)) {
        return 'Error: ' . $ads_response->get_error_message();
    }

    $ads_body = wp_remote_retrieve_body($ads_response);
    $ads_data = json_decode($ads_body, true);

    // Check if any Google Ads accounts are linked
    $google_ads_linked = isset($ads_data['googleAdsLinks']) && count($ads_data['googleAdsLinks']) > 0;

    return [
        'id' => $property_data['name'] ?? 'Not Available',
        'displayName' => $property_data['displayName'] ?? 'Not Available',
        'currency' => $property_data['currencyCode'] ?? 'Not Available',
        'timeZone' => $property_data['timeZone'] ?? 'Not Available',
        'createTime' => $property_data['createTime'] ?? 'Not Available',
        'updateTime' => $property_data['updateTime'] ?? 'Not Available',
        'googleAdsLinked' => $google_ads_linked // ✅ New field: true/false if Google Ads is linked
    ];
}

// Get Google API access token using Service Account JSON.
function get_google_access_token($ga4_api_creds) {
    $jwtHeader = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $now = time();
    $jwtClaim = [
        'iss' => $ga4_api_creds['client_email'],
        'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $jwtHeaderEncoded = base64_encode(json_encode($jwtHeader));
    $jwtClaimEncoded = base64_encode(json_encode($jwtClaim));

    // Create signature
    openssl_sign(
        "{$jwtHeaderEncoded}.{$jwtClaimEncoded}",
        $signature,
        openssl_pkey_get_private($ga4_api_creds['private_key']),
        'SHA256'
    );

    $jwt = "{$jwtHeaderEncoded}.{$jwtClaimEncoded}." . base64_encode($signature);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    return $data['access_token'] ?? false;
}

// Get GA4 Property Timezone
function get_ga4_property_timezone($caveni_client_id) {
    $property_details = get_ga4_property_details($caveni_client_id);

    if (isset($property_details['timeZone']) && !empty($property_details['timeZone'])) {
        return $property_details['timeZone'];
    }

    return 'UTC'; // Default to UTC if timezone is not available
}


