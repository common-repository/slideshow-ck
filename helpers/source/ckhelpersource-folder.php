<?php
/**
 * @copyright	Copyright (C) 2017. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr - http://www.wp-pluginsck.com
 */

Namespace Slideshowck;

// No direct access
defined('ABSPATH') or die;

/**
 * Helper Class.
 */
class CKHelpersourceFolder {

	private static $folderLabels = array();

	private static $imagesOrderByLabels = array();

	private static $params;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params, $folder = null) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		// $folder = $folder ? $folder : $params->get('folder', '');
		if (! $folder) {
			return false;
		}

		CKFof::loadHelper('folder');
		CKFof::loadHelper('path');
		// encode the folder path, needed if contains an accent
		$foldern = iconv("UTF-8", "ISO-8859-1//TRANSLIT", urldecode($folder));
		$files = CKFolder::files(ABSPATH . '/' . $foldern, '.png|.jpg|.bmp|.tif|.tiff|.jpeg', false, false);
		if (!$files) {
			echo '<p style="color:red">' . ('No images found in the folder ' . $folder) . '</p>';
			return array();
		}
		natsort($files);

		// load the labels from the folder
		self::$imagesOrderByLabels = array();
		self::getImageLabelsFromFolder($foldern);

		// set the images order
		// $order = 'labels';
		// if ($order == 'random') {
			// shuffle($files);
		// } else if($order == 'labels') {
			natsort($files);
			$files = array_map(array(__CLASS__, 'formatPath'), $files);
			$baseDir = self::formatPath($folder);
			$labelsOrder = array_reverse(self::$imagesOrderByLabels);

			foreach ($labelsOrder as $name) {
				if (in_array($name, $files)) array_unshift($files, $name);
			}
			// now make it unique
			$files = array_unique($files);
		// } else {
			// natsort($files);
		// }

		$items = array();

		foreach ($files as $file) {
			// get the data for the image
			$filedata = self::getImageDataFromfolder($file, $foldern);

			$file = str_replace("\\", "/", utf8_encode($file));
			$item = new \stdClass();
			$item->path = trim(str_replace('\\', '/', $folder), '/') . '/' . $file;
			$item->link = $filedata->link ? $filedata->link : ($params['linkautoimage'] && $params['lightbox'] != 'none' ? site_url() . '/' . $item->path : '' );
			$item->title = trim($filedata->title);
			$item->desc = trim($filedata->desc);

			$items[] = $item;
		}
		return $items;
	}

	/*
	 * Load the data for the image (title and description)
	 */
	private static function getImageDataFromfolder($file, $folder) {
		$filename = explode('/', $file);
		$filename = end($filename);
		$dirindex = self::cleanName($folder);
		$fileindex = self::cleanName($filename);

		if (! empty(self::$folderLabels[$dirindex]) && ! empty(self::$folderLabels[$dirindex][$fileindex])) {
			$item = self::$folderLabels[$dirindex][$fileindex];
		} else {
			$item = new \stdClass();
			$item->title = null;
			$item->desc = null;
			$item->link = null;
		}

		return $item;
	}

	/*
	 * Load the data for the image (title and description)
	 */
	private static function getImageLabelsFromFolder($folder) {
		$dirindex = self::cleanName($folder);
		if (! empty(self::$folderLabels[$dirindex])) return;

		$items = array();
		$item = new \stdClass();

		// get the language
		$langtag = get_locale(); // returns fr_FR or en_GB

		// load the image data from txt
		if (file_exists(ABSPATH . '/' . $folder . '/labels.' . $langtag . '.txt')) {
			$data = file_get_contents(ABSPATH . '/' . $folder . '/labels.' . $langtag . '.txt');
		} else if (file_exists(ABSPATH . '/' . $folder . '/labels.txt')) {
			$data = file_get_contents(ABSPATH . '/' . $folder . '/labels.txt');
		} else {
			return null;
		}

		$doUTF8encode = true;
		// remove UTF-8 BOM and normalize line endings
		if (!strcmp("\xEF\xBB\xBF", substr($data,0,3))) {  // file starts with UTF-8 BOM
			$data = substr($data, 3);  // remove UTF-8 BOM
			$doUTF8encode = false;
		}
		$data = str_replace("\r", "\n", $data);  // normalize line endings

		// if no data found, exit
		if(! $data) return null;

		// explode the file into rows
		// $imgdatatmp = explode("\n", $data);
		$imgdatatmp = preg_split("/\r\n|\n|\r/", $data, -1, PREG_SPLIT_NO_EMPTY);

		$parmsnumb = count($imgdatatmp);
		for ($i = 0; $i < $parmsnumb; $i++) {
			$imgdatatmp[$i] = trim($imgdatatmp[$i]);
			$line = explode('|', $imgdatatmp[$i]);

			// store the order or files from the TXT file
			self::$imagesOrderByLabels[] = $line[0];

			$item = new \stdClass();
			$item->index = self::cleanName($line[0]);
			$item->title = (isset($line[1])) ? ( $doUTF8encode ? (utf8_encode($line[1])) : ($line[1]) ) : '';
			$item->desc = (isset($line[2])) ? ( $doUTF8encode ? (utf8_encode($line[2])) : ($line[2]) ) : '';
			$item->link = (isset($line[3])) ? ( $doUTF8encode ? htmlspecialchars(utf8_encode($line[3])) : htmlspecialchars($line[3]) ) : '';

			$items[$item->index] = $item;
		}

		self::$folderLabels[$dirindex] = $items;
	}

	/*
	 * Remove special character
	 */
	private static function cleanName($path) {
		return preg_replace('/[^a-z0-9]/i', '_', $path);
	}

	/*
	 * Format the path to use only /
	 */
	public static function formatPath($p) {
			return trim(str_replace("\\", "/", $p), "/");
	}

	/**
	 * Render the field to select the folder from which to import the files
	 */
	public static function render_options($fields) {
		?>
		<div>
			<label for="autoloadfoldername"><?php _e('Autoload from a folder', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/folder_explore.png" />
			<?php echo $fields->render('text', 'autoloadfoldername') ?>
			<span class="button" onclick="ckShowBrowser('folder', 'autoloadfoldername')"><?php _e('Select'); ?></span>
			<i><?php _e('Select the folder in the interface or write your own path. Example : wp-content/images', 'slideshow-ck'); ?></i>
		</div>
		<?php
	}
	
	/**
	 * Get the items from a folder path
	 */
	public static function get_items_autoloadfolder() {
		$Slideshowck = \Slideshowck\Slideshowck::getInstance();
		$options = array('linkautoimage' => $Slideshowck->get_param('linkautoimage', '1')
						, 'lightbox' => ($Slideshowck->get_param('imagetarget', 'mediabox') == 'mediabox' ? 'mediabox' : 'none')
						);
		$items = self::getItems($options, trim($Slideshowck->get_param('autoloadfoldername'), '/'));

		foreach ($items as $i => $itemf) {
			$item = new \stdClass();
			$item->imgname = $itemf->path;
			$Slideshowck->get_item_data($item);

			// set the variables
			$item->imgthumb = $item->imgname;
			$item->description = $itemf->desc;
			$item->imglink = $itemf->link;
			$item->title = $itemf->title;

			// load the image data from txt
//			$item = self::getImageDataFromfolder($item, $params); // TODO : à mettre en place
//			$item->imgname = get_site_url() . '/' . trim($item->imgname, '/');
			$items[$i] = $item;
		}

		return $items;
	}

	/**
	 * Search for image files in a folder
	 */
	public static function load_images_from_folder($folder) {
		CKFof::loadHelper('folder');
		if (class_exists('SlideshowckFolder')) {
			$files = SlideshowckFolder::files(ABSPATH . trim($folder, '/'), '.png|.jpg|.bmp|.tif|.tiff|.jpeg|.JPEG|.JPG|.PNG');
		} else {
			$files = CKFolder::files(ABSPATH . trim($folder, '/'), '.png|.jpg|.bmp|.tif|.tiff|.jpeg|.JPEG|.JPG|.PNG');
		}

		if (! is_array($files) or ! count($files)) {
			return false;
		}
		return $files;
	}
}
