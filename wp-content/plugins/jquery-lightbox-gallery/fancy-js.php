<?php

// Send Header Mime type
Header ('Content-Type: text/javascript');

// Load WordPress
While (!Is_File ('wp-load.php')){
  If (Is_Dir('../')) ChDir('../');
  Else Die('Could not find WordPress.');
}
Include_Once 'wp-load.php';

// Is the class ready?
Global $wp_plugin_fancy_gallery;
If (!Is_Object($wp_plugin_fancy_gallery)) Die ('Could not find the Fancy Gallery Plugin.');
Else $FG = $wp_plugin_fancy_gallery;

// Check Referer
If (!$FG->get_option('disable_referer_check') && IsSet($_SERVER['HTTP_REFERER'])){
  $referer = Parse_URL($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
  If (!Empty($referer) && !Empty($_SERVER['SERVER_NAME'])){
    If (StrIPos($referer, $_SERVER['SERVER_NAME']) === False) : ?>
    alert("Wrong Referer for <?php Echo BaseName(__FILE__) ?>!\n\nHost: <?php Echo $_SERVER['SERVER_NAME'] ?>\nReferer: <?php Echo $referer ?>");
    window.location.href = "http://<?php Echo $_SERVER['SERVER_NAME'] ?>/";
    <?php Exit; Endif;
  }
}

// Set image extensions
$arr_type = Array( 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'wbmp', 'ico' );

?>jQuery(function(){

  // group gallery items
  jQuery('div.fancy-gallery a')
  .each(function(){
    var $this = jQuery(this);
    $this.attr('rel', $this.parents('.fancy-gallery').attr('id'));
  });

  <?php If($FG->get_option('img_block_fix')) : ?>
  jQuery('div.fancy-gallery a img').addClass('alignleft');
  <?php EndIf; ?>

  // Add Fancy Classes to single items:
  jQuery('a').each(function(){
    // filter items
    if ( <?php ForEach ($arr_type AS $type) : ?>
         this.href.substr(this.href.length-<?php Echo StrLen($type)+1 ?>).toLowerCase().indexOf('.<?php Echo $type ?>') < 0 &&
         <?php EndForEach; ?>
         true )
    return;

    // shorter access path
    var $lnk = jQuery(this);
    var $img = $lnk.find('img');

    // Add the fancybox class
    $lnk.addClass('fancybox');

    // Associate single images
    if ($lnk.attr('rel') == '' || $lnk.attr('rel') == undefined)
      $lnk.attr('rel', 'single-image');

    <?php If ($FG->get_option('use_as_image_title') == 'alt_text') : // Copy the alternate texts ?>
    $img.attr('title', $img.attr('alt'));
    <?php ElseIf ($FG->get_option('use_as_image_title') == 'caption') : // Copy the captions ?>
    if (caption = $lnk.parent('.wp-caption').find('.wp-caption-text').html())
      $img.attr('title', caption);
    <?php EndIf; ?>

    <?php If ($FG->get_option('change_image_display')) : ?>
    $img.css('display', 'inline-block');
    <?php EndIf; ?>

    // Copy the title tag from link to img
    if ($lnk.attr('title') == '' || $lnk.attr('title') == undefined)
      $lnk.attr('title', $img.attr('title'));
  });

  jQuery('a.fancybox')
  .unbind('click')
  .fancybox({
    padding         :  <?php Echo IntVal($FG->get_option('border_width')) ?>,
    cyclic          :  <?php Echo $FG->get_option('cyclic') ? 'true' : 'false' ?>,
    scrolling       : '<?php Echo $FG->get_option('scrolling') ?>',
    centerOnScroll  :  <?php Echo $FG->get_option('center_on_scroll') ? 'true' : 'false' ?>,
    overlayOpacity  :  <?php Echo Number_Format($FG->get_option('overlay_opacity') / 100, 2) ?>,
    overlayColor    : '<?php Echo $FG->get_option('overlay_color') ?>',
    titleShow       :  <?php Echo ($FG->get_option('use_as_image_title')=='none') ? 'false' : 'true' ?>,
    titlePosition   : '<?php Echo $FG->get_option('title_position') ?>',
    transitionIn    : '<?php Echo $FG->get_option('transition_in') ?>',
    transitionOut   : '<?php Echo $FG->get_option('transition_out') ?>',
    speedIn         :  <?php Echo IntVal($FG->get_option('speed_in')) ?>,
    speedOut        :  <?php Echo IntVal($FG->get_option('speed_out')) ?>,
    changeSpeed     :  <?php Echo IntVal($FG->get_option('change_speed')) ?>,
    showCloseButton :  <?php Echo $FG->get_option('hide_close_button') ? 'false' : 'true' ?>
  });

  jQuery('a.fancyframe')
  .unbind('click')
  .fancybox({
    padding         :  <?php Echo IntVal($FG->get_option('border_width')) ?>,
    cyclic          :  <?php Echo $FG->get_option('cyclic') ? 'true' : 'false' ?>,
    scrolling       : '<?php Echo $FG->get_option('scrolling', 'auto') ?>',
    centerOnScroll  :  <?php Echo $FG->get_option('center_on_scroll') ? 'true' : 'false' ?>,
    overlayOpacity  :  <?php Echo Number_Format($FG->get_option('overlay_opacity') / 100, 2) ?>,
    overlayColor    : '<?php Echo $FG->get_option('overlay_color') ?>',
    speedIn         :  <?php Echo IntVal($FG->get_option('speed_in')) ?>,
    speedOut        :  <?php Echo IntVal($FG->get_option('speed_out')) ?>,
    showCloseButton :  <?php Echo $FG->get_option('hide_close_button') ? 'false' : 'true' ?>,
    height          : '75%',
    width           : '75%',
    type            : 'iframe'
  });

});