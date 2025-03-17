<!-- PDF Viewer Modal -->
<div class="modal fade" id="crm_pdf_viewer_modal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="caveni--overlay-loader" style="display:none">
            <img src="<?= CAVENI_IO_URL . 'public/images/loader.svg' ?>">
            <h5><?php echo __('Loading Report...', 'caveni-io'); ?></h5>
        </div>
        <div class="modal-content">
            <div class="modal-header">
                <i class="fe fe-file-text"></i>
                <h5 class="modal-title" id="crm_pdf_modal_title"><?php echo __('View Report', 'caveni-io'); ?></h5>
                <button type="button" class="btn-close btn-close-report" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fe fe-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewerFrame" src="" width="100%" height="600px" style="border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
