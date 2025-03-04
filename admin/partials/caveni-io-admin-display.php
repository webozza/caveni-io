<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/admin/partials
 */

?>
<div class="wrap">
   <form method="post" action="options.php" id="plugin-hub-setting-form">
      <!-- Page Header -->
      <div class="page-header d-xxl-flex d-block justify-content-xxl-between p-3">
         <div class="page-leftheader">
            <div class="page-title">
               <img src="
                  <?php echo CAVENI_IO_URL . 'public/images/cropped-CAVENI.IO_.png'; ?>" alt="caveni io logo" class="img-thumbnail" /> <?php //esc_html_e( 'Caveni.IO ', 'caveni-io' ); 
                                                                                                                                       ?>
            </div>
         </div>
      </div>
      <?php
      settings_errors();
      settings_fields('caveni_settings');
      do_settings_sections('caveni_settings');
      $settings = get_option('caveni_settings');
      ?>
      <!-- Page Header Close -->
      <div class="row">
         <div class="col-12">
            <div class="alert d-none m-3" id="setting_save_confirmation_alert" role="alert"></div>
         </div>
      </div>
      <!-- Start::row-1 -->
      <div class="row caveni-admin-content-section">
         <div class="col-lg-3 admin-sidebar">
            <div class="card custom-card">
               <div class="nav flex-column admisetting-tabs" id="settings-tab" role="tablist" aria-orientation="vertical">
                  <a class="nav-link plugin-hub-link active" data-bs-toggle="pill" href="#plugin-hub" role="tab">
                     <i class="nav-icon las la-cog"></i>
                     <span> <?php esc_html_e('Plugin Hub', 'caveni-io'); ?> </span>
                  </a>
                  <a class="nav-link plugin-hub-link" data-bs-toggle="pill" href="#helpdesk-settings" role="tab">
                     <i class="nav-icon las la-edit"></i>
                     <span> <?php esc_html_e('HelpDesk Settings', 'caveni-io'); ?> </span>
                  </a>
                  <a class="nav-link plugin-hub-link" data-bs-toggle="pill" href="#messages-settings" role="tab">
                     <i class="nav-icon las la-envelope"></i>
                     <span> <?php esc_html_e('Messages Settings', 'caveni-io'); ?> </span>
                  </a>
                  <a class="nav-link plugin-hub-link" data-bs-toggle="pill" href="#client-settings" role="tab">
                     <i class="nav-icon las la-users"></i>
                     <span> <?php esc_html_e('Cilents Settings', 'caveni-io'); ?> </span>
                  </a>
                  <a class="nav-link plugin-hub-link" data-bs-toggle="pill" href="#ga4-api-settings" role="tab">
                     <i class="nav-icon fe fe-pie-chart"></i>
                     <span> <?php esc_html_e('Analytics Settings', 'caveni-io'); ?> </span>
                  </a>
               </div>
            </div>
         </div>
         <div class="col-lg-9 admin-content">
            <div class="tab-content adminsetting-content" id="setting-tabContent">
               <div class="tab-pane fade show active" id="plugin-hub" role="tabpanel">
                  <div class="card custom-card">
                     <div class="card-body text-end caveni-io-top-save">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                     <div class="card-header  border-0 text-end">
                        <h4 class="card-title">
                           <i class="nav-icon las la-cog"></i> <?php esc_html_e('General Settings', 'caveni-io'); ?>
                        </h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Helpdesk Enable'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <div class="form-check form-check-lg form-switch custom-switch-checkbox">
                                    <label class="label-text">
                                       <input class="form-check-input" name="caveni_settings[enable]" <?php echo isset($settings['enable']) ? 'checked' : ''; ?> type="checkbox" role="switch" id="switch-lg1">
                                       <span class="handle"></span>
                                    </label>
                                    <label class="form-check-label custom-switch-description" for="switch-lg1">Enable/Disable</label>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Messages Enable'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <div class="form-check form-check-lg form-switch custom-switch-checkbox">
                                    <label class="label-text">
                                       <input class="form-check-input" name="caveni_settings[messages_enable]" <?php echo isset($settings['messages_enable']) ? 'checked' : ''; ?> type="checkbox" role="switch" id="switch-lg1">
                                       <span class="handle"></span>
                                    </label>
                                    <label class="form-check-label custom-switch-description" for="switch-lg1">Enable/Disable</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                  </div>
               </div>
               <div class="tab-pane fade" id="helpdesk-settings" role="tabpanel">
                  <div class="card custom-card">
                     <div class="card-body text-end caveni-io-top-save">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                     <div class="card-header  border-0">
                        <h4 class="card-title">
                           <i class="nav-icon las las la-hands-helping"></i> <?php esc_html_e('Helpdesk General Settings', 'caveni-io'); ?>
                        </h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('User Role', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <?php global $wp_roles; ?>
                                 <select class='cavenio-roles form-control' name="caveni_settings[role][]" multiple>
                                    <?php
                                    if (is_array($wp_roles->roles) && ! empty($wp_roles->roles)) {
                                       foreach ($wp_roles->roles  as $key => $name) {
                                          if ('administrator' === $key) {
                                             continue;
                                          }
                                    ?>
                                          <option value="<?php echo esc_attr($key); ?>" <?php echo (isset($settings['role']) && in_array($key, $settings['role'], true)) ? 'selected' : ''; ?>><?php echo esc_html($name['name']); ?> </option>
                                    <?php
                                       }
                                    }
                                    ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Admin Staff', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <?php $admins = get_users(array('role' => 'administrator', 'fields' => array('ID', 'display_name'))); ?>
                                 <select class='form-control' name="caveni_settings[staff]">
                                    <?php
                                    if (is_array($admins) && ! empty($admins)) {
                                       foreach ($admins  as $admin) {
                                    ?>
                                          <option value="<?php echo esc_attr($admin->ID); ?>" <?php echo (isset($settings['staff']) && $admin->ID == $settings['staff']) ? 'selected' : ''; ?>><?php echo esc_html($admin->display_name); ?> </option>
                                    <?php
                                       }
                                    }
                                    ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('File size', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input type="number" class="form-control" name="caveni_settings[file_size]" value="<?php echo isset($settings['file_size']) ? esc_html($settings['file_size']) : ''; ?>">
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('File type', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[file_types]"><?php echo isset($settings['file_types']) ? esc_html($settings['file_types']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Sets the file extensions allowed to upload. Seperate each extension by a comma. For example: .jpg, .png, .pdf', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>
                     <hr />
                     <div class="card-header category-card-header border-0">
                        <h4 class="card-title">
                           <i class="nav-icon las la-list-ul"></i> <?php esc_html_e('Helpdesk Categories', 'caveni-io'); ?>
                        </h4>
                        <div class="category-button-holder">
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-categories"> <?php esc_html_e('ADD NEW CATEGORY', 'caveni-io'); ?> </button>
                        </div>
                     </div>
                     <div class="card-body">
                        <!-- Button trigger modal -->
                        <!-- Button trigger modal -->
                        <div class="category-section">
                           <div class="row">
                              <div class="col-12">
                                 <div class="alert d-none m-3" id="deleted_category_confirmation_alert" role="alert"></div>
                              </div>
                           </div>
                           <div class="category-table-area">
                              <div class="table-responsive">
                                 <table class="table">
                                    <caption> <?php esc_html_e('List of Categories', 'caveni-io'); ?> </caption>
                                    <thead>
                                       <tr>
                                          <th scope="col">#</th>
                                          <th scope="col"> <?php esc_html_e('Name', 'caveni-io'); ?> </th>
                                          <th scope="col"> <?php esc_html_e('Action', 'caveni-io'); ?> </th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php
                                       if ($all_categories) {
                                          foreach ($all_categories as $category) {
                                             //print_r($category);
                                       ?>
                                             <tr>
                                                <th scope="row"> <?php echo $category->term_id ?> </th>
                                                <td> <?php echo $category->name ?> </td>
                                                <td>
                                                   <div class="d-flex">
                                                      <a href="javascript:void(0);" class="action-btns1 edit-ticket-category-admin" data-bs-toggle="tooltip" data-bs-placement="top" data-category-id="<?php echo $category->term_id; ?>" data-category-name="<?php echo $category->name; ?>" title="Edit Category">
                                                         <i class="fe fe-edit text-primary"></i>
                                                      </a> <?php if (is_user_admin_or_in_array(array())) : ?> <a href="javascript:void(0);" class="action-btns1 delete-ticket-category-admin" data-bs-toggle="tooltip" data-bs-placement="top" data-category-id="<?php echo $category->term_id ?>" title="Delete Category">
                                                            <i class="fe fe-trash-2 text-danger"></i>
                                                         </a> <?php endif; ?>
                                                   </div>
                                                </td>
                                             </tr>
                                       <?php
                                          }
                                       }
                                       ?>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                     <hr />
                     <div class="card-header  border-0">
                        <h4 class="card-title">
                           <i class="nav-icon las la-envelope"></i> <?php esc_html_e('Email Settings', 'caveni-io'); ?>
                        </h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Email Enable'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <div class="form-check-lg custom-switch-checkbox">
                                    <label class="label-text">
                                       <input class="form-check-input" name="caveni_settings[email_enable]" <?php echo isset($settings['email_enable']) ? 'checked' : ''; ?> type="checkbox" role="switch" id="switch-lg2">
                                       <span class="handle"></span>
                                    </label>
                                    <label class="form-check-label custom-switch-description" for="switch-lg2">Enable/Disable</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('From Email', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="email" class="form-control" id="caveni_setting_from_emai" name="caveni_settings[from_email]" value="<?php echo (isset($settings['from_email'])) ? esc_html($settings['from_email']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('Set the Email Address which will be used to send emails', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Ticket Creation Email Subject', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[ticket_email_subject]" value="<?php echo (isset($settings['ticket_email_subject'])) ? esc_html($settings['ticket_email_subject']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('Allowed placeholders: {site_title} {user_name}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Ticket Creation Email Content', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[ticket_email_content]"><?php echo (isset($settings['ticket_email_content'])) ? esc_html($settings['ticket_email_content']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Allowed placeholders: {site_title} {user_name} {ticket_message} {title} {category} {ticket_link}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <!-- Client Administrator Reply Settings -->
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Admin Reply Email subject', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[admin_reply_email_subject]" value="<?php echo (isset($settings['admin_reply_email_subject'])) ? esc_html($settings['admin_reply_email_subject']) : ''; ?>">
                                 <p class="description"><?php esc_html_e('Allowed placeholders: {site_title} {user_name}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Admin Reply Email content', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[admin_reply_email_content]"><?php echo (isset($settings['admin_reply_email_content'])) ? esc_html($settings['admin_reply_email_content']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Allowed placeholders: {site_title} {user_name} {reply_message} {ticket_link}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <!-- End Administrator Email Reply Settings -->
                        <!-- Client Email Reply Settings -->
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Client Reply Email subject', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[client_reply_email_subject]" value="<?php echo (isset($settings['client_reply_email_subject'])) ? esc_html($settings['client_reply_email_subject']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('Allowed placeholders: {site_title} {user_name}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Client Reply Email content', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[client_reply_email_content]"><?php echo (isset($settings['client_reply_email_content'])) ? esc_html($settings['client_reply_email_content']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Allowed placeholders: {site_title} {user_name} {reply_message} {ticket_link}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <!-- End Client Email Reply Settings -->
                     </div>


                     <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                  </div>
               </div>
               <!--- End Helpdesk --->

               <!--- End Helpdesk --->
               <div class="tab-pane fade" id="messages-settings" role="tabpanel">
                  <div class="card custom-card">
                     <div class="card-body text-end caveni-io-top-save">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                     <div class="card-header  border-0">
                        <h4 class="card-title">
                           <i class="nav-icon las las la-hands-helping"></i> <?php esc_html_e('Messages General Settings', 'caveni-io'); ?>
                        </h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('User Role', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <?php global $wp_roles; ?>
                                 <select class='cavenio-roles form-control' name="caveni_settings[messages_role][]" multiple>
                                    <?php
                                    if (is_array($wp_roles->roles) && ! empty($wp_roles->roles)) {
                                       foreach ($wp_roles->roles  as $key => $name) {
                                          if ('administrator' === $key) {
                                             continue;
                                          }
                                    ?>
                                          <option value="<?php echo esc_attr($key); ?>" <?php echo (isset($settings['messages_role']) && in_array($key, $settings['messages_role'], true)) ? 'selected' : ''; ?>><?php echo esc_html($name['name']); ?> </option>
                                    <?php
                                       }
                                    }
                                    ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Admin Staff', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <?php $admins = get_users(array('role' => 'administrator', 'fields' => array('ID', 'display_name'))); ?>
                                 <select class='form-control' name="caveni_settings[messages_staff]">
                                    <?php
                                    if (is_array($admins) && ! empty($admins)) {
                                       foreach ($admins  as $admin) {
                                    ?>
                                          <option value="<?php echo esc_attr($admin->ID); ?>" <?php echo (isset($settings['messages_staff']) && $admin->ID == $settings['messages_staff']) ? 'selected' : ''; ?>> <?php echo esc_html($admin->display_name); ?> </option>
                                    <?php
                                       }
                                    }
                                    ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('File size', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input type="number" class="form-control" name="caveni_settings[messages_file_size]" value="<?php echo isset($settings['messages_file_size']) ? esc_html($settings['messages_file_size']) : ''; ?>">
                                 <p class="description mt-2">Enter file size in mega bytes (MB).</p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('File type', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[messages_file_types]"><?php echo isset($settings['messages_file_types']) ? esc_html($settings['messages_file_types']) : ''; ?></textarea>
                                 <p class="description  mt-2"> <?php esc_html_e('Sets the file extensions allowed to upload. Seperate each extension by a comma. For example: .jpg, .png, .pdf', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                  </div>
               </div>


               <!-- starts clients -->
               <div class="tab-pane fade" id="client-settings" role="tabpanel">
                  <div class="card custom-card">
                     <div class="card-body text-end caveni-io-top-save">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>


                     <div class="card-header  border-0">
                        <h4 class="card-title">
                           <i class="nav-icon las la-envelope"></i> <?php esc_html_e('Email Settings', 'caveni-io'); ?>
                        </h4>
                     </div>
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Email Enable'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <div class="form-check-lg custom-switch-checkbox">
                                    <label class="label-text">
                                       <input class="form-check-input" name="caveni_settings[client_email_enable]" <?php echo isset($settings['client_email_enable']) ? 'checked' : ''; ?> type="checkbox" role="switch" id="switch-lg2">
                                       <span class="handle"></span>
                                    </label>
                                    <label class="form-check-label custom-switch-description" for="switch-lg2"><?php esc_html_e('Enable/Disable', 'caveni-io'); ?></label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('From Email', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="email" class="form-control" id="caveni_setting_from_emai" name="caveni_settings[client_from_email]" value="<?php echo (isset($settings['client_from_email'])) ? esc_html($settings['client_from_email']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('Set the Email Address which will be used to send emails', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Client Creation Email Subject', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[client_email_subject]" value="<?php echo (isset($settings['client_email_subject'])) ? esc_html($settings['client_email_subject']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('Allowed placeholders:{site_title} {company_name}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Client Creation Email Content', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[client_email_content]"><?php echo (isset($settings['client_email_content'])) ? esc_html($settings['client_email_content']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Allowed placeholders:{username} {password} {company}', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>

                     </div>


                     <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                  </div>
               </div>
               <!-- end-clients -->

               <!-- GA4 API SETTINGS -->
               <div class="tab-pane fade" id="ga4-api-settings" role="tabpanel">
                  <div class="card custom-card">
                     <div class="card-body text-end caveni-io-top-save">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>

                     <div class="card-header  border-0">
                        <h4 class="card-title">
                           <i class="nav-icon fe fe-cpu"></i> <?php esc_html_e('API Credentials', 'caveni-io'); ?>
                        </h4>
                     </div>

                     <!-- GA4 API Credentials -->
                     <div class="card-body">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('GA4 API Credentials', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[ga4_credentials]"><?php echo (isset($settings['ga4_credentials'])) ? esc_html($settings['ga4_credentials']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Input JSON generated from your Google Cloud Service Account', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Google Ads API Credentials -->
                     <div class="card-body" style="display: none;">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Google Ads API Credentials', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <textarea cols="40" rows="5" class="form-control" name="caveni_settings[adwords_credentials]"><?php echo (isset($settings['adwords_credentials'])) ? esc_html($settings['adwords_credentials']) : ''; ?></textarea>
                                 <p class="description"> <?php esc_html_e('Input JSON generated from your Google Cloud OAuth client', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Google Ads Refresh Token -->
                     <div class="card-body" style="display: none;">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Google Ads Refresh Token', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[adwords_refresh_token]" value="<?php echo (isset($settings['adwords_refresh_token'])) ? esc_html($settings['adwords_refresh_token']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('This is automatically generated and refreshed upon each request', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- Google Ads Developer Token -->
                     <div class="card-body" style="display: none;">
                        <div class="form-group mb-3">
                           <div class="row">
                              <div class="col-md-3">
                                 <label class="form-label mb-0 mt-2"> <?php esc_html_e('Google Ads Developer Token', 'caveni-io'); ?> </label>
                              </div>
                              <div class="col-md-9">
                                 <input class="caveni-input" type="text" class="form-control" name="caveni_settings[adwords_developer_token]" value="<?php echo (isset($settings['adwords_developer_token'])) ? esc_html($settings['adwords_developer_token']) : ''; ?>">
                                 <p class="description"> <?php esc_html_e('You will need to obtain this from your Google Ads Manager Account API Center', 'caveni-io'); ?> </p>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="card-footer text-end">
                        <button type="button" class="btn btn-primary submit-setting-changes update_plugin_hub_settings_btn">
                           <span class="spinner-display d-none">
                              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CHANGES', 'caveni-io'); ?> </span>
                           <span class="button-text"> <?php echo __('SAVE CHANGES', 'caveni-io'); ?> </span>
                        </button>
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>
      <!--End::row-1 -->
   </form>
</div>
</div>
<!-- Modal -->
<div class="modal fade add-categories-modal" id="add-categories" tabindex="-1" aria-labelledby="add-categories-label" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="add-categories-label"> <?php esc_html_e('Add New Category', 'caveni-io'); ?> </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form class="row g-3" name="add-new-category-form" id="add-new-category-modal-form">
               <div class="mb-3">
                  <label for="category-name-input" class="form-label"> <?php esc_html_e('Category Name', 'caveni-io'); ?> </label>
                  <input type="text" name="category_name_field" class="form-control" id="category-name-input" placeholder="<?php esc_html_e('Category Name', 'caveni-io'); ?>" required>
               </div>
               <?php wp_nonce_field('add_category_nonce_action', 'add_category_nonce'); ?> <input type="hidden" name="action" value="add_category_backend_action" />
            </form>
         </div>
         <div class="modal-footer">
            <div class="row flex-grow-1">
               <div class="col-12">
                  <div class="alert d-none " id="category_save_confirmation_alert" role="alert"></div>
               </div>
            </div>
            <button type="button" class="btn btn-primary" id="add-new-categorry-button">
               <span class="spinner-display d-none">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SUBMITTING CATEGORY', 'caveni-io'); ?> </span>
               <span class="button-text"> <?php echo __('SUBMIT', 'caveni-io'); ?> </span>
            </button>
         </div>
      </div>
   </div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-categories" tabindex="-1" aria-labelledby="edit-categories-label" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5" id="edit-categories-label"> <?php esc_html_e('Edit Category', 'caveni-io'); ?> </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form class="row g-3" name="edit-new-category-form" id="edit-new-category-modal-form">
               <div class="mb-3">
                  <label for="category-name-input" class="form-label"> <?php esc_html_e('Category Name', 'caveni-io'); ?> </label>
                  <input type="text" name="edit_category_name_field" class="form-control" id="edit-category-name-input" placeholder="<?php esc_html_e('Category Name', 'caveni-io'); ?>" required>
               </div>
               <?php wp_nonce_field('edit_category_nonce_action', 'edit_category_nonce'); ?> <input type="hidden" name="action" value="edit_category_backend_action" />
               <input type="hidden" name="category_id" id="category_id_hidden_field" value=0 />
            </form>
         </div>
         <div class="modal-footer">
            <div class="row flex-grow-1">
               <div class="col-12">
                  <div class="alert d-none " id="edit_category_save_confirmation_alert" role="alert"></div>
               </div>
            </div>
            <button type="button" class="btn btn-primary" id="edit-new-categorry-button">
               <span class="spinner-display d-none">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo __('SAVING CATEGORY', 'caveni-io'); ?> </span>
               <span class="button-text"> <?php echo __('SAVE CATEGORY', 'caveni-io'); ?> </span>
            </button>
         </div>
      </div>
   </div>
</div>