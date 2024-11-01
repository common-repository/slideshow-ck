<?php
Namespace Slideshowck;

defined('ABSPATH') or die;

// check if the user has the rights to access this page
//if (!current_user_can('manage_options')) {
//	wp_die(__('You do not have sufficient permissions to access this page.'));
//}

// init variables
$post = $post_type = $post_type_object = null;

// check if the post exists
if (isset($_GET['id'])) {
	$post_id = (int) $_GET['id'];
} elseif (isset($_POST['post_ID'])) {
	$post_id = (int) $_POST['post_ID'];
} else {
	$post_id = 0;
}

// get the existing post
if ($post_id) {
	$post = get_post($post_id);
}

if ($post) {
	$post_type = $post->post_type;
}

// check if we get a slideshow object
if (0 !== $post_id && 'slideshowck' !== $post->post_type) {
	wp_die(__('Invalid post type'));
}

// get the settings for the post
$this->params = json_decode(str_replace('|qq|', '"', get_post_meta((int) $post_id, 'slideshow-ck-params', TRUE)));
$post_title = isset( $post->post_title ) ? $post->post_title : '';

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
$input = CKFof::getInput();
$modalclass = ($input->get('modal', '', 'string') === '1') ? ' ckmodal' : '';
?>
<?php //echo $CK_Notices; // TODO : creer notices pour dire "bien enregistre"  ?>
<?php if ($input->get('modal', '', 'string') === '1') { ?>
<style>
#wpadminbar { display: none !important; }
#adminmenuback { display: none !important; }
#adminmenuwrap { display: none !important; }
</style>
<?php } ?>
<link rel="stylesheet" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/admin.css" type="text/css" />
<link rel="stylesheet" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/jscolor/jscolor.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/ckbox.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/ckframework.css" media="all" />
<link rel="stylesheet" type="text/css" href="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/slideshowck_edit.css" media="all" />
<script type="text/javascript" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/jscolor/jscolor.js" ></script>
<script type="text/javascript" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/ckbox.js" ></script>
<script type="text/javascript" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/assets/slideshowck_edit.js" ></script>
<input type="hidden" id="demo-params" name="demo-params" value="{|qq|slides_sources|qq|:|qq|slidemanager|qq|,|qq|ckaddslide|qq|:|qq|Add a Slide|qq|,|qq|ckslideimgname0|qq|:|qq|wp-content/plugins/slideshow-ck/slides/bridge.jpg|qq|,|qq|undefined|qq|:|qq|Clear|qq|,|qq|ckslidedataalignmenttext0|qq|:|qq|default|qq|,|qq|ckslidetargettext0|qq|:|qq|default|qq|,|qq|ckslideimgname1|qq|:|qq|/wp-content/plugins/slideshow-ck/slides/road.jpg|qq|,|qq|ckslidedataalignmenttext1|qq|:|qq|default|qq|,|qq|ckslidetargettext1|qq|:|qq|default|qq|,|qq|ckslideimgname2|qq|:|qq|/wp-content/plugins/slideshow-ck/slides/sea.jpg|qq|,|qq|ckslidedataalignmenttext2|qq|:|qq|default|qq|,|qq|ckslidetargettext2|qq|:|qq|default|qq|,|qq|ckaddslide1|qq|:|qq|Add a Slide|qq|,|qq|height|qq|:|qq|62%|qq|,|qq|width|qq|:|qq|auto|qq|,|qq|loader|qq|:|qq|1|qq|,|qq|navigation|qq|:|qq|1|qq|,|qq|thumbnails|qq|:|qq|1|qq|,|qq|pagination|qq|:|qq|1|qq|,|qq|theme|qq|:|qq|default|qq|,|qq|skin|qq|:|qq|camera_amber_skin|qq|,|qq|imgalignment|qq|:|qq|default|qq|,|qq|thumbnailheight|qq|:|qq|100|qq|,|qq|captiontitle_color|qq|:|qq|#000000|qq|,|qq|captiontitle_fontsize|qq|:|qq|18|qq|,|qq|captiontitle_fontfamily|qq|:|qq||qq|,|qq|captiontitle_fontweight|qq|:|qq|normal|qq|,|qq|captiontitle_fontweightnormal|qq|:|qq|normal|qq|,|qq|captiontitle_fontweightbold|qq|:|qq|bold|qq|,|qq|captiondesc_color|qq|:|qq|#6b6b6b|qq|,|qq|captiondesc_fontsize|qq|:|qq|12|qq|,|qq|captiondesc_fontfamily|qq|:|qq||qq|,|qq|captiondesc_fontweight|qq|:|qq|normal|qq|,|qq|captiondesc_fontweightnormal|qq|:|qq|normal|qq|,|qq|captiondesc_fontweightbold|qq|:|qq|bold|qq|,|qq|caption_bgcolor1|qq|:|qq|#000000|qq|,|qq|caption_bgcolor2|qq|:|qq||qq|,|qq|caption_opacity|qq|:|qq|0.8|qq|,|qq|caption_margintop|qq|:|qq||qq|,|qq|caption_marginright|qq|:|qq||qq|,|qq|caption_marginbottom|qq|:|qq||qq|,|qq|caption_marginleft|qq|:|qq||qq|,|qq|caption_paddingtop|qq|:|qq||qq|,|qq|caption_paddingright|qq|:|qq||qq|,|qq|caption_paddingbottom|qq|:|qq||qq|,|qq|caption_paddingleft|qq|:|qq||qq|,|qq|caption_roundedcornerstl|qq|:|qq||qq|,|qq|caption_roundedcornerstr|qq|:|qq||qq|,|qq|caption_roundedcornersbr|qq|:|qq||qq|,|qq|caption_roundedcornersbl|qq|:|qq||qq|,|qq|caption_bordercolor|qq|:|qq||qq|,|qq|caption_borderwidth|qq|:|qq||qq|,|qq|caption_shadowcolor|qq|:|qq||qq|,|qq|caption_shadowblur|qq|:|qq||qq|,|qq|caption_shadowspread|qq|:|qq||qq|,|qq|caption_shadowoffsetx|qq|:|qq||qq|,|qq|caption_shadowoffsety|qq|:|qq||qq|,|qq|caption_shadowinset|qq|:|qq||qq|,|qq|caption_shadowinset0|qq|:|qq|0|qq|,|qq|caption_shadowinset1|qq|:|qq|1|qq|,|qq|effect|qq|:[|qq|random|qq|],|qq|captioneffect|qq|:|qq|moveFromLeft|qq|,|qq|time|qq|:|qq|7000|qq|,|qq|transperiod|qq|:|qq|1500|qq|,|qq|portrait|qq|:|qq|0|qq|,|qq|portrait1|qq|:|qq|1|qq|,|qq|portrait0|qq|:|qq|0|qq|,|qq|autoAdvance|qq|:|qq|1|qq|,|qq|autoAdvance1|qq|:|qq|1|qq|,|qq|autoAdvance0|qq|:|qq|0|qq|,|qq|hover|qq|:|qq|1|qq|,|qq|hover1|qq|:|qq|1|qq|,|qq|hover0|qq|:|qq|0|qq|,|qq|displayorder|qq|:|qq|normal|qq|,|qq|fullpage|qq|:|qq|0|qq|,|qq|fullpage1|qq|:|qq|1|qq|,|qq|fullpage0|qq|:|qq|0|qq|,|qq|imagetarget|qq|:|qq|_parent|qq|}" />
<input type="hidden" id="demo-slides" name="demo-slides" value="[{|qq|imgname|qq|:|qq|wp-content/plugins/slideshow-ck/slides/bridge.jpg|qq|,|qq|title|qq|:|qq|This is a bridge|qq|,|qq|description|qq|:|qq|You can get more information about Slideshow CK for Wordpress on <a href='https://www.ceikay.com'>WP Plugins CK</a>|qq|,|qq|imglink|qq|:|qq||qq|,|qq|imgtarget|qq|:|qq|default|qq|,|qq|imgalignment|qq|:|qq|default|qq|,|qq|imgvideo|qq|:|qq||qq|,|qq|imgtime|qq|:|qq||qq|},{|qq|imgname|qq|:|qq|wp-content/plugins/slideshow-ck/slides/road.jpg|qq|,|qq|title|qq|:|qq|On the road again|qq|,|qq|description|qq|:|qq|When the sky is blue, the rain will come.|qq|,|qq|imglink|qq|:|qq||qq|,|qq|imgtarget|qq|:|qq|default|qq|,|qq|imgalignment|qq|:|qq|default|qq|,|qq|imgvideo|qq|:|qq||qq|,|qq|imgtime|qq|:|qq||qq|},{|qq|imgname|qq|:|qq|wp-content/plugins/slideshow-ck/slides/sea.jpg|qq|,|qq|title|qq|:|qq||qq|,|qq|description|qq|:|qq||qq|,|qq|imglink|qq|:|qq||qq|,|qq|imgtarget|qq|:|qq|default|qq|,|qq|imgalignment|qq|:|qq|default|qq|,|qq|imgvideo|qq|:|qq||qq|,|qq|imgtime|qq|:|qq||qq|}]" />
<div id="ckoptionswrapper" class="ckinterface<?php echo $modalclass ?>">
	<form id="slideshowck-edit" method="post" action="">
		<a href="<?php echo SLIDESHOWCK_WEBSITE ?>" target="_blank" style="text-decoration:none;"><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/logo_slideshowck_64.png" style="margin: 5px;" class="cklogo" /><span class="cktitle">Slideshow CK - <?php echo __('Edit') . ' <span class="small">[ ' . $post_title . ' ]</span>'; ?></span></a>
		<div style="clear:both;"></div>
			<input type="button" class="button button-primary" name="ckSaveSlideshowck" value="<?php esc_attr_e('Save'); ?>" onclick="ckSaveSlideshow()" />
			<a type="button" class="button" href="https://www.ceikay.com/documentation/slideshow-ck" target="_blank" ><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/page_white_acrobat.png" style="width:16px;height:16px;vertical-align:text-bottom;" /> <?php _e('Documentation'); ?></a>
		</h2>
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="appendurl" value="<?php echo ($input->get('modal', '', 'string') === '1' ? '&modal=1' : '') ?>"/>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('slideshowck_save'); ?>" />
		<input type="hidden" name="ID" value="<?php echo $post_id ?>" />
		<input type="hidden" name="post_content" id="post_content" value="" />
		<div style="clear:both;margin-top:10px; "></div>
		<label for="post_title"><?php _e('Name', 'slideshow-ck'); ?></label>
		<input type="text" name="post_title" id="post_title" value="<?php echo $post_title ?>" />
		<a class="button" href="javascript:void(0)" onclick="ckLoadDemoData()"><img src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/magic.png" style="margin: -1px 3px 0 0;vertical-align: middle;" /><?php _e('Load demo data', 'slideshow-ck'); ?></a>
		<input type="hidden" name="slideshow-ck-params" id="slideshow-ck-params" value="<?php echo get_post_meta($post_id, 'slideshow-ck-params', TRUE); ?>" />
		<input type="hidden" name="slideshow-ck-slides" id="slideshow-ck-slides" value="<?php echo get_post_meta($post_id, 'slideshow-ck-slides', TRUE); ?>" />
	</form>
