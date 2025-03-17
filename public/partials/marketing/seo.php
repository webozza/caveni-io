<?php if( is_page('seo')) { echo do_shortcode('[caveni_filter_settings]'); } ?>

<?php
    $caveni_loader = '<img style="display:none;" class="caveni-loader" src="' . CAVENI_IO_URL . '/public/images/skeleton-loader-graph.gif">';
?>

<div id="top-metrics" class="top-metrics-container">
    <div class="caveni-box">
        <div class="caveni-box-header">
            <div class="caveni-flex">
                <div class="caveni-icon"><i class="fe fe-user-plus"></i></div>
                <div class="caveni-title">New Users</div>
            </div>
            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
        </div>
        <div class="caveni-box-content">
            <div class="loader-container">
                <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
            </div>
        </div>
    </div>

    <div class="caveni-box">
        <div class="caveni-box-header">
            <div class="caveni-flex">
                <div class="caveni-icon"><i class="fe fe-clock"></i></div>
                <div class="caveni-title">Session Duration</div>
            </div>
            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
        </div>
        <div class="caveni-box-content">
            <div class="loader-container">
                <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
            </div>
        </div>
    </div>

    <div class="caveni-box">
        <div class="caveni-box-header">
            <div class="caveni-flex">
                <div class="caveni-icon"><i class="fe fe-eye"></i></div>
                <div class="caveni-title">Views per Session</div>
            </div>
            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
        </div>
        <div class="caveni-box-content">
            <div class="loader-container">
                <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
            </div>
        </div>
    </div>

    <div class="caveni-box">
        <div class="caveni-box-header">
            <div class="caveni-flex">
                <div class="caveni-icon"><i class="fe fe-external-link"></i></div>
                <div class="caveni-title">Bounce Rate</div>
            </div>
            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
        </div>
        <div class="caveni-box-content">
            <div class="loader-container">
                <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
            </div>
        </div>
    </div>
</div>

<div class="caveni-seo-section">
    <div class="seo-left-col">

        <div id="caveni__impression-chart" class="caveni-box">

            <div class="caveni-box-title">
                <div class="caveni-icon">
                    <i class="fe fe-target"></i>
                </div>
                <h3>Impressions</h3>
            </div>

            <?= $caveni_loader ?>

            <div class="caveni-impressions caveni-box-main-metric force-hide">
            </div>
            
            <div id="impression-chart-seo"></div>
        </div>

        <div id="caveni__user-chart" class="caveni-box">
            
            <div class="caveni-box-title">
                <div class="caveni-icon">
                    <i class="fe fe-users"></i>
                </div>
                <h3>Total Users</h3>
            </div>

            <?= $caveni_loader ?>

            <div class="caveni-total-users caveni-box-main-metric force-hide">
            </div>

            <!-- <div class="caveni-value">570</div>
            <div class="caveni-subtext">▲ 18% vs previous period (200)</div> -->
            <div id="user-chart-seo"></div>
        </div>

        <div class="caveni-container-row">
            <!-- Keyword Avg. Position -->
            <div class="caveni-box">
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <i class="fe fe-hash"></i>
                    </div>
                    <h3>Average Position</h3>
                </div>

                <div class="caveni-table-reponsive keyword-avg-position">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Value</th>
                                <th>VS Prev</th>
                            </tr>
                        </thead>
                        <tbody id="avg_position_body">
                            
                        </tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>

            <!-- Impressions by Keyword -->
            <div class="caveni-box">
                
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <img src="<?= CAVENI_IO_URL . 'public/images/fe-key.svg' ?>">
                    </div>
                    <h3>Impressions By Keyword</h3>
                </div>

                <div class="caveni-table-reponsive impressions-by-keyword">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Value</th>
                                <th>VS Prev</th>
                            </tr>
                        </thead>
                        <tbody id="keyword_body">
                            
                        </tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>

            <!-- Users by Soure -->
            <div class="caveni-box">
                
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <i class="fe fe-user-check"></i>
                    </div>
                    <h3>Users By Source</h3>
                </div>

                <div class="caveni-table-reponsive">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Value</th>
                                <th>VS Prev</th>
                            </tr>
                        </thead>
                        <tbody id="source_body"></tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>