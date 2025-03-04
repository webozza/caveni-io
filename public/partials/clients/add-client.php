<?php

global $wp_roles;
if (!isset($wp_roles)) {
    $wp_roles = new WP_Roles();
}
$roles = $wp_roles->roles;
?>

<div class="modal fade" id="caveni-newclientmodal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <i class="fe fe-user-plus"></i>
                <h5 class="modal-title" id="caveni-modal-title"> <?php echo __('Add New Client', 'caveni-io'); ?></h5> <button type="button" class="btn-close btn-close-client" data-bs-dismiss="modal" aria-label="Close"><i class="fe fe-x"></i></button>
            </div>
            <div class="modal-body">
                <form id="cavein-addClientForm" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="action" value="caveni_io_add_new_client">
                    <input type="hidden" name="client-id-update" id="client-id-update" value="">
                    <div class="row">
                        <!-- <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('First Name', 'caveni-io'); ?></label><span class="text-danger">*</span> <input class="form-control" placeholder="First Name" name="caveni_client_firstname" required> </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Last Name', 'caveni-io'); ?></label><span class="text-danger">*</span><input class="form-control" placeholder="Last Name" name="caveni_client_lastname" required> </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Username', 'caveni-io'); ?></label><span class="text-danger">*</span> <input class="form-control" placeholder="Username" name="caveni_client_username" required> </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Email Address', 'caveni-io'); ?></label><span class="text-danger">*</span><input class="form-control" placeholder="Email Address" id="caveni_client_email" name="caveni_client_email" required> </div>
                        </div>

                        <div class="col-md-6" id="caveni_client_pass">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Password', 'caveni-io'); ?></label><span class="text-danger">*</span><input type="password" class="form-control" placeholder="Password" name="caveni_client_pass" required> </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Phone', 'caveni-io'); ?></label><span class="text-danger">*</span><input class="form-control" placeholder="Phone" name="caveni_client_phone" required> </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Website', 'caveni-io'); ?></label><span class="text-danger">*</span><input class="form-control" name="caveni_client_web" placeholder="Website" required> </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('Company', 'caveni-io'); ?></label><span class="text-danger">*</span><input class="form-control" placeholder="Company" name="caveni_company_name" required> </div>
                        </div>
                        <!-- GA4 Property ID -->
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('GA4 Property ID', 'caveni-io'); ?></label><input class="form-control" placeholder="Property ID" name="caveni_client_ga4_property_id"> </div>
                        </div>
                        <!-- GSC Property URL -->
                        <div class="col-md-6">
                            <div class="form-group"> <label class="form-label"> <?php echo __('GSC Property URL', 'caveni-io'); ?></label><input class="form-control" placeholder="Property URL" name="caveni_client_gsc_property_url"> </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label"><?php echo __('Roles', 'caveni-io'); ?></label><span class="text-danger">*</span>
                            <div class="form-group">
                                <select id="caveni-multiple-select" name="caveni_user_roles[]" multiple style="width: 100%;">
                                    <?php foreach ($roles as $role_key => $role): ?>
                                        <option value="<?php echo esc_attr($role_key); ?>"><?php echo esc_html($role['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label"> <?php echo __('Upload Avatar', 'caveni-io'); ?></label>
                            <div class="form-group">
                                <input class="form-control" type="file" accept="image/*" name="caveni_client_profile" id="caveni_client_profile">
                                <input type="hidden" id="ci_profile_action" name="ci_profile_action" id="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="imagePreviewContainer" style="margin-top: 10px; position: relative;">
                                <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100%; display: none;">
                                <span id="removeImage" class="caveni-remove-profile" style="position: absolute; top: 10px; right: 10px; color: red; font-size: 24px; cursor: pointer; display: none;">X</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-check caveni_client_notify_hide">
                        <input class="form-check-input" type="checkbox" value="1" id="caveni_client_notify" name="caveni_client_notify">
                        <label class="form-check-label" for="caveni_client_notify">
                            <?php echo __('Notify user of account creation', 'caveni-io'); ?>
                        </label>
                    </div>

                </form>
                <span class="error alert" style="display: none;" id="error-client"></span>
                <div class="modal-footer"> <button class="btn btn-outline-primary btn-close btn-close-client" data-bs-dismiss="modal"><?php echo __('Close', 'caveni-io'); ?></button> <input type="submit" class="addclient_submit btn btn-success successful-notify"></div>
            </div>


        </div>
    </div>
</div>