<div id="slideshowedition" class="ckinterface">
	<div class="nav-tab-wrapper">
		<div class="menulinkck nav-tab nav-tab-active current" tab="tab_images"><?php _e('Images', 'slideshow-ck'); ?></div>
		<div class="menulinkck nav-tab" tab="tab_styles"><?php _e('Styles', 'slideshow-ck'); ?></div>
		<div class="menulinkck nav-tab" tab="tab_effects"><?php _e('Effects', 'slideshow-ck'); ?></div>
		<div class="menulinkck nav-tab" tab="tab_options"><?php _e('Options', 'slideshow-ck'); ?></div>
	</div>
	<div class="tabck menustyles current" id="tab_images">
		<!--<input type="hidden" name="slides_sources" id="slides_sources" value="slidemanager" />-->
		<div class="saveparam">
		<?php
			$options_slides_sources = array(
			'slidemanager' => __('Slides Manager', 'slideshow-ck')
			, 'autoloadfolder' => __('Autoload from a folder', 'slideshow-ck')
			, 'flickr' => __('Load a Flickr album', 'slideshow-ck')
			, 'posts' => __('Load from posts', 'slideshow-ck')
			, 'woocommerce' => __('Load Woocommerce products', 'slideshow-ck')
			, 'google' => __('Load Google photos', 'slideshow-ck')
			);
			echo $this->fields->render('select', 'slides_sources', $this->get_param('slides_sources'), $options_slides_sources, '', false, 'onchange="ckShowSlidesSources();"');
		?>
		</div>
		<div class="slides_source" data-source="slidemanager">
			<div id="ckslides">
				<input name="ckaddslide" id="ckaddslide" type="button" value="<?php _e('Add a Slide', 'slideshow-ck') ?>" class="button button-secondary" onclick="ckAddSlide();"/>
				<span id="addslide_waiticon"></span>
				<div>
					<label for="importfoldername"><?php _e('Import slides from a folder', 'slideshow-ck'); ?></label>
					<?php echo Helper::renderProMessage(); ?>
				</div>
				
				<?php
				if (get_post_meta($post_id, 'slideshow-ck-slides', TRUE)) {
					$slides = json_decode(str_replace('|qq|', '"', get_post_meta((int) $post_id, 'slideshow-ck-slides', TRUE)));
					if ($slides && count($slides)) {
						foreach ($slides as $i => $slide) {
							$this->add_slide($i+1, $slide);
						}
					}
				}
				?>
			</div>
			<input name="ckaddslide" id="ckaddslide1" type="button" value="<?php _e('Add a Slide', 'slideshow-ck') ?>" class="button button-secondary" onclick="ckAddSlide();"/>
		</div>
		<div class="slides_source saveparam" data-source="autoloadfolder">
			<?php 
				echo Helper::renderProMessage();
			?>
		</div>
		<div class="slides_source saveparam" data-source="flickr">
			<?php 
				echo Helper::renderProMessage();
			?>
		</div>
		<div class="slides_source saveparam" data-source="posts">
			<?php 
				echo Helper::renderProMessage();
			?>
		</div>
		<div class="slides_source saveparam" data-source="woocommerce">
			<?php 
				echo Helper::renderProMessage();
			?>
		</div>
		<div class="slides_source saveparam" data-source="google">
			<?php 
				echo Helper::renderProMessage();
			?>
		</div>
	</div>
	<div class="tabck menustyles saveparam" id="tab_styles">
		<div style="background: #fff;border:1px solid #ddd;">
			<div style="background: url(<?php echo SLIDESHOWCK_MEDIA_URL; ?>/images/slideshowck_styles.png) 100px 50px no-repeat; width:550px;height:360px;position:relative;margin:0px auto 10px auto;">
				<div style="position:absolute;left:10px;top:150px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Height', 'slideshow-ck') ?></div>
					<input id="height" type="text" value="<?php echo $this->get_param('height'); ?>" name="height" style="">
				</div>
				<div style="position:absolute;left:220px;top:40px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Width', 'slideshow-ck') ?></div>
					<input id="width" type="text" value="<?php echo $this->get_param('width'); ?>" name="width" style="">
				</div>
				<?php
				$options_yes_no = array(
					'1' => __('Yes')
					, '0' => __('No')
				);
				?>
				<div style="position:absolute;left:420px;top:20px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Loader icon', 'slideshow-ck') ?></div>
					<?php
					$options_loader = array('pie' => 'Pie'
						, 'bar' => __('Bar', 'slideshow-ck')
						, 'none' => __('None', 'slideshow-ck')
						);
					echo $this->fields->render('select', 'loader', $this->get_param('loader'), $options_loader);
					?>
				</div>
				<div style="position:absolute;left:85px;top:320px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Navigation', 'slideshow-ck') ?></div>
					<?php
					$options_navigation = array(
						'2' => __('On mouseover', 'slideshow-ck')
						, '1' => __('Always', 'slideshow-ck')
						, '0' => __('None', 'slideshow-ck')
						);
					echo $this->fields->render('select', 'navigation', $this->get_param('navigation'), $options_navigation);
					?>
				</div>
				<div style="position:absolute;left:220px;top:320px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Thumbnails', 'slideshow-ck') ?></div>
					<?php
					echo $this->fields->render('select', 'thumbnails', $this->get_param('thumbnails'), $options_yes_no);
					?>
				</div>
				<div style="position:absolute;left:420px;top:320px;width:105px;">
					<div style="position:absolute;left:5px;top:-18px;"><?php _e('Pagination', 'slideshow-ck') ?></div>
					<?php
					echo $this->fields->render('select', 'pagination', $this->get_param('pagination'), $options_yes_no);
					?>
				</div>
			</div>
		</div>
		<div>
			<label for="theme"><?php _e('Theme', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/photo.png" />
			<?php echo $this->fields->render('select', 'theme', null, CKFolder::folders(SLIDESHOWCK_PATH . '/themes'), '', true); ?>
		</div>
		<div>
			<label for="skin"><?php _e('Skin', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/palette.png" />
			<?php
			$options_skin = array('camera_amber_skin' => 'camera_amber_skin',
				'camera_ash_skin' => 'camera_ash_skin',
				'camera_azure_skin' => 'camera_azure_skin',
				'camera_beige_skin' => 'camera_beige_skin',
				'camera_black_skin' => 'camera_black_skin',
				'camera_blue_skin' => 'camera_blue_skin',
				'camera_brown_skin' => 'camera_brown_skin',
				'camera_burgundy_skin' => 'camera_burgundy_skin',
				'camera_charcoal_skin' => 'camera_charcoal_skin',
				'camera_chocolate_skin' => 'camera_chocolate_skin',
				'camera_coffee_skin' => 'camera_coffee_skin',
				'camera_cyan_skin' => 'camera_cyan_skin',
				'camera_fuchsia_skin' => 'camera_fuchsia_skin',
				'camera_gold_skin' => 'camera_gold_skin',
				'camera_green_skin' => 'camera_green_skin',
				'camera_grey_skin' => 'camera_grey_skin',
				'camera_indigo_skin' => 'camera_indigo_skin',
				'camera_khaki_skin' => 'camera_khaki_skin',
				'camera_lime_skin' => 'camera_lime_skin',
				'camera_magenta_skin' => 'camera_magenta_skin',
				'camera_maroon_skin' => 'camera_maroon_skin',
				'camera_orange_skin' => 'camera_orange_skin',
				'camera_olive_skin' => 'camera_olive_skin',
				'camera_pink_skin' => 'camera_pink_skin',
				'camera_pistachio_skin' => 'camera_pistachio_skin',
				'camera_pink_skin' => 'camera_pink_skin',
				'camera_red_skin' => 'camera_red_skin',
				'camera_tangerine_skin' => 'camera_tangerine_skin',
				'camera_turquoise_skin' => 'camera_turquoise_skin',
				'camera_violet_skin' => 'camera_violet_skin',
				'camera_white_skin' => 'camera_white_skin',
				'camera_yellow_skin' => 'camera_yellow_skin');
			echo $this->fields->render('select', 'skin', $this->get_param('skin'), $options_skin);
			?>
		</div>
		<div>
			<label for="thumbnailheight"><?php _e('Thumbnail height', 'slideshow-ck') ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/height.png" />
			<input id="thumbnailheight" name="thumbnailheight" type="text" value="<?php echo $this->get_param('thumbnailheight') ?>" >
		</div>
			<div class="ckheading"><?php _e('Caption Title', 'slideshow-ck'); ?></div>
			<div>
				<label for="captiontitle_color"><?php _e('Title Color', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/color.png" />
				<?php echo $this->fields->render('color', 'captiontitle_fontcolor') ?>
			</div>
			<div>
				<label for="captiontitle_fontsize"><?php _e('Font Size', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_fontsize.png" />
				<?php echo $this->fields->render('text', 'captiontitle_fontsize') ?>
			</div>
			<div>
				<label for="captiontitle_fontfamily"><?php _e('Font Family', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/style.png" />
				<?php echo $this->fields->render('text', 'captiontitle_fontfamily') ?>
			</div>
			<div>
				<label for="captiontitle_fontweight"><?php _e('Font Weight', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_bold.png" />
				<?php
				$options_fontweight = array('normal' => __('Normal', 'slideshow-ck'), 'bold' => __('Bold', 'slideshow-ck'));
				echo $this->fields->render('radio', 'captiontitle_fontweight', '', $options_fontweight);
				?>
			</div>
			<div class="ckheading"><?php _e('Caption Description', 'slideshow-ck'); ?></div>
			<div>
				<label for="captiondesc_color"><?php _e('Description Color', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/color.png" />
				<?php echo $this->fields->render('color', 'captiondesc_fontcolor') ?>
			</div>
			<div>
				<label for="captiondesc_fontsize"><?php _e('Font Size', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_fontsize.png" />
				<?php echo $this->fields->render('text', 'captiondesc_fontsize') ?>
			</div>
			<div>
				<label for="captiondesc_fontfamily"><?php _e('Font Family', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/style.png" />
				<?php echo $this->fields->render('text', 'captiondesc_fontfamily') ?>
				<?php /* if ($this->ispro) : ?>
									<br />
									<label for="title_googlefont"><?php _e('Google Font'); ?></label>
									<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/google.png" />
					<?php echo $this->fields->render('text', 'captiondesc_googlefont', $this->get_param('captiondesc_googlefont')) ?>
									<a class="button btn-primary btn" href="javascript:void(0)" onclick="ck_load_googlefont();" title="Example: <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>"><?php _e('Import'); ?></a>
									<span id="captiondesc_googlefont_wait" style="height:16px;width:16px;"></span>
				<?php endif; */ ?>
			</div>
			<div>
				<label for="captiondesc_fontweight"><?php _e('Font Weight', 'slideshow-ck'); ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_bold.png" />
				<?php
				$options_fontweight = array('normal' => __('Normal', 'slideshow-ck'), 'bold' => __('Bold', 'slideshow-ck'));
				echo $this->fields->render('radio', 'captiondesc_fontweight', '', $options_fontweight);
				?>
			</div>
			<div class="ckheading"><?php _e('Caption Styles', 'slideshow-ck'); ?></div>
			<div>
				<label for="caption_bgcolor1"><?php _e('Background Color', 'slideshow-ck') ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/color.png" />
<?php echo $this->fields->render('color', 'caption_bgcolor1') ?>
<?php echo $this->fields->render('color', 'caption_bgcolor2') ?>
			</div>
			<div>
				<label for="caption_opacity"><?php _e('Opacity', 'slideshow-ck') ?></label>
				<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/layers.png" />
<?php echo $this->fields->render('text', 'caption_bgopacity') ?>
			</div>
			<div>
				<label for="caption_margintop"><?php _e('Margin', 'slideshow-ck'); ?></label>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/margin_top.png" /></span>
				<span style="width:35px;" caption="<?php _e('Top', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_margintop') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/margin_right.png" /></span>
				<span style="width:35px;" caption="<?php _e('Right', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_marginright') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/margin_bottom.png" /></span>
				<span style="width:35px;" caption="<?php _e('Bottom', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_marginbottom') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/margin_left.png" /></span>
				<span style="width:35px;" caption="<?php _e('Left', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_marginleft') ?></span>
			</div>
			<div>
				<label for="caption_paddingtop"><?php _e('Padding', 'slideshow-ck'); ?></label>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/padding_top.png" /></span>
				<span style="width:35px;" caption="<?php _e('Top', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_paddingtop') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/padding_right.png" /></span>
				<span style="width:35px;" caption="<?php _e('Right', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_paddingright') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/padding_bottom.png" /></span>
				<span style="width:35px;" caption="<?php _e('Bottom', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_paddingbottom') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/padding_left.png" /></span>
				<span style="width:35px;" caption="<?php _e('Left', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_paddingleft') ?></span>
			</div>
			<div>
				<label for="caption_roundedcornerstl"><?php _e('Border Radius', 'slideshow-ck'); ?></label>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/border_radius_tl.png" /></span>
				<span style="width:35px;" title="<?php _e('Top Left', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_roundedcornerstl') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/border_radius_tr.png" /></span>
				<span style="width:35px;" title="<?php _e('Top Right', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_roundedcornerstr') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/border_radius_br.png" /></span>
				<span style="width:35px;" title="<?php _e('Bottom Right', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_roundedcornersbr') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/border_radius_bl.png" /></span>
				<span style="width:35px;" title="<?php _e('Bottom Left', 'slideshow-ck'); ?>"><?php echo $this->fields->render('text', 'caption_roundedcornersbl') ?></span>
			</div>
			<div>
				<label for="caption_bordercolor"><?php _e('Border', 'slideshow-ck'); ?></label>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/color.png" /></span>
				<span><?php echo $this->fields->render('color', 'caption_bordercolor', $this->get_param('caption_bordercolor')) ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/shape_square.png" /></span>
				<span style="width:35px;"><?php echo $this->fields->render('text', 'caption_borderwidth') ?></span>
			</div>
			<div>
				<label for="caption_shadowcolor"><?php _e('Shadow', 'slideshow-ck'); ?></label>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/color.png" /></span>
				<span><?php echo $this->fields->render('color', 'caption_shadowcolor') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/shadow_blur.png" /></span>
				<span style="width:35px;"><?php echo $this->fields->render('text', 'caption_shadowblur') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/shadow_spread.png" /></span>
				<span style="width:35px;"><?php echo $this->fields->render('text', 'caption_shadowspread') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/offsetx.png" /></span>
				<span style="width:35px;"><?php echo $this->fields->render('text', 'caption_shadowoffsetx') ?></span>
				<span><img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/offsety.png" /></span>
				<span style="width:35px;"><?php echo $this->fields->render('text', 'caption_shadowoffsety') ?></span>
				<?php
				$optionsboxshadowinset = array('0' => __('Out', 'slideshow-ck'), '1' => __('In', 'slideshow-ck'));
				echo $this->fields->render('radio', 'caption_shadowinset', '', $optionsboxshadowinset);
				?>
			</div>
	</div>
	<div class="tabck menustyles saveparam" id="tab_effects">
		<div>
			<label for="effect"><?php _e('Animation', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/application_view_gallery.png" />
			<?php
			$options_effect = '<option value="random">random</option>
					<option value="simpleFade">simpleFade</option>
					<option value="curtainTopLeft">curtainTopLeft</option>
					<option value="curtainTopRight">curtainTopRight</option>
					<option value="curtainBottomLeft">curtainBottomLeft</option>
					<option value="curtainBottomRight">curtainBottomRight</option>
					<option value="curtainSliceLeft">curtainSliceLeft</option>
					<option value="curtainSliceRight">curtainSliceRight</option>
					<option value="blindCurtainTopLeft">blindCurtainTopLeft</option>
					<option value="blindCurtainTopRight">blindCurtainTopRight</option>
					<option value="blindCurtainBottomLeft">blindCurtainBottomLeft</option>
					<option value="blindCurtainBottomRight">blindCurtainBottomRight</option>
					<option value="blindCurtainSliceBottom">blindCurtainSliceBottom</option>
					<option value="blindCurtainSliceTop">blindCurtainSliceTop</option>
					<option value="stampede">stampede</option>
					<option value="mosaic">mosaic</option>
					<option value="mosaicReverse">mosaicReverse</option>
					<option value="mosaicRandom">mosaicRandom</option>
					<option value="mosaicSpiral">mosaicSpiral</option>
					<option value="mosaicSpiralReverse">mosaicSpiralReverse</option>
					<option value="topLeftBottomRight">topLeftBottomRight</option>
					<option value="bottomRightTopLeft">bottomRightTopLeft</option>
					<option value="bottomLeftTopRight">bottomLeftTopRight</option>
					<option value="bottomLeftTopRight">bottomLeftTopRight</option>
					<option value="scrollLeft">scrollLeft</option>
					<option value="scrollRight">scrollRight</option>
					<option value="scrollHorz">scrollHorz</option>
					<option value="scrollBottom">scrollBottom</option>
					<option value="scrollTop">scrollTop</option>';
			echo $this->fields->render('select', 'effect', null, $options_effect, false, false ,' multiple="true"')
			?>
		</div>
		<div>
			<label for="captioneffect"><?php _e('Caption animation', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/application_view_gallery.png" />
			<?php
			$options_captioneffect = '<option value="moveFromLeft">moveFromLeft</option>
					<option value="moveFromRight">moveFromRight</option>
					<option value="moveFromTop">moveFromTop</option>
					<option value="moveFromBottom">moveFromBottom</option>
					<option value="fadeIn">fadeIn</option>
					<option value="fadeFromLeft">fadeFromLeft</option>
					<option value="fadeFromRight">fadeFromRight</option>
					<option value="fadeFromTop">fadeFromTop</option>
					<option value="fadeFromBottom">fadeFromBottom</option>
					<option value="none">none</option>';
			echo $this->fields->render('select', 'captioneffect', null, $options_captioneffect)
			?>
		</div>
		<div>
			<label for="time"><?php _e('Slide duration', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/hourglass.png" />
			<?php echo $this->fields->render('text', 'time', $this->get_param('time')) ?> ms
		</div>
		<div>
			<label for="transperiod"><?php _e('Transition duration', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/hourglass.png" />
			<?php echo $this->fields->render('text', 'transperiod') ?> ms
		</div>
		<div>
			<label for="portrait"><?php _e('Keep image size', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/shape_handles.png" />
<?php
echo $this->fields->render('radio', 'portrait', $this->get_param('portrait'), $options_yes_no);
?>
		</div>
		<div>
			<label for="autoAdvance"><?php _e('Autoplay', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/control_play.png" />
<?php
echo $this->fields->render('radio', 'autoAdvance', $this->get_param('autoAdvance'), $options_yes_no);
?>
		</div>
		<div>
			<label for="hover"><?php _e('Pause on mouseover', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/control_pause.png" />
<?php
echo $this->fields->render('radio', 'hover', null, $options_yes_no);
?>
		</div>
		<div>
			<label for="fullpage"><?php _e('Full page background', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/arrow_out.png" />
<?php
echo $this->fields->render('radio', 'fullpage', null, $options_yes_no);
?>
		</div>
		<div>
			<label for="container"><?php _e('Container background', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/arrow_out.png" />
<?php
echo $this->fields->render('text', 'container');
?>
			<span class="description"><?php _e('Write the css selector (e.g. : #main) of the element where to place the slideshow', 'slideshow-ck'); ?></span>
		</div>
	</div>
	<div class="tabck menustyles saveparam" id="tab_options">
		<div class="ckheading"><?php _e('Text', 'slideshow-ck'); ?></div>
		<div>
			<label for="showcaption"><?php _e('Show caption', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/switch.png" />
			<?php
			echo $this->fields->render('radio', 'showcaption', null, $options_yes_no);
			?>
		</div>
		<div>
			<label for="showtitle"><?php _e('Show title', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/switch.png" />
			<?php
			echo $this->fields->render('radio', 'showtitle', null, $options_yes_no);
			?>
		</div>
		<div>
			<label for="showdescription"><?php _e('Show description', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/switch.png" />
			<?php
			echo $this->fields->render('radio', 'showdescription', null, $options_yes_no);
			?>
		</div>
		<div>
			<label for="textlength"><?php _e('Text length', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_signature.png" />
			<?php
			echo $this->fields->render('text', 'textlength');
			?>
		</div>
		<div class="ckheading"><?php _e('Slides', 'slideshow-ck'); ?></div>
		<div>
			<label for="numberslides"><?php _e('Number of slides', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_signature.png" />
			<?php
			echo $this->fields->render('text', 'numberslides');
			?>
		</div>
		<div>
			<label for="displayorder"><?php _e('Display order', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/control_repeat.png" />
<?php
$options_displayorder = '<option value="normal">' . __('Normal', 'slideshow-ck') . '</option>
					<option value="shuffle">' . __('Random', 'slideshow-ck') . '</option>';
echo $this->fields->render('select', 'displayorder', null, $options_displayorder)
?>
		</div>
		<div class="ckheading"><?php _e('Link', 'slideshow-ck'); ?></div>
		<div>
			<label for="linkposition"><?php _e('Link position', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/link.png" />
			<?php
			$options_linkposition = '<option value="fullslide">' . __('Full slide', 'slideshow-ck') . '</option>
								<option value="caption">' . __('Caption', 'slideshow-ck') . '</option>
								<option value="title">' . __('Title', 'slideshow-ck') . '</option>
								<option value="button">' . __('Button', 'slideshow-ck') . '</option>
								<option value="none">' . __('None', 'slideshow-ck') . '</option>';
			echo $this->fields->render('select', 'linkposition', null, $options_linkposition)
			?>
		</div>
		<div>
			<label for="linkbuttontext"><?php _e('Button text', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_signature.png" />
			<?php
			echo $this->fields->render('text', 'linkbuttontext');
			?>
		</div>
		<div>
			<label for="linkbuttonclass"><?php _e('Button CSS class', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/css.png" />
			<?php
			echo $this->fields->render('text', 'linkbuttonclass');
			?>
		</div>
		<div>
			<label for="linkautoimage"><?php _e('Link auto to image', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/link.png" />
			<?php
			echo $this->fields->render('radio', 'linkautoimage', null, $options_yes_no);
			?>
		</div>
		<div>
			<label for="imagetarget"><?php _e('Image link target', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/link_go.png" />
<?php

	$options_imagetarget = array(
		'_parent' => __('Open in the same window', 'slideshow-ck')
		, '_blank' => __('Open in a new window', 'slideshow-ck')
		, 'lightbox'=> __('Open in a Lightbox', 'slideshow-ck')
	);

echo $this->fields->render('select', 'imagetarget', null, $options_imagetarget)
?>
			<span class="description"><?php _e('The Lightbox option can be used with any plugin that uses the rel attribute "lightbox". You can use the plugin ', 'slideshow-ck'); ?><a href="https://www.ceikay.com/en/wordpress-plugins/mediabox-ck" target="_blank">Mediabox CK</a></span>
			<?php
			echo Helper::renderProMessage();
			?>
		</div>
		<div class="ckheading"><?php _e('Lightbox', 'slideshow-ck'); ?></div>
		<div>
			<label for="lightbox"><?php _e('Enable Lightbox', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/magnifier.png" />
			<?php
			echo $this->fields->render('radio', 'lightbox', null, $options_yes_no);
			?>
		</div>
		<div>
			<label for="lightboxattribvalue"><?php _e('Lightbox attribute value', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/text_signature.png" />
			<?php
			echo $this->fields->render('text', 'lightboxattribvalue');
			?>
		</div>
		<div>
			<label for="lightboxcaption"><?php _e('Show the caption', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/switch.png" />
			<?php
			echo Helper::renderProMessage();
			?>
		</div>
		<div>
			<label for="lightboxgroupalbum"><?php _e('Group links into an album', 'slideshow-ck'); ?></label>
			<img class="iconck" src="<?php echo SLIDESHOWCK_MEDIA_URL ?>/images/images.png" />
			<?php
			echo Helper::renderProMessage();
			?>
		</div>
	</div>
</div>
</div>
<?php echo $this->copyright() ?>
<div id="ckbrowser"></div>
<script type="text/javascript">
	jQuery('#slideshowedition > div.tabck:not(.current)').hide();
	jQuery('#slideshowedition > div > .menulinkck').each(function(i, tab) {
		jQuery(tab).click(function() {
			jQuery('#slideshowedition > div.tabck').hide();
			jQuery('#slideshowedition > div > .menulinkck').removeClass('current').removeClass('nav-tab-active');
			if (jQuery('#' + jQuery(tab).attr('tab')).length)
				jQuery('#' + jQuery(tab).attr('tab')).show();
			jQuery(this).addClass('current nav-tab-active');
		});
	});

	function ckAddSlide() {
		var data = {
			action: 'slideshowck_add_slide',
			number: jQuery('.ckslide').length
		};
		jQuery('#addslide_waiticon').addClass('ckwait_mini');
		jQuery.post(ajaxurl, data, function(response) {
			response = jQuery(response);
			jQuery('#ckslides').append(response);
			jQuery('#addslide_waiticon').removeClass('ckwait_mini');
			ckCreateTabsInSlide(response);
		});
	}

	function ckAddImageUrToSlide(button, url) {
		button = jQuery(button);
		url_relative = url.replace('<?php echo get_site_url(); ?>/', '');
		var ckslide = jQuery(button.parents('.ckslide')[0]);
		ckslide.find('.ckslideimgname').val(url_relative);
		var url = ckCheckExternalUrl(url_relative) ? url_relative : '<?php echo get_site_url(); ?>/' + url_relative;
		ckslide.find('.ckslideimgthumb').attr('src', url);
	}

	function ckCheckExternalUrl(url) {
		if (url.indexOf('http') == 0) return true;
		return false;
	}

	function ckRenumberSlides() {
		var index = 1;
		jQuery('#ckslides .ckslide').each(function(i, slide) {
			jQuery('.ckslidenumber', jQuery(slide)).html(index);
			index++;
		});
	}

	jQuery(document).ready(function($) {
//		ckRenumberSlides();
		ckShowSlidesSources();

		jQuery("#ckslides").sortable({
			placeholder: "ui-state-highlight",
			handle: ".ckslidehandle",
			items: ".ckslide",
			axis: "y",
			forcePlaceholderSize: true,
			forceHelperSize: true,
			dropOnEmpty: true,
			tolerance: "pointer",
			placeholder: "placeholder",
					zIndex: 9999,
			update: function(event, ui) {
				ckRenumberSlides();
			}
		});

		jQuery('#ckslides .ckslide').each(function(i, slide) {
			slide = jQuery(slide);
			ckCreateTabsInSlide(slide);
		});

		var modalpopup = jQuery('#ckoptionswrapper.ckmodal');
		if (modalpopup.length) {
			jQuery(document.body).prepend(modalpopup);
		}
	});
</script>
