var request_getlistfields = false;
var request_subscribe = false;

jQuery(document).ready(function(){
	jQuery("input[id*=checkboxall]").click(function() {
		var checked_status = this.checked;
		jQuery("input[id*=checklist]").each(function() {
			this.checked = checked_status;
		});
	});
	
	jQuery("input[id*=checkinvert]").click(function() {	
		jQuery("input[id*=checklist]").each(function() {
			var status = this.checked;
			
			if (status == true) {
				this.checked = false;
			} else {
				this.checked = true;
			}
		});
	});
});

(function($) {
	
	// Open and closing of postboxes
	$('.newsletters .postbox .hndle').on('click', function() {
		//$(this).parent().toggleClass('closed');
	});
	
	// Hook into the "notice-my-class" class we added to the notice, so
	// Only listen to YOUR notices being dismissed
	$(document).on('click', '.notice-newsletters .notice-dismiss', function () {
		// Read the "data-notice" information to track which notice
		// is being dismissed and send it via AJAX
		var type = $(this).closest('.notice-newsletters').data('notice');
		
		// Make an AJAX call
		// Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.ajax(newsletters_ajaxurl + 'action=newsletters_dismissed_notice', {
			type: 'POST',
			data: {
				action: 'newsletters_dismissed_notice',
				type: type,
				security: newsletters.ajaxnonce.dismissed_notice
			}
		});
	});
})(jQuery);

function newsletters_get_country(link) {
	
	var subscriber_id = jQuery(link).data('subscriber-id');	
	var get_country_link = jQuery('#newsletters_subscriber_' + subscriber_id + '_get_country');
	var country_span = jQuery('#newsletters_subscriber_' + subscriber_id + '_country');
	
	get_country_link.html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
	
	if (typeof get_country_request !== 'undefined') {
		get_country_request.abort();
	}
	
	get_country_request = jQuery.ajax({
		url: newsletters_ajaxurl + 'action=newsletters_get_country&security=' + newsletters.ajaxnonce.get_country,
		data: {
			subscriber_id: subscriber_id, 
			security: newsletters.ajaxnonce.get_country
		},
		dataType: "json",
		type: 'POST',
		success: function(response) {			
			if (response.success == true) {
				country_span.html(response.flag);
				get_country_link.remove();
			} else {
				country_span.html('N/A');
				get_country_link.html('<i class="fa fa-question fa-fw"></i>');
			}
		}, 
		error: function(resposne) {
			get_country_link.html('<i class="fa fa-question fa-fw"></i>');
		}
	});
}

function newsletters_focus(element) {
	if (typeof jQuery(element) != "undefined" && jQuery(element).val() == "") {
		setTimeout(function() {
			jQuery(element).focus();
		}, 500);
	}
}

function newsletters_tinymce_content(contentid) {	
	if (jQuery("#wp-" + contentid + "-wrap").hasClass("tmce-active")) {		
		if (typeof(tinyMCE.activeEditor) == "object" && typeof(tinyMCE.activeEditor.getContent) == "function") {
			tinyMCE.triggerSave();
			return tinyMCE.activeEditor.getContent();
		}
	}
	
	return jQuery('#' + contentid).val();
}

function newsletters_tinymce_setcontent(content) {
	jQuery('.wp-editor-wrap .switch-tmce').trigger('click');	
	//if (typeof(tinyMCE.activeEditor) == "object" && tinyMCE.activeEditor.setContent != null && typeof(tinyMCE.activeEditor.setContent) == "function") {
	//	tinyMCE.activeEditor.setContent(content);
	//	tinyMCE.triggerSave();
	//}
	
	jQuery('textarea#content').html(content);
	return true;
}

function newsletters_change_filter(section, filter, value) {
	var expires;
	var date = new Date();
    date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
    expires = date.toGMTString();
    
    document.cookie = "newsletters_filter_" + section + "=1; expires=" + expires + "; path=/";
	document.cookie = "newsletters_filter_" + section + "_" + filter + "=" + value + "; expires=" + expires + "; path=/";
}

