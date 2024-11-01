'use strict';
jQuery( document ).ready( function( $ ) {
	// var path = [];
	var html = '<div id="slideshowck-tinymce-modal"></div>';

	$( '#slideshowck-tinymce-button' ).click( tinymceOnclick );
	$( 'body' ).append( html );

	function tinymceOnclick() {
		tinymceHtml();
		tb_show( slideshowckTinymceLocalize.dialog_title, '#TB_inline?inlineId=slideshowck-tinymce-modal' );
		var content = tinymce.activeEditor.getContent({format: 'text'});
		var re = /\[slideshowck\s+id=(.*?)\s*\]/ig;
				var matches = content.match(re);
				if (matches && matches.length) {
					var slideshowids = [];
					for (var i = 0; i < matches.length; i++) {
						var s = matches[i];
						var re2 = /\d+/ig;
						var slideshowid = s.match(re2);
						if (slideshowid) slideshowids.push(slideshowid[0]);
					}
				}
		ajaxQuery(slideshowids);
	}

	function tinymceHtml() {
		var html = '<h3>' + slideshowckTinymceLocalize.root_name + '</h3>';
		html += '<table id="slideshowck-tinymce-table" class="widefat">';
		html += '<thead>';
		html += '<tr>';
		html += '<td>ID</td>';
		html += '<td>' + slideshowckTinymceLocalize.title + '</td>';
		html += '<td>' + slideshowckTinymceLocalize.action + '</td>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody id="slideshowck-tinymce-list">Loading ...</tbody>';
		// html += '<tfoot>';
		// html += '<tr>';
		// html += '<td class="slideshowck-tinymce-path">' + slideshowckTinymceLocalize.root_name + '</td>';
		// html += '</tr>';
		// html += '</tfoot>';
		html += '</table>';
		// html += '<p>&nbsp;</p>';
		// html += '<div class="slideshowck-tinymce-footer">';
		// html += '<a id="slideshowck-tinymce-insert" class="button button-primary">' + slideshowckTinymceLocalize.insert_button + '</a>';
		// html += '</div>';
		$( '#slideshowck-tinymce-modal' ).html( html );
		$( '#slideshowck-tinymce-insert' ).click( function() {
			tinymceSubmit();
		});
	}

	// function tinymceSubmit() {
		// if ( $( '#slideshowck-tinymce-insert' ).attr( 'disabled' ) ) {
			// return;
		// }
		// tinymce.activeEditor.insertContent( '[slideshowck path="' + path.join( '/' ) + '"]' );
		// tb_remove();
	// }

	function ajaxQuery(slideshowids) {
		$( '#slideshowck-tinymce-insert' ).attr( 'disabled', 'disabled' );
		$.get( slideshowckTinymceLocalize.ajax_url, {
			_ajax_nonce: slideshowckTinymceLocalize.nonce, // eslint-disable-line camelcase
			action: 'list_slideshows',
			slideshowids: slideshowids
			}, function( data ) {
				// $( '#slideshowck-tinymce-list' ).html( '' );
				// if ( data.directories ) {
					// success( data.directories );
				// } else if ( data.error ) {
					// error( data.error );
				// }
				$( '#slideshowck-tinymce-list' ).html(data);
			}
		);
	}

	// function success( data ) {
		// var i;
		// var html = '';
		// var len = data.length;
		// $( '#slideshowck-tinymce-insert' ).removeAttr( 'disabled' );
		// if ( 0 < path.length ) {
			// html += '<tr><td class="row-title"><label>..</label></td></tr>';
		// }
		// for ( i = 0; i < len; i++ ) {
			// html += '<tr class="';
			// if ( ( 0 === path.length && 1 === i % 2 ) || ( 0 < path.length && 0 === i % 2 ) ) {
				// html += 'alternate';
			// }
			// html += '"><td class="row-title"><label>' + data[i] + '</label></td></tr>';
		// }
		// $( '#slideshowck-tinymce-list' ).html( html );
		// html = '<a>' + slideshowckTinymceLocalize.root_name + '</a>';
		// len = path.length;
		// for ( i = 0; i < len; i++ ) {
			// html += ' > ';
			// html += '<a data-name="' + path[i] + '">' + path[i] + '</a>';
		// }
		// $( '.slideshowck-tinymce-path' ).html( html );
		// $( '.slideshowck-tinymce-path a' ).click( pathClick );
		// $( '#slideshowck-tinymce-list label' ).click( click );
	// }

	// function pathClick() {
		// path = path.slice( 0, path.indexOf( $( this ).data( 'name' ) ) + 1 );
		// ajaxQuery();
	// }

	// function click() {
		// var newDir = $( this ).html();
		// if ( '..' === newDir ) {
			// path.pop();
		// } else {
			// path.push( newDir );
		// }
		// ajaxQuery();
	// }

	// function error( message ) {
		// var html = '<div class="notice notice-error"><p>' + message + '</p></div>';
		// $( '#TB_ajaxContent' ).html( html );
	// }

	
});

function ckAddSlideshoToEditor(id) {
	tinymce.activeEditor.insertContent( '[slideshowck id="' + id + '"]' );
	tb_remove();
}

function ckEditSlideshowFromEditor(id) {
	// http://wordpress1/wp-admin/admin.php?page=slideshowck_edit&id=152
	CKBox.open({url : 'admin.php?page=slideshowck_edit&modal=1&id=' + id});
}
