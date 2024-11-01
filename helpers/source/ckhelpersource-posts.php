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
class CKHelpersourcePosts {

	private static $params;

	/**
	 * Get a list of the items.
	 */
	static function getItems($params) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		$args = array(
			'posts_per_page'   => (int)$params['numberslides'],
			// 'offset'           => 0,
			'category'         => $params['category'],
			// 'category_name'    => '',
			'orderby'          => $params['orderby'],
			'order'            => $params['order'],
			// 'include'          => '',
			// 'exclude'          => '',
			// 'meta_key'         => '',
			// 'meta_value'       => '',
			'post_type'        => 'post',
			// 'post_mime_type'   => '',
			// 'post_parent'      => '',
			// 'author'	   => '',
			// 'author_name'	   => '',
			'post_status'      => 'publish',
			'meta_query' 	   => array(array('key' => '_thumbnail_id'))
			// 'suppress_filters' => true 
		);

		$posts_array = get_posts($args);

		$items = Array();
		$i = 0;
		foreach ($posts_array as & $post) {
			$img= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' )[0];
			// if no image, then go next
			if (! $img) continue;

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

			$item->imgname = $img;
			$item->imgthumb = $item->imgname;
			$item->imgtitle = $post->post_title;
			$item->imgcaption = ($post->post_excerpt ? $post->post_excerpt : $post->post_content);
//			$item->imglink = $image->{'url_' . $suffix};

			$item->description = $item->imgcaption;
			$item->title = $item->imgtitle;

			$i++;
		}

		return $items;
	}

	/**
	 * Render the field to select the folder from which to import the files
	 */
	public static function render_options() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();
		$cat_options = Array();
		$post_categories = \get_categories();
		$selected = $Slideshowck->get_param('posts_category', array());
		foreach ($post_categories as $cat) {
			$isselected = in_array($cat->cat_ID, $selected) ? ' selected="true"' : '';
			$cat_options[] = '<option value="' . $cat->cat_ID . '"' . $isselected . '>' . $cat->name . '</option>';
		}
		?>
		<div>
			<label for="posts_category"><?php _e('Posts category', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<select name="posts_category" id="posts_category" multiple="true" style="height:200px;">
				<?php echo implode('', $cat_options); ?>
			</select>
			<i><?php _e('Select the categories from which to show the posts', 'slideshow-ck'); ?></i>
		</div>
		<?php
	}

	/**
	 * Get the items from flickr
	 */
	public static function get_items_posts() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();

		$numberslides = $Slideshowck->get_param('numberslides', '') ? $Slideshowck->get_param('numberslides', '') : -1;
		$category = is_array($Slideshowck->get_param('posts_category', '')) ? implode(',', $Slideshowck->get_param('posts_category', '')) : $Slideshowck->get_param('posts_category', '');

		$options = array('numberslides' 	=> $numberslides
						, 'category'       	=> $category
						, 'orderby'       	=> 'date'
						, 'order'         	=> 'DESC'
						);

		$items = self::getItems($options);

		return $items;
	}
}
