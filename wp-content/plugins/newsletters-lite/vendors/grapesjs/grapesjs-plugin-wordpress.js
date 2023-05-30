grapesjs.plugins.add('gjs-plugin-wordpress', (editor, opts = {}) => {	
	let c = opts;
	let config = editor.getConfig();
	let pfx = config.stylePrefix || '';
	let btnEl;
	
	var file_frame, 
		assets = [];
	
	let defaults = {
		// Custom button element which triggers modal
		btnEl: '',
		
		// Text for the button in case the custom one is not provided
		btnText: '<i class="fa fa-image fa-fw"></i> Add Media',
		
		// On complete upload callback
		// blobs - Array of Objects, eg. [{url:'...', filename: 'name.jpeg', ...}]
		// assets - Array of inserted assets
		onComplete: (blobs, assets) => {},
	};

	// Load defaults
	for (let name in defaults) {
		if (!(name in c))
		c[name] = defaults[name];
	}
	
	editor.on('run:open-assets', function() {		
		const modal = editor.Modal;
	    const modalBody = modal.getContentEl();
	    const uploader = modalBody.querySelector('.' + pfx + 'am-file-uploader');
	    const assetsHeader = modalBody.querySelector('.' + pfx + 'am-assets-header');
	    const assetsBody = modalBody.querySelector('.' + pfx + 'am-assets-cont');
	    
	    uploader && (uploader.style.display = 'none');
	    assetsHeader && (assetsHeader.style.display = 'none');
	    assetsBody.style.width = '100%';
	    
	    // Instance button if not yet exists
	    if(!btnEl) {		    
			btnEl = c.btnEl;
			
			if(!btnEl) {				
				btnEl = document.createElement('button');
				btnEl.className = pfx + 'btn-prim ' + pfx + 'btn-wordpress';
				btnEl.innerHTML = c.btnText;
			}
	    }
	    
	    btnEl.onclick = function() {
		    event.preventDefault();
					
			// If the media frame already exists, reopen it.
			if (file_frame) {
				file_frame.open();
				return;
			}
			
			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Upload Media',
				button: {
					text: 'Select',
				},
				multiple: true
			});
			
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				var attachments = file_frame.state().get('selection');
				
				attachments.map(function( attachment) {
			    	attachment = attachment.toJSON();
					assets.push({src:attachment.url});
			    });
			      
			    editor.AssetManager.add(assets);
			});
			
			file_frame.open();
	    }
	    
	    assetsBody.insertBefore(btnEl, assetsHeader);
	});
	
	var pnm = editor.Panels;
	pnm.addButton('options', [{
		id: 'clean-all',
		className: 'fa fa-trash icon-blank',
		command: 'clean-all',
		attributes: {title: 'Empty canvas'}
	}]);
	
	// Commands
	var cmd = editor.Commands;
	cmd.add('clean-all', {
		run: function(editor, sender) {
			sender && sender.set('active',false);
			if(confirm('Are you sure to clean the canvas?')){
				var comps = editor.DomComponents.clear();
				localStorage.clear();
			}
		}
	});
	
	/*editor.DomComponents.addType('custom-type', {
	  model: {},
	  view: defaultView.extend({
	     init() {
	       this.listenTo(this.model, 'active', this.doStuff); // listen for active event
	     },
	     doStuff() {
		     console.log('do stuff');
	     }
	  }),
	});
	
	var blockManager = editor.BlockManager;
	
	blockManager.add('test-block', {
	 label: 'Label',
	 content: {
	    type: 'custom-type',
	    activeOnRender: 1, // <- this will trigger the active event
	 },
	});*/
	
	// Blocks
	/*var bm = editor.BlockManager;
	
	bm.add('newsletters-post', {
		label: 'Single Post',
		attributes: {class:'fa fa-file-o'},
		category: 'Newsletters',
		content: {
			type: 'shortcode_post',
			editable: true,
			droppable: false,
			style: {},
			content: '[newsletters_post post_id=X]',
			activeOnRender: 1
		}
	});
	
	editor.on('block:drag:stop', function(model) {
		console.log('block');
		console.log(model);
		
		if (model.attributes.type == "shortcode_post") {
			
		}
		
		editor.run('clean-all');
		
		return "testing this out";
		
		//editor.runCommand('open-assets');
	});*/
	
	/*var bm = editor.BlockManager;
	
	// Headings
	bm.add('heading1', {
		label: 'Heading 1',
		attributes: {class:'fa fa-header'},
		category: 'Headings',
		content: '<h1 class="newsletters-heading1">Type Heading 1 Here</h1>'
	});
	
	bm.add('heading2', {
		label: 'Heading 2',
		attributes: {class:'fa fa-header'},
		category: 'Headings',
		content: '<h2 class="newsletters-heading2">Type Heading 2 Here</h2>'
	});
	
	bm.add('heading3', {
		label: 'Heading 3',
		attributes: {class:'fa fa-header'},
		category: 'Headings',
		content: '<h3 class="newsletters-heading3">Type Heading 3 Here</h3>'
	});
	
	// Shortcodes
	
	// Columns
	bm.add('columns1-1', {
		label: '1/1 Column',
		attributes: { class:'gjs-fonts gjs-f-b1'},
		category: 'Columns & Rows',
		content: `<table class="newsletters-table" style="height: 150px; margin: 10px auto; padding: 5px; width: 100%;" width="100%" height="150">
		<tbody>
		<tr>
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:100%;" valign="top">
		</td>
		</tr>
		</tbody>
		</table>`,
    });
    
    bm.add('columns1-2', {
		label: '1/2 Column',
		attributes: { class:'gjs-fonts gjs-f-b2'},
		category: 'Columns & Rows',
		content: `<table class="newsletters-table" style="height: 150px; margin: 10px auto; padding: 5px; width: 100%;" width="100%" height="150">
		<tbody>
		<tr>
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:50%;" valign="top">
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:50%;" valign="top">
		</td>
		</tr>
		</tbody>
		</table>`,
    });
    
    bm.add('columns1-3', {
		label: '1/3 Column',
		attributes: { class:'gjs-fonts gjs-f-b3'},
		category: 'Columns & Rows',
		content: `<table class="newsletters-table" style="height: 150px; margin: 10px auto; padding: 5px; width: 100%;" width="100%" height="150">
		<tbody>
		<tr>
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:33.3333%;" valign="top">
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:33.3333%;" valign="top">
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:33.3333%;" valign="top">
		</td>
		</tr>
		</tbody>
		</table>`,
    });
    
    bm.add('columns3-7', {
		label: '3/7 Column',
		attributes: { class:'gjs-fonts gjs-f-b37'},
		category: 'Columns & Rows',
		content: `<table class="newsletters-table" style="height: 150px; margin: 10px auto; padding: 5px; width: 100%;" width="100%" height="150">
		<tbody>
		<tr>
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:30%;" valign="top">
		<td class="newsletters-column" style="padding: 0; margin: 0; vertical-align: top; width:70%;" valign="top">
		</td>
		</tr>
		</tbody>
		</table>`,
    });*/
    
    /*bm.add('sect50', {
      label: '1/2 Section',
      attributes: {class:'gjs-fonts gjs-f-b2'},
      content: `<table style="${tableStyleStr}">
        <tr>
          <td style="${cellStyleStr} width: 50%"></td>
          <td style="${cellStyleStr} width: 50%"></td>
        </tr>
        </table>`,
    });
    bm.add('sect30', {
      label: '1/3 Section',
      attributes: {class:'gjs-fonts gjs-f-b3'},
      content: `<table style="${tableStyleStr}">
        <tr>
          <td style="${cellStyleStr} width: 33.3333%"></td>
          <td style="${cellStyleStr} width: 33.3333%"></td>
          <td style="${cellStyleStr} width: 33.3333%"></td>
        </tr>
        </table>`,
    });
    bm.add('sect37', {
      label: '3/7 Section',
      attributes: {class:'gjs-fonts gjs-f-b37'},
      content: `<table style="${tableStyleStr}">
        <tr>
          <td style="${cellStyleStr} width:30%"></td>
          <td style="${cellStyleStr} width:70%"></td>
        </tr>
        </table>`,
    });*/
	
	/*var bm = editor.BlockManager;
  bm.add('testing', {
    label: 'testing',
    attributes: {class:'fa fa-envelope'},
    category: 'Basic',
    content: {
      type:'link',
      editable: false,
      droppable: true,
      style:{
        display: 'inline-block',
        padding: '5px',
        'min-height': '50px',
        'min-width': '50px'
      }
    },
  });*/
	
	/*var cmdm = editor.Commands;
  cmdm.add('undo', {
    run: function(editor, sender) {
      sender.set('active', 0);
      editor.UndoManager.undo(1);
    }
  });
  cmdm.add('redo', {
    run: function(editor, sender) {
      sender.set('active', 0);
      editor.UndoManager.redo(1);
    }
  });
  cmdm.add('set-device-desktop', {
    run: function(editor) {
      editor.setDevice('Desktop');
    }
  });
  cmdm.add('set-device-tablet', {
    run: function(editor) {
      editor.setDevice('Tablet');
    }
  });
  cmdm.add('set-device-mobile', {
    run: function(editor) {
      editor.setDevice('Mobile portrait');
    }
  });
  cmdm.add('clean-all', {
    run: function(editor, sender) {
      sender && sender.set('active',false);
      if(confirm('Are you sure to clean the canvas?')){
        var comps = editor.DomComponents.clear();
        localStorage.clear();
      }
    }
  });

  cmdm.add('html-import', {
    run: function(editor, sender) {
      sender && sender.set('active', 0);

      var modalContent = modal.getContentEl();
      var viewer = codeViewer.editor;
      modal.setTitle('Import Template');

      // Init code viewer if not yet instantiated
      if (!viewer) {
        var txtarea = document.createElement('textarea');
        var labelEl = document.createElement('div');
        labelEl.className = pfx + 'import-label';
        labelEl.innerHTML = 'Paste here your HTML/CSS and click Import';
        container.appendChild(labelEl);
        container.appendChild(txtarea);
        container.appendChild(btnImp);
        codeViewer.init(txtarea);
        viewer = codeViewer.editor;
      }

      modal.setContent('');
      modal.setContent(container);
      codeViewer.setContent(
          '<div class="txt-red">Hello world!</div>' +
          '<style>\n.txt-red {color: red;padding: 30px\n}</style>'
      );
      modal.open();
      viewer.refresh();
    }
  });*/

  /****************** BLOCKS *************************/
});