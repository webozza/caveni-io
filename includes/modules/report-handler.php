<?php

// Caveni Reporting Ajax Handler with mPDF PDF Generation
function caveni_generate_report() {
    check_ajax_referer('caveni_reports_nonce', 'security');

    if (empty($_POST['report_html'])) {
        wp_send_json_error(['message' => 'No report HTML received.']);
        return;
    }

    require_once CAVENI_IO_PATH . 'vendor/autoload.php'; // Ensure mPDF is installed via Composer

    $report_html = stripslashes($_POST['report_html']); // Decode JSON-encoded HTML

    $upload_dir = wp_upload_dir();
    $pdf_dir = trailingslashit($upload_dir['basedir']) . 'caveni-reports/';
    $pdf_url = trailingslashit($upload_dir['baseurl']) . 'caveni-reports/';

    if (!file_exists($pdf_dir)) {
        mkdir($pdf_dir, 0755, true);
    }

    $filename = 'Caveni-Report-' . time() . '.pdf';
    $pdf_path = $pdf_dir . $filename;
    $pdf_download_url = $pdf_url . $filename;

    try {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $pdf_dir
        ]);

        $mpdf->WriteHTML($report_html);
        $mpdf->Output($pdf_path, 'F');

        wp_send_json_success([
            'message'    => 'Report generated successfully.',
            'report_url' => $pdf_download_url
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Failed to generate PDF: ' . $e->getMessage()]);
    }
}
add_action('wp_ajax_caveni_generate_report', 'caveni_generate_report');
add_action('wp_ajax_nopriv_caveni_generate_report', 'caveni_generate_report');
