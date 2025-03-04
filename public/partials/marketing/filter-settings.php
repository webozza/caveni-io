<?php
    $svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="30" height="30">
        <rect width="64" height="64" rx="12" fill="#E7E8FC"/>
        <circle cx="16" cy="20" r="3" fill="#5A66F0"/>
        <rect x="22" y="18" width="26" height="4" rx="2" fill="#5A66F0"/>
        <circle cx="16" cy="32" r="3" fill="#5A66F0"/>
        <rect x="22" y="30" width="26" height="4" rx="2" fill="#5A66F0"/>
        <circle cx="16" cy="44" r="3" fill="#5A66F0"/>
        <rect x="22" y="42" width="26" height="4" rx="2" fill="#5A66F0"/>
    </svg>';

    $increase_indicator = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 17 9 11 13 15 21 7"></polyline>
        <path d="M19 7h2v2"></path>
    </svg>';

    $decrease_indicator = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 7 9 13 13 9 21 17"></polyline>
        <path d="M19 17h2v-2"></path>
    </svg>';

    $caveni_loader = '<img style="display:none;" class="caveni-loader" src="' . CAVENI_IO_URL . '/public/images/skeleton-loader-graph.gif">';

    // Use get_users() to fetch all users matching the criteria
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
                'roles'        => implode(', ', $roles)
            );
        }
    }

    if(isset($_POST['caveni_start_date'])) {
        $selected_start_date = $_POST['caveni_start_date'];

        if($selected_start_date == "2daysAgo") {
            $custom_start_date = date('m/d/Y', strtotime('-1 day'));
            $custom_end_date = date('m/d/Y', strtotime('-1 day'));
        } elseif ($selected_start_date == "14daysAgo") {
            $custom_start_date = date('m/d/Y', strtotime('-7 days'));
            $custom_end_date = date('m/d/Y', strtotime('-1 day'));
        } elseif ($selected_start_date == "60daysAgo") {
            $custom_start_date = date('m/d/Y', strtotime('-30 days'));
            $custom_end_date = date('m/d/Y', strtotime('-1 day'));
        } elseif ($selected_start_date == "360daysAgo") {
            $custom_start_date = date('m/d/Y', strtotime('-180 days'));
            $custom_end_date = date('m/d/Y', strtotime('-1 day'));
        } elseif ($selected_start_date == "730daysAgo") {
            $custom_start_date = date('m/d/Y', strtotime('-360 days'));
            $custom_end_date = date('m/d/Y', strtotime('-1 day'));
        } else {
            $custom_start_date = date('m/d/Y', strtotime($_POST['caveni_start_date']));
            $custom_end_date = date('m/d/Y', strtotime($_POST['caveni_end_date']));
        }
    } else {
        $custom_start_date = date('m/d/Y', strtotime('-30 days'));
        $custom_end_date = date('m/d/Y', strtotime('-1 day'));
    }
    
?>

<script>
    let increaseIndicator = `<?= $increase_indicator ?>`;
    let decreaseIndicator = `<?= $decrease_indicator ?>`;
    let svgIcon = `<?= $svg_icon ?>`;
</script>

<div class="ticket-summary-block search-seo-summary">
    <div class="card custom-card">
        <div class="custom-header">
            <div class="cilent-title">
                <i class="fe fe-filter text-primary"></i>
                <h4>Filter Settings</h4>
            </div>
        </div>
        
        <?php if ( current_user_can( 'administrator' ) ) { ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group <?= is_page('analytics') ? "caveni--analytics-pg" : "" ?>">
                                    <select class="form-control select2 client-search-seo" data-trigger="" data-placeholder="Test" fdprocessedid="7b7n2a">
                                        <?php
                                            foreach ($clients as $client) {
                                                // Check if 'Client' is in the roles string
                                                $selected = (isset($_POST['client_search_form']) && $_POST['client_search_form'] == "1" && $_POST['client_search'] == $client['ID']) ? 'selected' : '';
                                                echo sprintf(
                                                    '<option value="%d" %s>%s</option>',
                                                    $client['ID'], // User ID as value
                                                    $selected,     // Add 'selected' if this is the selected client
                                                    esc_html($client['display_name']) // Client display name
                                                );
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 force-hide">
                        <div class="form-group">
                            <a class="client-search-trigger for-seo" href="javascript:void(0);" class="btn btn-primary d-grid">SUBMIT</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if(is_page('analytics')) { ?>
            <div class="caveni--filter-tabs filter-seo-ppc active-left">
                <button class="caveni--tab-option caveni--active" data-value="seo">
                    <i class="fe fe-bar-chart-2 text-primary"></i>
                    SEO
                </button>
                <button class="caveni--tab-option" data-value="ppc">
                    <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                    PPC
                </button>
                <div class="caveni--tab-highlight"></div>
            </div>
        <?php } ?>

        <div class="caveni--filter-tabs seo-date-filters">
            <i class="fe fe-calendar text-primary"></i>
            <select class="caveni--select-option">
                <option value="730daysAgo" <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '730daysAgo') ? 'selected' : '' ?>>1 Year</option>
                <option value="360daysAgo" <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '360daysAgo') ? 'selected' : '' ?>>6 Months</option>
                <option value="60daysAgo" <?= (!isset($_POST['caveni_start_date']) || $_POST['caveni_start_date'] === '60daysAgo') ? 'selected' : '' ?>>30 Days</option>
                <option value="14daysAgo" <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '14daysAgo') ? 'selected' : '' ?>>7 Days</option>
                <option value="2daysAgo" <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '2daysAgo') ? 'selected' : '' ?>>24 Hours</option>
                <option disabled value="custom" <?= (isset($_POST['custom_date_selected']) && $_POST['custom_date_selected'] == '1') ? 'selected' : '' ?>>Custom</option>
            </select>
        </div>

        <div class="caveni--custom-date-btn">
            <a href="javascript:void(0);" class="btn btn-primary d-grid">CUSTOM</a>
        </div>
    </div>
