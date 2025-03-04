
<div class="modal fade" id="caveni-newreportmodal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fe fe-file-text"></i>
                <h5 class="modal-title" id="caveni-report-modal-title"> <?php echo __('Add New Report', 'caveni-io'); ?></h5> 
                <button type="button" class="btn-close btn-close-report" data-bs-dismiss="modal" aria-label="Close"><i class="fe fe-x"></i></button>
            </div>
            <div class="modal-body">
                <form id="caveni-addReportForm" method="post" enctype="multipart/form-data">
                    
                    <input type="hidden" name="action" value="caveni_io_add_new_report">
                    <input type="hidden" name="report-id-update" id="report-id-update" value="">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> <?php echo __('Client', 'caveni-io'); ?></label><span class="text-danger">*</span>
                                
                                <!-- Client Company -->
                                <?php
                                    // Fetch all users
                                    $args = array('orderby' => 'display_name', 'order' => 'ASC');
                                    $users = get_users($args);

                                    $clients = array();
                                    foreach ($users as $user) {
                                        $roles = array_map('get_all_role_titles', $user->roles);

                                        // Check if the user has the "Client" role
                                        if (in_array('Client', $roles)) {
                                            // Fetch the 'company' meta value for each user
                                            $company = get_user_meta($user->ID, 'company', true);

                                            $clients[] = array(
                                                'ID'           => $user->ID,
                                                'company'      => $company // Fetch 'company' field instead of 'display_name'
                                            );
                                        }
                                    }
                                    ?>

                                <select class="form-control report-client-select client-search-seo" name="caveni_report_client" id="caveni-report-client" required>
                                    <option value="">Select Client</option>
                                    <?php
                                    if (!empty($clients)) {
                                        foreach ($clients as $client) {
                                            echo sprintf(
                                                '<option value="%d">%s</option>',
                                                esc_attr($client['ID']),
                                                esc_html($client['company']) // Display the 'company' value
                                            );
                                        }
                                    } else {
                                        echo '<option value="">No Clients Found</option>';
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>

                         <!-- Services Dropdown -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> <?php echo __('Services', 'caveni-io'); ?></label><span class="text-danger">*</span>
                                <select class="form-control" name="caveni_report_service" id="caveni-report-service" required>
                                    <option value="">Select Service</option>
                                    <option value="SEO">SEO</option>
                                    <option value="PPC">PPC</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                    <!-- Period Picker (Full Width) -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <?php echo __('Select Period', 'caveni-io'); ?></label><span class="text-danger">*</span>
                            <input type="text" id="caveni-report-period" class="form-control" name="caveni_report_period" placeholder="Select Period" required>
                            </div>
                    </div>
                </div>

                <!-- buttons section  -->
                <div class="row">
                        <div class="col-md-12">
                            <div class="form-group d-flex justify-content-start">
                                <button type="button" class="btn btn-primary m-2" id="download-btn"><?php echo __('Download', 'caveni-io'); ?></button>
                                <button type="button" class="btn btn-secondary m-2" id="schedule-btn"><?php echo __('Schedule', 'caveni-io'); ?></button>
                                <button type="button" class="btn btn-success m-2" id="send-btn"><?php echo __('Send', 'caveni-io'); ?></button>
                            </div>
                        </div>
                    </div>

                </div>


                    <div class="form-check caveni_report_notify_hide">
                        <input class="form-check-input" type="checkbox" value="1" id="caveni_report_notify" name="caveni_report_notify">
                        <label class="form-check-label" for="caveni_report_notify">
                            <?php echo __('Notify user about this report', 'caveni-io'); ?>
                        </label>
                    </div>
                </form>

                <span class="error alert" style="display: none;" id="error-report"></span>
                <div class="modal-footer"> 
                    <button class="btn btn-outline-primary btn-close btn-close-report" data-bs-dismiss="modal">
                        <?php echo __('Close', 'caveni-io'); ?>
                    </button> 
                    <input type="submit" class="addreport_submit btn btn-success successful-notify">
                </div>
            </div>
        </div>
    </div>
</div>


