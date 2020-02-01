jQuery(document).ready(function() {

	/* Copy target content to clipboard on click */
	function copyToClipboard(selector, target) {
		jQuery(document).on('click', selector, function() {
			var value = jQuery(target).text();
			var temp = jQuery("<input>");
			jQuery("body").append(temp);
			temp.val(value).select();
			document.execCommand("copy");
			temp.remove();
			toastr.success('Copied to clipboard.');
		});
	}

	/* Reset form on open modal */
	function resetFormOnOpenModal(modal, form, refresh = false, target = null) {
		jQuery(document).on('show.bs.modal', modal, function() {
			jQuery(form)[0].reset();
			jQuery('span.text-danger').remove();
			if(refresh) {
				jQuery(target).load(location.href + " " + target, function() {
					/* Select single option */
					try {
						jQuery('.selectpicker').selectpicker({
							liveSearch: true
						});
					} catch (error) {
					}
				});
			}
		});
	}

	/* Append modal to body */
	function appendModalToBody(modal) {
		jQuery(modal).appendTo("body");
	}

	copyToClipboard('#wl_im_enquiry_form_shortcode_copy', '#wl_im_enquiry_form_shortcode');

	resetFormOnOpenModal('#add-administrator', '#wlim-add-administrator-form');
	resetFormOnOpenModal('#add-course', '#wlim-add-course-form');
	resetFormOnOpenModal('#add-enquiry', '#wlim-add-enquiry-form',  true, '.wlim-add-enquiry-form-fields');
	resetFormOnOpenModal('#add-student', '#wlim-add-student-form', true, '.wlim-add-student-form-fields');
	resetFormOnOpenModal('#add-installment', '#wlim-add-installment-form', true, '.wlim-add-installment-form-fields');
	resetFormOnOpenModal('#add-batch', '#wlim-add-batch-form', true, '.wlim-selectpicker');

	appendModalToBody('#add-administrator');
	appendModalToBody('#update-administrator');
	appendModalToBody('#add-course');
	appendModalToBody('#update-course');
	appendModalToBody('#add-batch');
	appendModalToBody('#update-batch');
	appendModalToBody('#add-enquiry');
	appendModalToBody('#update-enquiry');
	appendModalToBody('#add-student');
	appendModalToBody('#update-student');
	appendModalToBody('#add-installment');
	appendModalToBody('#update-installment');

	/* Select single option */
	jQuery('.selectpicker').selectpicker({
		liveSearch: true
	});
});