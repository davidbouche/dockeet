<h2><?php echo $document->title; ?></h2>

<div id="document_control_box">
  <ul>
    <?php if($sf_user->hasCredential('admin')): ?>
      <li><a href="<?php echo url_for('document/edit?slug=' . $document->slug); ?>"><?php echo __('Edit'); ?></a></li>
      <li><a href="<?php echo url_for('document/delete?slug=' . $document->slug); ?>"><?php echo __('Delete'); ?></a></li>
    <?php endif; ?>
    <li><a href="<?php echo url_for('document/download?slug=' . $document->slug); ?>"><?php echo __('Download'); ?></a></li>
  </ul>
</div>