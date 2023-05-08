<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>
<div class="flex-grow-1 mb-3 border p-2">
    <div>
        <div class="fw-bold">
            <?php echo $message['title'] ?>
        </div>
        <div class="">
            <?php echo substr(strip_tags($message['message']), 0, 200) ?>...
            <?php echo anchor('forum/message/' . $message['mid'], lang('Forum.more')) ?>
        </div>
    </div>
    <?php if ($message['last_date'] != $message['date']) { ?>
        <div class="text-end">
            <?php echo lang('Forum.last_change', ['last_date' => anchor('forum/message/' . $message['last_mid'], date_ago($message['last_date'])), 'last_username' => anchor('forum/user/' . $message['username'], $message['last_username'])]) ?>
        </div>
    <?php } ?>
</div>
<div class="reply">
    <form action="<?php echo site_url('forum/message/' . $message['mid'] . '/reply') ?>" method="post">
        <?php echo csrf_field(); ?>
        <div class="d-flex mb-3 border p-3">
            <div class="me-3">
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
                <div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <textarea class="form-control" name="message" id="message" rows="15"><?php if($quote) echo "[quote=" . $quote['username'] . "]" . strip_tags(trim($quote['message'])) . "[/quote]"; ?></textarea>
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
    toolbar: 'bold,italic,underline,color|link,quote,image,bulletlist,orderedlist,youtube|source',
	style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
});
</script>
<?php $this->endSection(); ?>