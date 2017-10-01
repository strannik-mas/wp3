<ol>
  <?php ForEach ( $this->Get_Template_Files() AS $template_name => $properties ) : ?>
  <li>
    <?php If (Empty($properties['name'])) : ?>
      <em><?php Echo $properties['file'] ?></em>
    <?php Else : ?>
      <strong><?php Echo $properties['name'] ?></strong>
    <?php EndIf; ?>
    <?php If ($properties['version']) : ?> <small>(<?php Echo $properties['version'] ?>)</small><?php Endif; ?>
    <?php If ($properties['author'] && !$properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <?php Echo $properties['author'] ?>
    <?php ElseIf ($properties['author'] && $properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <a href="<?php Echo $properties['author_uri'] ?>" target="_blank"><?php Echo $properties['author'] ?></a>
    <?php Endif; ?>
    <?php If ($properties['description']) : ?><br /><?php Echo $properties['description']; Endif; ?><br />
    <small><?php PrintF($this->t('Found in <em>%s</em>.'), $properties['file']) ?></small>
  </li>
  <?php EndForEach; ?>
</ol>


<h4><?php Echo $this->t('Install a template in ZIP format') ?></h4>
<p><?php Echo $this->t('If you have a template as a .zip archive, you may install it by uploading it here.') ?></p>
<p><label for=""><?php Echo $this->t('Template as ZIP File') ?></label>: <input type="file" disabled></p>

<h4><?php Echo $this->t('Install a template in PHP format') ?></h4>
<p><?php Echo $this->t('If you have a template as a .php file, you may install it by uploading it here.') ?></p>
<p><label for="template_php"><?php Echo $this->t('Templates PHP File') ?></label>: <input type="file" disabled></p>

<p><input type="submit" value="<?php Echo $this->t('Install template and save all options') ?>" class="no-button-primary" disabled></p>
<p class="pro-notice"><?php $this->Pro_Notice() ?></p>