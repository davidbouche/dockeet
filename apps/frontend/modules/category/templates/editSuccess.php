<div id="title_box">
  <div id="title_top"></div>
  <div id="title_content">
    <h2><?php echo ($form->getObject()->isNew()) ? __('New category') :  __('Edit category')  . ' ' . $form->getObject()->getPublicTitle(); ?></h2>
  </div>
  <div id="title_bottom"></div>
</div>

<?php include_partial('category/breadcrumb', array('category' => $form->getObject())); ?>
<div class="clear"></div>

<div id="cat_edit">
	<form action="<?php echo url_for('category/edit' . ($form->getObject()->isNew() ? '' : '?slug=' . $form->getObject()->slug)); ?>" method="post">
		<?php echo $form->renderHiddenFields(); ?>
		<?php echo $form->renderGlobalErrors(); ?>
	
		<?php echo $form['title']->renderError(); ?>
		<?php echo $form['title']->renderLabel(); ?>
		<?php echo $form['title']; ?>
	
		<?php echo $form['description']->renderError(); ?>
		<?php echo $form['description']->renderLabel(); ?>
		<?php echo $form['description']; ?>
	
		<input class="submit" type="submit" value="<?php echo __('Save'); ?>">
	</form>
</div>

<?php if($sf_user->hasCredential('admin') && !$form->getObject()->isNew()): ?>
  <div id="category_users">
    <?php include_partial('category_users', array('form' => new UserCategoryAddForm($form->getObject()))); ?>
  </div>
<?php endif; ?>