<?php
namespace Slideshowck\Tinymce;

use \Slideshowck\CKfof;

if ( ! is_admin() ) {
	return;
}

function register() {
	add_action( 'media_buttons', '\\Slideshowck\\Tinymce\\add_button' );
	add_action( 'wp_enqueue_media', '\\Slideshowck\\Tinymce\\register_scripts_styles' );
	add_action( 'wp_ajax_list_slideshows', '\\Slideshowck\\Tinymce\\ajax_list_slideshows' );
}

function add_button() {
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) || 'true' !== get_user_option( 'rich_editing' ) ) {
		return;
	}
	echo( '<a href="#" id="slideshowck-tinymce-button" class="button"><img class="slideshowck-tinymce-button-icon" src="' . esc_attr( plugins_url( '/slideshow-ck-pro/images/photo.png' ) ) . '">' . esc_html__( 'Slideshow CK', 'slideshow-ck' ) . '</a>' );
	add_thickbox();
}

function register_scripts_styles() {
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) || 'true' !== get_user_option( 'rich_editing' ) ) {
		return;
	}
	wp_enqueue_style( 'slideshowck_tinymce', SLIDESHOWCK_MEDIA_URL . '/assets/tinymce.css' );
	wp_enqueue_style( 'ckbox', SLIDESHOWCK_MEDIA_URL . '/assets/ckbox.css' );
	$isDivi = isset($_REQUEST['page']) && $_REQUEST['page'] === 'et_theme_builder';
	if (! $isDivi) wp_enqueue_script( 'slideshowck_tinymce', SLIDESHOWCK_MEDIA_URL . '/assets/tinymce.js'); 
	wp_enqueue_script( 'ckbox', SLIDESHOWCK_MEDIA_URL . '/assets/ckbox.js' );
	wp_localize_script(
		'slideshowck_tinymce',
		'slideshowckTinymceLocalize',
		[
			'dialog_title'  => esc_html__( 'Slideshow CK list', 'slideshow-ck' ),
			'title'  => esc_html__( 'Title', 'slideshow-ck' ),
			'action'  => esc_html__( 'Action', 'slideshow-ck' ),
			'root_name'     => esc_html__( 'Slideshow CK', 'slideshow-ck' ),
			'insert_button' => esc_html__( 'Insert', 'slideshow-ck' ),
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'slideshowck_editor_plugin' ),
		]
	);
}

// function handle_ajax() {
	// try {
		// ajax_handler_body();
	// } catch ( \Slideshowck\Vendor\Google_Service_Exception $e ) {
		// if ( 'userRateLimitExceeded' === $e->getErrors()[0]['reason'] ) {
			// wp_send_json( [ 'error' => esc_html__( 'The maximum number of requests has been exceeded. Please try again in a minute.', 'slideshow-ck' ) ] );
		// } else {
			// wp_send_json( [ 'error' => $e->getErrors()[0]['message'] ] );
		// }
	// } catch ( \Exception $e ) {
		// wp_send_json( [ 'error' => $e->getMessage() ] );
	// }
// }

/*function ajax_handler_body() {
	check_ajax_referer( 'slideshowck_editor_plugin' );
	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		throw new \Exception( esc_html__( 'Insufficient role for this action.', 'slideshow-ck' ) );
	}
	if ( ! get_option( 'slideshowck_access_token' ) ) {
		// translators: 1: Start of link to the settings 2: End of link to the settings
		throw new \Exception( sprintf( esc_html__( 'Google Drive gallery hasn\'t been granted permissions yet. Please %1$sconfigure%2$s the plugin and try again.', 'slideshow-ck' ), '<a href="' . esc_url( admin_url( 'admin.php?page=slideshowck_basic' ) ) . '">', '</a>' ) );
	}

	$client = \Slideshowck\Frontend\GoogleAPILib\get_drive_client();

	$path = isset( $_GET['path'] ) ? $_GET['path'] : [];
	$ret  = walk_path( $client, $path );

	wp_send_json( [ 'directories' => $ret ] );
}

function walk_path( $client, array $path, $root = null ) {
	if ( ! isset( $root ) ) {
		$root_path = \Slideshowck\Options::$root_path->get();
		$root      = end( $root_path );
	}
	if ( 0 === count( $path ) ) {
		return list_files( $client, $root );
	}
	$page_token = null;
	do {
		$params   = [
			'q'                     => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id, name)',
		];
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Slideshowck\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			if ( $file->getName() === $path[0] ) {
				array_shift( $path );
				return walk_path( $client, $path, $file->getId() );
			}
		}
	} while ( null !== $page_token );
	throw new \Exception( esc_html__( 'No such directory found - it may have been deleted or renamed. ', 'slideshow-ck' ) );
}

function list_files( $client, $root ) {
	$ret        = [];
	$page_token = null;
	do {
		$params   = [
			'q'                     => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id, name)',
		];
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Slideshowck\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$ret[] = $file->getName();
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	return $ret;
}*/

function ajax_list_slideshows() {
	require_once(SLIDESHOWCK_PATH . '/helpers/ckfof.php');

	// -- Preparing your query -- 
	$query = "SELECT * FROM #__posts WHERE post_type = 'slideshowck'";
	$items = CKFof::dbLoadObjectList($query);
	$slideshowids = isset( $_GET['slideshowids'] ) ? (array)$_GET['slideshowids'] : [];
	?>
<table class="wp-list-table">
	<thead>
		<tr>
		</tr>
	</thead>
	<tbody id="the-list">
		<?php 
		$row_class = '';
		if (! empty($items) ) {
			foreach ( $items as $item ) {
				// static $row_class = '';
				$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

				echo '<tr' . $row_class . '>';
				?>
				<td><?php echo $item->ID ?></td>
				<td><?php echo $item->post_title ?></td>
				
				<?php if (in_array($item->ID, $slideshowids)) { ?>
				<td><a class="button" onclick="ckEditSlideshowFromEditor('<?php echo $item->ID ?>')"><?php _e('Edit', 'slideshow-ck') ?></a></td>
				<?php } else { ?>
				<td><a class="button" onclick="ckAddSlideshoToEditor('<?php echo $item->ID ?>')"><?php _e('Insert', 'slideshow-ck') ?></a></td>
				<?php } ?>
				<?php
				echo '</tr>';
			}
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="2">';
			_e( 'No items found.' );
			echo '</td></tr>';
		}
		?>
	</tbody>
</table>
<?php

	//Prepare Table of elements
	// $wp_list_table = new \SlideshowCKListTableGeneral();
	// $wp_list_table->prepare_items();
	// $wp_list_table->display();
	exit;
}