</div>

<div class="caveni-chart-filters custom-date-filter-modal">
    <div class="filter-section">
        <h4>Popular <i class="fe fe-chevron-down"></i></h4>
        <div class="filter-items">
            <div class="filter-item">Week to Date</div>
            <div class="filter-item">Last Week</div>
            <div class="filter-item">Month to Date</div>
            <div class="filter-item">Last Month</div>
        </div>
    </div>
    <div class="filter-section">
        <h4>Current <i class="fe fe-chevron-down"></i></h4>
        <div class="filter-items">
            <div class="filter-item">Today</div>
            <div class="filter-item">This Week</div>
            <div class="filter-item">This Month</div>
            <div class="filter-item">This Quarter</div>
            <div class="filter-item">This Year</div>
            <hr>
            <div class="filter-item">Week to Date</div>
            <div class="filter-item">Month to Date</div>
            <div class="filter-item">Quarter to Date</div>
            <div class="filter-item">Year to Date</div>
        </div>
    </div>
    <div class="filter-section">
        <h4>Previous <i class="fe fe-chevron-down"></i></h4>
        <div class="filter-items">
            <div class="filter-item">Yesterday</div>
            <div class="filter-item">Last Week</div>
            <div class="filter-item">Last Month</div>
            <div class="filter-item">Last Quarter</div>
            <div class="filter-item">Last Year</div>
        </div>
    </div>

    <div class="filter-section">
        <h4>Custom Range</h4>
        <div class="filter-items">
            <div class="filter-search">
                <input type="text" value="<?= $custom_start_date ?>" id="custom_start_date" autocomplete="off" style="display: none;">
                <input type="text" value="<?= $custom_end_date ?>" id="custom_end_date" autocomplete="off" style="display: none;">
            </div>
        </div>
    </div>
</div>

<form class="seo-client-search-form" method="POST" action="">
    <input type="hidden" name="client_search" value="<?= (isset($_POST['client_search_form']) && $_POST['client_search_form'] == "1") ? $_POST['client_search'] : $clients[0]['ID'] ?>">
    <input type="hidden" name="caveni_start_date" value="<?= (isset($_POST['caveni_start_date']) && !empty($_POST['caveni_start_date'])) ? $_POST['caveni_start_date'] : '60daysAgo' ?>">
    <input type="hidden" name="caveni_end_date" value="<?= (isset($_POST['caveni_end_date']) && !empty($_POST['caveni_end_date'])) ? $_POST['caveni_end_date'] : 'yesterday' ?>">
    <input type="hidden" name="client_search_form" value="1">
    <input type="hidden" name="custom_date_selected" value="<?= isset($_POST['custom_date_selected']) ? $_POST['custom_date_selected'] : '0'; ?>">
</form>

<div class="caveni-filter-container force-hide">

    <div class="caveni-box-header" bis_skin_checked="1">
        <div class="caveni-flex" bis_skin_checked="1">
            <div class="caveni-icon" bis_skin_checked="1">
            <i class="fe fe-file-text"></i>
            </div>
            <div class="caveni-title" bis_skin_checked="1">Filter Settings</div>
        </div>
        <div class="info-icon" bis_skin_checked="1">
        <i class="fas fa-info-circle"></i>
        </div>
    </div>

    <div class="caveni-filters">
        <?php if(is_page('analytics')) { ?>
            <div class="caveni--filter-tabs filter-seo-ppc">
                <button class="caveni--tab-option caveni--active" data-value="seo">SEO</button>
                <button class="caveni--tab-option" data-value="ppc">PPC</button>
            </div>
        <?php } ?>

        <div class="caveni--filter-tabs seo-date-filters">
            <button class="caveni--tab-option <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '730daysAgo') ? 'caveni--active' : '' ?>" data-value="730daysAgo">1 Year</button>
            <button class="caveni--tab-option <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '360daysAgo') ? 'caveni--active' : '' ?>" data-value="360daysAgo">6 Months</button>
            <button class="caveni--tab-option <?= (!isset($_POST['caveni_start_date']) || $_POST['caveni_start_date'] === '60daysAgo') ? 'caveni--active' : '' ?>" data-value="60daysAgo">30 Days</button>
            <button class="caveni--tab-option <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '14daysAgo') ? 'caveni--active' : '' ?>" data-value="14daysAgo">7 Days</button>
            <button class="caveni--tab-option <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '2daysAgo') ? 'caveni--active' : '' ?>" data-value="2daysAgo">24 Hours</button>
            <button class="caveni--tab-option trigger-custom-range <?= (isset($_POST['caveni_start_date']) && $_POST['caveni_start_date'] === '2025-01-22') ? 'caveni--active' : '' ?>" data-value="2025-01-22">Custom</button>
        </div> 
    </div>
</div>