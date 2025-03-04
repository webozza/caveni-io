jQuery(document).ready(function($){
	"use strict";


	// Enable Disable Submit buttons 
	function submit_button_enable( button, enable=false ) {
		
		if(enable){
			button.attr("disabled", false);
			button.find('span.button-text').removeClass('d-none');
			button.find('span.spinner-display').addClass('d-none');
		}else{
			button.attr("disabled", true);
			button.find('span.button-text').addClass('d-none');
			button.find('span.spinner-display').removeClass('d-none');
		}	
	}

	// Enable Disable Submit buttons 
	function setting_saved_confirmation( msg="", error=false, reload=true, reloadUrl ='', alertId = "#setting_save_confirmation_alert" ) {
		console.log(alertId,"work",$(alertId));
		if(error){
			$(alertId).html(msg);
			$(alertId).addClass("alert-danger");
			$(alertId).removeClass("d-none");
		}else{
			$(alertId).html(msg);
			$(alertId).addClass("alert-success");
			$(alertId).removeClass("d-none");
		}

		if(reload){
			setTimeout(() => {
				if(reloadUrl !== ''){
					window.location.href = reloadUrl;
				}else{
					window.location.reload();
				}
			}, 5000);
		}
	}

	$(".update_plugin_hub_settings_btn").on('click',function(event) {
		event.preventDefault();
		let from_email = $("#caveni_setting_from_emai").val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (from_email !== "" && !emailRegex.test(from_email)) {
            alert("Please enter a valid From Email before saving the settings");
			return false;
        }
		let button = $(this);
		submit_button_enable(button,false);
		var settingsFormData = new FormData($("#plugin-hub-setting-form")[0]);

		$.ajax({
			type: 'POST',
			url: caveni.optionsUrl,
			data: settingsFormData,
			processData: false,
			contentType: false,
			success: function () {
				setting_saved_confirmation('Settings saved Successfully',false,false,'');
				submit_button_enable(button,true);
				// window.location.reload(); 
			},
			error: function () {
				console.log('not work');
			}
		});

		return false;
	});


	$(window).on('load',function(){
		if (window.location.hash) {
			let hash_id = window.location.hash;
			$("a.plugin-hub-link[href='"+hash_id+"']")[0].click();
		}
		$("a.plugin-hub-link").on('click',function(event) {
			event.preventDefault();
			let hash_id = $(this).attr('href');
			window.location.hash = hash_id;
		});
	});

	

	if($('#post_type').length > 0 && $('#post_type').val() == "caveni_ticket"){
		
		$.validator.addMethod('filesize', function(value, element, param) {
			// param = size (en bytes) 
			// element = element to validate (<input>)
			// value = value of the element (file name)
			return this.optional(element) || (element.files[0].size <= param) 
		});
		$.validator.addMethod('accept', function(value, element, param) {
			// param = size (en bytes) 
			// element = element to validate (<input>)
			// value = value of the element (file name)
			//var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
			var allowed_files = param.split(",");
			//console.log(element.files[0].name.split('.').pop().toLowerCase(),allowed_files, $.inArray(element.files[0].name.split('.').pop().toLowerCase(), allowed_files) != -1);
			return this.optional(element) || ($.inArray('.'+(element.files[0].name.split('.').pop().toLowerCase()), allowed_files) != -1)
		});
		// Initialize form validation on the registration form.
		// It has the name attribute "registration"
		$("#post").validate({
			// Specify validation rules
			rules: {
			// The key name on the left side is the name attribute
			// of an input field. Validation rules are defined
			// on the right side
			
			caveni_ticket_file: { required: false, accept: caveni.allowed_file_types, filesize: caveni.allowed_file_size  },
			caveni_ticket_comment_file: { required: false, accept: caveni.allowed_file_types, filesize: caveni.allowed_file_size  }
		},
			// Specify validation error messages
			messages: {
				caveni_ticket_file: caveni.allowed_file_types_msg,
				caveni_ticket_comment_file: caveni.allowed_file_types_msg
			},
			errorElement: "em",
			// Make sure the form is submitted to the destination defined
			// in the "action" attribute of the form when valid
			submitHandler: function(form) {
				form.submit();
			}
		});
	}

	$("#add-new-category-modal-form").validate({
		// Specify validation rules
		rules: {
		// The key name on the left side is the name attribute
		// of an input field. Validation rules are defined
		// on the right side
		
		category_name_field: "required",
	},
		// Specify validation error messages
		messages: {
			category_name_field: caveni.category_name_required_msg
		},
		errorElement: "em",
		// Make sure the form is submitted to the destination defined
		// in the "action" attribute of the form when valid
		submitHandler: function(form) {
			form.submit();
		}
	});

	$("#edit-new-category-modal-form").validate({
		// Specify validation rules
		rules: {
		// The key name on the left side is the name attribute
		// of an input field. Validation rules are defined
		// on the right side
		
		edit_category_name_field: "required",
	},
		// Specify validation error messages
		messages: {
			edit_category_name_field: caveni.category_name_required_msg
		},
		errorElement: "em",
		// Make sure the form is submitted to the destination defined
		// in the "action" attribute of the form when valid
		submitHandler: function(form) {
			form.submit();
		}
	});


	$('.cavenio-roles').select2({
		width:"290px",
		multiple:true,
		allowClear: true
	});
	$('.submit_ticket_comment').on('click',function(e){
		e.preventDefault();
		if( $('#reply_comment').val() == "" ) {
			alert('Please add your comment before submiting');
			return false;
		}
		if(!$('form#post').valid()) return false;

		
		$('.caveni-spinner').addClass('is-active');
		var formData = new FormData();
		formData.append('action', 'reply_ticket_comment');
		formData.append('comment', $('#reply_comment').val());
		formData.append('caveni_ticket_comment_file', $('#comment-file')[0].files[0]);
		formData.append('ticket_id', $('#post_ID').val());
		formData.append('_caveni_io_tickets_nonce', $('#_caveni_io_tickets_nonce').val());
		$.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
				$('.caveni_comments').html(response.data);
				$('.caveni-spinner').removeClass('is-active');  
				window.location.reload();
            },
            error: function(xhr, status, error) {
            }
        });
	});

	$("#add-new-categorry-button").on("click",function(event) {
		event.preventDefault();
		if(!$('#add-new-category-modal-form').valid()) return false;
		let button = $(this);
		submit_button_enable(button,false);
		var categoryFormData = new FormData($("#add-new-category-modal-form")[0]);
		$.ajax({
			type: 'POST',
			url: caveni.ajaxurl,
			data: categoryFormData,
			processData: false,
			contentType: false,
			success: function (response) {
				let responseInner = JSON.parse(response);
				//let jsonReponse = JSON.parse(responseA);
				if(responseInner.status == "success"){
					$("#add-new-category-modal-form").addClass('d-none');
				}
				
				setting_saved_confirmation(responseInner.message,false,true,'','#category_save_confirmation_alert');
				//submit_button_enable(button,true);
			},
			error: function () {
				console.log('not work');
			}
		});

		return false;
	
	});

	$("#edit-new-categorry-button").on("click",function(event) {
		event.preventDefault();
		if(!$('#edit-new-category-modal-form').valid()) return false;
		let button = $(this);
		submit_button_enable(button,false);
		var categoryFormData = new FormData($("#edit-new-category-modal-form")[0]);
		$.ajax({
			type: 'POST',
			url: caveni.ajaxurl,
			data: categoryFormData,
			processData: false,
			contentType: false,
			success: function (response) {
				let responseInner = JSON.parse(response);
				//let jsonReponse = JSON.parse(responseA);
				if(responseInner.status == "success"){
					$("#edit-new-category-modal-form").addClass('d-none');
				}
				
				setting_saved_confirmation(responseInner.message,false,true,'','#edit_category_save_confirmation_alert');
				//submit_button_enable(button,true);
			},
			error: function () {
				console.log('not work');
			}
		});

		return false;
	
	});

	$(".edit-ticket-category-admin").on('click',function(event){
		event.preventDefault();
		let category_id = parseInt($(this).attr('data-category-id'));
		let category_name = $(this).attr('data-category-name');
		$("#edit-category-name-input").val(category_name);
		$("#category_id_hidden_field").val(category_id);
		
		var myModal = new bootstrap.Modal(document.getElementById('edit-categories'), {
			keyboard: false
		  })
		  myModal.show();
	});

	$(".delete-ticket-category-admin").on('click',function(event){
		event.preventDefault();
			if (!confirm("Do you really want to delete this category?") == true) return false;
			let category_id = parseInt($(this).attr('data-category-id'));
			
			if(category_id <= 0) {
				alert("Category id is not correct");
				return false;
			}

			let formData = new FormData();
			formData.append("action", "caveni_io_delete_category_action");
			formData.append("delete_category_id", category_id);
			formData.append("delete_category_nonce", caveni.security_category);

			$.ajax({
				type : "POST",
				contentType: false,
				processData: false,
				url : caveni.ajaxurl,
				data : formData ,
				success: function(response) {
					let responseInner = JSON.parse(response);
					setting_saved_confirmation(responseInner.message,false,true,'','#deleted_category_confirmation_alert');
				}
			});
	});
});
