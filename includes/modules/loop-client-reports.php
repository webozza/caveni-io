<?php
global $wpdb;
$table_name = $wpdb->prefix . 'caveni_client_reports';

// ✅ Pagination Settings
$reports_per_page = 10;
$page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
$offset = ($page - 1) * $reports_per_page;

// ✅ Fetch Reports with Pagination
$reports = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT $reports_per_page OFFSET $offset");

$total_reports = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
$total_pages = ceil($total_reports / $reports_per_page);
?>

<?php if (!empty($reports)) : ?>
    <?php foreach ($reports as $index => $report) : ?>
        <?php
        $report_id = 'CR-' . str_pad($report->id, 4, '0', STR_PAD_LEFT); // Format: CR-0001
        $company_name = get_user_meta($report->client_id, 'company', true) ?: 'Unknown';
        $formatted_date = date("F j, Y", strtotime($report->created_at));
        $pdf_icon = CAVENI_IO_URL . 'public/images/pdf-icon.png';

        // Formatting PDF File Name
        $pdf_filename = basename($report->pdf_reference);
        $pdf_filename = str_replace('.pdf', '', $pdf_filename);
        $clean_filename = str_replace(str_replace(' ', '-', $company_name), '', $pdf_filename);
        $clean_filename = trim($clean_filename, '-');
        $clean_filename = str_replace('-', ' ', $clean_filename);
        $clean_filename = preg_replace('/\s+\d+$/', '', $clean_filename);
        ?>
        <tr>
            <td><?php echo esc_html($report_id); ?></td>
            <td><?php echo esc_html($company_name); ?></td>
            <td><?php echo esc_html($clean_filename); ?></td>
            <td><?php echo esc_html($formatted_date); ?></td>
            <td><img src="<?php echo esc_url($pdf_icon); ?>" alt="PDF" width="30"></td>
            <td>
                <div class="d-flex">
                    <!-- View Report -->
                    <a href="javascript:void(0);" class="action-btns1 caveni-view-report"
                        data-report-url="<?php echo esc_url($report->pdf_reference); ?>"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="View Report">
                        <i class="fe fe-eye text-primary"></i>
                    </a>

                    <!-- Download Report -->
                    <a href="<?php echo esc_url($report->pdf_reference); ?>" download class="action-btns1"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Download Report">
                        <i class="fe fe-download text-success"></i>
                    </a>

                    <!-- Delete Report -->
                    <a href="javascript:void(0);" class="action-btns1 caveni-delete-report"
                        data-report-id="<?php echo esc_attr($report->id); ?>"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Report">
                        <i class="fe fe-trash-2 text-danger"></i>
                    </a>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else : ?>
    <tr>
        <td colspan="6" class="text-center"><?php echo __('No reports found.', 'caveni-io'); ?></td>
    </tr>
<?php endif; ?>


