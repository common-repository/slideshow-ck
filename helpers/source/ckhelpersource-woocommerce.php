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
class CKHelpersourceWoocommerce {

	private static $params;

	/**
	 * Get a list of the items.
	 */
	static function getItems($params) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		$categories = is_array($params['category']) ? $params['category'] : explode(',', $params['category']);
		$tax_query = (int)$params['category'] == 0 ? '' : array(
							array(
								'taxonomy'		=> 'product_cat',
								'field' 		=> 'term_id', 	// This is optional, as it defaults to 'term_id'
								'terms'			=> $categories,
								'operator'		=> 'IN' 		// Possible values are 'IN', 'NOT IN', 'AND'.
							)
						);
		$args = array(
			'posts_per_page'   => (int)$params['numberslides'],
			// 'offset'           => 0,
			// 'category'         => (int)$params['category'],
			// 'category_name'    => '',
			'orderby'          => $params['orderby'],
			'order'            => $params['order'],
			// 'include'          => '',
			// 'exclude'          => '',
			// 'meta_key'         => '',
			// 'meta_value'       => '',
			'post_type'        => 'product',
			// 'post_mime_type'   => '',
			// 'post_parent'      => '',
			// 'author'	   => '',
			// 'author_name'	   => '',
			'post_status'      => 'publish',
			// 'meta_query' 	   => array(array('key' => '_thumbnail_id')),
			'tax_query'			=> $tax_query,
			// 'suppress_filters' => true 
		);

		$posts_array = get_posts($args);
// var_dump($posts_array);
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
			$item->imglink = $image->{'url_' . $suffix};

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
		$args = array(
		  'taxonomy'     => 'product_cat',
		  'orderby'      => 'name',
		  // 'show_count'   => $show_count,
		  // 'pad_counts'   => $pad_counts,
		  // 'hierarchical' => $hierarchical,
		  // 'title_li'     => $title,
		  // 'hide_empty'   => $empty
		);
		$post_categories = get_categories($args);
		$selected = $Slideshowck->get_param('woocommerce_category', array());
		foreach ($post_categories as $cat) {
			$isselected = in_array($cat->cat_ID, $selected) ? ' selected="true"' : '';
			$cat_options[] = '<option value="' . $cat->cat_ID . '"' . $isselected . '>' . $cat->name . '</option>';
		}
		?>
		<div>
			<label for="woocommerce_category"><?php _e('Woocommerce category', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<select name="posts_category" id="woocommerce_category" multiple="true" style="height:200px;">
				<?php echo implode('', $cat_options); ?>
			</select>
			<i><?php _e('Select the categories from which to show the products', 'slideshow-ck'); ?></i>
		</div>
		<?php
	}

	/**
	 * Get the items from flickr
	 */
	public static function get_items_woocommerce() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();

		$numberslides = $Slideshowck->get_param('numberslides', '') ? $Slideshowck->get_param('numberslides', '') : -1;
		$category = is_array($Slideshowck->get_param('woocommerce_category', '')) ? implode(',', $Slideshowck->get_param('woocommerce_category', '')) : $Slideshowck->get_param('posts_category', '');

		$options = array('numberslides' 	=> $numberslides
						, 'category'       	=> $category
						, 'orderby'       	=> 'date'
						, 'order'         	=> 'DESC'
						);

		$items = self::getItems($options);

		return $items;
	}
}
