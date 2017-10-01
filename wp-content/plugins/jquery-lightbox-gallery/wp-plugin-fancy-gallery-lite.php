<?php
/*
Plugin Name: Fancy Gallery Lite
Plugin URI: http://dennishoppe.de/en/wordpress-plugins/fancy-gallery
Description: Fancy Gallery Lite enables you to create galleries and converts your galleries in post and pages to valid HTML blocks and associates linked images with the Fancy Light Box.
Version: 1.0.22
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

If (!Class_Exists('wp_plugin_fancy_gallery')){
Class wp_plugin_fancy_gallery {
  var $base_url; // url to the plugin directory
  var $version = '1.0.22'; // Current release number
  var $arr_option_box; // Meta boxes for the option page
  var $arr_gallery_meta_box; // Meta boxes for the gallery post type
  var $arr_taxonomies; // All buildIn Gallery Taxonomies - also the inactive ones.
  var $gallery_post_type = 'fancy-gallery'; // Name of the gallery post type
  var $gallery; // The current gallery object while running shortcode

  function __construct(){
    // Read base
    $this->base_url = get_bloginfo('wpurl').'/'.SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH));
    $this->base_url = Str_Replace("\\", '/', $this->base_url);

    // Option boxes
    $this->arr_option_box = Array( 'main' => Array(), 'side' => Array() );

    // Meta Boxes
    $this->arr_gallery_meta_box = Array();

    // Get ready to translate
    Add_Action('widgets_init', Array($this, 'Load_TextDomain'));

    // This Plugin supports post thumbnails
    Add_Theme_Support('post-thumbnails');

    // Set Hooks
    Register_Activation_Hook(__FILE__, Array($this, 'Plugin_Activation'));
    Add_Action('init', Array($this, 'Register_Gallery_Post_Type'));
    Add_Action('init', Array($this, 'Register_Gallery_Taxonomies'));
    Add_Action('init', Array($this, 'Add_Taxonomy_Archive_Urls'), 99);
    Add_Action('init', Array($this, 'Add_GetTextFilter'), 99);
    Add_Filter('post_updated_messages', Array($this, 'Gallery_Updated_Messages' ));
    Add_Action('admin_menu', Array($this, 'Add_Options_Page'));
    Add_Filter('the_content', Array($this, 'Filter_Content'), 9  );
    Add_Filter('the_content_feed', Array($this, 'Filter_Feed_Content'));
    Add_Filter('the_excerpt_rss', Array($this, 'Filter_Feed_Content'));
    Add_Filter('image_upload_iframe_src', Array($this, 'Image_Upload_Iframe_Src'));
    Add_Filter('post_class', Array($this, 'Filter_Post_Class'));
    Add_Action('wp_enqueue_scripts', Array($this, 'enqueue_frontend_scripts'));
    Add_Action('admin_enqueue_scripts', Array($this, 'enqueue_admin_scripts'));
    Add_Action('media_upload_gallery', Array($this, 'Media_Upload_Tab') );
    Add_Action('save_post', Array($this, 'Save_Meta_Box')    );
    Add_Action('admin_init', Array($this, 'User_Creates_New_Gallery'));
    Add_Action('untrash_post', Array($this, 'User_Untrashes_Post'));
    Add_Filter('views_edit-fancy-gallery', Array($this, 'Add_Gallery_Count_Notice'));

    Add_ShortCode('gallery', Array($this, 'ShortCode_Gallery'));

    If (IsSet($_REQUEST['strip_tabs'])){
      Add_Action('media_upload_gallery', Array($this, 'Add_Media_Upload_Style'));
      Add_Action('media_upload_image', Array($this, 'Add_Media_Upload_Style'));
      Add_Filter('media_upload_tabs', Array($this, 'Media_Upload_Tabs'));
      Add_Filter('media_upload_form_url', Array($this, 'Media_Upload_Form_URL'));
      Add_Action('media_upload_import_images', Array($this, 'Import_Images'));
    }

    If (!$this->get_option('disable_excerpts')){
      Add_Filter('get_the_excerpt', Array($this, 'Filter_Excerpt'), 9 );
    }

    // Add to GLOBALs
    $GLOBALS[__CLASS__] = $this;
  }

  function Plugin_Activation(){
    $this->Load_TextDomain();
    $this->Register_Gallery_Post_Type();
    Flush_Rewrite_Rules();
  }

  function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    Load_TextDomain (__CLASS__, DirName(__FILE__).'/language/' . $locale . '.mo');
  }

  function t ($text, $context = ''){
    // Translates the string $text with context $context
    If ($context == '')
      return Translate ($text, __CLASS__);
    Else
      return Translate_With_GetText_Context ($text, $context, __CLASS__);
  }

  function Enqueue_Frontend_Scripts(){
    WP_Enqueue_Script('jquery');
    WP_Enqueue_Script('jquery.easing', $this->base_url . '/js/jquery.easing.js', Array('jquery'), '1.3', ($this->get_option('script_position') != 'header') );
    WP_Enqueue_Script('jquery.mousewheel', $this->base_url . '/js/jquery.mousewheel.js', Array('jquery'), '3.0.6', ($this->get_option('script_position') != 'header') );
    WP_Enqueue_Script('fancybox', $this->base_url . '/fancybox/jquery.fancybox.js', Array('jquery', 'jquery.easing'), '1.3.4', ($this->get_option('script_position') != 'header') );
    WP_Enqueue_Script('fancy-gallery', $this->base_url . '/fancy-js.php', Array('jquery', 'fancybox'), $this->version, ($this->get_option('script_position') != 'header') );
    WP_Enqueue_Style('fancybox', $this->base_url . '/fancybox/jquery.fancybox.css', Null, '1.3.4');
    WP_Enqueue_Style('fancybox-ie-fix', $this->base_url . '/fancybox/jquery.fancybox.css-png-fix.php', Null, '1.3.4');
    WP_Enqueue_Style('fancy-gallery', $this->base_url . '/fancy-gallery.css', Null, $this->version);

    // Enqueue Template Stylesheets
    ForEach ($this->Get_Template_Files() AS $template_name => $template_properties){
      $style_sheet_name = BaseName($template_properties['file'], '.php') . '.css';
      $style_sheet_file = DirName($template_properties['file']) . '/' . $style_sheet_name;
      If (!Is_File($style_sheet_file)) Continue;
      $template_dir = DirName($style_sheet_file);
      $style_sheet_id = 'fancy-gallery-template-'.Sanitize_Title($template_name);

      $template_base_url = Get_Bloginfo('wpurl').'/'.SubStr($template_dir, Strlen(ABSPATH));
      $template_base_url = Str_Replace("\\", '/', $template_base_url);
      WP_Enqueue_Style($style_sheet_id, $template_base_url . '/' . $style_sheet_name);
    }
  }

  function Enqueue_Admin_Scripts(){
    WP_Enqueue_Style('fancy-gallery-icon', $this->base_url . '/fancy-gallery-icon.css');
    WP_Enqueue_Script('livequery', $this->base_url.'/js/jquery.livequery.js', Array('jquery'), '1.1.1', True);
    WP_Enqueue_Script('fancy-gallery-media-gallery-settings', $this->base_url . '/js/gallery-settings.js', Array('jquery'), Null, True );
  }

  function Add_GetTextFilter(){
    Global $pagenow;
    If (($pagenow == 'async-upload.php' || $pagenow == 'media-upload.php')){
      If (IsSet($_REQUEST['post_id'])){
        $post = Get_Post(IntVal($_REQUEST['post_id']));
      }
      ElseIf (IsSet($_REQUEST['attachment_id'])){
        $attachment = Get_Post(IntVal($_REQUEST['attachment_id']));
        $post = Get_Post($attachment->post_parent);
      }

      If ($post->post_type == $this->gallery_post_type)
        Add_Filter ( 'gettext', Array($this, 'Filter_GetText'), 10, 3 );
    }
  }

  function Filter_GetText($translation, $text, $domain = 'default'){
    If ($domain == 'default'){
      $arr_replace = Array(
        'Set featured image' => $this->t('Set Gallery Thumbnail'),
        'Remove featured image' => $this->t('Remove Gallery Thumbnail'),
        'Use as featured image' => $this->t('Use as Gallery Thumbnail')
      );
      If (IsSet($arr_replace[$text])) return $arr_replace[$text];
    }
    return $translation;
  }

  function Media_Upload_Tab(){
    WP_Enqueue_Script('fancy-gallery-media-upload', $this->base_url . '/media-upload.js', Array('jquery') );
  }

  function Field_Name($option_name){
    // Generates field names for the meta box
    return __CLASS__ . '[' . $option_name . ']';
  }

  function Save_Meta_Box($post_id){
    Global $post;

    // If this is an autosave we dont care
    If ( Defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    // Check the PostType
    If ($post->post_type != $this->gallery_post_type) return;

    // Check if this request came from the edit page section
    If (IsSet($_POST[ __CLASS__ ]))
      // Save Meta data
      update_post_meta ($post_id, '_' . __CLASS__, (Array) ($_POST[ __CLASS__ ]) );
  }

  function Get_Gallery_Meta ($key = Null, $default = False, $post_id = Null){
    // Get the post id
    If ($post_id == Null && Is_Object($GLOBALS['post']))
      $post_id = $GLOBALS['post']->ID;
    ElseIf ($post_id == Null && !Is_Object($GLOBALS['post']))
      return False;

    // Read meta data
    $arr_meta = get_post_meta($post_id, '_' . __CLASS__, True);
    If (Empty($arr_meta) || !Is_Array($arr_meta)) $arr_meta = Array();

    // Clean Meta data
    ForEach ($arr_meta AS $k => $v)
      If (!$v) Unset ($arr_meta[$k]);

    // Load default Meta data
    $arr_meta = Array_Merge ( $this->Default_Meta(), $arr_meta );

    // Get the key value
    If ($key == Null)
      return $arr_meta;
    ElseIf (IsSet($arr_meta[$key]) && $arr_meta[$key])
      return $arr_meta[$key];
    Else
      return $default;
  }

  function Add_Options_Page(){
    $handle = Add_Options_Page (
      $this->t('Fancy Gallery Options'),
      $this->t('Fancy Gallery'),
      'manage_options',
      __CLASS__,
      Array($this, 'Print_Options_Page')
    );

    // Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));

    If (!$this->get_option('disable_option_page_in_gallery_menu')){
      $handle = Add_Submenu_Page (
        'edit.php?post_type=' . $this->gallery_post_type,
        $this->t('Fancy Gallery Options'),
        __('Settings'),
        'manage_options',
        __CLASS__,
        Array($this, 'Print_Options_Page')
      );

      // Add JavaScript to this handle
      Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));
    }

    // Add option boxes
    $this->Add_Option_Box ( $this->t('Fancy Light Box'), DirName(__FILE__).'/option-box-fancybox.php' );
    $this->Add_Option_Box ( $this->t('Templates'), DirName(__FILE__).'/option-box-templates.php', 'main', 'closed' );
    $this->Add_Option_Box ( $this->t('Capabilities'), DirName(__FILE__).'/option-box-capabilities.php', 'main', 'closed' );

    $this->Add_Option_Box ( $this->t('Taxonomies'), DirName(__FILE__).'/option-box-taxonomies.php', 'side' );
    $this->Add_Option_Box ( $this->t('Archive Url'), DirName(__FILE__).'/option-box-archive-link.php', 'side' );
    $this->Add_Option_Box ( $this->t('Miscellaneous'), DirName(__FILE__).'/option-box-misc.php', 'side' );
  }

  function Get_Options_Page_Url($parameters = Array()){
    $url = Add_Query_Arg(Array('page' => __CLASS__), Admin_Url('options-general.php'));
    If (Is_Array($parameters) && !Empty($parameters)) $url = Add_Query_Arg($parameters, $url);
    return $url;
  }

  function Load_Options_Page(){
    // If the Request was redirected from a "Save Options"-Post
    If (IsSet($_REQUEST['options_saved'])) Flush_Rewrite_Rules();

    // If this is a Post request to save the options
    If ($this->Save_Options()) WP_Redirect( $this->Get_Options_Page_Url(Array('options_saved' => 'true')) );

    WP_Enqueue_Script('dashboard');
    WP_Enqueue_Style('dashboard');

    WP_Enqueue_Script('farbtastic');
    WP_Enqueue_Style('farbtastic');

    WP_Enqueue_Script('fancy-gallery-options-page', $this->base_url . '/options-page.js', Array('jquery') );
    WP_Enqueue_Style('fancy-gallery-options-page', $this->base_url . '/options-page.css' );

    // Remove incompatible JS Libs
    WP_Dequeue_Script('post');
  }

  function Print_Options_Page(){
    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php Echo $this->t('Fancy Gallery Options') ?></h2>

      <?php If (IsSet($_GET['options_saved'])) : ?>
      <div id="message" class="updated fade">
        <p><strong><?php _e('Settings saved.') ?></strong></p>
      </div>
      <?php EndIf; ?>

      <form method="post" action="" enctype="multipart/form-data">
      <div class="metabox-holder">

        <div class="postbox-container" style="width:69%;">
          <?php ForEach ($this->arr_option_box['main'] AS $box) : ?>
            <div class="postbox should-be-<?php Echo $box['state'] ?>">
              <div class="handlediv" title="Click to toggle"><br /></div>
              <h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
              <div class="inside"><?php Include $box['file'] ?></div>
            </div>
          <?php EndForEach ?>
        </div>

        <div class="postbox-container" style="width:29%;float:right">
          <?php ForEach ($this->arr_option_box['side'] AS $box) : ?>
            <div class="postbox should-be-<?php Echo $box['state'] ?>">
              <div class="handlediv" title="Click to toggle"><br /></div>
              <h3 class="hndle"><span><?php Echo $box['title'] ?></span></h3>
              <div class="inside"><?php Include $box['file'] ?></div>
            </div>
          <?php EndForEach ?>
        </div>

        <div class="clear"></div>
      </div>

      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
        <input type="reset" value="<?php _e('Reset') ?>">
      </p>

      </form>
    </div>
    <?php
  }

  function Add_Option_Box($title, $include_file, $column = 'main', $state = 'opened'){
    // Check the input
    If (!Is_File($include_file)) return False;
    If ( $title == '' ) $title = '&nbsp;';

    // Column (can be 'side' or 'main')
    If ($column != '' && $column != Null && $column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    // State (can be 'opened' or 'closed')
    If ($state != '' && $state != Null && $state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    // Add a new box
    $this->arr_option_box[$column][] = Array('title' => $title, 'file' => $include_file, 'state' => $state);
  }

  function Save_Options(){
    // Check if this is a post request
    If (Empty($_POST)) return False;

    // Clean the Post array
    $_POST = StripSlashes_Deep($_POST);
    ForEach ($_POST AS $option => $value)
      If (!$value) Unset ($_POST[$option]);

    // Save Options
    Update_Option (__CLASS__, $_POST);

    return True;
  }

  function Default_Options(){
    return Array(
      'scrolling' => 'auto',
      'overlay_opacity' => 30,
      'overlay_color' => '#666',
      'border_width' => 10,
      'use_as_image_title' => 'title',
      'speed_in' => 300,
      'speed_out' => 300,
      'change_speed' => 300,
      'gallery_taxonomy' => Array(),
      'title_position' => 'float',
      'transition_in' => 'fade',
      'transition_out' => 'fade',
      'excerpt_thumb_width' => get_option('thumbnail_size_w'),
      'excerpt_thumb_height' => get_option('thumbnail_size_h'),
      'excerpt_image_number' => 3
    );
  }

  function Default_Meta(){
    return Array(
      'excerpt_type' => 'images',
      'thumb_width' => get_option('thumbnail_size_w'),
      'thumb_height' => get_option('thumbnail_size_h'),
      'excerpt_image_number' => $this->Get_Option('excerpt_image_number'),
      'excerpt_thumb_width' => $this->get_option('excerpt_thumb_width'),
      'excerpt_thumb_height' => $this->get_option('excerpt_thumb_height')
    );
  }

  function Get_Option($key = Null, $default = False){
    // Read Options
    $arr_option = Array_Merge (
      (Array) $this->Default_Options(),
      (Array) get_option(__CLASS__)
    );

    // Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

  function Register_Gallery_Post_Type(){
    // Register Product Post Type
    Register_Post_Type ($this->gallery_post_type, Array(
      'labels' => Array(
        'name' => $this->t('Galleries'),
        'singular_name' => $this->t('Gallery'),
        'add_new' => $this->t('Add Gallery'),
        'add_new_item' => $this->t('New Gallery'),
        'edit_item' => $this->t('Edit Gallery'),
        'view_item' => $this->t('View Gallery'),
        'search_items' => $this->t('Search Galleries'),
        'not_found' =>  $this->t('No Galleries found'),
        'not_found_in_trash' => $this->t('No Galleries found in Trash'),
        'parent_item_colon' => ''
        ),
      'public' => True,
      'show_ui' => True,
      'has_archive' => !$this->get_option('deactivate_archive'),
      'capability_type' => Array('post', 'posts'),
			'map_meta_cap' => True,
			'hierarchical' => False,
      'rewrite' => Array(
        'slug' => $this->t('galleries', 'URL slug'),
        'with_front' => False
      ),
      'supports' => Array( 'title', 'author', 'excerpt', 'thumbnail', 'comments' ),
      'menu_position' => 10, // below Media
      'register_meta_box_cb' => Array($this, 'Add_Gallery_Meta_Boxes')
    ));
  }

  function Gallery_Updated_Messages($arr_message){
    return Array_Merge ($arr_message, Array($this->gallery_post_type => Array(
      1 => SPrintF ($this->t('Gallery updated. (<a href="%s">View Gallery</a>)'), get_permalink()),
      2 => __('Custom field updated.'),
      3 => __('Custom field deleted.'),
      4 => $this->t('Gallery updated.'),
      5 => IsSet($_GET['revision']) ? SPrintF($this->t('Gallery restored to revision from %s'), WP_Post_Revision_Title( (Int) $_GET['revision'], False ) ) : False,
      6 => SPrintF($this->t('Gallery published. (<a href="%s">View Gallery</a>)'), get_permalink()),
      7 => $this->t('Gallery saved.'),
      8 => $this->t('Gallery submitted.'),
      9 => SPrintF($this->t('Gallery scheduled. (<a target="_blank" href="%s">View Gallery</a>)'), get_permalink()),
      10 => SPrintF($this->t('Gallery draft updated. (<a target="_blank" href="%s">Preview Gallery</a>)'), Add_Query_Arg('preview', 'true', get_permalink()))
    )));
  }

  function Get_Gallery_Taxonomies(){
    return Array(
      'gallery_category' => Array(
        'label' => $this->t( 'Gallery Categories' ),
        'labels' => Array(
          'name' => $this->t( 'Categories' ),
          'singular_name' => $this->t( 'Category' ),
          'search_items' =>  $this->t( 'Search Categories' ),
          'all_items' => $this->t( 'All Categories' ),
          'parent_item' => $this->t( 'Parent Category' ),
          'parent_item_colon' => $this->t( 'Parent Category:' ),
          'edit_item' => $this->t( 'Edit Category' ),
          'update_item' => $this->t( 'Update Category' ),
          'add_new_item' => $this->t( 'Add New Category' ),
          'new_item_name' => $this->t( 'New Category' )
        ),
        'hierarchical' => True,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => 'gallery-category'
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_categories',
          'edit_terms' => 'manage_categories',
          'delete_terms' => 'manage_categories',
          'assign_terms' => 'edit_posts'
        )
      ),
      'gallery_tag' => Array(
        'label' => $this->t( 'Gallery Tags' ),
        'labels' => Array(
          'name' => $this->t( 'Tags' ),
          'singular_name' => $this->t( 'Tag' ),
          'search_items' =>  $this->t( 'Search Tags' ),
          'all_items' => $this->t( 'All Tags' ),
          'edit_item' => $this->t( 'Edit Tag' ),
          'update_item' => $this->t( 'Update Tag' ),
          'add_new_item' => $this->t( 'Add New Tag' ),
          'new_item_name' => $this->t( 'New Tag' )
        ),
        'hierarchical' => False,
        'show_ui' => True,
        'query_var' => True,
        'rewrite' => Array(
          'with_front' => False,
          'slug' => 'gallery-tag'
        ),
        'capabilities' => Array (
          'manage_terms' => 'manage_categories',
          'edit_terms' => 'manage_categories',
          'delete_terms' => 'manage_categories',
          'assign_terms' => 'edit_posts'
        )
      ),
    );
  }

  function Register_Gallery_Taxonomies(){
    // Load Taxonomies
    $this->arr_taxonomies = $this->Get_Gallery_Taxonomies();

    // Register Taxonomies
    ForEach ( (Array) $this->get_option('gallery_taxonomies') As $taxonomie => $attributes ){
      If (!IsSet($this->arr_taxonomies[$taxonomie])) Continue;
      Register_Taxonomy ($taxonomie, $this->gallery_post_type, Array_Merge($this->arr_taxonomies[$taxonomie], $attributes));
    }
  }

  function Add_Taxonomy_Archive_Urls(){
    ForEach(Get_Object_Taxonomies($this->gallery_post_type) AS $taxonomy){ /*$taxonomy = Get_Taxonomy($taxonomy)*/
      Add_Action ($taxonomy.'_edit_form_fields', Array($this, 'Print_Taxonomy_Archive_Urls'), 10, 3);
    }
  }

  function Print_Taxonomy_Archive_Urls($tag, $taxonomy){
    $taxonomy = Get_Taxonomy($taxonomy);
    $archive_url = get_term_link(get_term($tag->term_id, $taxonomy->name));
    $archive_feed = get_term_feed_link($tag->term_id, $taxonomy->name);
    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Url') ?></th>
      <td>
        <code><a href="<?php Echo $archive_url ?>" target="_blank"><?php Echo $archive_url ?></a></code><br />
        <span class="description"><?php PrintF($this->t('This is the URL to the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <tr class="form-field">
      <th scope="row" valign="top"><?php Echo $this->t('Archive Feed') ?></th>
      <td>
        <code><a href="<?php Echo $archive_feed ?>" target="_blank"><?php Echo $archive_feed ?></a></code><br />
        <span class="description"><?php PrintF($this->t('This is the URL to the feed of the archive of this %s.'), $taxonomy->labels->singular_name) ?></span>
      </td>
    </tr>
    <?php
  }

  function Add_Media_Upload_Style(){
    WP_Enqueue_Style('fancy-gallery-media-upload', $this->base_url . '/media-upload.css');
  }

  function Media_Upload_Tabs($arr_tabs){
    return Array(
      'type' => $this->t('Upload Images'),
      'gallery' => $arr_tabs['gallery'],
      'import_images' => $this->t('Import from Library')
    );
  }

  function Media_Upload_Form_URL($url){
    return $url . '&strip_tabs=true';
  }

  function Image_Upload_Iframe_Src($url){
    If ($GLOBALS['post']->post_type == $this->gallery_post_type)
      return $url . '&strip_tabs=true';
    Else
      return $url;
  }

  function Filter_Excerpt($excerpt){
    If ( $GLOBALS['post']->post_type == $this->gallery_post_type && $this->Get_Gallery_Meta('excerpt_type') == 'images' ){
      return $this->ShortCode_Gallery(Array(
        'number'          => $this->Get_Gallery_Meta('excerpt_image_number'),
        'orderby'         => 'rand',
        'thumb_width'     => $this->Get_Gallery_Meta('excerpt_thumb_width'),
        'thumb_height'    => $this->Get_Gallery_Meta('excerpt_thumb_height'),
        'thumb_grayscale' => $this->Get_Gallery_Meta('excerpt_thumb_grayscale'),
        'thumb_negate'    => $this->Get_Gallery_Meta('excerpt_thumb_negate'),
        'template'        => $this->Get_Gallery_Meta('excerpt_template')
      ));
    }
    Else return $excerpt;
  }

  function Filter_Content($content){
    If ( $GLOBALS['post']->post_type == $this->gallery_post_type &&
         StrPos($content, '[gallery]') === False &&
         StrPos($content, '[gallery ') === False &&
         !post_password_required() ){
      return $content . $this->ShortCode_Gallery();
    }
    Else return $content;
  }

  function Filter_Feed_Content($content){
    If ( $GLOBALS['post']->post_type == $this->gallery_post_type){
      return $this->ShortCode_Gallery(Array(
        'number'          => $this->Get_Gallery_Meta('excerpt_image_number'),
        'orderby'         => 'rand',
        'thumb_width'     => $this->Get_Gallery_Meta('excerpt_thumb_width'),
        'thumb_height'    => $this->Get_Gallery_Meta('excerpt_thumb_height'),
        'thumb_grayscale' => $this->Get_Gallery_Meta('excerpt_thumb_grayscale'),
        'thumb_negate'    => $this->Get_Gallery_Meta('excerpt_thumb_negate'),
        'template'        => $this->Get_Default_Feed_Template()
      ));
    }
    Else return $content;
  }

  function Filter_Post_Class($arr_class){
    $arr_class[] = 'fancy-gallery-content-unit';
    return $arr_class;
  }

  function Add_Gallery_Meta_Box($title, $include_file, $column = 'normal', $priority = 'default'){
    If (!$title) return False;
    If (!Is_File($include_file)) return False;
    If ($column != 'side') $column = 'normal';

    // Add to array
    $this->arr_gallery_meta_box[] = Array(
      'title' => $title,
      'include_file' => $include_file,
      'column' => $column,
      'priority' => $priority
    );
  }

  function Add_Gallery_Meta_Boxes(){
    Global $post_type_object;

    // Enqueue Edit Gallery JavaScript/CSS
    WP_Enqueue_Script('fancy-gallery-meta-boxes', $this->base_url . '/meta-boxes.js', Array('jquery'), $this->version);
    WP_Enqueue_Style('fancy-gallery-meta-boxes', $this->base_url . '/meta-boxes.css', False, $this->version);

    // Remove Meta Boxes
    Remove_Meta_Box('authordiv', $this->gallery_post_type, 'normal');
    Remove_Meta_Box('postexcerpt', $this->gallery_post_type, 'normal');

    // Change some core texts
    Add_Filter ( 'gettext', Array($this, 'Filter_GetText'), 10, 3 );

    // Register Meta Boxes
    $this->Add_Gallery_Meta_Box( $this->t('Images'), DirName(__FILE__) . '/gallery-meta-box-images.php', 'normal', 'high' );

    If (!$this->get_option('disable_excerpts'))
      $this->Add_Gallery_Meta_Box( $this->t('Excerpt'), DirName(__FILE__) . '/gallery-meta-box-excerpt.php', 'normal', 'high' );

    $this->Add_Gallery_Meta_Box( $this->t('Template'), DirName(__FILE__) . '/gallery-meta-box-template.php', 'normal', 'high' );

    If (Current_User_Can($post_type_object->cap->edit_others_posts))
      $this->Add_Gallery_Meta_Box( $this->t('Owner'), DirName(__FILE__) . '/gallery-meta-box-owner.php' );

    $this->Add_Gallery_Meta_Box( $this->t('Gallery ShortCode'), DirName(__FILE__) . '/gallery-meta-box-show-code.php', 'side', 'high' );
    $this->Add_Gallery_Meta_Box( $this->t('Thumbnails'), DirName(__FILE__) . '/gallery-meta-box-thumbnails.php', 'side' );

    // Add Meta Boxes
    ForEach ($this->arr_gallery_meta_box AS $box_index => $meta_box){
      Add_Meta_Box(
        BaseName($meta_box['include_file'], '.php'),
        $meta_box['title'],
        Array($this, 'Print_Gallery_Meta_Box'),
        $this->gallery_post_type,
        $meta_box['column'],
        $meta_box['priority'],
        $box_index
      );
    }
  }

  function Print_Gallery_Meta_Box($post, $box){
    $include_file = $this->arr_gallery_meta_box[$box['args']]['include_file'];
    If (Is_File ($include_file))
      Include $include_file;
  }

  function User_Creates_New_Gallery(){
    If ( BaseName($_SERVER['SCRIPT_NAME']) == 'post-new.php' &&
         IsSet($_GET['post_type']) &&
         $_GET['post_type'] == $this->gallery_post_type
       ){
       $this->Check_Gallery_Count();
    }
  }

  function User_Untrashes_Post($post_id){
    If (Get_Post_Type($post_id) == $this->gallery_post_type)
      $this->Check_Gallery_Count();
  }

  function Check_Gallery_Count(){
    If (Count(Get_Posts(Array('post_type' => $this->gallery_post_type, 'post_status' => 'any', 'numberposts' => 1))) >= 1)
      $this->Print_Gallery_Count_Limit();
  }

  function Print_Gallery_Count_Limit(){
    WP_Die(
      SPrintF(
        '<h1>%s</h1><p>%s</p><p>%s</p><p>%s</p>',
        $this->t('Sorry!'),
        $this->t('In the Lite Version you can create one gallery only.'),
        $this->t('Why not switching to the <a href="http://dennishoppe.de/en/wordpress-plugins/fancy-gallery">Pro Version of Fancy Gallery</a>? :)'),
        SPrintF(
          '<a href="%s" class="button">%s</a>',
          Admin_URL('edit.php?post_type=' . $this->gallery_post_type),
          $this->t('&laquo; Back to your galleries')
        )
      )
    );
  }

  function Add_Gallery_Count_Notice($views){
    ?><div id="message" class="error">
    <p><?php PrintF('%s %s %s',
      $this->t('Please notice:'),
      $this->t('In the Lite Version you can create one gallery only.'),
      $this->t('Why not switching to the <a href="http://dennishoppe.de/en/wordpress-plugins/fancy-gallery">Pro Version of Fancy Gallery</a>? :)')
    );
    ?></p>
    </div><?php
    return $views;
  }

  function Import_Images(){
		// Enqueue Scripts and Styles
		WP_Enqueue_Style('media');
		WP_Enqueue_Style('import-images', $this->base_url.'/import-images-form.css', Null, $this->version);
		WP_Enqueue_Script('import-images', $this->base_url.'/import-images-form.js', Array('jquery'), $this->version, True);

		// Check if an attachment should be moved
		$message = '';
		If (IsSet($_REQUEST['move_attachment']) && IsSet($_REQUEST['move_to'])){
			$attachment_id = IntVal($_REQUEST['move_attachment']);
			$dst_post_id = IntVal($_REQUEST['move_to']);
			WP_Update_Post(Array(
				'ID' => $attachment_id,
				'post_parent' => $dst_post_id
			));
			$message = $this->t('The Attachment was moved to your gallery.');
		}

		// Generate Output
		return wp_iframe( Array($this, 'Print_Import_Images_Form'), $message );
	}

	function Print_Import_Images_Form($message = ''){
		Media_Upload_Header();
		Include DirName(__FILE__).'/import-images-form.php';
	}

  function Get_Image_Title($attachment){
    If (!Is_Object($attachment)) return False;

    // Image title
    $image_title = $attachment->post_title;

    // Alternative Text
    $alternative_text = Get_Post_Meta($attachment->ID, '_wp_attachment_image_alt', True);
    If (Empty($alternative_text)) $alternative_text = $image_title;

    // Image caption
    $caption = $attachment->post_excerpt;
    If (Empty($caption)) $caption = $image_title;

    // Image description
    $description = nl2br($attachment->post_content);
    $description = Str_Replace ("\n", '', $description);
    $description = Str_Replace ("\r", '', $description);
    If (Empty($description)) $description = $caption;

    // return Title
    Switch ($this->get_option('use_as_image_title')){
      Case 'none': return False;
      Case 'alt_text': return $alternative_text;
      Case 'caption': return $caption;
      Case 'description': return $description;
      Default: return $image_title;
    }
  }

  function Get_Template_Files(){
    $arr_template = Array_Unique(Array_Merge (
      (Array) Glob ( DirName(__FILE__) . '/templates/*.php' ),
      (Array) Glob ( DirName(__FILE__) . '/templates/*/*.php' ),

      (Array) Glob ( Get_StyleSheet_Directory() . '/*.php' ),
      (Array) Glob ( Get_StyleSheet_Directory() . '/*/*.php' ),

      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*.php' ) : Array(),
      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*/*.php' ) : Array()

    ));

    // Filter to add template files - you can use this filter to add template files to the user interface
    $arr_template = (Array) Apply_Filters('fancy_gallery_template_files', $arr_template);

    // Check if there template files
    If (Empty($arr_template)) return False;

    $arr_result = Array();
    $arr_sort = Array();
    ForEach ($arr_template AS $index => $template_file){
      // Read meta data from the template
      If (!$arr_properties = $this->Get_Template_Properties($template_file))
        Continue;
      Else {
        $arr_result[$arr_properties['name']] = Array_Merge ($arr_properties, Array('file' => $template_file));
        $arr_sort[$arr_properties['name']] = StrToLower($arr_properties['name']);
      }
    }
    Array_MultiSort($arr_sort, SORT_STRING, SORT_ASC, $arr_result);

    return $arr_result;
  }

  function Get_Template_Properties($template_file){
    // Check if this is a file
    If (!$template_file || !Is_File ($template_file) || !Is_Readable($template_file)) return False;

    // Read meta data from the template
    $arr_properties = Array_Merge(Get_File_Data ($template_file, Array(
      'name' => 'Fancy Gallery Template',
      'description' => 'Description',
      'author' => 'Author',
      'author_uri' => 'Author URI',
      'author_email' => 'Author E-Mail',
      'version' => 'Version'
    )), Array('file' => $template_file));

    // Check if there is a name for this template
    If (Empty($arr_properties['name']))
      return False;
    Else
      return $arr_properties;
  }

  function Get_Default_Template(){
    // Which file set the user as default?
    $template_file = $this->Get_Option('default_template_file');
    If (Is_File($template_file)) return $template_file;

    // Else:
    return DirName(__FILE__) . '/templates/gallery-default.php';
  }

  function Get_Default_Feed_Template(){
    return DirName(__FILE__) . '/templates/thumbs-only.php';
  }

  function Generate_Gallery_Attributes($attributes){
    If (!IsSet($attributes['id'])) $attributes['id'] = $GLOBALS['post']->ID;

    // Merge Attributes
    $attributes = Array_Merge(Array(
      'id'              => $GLOBALS['post']->ID,
      'post_status'     => 'inherit',
      'post_type'       => 'attachment',
      'post_mime_type'  => 'image',
      'order'           => 'ASC',
      'orderby'         => 'menu_order',
      'number'          => -1,
      'include'         => '',
      'ids'             => '',
      'exclude'         => '',
      'size'            => 'thumbnail',
      'link'            => 'file', // nothing else make sense
      'link_class'      => '',
      'thumb_width'     => '',
      'thumb_height'    => '',
      'thumb_grayscale' => False,
      'thumb_negate'    => False
    ),
    (Array) $this->Get_Gallery_Meta(Null, False, $attributes['id']),
    (Array) $attributes);
    $attributes['post_parent'] = $attributes['id']; Unset ($attributes['id']);
    $attributes['numberposts'] = $attributes['number']; Unset ($attributes['number']);
    $attributes['include'] .= $attributes['ids']; Unset ($attributes['ids']);

    return $attributes;
  }

  function Build_Gallery($arr_images, $attributes){
    // Prepare Gallery Array
    $this->gallery = New StdClass;
    $this->gallery->attributes = New StdClass;
    $this->gallery->images = Array();

    // Fill Attributes
    ForEach ($attributes AS $key => $value)
      $this->gallery->attributes->$key = $value;

    // Generate Gallery HTML ID
    If (Empty($attributes['include'])){ // this gallery uses the post attachments
      $this->gallery->id = $attributes['post_parent'];
    }
    Else { // this gallery only includes images
      Unset($attributes['post_parent']);
      $this->gallery->id = Sanitize_title($attributes['include']);
    }

  	// Build the Gallery object
    ForEach ($arr_images AS $id => &$image){
      // Thumb URL, width, height
      List($src, $width, $height) = wp_get_attachment_image_src($image->ID, $attributes['size']);

      $image->width = $width;
      $image->height = $height;
      $image->src = $src;

      // Image title
      $image->title = $this->get_image_title($image);

      // CSS Class
      $image->class = 'attachment-' . $attributes['size'];

      // Image Link
      If ($image->href == '') $image->href = WP_Get_Attachment_URL($image->ID);

      // Run filter
      $image->attributes = Apply_Filters( 'wp_get_attachment_image_attributes', Array(
        'src'    => $image->src,
        'width'  => $image->width,
        'height' => $image->height,
        'class'  => $image->class,
        'alt'    => $image->title,
        'title'  => $image->title
      ), $image );

      // Write in Object:
      $this->gallery->images[] = $image;
    }
  }

  function Render_Gallery ($template_file){
    // Uses template filter
    $template_file = Apply_Filters('fancy_gallery_template', $template_file);

    // If there is no valid template file we bail out
    If (!Is_File($template_file)) $template_file = $this->Get_Default_Template();

    // Load template
    Ob_Start();
    Include $template_file;
    $code = Ob_Get_Clean();

    // Strip Whitespaces
    $code = $this->Trim_HTML_Code($code);

    // Return
  	return $code;
  }

  function Trim_HTML_Code($html){
    $html = PReg_Replace('/\s+/', ' ', $html);
    $html = Str_Replace('> <', '><', $html);
    $html = Trim($html);
    return $html;
  }

  function ShortCode_Gallery ($attributes = Array()){
    $attributes = $this->Generate_Gallery_Attributes($attributes);

  	// get attachments
    If (Empty($attributes['include'])){ // this gallery uses the post attachments
      $arr_gallery = Get_Children($attributes);
    }
  	Else { // this gallery only includes images
      Unset($attributes['post_parent']);
      If (!IsSet($attributes['orderby'])) $attributes['orderby'] = 'post__in';
      $arr_gallery = Get_Posts($attributes);
    }

  	// There are no attachments
  	If (Empty($arr_gallery)) return False;

  	// Build the Gallery object
  	$this->Build_Gallery($arr_gallery, $attributes);

    // Load Template
    return $this->Render_Gallery($attributes['template']);
  }

  function Pro_Notice(){
    PrintF (
      $this->t('Sorry, this feature is only available in the <a href="%s" target="_blank">Pro Version of Fancy Gallery</a>.'),
      $this->t('http://dennishoppe.de/en/wordpress-plugins/fancy-gallery', 'Link to the authors website')
    );
  }

} /* End of the Class */
New wp_plugin_fancy_gallery();
Include DirName(__FILE__).'/wp-widget-fancy-random-images.php';
Include DirName(__FILE__).'/wp-widget-fancy-taxonomies.php';
Include DirName(__FILE__).'/wp-widget-fancy-taxonomy-cloud.php';
} /* End of the If-Class-Exists-Condition */
/* End of File */
