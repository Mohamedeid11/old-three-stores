"use strict";

// Class definition
var KTWizard1 = function () {
	// Base elements
	var wizardEl;
	var formEl;
	var validator;
	var wizard;
	
	// Private functions
	var initWizard = function () {
		// Initialize form wizard
		wizard = new KTWizard('kt_wizard_v1', {
			startStep: 1
		});

		// Validation before going to next page
		wizard.on('beforeNext', function(wizardObj) {
			if (validator.form() !== true) {
				wizardObj.stop();  // don't go to the next step
			}
		})

		// Change event
		wizard.on('change', function(wizard) {
			setTimeout(function() {
				KTUtil.scrollTop();	
			}, 500);
		});
	}	

	var initValidation = function() {
		validator = formEl.validate({
			// Validate only visible fields
			ignore: ":hidden",

			// Validation rules
			rules: {	
				//= Step 1
				client_search: {
					required: true 
				},
				name: {
					required: true
				},	   
				phone: {
					required: true
				},	 
                //= Step 2
                order_number: {
                    required: true
                },
                order_category: {
                    required: true
                },
                shipping_date: {
                    required: true
                },
				//= Step 4
				address: {
					required: true
				},
				city: {
					required: true
				},	
				ship_price: {
					required: true,
					number: true,
					min: 0
				},	
				location: {
				    url: true
				}
			},
			
			// Display error  
			invalidHandler: function(event, validator) {	 
				KTUtil.scrollTop();

				swal.fire({
					"title": "", 
					"text": "There are some errors in your submission. Please correct them.", 
					"type": "error",
					"confirmButtonClass": "btn btn-secondary"
				});
			},

			// Submit valid form
			submitHandler: function (form) {
				
			}
		});   
	}

	var initSubmit = function() {
		var btn = formEl.find('[data-ktwizard-type="action-submit"]');

		btn.on('click', function(e) {
			e.preventDefault();

			if (validator.form()) {
				// See: src\js\framework\base\app.js
				KTApp.progress(btn);
				//KTApp.block(formEl);

				// See: http://malsup.com/jquery/form/#ajaxSubmit
				e.preventDefault(); 
                $('#ajsuform_yu').empty();
                var action = $("#kt_form").attr('action');
                var formData = new FormData($("#kt_form")[0]);
                $.ajax({
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    url: action,
                    error: function(data) {
                        jQuery.each(data.errors, function(key, value){
                            $('#ajsuform_yu').html('<div class="alert alert-danger">'+value+'</div>');	
                            KTApp.unprogress(btn);
                        });
                    },
                    success: function(data) 
                    {
                        if(data.success)
                        {
                            window.location.href = btn.attr('data-url');
                        }
                        else
                        {
                            $('#ajsuform_yu').html('<div class="alert alert-danger">'+data.errors+'</div>');
                            KTApp.unprogress(btn);
                        }
                    }
                });
			}
		});
	}

	return {
		// public functions
		init: function() {
			wizardEl = KTUtil.get('kt_wizard_v1');
			formEl = $('#kt_form');

			initWizard(); 
			initValidation();
			initSubmit();
		}
	};
}();

jQuery(document).ready(function() {	
	KTWizard1.init();
});