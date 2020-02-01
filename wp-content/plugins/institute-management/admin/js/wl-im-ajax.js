jQuery(document).ready(function() {

	/* Serialize object */
	(function($,undefined){
	  '$:nomunge';
	  $.fn.serializeObject = function(){
	    var obj = {};
	    $.each( this.serializeArray(), function(i,o){
	      var n = o.name,
	        v = o.value;
	        obj[n] = obj[n] === undefined ? v
	          : $.isArray( obj[n] ) ? obj[n].concat( v )
	          : [ obj[n], v ];
	    });
	    return obj;
	  };
	})(jQuery);

	/* Get data to display on table */
	function initializeDatatable(table, action) {
		jQuery(table).DataTable({
	        aaSorting: [],
	        responsive: true,
			ajax: {
				url: ajaxurl + '?security=' + WLIMAjax.security + '&action=' + action,
	            dataSrc: 'data'
			},
			language: {
				"loadingRecords": "Loading..."
			}
		});
	}

	/* Add or update record */
	function save(selector, action, form = null, modal = null, reloadTables = []) {
		jQuery(document).on('click', selector, function(event) {
			var data = {
				action: action,
				data: form ? jQuery(form).serializeArray() : []
			};
			var formData = {};
			if(form) {
				formData = jQuery(form).serializeObject();
			}
			jQuery(selector).prop('disabled', true);
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: jQuery.extend(formData, data),
				success: function(response) {
					jQuery(selector).prop('disabled', false);
					if(response.success) {
						toastr.success(response.data.message);
						if (response.data.hasOwnProperty('reload') && response.data.reload) {
							location.reload();
						} else {
							jQuery(form)[0].reset();
							if(modal) {
								jQuery(modal).modal('hide');
							}
							reloadTables.forEach(function(table) {
								jQuery(table).DataTable().ajax.reload(null, false);
							});
						}
					} else {
						jQuery('span.text-danger').remove();
						if(response.data && jQuery.isPlainObject(response.data)) {
							jQuery(form + ' :input').each(function() {
								var input = this;
								if(response.data[input.name]) {
									var errorSpan = '<span class="text-danger">' + response.data[input.name] + '</span>';
									jQuery(errorSpan).insertAfter(this);
								}
							});
						} else {
							var errorSpan = '<span class="text-danger">' + response.data + '<hr></span>';
							jQuery(errorSpan).insertBefore(form);
						}
					}
				},
				error: function(response) {
					jQuery(selector).prop('disabled', false);
					toastr.error(response.statusText);
				},
				dataType: 'json'
			});
		});
	}

	/* Fetch record to update */
	function fetch(modal, action, target) {
		jQuery(modal).on('show.bs.modal', function (e) {
			var id = jQuery(e.relatedTarget).data('id');
			jQuery.ajax({
				type : 'post',
				url : ajaxurl + '?security=' + WLIMAjax.security + '&action=' + action,
				data :  'id='+ id,
				success : function(data) {
					jQuery(target).html(data);
				}
			});
		});
	}

	/* Delete record */
	function remove(selector, id_attribute, nonce_attribute, nonce_name, action, reloadTables = []) {
		jQuery(document).on("click", selector, function (event) {
			var id = jQuery(this).attr(id_attribute);
			var nonce = jQuery(this).attr(nonce_attribute);
			jQuery.confirm({
			    title: 'Confirm!',
			    content: 'Please confirm!',
			    buttons: {
			        confirm: function () {
						jQuery.ajax({
							data: "id=" + id + "&" + nonce_name + "-" + id + "=" + nonce + "&action=" + action,
							url: ajaxurl,
							type: "POST",
							success: function(response) {
								if(response.success) {
									toastr.success(response.data.message);
									reloadTables.forEach(function(table) {
										jQuery(table).DataTable().ajax.reload(null, false);
									});
								} else {
									toastr.error(response.data);
								}
							},
							error: function(response) {
								toastr.error(response.statusText);
							}
						});
			        },
			        cancel: function () {
			        }
			    }
			});
		});
	}

	/* Fetch records */
	function fetchRecords(action, target, data = null) {
		jQuery.ajax({
			type : 'post',
			url : ajaxurl + '?security=' + WLIMAjax.security + '&action=' + action,
			data :  data,
			success : function(data) {
				jQuery(target).html(data);
			}
		});
	}

	/* Actions for administrator */
	initializeDatatable('#administrator-table', 'wl-im-get-administrator-data');
	save('.add-administrator-submit', 'wl-im-add-administrator', '#wlim-add-administrator-form', '#add-administrator', ['#administrator-table']);
	fetch('#update-administrator', 'wl-im-fetch-administrator', '#fetch_administrator');
	save('.update-administrator-submit', 'wl-im-update-administrator', '#wlim-update-administrator-form', '#update-administrator', ['#administrator-table']);

	/* Actions for course */
	initializeDatatable('#course-table', 'wl-im-get-course-data');
	save('.add-course-submit', 'wl-im-add-course', '#wlim-add-course-form', '#add-course', ['#course-table']);
	fetch('#update-course', 'wl-im-fetch-course', '#fetch_course');
	save('.update-course-submit', 'wl-im-update-course', '#wlim-update-course-form', '#update-course', ['#course-table']);
	remove('.delete-course', 'delete-course-id', 'delete-course-security', 'delete-course', 'wl-im-delete-course', ['#course-table']);

	/* Actions for batch */
	initializeDatatable('#batch-table', 'wl-im-get-batch-data');
	save('.add-batch-submit', 'wl-im-add-batch', '#wlim-add-batch-form', '#add-batch', ['#batch-table']);
	fetch('#update-batch', 'wl-im-fetch-batch', '#fetch_batch');
	save('.update-batch-submit', 'wl-im-update-batch', '#wlim-update-batch-form', '#update-batch', ['#batch-table']);
	remove('.delete-batch', 'delete-batch-id', 'delete-batch-security', 'delete-batch', 'wl-im-delete-batch', ['#batch-table']);

	/* Actions for enquiry */
	initializeDatatable('#enquiry-table', 'wl-im-get-enquiry-data');
	save('.add-enquiry-submit', 'wl-im-add-enquiry', '#wlim-add-enquiry-form', '#add-enquiry', ['#enquiry-table']);
	fetch('#update-enquiry', 'wl-im-fetch-enquiry', '#fetch_enquiry');
	save('.update-enquiry-submit', 'wl-im-update-enquiry', '#wlim-update-enquiry-form', '#update-enquiry', ['#enquiry-table']);
	remove('.delete-enquiry', 'delete-enquiry-id', 'delete-enquiry-security', 'delete-enquiry', 'wl-im-delete-enquiry', ['#enquiry-table']);

	/* Actions for student */
	initializeDatatable('#student-table', 'wl-im-get-student-data');
	save('.add-student-submit', 'wl-im-add-student', '#wlim-add-student-form', '#add-student', ['#student-table']);
	fetch('#update-student', 'wl-im-fetch-student', '#fetch_student');
	save('.update-student-submit', 'wl-im-update-student', '#wlim-update-student-form', '#update-student', ['#student-table']);
	remove('.delete-student', 'delete-student-id', 'delete-student-security', 'delete-student', 'wl-im-delete-student', ['#student-table']);

	/* Fetch student enquiries */
	jQuery(document).on('change', '#wlim-student-from_enquiry', function() {
		jQuery('span.text-danger').remove();
		if(this.checked) {
			fetchRecords('wl-im-add-student-fetch-enquiries', '#wlim-add-student-from-enquiries');
		} else {
			jQuery('#wlim-add-student-from-enquiries').html("");
			fetchRecords('wl-im-add-student-form', '#wlim-add-student-form-fields');
		}
	});

	/* Fetch student course batches */
	jQuery(document).on('change', '#wlim-student-course', function() {
		jQuery('span.text-danger').remove();
		if(this.value) {
			var data = 'id='+ this.value;
			fetchRecords('wl-im-add-student-fetch-course-batches', '#wlim-add-student-course-batches', data);
		} else {
			jQuery('#wlim-add-student-course-batches').html("");
		}
	});
	jQuery(document).on('change', '#wlim-student-course_update', function() {
		jQuery('span.text-danger').remove();
		if(this.value) {
			var batch_id = jQuery(this).data('batch_id');
			var data = 'id='+ this.value + '&batch_id=' + batch_id;
			fetchRecords('wl-im-add-student-fetch-course-update-batches', '#wlim-add-student-course-update-batches', data);
		} else {
			jQuery('#wlim-add-student-course-update-batches').html("");
		}
	});

	/* Fetch student enquiry */
	jQuery(document).on('change', '#wlim-student-enquiry', function() {
		var data = null;
		if(this.value) {
			data = 'id='+ this.value;
		}
		fetchRecords('wl-im-add-student-fetch-enquiry', '#wlim-add-student-form-fields', data);
	});

	/* Fetch student fees payable */
	jQuery(document).on('change', '#wlim-student-course', function() {
		var data = null;
		if(this.value) {
			data = 'id='+ this.value;
		}
		fetchRecords('wl-im-add-student-fetch-fees-payable', '#wlim-add-student-fetch-fees-payable', data);
	});

	/* Actions for fee */
	initializeDatatable('#installment-table', 'wl-im-get-installment-data');
	save('.add-installment-submit', 'wl-im-add-installment', '#wlim-add-installment-form', '#add-installment', ['#installment-table']);
	fetch('#update-installment', 'wl-im-fetch-installment', '#fetch_installment');
	save('.update-installment-submit', 'wl-im-update-installment', '#wlim-update-installment-form', '#update-installment', ['#installment-table']);
	remove('.delete-installment', 'delete-installment-id', 'delete-installment-security', 'delete-installment', 'wl-im-delete-installment', ['#installment-table']);

	/* Fetch student fees */
	jQuery(document).on('change', '#wlim-installment-student', function() {
		var data = null;
		if(this.value) {
			data = 'id='+ this.value;
			fetchRecords('wl-im-add-installment-fetch-fees', '#wlim_add_installment_fetch_fees', data);
		}
	});

});