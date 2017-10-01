<table class="form-table">
<tr valign="top">
  <th scope="row"><?php Echo $this->t('Loop mode') ?></th>
  <td>
    <input type="checkbox" name="cyclic" id="cyclic" value="yes" <?php Checked ($this->get_option('cyclic'), 'yes') ?>/>            
    <label for="cyclic"><?php Echo $this->t('Will enable the user to get from the last image to the first one with the "Next &raquo;" button.') ?></label>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="scrolling"><?php Echo $this->t('Scrollbars') ?></label></th>
  <td>
    <select name="scrolling" id="scrolling">
      <option value="auto" <?php Selected ($this->get_option('scrolling'), 'auto') ?> ><?php Echo $this->t('Automatic') ?></option>
      <option value="yes" <?php Selected ($this->get_option('scrolling'), 'yes') ?> ><?php _e('Yes') ?></option>
      <option value="no" <?php Selected ($this->get_option('scrolling'), 'no') ?> ><?php _e('No') ?></option>
    </select>
    (<?php Echo $this->t('"Automatic" means scrollbars will be visibly if necessary. "Yes" and "No" should be clear.') ?>)<br />
    <small><?php Echo $this->t('This option controls the appearance of the scrollbars inside the fancy box.') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><?php Echo $this->t('Center on scroll') ?></th>
  <td>
    <input type="checkbox" name="center_on_scroll" id="center_on_scroll" value="yes" <?php Checked ($this->get_option('center_on_scroll'), 'yes') ?>/>            
    <label for="center_on_scroll"><?php Echo $this->t('Keep the FancyBox always in the center of the screen while scrolling the page.') ?></label>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><?php Echo $this->t('Close button') ?></th>
  <td>
    <input type="checkbox" name="hide_close_button" id="hide_close_button" value="yes" <?php Checked ($this->get_option('hide_close_button'), 'yes') ?>/>            
    <label for="hide_close_button"><?php Echo $this->t('Hide the close button.') ?></label>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="overlay_opacity"><?php Echo $this->t('Overlay opacity') ?></label></th>
  <td>
    <input type="text" name="overlay_opacity" id="overlay_opacity" value="<?php Echo IntVal($this->get_option('overlay_opacity')) ?>" size="3" />%<br />            
    <small><?php Echo $this->t('Percentaged opacity of the background of the FancyBox. Should be a value from 0 (invisible) to 100 (opaque). (Default is 30)') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="overlay_color"><?php Echo $this->t('Overlay color') ?></label></th>
  <td>
    <input type="text" name="overlay_color" id="overlay_color" value="<?php Echo $this->get_option('overlay_color') ?>" class="color" /><br />            
    <div class="colorpicker"></div>
    <small><?php Echo $this->t('Please choose the color of the "darker" background.') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="border_width"><?php Echo $this->t('Border width') ?></label></th>
  <td>
    <input type="text" name="border_width" id="border_width" value="<?php Echo IntVal($this->get_option('border_width')) ?>" size="4" /><?php Echo $this->t('px', 'Abbr. Pixels') ?><br />            
    <small><?php Echo $this->t('Width of the image frame border. (in pixels)') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><?php Echo $this->t('Image title') ?></th>
  <td>
    <input type="radio" name="use_as_image_title" value="title" id="use_as_image_title_title" <?php Checked ($this->get_option('use_as_image_title'), 'title') ?>/>
    <label for="use_as_image_title_title"><?php Echo $this->t('Use the title of the image as title of the fancy box.') ?></label><br />
    
    <input type="radio" name="use_as_image_title" value="alt_text" id="use_as_image_title_alt_text" <?php Checked ($this->get_option('use_as_image_title'), 'alt_text') ?>/>
    <label for="use_as_image_title_alt_text"><?php Echo $this->t('Use the alternative text of the image as title of the fancy box.') ?></label><br />
    
    <input type="radio" name="use_as_image_title" value="caption" id="use_as_image_title_caption" <?php Checked ($this->get_option('use_as_image_title'), 'caption') ?>/>
    <label for="use_as_image_title_caption"><?php Echo $this->t('Use the image caption as title of the fancy box.') ?></label><br />
    
    <input type="radio" name="use_as_image_title" id="use_as_image_title_description" disabled>
    <label for="use_as_image_title_description"><?php Echo $this->t('Use the image description as title of the fancy box.') ?></label>
    (<small class="pro-notice"><?php $this->Pro_Notice() ?></small>)<br />
    
    <input type="radio" name="use_as_image_title" value="none" id="use_as_image_title_none" <?php Checked ($this->get_option('use_as_image_title'), 'none') ?>/>
    <label for="use_as_image_title_none"><?php Echo $this->t('Do not show image titles.') ?></label>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><?php Echo $this->t('Title position') ?></th>
  <td>
    <input type="radio" name="title_position" value="float" id="title_position_float" <?php Checked ($this->get_option('title_position'), 'float') ?> />
    <label for="title_position_float"><?php Echo $this->t('Outside the FancyBox') ?> (<?php Echo $this->t('Does not work with multiline titles.') ?>)</label><br />
    
    <input type="radio" name="title_position" value="inside" id="title_position_inside" <?php Checked ($this->get_option('title_position'), 'inside') ?> />
    <label for="title_position_inside"><?php Echo $this->t('Inside the FancyBox') ?> (<?php Echo $this->t('Does not work blameless with multiline titles.') ?>)</label><br />
    
    <input type="radio" name="title_position" value="over" id="title_position_over" <?php Checked ($this->get_option('title_position'), 'over') ?> />
    <label for="title_position_over"><?php Echo $this->t('Over the image') ?> (<?php Echo $this->t('Works fine with multiline titles.') ?>)</label><br />
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="transition_in"><?php Echo $this->t('Opening transition') ?></label></th>
  <td>
    <select name="transition_in" id="transition_in">
      <option value="fade" <?php Selected ($this->get_option('transition_in'), 'fade') ?> ><?php Echo $this->t('Fade') ?></option>
      <option value="elastic" <?php Selected ($this->get_option('transition_in'), 'elastic') ?> ><?php Echo $this->t('Elastic') ?></option>
      <option value="none" <?php Selected ($this->get_option('transition_in'), 'none') ?> ><?php Echo $this->t('No transition') ?></option>
    </select>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="speed_in"><?php Echo $this->t('Opening speed') ?></label></th>
  <td>
    <input type="text" name="speed_in" id="speed_in" value="<?php Echo IntVal($this->get_option('speed_in')) ?>" size="4" /><?php Echo $this->t('msec', 'Abbr. Milliseconds') ?><br />            
    <small><?php Echo $this->t('Speed of the fade and elastic transitions. (in milliseconds)') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="transition_out"><?php Echo $this->t('Closing transition') ?></label></th>
  <td>
    <select name="transition_out" id="transition_out">
      <option value="fade" <?php Selected ($this->get_option('transition_out'), 'fade') ?> ><?php Echo $this->t('Fade') ?></option>
      <option value="elastic" <?php Selected ($this->get_option('transition_out'), 'elastic') ?> ><?php Echo $this->t('Elastic') ?></option>
      <option value="none" <?php Selected ($this->get_option('transition_out'), 'none') ?> ><?php Echo $this->t('No transition') ?></option>
    </select>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="speed_out"><?php Echo $this->t('Closing speed') ?></label></th>
  <td>
    <input type="text" name="speed_out" id="speed_out" value="<?php Echo IntVal($this->get_option('speed_out')) ?>" size="4" /><?php Echo $this->t('msec', 'Abbr. Milliseconds') ?><br />            
    <small><?php Echo $this->t('Speed of the fade and elastic transitions. (in milliseconds)') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="change_speed"><?php Echo $this->t('Image resizing speed') ?></label></th>
  <td>
    <input type="text" name="change_speed" id="change_speed" value="<?php Echo IntVal($this->get_option('change_speed')) ?>" size="4" /><?php Echo $this->t('msec', 'Abbr. Milliseconds') ?><br />            
    <small><?php Echo $this->t('Speed of resizing when changing gallery items. (in milliseconds)') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><label for="script_position"><?php Echo $this->t('Script position') ?></label></th>
  <td>
    <select name="script_position" id="script_position">
      <option value="footer" <?php Selected ($this->get_option('script_position'), 'footer') ?> ><?php Echo $this->t('Footer of the website') ?></option>
      <option value="header" <?php Selected ($this->get_option('script_position'), 'header') ?> ><?php Echo $this->t('Header of the website') ?></option>
    </select><br />            
    <small><?php Echo $this->t('Please choose the position of the javascript. Footer is recommended. Use "Header" if you have trouble to make the Fancybox work.') ?></small>
  </td>
</tr>

<tr valign="top">
  <th scope="row"><?php Echo $this->t('Referer Check') ?></th>
  <td>
    <input type="checkbox" name="disable_referer_check" id="disable_referer_check" value="yes" <?php Checked ($this->get_option('disable_referer_check'), 'yes') ?> />            
    <label for="disable_referer_check"><?php Echo $this->t('Disable the referer check in the dynamically JavaScript and CSS files.') ?></label><br />            
    <small><?php Echo $this->t('Tick this is box if see a "Wrong Referer" message on your own website!') ?></small>
  </td>
</tr>

</table>
