/* global jQuery, woocommerce_admin_system_status, wcSetClipboard, wcClearClipboard */
jQuery( function ( $ ) {

	/**
	 * Users country and state fields
	 */
	var win;
	var wcSystemStatus = {
		init: function() {
			$( document.body )
				.on( 'click', 'a.help_tip, a.woocommerce-help-tip', this.preventTipTipClick )
				.on( 'click', 'a.debug-report', this.generateReport )
				.on( 'click', '#copy-for-support', this.copyReport )
				.on( 'change', '#envato_activate_checkbox', this.buttonTigger )
				$(document).on('aftercopy',  this.copySuccess );
				$(document).on('aftercopyfailure',  this.copyFail );
		},

		/**
		 * Prevent anchor behavior when click on TipTip.
		 *
		 * @return {Bool}
		 */
		preventTipTipClick: function() {
			return false;
		},

		/**
		 * Generate system status report.
		 *
		 * @return {Bool}
		 */
		generateReport: function() {
			var report = '';

			$( '.'+envato_theme_systemerrorshow.table_class+' thead, .'+envato_theme_systemerrorshow.table_class+' tbody' ).each( function() {
				if ( $( this ).is( 'thead' ) ) {
					var label = $( this ).find( 'th:eq(0)' ).data( 'export-label' ) || $( this ).text();
					report = report + '\n---- ' + $.trim( label ) + ' ----\n\n';
				} else {
					$( 'tr', $( this ) ).each( function() {
						var label       = $( this ).find( 'td:eq(0)' ).data( 'export-label' ) || $( this ).find( 'td:eq(0)' ).text();
						var the_name    = $.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML.

						// Find value
						var $value_html = $( this ).find( 'td:eq(2)' ).clone();
						$value_html.find( '.private' ).remove();
						$value_html.find( '.dashicons-yes' ).replaceWith( '&#10004;' );
						$value_html.find( '.dashicons-no-alt, .dashicons-warning' ).replaceWith( '&#10060;' );

						// Format value
						var the_value   = $.trim( $value_html.text() );
						var value_array = the_value.split( ', ' );

						if ( value_array.length > 1 ) {
							// If value have a list of plugins ','.
							// Split to add new line.
							var temp_line ='';
							$.each( value_array, function( key, line ) {
								temp_line = temp_line + line + '\n';
							});

							the_value = temp_line;
						}

						report = report + '' + the_name + ': ' + the_value + '\n';
					});
				}
			});

			try {
				$( '#debug-report' ).slideDown();
				$( '#debug-report' ).find( 'textarea' ).val( '`' + report + '`' ).focus().select();
				$( this ).fadeOut();
				return false;
			} catch ( e ) {
				/* jshint devel: true */
				console.log( e );
			}

			return false;
		},

		/**
		 * Copy for report.
		 *
		 * @param {Object} evt Copy event.
		 */
		copyReport: function( evt ) {
			$( '#debug-report' ).find( 'textarea' ).select();
			var copytext=document.execCommand('copy');
			if(copytext){
				
				$( document.body ).trigger( "aftercopy" );
			}else{
				$( document.body ).trigger( "aftercopyfailure" );
			}
			evt.preventDefault();
		},

		/**
		 * Display a "Copied!" tip when success copying
		 */
		copySuccess: function() {
			$("#copy-for-support").attr('title',ajax_dashboard_js.copytext) 
			$("#copy-for-support").tooltip({ 
				hide: {effect: "explode",delay: 250}
			});
			$("#copy-for-support").trigger('mouseenter');
			setTimeout(function(){ 
				
				$("#copy-for-support").tooltip('destroy')
				$("#copy-for-support").attr('title','');
			}, 5000);
		},

		/**
		 * Displays the copy error message when failure copying.
		 */
		copyFail: function() {
			$( '.copy-error' ).removeClass( 'hidden' );
			$( '#debug-report' ).find( 'textarea' ).focus().select();
		},
		buttonTigger: function(e){
			if($(e.target).is(":checked")) {
				$('.envato-liccence-button-tr').show();
			}else{
				$('.envato-liccence-button-tr').hide();
			}
		}
	};

	wcSystemStatus.init();

	$( '#log-viewer-select' ).on( 'click', 'h2 a.page-title-action', function( evt ) {
		evt.stopImmediatePropagation();
		return window.confirm( woocommerce_admin_system_status.delete_log_confirmation );
	});
	
	if(typeof(envato_theme_systemerrorshow)!='undefined'){
        if(envato_theme_systemerrorshow.count>0){
            $('.nav-tab-wrapper .nav-tab-active').append('<span class="systemerrorshow_notification"><div class="systemerrorshow_notification_count">'+envato_theme_systemerrorshow.count+'</div></span>')
        }
    }


	$(document ).on( 'click', '.envato_theme_theme_license_activate_envato', function( evt ) {
		var $this=$(this);
		$this.addClass('active');
		setTimeout(()=> {
			$this.addClass('loader');
		}, 125);
		var url=$(this).data('url')
		win=window.open ('about:blank',"enlicencewindow", "width=500,height=500,status");
		win.location.href=url;
		var timer = setInterval(function() { 
			if(win.closed) {
				clearInterval(timer);
				$this.removeClass("loader active");
				$this.addClass('animated pulse');
			}
		  }, 2000);
		
	});

	$(document ).on( 'click', '.envato_theme_theme_license_deactivate_envato', function( evt ) {
		var $this=$(this);
		$this.addClass('active');
		setTimeout(()=> {
			$this.addClass('loader');
		}, 125);
		var $url=$(this).data('url');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajax_dashboard_js.ajax_url,
			data: {
				action: 'delete_api_key',
				url: $url,
			},
			success: function (data) {
				console.log(data)
				$('.ajax_massage p').text(data.message)
				if(data.status){
					setTimeout(function(){
						window.location.reload()
					}, 2000)
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus)
				console.log(errorThrown)
				console.log(55)
			}
		});
	});
	
	window.addEventListener('message', function(e) {
		// e.data hold the message from child
		//if(e.origin !=ajax_dashboard_js.liecence_endpoint){ 
		if(e.origin !="https://my.smartdatasoft.com"){ 
			console.log("false")
			return false
		}
		const obj =e.data;
		if(obj.success){
			$.ajax({
				type: "POST",
				dataType: "json",
				url: ajax_dashboard_js.ajax_url,
				data: {
					action: 'save_api_key',
					api: obj.apikeys,
					key: obj.key,
					status: obj.status,
				},
				success: function (data) {
					window.location.reload()
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.log(textStatus)
					console.log(errorThrown)
					console.log(55)
				}
			});
		}
		
	} , false);

});
