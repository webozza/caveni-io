(function ($) {
  "use strict";

  // Add a global alert modal element (only if it doesn't exist)
  if ($("#crm_global_alertmodal").length === 0) {
      $("body").append('<div id="crm_global_alertmodal" class="crm_global_alertmodal"></div>');
  }

  // Function to show alert modal (Reusable)
  function showAlertModal(message, type = "success") {
      let alertModal = $("#crm_global_alertmodal");
      alertModal.removeClass("crm_success crm_error").addClass("crm_" + type).text(message).fadeIn(300);

      // Auto-hide after 3 seconds
      setTimeout(() => {
          alertModal.fadeOut(300);
      }, 3000);
  }

  // Open the Modal
  $("#crm_report_add").on("click", function () {
      $("#crm_modal_title").text("Add New Report"); // Set modal title
      $(".error-message").remove(); // Clear previous errors
      $("#crm_addform")[0].reset(); // Reset the form
      $("#submissionType").val(""); // Reset hidden submissionType field
      $("#crm_newsmodal").modal("show"); // Show modal
  });

  // Close Modal
  $(document).on("click", ".btn-close-report", function () {
      $("#crm_newsmodal").modal("hide");
      $("body").removeClass("modal-open");
      $(".modal-backdrop").remove();
  });

  // Initialize Select2 and Period Picker When Modal is Opened
  $('#crm_newsmodal').on('shown.bs.modal', function () {
      $('.crm_select').select2({
          dropdownParent: $('#crm_newsmodal'),
          placeholder: "Select Client",
          allowClear: true,
          width: '100%'
      }).val(null).trigger('change'); // Ensure it's cleared when modal opens

      if (typeof $.fn.periodpicker !== 'undefined') {
          $('#crm_date_range').periodpicker({
              end: "#crm_end_date",
              formatDate: "YYYY-MM-DD",
              cells: [1, 2],
              todayButton: false,
              onChangePeriod: function () {
                  let startDate = $("#crm_date_range").periodpicker("valueStart");
                  let endDate = $("#crm_end_date").periodpicker("valueEnd");

                  console.log("Raw Period Picker Values:", startDate, endDate); // Debugging

                  if (startDate) {
                      $("#crm_date_range").val(formatDateString(startDate)); // Store formatted Start Date
                  }
                  if (endDate) {
                      $("#crm_end_date").val(formatDateString(endDate)); // Store formatted End Date
                  }
              }
          });
      } else {
          console.log('Period Picker is not loaded.');
      }
  });

  // Function to Format Date String (Handles Already Formatted Dates)
  function formatDateString(dateStr) {
      // Ensure date is already in YYYY-MM-DD or MM/DD/YYYY format
      if (!dateStr) return ""; // Return empty if no date is provided

      // Check if it's already formatted
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
          return dateStr; // Already in YYYY-MM-DD, return as-is
      }

      // Convert MM/DD/YYYY to YYYY-MM-DD
      let parts = dateStr.split("/");
      if (parts.length === 3) {
          return `${parts[2]}-${parts[0].padStart(2, "0")}-${parts[1].padStart(2, "0")}`;
      }

      return ""; // Return empty if format is unknown
  }

  // Update hidden company name field when a company is selected
  $('#crm_company').on('change', function () {
      var selectedCompanyID = $(this).val(); // Get company ID
      var selectedCompanyName = $(this).find(":selected").data("company") || ""; // Get company name from data attribute
      $('#crm_company_name').val(selectedCompanyName); // Set the hidden input value

      console.log("Selected Company ID:", selectedCompanyID); // Logs ID
      console.log("Selected Company Name:", selectedCompanyName); // Logs Name
  });

  // Handle Form Submission (Redirect to JSON Output)
  $('.submission-btn').on('click', function (event) {
      event.preventDefault(); // Prevent default form submission

      var submissionType = $(this).val(); // Get button value
      $('#submissionType').val(submissionType); // Set hidden field

      // Ensure the latest selected company name is stored
      var selectedCompanyID = $("#crm_company").val();
      var selectedCompanyName = $("#crm_company").find(":selected").data("company") || "";
      $("#crm_company_name").val(selectedCompanyName);

      // Get form data
      var formData = $('#crm_addform').serializeArray();
      var payload = {};
      $.each(formData, function (index, field) {
          payload[field.name] = field.value;
      });

      // Ensure company name and ID are correct in payload
      payload["crm_company_id"] = selectedCompanyID;
      payload["crm_company_name"] = selectedCompanyName;

      // Ensure both dates are captured correctly and formatted properly
      payload["crm_date_range"] = $("#crm_date_range").val(); // Start Date
      payload["crm_end_date"] = $("#crm_end_date").val(); // End Date

      console.log("Final Payload Data:", JSON.stringify(payload, null, 2)); // Debugging

      // Redirect to JSON output URL (for viewing in Network tab)
      var jsonString = encodeURIComponent(JSON.stringify(payload));
      window.location.href = "data:text/json;charset=utf-8," + jsonString;
  });

})(jQuery);
