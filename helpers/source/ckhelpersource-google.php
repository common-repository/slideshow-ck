<?php
/**
 * @copyright	Copyright (C) 2018. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr - http://www.wp-pluginsck.com
 */

Namespace Slideshowck;

// No direct access
defined('ABSPATH') or die;

/**
 * Helper Class.
 */
class CKHelpersourceGoogle {

	private static $params;

	/**
	 * Get a list of the items.
	 */
	static function getItems($params) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		$url = $params['google_album_url'];
		if (! $url) {
			_e('Empty Google Photo album url');
			return;
		}

		$response = wp_remote_get( $url );
		if ( !is_wp_error( $response ) ) {
			$body = $response['body'];
			preg_match_all('@\["AF1Q.*?",\["(.*?)"\,@', $body, $urls);
			if(isset($urls[1])) $photos = $urls[1];
		}

		if(isset($urls[1])) {
			$photos = $urls[1];
		} else {
			_e('No Google Photo found');
			return;
		}

		$items = Array();
		$i = 0;
		foreach ($photos as & $photo) {
			$items[$i] = new \stdClass();
			$item = $items[$i];

			// set the variables
			$item->imgvideo = null;
			$item->slideselect = null;
			$item->slideselect = null;
			$item->imgcaption = null;
			$item->article = null;
			$item->slidearticleid = null;
			$item->imgalignment = null;
			$item->imgtarget = 'default';
			$item->imgtime = null;
			$item->imglink = null;
			$item->imgtitle = null;

			$imagewidth = (isset($params['google_image_width']) && $params['google_image_width'] ) ? '=w' . (int)$params['google_image_width'] : '';
			$imagewidthslideshow = (isset($params['google_image_width_slideshow']) && $params['google_image_width_slideshow'] ) ? '=w' . (int)$params['google_image_width_slideshow'] : '';
			$item->imgname = $photo . $imagewidthslideshow;
			$item->imgthumb = $item->imgname;
			$item->imgtitle = '';
			$item->imgcaption = '';
			$item->imglink = $photo . $imagewidth;

			$item->description = $item->imgcaption;
			$item->title = $item->imgtitle;

			$i++;
		}

		return $items;
	}

	/**
	 * Render the field to select the folder from which to import the files
	 */
	public static function render_options($fields) {
		?>
		<div>
			<label for="google_album_url"><?php _e('Google album url', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<?php echo $fields->render('text', 'google_album_url') ?>
			<i><?php _e('Click on the share button of your Google Photos album and paste it here', 'slideshow-ck'); ?></i>
		</div>
		<div>
			<label for="google_image_width_slideshow"><?php _e('Image width for the slideshow', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/width.png" />
			<?php echo $fields->render('text', 'google_image_width_slideshow') ?>
			<i><?php _e('Give a value in px for the image in the slideshow', 'slideshow-ck'); ?></i>
		</div>
		<div>
			<label for="google_image_width"><?php _e('Image width', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/width.png" />
			<?php echo $fields->render('text', 'google_image_width') ?>
			<i><?php _e('Give a value in px to link the full image on click', 'slideshow-ck'); ?></i>
		</div>
		<?php
	}

	/**
	 * Get the items from flickr
	 */
	public static function get_items_google() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();

		$options = array(
				'google_album_url' => $Slideshowck->get_param('google_album_url', '')
				,'google_image_width' => $Slideshowck->get_param('google_image_width', '')
				,'google_image_width_slideshow' => $Slideshowck->get_param('google_image_width_slideshow', '')
				);

		$items = self::getItems($options);

		return $items;
	}
}
