jQuery(document).ready(function() {
	jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");
	jQuery('.hide-if-js').removeClass('hide-if-js');
	if (typeof(postboxes) !== "undefined") { 
		postboxes.add_postbox_toggles(pagenow);
	}
});