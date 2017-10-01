<p class="pro-notice"><?php $this->Pro_Notice() ?></p>
<?php

// User capabilities
$post_type = Get_Post_Type_Object($this->gallery_post_type);
$arr_capabilities = Array(
  $post_type->cap->edit_posts => $this->t('Edit and create (own) Galleries'),
  $post_type->cap->edit_others_posts => $this->t('Edit others Galleries'),
  $post_type->cap->edit_private_posts => $this->t('Edit (own) private Galleries'),
  $post_type->cap->edit_published_posts => $this->t('Edit (own) published Galleries'),

  $post_type->cap->delete_posts => $this->t('Delete (own) Galleries'),
  $post_type->cap->delete_private_posts => $this->t('Delete (own) private Galleries'),
  $post_type->cap->delete_published_posts => $this->t('Delete (own) published Galleries'),
  $post_type->cap->delete_others_posts => $this->t('Delete others Galleries'),

  $post_type->cap->publish_posts => $this->t('Publish Galleries'),
  $post_type->cap->read_private_posts => $this->t('View (others) private Galleries')
);

// Taxonomies
ForEach ( (Array) $this->arr_taxonomies AS $taxonomie => $tax_args )
  $arr_capabilities[ $tax_args['capabilities']['manage_terms'] ] = SPrintF($this->t('Manage %s'), $tax_args['labels']['name']);

// Show the user roles
ForEach ($GLOBALS['wp_roles']->roles AS $role_name => $arr_role) : ?>
  <h4><?php Echo Translate_User_Role($arr_role['name']) ?></h4>
  
  <?php ForEach ($arr_capabilities AS $capability => $caption) : ?>

    <div class="capability-selection">
      <span class="caption"><?php Echo $caption ?>:</span>

      <input type="radio" id="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][yes]" <?php Checked(IsSet($arr_role['capabilities'][$capability])) ?> disabled>
      <label for="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][yes]"><?php _e('Yes') ?></label>
      
      <input type="radio" id="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][no]" <?php Checked(!IsSet($arr_role['capabilities'][$capability])) ?> disabled>
      <label for="capabilities[<?php Echo $role_name ?>][<?php Echo $capability ?>][no]"><?php _e('No') ?></label>
    </div>    

  <?php EndForEach ?>

<?php EndForEach ?>