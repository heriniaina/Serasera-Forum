<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>

<?php $request = service('request'); ?>
<?php $i = ($request->getGet('page') ?? 1) * 20 - 19; ?>
<?php foreach ($messages as $row) { ?>
    <div class="d-flex mb-3 border p-3">
        <a name="<?php echo $row['mid']; ?>"></a>
        <div class="me-3">
            <img src="https://avatar.serasera.org/<?php echo md5($row['username']) ?>.jpg" class="border rounded-circle"
                style="width: 50px">
        </div>
        <div class="flex-grow-1">
            <div>
                <b>
                    <?php echo $i . '. ' . $row['username'] ?>
                </b>
                <em>(
                    <?php echo date_ago($row['date']) ?>)
                </em>
            </div>
            <div>
                <div class="">
                    <?php echo $bbCode->convertToHtml(nl2br(strip_tags($row['message']))) ?>
                    <div class="text-end mt-2">
                        <?php echo anchor ('forum/message/' . $message['mid'] . '/reply/' . $row['id'], '<i class="bi-chat"></i> ' . lang('Forum.reply')); ?>
                    </div>
                </div>
            </div>
            <?php if ($row['last_date'] != $row['date']) { ?>
                <div class="text-end">
                    <?php echo lang('Forum.last_change', ['last_date' => anchor('forum/message/' . $row['last_mid'], date_ago($row['last_date'])), 'last_username' => anchor('forum/user/' . $row['username'], $row['last_username'])]) ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php $i++; ?>

<?php } ?>
<div class="text-center">
    <?php echo $pager->links() ?>
</div>

<div class="reply">
    <?php if (isset($user)) { ?>
        <form action="<?php echo site_url('forum/message/' . $message['mid'] . '/reply') ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="d-flex mb-3 border p-3">
                <a name="<?php echo $row['mid']; ?>"></a>
                <div class="me-3">
                    <img src="https://avatar.serasera.org/<?php echo md5($row['username']) ?>.jpg"
                        class="border rounded-circle"
                        style="width: 50px">
                </div>
                <div class="flex-grow-1">
                    <div>
                        <b>
                            <?php echo $user['username'] ?>
                        </b>
                    </div>
                    <div>
                        <div class="mb-3">
                            <div class="mb-3">
                                <textarea class="form-control" name="message" id="message" rows="10"></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="auto btn btn-primary">
                                    <?php echo lang('Forum.submit') ?>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php } else { ?>
        <?php echo anchor('forum/message/' . $message['mid'] . '/reply', lang('Forum.reply'), ['class' => 'btn btn-primary']); ?>
    <?php } ?>
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