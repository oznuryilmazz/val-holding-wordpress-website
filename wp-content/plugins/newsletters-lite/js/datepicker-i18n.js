jQuery(document).ready(function() {
	jQuery.datepicker.setDefaults({
	    showButtonPanel: true,
	    closeText: objectL10n.closeText,
	    currentText: objectL10n.currentText,
	    monthNames: objectL10n.monthNames,
	    monthNamesShort: objectL10n.monthNamesShort,
	    dayNames: objectL10n.dayNames,
	    dayNamesShort: objectL10n.dayNamesShort,
	    dayNamesMin: objectL10n.dayNamesMin,
	    dateFormat: objectL10n.dateFormat,
	    firstDay: objectL10n.firstDay,
	    isRTL: objectL10n.isRTL,
	});
});