<?php
defined('ABSPATH') or die;
//require_once( ABSPATH . 'wp-admin/admin.php' );

//Our class extends the WP_List_Table class, so we need to make sure that it's there
require_once(SLIDESHOWCK_PATH . '/helpers/cklisttable.php');
require_once(SLIDESHOWCK_PATH . '/helpers/cklisttable-general.php');

//Prepare Table of elements
$wp_list_table = new SlideshowCKListTableGeneral();
$wp_list_table->prepare_items();
?>
<link rel="stylesheet" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/admin.css" type="text/css" />
<div id="ckoptionswrapper" class="ckinterface">
	<a href="<?php echo SLIDESHOWCK_WEBSITE ?>" target="_blank" style="text-decoration:none;"><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/logo_slideshowck_64.png" style="margin: 5px;" class="cklogo" /><span class="cktitle">Slideshow CK</span></a>
	<div style="clear:both;"></div>
<link rel="stylesheet" type="text/css" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/slideshowck_edit.css" media="all" />
<div class="wrap">
	
	<div>
<?php
	echo ' <a href="admin.php?page=slideshowck_edit&id=0" class="button button-primary">' . __('Add New', 'slideshow-ck') . '</a>';
?>
	<a type="button" class="button" href="https://www.ceikay.com/documentation/slideshow-ck" target="_blank" style="vertical-align: text-bottom;"><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/page_white_acrobat.png" style="width:16px;height:16px;vertical-align:text-bottom;" /> <?php _e('Documentation'); ?></a>
	</div>
	<div style="clear:both;"></div>
	<form id="filter" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ) ?>" />
		<input type="hidden" name="post_status" class="post_status_page" value="<?php echo!empty($_REQUEST['post_status']) ? esc_attr($_REQUEST['post_status']) : 'all'; ?>" />
		<input type="hidden" name="post_type" class="post_type_page" value="slideshowck" />
<?php
$wp_list_table->display()
?>
	</form>
	<?php echo $this->copyright() ?>
</div>