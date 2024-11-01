<?php
Namespace Slideshowck;

defined('ABSPATH') or die;

// check if the user has the rights to access this page
if (!current_user_can('manage_options')) {
	wp_die(__('You do not have sufficient permissions to access this page.'));
}

// load scripts
wp_enqueue_media();
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core ');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_style('wp-jquery-ui-dialog');
wp_enqueue_style('wp-color-picker');

// load the additional files
CKFof::loadHelper('folder');
CKFof::loadHelper('file');
CKFof::loadHelper('path');
$plugin = 'slideshow-ck';
$jsFuncName = str_replace('-', '_', $plugin);
// add_option to store
$key = get_option('license_key_' . $plugin);
// get the membership remaining days
$remaining_days = get_transient( $plugin . '-remainingdays' );
if ( $remaining_days === false) {
	$remaining_days = self::getRemote('days');
	set_transient( $plugin . '-remainingdays', $remaining_days, 12 * HOUR_IN_SECONDS );
}

$days_style = $remaining_days < 0 ? 'red' : ( $remaining_days < 15 ? 'orange' : '#333' );
?>
<script>
	function ck_submit_license_key_<?php echo $jsFuncName ?>(btn) {
		if (! document.getElementById('license_key_<?php echo $plugin ?>').value) {
			alert('<?php _e('Please fill the license number', $plugin) ?>');
			return;
		}
		var spinner = jQuery(btn).find('.spinner');
		spinner.addClass('is-active');
		var data = {
			action: 'save_license_key',
			// plugin: '<?php echo $plugin ?>',
			_wpnonce: '<?php echo wp_create_nonce('save_license_key_' . $plugin); ?>',
			key: document.getElementById('license_key_<?php echo $plugin ?>').value
		};
		jQuery.post(ajaxurl, data, function(response) {
			spinner.removeClass('is-active');
		});
	}
</script>
<link rel="stylesheet" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/admin.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/ckframework.css" media="all" />
<div id="ckoptionswrapper" class="ckinterface">
		<a href="<?php echo SLIDESHOWCK_WEBSITE ?>" target="_blank" style="text-decoration:none;"><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/logo_slideshowck_64.png" style="margin: 5px;" class="cklogo" /><span class="cktitle">Slideshow CK Pro - <?php echo __('License', 'slideshow-ck') ?></span></a>
		<div style="clear:both;"></div>
		<p>
			<label for="license_key_<?php echo $plugin ?>"><?php _e('Enter your license key', $plugin) ?></label>
			<input type="text" id="license_key_<?php echo $plugin ?>" placeholder="<?php _e('License key number', $plugin) ?>" value="<?php echo $key ?>"/>
			<a class="button" href="javascript:void(0)" onclick="ck_submit_license_key_<?php echo $jsFuncName ?>(this)"><span class="spinner" style="margin-left:5px;margin-right:0;"></span> <?php _e('Save', $plugin) ?></a>
		</p>
		<div><a href="https://www.ceikay.com/documentation/miscellaneous/how-to-use-your-license-code/" target="_blank"><?php _e('Read the documentation', $plugin) ?></a></div>
		<?php if ($remaining_days > 0) { ?>
		<p class="ckalert-success"><?php _e('You license is active !', $plugin) ?><br /><span><?php _e('Remaining days', $plugin) ?></span> : <span style="color:<?php echo $days_style ?>;"><?php echo $remaining_days; ?></span></p>
		<?php } else { ?>
		<p class="ckalert-danger"><?php _e('You license is expired or not configured correctly !', $plugin) ?><br /><span><?php _e('Remaining days', $plugin) ?></span> : <span style="color:<?php echo $days_style ?>;"><?php echo $remaining_days; ?></span></p>
		<?php } ?>
</div>
<?php echo $this->copyright() ?>
