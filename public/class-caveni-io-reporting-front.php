<?php

if (!class_exists('Caveni_Reporting_Module')) {
    class Caveni_Reporting_Module {
        private static $instance = null;

        public function __construct() {
            add_shortcode('caveni_module_reporting', [$this, 'render_reporting_module']);
            add_action('wp_enqueue_scripts', [$this, 'conditionally_enqueue_scripts']);
        }

        // Singleton pattern to ensure only one instance
        public static function get_instance() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        // Render the reporting module
        public function render_reporting_module() {
            ob_start();

            $reporting_file = CAVENI_IO_PATH . 'includes/modules/reporting.php';

            if (file_exists($reporting_file)) {
                include $reporting_file;
                $this->enqueue_scripts();
            } else {
                echo '<p>Error: Reporting module file not found.</p>';
            }

            return ob_get_clean();
        }

        // Enqueue Scripts and Styles only when shortcode is present
        public function enqueue_scripts() {
            $scripts = [
                'caveni-reports-js' => CAVENI_IO_URL . 'includes/modules/js/caveni-report.js',
                'caveni-periodpicker-js' => CAVENI_IO_URL . 'public/js/jquery.periodpicker.full.min.js',
            ];

            $styles = [
                'caveni-reports-css' => CAVENI_IO_URL . 'includes/modules/css/caveni-report.css',
            ];

            // Enqueue Scripts
            foreach ($scripts as $handle => $src) {
                wp_enqueue_script($handle, $src, ['jquery'], null, true);
            }

            // Enqueue Styles
            foreach ($styles as $handle => $src) {
                wp_enqueue_style($handle, $src);
            }

            // Localize script to pass AJAX URL and nonce
            wp_localize_script('caveni-reports-js', 'caveniReportsData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('caveni_reports_nonce'),
            ]);
        }

        // Prevent scripts from loading unless shortcode is detected
        public function conditionally_enqueue_scripts() {
            global $post;
            if (has_shortcode($post->post_content, 'caveni_module_reporting')) {
                $this->enqueue_scripts();
            }
        }
    }
}

// Initialize the class
Caveni_Reporting_Module::get_instance();
