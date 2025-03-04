(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  var searchTimeout;
  var sendMessage = null;

  let caveniPath = window.location.pathname;

  setTimeout(() => {
    $(".select2.select2-container .select2-selection").attr(
      "style",
      "border-radius:8px !important"
    );
  }, 600);

  $(function () {
    const socket = new WebSocket("wss://caveni.io:6464/messages");

    socket.onopen = function () {
      console.log("Connected to WebSocket server");
      if (
        $(".chat-list").length > 0 &&
        jQuery.inArray(ajaxObj.caveniUser, ajaxObj.caveniReceviers) !== -1
      ) {
        let notify_unred = {
          user: $('input[name="receiver"]').val(),
          type: "notification",
        };
        socket.send(JSON.stringify(notify_unred));
      }
    };

    socket.onmessage = function (event) {
      if (event.data instanceof Blob) {
        const reader = new FileReader();
        reader.onload = function () {
          console.log(reader.result);
          var data = JSON.parse(reader.result);
          loadMessage(data);
        };
        reader.readAsText(event.data); // Convert Blob to text
      } else {
        console.log(event.data);
        var data = JSON.parse(reader.result);
        loadMessage(data);
      }
    };

    socket.onclose = function () {
      console.log("WebSocket connection closed");
    };
    socket.onerror = (error) => {
      console.log(`WebSocket error: ${error.message}`);
    };

    $.validator.setDefaults({ ignore: ":hidden:not(select)" });
    $.validator.addMethod("filesize", function (value, element, param) {
      // param = size (en bytes)
      // element = element to validate (<input>)
      // value = value of the element (file name)
      return this.optional(element) || element.files[0].size <= param;
    });
    $.validator.addMethod("accept", function (value, element, param) {
      // param = size (en bytes)
      // element = element to validate (<input>)
      // value = value of the element (file name)
      //var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
      var allowed_files = param.split(",");
      //console.log(element.files[0].name.split('.').pop().toLowerCase(),allowed_files, $.inArray(element.files[0].name.split('.').pop().toLowerCase(), allowed_files) != -1);
      return (
        this.optional(element) ||
        $.inArray(
          "." + element.files[0].name.split(".").pop().toLowerCase(),
          allowed_files
        ) != -1
      );
    });
    // Initialize form validation on the registration form.
    // It has the name attribute "registration"
    $("#caveni-create-ticket-form").validate({
      // Specify validation rules
      rules: {
        // The key name on the left side is the name attribute
        // of an input field. Validation rules are defined
        // on the right side
        ticket_subject: "required",
        ticket_category: "required",
        ticket_file: {
          required: false,
          accept: ajaxObj.allowed_file_types,
          filesize: ajaxObj.allowed_file_size,
        },
      },
      // Specify validation error messages
      messages: {
        ticket_subject: "Please enter Ticket Subject",
        ticket_category: "Please Select Ticket Category",
        ticket_file: ajaxObj.allowed_file_types_msg,
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else if (element.prop("type") === "select-one") {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element)
          .parents(".col-md-9")
          .addClass("has-error")
          .removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element)
          .parents(".col-md-9")
          .addClass("has-success")
          .removeClass("has-error");
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function (form) {
        form.submit();
      },
    });

    $("#add-ticket-reply-form").validate({
      // Specify validation rules
      rules: {
        ticket_comment_file: {
          required: false,
          accept: ajaxObj.allowed_file_types,
          filesize: ajaxObj.allowed_file_size,
        },
      },
      // Specify validation error messages
      messages: {
        ticket_comment_file: ajaxObj.allowed_file_types_msg,
      },
      errorElement: "em",
      errorPlacement: function (error, element) {
        // Add the `help-block` class to the error element
        error.addClass("help-block");

        if (element.prop("type") === "checkbox") {
          error.insertAfter(element.parent("label"));
        } else if (element.prop("type") === "select-one") {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      highlight: function (element, errorClass, validClass) {
        $(element)
          .parents(".col-md-9")
          .addClass("has-error")
          .removeClass("has-success");
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element)
          .parents(".col-md-9")
          .addClass("has-success")
          .removeClass("has-error");
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function (form) {
        form.submit();
      },
    });

    /* Choices JS */
    $(window).load(function () {
      if ($("select.ticket_category_select").length > 0) {
        var ticket_category = new Choices(
          $("select.ticket_category_select")[0],
          {
            allowHTML: true,
            searchEnabled: false,
            placeholder: true,
          }
        );
      }
    });
    /* Choices JS */

    /* Quill Editor Setup */
    var toolbarOptions = [
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ font: [] }],
      ["bold", "italic", "underline", "strike"], // toggled buttons
      ["blockquote", "code-block"],

      [{ header: 1 }, { header: 2 }], // custom button values
      [{ list: "ordered" }, { list: "bullet" }],

      [{ color: [] }, { background: [] }], // dropdown with defaults from theme
      [{ align: [] }],
      ["clean"], // remove formatting button
    ];
    var quill = new Quill("#reply-text-area", {
      modules: {
        toolbar: toolbarOptions,
      },
      theme: "snow",
    });
    /* Quill Editor Setup */

    // basic datatable
    $("#supportticket-dash").DataTable({
      language: {
        emptyTable: "No tickets found",
      },
      bLengthChange: false,
      searching: false,
      columnDefs: [{ orderable: false, targets: -1 }],
      paging: $("#supportticket-dash tbody tr").length > 10,
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

    // Enable Disable Submit buttons
    function submit_button_enable(button, enable = false) {
      if (enable) {
        button.attr("disabled", false);
        button.find("span.button-text").removeClass("d-none");
        button.find("span.spinner-display").addClass("d-none");
      } else {
        button.attr("disabled", true);
        button.find("span.button-text").addClass("d-none");
        button.find("span.spinner-display").removeClass("d-none");
      }
    }

    // Enable Disable Submit buttons
    function ticket_creation_confirmation(
      msg = "",
      error = false,
      reload = true,
      reloadUrl = "",
      alertId = "#ticket_confirmation_alert"
    ) {
      console.log(alertId, "work", $(alertId));
      if (error) {
        $(alertId).html(msg);
        $(alertId).addClass("alert-danger");
        $(alertId).removeClass("d-none");
      } else {
        $(alertId).html(msg);
        $(alertId).addClass("alert-success");
        $(alertId).removeClass("d-none");
      }

      if (reload) {
        setTimeout(() => {
          if (reloadUrl !== "") {
            window.location.href = reloadUrl;
          } else {
            window.location.reload();
          }
        }, 5000);
      }
    }

    function handleSubmitResponse(responseA, alertId = "") {
      let jsonReponse = JSON.parse(responseA);
      switch (jsonReponse.status) {
        case "success":
          if (alertId != "") {
            ticket_creation_confirmation(
              jsonReponse.message,
              false,
              true,
              jsonReponse.redirectUrl,
              alertId
            );
          } else {
            ticket_creation_confirmation(
              jsonReponse.message,
              false,
              true,
              jsonReponse.redirectUrl
            );
          }
          break;

        case "error":
          if (alertId != "") {
            ticket_creation_confirmation(
              jsonReponse.message,
              true,
              true,
              jsonReponse.redirectUrl,
              alertId
            );
          } else {
            ticket_creation_confirmation(
              jsonReponse.message,
              true,
              true,
              jsonReponse.redirectUrl
            );
          }
          break;

        default:
          if (alertId != "") {
            ticket_creation_confirmation(
              jsonReponse.message,
              true,
              true,
              jsonReponse.redirectUrl,
              alertId
            );
          } else {
            ticket_creation_confirmation(
              jsonReponse.message,
              true,
              true,
              jsonReponse.redirectUrl
            );
          }
          break;
      }
    }

    // Action perform when click on create ticket button
    $("#caveni-ticket-create").on("click", function (event) {
      event.preventDefault();
      let form = $("#caveni-create-ticket-form");
      if (!form.valid()) return false;
      if (quill.getContents().ops[0].insert == "\n") {
        alert("Please add a description of your ticket before submitting.");
        return false;
      }
      let button = $(this);
      let formData = new FormData(form[0]);
      formData.append("ticket_description", quill.getContents().ops[0].insert);
      submit_button_enable(button, false);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          handleSubmitResponse(response);
          submit_button_enable(button, true);
        },
      });
    });

    // Action perform when click on reply ticket button
    $("#add-ticket-reply").on("click", function (event) {
      event.preventDefault();
      let form = $("#add-ticket-reply-form");
      if (!form.valid()) return false;
      if (quill.getContents().ops[0].insert == "\n") {
        alert("Please add a description of your ticket before submitting.");
        return false;
      }
      let button = $(this);
      let formData = new FormData(form[0]);
      formData.append(
        "ticket_reply_description",
        quill.getContents().ops[0].insert
      );
      submit_button_enable(button, false);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          console.log(response);
          handleSubmitResponse(response);
          submit_button_enable(button, true);
        },
      });
    });

    // Action perform when click on close ticket button
    $("#close-ticket-button").on("click", function (event) {
      event.preventDefault();
      if (!confirm("Do you really want to close this ticket?") == true)
        return false;
      let form = $("#close-ticket-form");
      let button = $(this);
      let formData = new FormData(form[0]);
      submit_button_enable(button, false);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          handleSubmitResponse(response, "#close_ticket_confirmation_alert");
          submit_button_enable(button, true);
        },
      });
    });

    // Action perform when click on close ticket button
    $("#delete-ticket-button").on("click", function (event) {
      event.preventDefault();
      if (!confirm("Do you really want to delete this ticket?") == true)
        return false;
      let form = $("#delete-ticket-form");
      let button = $(this);
      let formData = new FormData(form[0]);
      submit_button_enable(button, false);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          handleSubmitResponse(response, "#close_ticket_confirmation_alert");
          submit_button_enable(button, true);
        },
      });
    });

    $(".delete-ticket-by-admin").on("click", function (event) {
      event.preventDefault();
      if (!confirm("Do you really want to delete this ticket?") == true)
        return false;
      let ticket_id = parseInt($(this).attr("data-ticket-id"));

      if (ticket_id <= 0) {
        alert("Ticket id is not correct");
        return false;
      }

      let formData = new FormData();
      formData.append("action", "caveni_io_delete_ticket_action");
      formData.append("delete_ticket_id", ticket_id);
      formData.append("delete_ticket_nonce", ajaxObj.security);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          handleSubmitResponse(response, "#deleted_ticket_confirmation_alert");
        },
      });
    });

    $(".edit-comment-button").on("click", function (event) {
      event.preventDefault();
      let comment_id = $(this).attr("data-comment-id");
      let reply_form = $("#add-ticket-reply-form");
      let comment_text_obj = $(this)
        .closest(".card-body")
        .find("p.supportnote-body");
      let comment_text = "";
      if (comment_text_obj.length > 0) {
        comment_text = $.trim(comment_text_obj.text());
      }
      quill.setContents([
        {
          insert: comment_text,
        },
      ]);
      reply_form.find("input[name=ticket_comment_id]").val(comment_id);
      reply_form
        .find("input[name=action]")
        .val("caveni_io_update_reply_ticket_action");
      reply_form
        .find("button#add-ticket-reply")
        .find("span.button-text")
        .text("UPDATE REPLY");
      $.scrollTo(document.getElementById("add-ticket-reply-form"), 800);
    });

    $(".delete-comment-button").on("click", function (event) {
      event.preventDefault();
      if (!confirm("Do you really want to delete this comment?") == true)
        return false;
      let comment_id = $(this).attr("data-comment-id");

      if (comment_id <= 0) {
        alert("Comment id is not correct");
        return false;
      }

      let formData = new FormData();
      formData.append("action", "caveni_io_delete_ticket_comment_action");
      formData.append("delete_comment_id", comment_id);
      formData.append("delete_ticket_comment_nonce", ajaxObj.security_comment);

      $.ajax({
        type: "POST",
        contentType: false,
        processData: false,
        url: ajaxObj.ajaxurl,
        data: formData,
        success: function (response) {
          handleSubmitResponse(
            response,
            "#deleted_ticket_comment_confirmation_alert"
          );
        },
      });
    });

    $(document).on("click", ".ci-load-user", function (event) {
      get_messages($(this), 0);
    });

    $(document).on("click", ".ci-load-message", function (event) {
      let user = $(this).data("user");
      let message_id = $(this).data("id");
      let chat_element = $(".chat-users-list").find(
        ".ci-load-user[data-user='" + user + "']"
      );
      get_messages(chat_element, message_id);
      $(".ci-search-result").hide();
    });
    $(document).on("click", ".ci-scroll-message", function (event) {
      let message_id = $(this).data("id");
      let $parentDiv = $(".chat-list");
      let $innerListItem = $(".chat-item[data-id='" + message_id + "']");
      console.log(message_id, $innerListItem);

      $parentDiv.scrollTop(
        $parentDiv.scrollTop() + $innerListItem.position().top
      );
      $(".ci-search-result").hide();
    });
    $(document).on("click", ".ci-load-client", function (event) {
      let user = $(this).data("user");
      $("#clients")
        .find(".ci-load-user[data-user='" + user + "']")
        .trigger("click");
      $(".ci-search-result").hide();
    });

    $(document).on("change", "#ci-attachment", function (event) {
      var fileName = event.target.files[0]?.name;
      $(".ci-file-preview").remove();
      if (fileName) {
        $("#ci-message-form").prepend(
          '<div class="ci-file-preview"><span>' +
            fileName +
            '</span><span class="ci-delete-file"><i class="fas fa-times-circle"></i></span></div>'
        );
        $('textarea[name="message"]').trigger("blur");
      }
    });
    $(document).on("click", ".ci-msg-delete", function (event) {
      let message = $(this).data("message");
      let attachment = $(this).find(".ci-attachment-file").data("id");
      let group_id = $("input[name='group_id']").val();
      let receiver_id = $("input[name='receiver']").val();
      $(".ci-loader").show();
      let msgel = $(this);
      $.ajax({
        type: "POST",
        url: ajaxObj.ajaxurl,
        data: {
          action: "caveni_io_delete_messages",
          message: message,
          attachment: attachment,
          caveni_nonce: ajaxObj.security_message_delete,
        },
        success: function (response) {
          $(".ci-loader").hide();
          if (response.success) {
            let message_delete = {
              message: message,
              type: "message_delete",
              receiver_id: receiver_id,
            };
            socket.send(JSON.stringify(message_delete));
            let msg_date = msgel.parents(".chat-item").data("date");
            msgel.parents(".chat-item").remove();
            if ($(".chat-item[data-date='" + msg_date + "']").length == 0) {
              $(".ci-separator[data-date='" + msg_date + "']").remove();
            }
            if ($(".chat-users-list").length > 0) {
              let chaterwrap = $(".chat-users-list").find(
                ".ci-load-user[data-user='" + receiver_id + "']"
              );
              if ($(".chat-item").length > 0) {
                let last_msg = $(".chat-item:last").find(".inner").text();
                let last_msg_time = $(".chat-item:last")
                  .find(".ci-msg-time")
                  .text();
                chaterwrap.find(".ci-short-msg").text(splitMessage(last_msg));
                chaterwrap.find(".ci-time-ago").text(last_msg_time);
              } else {
                $(".chat-list").html(
                  "<p class='ci-no-message'>Send a new message to start the chat.</p>"
                );
                chaterwrap.remove();
              }
            }
          } else {
            $(".ci-response").html(
              '<div class="ci-error alert alert-danger">' +
                response.data.message +
                "<div>"
            );
          }
        },
      });
    });
    $(document).on("click", function (event) {
      if (!$(event.target).closest(".ci-options-toggle").length) {
        $(".ci-options-menu").removeClass("show");
      }
    });

    $(document).on("click", ".ci-options-toggle", function (event) {
      console.log(event.target, "event");
      $(this).next().addClass("show");
    });

    $(document).on("click", ".ci-delete-file", function (event) {
      $(".ci-file-preview").remove();
      $("#ci-attachment").val("");
      $(".ci-error").remove();
    });
    $(document).on("click", ".ci-tabs", function (event) {
      if ($(this).data("tab") == "chat") {
        $("#ci-search").attr("placeholder", "Search Messages");
      } else {
        $("#ci-search").attr("placeholder", "Search Clients");
      }
    });
    $(document).on("click", ".ci-searching .fa-close", function (event) {
      $(".ci-search-result").html("");
      $(".ci-searching").hide();
      $(".ci-search").val("");
    });
    $(document).on("click", ".ci-search", function (event) {
      $(".ci-search-result").show();
    });
    $(document).on("input keypress", "#ci-search-message", function (event) {
      let search = $("#ci-search-message").val();
      clearTimeout(searchTimeout);
      $(".ci-search-result").html("");
      if (search.length >= 3) {
        $(".ci-searching")
          .show()
          .html('<i class="fas fa-spinner fa-spin"></i>');
        searchTimeout = setTimeout(function () {
          $.ajax({
            type: "POST",
            url: ajaxObj.ajaxurl,
            data: {
              action: "caveni_io_search_messages",
              search: search,
              user: ajaxObj.caveniUser,
              caveni_nonce: ajaxObj.search_message,
            },
            success: function (response) {
              $(".ci-searching").html('<i class="fas fa-close"></i>');
              if (response.success) {
                let list = "";
                if (response.data.results) {
                  list = "<ul>";
                  $.each(response.data.results, function (i, s) {
                    list +=
                      '<li class="ci-scroll-message"  data-id="' +
                      s.id +
                      '" data-type="message"><div class="ci-search-top">';
                    list +=
                      '<img src="' +
                      s.avtar +
                      '" class="rounded-square" width="200" height="200" alt="attachment" />';
                    list += "<span>" + s.name + "</span></div>";
                    list +=
                      '<div class="ci-search-bottom">' + s.message + "</div>";
                    list += "</li>";
                  });
                  list += "</ul>";
                }
                $(".ci-search-result").show().html(list);
              }
            },
          });
        }, 500);
      } else {
        $(".ci-searching").hide();
      }
    });

    $(document).on("input keypress", "#ci-search", function (event) {
      let search = $("#ci-search").val();
      let type = $(".ci-tabs.active").data("tab");
      clearTimeout(searchTimeout);
      $(".ci-search-result").html("");
      if (search.length >= 3) {
        $(".ci-searching")
          .show()
          .html('<i class="fas fa-spinner fa-spin"></i>');
        searchTimeout = setTimeout(function () {
          $.ajax({
            type: "POST",
            url: ajaxObj.ajaxurl,
            data: {
              action: "caveni_io_search_messages",
              search: search,
              type: type,
              caveni_nonce: ajaxObj.search_message,
            },
            success: function (response) {
              $(".ci-searching").html('<i class="fas fa-close"></i>');

              if (response.success) {
                let list = "";
                console.log(
                  response.data.results.length,
                  response.data.results
                );
                if (response.data.results) {
                  list = "<ul>";

                  if (type == "clients") {
                    $.each(response.data.results, function (i, s) {
                      list +=
                        '<li class="ci-load-client" data-group="' +
                        s.group_id +
                        '" data-user="' +
                        s.user +
                        '" data-type="searchclient"><div class="ci-search-top">';
                      list +=
                        '<img src="' +
                        s.profileImg +
                        '" class="rounded-square" width="200" height="200" alt="attachment" />';
                      list += "<span>" + s.name + "</span></div>";
                      list += "</li>";
                    });
                  } else {
                    $.each(response.data.results, function (i, s) {
                      list +=
                        '<li class="ci-load-message" data-group_id="' +
                        s.group_id +
                        '" data-message="' +
                        s.message +
                        '" data-name="' +
                        s.name +
                        '" data-user="' +
                        s.user +
                        '" data-id="' +
                        s.id +
                        '" data-type="message"><div class="ci-search-top">';
                      list +=
                        '<img src="' +
                        s.avtar +
                        '" class="rounded-square" width="200" height="200" alt="attachment" />';
                      list += "<span>" + s.name + "</span></div>";
                      list +=
                        '<div class="ci-search-bottom">' + s.message + "</div>";
                      list += "</li>";
                    });
                  }
                  list += "</ul>";
                }
                $(".ci-search-result").show().html(list);
              } else {
              }
            },
          });
        }, 500);
      } else {
        $(".ci-searching").hide();
      }
    });

    $(document).on("keydown", 'textarea[name="message"]', function (e) {
      if (e.keyCode == 13 && !e.shiftKey) {
        // Prevent the default action (which is creating a new line)
        e.preventDefault();
        $(this).closest("form").submit();
      }
    });

    $(document).on("submit", "#ci-message-form", function (event) {
      event.preventDefault();
      let form = $(this);
      let msg = $.trim(form.find('textarea[name="message"]').val()) || "";
      let fileInput = $("#ci-attachment")[0];
      let filecount = fileInput.files.length;
      if (msg == "" && filecount == 0) {
        return;
      }
      if (sendMessage) {
        return;
      }
      var formData = new FormData(this);
      if (filecount > 0) {
        $(".ci-loader").show();
      }
      $(".ci-error, .ci-no-message").remove();
      let group_id = $("input[name='group_id']").val();
      let sender_avtar = $(".chater-avtar").attr("src");
      let sender_name = $(".chater-info span").data("name");

      sendMessage = $.ajax({
        type: "POST",
        url: ajaxObj.ajaxurl,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          sendMessage = null;
          $(".ci-loader").hide();
          $(".ci-file-preview").remove();
          form.trigger("reset");
          console.log(response);
          if (response.success) {
            socket.send(JSON.stringify(response.data));
            $(".ci-file-preview").remove();
            form.trigger("reset");
            let msg =
              '<div class="chat-item mb-3 even" data-id="' +
              response.data.sent.id +
              '" data-date="' +
              response.data.sent.date +
              '">' +
              '<div class="d-flex justify-content-between ci-message-info">' +
              '<span class="ci-msg-time">now</span>' +
              "</div>" +
              '<div class="message mt-1"><div class="inner">';
            if (response.data.sent.attachment.url) {
              msg +=
                '<a href="' +
                response.data.sent.attachment.url +
                '" class="btn btn-link p-0 ci-attachment-file" download data-id="' +
                response.data.sent.attachment.id +
                '">';
              if (response.data.sent.attachment.type == "image") {
                msg +=
                  '<img src="' +
                  response.data.sent.attachment.url +
                  '" class="rounded-square" width="200" height="200" alt="attachment" />';
              } else {
                msg += '<i class="fas fa-file"></i>';
              }
              msg += response.data.sent.attachment.name + "</a>";
            }
            msg += response.data.sent.message + "</div>";
            if (
              jQuery.inArray(ajaxObj.caveniUser, ajaxObj.caveniReceviers) !== -1
            ) {
              msg +=
                '<div class="ci-msg-options"><button class="ci-options-toggle card-dot-btn lh-1"><i class="fas fa-ellipsis-v"></i></button><ul class="ci-options-menu"><li><a class="ci-options-item ci-msg-delete" href="javascript:void(0);" data-message="' +
                response.data.sent.id +
                '"><i class="fas fa-trash"></i> Delete </a></li></ul></div>';
            }
            msg += "</div>";
            msg += "</div>";
            if (
              $(".chat-item[data-date='" + response.data.sent.date + "']")
                .length == 0
            ) {
              $(".chat-list").append(
                '<div class="ci-separator" data-date="' +
                  response.data.sent.date +
                  '"><span>Today</span></div>'
              );
            }

            $(".chat-list").append(msg);
            let shortmsg = splitMessage(response.data.sent.message);
            if ($(".chat-users-list").length > 0) {
              let chaterwrap = $(".chat-users-list").find(
                ".ci-load-user[data-user='" +
                  response.data.sent.receiver_id +
                  "']"
              );
              if (chaterwrap.length > 0) {
                chaterwrap.find(".ci-short-msg").text(shortmsg);
                chaterwrap.find(".ci-time-ago").text("now");
                chaterwrap.prependTo(".chat-users-list");
              } else {
                let userchat =
                  '<div class="single-user-item position-relative ci-load-user" data-user="' +
                  response.data.sent.receiver_id +
                  '" data-name="' +
                  sender_name +
                  '" data-group_id="' +
                  group_id +
                  '" data-type="chat"><div class="d-flex align-items-center"><img src="' +
                  sender_avtar +
                  '" width="44" height="44" class="rounded-circle" alt="user"><div class="ms-12"><span class="title">' +
                  sender_name +
                  '<span class="ci-time-ago">now</span></span><span class="d-block text-black ci-short-msg">' +
                  splitMessage(response.data.sent.message) +
                  '</span></div></div><span class="rounded-circle ci-unread-msg"></span></div>';
                $(".chat-users-list").prepend(userchat);
                let refresh_recent_chats = {
                  receiver_id: response.data.sent.receiver_id,
                  sender_name: sender_name,
                  group_id: group_id,
                  sender_avtar: sender_avtar,
                  message: splitMessage(response.data.sent.message),
                  type: "refresh_recent_chats",
                };
                socket.send(JSON.stringify(refresh_recent_chats));
              }
            }
            if ($(".ci-admin-chat").length > 0) {
              $(".ci-admin-chat").find(".ci-short-msg").text(shortmsg);

              $(".ci-admin-chat")
                .find("img")
                .attr("src", $(".ci-admin-chat").data("clientavtar"));
              $(".ci-admin-chat")
                .find(".title")
                .html(
                  $(".ci-admin-chat").data("client") +
                    '<span class="ci-time-ago">now</span>'
                );

              // $(".ci-admin-chat").find(".ci-time-ago").text("now");
            }
          } else {
            $(".chat-list").append(
              '<div class="ci-error alert alert-danger">' +
                response.data.message +
                "<div>"
            );
          }
          $(".chat-list").scrollTop($(".chat-list")[0].scrollHeight);
        },
      });
    });
    if ($(".chat-list").length > 0) {
      $(".chat-list").scrollTop($(".chat-list")[0].scrollHeight);
    }

    function get_messages(element, scroll_id = 0) {
      let user = element.data("user");
      let group_id = parseInt(element.data("group_id")) || 0;
      let name = element.data("name");
      let sender = $("input[name='sender']").val();
      let sender_avtar = element.find(".rounded-circle").attr("src");
      let sender_name = name;
      $(".chater-avtar").attr("src", sender_avtar);
      $(".chater-info span").text(sender_name + " Chat");
      $(".ci-load-user").removeClass("active");
      $('input[name="receiver"]').val(user);
      $('input[name="group_id"]').val(group_id);
      $(".ci-loader").show();
      $(".ci-messager-name").text(name);
      $(".ci-error").remove();
      $(".chat-list").html("");
      $("#ci-message-form").trigger("reset");
      $(".ci-file-preview").remove();
      element.addClass("active");
      element.find(".ci-unread-msg").text("");
      element.find(".ci-unread-msg").removeClass("show");
      element.find(".ci-unread-msg").attr("data-count", 0);

      $.ajax({
        type: "POST",
        url: ajaxObj.ajaxurl,
        data: {
          action: "caveni_io_load_user_messages",
          user: user,
          group_id: group_id,
          caveni_nonce: ajaxObj.security_message,
        },
        success: function (response) {
          $(".ci-loader").hide();
          $(".chat-list").html("");
          if (response.success) {
            if (response.data.messages && response.data.messages.length > 0) {
              let lastDate = "";
              $.each(response.data.messages, function (item, val) {
                let even = sender == val.sender_id ? "even" : "";
                let msg = "";
                if (val.separator != lastDate) {
                  msg +=
                    '<div class="ci-separator" data-date="' +
                    val.date_group +
                    '"><span>' +
                    val.separator +
                    "</span></div>";
                  lastDate = val.separator;
                }
                msg +=
                  '<div class="chat-item mb-3 ' +
                  even +
                  '" data-id="' +
                  val.id +
                  '" data-date="' +
                  val.date_group +
                  '">' +
                  '<div class="d-flex justify-content-between ci-message-info">';
                if (!even) {
                  msg +=
                    '<div class="chater-img"><img src="' +
                    sender_avtar +
                    '" class="rounded-circle user" width="44" height="44" alt="user" /></div>' +
                    '<span class="ci-sender-name">' +
                    sender_name +
                    "</span>";
                }
                msg +=
                  '<span class="ci-msg-time">' +
                  val.date +
                  "</span>" +
                  "</div>" +
                  '<div class="message mt-1"><div class="inner">';
                if (val.attachment.url) {
                  msg +=
                    '<a href="' +
                    val.attachment.url +
                    '" class="btn btn-link p-0 ci-attachment-file" download data-id="' +
                    val.attachment.id +
                    '">';
                  if (val.attachment.type == "image") {
                    msg +=
                      '<img src="' +
                      val.attachment.url +
                      '" class="rounded-square" width="200" height="200" alt="attachment" />';
                  } else {
                    msg += '<i class="fas fa-file"></i>';
                  }
                  msg += val.attachment.name + "</a>";
                }
                msg += val.message + "</div>";
                msg +=
                  '<div class="ci-msg-options"><button class="ci-options-toggle card-dot-btn lh-1"><i class="fas fa-ellipsis-v"></i></button><ul class="ci-options-menu"><li><a class="ci-options-item ci-msg-delete" href="javascript:void(0);" data-message="' +
                  val.id +
                  '"><i class="fas fa-trash"></i> Delete </a></li></ul></div>';
                msg += "</div>";
                msg += "</div>";

                $(".chat-list").append(msg);
              });
              $(".chat-list").scrollTop($(".chat-list")[0].scrollHeight);
              let notify_unred = { user: user, type: "notification" };
              socket.send(JSON.stringify(notify_unred));

              if (scroll_id) {
                let $parentDiv = $(".chat-list");
                let $innerListItem = $(
                  ".chat-item[data-id='" + scroll_id + "']"
                );
                console.log(scroll_id, $innerListItem);

                $parentDiv.scrollTop(
                  $parentDiv.scrollTop() + $innerListItem.position().top
                );
              }
            } else {
              $(".chat-list").html(
                "<p class='ci-no-message'>" + response.data.message + "</p>"
              );
            }
          } else {
            $(".ci-response").html(
              '<div class="ci-error alert alert-danger">' +
                response.data.message +
                "<div>"
            );
          }
        },
      });
    }
    function loadMessage(messageData) {
      console.log(messageData);
      if (messageData.type && messageData.type == "notification") {
        read_notification(messageData);
        return;
      } else if (messageData.type && messageData.type == "message_delete") {
        delete_message(messageData);
        return;
      } else if (
        messageData.type &&
        messageData.type == "refresh_recent_chats"
      ) {
        refresh_recent_chats(messageData);
      }
      let receivedData = messageData.sent;
      let firstMessage = messageData.first;
      let receiver = $('input[name="receiver"]').val();
      let sender = $("input[name='sender']").val();
      let admins = ajaxObj.caveniReceviers;
      let loadMsg = false;
      if (
        jQuery.inArray(ajaxObj.caveniUser, admins) !== -1 &&
        (receiver == receivedData.sender_id ||
          receivedData.receiver_id == receiver)
      ) {
        loadMsg = true;
      } else if (
        !(jQuery.inArray(ajaxObj.caveniUser, admins) !== -1) &&
        receivedData.receiver_id == sender
      ) {
        loadMsg = true;
      }
      //ajaxObj.caveniUser == receivedData.receiver_id && receiver == receivedData.sender_id
      let shortmsg = splitMessage(receivedData.message);
      if (loadMsg) {
        let msg =
          '<div class="chat-item mb-3" data-id="' +
          receivedData.id +
          '" data-date="' +
          receivedData.date +
          '">' +
          '<div class="d-flex justify-content-between ci-message-info">' +
          '<div class="chater-img"><img src="' +
          receivedData.sender_avtar +
          '" class="rounded-circle user" width="44" height="44" alt="user" /></div>' +
          '<span class="ci-sender-name">' +
          receivedData.sender_name +
          "</span>" +
          '<span class="ci-msg-time">now</span>' +
          "</div>" +
          '<div class="message mt-1"><div class="inner">';
        if (receivedData.attachment.url) {
          msg +=
            '<a href="' +
            receivedData.attachment.url +
            '" class="btn btn-link p-0 ci-attachment-file" download data-id="' +
            receivedData.attachment.id +
            '">';
          if (receivedData.attachment.type == "image") {
            msg +=
              '<img src="' +
              receivedData.attachment.url +
              '" class="rounded-square" width="200" height="200" alt="attachment" />';
          } else {
            msg += '<i class="fas fa-file"></i>';
          }
          msg += receivedData.attachment.name + "</a>";
        }
        msg += receivedData.message + "</div>";
        if (
          jQuery.inArray(ajaxObj.caveniUser, ajaxObj.caveniReceviers) !== -1
        ) {
          msg +=
            '<div class="ci-msg-options"><button class="ci-options-toggle card-dot-btn lh-1"><i class="fas fa-ellipsis-v"></i></button><ul class="ci-options-menu"><li><a class="ci-options-item ci-msg-delete" href="javascript:void(0);" data-message="' +
            receivedData.id +
            '"><i class="fas fa-trash"></i> Delete </a></li></ul></div>';
        }
        msg += "</div>";
        msg += "</div>";
        $(".ci-error, .ci-no-message").remove();
        if (
          $(".chat-item[data-date='" + receivedData.date + "']").length == 0
        ) {
          $(".chat-list").append(
            '<div class="ci-separator" data-date="' +
              receivedData.date +
              '"><span>Today</span></div>'
          );
        }
        $(".chat-list").append(msg);
        $(".chat-list").scrollTop($(".chat-list")[0].scrollHeight);
        //update client side sidebar
        if ($(".ci-admin-chat").length > 0) {
          $(".ci-admin-chat").find(".ci-short-msg").text(shortmsg);
          $(".ci-admin-chat")
            .find("img")
            .attr("src", receivedData.sender_avtar);
          $(".ci-admin-chat")
            .find(".title")
            .html(
              receivedData.sender_name + '<span class="ci-time-ago">now</span>'
            );
        }
      }
      if ($(".chat-users-list").length > 0) {
        let chaterwrap = $(".chat-users-list").find(
          ".ci-load-user[data-user='" + receivedData.sender_id + "']"
        );

        if (jQuery.inArray(receivedData.sender_id, admins) !== -1) {
          chaterwrap = $(".chat-users-list").find(
            ".ci-load-user[data-user='" + receivedData.receiver_id + "']"
          );
        }

        if (chaterwrap.length > 0) {
          chaterwrap.find(".ci-short-msg").text(shortmsg);
          chaterwrap.find(".ci-time-ago").text("now");
          chaterwrap.prependTo(".chat-users-list");
          if (receiver != receivedData.sender_id) {
            let unread =
              parseInt(chaterwrap.find(".ci-unread-msg").attr("data-count")) ||
              0;
            chaterwrap.find(".ci-unread-msg").attr("data-count", unread + 1);
            chaterwrap
              .find(".ci-unread-msg")
              .addClass("show")
              .text(unread + 1);
          }
        } else if (
          firstMessage &&
          !(jQuery.inArray(receivedData.sender_id, admins) !== -1)
        ) {
          let userchat =
            '<div class="single-user-item position-relative ci-load-user" data-user="' +
            receivedData.sender_id +
            '" data-name="' +
            firstMessage.name +
            '" data-group_id="' +
            receivedData.group_id +
            '" data-type="chat"><div class="d-flex align-items-center"><img src="' +
            firstMessage.avtar +
            '" width="44" height="44" class="rounded-circle" alt="user"><div class="ms-12"><span class="title">' +
            firstMessage.name +
            '<span class="ci-time-ago">now</span></span><span class="d-block text-black ci-short-msg">' +
            splitMessage(receivedData.message) +
            '</span></div></div><span class="rounded-circle ci-unread-msg show">1</span></div>';
          $(".chat-users-list").prepend(userchat);
        }
      }
    }

    function read_notification(notification) {
      let admins = ajaxObj.caveniReceviers;
      if (jQuery.inArray(ajaxObj.caveniUser, admins) !== -1) {
        let chaterwrap = $(".chat-users-list").find(
          ".ci-load-user[data-user='" + notification.user + "']"
        );
        chaterwrap.find(".ci-unread-msg").text("");
        chaterwrap.find(".ci-unread-msg").removeClass("show");
        chaterwrap.find(".ci-unread-msg").attr("data-count", 0);
      }
    }
    function delete_message(response) {
      if ($(".chat-item[data-id='" + response.message + "']").length > 0) {
        if ($(".chat-item[data-date='" + msg_date + "']").length == 0) {
          $(".ci-separator[data-date='" + msg_date + "']").remove();
        }
        $(".chat-item[data-id='" + response.message + "']").remove();
      }

      if ($(".chat-admin-list").length > 0) {
        if ($(".chat-item").length > 0) {
          let last_msg = $(".chat-item:last").find(".inner").text();
          let last_msg_time = $(".chat-item:last").find(".ci-msg-time").text();
          $(".ci-short-msg").text(splitMessage(last_msg));
          $(".ci-time-ago").text(last_msg_time);
        } else {
          $(".chat-list").html(
            "<p class='ci-no-message'>Send a new message to start the chat.</p>"
          );
          $(".ci-short-msg").text("");
          $(".ci-time-ago").text("");
        }
      }
      if (
        $(".chat-users-list").length > 0 &&
        $("input[name='receiver']").val() == response.receiver_id
      ) {
        let chaterwrap = $(".chat-users-list").find(
          ".ci-load-user[data-user='" + response.receiver_id + "']"
        );
        if ($(".chat-item").length > 0) {
          let last_msg = $(".chat-item:last").find(".inner").text();
          let last_msg_time = $(".chat-item:last").find(".ci-msg-time").text();
          chaterwrap.find(".ci-short-msg").text(splitMessage(last_msg));
          chaterwrap.find(".ci-time-ago").text(last_msg_time);
        } else {
          $(".chat-list").html(
            "<p class='ci-no-message'>Send a new message to start the chat.</p>"
          );
          chaterwrap.remove();
        }
      }
    }

    function refresh_recent_chats(response) {
      if ($(".chat-users-list").length > 0) {
        let userchat =
          '<div class="single-user-item position-relative ci-load-user" data-user="' +
          response.receiver_id +
          '" data-name="' +
          response.sender_name +
          '" data-group_id="' +
          response.group_id +
          '" data-type="chat"><div class="d-flex align-items-center"><img src="' +
          response.sender_avtar +
          '" width="44" height="44" class="rounded-circle" alt="user"><div class="ms-12"><span class="title">' +
          response.sender_name +
          '<span class="ci-time-ago">now</span></span><span class="d-block text-black ci-short-msg">' +
          splitMessage(response.message) +
          '</span></div></div><span class="rounded-circle ci-unread-msg"></span></div>';
        $(".chat-users-list").prepend(userchat);
      }
    }

    function splitMessage(str) {
      if (str == "") {
        return "attachment";
      }
      return str.length > 30 ? str.substring(0, 30) + "..." : str;
      var words = str.split(" ");
      if (words.length > 4) {
        var result = words.slice(0, 4).join(" ") + "...";
      } else {
        var result = str;
      }
      return result;
    }

    // Set the new position dynamically

    $(".select2").select2();

    function addSearchIcon() {
      $(".select2-search__field").each(function () {
        if (!$(this).siblings(".search-icon").length) {
          $(this).before('<i class="fa fa-search search-icon"></i>');
        }

        $(this).attr("placeholder", "search client");
      });
    }

    addSearchIcon();

    $(".select2").on("select2:open", function () {
      addSearchIcon();
    });
  });

  $(".caveni--filter-tabs").each(function () {
    const $filterTabs = $(this); // Reference to the current filter tabs container

    $filterTabs.find(".caveni--tab-option").on("click", function () {
      const $currentTab = $(this); // Reference to the clicked tab option

      // Remove active class from other tabs within the current container
      $filterTabs
        .find(".caveni--tab-option.caveni--active")
        .removeClass("caveni--active");
      $currentTab.parent().removeClass("active-left active-right");

      $currentTab.addClass("caveni--active");

      if ($currentTab.data("value") == "seo") {
        $(".caveni--tab-option[data-value='seo']").addClass("caveni--active");
        $(".caveni--tab-option[data-value='ppc']").removeClass(
          "caveni--active"
        );

        $currentTab.parent().addClass("active-left");
      }

      if ($currentTab.data("value") == "ppc") {
        $(".caveni--tab-option[data-value='ppc']").addClass("caveni--active");
        $(".caveni--tab-option[data-value='seo']").removeClass(
          "caveni--active"
        );

        $currentTab.parent().addClass("active-right");
      }

      const filterContainer = $currentTab
        .parent()
        .parent()
        .parent()
        .find(".caveni-chart-filters");

      if ($currentTab.hasClass("trigger-custom-range")) {
        // Toggle only the filter container related to the clicked tab
        console.log(filterContainer);
        filterContainer.slideToggle().css({
          top: "90px",
          right: "25px",
        });
      } else {
        filterContainer.slideUp();
      }
    });
  });

  $(".caveni--custom-date-btn a").click(function () {
    $(".custom-date-filter-modal").slideToggle().css({
      top: "75px",
      right: "25px",
    });
  });

  // Accordion toggle behavior for each filter section
  $(".filter-section h4").on("click", function () {
    const $section = $(this).parent();
    const $items = $section.find(".filter-items");
    const isOpen = $section.hasClass("open");

    // Close all sections
    $(".filter-section").removeClass("open").find(".filter-items").slideUp(300);

    // If the current section was not open, open it
    if (!isOpen) {
      $section.addClass("open");
      $items.stop().slideDown(300);
    }
  });

  $('.filter-seo-ppc button[data-value="seo"]').click(function () {
    $("#e-n-tab-title-2555460631").click();
  });

  $('.filter-seo-ppc button[data-value="ppc"]').click(function () {
    $("#e-n-tab-title-2555460632").click();
  });

  $(".seo-date-filters .caveni--select-option").change(function () {
    $('input[name="custom_date_selected"]').val("0");
    $('input[name="caveni_end_date"]').val("yesterday");

    let selected = $(this).find(":selected").val();
    $(
      `.seo-date-filters .caveni--tab-option[data-value="${selected}"]`
    ).click();
  });

  let clientSearch = (module) => {
    $(`.client-search-${module}`).change(function () {
      let selectedClientId = $(this).val();
      console.log(selectedClientId);
      $(`.${module}-client-search-form`)
        .find("input[name='client_search']")
        .val(selectedClientId);

      $(`.client-search-trigger.for-${module}`).click();
    });

    $(`.${module}-date-filters .caveni--tab-option`).click(function () {
      let selectedDate = $(this).data("value");
      console.log(selectedDate);

      $(`.${module}-client-search-form input[name="caveni_start_date"]`).val(
        selectedDate
      );

      $(`.${module}-client-search-form`).submit();
    });

    $(".client-search-trigger").click(function () {
      $(`.${module}-client-search-form`).submit();
    });
  };

  clientSearch("seo");
})(jQuery);

