<div class="modal fade" id="crm_newsmodal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fe fe-file-text"></i>
                <h5 class="modal-title" id="crm_modal_title"> <?php echo __('Add New Report', 'caveni-io'); ?></h5> 
                <button type="button" class="btn-close btn-close-report" data-bs-dismiss="modal" aria-label="Close"><i class="fe fe-x"></i></button>
            </div>
            <div class="modal-body">
                <form id="crm_addform" method="post" enctype="multipart/form-data">

                    <!-- Hidden input to capture which button was clicked -->
                    <input type="hidden" name="submissionType" id="submissionType" value="">

                    <!-- Hidden fields for capturing company details -->
                    <input type="hidden" name="crm_company_id" id="crm_company_id" value="">
                    <input type="hidden" name="crm_company_name" id="crm_company_name" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> <?php echo __('Client', 'caveni-io'); ?></label><span class="text-danger">*</span>

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
                                                'ID'      => $user->ID,
                                                'company' => $company 
                                            );
                                        }
                                    }
                                ?>

                                <!-- Company Select -->
                                <select class="form-control crm_select client-search-seo" name="crm_company_id" id="crm_company" required>
                                    <option value="">Select Company</option>
                                    <?php
                                    if (!empty($clients)) {
                                        foreach ($clients as $client) {
                                            echo sprintf(
                                                '<option value="%d" data-company="%s">%s</option>',
                                                esc_attr($client['ID']),
                                                esc_attr($client['company']), // Store company name as a data attribute
                                                esc_html($client['company']) 
                                            );
                                        }
                                    } else {
                                        echo '<option value="">No Company Found</option>';
                                    }
                                    ?>
                                </select>

                                <!-- Hidden Input for Company Name -->
                                <input type="hidden" name="crm_company_name" id="crm_company_name" value="">

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> <?php echo __('Service', 'caveni-io'); ?></label><span class="text-danger">*</span>
                                <div class="crm-report-type-selector">
                                    <select class="form-control" name="crm_service" id="crm_service" required>
                                        <option value="">Select Service</option>
                                        <option value="SEO">SEO</option>
                                        <option value="PPC">PPC</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"> <?php echo __('Range', 'caveni-io'); ?></label><span class="text-danger">*</span>
                                <input type="text" id="crm_date_range" class="form-control" name="crm_date_range" placeholder="Select Date" required>
                            </div>
                        </div>
                    </div>

                    <span class="error alert" style="display: none;" id="error-report"></span>
                    <div class="modal-footer">
                        <button type="submit" class="btn text-dark outline-btn submission-btn" value="Download"><?php echo __('Download', 'caveni-io'); ?></button>
                        <button type="submit" class="btn text-dark outline-btn submission-btn" value="Schedule"><?php echo __('Schedule', 'caveni-io'); ?></button>
                        <button type="submit" class="btn btn-success m-2 full-btn submission-btn" value="Send"><?php echo __('Send', 'caveni-io'); ?></button>
                        <button type="submit" class="force-hide addreport_submit btn btn-success successful-notify full-btn submission-btn" value="Submit"><?php echo __('Submit', 'caveni-io'); ?></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
