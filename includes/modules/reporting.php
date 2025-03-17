<?php

// Include the add-report.php file (modal content)
include_once CAVENI_IO_PATH . 'includes/modules/add-report.php';
include_once CAVENI_IO_PATH . 'includes/modules/modal-pdf-viewer.php';

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
?>

<script>
    let increaseIndicator = `<?= $increase_indicator ?>`;
    let decreaseIndicator = `<?= $decrease_indicator ?>`;
    let svgIcon = `<?= $svg_icon ?>`;
</script>

<div class="col-xl-12 crm_dash_report">
    <div class="row">

        <!-- Cabeni Client Reports -->
        <div class="col-xl-12 col-lg-12 col-md-12 crm_report_summuery">
            <div class="card custom-card mb-0">
                <div class="card-header border-0">
                    <div class="row align-items-center flex-grow-1">
                        <div class="col">
                            
                                <h4 class="card-title">
                                    <i class="fe fe-file-text"></i>
                                    <?php echo __('Reports Summary','caveni-io'); ?>
                                </h4>
                            
                        </div>
                        <?php if ( current_user_can( 'administrator' ) ) { ?>
                            <div class="col col-auto crm_add_report_button">
                                <a id="crm_report_add" class="btn btn-primary btn-w-md btn-wave text-white">
                                    <?php echo __('ADD REPORT', 'caveni-io'); ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body crm_reports_homepage">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert d-none m-3" id="deleted_report_confirmation_alert" role="alert"></div>
                        </div>
                    </div>
                    <div class="table-responsive crm_table_area">
                        <table
                            class="table table-vcenter text-nowrap table-bordered border-bottom"
                            id="crm_support_dash" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#<?php echo __('ID','caveni-io'); ?></th>
                                    <th class="border-bottom-0"><?php echo __('Company','caveni-io'); ?></th>
                                    <th class="border-bottom-0"><?php echo __('Report','caveni-io'); ?></th>
                                    <th class="border-bottom-0"><?php echo __('Date','caveni-io'); ?></th>
                                    <th class="border-bottom-0"><?php echo __('File','caveni-io'); ?></th>
                                    <th class="border-bottom-0"><?php echo __('Actions','caveni-io'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php include(CAVENI_IO_PATH . 'includes/modules/loop-client-reports.php');  ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1) : ?>
                            <nav>
                                <ul class="pagination">
                                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                        <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?paged=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Report Generation -->
        <div class="col-xl-12 col-lg-12 col-md-12 crm-report-generation" style="margin-top: 50px; opacity: 0;">
            <div id="caveni__ppc-report"><?php include CAVENI_IO_PATH . 'includes/modules/ppc-report.php'; ?></div>
            
            <br>
            <hr style="height: 10px; background: black;">
            <br>

            <div id="caveni__seo-report"><?php include CAVENI_IO_PATH . 'includes/modules/seo-report.php'; ?></div>
        </div>
    </div>
</div>

