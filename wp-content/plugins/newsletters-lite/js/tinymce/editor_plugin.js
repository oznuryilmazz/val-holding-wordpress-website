/**
 * Newsletters TinyMCE Plugin
 * @author Tribulant
 */

(function() {	
	tinymce.PluginManager.add('Newsletters', function(editor, url) {
	
		var self = this, post_element;
	
		function post_change_category(category_id) {			
			tinyMCE.activeEditor.plugins.Newsletters.refresh([{text:'loading...', value:'loading'}], true);
			
			jQuery.post(newsletters_ajaxurl + 'action=newsletters_posts_by_category&security=' + $ajaxnonce_posts_by_category + '&cat_id=' + category_id, {category:category_id}, function(response) {				
				tinyMCE.activeEditor.plugins.Newsletters.refresh(response, false);	
			});
			
			return true;
		}
		
		self.refresh = function(values, disabledstate) {						
			if (typeof values[1] == 'undefined') {
				disabledstate = true;	
			}
			
			if (post_element.menu) {
				post_element.menu.remove();
			}
			
			post_element.menu = null;
			post_element.state.data.menu = post_element.settings.values = post_element.settings.menu = values;
			post_element.disabled(disabledstate);
			post_element.value(values[0]['value']);
			post_element.focus();
		}
		
		var buttonmenu = [{
					text: tinymce.settings.newsletters_anchor_link_menu,
					onclick: function() {
						editor.windowManager.open({
							title: tinymce.settings.newsletters_anchor_link_title,
							body: [{
								type: 'textbox',
								name: 'newsletters_anchor_text',
								label: tinymce.settings.newsletters_anchor_link_label,
								tooltip: tinymce.settings.newsletters_anchor_link_tooltip
							}],
							onsubmit: function(e) {
								if (e.data.newsletters_anchor_text.length > 0) {
									editor.insertContent('<a name="' + e.data.newsletters_anchor_text + '"></a>');
								} else {
									alert(tinymce.settings.newsletters_anchor_link_error);
									return false;
								}
							}
						});
					}
				}, {
					text: "Subscribe Form",
					onclick: function() {
						var mailinglists = [];
						var index;
						for (index = 0; index < tinymce.settings.newsletters_mailinglists_list.length; index++) {
							mailinglists.push(tinymce.settings.newsletters_mailinglists_list[index]);
						}
						
						var subscribeforms = [];
						for (index = 0; index < tinymce.settings.newsletters_subscribeforms.length; index++) {
							subscribeforms.push(tinymce.settings.newsletters_subscribeforms[index]);
						}
					
						editor.windowManager.open({
							title: 'Insert Subscribe Form',
							body: [{
								type: 'listbox',
								name: 'newsletters_subscribe_list',
								label: 'Form Type',
								values: mailinglists,
								tooltip: 'Either multiple (select drop down or checkboxes list), specific mailing list or a subscribe form.',
								onSelect: function() {
									if (this.value() == "select" || this.value() == "checkboxes" || this.value() == "all") {
										subscribeinclude_element.show();
										subscribeform_element.hide();
									} else if (this.value() == "form") {
										subscribeform_element.show();
									} else {
										subscribeinclude_element.hide();
										subscribeform_element.hide();
									}
								}
							}, {
								type: 'listbox',
								name: 'newsletters_subscribeform',
								label: 'Subscribe Form',
								values: subscribeforms,
								tooltip: 'Choose the subscribe form to use.',
								onPostRender: function() {
									subscribeform_element = this;
								}
							}, {
								type: 'textbox',
								name: 'newsletters_subscribe_include',
								label: 'Include',
								tooltip: 'Optional. When using multiple, you can specify a comma separated list of mailing list IDs to show',
								onPostRender: function() {
									subscribeinclude_element = this;
								}
							}],
							onsubmit: function(e) {
								var newsletters_subscribe = '[newsletters_subscribe';
								if (e.data.newsletters_subscribe_list == "form") {
									newsletters_subscribe += ' form=' + e.data.newsletters_subscribeform + '';
								} else {
									if (e.data.newsletters_subscribe_include.length > 0) {
										newsletters_subscribe += ' lists="' + e.data.newsletters_subscribe_include + '"';
									}
									newsletters_subscribe += ' list="' + e.data.newsletters_subscribe_list + '"';
								}
								newsletters_subscribe += ']';
								editor.insertContent(newsletters_subscribe);
							}
						});
					}
				}, {	// Subscribe Link
					text: 'Subscribe Link',
					onclick: function() {					
						editor.windowManager.open({
							title: 'Insert Subscribe Link',
							body: [{
								type: 'textbox',
								name: 'newsletters_subscribe_link_text',
								label: 'Link Text',
								values: false
							}, {
								type: 'textbox',
								name: 'newsletters_subscribe_link_list',
								label: 'Mailing List/s',
								values: false,
								tooltip: 'Mailing list ID or comma separated list',
							}],
							onsubmit: function(e) {								
								var newsletters_subscribe = '[newsletters_subscribe_link';
								newsletters_subscribe += ' list="' + e.data.newsletters_subscribe_link_list + '"]';
								newsletters_subscribe += e.data.newsletters_subscribe_link_text + '[/newsletters_subscribe_link]';
								editor.insertContent(newsletters_subscribe);
							}
						});
					}
				}, {
					text: "Single Post",
					onclick: function() {
						var newsletters_post_body = [];
						
						if (typeof(tinymce.settings.newsletters_languages) !== 'undefined' && tinymce.settings.newsletters_languages.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_language',
								label: 'Language',
								values: tinymce.settings.newsletters_languages,
								tooltip: 'Choose the language of the post to use'
							});
						}
						
						newsletters_post_body.push({
							type: 'checkbox',
							name: 'newsletters_post_showdate',
							label: 'Show Date',
							text: 'Yes, show the post date',
							tooltip: 'Choose whether or not to show the date of the post'
						});
						

						newsletters_post_body.push({
							type: 'checkbox',
							name: 'newsletters_post_hidethumbnail',
							label: 'Hide Thumbnail',
							text: 'Yes, Hide the thumbnail',
							tooltip: 'Choose whether or not to hide the thumbnail of the post'
						});

						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_eftype',
							label: 'Type',
							values: [{text:'Excerpt', value:'excerpt'}, {text:'Full Post', value:'full'}],
							tooltip: 'Either full post or excerpt'
						});
						
						if (tinymce.settings.newsletters_thumbnail_sizes.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_thumbnail_size',
								label: 'Thumbnail Size',
								values: tinymce.settings.newsletters_thumbnail_sizes,
								tooltip: 'Choose the size of the thumbnail'
							});
						}
						
						if (tinymce.settings.newsletters_thumbnail_align.length > 0) {
							newsletters_post_body.push({
								type: 'listbox',
								name: 'newsletters_post_thumbnail_align',
								label: 'Thumbnail Align',
								values: tinymce.settings.newsletters_thumbnail_align,
								tooltip: 'Choose the alignment of the thumbnail'
							});
						}
						
						newsletters_post_body.push({
							type: 'textbox',
							name: 'newsletters_post_thumbnail_hspace',
							label: 'Thumbnail Space',
							value: '15',
							tooltip: 'The spacing of the thumbnail',
						});
						
						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_category',
							label: 'Category',
							values: tinymce.settings.newsletters_post_categories,
							tooltip: 'Choose a category to populate the posts below in order to choose a post',
							onSelect: function(e) {								
								post_change_category(this.value());
								this.value(null);
							}
						});
						
						newsletters_post_body.push({
							type: 'listbox',
							name: 'newsletters_post_id',
							label: 'Post',
							values: [{text:'- Choose Category Above -', value:false}],
							tooltip: 'First choose a category above, then choose the post to insert',
							onPostRender: function() {
								post_element = this;
							}
						});
					
						editor.windowManager.open({
							title: 'Insert Single Post',
							body: newsletters_post_body,
							onsubmit: function(e) {
								var newsletters_post = '[newsletters_post';
								
								if (e.data.newsletters_post_showdate == true) {
									newsletters_post += ' showdate="Y"';
								} else {
									newsletters_post += ' showdate="N"';
								}

								if (e.data.newsletters_post_hidethumbnail == true) {
									newsletters_post += ' hidethumbnail="Y"';
								} else {
									newsletters_post += ' hidethumbnail="N"';
								}

								
								newsletters_post += ' eftype="' + e.data.newsletters_post_eftype + '"';
								newsletters_post += ' post_id="' + e.data.newsletters_post_id + '"';
								newsletters_post += ' thumbnail_size="' + e.data.newsletters_post_thumbnail_size + '"';
								newsletters_post += ' thumbnail_align="' + e.data.newsletters_post_thumbnail_align + '"';
								newsletters_post += ' thumbnail_hspace="' + e.data.newsletters_post_thumbnail_hspace + '"';
								
								if (e.data.newsletters_post_id == false || e.data.newsletters_post_id.length <= 0) {
									alert('Choose a post');
									return false;
								}
								
								if (typeof(e.data.newsletters_post_language) !== 'undefined' && e.data.newsletters_post_language.length > 0) {
									newsletters_post += ' language="' + e.data.newsletters_post_language + '"';
								}
								
								newsletters_post += ']';
								editor.insertContent(newsletters_post);
							}
						});
					}
				}, {
					text: "Multiple Posts",
					onclick: function() {
						var newsletters_posts_body = [];
						
						if (typeof(tinymce.settings.newsletters_languages) !== 'undefined' && tinymce.settings.newsletters_languages.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_language',
								label: 'Language',
								values: tinymce.settings.newsletters_languages,
								tooltip: 'Choose the language of the posts to use'
							});
						}
						
						newsletters_posts_body.push({
							type: 'textbox',
							name: 'newsletters_posts_number',
							label: 'Number',
							tooltip: 'Optional. Choose the number of posts to show'
						});
						
						newsletters_posts_body.push({
							type: 'checkbox',
							name: 'newsletters_posts_showdate',
							label: 'Show Date',
							text: 'Yes, show the post date',
							tooltip: 'Choose whether or not to show the date of the post'
						});

						newsletters_posts_body.push({
							type: 'checkbox',
							name: 'newsletters_posts_hidethumbnail',
							label: 'Hide Thumbnail',
							text: 'Yes, Hide the thumbnail',
							tooltip: 'Choose whether or not to hide the thumbnail of the post'
						});

						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_eftype',
							label: 'Type',
							values: [{text:'Excerpt', value:'excerpt'}, {text:'Full Post', value:'full'}],
							tooltip: 'Either full post or excerpt'
						});

						if (tinymce.settings.newsletters_thumbnail_sizes.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_thumbnail_size',
								label: 'Thumbnail Size',
								values: tinymce.settings.newsletters_thumbnail_sizes,
								tooltip: 'Choose the size of the thumbnail'
							});
						}
						
						if (tinymce.settings.newsletters_thumbnail_align.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_thumbnail_align',
								label: 'Thumbnail Align',
								values: tinymce.settings.newsletters_thumbnail_align,
								tooltip: 'Choose the alignment of the thumbnail'
							});
						}
						
						newsletters_posts_body.push({
							type: 'textbox',
							name: 'newsletters_posts_thumbnail_hspace',
							label: 'Thumbnail Space',
							value: '15',
							tooltip: 'The spacing of the thumbnail',
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_orderby',
							label: 'Order By',
							values: tinymce.settings.newsletters_posts_orderby_values,
							tooltip: 'Choose by what value posts should be ordered'
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_order',
							label: 'Order',
							values: [{text:'Ascending', value:'ASC'}, {text:'Descending', value:'DESC'}],
							tooltip: 'Choose in what direction posts should be ordered'
						});
						
						newsletters_posts_body.push({
							type: 'listbox',
							name: 'newsletters_posts_category',
							label: 'Category',
							values: tinymce.settings.newsletters_posts_categories,
							tooltip: 'Choose the category to take posts from'
						});
						
						if (tinymce.settings.newsletters_post_types.length > 0) {
							newsletters_posts_body.push({
								type: 'listbox',
								name: 'newsletters_posts_posttype',
								label: 'Post Type',
								values: tinymce.settings.newsletters_post_types,
								tooltip: 'Optional. Choose a custom post type to take posts from'
							});
						}
					
						editor.windowManager.open({
							title: 'Insert Multiple Posts',
							body: newsletters_posts_body,
							onsubmit: function(e) {								
								var newsletters_posts = '[newsletters_posts';
								
								if (typeof(e.data.newsletters_posts_language) !== 'undefined' && e.data.newsletters_posts_language.length > 0) {
									newsletters_posts += ' language="' + e.data.newsletters_posts_language + '"';
								}
								
								if (e.data.newsletters_posts_number.length > 0) {
									newsletters_posts += ' numberposts="' + e.data.newsletters_posts_number + '"';
								}
								
								if (e.data.newsletters_posts_showdate == true) {
									newsletters_posts += ' showdate="Y"';
								} else {
									newsletters_posts += ' showdate="N"';
								}

								if (e.data.newsletters_posts_hidethumbnail == true) {
									newsletters_posts += ' hidethumbnail="Y"';
								} else {
									newsletters_posts += ' hidethumbnail="N"';
								}

								
								newsletters_posts += ' eftype="' + e.data.newsletters_posts_eftype + '"';
								newsletters_posts += ' orderby="' + e.data.newsletters_posts_orderby + '"';
								newsletters_posts += ' order="' + e.data.newsletters_posts_order + '"';
								newsletters_posts += ' thumbnail_size="' + e.data.newsletters_posts_thumbnail_size + '"';
								newsletters_posts += ' thumbnail_align="' + e.data.newsletters_posts_thumbnail_align + '"';
								newsletters_posts += ' thumbnail_hspace="' + e.data.newsletters_posts_thumbnail_hspace + '"';
								
								if (e.data.newsletters_posts_category == false || e.data.newsletters_posts_category.length <= 0) {									
									e.data.newsletters_posts_category = "";
								}
								
								newsletters_posts += ' category="' + e.data.newsletters_posts_category + '"';
								
								if (tinymce.settings.newsletters_post_types.length > 0) {
									if (e.data.newsletters_posts_posttype.length > 0) {
										newsletters_posts += ' post_type="' + e.data.newsletters_posts_posttype + '"';
									}
								}
								
								newsletters_posts += ']';
								editor.insertContent(newsletters_posts);
							}
						});
					}
				}, {
					text: "Featured Image",
					onclick: function() {
						editor.windowManager.open({
							title: 'Insert Featured Image',
							body: [{
								type: 'textbox',
								name: 'newsletters_thumbnail_postid',
								label: 'Post ID',
								value: tinymce.settings.newsletters_post_id,
								tooltip: 'Specify the ID of the post to take the featured image from'
							}, {
								type: 'listbox',
								name: 'newsletters_thumbnail_size',
								label: 'Size',
								values: tinymce.settings.newsletters_thumbnail_sizes,
								tooltip: 'Choose the size of the image to show. Sizes can be configured under Settings > Media in your dashboard'
							}],
							onsubmit: function(e) {
								if (e.data.newsletters_thumbnail_size.length > 0) {
									var newsletters_thumbnail = '[newsletters_post_thumbnail';
									
									if (e.data.newsletters_thumbnail_postid.length > 0) {
										newsletters_thumbnail += ' post_id="' + e.data.newsletters_thumbnail_postid + '"';
									}
									
									newsletters_thumbnail += ' size="' + e.data.newsletters_thumbnail_size + '"';
									newsletters_thumbnail += ']';
									
									editor.insertContent(newsletters_thumbnail);
								} else {
									alert('Please choose an image size');
									return false;
								}
							}
						});
					}
				}, {
					text: "Email History",
					onclick: function() {
						var newsletters_history_body = [{
							type: 'textbox',
							name: 'newsletters_history_number',
							label: 'Number',
							tooltip: 'Specify the number of emails/newsletters to display. Leave empty for all.'
						}, {
							type: 'listbox',
							name: 'newsletters_history_orderby',
							label: 'Order By',
							values: [{text:'Date', value:'modified'}, {text:'Subject', value:'subject'}, {text:'Times Sent', value:'sent'}]
						}, {
							type: 'listbox',
							name: 'newsletters_history_order',
							label: 'Order',
							values: [{text:'Descending (new to old/Z to A/Large to Small)', value:'DESC'}, {text:'Ascending (old to new/A to Z/Small to Large)', value:'ASC'}]
						}, {
							type: 'textbox',
							name: 'newsletters_history_lists',
							label: 'Mailing List/s',
							values: tinymce.settings.newsletters_mailinglists_list,
							tooltip: 'Leave empty for all else fill in comma separated list IDs'
						}, {
							type: 'checkbox',
							name: 'newsletters_history_linksonly',
							label: 'Show Links Only',
							tooltip: 'Turn this on to only show links and not full newsletters',
						}];
					
						editor.windowManager.open({
							title: 'Insert Email History',
							body: newsletters_history_body,
							onsubmit: function(e) {
								var newsletters_history = '[newsletters_history';
								
								if (e.data.newsletters_history_number.length > 0) {
									newsletters_history += ' number="' + e.data.newsletters_history_number + '"';
								}
								
								if (e.data.newsletters_history_order.length > 0 && e.data.newsletters_history_orderby.length > 0) {
									newsletters_history += ' order="' + e.data.newsletters_history_order + '"';
									newsletters_history += ' orderby="' + e.data.newsletters_history_orderby + '"';
								}
								
								if (e.data.newsletters_history_lists.length > 0) {
									newsletters_history += ' list_id="' + e.data.newsletters_history_lists + '"';
								}
								
								if (e.data.newsletters_history_linksonly == true) {
									newsletters_history += ' linksonly=1';
								}
								
								newsletters_history += ']';
							
								editor.insertContent(newsletters_history);
							}
						});
					}
				}, {
					text: "Snippet",
					onclick: function() {						
						editor.windowManager.open({							
							title: tinymce.settings.newsletters_snippet_title,
							width: 350,
							height: 75,
							body: [{
								type: 'listbox',
                                name: 'newsletters_snippet_list',
                                label: 'Snippet',
                                values: tinymce.settings.newsletters_snippet_list,
                                tooltip: tinymce.settings.newsletters_snippet_tooltip
							}],
							onsubmit: function(e) {
								editor.insertContent('[newsletters_snippet id="' + e.data.newsletters_snippet_list + '"]');
							}
						});
					}
				}, {
					text: "Video",
					onclick: function() {						
						editor.windowManager.open({							
							title: "Video",
							width: 450,
							height: 200,
							body: [{
								type: 'textbox',
								name: 'newsletters_video_url',
								label: 'Video URL',
								tooltip: 'URL of video on any popular video service eg. YouTube, Vimeo, etc.'
							}, {
								type: 'textbox',
								name: 'newsletters_video_width',
								label: 'Width',
								tooltip: 'Specify the width of the video image.'
							}, {
								type: 'textbox',
								name: 'newsletters_video_height',
								label: 'Height',
								tooltip: 'Specify the height of the video image.'
							}],
							onsubmit: function(e) {
								var videocontent = '[newsletters_video url="' + e.data.newsletters_video_url + '"';
								
								if (e.data.newsletters_video_width.length > 0) {
									videocontent += ' width="' + e.data.newsletters_video_width + '"';
								}
								
								if (e.data.newsletters_video_height.length > 0) {
									videocontent += ' height="' + e.data.newsletters_video_height + '"';
								}
								
								videocontent += ']';
								editor.insertContent(videocontent);
							}
						});
					}
				}
			];
	
		editor.addButton('Newsletters', {
			icon: 'newsletters',
			type: 'menubutton',
			menu: buttonmenu
		});
	});
})();
