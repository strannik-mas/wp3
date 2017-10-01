<p>
  <?php Echo $this->t('These settings affect the appearance of the thumbnail images of this gallery.') ?>
</p>

<p>
  <label for="<?php Echo $this->Field_Name('thumb_width') ?>"><?php Echo $this->t('Thumbnail width:') ?></label>
  <input type="text" id="<?php Echo $this->Field_Name('thumb_width') ?>" value="<?php Echo get_option('thumbnail_size_w') ?>" size="4" disabled>px
</p>

<p>
  <label for="<?php Echo $this->Field_Name('thumb_height') ?>"><?php Echo $this->t('Thumbnail height:') ?></label>
  <input type="text" id="<?php Echo $this->Field_Name('thumb_height') ?>" value="<?php Echo get_option('thumbnail_size_h') ?>" size="4" disabled>px
</p>

<p>
  <input type="checkbox" id="<?php Echo $this->Field_Name('thumb_grayscale') ?>" disabled>
  <label for="<?php Echo $this->Field_Name('thumb_grayscale') ?>"><?php Echo $this->t('Convert thumbnails to grayscale.') ?></label>
</p>

<p>
  <input type="checkbox" id="<?php Echo $this->Field_Name('thumb_negate') ?>" disabled>
  <label for="<?php Echo $this->Field_Name('thumb_negate') ?>"><?php Echo $this->t('Negate the thumbnails.') ?></label>
</p>

<p class="pro-notice"><?php $this->Pro_Notice() ?></p>