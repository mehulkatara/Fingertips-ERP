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

	/* Add or update record */
	function save(selector, action, form = null, alert = false) {
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
				url: jQuery(form).attr('action'),
				data: jQuery.extend(formData, data),
				success: function(response) {
					jQuery(selector).prop('disabled', false);
					if(response.success) {
						if(alert) {
							jQuery('.wl_im_container .alert').remove();
							jQuery('span.text-danger').remove();
							var alertBox = '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong><i class="fa fa-check"></i> &nbsp;' + response.data.message + '</strong></div>';
							jQuery(alertBox).insertBefore(form);
						}
						toastr.success(response.data.message);
						if (response.data.hasOwnProperty('reload') && response.data.reload) {
							location.reload();
						} else {
							jQuery(form)[0].reset();
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

	/* Action to add enquiry */
	save('.add-enquiry-submit', 'wl-im-add-enquiry', '#wlim-add-enquiry-form', true);

});