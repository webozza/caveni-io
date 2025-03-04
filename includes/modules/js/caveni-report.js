
(function ($) {
    "use strict";
  
    // Add Report Button Click - Opens the Modal
    $("#cavein-report-add").on("click", function () {
      $("#caveni-report-modal-title").text("Add New Report"); // Set modal title
      $(".error-message").remove(); // Clear previous errors
      $("#caveni-addReportForm")[0].reset(); // Reset the form
      $("#caveni-newreportmodal").modal("show"); // Show the modal
    });
  
    // Close Modal - Handle Closing Report Modal
    $(document).on("click", ".btn-close-report", function () {
      $("#caveni-newreportmodal").modal("hide");
      $("body").removeClass("modal-open");
      $(".modal-backdrop").remove();
    });


    // Initialize Select2 for Report Client dropdown inside the modal
    $('#caveni-newreportmodal').on('shown.bs.modal', function () {
        $('.report-client-select').select2({
            dropdownParent: $('#caveni-newreportmodal'), // Ensure dropdown stays inside modal
            placeholder: "Select Client",
            allowClear: true,
            width: '100%'
        });
    });

      // Log selected user ID to console when a client is selected
      $('#caveni-report-client').on('change', function () {
        var selectedUserId = $(this).val(); // Get the selected user ID
        console.log(selectedUserId); // Log it to the console
    });

  
  })(jQuery);
  

  jQuery(document).ready(function ($) {
    // Initialize Period Picker when the modal is shown
    $('#caveni-newreportmodal').on('shown.bs.modal', function () {
        if (typeof $.fn.periodpicker !== 'undefined') {
            // Initialize Period Picker for the input field
            $('#caveni-report-period').periodpicker({
                startDate: true,
                endDate: true,
                dateFormat: 'MM/DD/YYYY',
                separator: ' to ',
                cells: [1, 2],
                todayButton: false
            });
        } else {
            console.log('Period Picker is not loaded.');
        }
    });
});




  
