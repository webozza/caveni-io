<?php
// Include the add-report.php file (modal content)
include_once CAVENI_IO_PATH . 'includes/modules/add-report.php';
?>

<div class="col-xl-12 crm_dash_report">
    <div class="row">
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
                                <!-- REPORTS GO HERE -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="caveni__seo-report"><?php include CAVENI_IO_PATH . 'includes/modules/seo-report.php'; ?></div>
<div id="caveni__ppc-report"></div>
