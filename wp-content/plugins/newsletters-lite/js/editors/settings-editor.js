jQuery(document).ready(function() {	
	jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");
	if (typeof(postboxes) !== "undefined") { 		
		postboxes.add_postbox_toggles(pagenow); 
	}
});