jQuery(document).ready(function ($) {
  let shortCodeRunning = $(".search-seo-summary").length;

  if (shortCodeRunning) {
    jQuery("#custom_start_date").periodpicker({
      end: "#custom_end_date",
      yearsLine: false,
      cells: [1, 2],
      formatDate: "MM/DD/YYYY",
      todayButton: false,

      onChangePeriod: function () {
        // Do nothing on change, just let the user pick dates
      },

      onOkButtonClick: function () {
        const start = new Date(this.startinput.val());
        const end = new Date(this.endinput.val());

        // Format the dates as YYYY-MM-DD
        const formattedStartDate = `${start.getFullYear()}-${String(
          start.getMonth() + 1
        ).padStart(2, "0")}-${String(start.getDate()).padStart(2, "0")}`;

        const formattedEndDate = `${end.getFullYear()}-${String(
          end.getMonth() + 1
        ).padStart(2, "0")}-${String(end.getDate()).padStart(2, "0")}`;

        // Set the formatted values only on button click
        $('input[name="caveni_start_date"]').val(formattedStartDate);
        $('input[name="caveni_end_date"]').val(formattedEndDate);

        // Mark custom date as selected
        $('input[name="custom_date_selected"]').val("1");

        // Submit form
        $(".seo-client-search-form").submit();

        $(".caveni-chart-filters").attr("style", "display:none");

        $(".caveni--select-option option[value='custom']")
          .prop("disabled", false)
          .prop("selected", true);

        setTimeout(() => {
          $(".caveni--select-option option[value='custom']").prop(
            "disabled",
            true
          );
        }, 100);
      },
    });
  }
});
