<div class="col-xl-12 dashboard-reports-by-client">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 report-summary-block">
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
                            <div class="col col-auto add-report-button">
                                
                                    <a href="<?php echo home_url( $wp->request )."/reports?_report_action=add"; ?>"
                                        class="btn btn-primary btn-w-md btn-wave"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="ADD NEW REPORT">
                                        <?php echo __('ADD REPORT','caveni-io'); ?> 
                                    </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body reports-home-page">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert d-none m-3" id="deleted_report_confirmation_alert" role="alert"></div>
                        </div>
                    </div>
                    <div class="table-responsive reports-table-area">
                        <table
                            class="table table-vcenter text-nowrap table-bordered border-bottom"
                            id="supportreport-dash" style="width:100%">
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