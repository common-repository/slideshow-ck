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
class CKHelpersourceFlickr {

	private static $params;

	/**
	 * Get a list of the items.
	 */
	static function getItems($params) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		$url = 'https://api.flickr.com/services/rest/?format=json&method=flickr.photosets.getPhotos&extras=description,original_format,url_sq,url_t,url_s,url_m,url_o&nojsoncallback=1';
		$url .= '&api_key=' . $params['flickr_apikey'];
		$url .= '&photoset_id=' . $params['flickr_photoset'];

		$response = wp_remote_get( $url );
		if ( !is_wp_error( $response ) ) {
			$result = $response['body'];
		} else {
			_e('No Flickr Photo found');
			return;
		}

		$images = json_decode($result)->photoset->photo;

		$items = Array();
		$i = 0;
		$flickrSuffixes = array('o', 'k', 'h', 'b', 'z', 'sq', 't', 'sm', 'm');
		foreach ($images as & $image) {
			$items[$i] = new \stdClass();
			$item = $items[$i];
			$suffix = 'o';
			foreach ($flickrSuffixes as $flickrSuffixe) {
				if (isset($image->{'url_' . $flickrSuffixe})) {
					$suffix = $flickrSuffixe;
					break;
				}
			}
			$item->imgname = $image->{'url_' . $suffix};
			$item->imgthumb = $item->imgname;

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

			// set the title, link and description of the image
			$item->imgtitle = $image->title;
			$item->imgcaption = $image->description->_content;
			$item->imglink = $image->{'url_' . $suffix};

			$item->description = $item->imgcaption;
			$item->title = $item->imgtitle;

			$i++;
		}

		return $items;
	}

	/**
	 * Render the field to select the folder from which to import the slides
	 */
	public static function render_options($fields) {
		?>
		<div>
			<label for="flickr_apikey"><?php _e('Flickr API key', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<?php echo $fields->render('text', 'flickr_apikey') ?>
			<i><?php _e('Give the Flickr API key that you must get in your Flickr account', 'slideshow-ck'); ?></i>
		</div>
		<div>
			<label for="flickr_photoset"><?php _e('Flickr album photoset', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<?php echo $fields->render('text', 'flickr_photoset') ?>
			<i><?php _e('Give the Flickr album photoset ID that is in the album url', 'slideshow-ck'); ?></i>
		</div>
		<?php
	}

	/**
	 * Get the items from flickr
	 */
	public static function get_items_flickr() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();
		$options = array('flickr_apikey' => $Slideshowck->get_param('flickr_apikey', '')
						, 'flickr_photoset' => $Slideshowck->get_param('flickr_photoset', '')
						);
		$items = self::getItems($options);

		return $items;
	}
}
