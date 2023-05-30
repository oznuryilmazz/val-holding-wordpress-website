<?php // phpcs:ignoreFile ?>
<div id="tribulant_header_full">
	<div id="tribulant_header">
		<a id="tribulant_logo" href="https://tribulant.com" target="_blank">
			Tribulant Plugin
		</a>
	</div>
</div>
<div id="tribulant_content_full">
	<div id="tribulant_content">		
		<?php echo wp_kses_post( wp_unslash($changelog)) ?>
	</div>
</div>
<div id="tribulant_footer_full">
	<div id="tribulant_footer">
		<a href="https://tribulant.com" target="_blank">Tribulant - All rights reserved</a>
	</div>
</div>

<style type="text/css">
	*, html, body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, label, fieldset, input, p, blockquote, th, td{margin:0;padding:0}
	table{border-collapse:collapse;border-spacing:0}
	fieldset, img{border:0}
	address, caption, cite, code, dfn, em, strong, th, var{font-style:normal;font-weight:normal}
	ol, ul, li{list-style:none}
	caption, th{text-align:left}
	h1, h2, h3, h4, h5, h6{font-size:100%;font-weight:normal}
	strong{font-weight:bold}
	em{font-style:italic}
	a img{border:none}
	div { position:relative; }
	body { color:#666; font-size:14px; line-height:21px; font-family:Arial, sans-serif; }
	h3 { color:#213123; }
	#tribulant_header_full, #tribulant_footer_full, #tribulant_content_full { width:100%; clear:both; }
	#tribulant_header_full { background:url('<?php echo esc_url_raw( $this -> render_url('images/changelog-header.png', 'admin', false)); ?>') repeat; }
	#tribulant_header, #tribulant_footer, #tribulant_content { width:587px; padding:0 32px; }
	#tribulant_header { height:100px; }
	#tribulant_logo { background:url('<?php echo esc_url_raw( $this -> render_url('images/changelog-logo.png', 'admin', false)); ?>') no-repeat; text-indent:-9999px; width:251px; height:59px; position:absolute; top:23px; }
	#tribulant_content_full { padding-top:20px; position:relative;}
	#tribulant_content { position:relative; }
	h2 { font-size:21px; padding-bottom:18px; line-height:26px; width:100%; clear:both;}
	h3 { font-size:16px; padding-bottom:8px; color:#888; line-height:21px; width:100%; padding-right:10px; display:inline-block; z-index:9999; }
	#tribulant_content_full ul { width:100%; padding-bottom:15px; padding-right:10px; display:inline-block; z-index:9999; }
	#tribulant_content_full ul li { padding-left:5px; width:100%; line-height:26px; list-style-position:inside; list-style-type:disc; z-index:9999; }
	#tribulant_sidebar { width:175px; position:absolute; left:0; margin-left:440px; top:50px; z-index:1;}
	#tribulant_sidebar a { display:block; width:165px; color:#1780CD; line-height:32px; border-bottom:1px dotted #CCC; text-decoration:none; overflow:hidden; }
	#tribulant_sidebar a:hover { text-decoration:underline; }
	#tribulant_footer { text-align:center; font-size:12px; }
	#tribulant_footer a { color:#888; text-decoration:none; height:42px; line-height:42px; }
</style>