function hsl2rgb(hsl) {
    var h = hsl[0], s = hsl[1], l = hsl[2];
    var m1, m2, hue;
    var r, g, b
    h = (Math.round( 360*h )/1);
    if (s == 0)
        r = g = b = (l * 255);
    else {
        if (l <= 0.5)
            m2 = l * (s + 1);
        else
            m2 = l + s - l * s;
        m1 = l * 2 - m2;
        hue = h / 360;
        r = Math.round(HueToRgb(m1, m2, hue + 1/3));
        g = Math.round(HueToRgb(m1, m2, hue));
        b = Math.round(HueToRgb(m1, m2, hue - 1/3));
    }
    return {r: r, g: g, b: b};
}

function HueToRgb(m1, m2, hue) {
    var v;
    if (hue < 0)
        hue += 1;
    else if (hue > 1)
        hue -= 1;

    if (6 * hue < 1)
        v = m1 + (m2 - m1) * hue * 6;
    else if (2 * hue < 1)
        v = m2;
    else if (3 * hue < 2)
        v = m1 + (m2 - m1) * (2/3 - hue) * 6;
    else
        v = m1;

    return 255 * v;
}

function wpml_submitserial(form) {
	jQuery('#newsletters_submitserial_button').prop('disabled', true);
	jQuery('#wpml_submitserial_loading').show();
	var formdata = jQuery(form).serialize();

	jQuery.post(newsletters_ajaxurl + 'action=wpmlserialkey&security=' + newsletters.ajaxnonce.serialkey, formdata, function(response) {
		jQuery('#wpmlsubmitserial').html(response);
		jQuery.colorbox.resize();
	});
}

function wpml_deleteserial() {
	jQuery('#wpml_submitserial_loading').show();

	jQuery.post(newsletters_ajaxurl + 'action=wpmlserialkey&delete=1&security=' + newsletters.ajaxnonce.serialkey, false, function(response) {
		jQuery.colorbox.close(); parent.location.reload(1);
	});
}

function jqCheckAll(checker, formid, name) {					
	jQuery('input:checkbox[name="' + name + '[]"]').each(function() {
		jQuery(this).attr("checked", checker.checked);
	});
}

function wpml_scroll(selector) {
	var targetOffset = (jQuery(selector).offset().top - 50);
    jQuery('html,body').animate({scrollTop: targetOffset}, 500);
}

function newsletters_refreshfields(widgetid) {
	if (request_getlistfields) { request_getlistfields.abort(); }
	jQuery('#' + widgetid + '-loading').show();
	jQuery('#' + widgetid + '-button').prop('disabled', true);
	jQuery('#' + widgetid + ' .newsletters-fieldholder :input').attr('readonly', true);
	var formvalues = jQuery('#' + widgetid + '-form').serialize();
	
	request_getlistfields = jQuery.post(newsletters_ajaxurl + "action=wpmlgetlistfields&widget_id=" + widgetid + '&security=' + newsletters.ajaxnonce.refreshfields, formvalues, function(response) {		
		jQuery('#' + widgetid + '-loading').hide();
		jQuery('#' + widgetid + '-button').prop('disabled', false);
		jQuery('#' + widgetid + '-fields').html(response);
		jQuery('#' + widgetid + ' .newsletters-fieldholder :input').attr('readonly', false);
		
		$postpage = jQuery('.newsletters-management, .entry-content, .post-entry, .entry, .page-entry, .page-content');
		$divs = $postpage.find('#' + widgetid + '-form').find('.newsletters-fieldholder:not(.newsletters_submit, .hidden)');		
		for (var i = 0; i < $divs.length; i += 2) {
			$divs.slice(i, i + 2).wrapAll('<div class="row"></div>');
		}
		jQuery($divs).wrap('<div class="col-md-6"></div>');
	});
}

function wpml_titletoslug(title) {
	var title = title.toLowerCase();
	var slug = title.replace(/[^0-9a-z]+/g, "");
	jQuery('#Field_slug').val(slug);	
}

function wpml_tinymcetag(tag) {
	if (typeof(tinyMCE) == "object" && typeof(tinyMCE.execCommand) == "function" && tinyMCE.activeEditor) {
		if (window.tinyMCE && tag != "") {
			window.tinyMCE.execCommand('mceInsertContent', false, tag);	
		}
	} else {
		jQuery('textarea#content').text(jQuery('textarea#content').val() + '\n\n' + tag);
	}
	
	wpml_scroll('#wp-content-editor-container');
}