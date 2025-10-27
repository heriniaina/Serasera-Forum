<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>
<form method="post">
    <div class="mb-3">
        <label for="title" class="form-label"><?php echo lang('Forum.topic') ?></label>
        <input
            type="text"
            name="title"
            id="title"
            class="form-control" />
    </div>
    <div class="mb-3">
        <label for="description" class="form-label"><?php echo lang('Forum.description') ?></label>
        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label"><?php echo lang('Forum.image') ?></label>
        <input
            type="file"
            class="form-control"
            name="image"
            id="image" />
    </div>

    <button
        type="submit"
        class="btn btn-primary">
        <?php echo lang('Forum.enregistrer'); ?>
    </button>


</form>

<?php $this->endSection(); ?>