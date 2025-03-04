(function ($) {
  "use strict";

  // basic datatable
  $("#supportclient-dash").DataTable({
    language: {
      emptyTable: "No clients found",
    },
    bLengthChange: false,
    searching: true, // Disable the default search box
    columnDefs: [{ orderable: false, targets: -1 }], // Disable ordering for the last column
    paging: $("#supportclient-dash tbody tr").length > 10,
    order: [[0, "desc"]], // Sort by the first column (assumed to be the ID column) in descending order
    drawCallback: function (settings) {
      var api = this.api();
      var info = api.page.info();
      if (info.recordsTotal <= 10) {
        $("#supportticket-dash_info").hide();
      } else {
        $("#supportticket-dash_info").show();
      }
    },
  });

  // Link custom search input to DataTable search
  var table = $("#supportclient-dash").DataTable(); // Access the initialized DataTable
  $("#caveni-custom-search").on("keyup", function () {
    table.search(this.value).draw(); // Perform search based on custom input
  });

  setTimeout(() => {
    $("#caveni-multiple-select").select2({
      placeholder: "Select Roles",
      allowClear: true,
      tags: true, // Enable tagging
    });
  }, 600);

  $("#cavein-client-add").on("click", function () {
    // alert("pppppppp");
    // Add the 'show' class to the modal
    // $("#cavein-addClientForm").trigger("reset");
    $("#caveni-modal-title").text("Add New Client");
    $(".error-message").remove();
    $("#caveni_client_email").attr("disabled", false);
    $("input[name='caveni_client_username']").attr("disabled", false);
    $("#caveni-multiple-select").val([]).trigger("change");
    $(".caveni_client_notify_hide").show();
    $("#caveni_client_pass").show();
    $("#imagePreview").hide();
    $("#removeImage").hide();
    $("#cavein-addClientForm")[0].reset();
    $("#caveni-newclientmodal").modal("show");
  });

  // Handle modal close
  $(document).on("click", ".btn-close-client", function () {
    // Remove the 'show' class and reset modal properties

    $("#caveni-newclientmodal").modal("hide");

    $("body").removeClass("modal-open");
    $(".modal-backdrop").remove();
  });

  $(".caveni-edit-client").on("click", function () {
    const clientId = $(this).data("client-id");
    // Clear existing modal data
    // $("#cavein-clientForm")[0].reset();
    // $("#cavein-addClientForm")[0].reset();
    $(".caveni_client_notify_hide").hide();
    $("#cavein-addClientForm").trigger("reset");
    $(".error-message").remove();
    $("#client-id-update").val(clientId);
    $("#modal-action").val("caveni_io_update_client");
    $(".ci-loader-list").show();
    // Fetch client data via AJAX
    $.ajax({
      url: ajaxurl,
      type: "POST",
      data: {
        action: "caveni_fetch_client_data",
        client_id: clientId,
      },
      dataType: "json",
      success: function (response) {
        $(".ci-loader-list").hide();
        if (response.success) {
          const client = response.data;

          console.log(client);

          console.log("show");
          $("#caveni-newclientmodal").modal("show");
          $("#caveni-modal-title").text("Update Client");
          $("#caveni_client_email").attr("disabled", true);
          $("input[name='caveni_client_username']").attr("disabled", true);

          $("#caveni_client_pass").hide();
          // Populate fields
          $('input[name="caveni_client_id"]').val(client.client_id);
          $('input[name="caveni_client_username"]').val(client.username);
          // $('input[name="caveni_client_firstname"]').val(client.firstname);
          // $('input[name="caveni_client_lastname"]').val(client.lastname);
          $('input[name="caveni_client_email"]').val(client.email);
          // $('input[name="caveni_client_phone"]').val(client.phone);
          $('input[name="caveni_client_web"]').val(client.website);
          $('input[name="caveni_company_name"]').val(client.comapny);
          $('input[name="caveni_client_ga4_property_id"]').val(
            client.ga4_property_id
          );
          $('input[name="caveni_client_gsc_property_url"]').val(
            client.gsc_property_url
          );

          if (client.profile_image) {
            $("#removeImage").attr("data-type", "live");
            $("#imagePreview").attr("src", client.profile_image).show();
            $("#removeImage").show();
          }
          const assignedRoles = client.roles; // Assuming `client.roles` is an array of assigned role keys
          $("#caveni-multiple-select").val(assignedRoles).trigger("change");
        } else {
          alert("Unable to fetch client data.");
        }
      },
      error: function () {
        alert("An error occurred while fetching client data.");
      },
    });
  });

  $(".caveni-delete-client").on("click", function () {
    var clientId = $(this).data("client-id");
    if (confirm("Are you sure you want to delete this client?")) {
      $.ajax({
        url: ajaxObj.ajaxurl,
        type: "POST",
        data: {
          action: "caveni_client_delete",
          user_id: clientId,
        },
        success: function (response) {
          if (response.success) {
            alert(response.data.message); // Success message
            // Optionally, remove the user from the DOM
            $(`#client-row-${clientId}`).remove();

            setTimeout(function () {
              location.reload();
            }, 1000);
          } else {
            alert(response.data.message); // Error message
          }
        },
        error: function () {
          alert("An error occurred while deleting the user. Please try again.");
        },
      });
    }
  });

  $(document).on("click", ".addclient_submit", function (e) {
    e.preventDefault(); // Prevent form submission
    $("#caveni_client_email").prop("disabled", false);
    $('input[name="caveni_client_username"]').prop("disabled", false);
    let isValid = true; // Flag to track form validity

    // Helper function to validate email
    function validateEmail(email) {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
    }

    // Helper function to validate URL
    function validateURL(url) {
      const regex = /^(https?:\/\/)?([\w-]+\.)+[\w-]+(\/[\w-]*)*\/?$/;
      return regex.test(url);
    }
    // function validatePhoneNumber(phone) {
    //   const regex = /^\d{10,15}$/; // Allow phone numbers with 10 to 15 digits
    //   return regex.test(phone);
    // }
    // Clear previous error messages
    $(".error-message").remove();

    // Loop through required fields
    $(
      "#cavein-addClientForm input[required]:not([type='password']), #cavein-addClientForm textarea[required]"
    ).each(function () {
      const value = $(this).val().trim();
      const fieldName = $(this).attr("placeholder");
      const $input = $(this); // Store the input element for error placement

      if (!value) {
        $input.after(
          `<small class="error-message text-danger">The field "${fieldName}" is required.</small>`
        );
        isValid = false;
      }

      // Specific validations
      if (
        $input.attr("name") === "caveni_client_email" &&
        value &&
        !validateEmail(value)
      ) {
        $input.after(
          '<small class="error-message text-danger">Please enter a valid email address.</small>'
        );
        isValid = false;
      }

      // if (
      //   $input.attr("name") === "caveni_client_phone" &&
      //   value &&
      //   !validatePhoneNumber(value)
      // ) {
      //   $input.after(
      //     '<small class="error-message text-danger">Please enter a valid phone number (10-15 digits).</small>'
      //   );
      //   isValid = false;
      // }

      if (
        $input.attr("name") === "caveni_client_web" &&
        value &&
        !validateURL(value)
      ) {
        $input.after(
          '<small class="error-message text-danger">Please enter a valid URL.</small>'
        );
        isValid = false;
      }
    });

    // Check the password field only if it is visible
    const passwordField = $("input[name='caveni_client_pass']");
    const rolesuser = $("select[name='caveni_user_roles[]']");
    const selectedRoles = rolesuser.val();
    const passwordValue = passwordField.val().trim();

    if (passwordField.is(":visible")) {
      passwordField.next(".error-message").remove(); // Remove any previous error message

      if (!passwordValue) {
        passwordField.after(
          '<small class="error-message text-danger">The "Password" field is required.</small>'
        );
        isValid = false;
      } else if (passwordValue.length < 6) {
        passwordField.after(
          '<small class="error-message text-danger">The "Password" must be at least 6 characters long.</small>'
        );
        isValid = false;
      }
    }
    if (!selectedRoles || selectedRoles.length === 0) {
      rolesuser.next(".select2-container").after(
        // Append error message after the Select2 container
        '<small class="error-message text-danger">Please choose at least one role.</small>'
      );
      isValid = false;
    }
    rolesuser.on("change", function () {
      const selectedRoles = $(this).val(); // Get the selected roles again
      if (selectedRoles && selectedRoles.length > 0) {
        // Remove the error message if roles are selected
        $(this).next(".select2-container").next(".error-message").remove();
      }
    });
    if (!isValid) {
      const form = $("#cavein-addClientForm");
      const firstError = form.find(".error-message:visible").first();
      if (firstError.length) {
        let $parentDiv = $("#caveni-newclientmodal .modal-content");
        $parentDiv.animate({ scrollTop: firstError.position().top - 50 }, 300);
        // $parentDiv.scrollTop(
        //   $parentDiv.scrollTop() + firstError.position().top
        // );
      }
    }

    // Add 'input' event listener for real-time validation
    $(
      "#cavein-addClientForm input[required], #cavein-addClientForm textarea[required]"
    ).on("input", function () {
      const $input = $(this);
      const value = $input.val().trim();

      // Remove error message when input is fixed
      if (value) {
        $input.next(".error-message").remove();
      }

      // Additional specific validations
      if (
        $input.attr("name") === "caveni_client_email" &&
        validateEmail(value)
      ) {
        $input.next(".error-message").remove();
      }

      if ($input.attr("name") === "caveni_client_web" && validateURL(value)) {
        $input.next(".error-message").remove();
      }
    });

    // If form is valid, proceed with submission
    if (isValid) {
      $(".ci-loader").show();
      $("#error-client").text("").hide().removeClass("alert-danger");
      var formData = new FormData($("#cavein-addClientForm")[0]);
      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          $(".ci-loader").hide();
          if (response.success) {
            // If backend validation was successful
            $("#error-client").show();
            $("#error-client")
              .text(response.data.message)
              .addClass("alert-success");
            setTimeout(function () {
              location.reload();
            }, 1000);
          } else {
            $("#error-client").show();
            $("#error-client")
              .text(response.data.message)
              .addClass("alert-danger");
            handleValidationErrors(response.data);
          }
        },
        error: function () {
          $(".ci-loader").hide();
          $("#error-client").text(
            "There was an error submitting the form. Please try again."
          );
        },
      });
    } else {
      // alert("Form has errors. Please correct them.");
    }
  });

  $("#caveni_client_profile").on("change", function (e) {
    var reader = new FileReader();

    reader.onload = function (event) {
      $("#imagePreview").attr("src", event.target.result);
      $("#imagePreview").show();
      $("#removeImage").show();
      $("#ci_profile_action").val("add");
    };

    // Check if a file was selected
    if (this.files && this.files[0]) {
      reader.readAsDataURL(this.files[0]);
    }
  });

  $("#removeImage").on("click", function () {
    $("#imagePreview").hide();
    $("#removeImage").hide();
    $("#caveni_client_profile").val("");
    if ($(this).data("type") == "live") {
      $("#ci_profile_action").val("remove");
    }
  });

  function handleValidationErrors(errors) {
    for (let field in errors) {
      const errorMessage = errors[field];
      $(`input[name="${field}"], textarea[name="${field}"]`).after(
        `<small class="error-message text-danger">${errorMessage}</small>`
      );
    }
  }
})(jQuery);
