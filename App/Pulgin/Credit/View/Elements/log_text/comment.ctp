<?php if($item_object['Comment']['type'] == 'core_activity_comment' || $item_object['Comment']['type'] == 'comment'): ?>
	<?php echo __d('credit','Reply Comment') ?>
<?php else: ?>
	<?php 
		echo __d('credit','Commenting');
	?>
<?php endif; ?>