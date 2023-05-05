<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>

<?php $request = service('request'); ?>
<div class="create">
    <form method="post">
        <?php echo csrf_field(); ?>
        <div class="d-flex mb-3 border p-3">
            <div class="me-3 d-none d-md-block">
                <img src="https://avatar.serasera.org/<?php echo md5($user['username']) ?>.jpg"
                    class="border rounded-circle"
                    style="width: 50px">
            </div>
            <div class="flex-grow-1">
                <div>
                    <b>
                        <?php echo $user['username'] ?>
                    </b>
                </div>
                <div class="mb-3">
                  <label for="title" class="form-label"><?php echo lang('Forum.title') ?></label>
                  <input type="text"
                    class="form-control" name="title" id="title" placeholder="<?php echo lang('Forum.title_placeholder') ?>" value="<?php echo $request->getPost('title') ?? '' ?>">
                </div>
                <div class="mb-3">
                    <label for="tid" class="form-label"><?php echo lang('Forum.topic') ?></label>
                    <select class="form-select form-select-md" name="tid" id="tid">
                        <option value=""><?php echo lang('Forum.select_one'); ?></option>
                        <?php foreach ($topics as $key => $t) { ?>
                            <option value="<?php echo $t['tid'] ?>" <?php echo ($tid && $tid == $t['tid']) ? 'selected' : '' ?>><?php echo $t['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <textarea class="form-control" name="message" id="message" rows="15"><?php echo $request->getPost('message') ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><?php echo lang('Forum.submit') ?></button>
                </div>
            </div>
        </div>
    </form>

</div>

<?php $this->endSection(); ?>
<?php $this->section('headers'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/bbcode.min.js"></script>
<script>
// Replace the textarea #example with SCEditor
var textarea = document.getElementById('message');
sceditor.create(textarea, {
	format: 'bbcode',
    width: '100%',
    emoticonsEnabled: false,
    toolbar: 'bold,italic,underline,color|link,quote,image,bulletlist,orderedlist,youtube',
	style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
<?php $this->endSection(); ?>