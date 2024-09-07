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
                <div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <textarea class="form-control" name="message" id="message"
                                rows="15"><?php if (isset($quote))
                                    echo "[quote=" . $quote['username'] . "]" . strip_tags(trim($quote['message'])) . "[/quote]"; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <div class="dm-uploader me-3" id="drag-and-drop-zone">
                        <div class="btn btn-primary">
                            <?php echo lang('Forum.upload_image') ?><input type="file" name="image" id="image"
                                title="add image">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?php echo lang('Forum.submit') ?>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

<?php $this->endSection(); ?>
<?php $this->section('headers'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<link rel="stylesheet" href="<?php echo site_url('css/jquery.dm-uploader.min.css') ?>">
<style>
    .dm-uploader .btn {
        position: relative;
        cursor: pointer;
    }

    .dm-uploader .btn input {
        opacity: 0;
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        cursor: pointer;
    }

    .sceditor-button-sary div {
        background-position: 0 -416px;
    }
</style>
<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/bbcode.min.js"></script>
<script src="<?php echo site_url('js/jquery.dm-uploader.min.js') ?>"></script>
<script>
    // Replace the textarea #example with SCEditor
    var textarea = document.getElementById('message');
    sceditor.command.set('sary', {
        exec: function () {
            // this is set to the editor instance
            var fileinput = document.getElementById('image');
            fileinput.click();
        },
        txtExec: function () {
            // this is set to the editor instance

        },
        tooltip: 'Upload an image'
    });
    sceditor.create(textarea, {
        format: 'bbcode',
        width: '100%',
        emoticonsEnabled: false,
        toolbar: 'bold,italic,underline,color|link,quote,image,bulletlist,orderedlist|sary|youtube|source',,
        style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
    });
    $("#drag-and-drop-zone").dmUploader({
        url: '<?php echo site_url('api/forum/image') ?>',
        fieldName: 'image',
        maxFileSize: 5000000,
        extFilter: ["jpg", "jpeg", "png", "gif"],
        multiple: false,
        //... More settings here...

        onInit: function () {
            console.log('Callback: Plugin initialized');
        },
        onUploadSuccess: function (id, data) {

            sceditor.instance(textarea).insert('[img]' + data.file + '[/img]');

        },



        // ... More callbacks
    });
</script>
<?php $this->endSection(); ?>