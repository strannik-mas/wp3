<?php
	
if (!defined('ABSPATH')) exit; // Exit if accessed directly	
	
?>

<div class="wrap <?php echo $this -> pre; ?> slideshow">
	<h1><?php _e('Manage Slides', $this -> plugin_name); ?>
	<?php echo $this -> Html -> link(__('Add New', $this -> plugin_name), $this -> url . '&amp;method=save', array('class' => "add-new-h2")); ?> 
	<?php echo $this -> Html -> link(__('Add Multiple', $this -> plugin_name), $this -> url . '&amp;method=save-multiple', array('class' => "add-new-h2")); ?></h1>

	<?php if (!empty($slides)) : ?>	
		<form id="posts-filter" action="<?php echo $this -> url; ?>" method="post">
			<ul class="subsubsub">
				<li><?php echo $paginate -> allcount; ?> <?php _e('slides', $this -> plugin_name); ?></li>
			</ul>
		</form>
	<?php endif; ?>
	
	<?php $this -> render('slides' . DS . 'loop', array('list_table' => $list_table, 'slides' => $slides, 'paginate' => $paginate), true, 'admin'); ?>
</div>