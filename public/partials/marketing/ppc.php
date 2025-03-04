<?php if( is_page('ppc')) { echo do_shortcode('[caveni_filter_settings]'); } ?>

<?php
    $caveni_loader = '<img style="display:none;" class="caveni-loader" src="' . CAVENI_IO_URL . '/public/images/skeleton-loader-graph.gif">';
?>

<div id="top-metrics-ppc" class="top-metrics-container">
    <!-- CPC Chart -->
    <div class="caveni-box">
        <div class="caveni-box-title">
            <div class="caveni-icon">
                <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
            </div>
            <h3>CPC</h3>
        </div>
        <div class="loader-container">
            <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
        </div>
        <div class="caveni-meter-chart" id="cpc-chart"></div>
    </div>

    <!-- CPM Chart -->
    <div class="caveni-box">
        <div class="caveni-box-title">
            <div class="caveni-icon">
                <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
            </div>
            <h3>CPM</h3>
        </div>
        <div class="loader-container">
            <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
        </div>
        <div class="caveni-meter-chart" id="cpm-chart"></div>
    </div>

    <!-- CTR Chart -->
    <div class="caveni-box">
        <div class="caveni-box-title">
            <div class="caveni-icon">
                <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
            </div>
            <h3>CTR</h3>
        </div>
        <div class="loader-container">
            <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
        </div>
        <div class="caveni-meter-chart" id="ctr-chart"></div>
    </div>

    <!-- COST Chart -->
    <div class="caveni-box">
        <div class="caveni-box-title">
            <div class="caveni-icon">
                <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
            </div>
            <h3>COST</h3>
        </div>
        <div class="loader-container">
            <img src="<?= CAVENI_IO_URL . '/public/images/metrics-loader.gif' ?>" alt="Loading..." class="loader-gif">
        </div>
        <div class="caveni-meter-chart" id="cost-chart"></div>
    </div>
</div>

<div class="caveni-seo-section caveni-ppc">
    <div class="seo-left-col">

        <!-- Clicks Chart -->
        <div class="caveni-box">
            
            <div class="caveni-box-title">
                <div class="caveni-icon">
                    <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                </div>
                <h3>Clicks</h3>
            </div>

            <?= $caveni_loader ?>

            <div class="caveni-total-clicks caveni-box-main-metric force-hide"></div>

            <div id="clicks-chart-ppc"></div>
        </div>

        <!-- Conversions Chart -->
        <div class="caveni-box">
            
            <div class="caveni-box-title">
                <div class="caveni-icon">
                    <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                </div>
                <h3>Conversions</h3>
            </div>

            <?= $caveni_loader ?>

            <div class="caveni-total-conversions caveni-box-main-metric force-hide"></div>

            <div id="conversions-chart-ppc"></div>
        </div>

        <div class="caveni-container-row">
            <!-- Campaigns Overview -->
            <div class="caveni-box ppc-overview-box caveni-campaigns-overview">
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                    </div>
                    <h3>Campaigns Overview</h3>
                </div>
                <div class="caveni-table-reponsive">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dimension</th>
                                <th>IMPR.</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody id="campaign_body">
                        </tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>

            <!-- Ad Groups Overview -->
            <div class="caveni-box ppc-overview-box caveni-adgroups-overview">
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                    </div>
                    <h3>Ad Groups Overview</h3>
                </div>
                <div class="caveni-table-reponsive">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dimension</th>
                                <th>IMPR.</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody id="ad_group_body">
                        </tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>

            <!-- Keywords Overview -->
            <div class="caveni-box ppc-overview-box caveni-keywords-overview">
                <div class="caveni-box-title">
                    <div class="caveni-icon">
                        <img src="<?= CAVENI_IO_URL . 'public/images/fe-mouse-pointer.svg' ?>">
                    </div>
                    <h3>Keywords Overview</h3>
                </div>
                <div class="caveni-table-reponsive">
                    <table class="caveni-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dimension</th>
                                <th>IMPR.</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody id="ads_body">
                        </tbody>
                    </table>
                    <div class="loader-container">
                        <img src="<?= CAVENI_IO_URL . '/public/images/table-data-loader.gif' ?>" alt="Loading..." class="loader-gif">
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
<div class="ci-loader" style="display: none;">
    <img src="<?php echo CAVENI_IO_URL . '/public/images/loader.svg'; ?>" alt="loader">
</div>