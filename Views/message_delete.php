<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h1><?php echo $page_title ?></h1>
<p><?php echo lang('Forum.delete_confirm') ?></p>
<?php echo anchor(previous_url() , lang('Forum.cancel'), ['class' => 'btn btn-secondary me-3']) ?>
<?php echo anchor(uri_string() . '/confirm' , lang('Forum.delete'), ['class' => 'btn btn-primary']) ?>

<?php $this->endSection(); ?>