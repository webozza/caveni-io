<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://xfinitysoft.com
 * @since      1.0.0
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Caveni_Io
 * @subpackage Caveni_Io/public
 * @author     Xfinity Soft <email@example.com>
 */
class Caveni_Io_Clients_Front
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $caveni_io    The ID of this plugin.
     */
    private $caveni_io;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $caveni_io       The name of the plugin.
     * @param      string $version    The version of this plugin.
     */
    public function __construct($caveni_io, $version)
    {
        $this->caveni_io = $caveni_io;
        $this->version   = $version;
    }



    /**
     * Perform  the of shortcode helpdesk.
     *
     * @since    1.0.0
     * @param    array $attributes Paramter of shortcode array.
     */

    public function perform_shortcode_action($attributes, $req_data)
    {

        return $this->display_clients_list();
    }

    public function display_clients_list()
    {
        ob_start();
        global $wp;
        $args = array(
            'role'    => 'contributor',
            'orderby' => 'ID',
            'order'   => 'DESC',
        );

        // Use get_users() to fetch all users matching the criteria
        $users = get_users($args);

        $clients = array();
        foreach ($users as $user) {
            $roles = [];
            foreach ($user->roles as $role) {
                $roles[] = get_all_role_titles($role);
            }
            $clients[] = array(
                'ID'           => $user->ID,
                'display_name' => $user->display_name,
                'email'        => $user->user_email,
                'roles'        => implode(', ', $roles)
            );
        }
        // include template with the arguments.
        include_once CAVENI_IO_PATH . 'public/partials/clients/list-clients.php';

        return ob_get_clean();
    }

    public function caveni_io_add_new_client()
    {
        ini_set("display_errors", true);
        // pre($_POST, 1);
        $data = $_POST;
        $validation_errors = validate_form_client_data($data);

        $cilent_settings = get_option('caveni_settings');

        $enable = (isset($settings['client_email_enable']) && $settings['client_email_enable']) ? 1 : 0;
        $from_email  = isset($cilent_settings['client_from_email']) ? $cilent_settings['client_from_email'] : '';
        $email_subject = isset($cilent_settings['client_email_subject']) ? $cilent_settings['client_email_subject'] : '';
        $email_content = isset($cilent_settings['client_email_content']) ? $cilent_settings['client_email_content'] : '';
        $ga4_credentials = isset($cilent_settings['ga4_credentials']) ? $cilent_settings['ga4_credentials'] : '';

        // pre($from_email, 1);

        if (!empty($validation_errors)) {
            // Return validation errors as JSON response
            wp_send_json_error($validation_errors);
        }

        // Get the posted data
        $username = isset($_POST['caveni_client_username']) ? sanitize_text_field($_POST['caveni_client_username']) : '';
        // $firstname = isset($_POST['caveni_client_firstname']) ? sanitize_text_field($_POST['caveni_client_firstname']) : '';
        // $lastname = isset($_POST['caveni_client_lastname']) ? sanitize_text_field($_POST['caveni_client_lastname']) : '';
        $company_name = isset($_POST['caveni_company_name']) ? sanitize_text_field($_POST['caveni_company_name']) : '';

        $ga4_property_id = isset($_POST['caveni_client_ga4_property_id']) ? sanitize_text_field($_POST['caveni_client_ga4_property_id']) : '';

        $gsc_property_url = isset($_POST['caveni_client_gsc_property_url']) ? sanitize_text_field($_POST['caveni_client_gsc_property_url']) : '';

        $email_notify = isset($_POST['caveni_client_notify']) ? sanitize_text_field($_POST['caveni_client_notify']) : '';

        $user_cilent_roles = isset($_POST['caveni_user_roles']) ? $_POST['caveni_user_roles'] : [];
        $client_update_id = isset($_POST['client-id-update']) ? $_POST['client-id-update'] : '';

        $client_email = isset($_POST['caveni_client_email']) ? sanitize_email($_POST['caveni_client_email']) : '';
        $client_pass = isset($_POST['caveni_client_pass']) ? sanitize_text_field($_POST['caveni_client_pass']) : '';
        // $client_phone = isset($_POST['caveni_client_phone']) ? sanitize_text_field($_POST['caveni_client_phone']) : '';
        $client_web = isset($_POST['caveni_client_web']) ? esc_url_raw($_POST['caveni_client_web']) : '';
        $ci_profile_action = isset($_POST['ci_profile_action']) ? sanitize_text_field($_POST['ci_profile_action']) : '';

        if (isset($_FILES['caveni_client_profile']['name']) && $_FILES['caveni_client_profile']['name']) {
            $file_types = array('jpg', 'jpeg', 'png', 'gif');
            $file_info = pathinfo($_FILES['caveni_client_profile']['name']);
            $extension = strtolower($file_info['extension']);
            if (!in_array($extension, $file_types)) {
                wp_send_json_error(array('message' => 'Upload Failed! Error was: Please upload only these file types: JPEG, GIF, PNG.'));
            }
            if ($_FILES['caveni_client_profile']['error'] > 0) {
                wp_send_json_error(array('message' => 'Upload Failed! Error was: Uploaded file is invalid.'));
            }
        }
        $headers[] = 'From: Caveni-io<' . $from_email . '>';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        $subject_shortcode = ['{site_title}', '{company}'];
        $subject_values = [get_bloginfo('name'), $company_name];
        $emailSubject = str_replace($subject_shortcode, $subject_values, $email_subject);

        $shortcodes = ['{username}', '{password}', '{company}'];
        $values = [$username, $client_pass,  $company_name];
        $emailContent = str_replace($shortcodes, $values, $email_content);

        // If the client_add value is set, it means we need to insert a new user
        if (empty($client_update_id)) {
            if (username_exists($username)) {
                wp_send_json_error(array('message' => __('Username already exist!, please try another username', 'caveni-io')));
            } else if (email_exists($client_email)) {
                wp_send_json_error(array('message' => __('Email already exist!, please try another email', 'caveni-io')));
            }
            // Insert new user data
            $user_data = array(
                'user_login' => $username,
                'user_email' => $client_email,
                'user_pass'  => $client_pass,
                // 'first_name' => $firstname,
                // 'last_name'  => $lastname,
                'user_url'   => $client_web,
                'role'       => '',
            );

            $user_id = wp_insert_user($user_data); // Create new user

            // Check for errors during user creation
            if (is_wp_error($user_id)) {
                wp_send_json_error(array('message' => 'Error creating user: ' . $user_id->get_error_message()));
            } else {
                // Assign additional roles
                if ($email_notify) {
                    wp_mail($client_email, $emailSubject, $emailContent, $headers);
                }

                $user_object = new WP_User($user_id);
                foreach ($user_cilent_roles as $role) {
                    $user_object->add_role($role);
                }
            }
        } else {
            $user_id = $client_update_id;
            $user_data = array(
                'ID'          => $client_update_id,
                // 'first_name' => $firstname,
                // 'last_name'  => $lastname,
                'user_email'  => $client_email,
                // 'user_pass'   => $client_pass,
                'user_url'    => $client_web,
                'role'        => '',
            );

            $user_id = wp_update_user($user_data);

            if (is_wp_error($user_id)) {
                wp_send_json_error(array('message' => 'Error updating user: ' . $user_id->get_error_message()));
            } else {
                // Assign additional roles
                $user_object = new WP_User($user_id);
                foreach ($user_cilent_roles as $role) {
                    $user_object->add_role($role);
                }
            }
            if ($ci_profile_action == 'remove') {
                bp_core_delete_existing_avatar(array('item_id' => $user_id, 'object' => 'user'));
                delete_user_meta($user_id, 'simple_local_avatar');
            }
        }

        if ($user_id) {

            update_user_meta($user_id, 'company', $company_name);
            update_user_meta($user_id, 'ga4_property_id', $ga4_property_id);
            update_user_meta($user_id, 'gsc_property_url', $gsc_property_url);
            
            // update_user_meta($user_id, '_client_phone', $client_phone);

            if (!empty($_FILES['caveni_client_profile']['name'])) {
                $object = 'user';
                $avatar_dir = 'avatars';
                $avatar_folder_dir = apply_filters('bp_core_avatar_folder_dir', bp_core_avatar_upload_path() . '/' . $avatar_dir . '/' . $user_id, $user_id, $object, $avatar_dir);

                $file = $_FILES['caveni_client_profile'];
                if (!file_exists($avatar_folder_dir)) {
                    wp_mkdir_p($avatar_folder_dir);
                }
                $file['name'] = sanitize_file_name($file['name']);
                $avatar_folder_dir = $avatar_folder_dir . '/' . $file['name'];

                // Handle the upload
                $movefile = move_uploaded_file($file['tmp_name'], $avatar_folder_dir);
                if ($movefile) {
                    $r = array(
                        'item_id'       => $user_id,
                        'object'        => $object,
                        'type'    => 'full',
                        'avatar_dir'    => $avatar_dir,
                        'is_crop'       => false,
                        'original_file' => $avatar_folder_dir
                    );
                    bp_core_avatar_handle_crop($r);
                    $avatar_data = [
                        'full' => get_buddypress_profile_picture($user_id)
                    ];
                    update_user_meta($user_id, 'simple_local_avatar', $avatar_data);
                }
            }

            wp_send_json_success(array('message' => 'User updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'User ID is missing or invalid.'));
        }

        wp_die();
    }


    public function caveni_fetch_client_data()
    {
        $client_id = isset($_POST['client_id']) ? intval($_POST['client_id']) : 0;

        if ($client_id) {
            $user_data = get_userdata($client_id);
            $avatar_url = '';
            if (bp_get_user_has_avatar($client_id)) {

                $avatar_url = bp_core_fetch_avatar(array(
                    'item_id' => $client_id,
                    'type'    => 'full',
                    //'width'   => '150',
                    //'height'  => '150',
                    'html'    => false
                ));
            }

            $client_data = [
                'username' => $user_data->user_login,
                'name' => $user_data->display_name,
                // 'firstname' => $user_data->first_name,
                // 'lastname' => $user_data->last_name,
                'email' => $user_data->user_email,
                'roles' => $user_data->roles,
                'client_id' => get_user_meta($client_id, 'client_id', true),
                // 'phone' => get_user_meta($client_id, '_client_phone', true),
                'website' => $user_data->user_url,
                'comapny' => get_user_meta($client_id, 'company', true),
                // 'address' => get_user_meta($client_id, '_client_address', true),
                // 'zip' =>  get_user_meta($client_id, '_client_address', true),
                // 'city' =>  get_user_meta($client_id, '_client_city', true),
                // 'state' =>  get_user_meta($client_id, '_client_state', true),
                // 'country' => get_user_meta($client_id, '_client_country', true),
                'ga4_property_id' => get_user_meta($client_id, 'ga4_property_id', true),
                'gsc_property_url' => get_user_meta($client_id, 'gsc_property_url', true),
                'profile_image' => $avatar_url,
            ];
            wp_send_json_success($client_data);
        } else {
            wp_send_json_error('Invalid client ID.');
        }
    }

    public function caveni_client_delete_callback()
    {

        // Check if user ID is provided
        if (!isset($_POST['user_id'])) {
            wp_send_json_error(['message' => 'Permission denied or user ID missing.']);
            return;
        }

        $user_id = intval($_POST['user_id']);

        // Attempt to delete the user
        if (wp_delete_user($user_id)) {
            wp_send_json_success(['message' => 'User deleted successfully.']);
        } else {
            wp_send_json_error(['message' => 'Failed to delete user.']);
        }
    }
}
add_filter('bp_core_pre_avatar_handle_crop', function ($crop, $r) {
    if (isset($r['is_crop'])) {
        //return false;
    }
    return $crop;
}, 10, 2);
