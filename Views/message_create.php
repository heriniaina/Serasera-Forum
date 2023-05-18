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
                    <label for="title" class="form-label">
                        <?php echo lang('Forum.title') ?>
                    </label>
                    <input type="text"
                        class="form-control" name="title" id="title"
                        placeholder="<?php echo lang('Forum.title_placeholder') ?>"
                        value="<?php echo $request->getPost('title') ?? '' ?>">
                </div>
                <div class="mb-3">
                    <label for="tid" class="form-label">
                        <?php echo lang('Forum.topic') ?>
                    </label>
                    <select class="form-select form-select-md" name="tid" id="tid">
                        <option value="">
                            <?php echo lang('Forum.select_one'); ?>
                        </option>
                        <?php foreach ($topics as $key => $t) { ?>
                            <option value="<?php echo $t['tid'] ?>" <?php echo ($tid && $tid == $t['tid']) ? 'selected' : '' ?>><?php echo $t['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <textarea class="form-control" name="message" id="message"
                                rows="15"><?php echo $request->getPost('message') ?? '' ?></textarea>
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
<link rel="stylesheet" href="<?php echo site_url('css/jquery.dm-uploader.min.css') ?>">
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
        toolbar: 'bold,italic,underline,color|link,quote,image,bulletlist,orderedlist|sary|youtube|source',
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