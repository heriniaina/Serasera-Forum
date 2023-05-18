<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<?php $request = service('request'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>
<div class="mb-3">
    <?php echo $page_description; ?>
</div>

<div class="mb-3">
    <form method="get">
        <input type="hidden" id="expand" name="x" value="<?php echo $request->getGet('x') ?? '0' ?>">
        <div class="d-flex">
            <div class="p-2 flex-fill">
                <input type="text" class="form-control" name="title" id="title"
                    placeholder="<?php echo lang('Forum.in_title') ?>"
                    value="<?php echo $request->getGet('title') ?>">
            </div>
            <div class="p-2 flex-fill">
                <input type="text" class="form-control" name="username" id="username"
                    placeholder="<?php echo lang('Forum.in_author') ?>"
                    value="<?php echo $request->getGet('username') ?>">
            </div>
            <div class="p-2 flex-shrink"><button type="submit" id="btn-submit" class="btn btn-primary rounded"><i
                        class="bi-search"></i></button></div>
            <div class="p-2 flex-shrink"><button type="button" id="btn-expand" class="btn btn-primary rounded"><i
                        class="bi-plus"></i></button></div>
        </div>

        <div id="search-more" <?php echo (($request->getGet('x') != 1) ? 'style="display: none;"' : '') ?>>
            <div class="p-2 flex-fill">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <textarea class="form-control" name="message" id="message" rows="1"
                            placeholder="<?php echo lang('Forum.in_message') ?>"><?php echo $request->getGet('message') ?></textarea>
                    </div>
                    <div class="col-sm-5 col-md-2">
                        <label for="sort">
                            <?php echo lang('Forum.message_order') ?>
                        </label>
                    </div>
                    <div class="col-sm-7 col-md-3">
                        <select class="form-select" name="sort" id="sort">
                            <option value='id' <?php echo ($request->getGet('sort') == 'id') ? 'selected' : '' ?>><?php echo lang('Forum.by_date') ?></option>
                            <option value="title" <?php echo ($request->getGet('sort') == 'title') ? 'selected' : '' ?>><?php echo lang('Forum.by_title') ?></option>
                            <option value="author" <?php echo ($request->getGet('sort') == 'author') ? 'selected' : '' ?>><?php echo lang('Forum.by_author') ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php foreach ($messages as $row) { ?>
    <?php //fix last 
        if ($row['last_mid'] == '')
            $row['last_mid'] = $row['mid'];
        if ($row['last_username'] == '')
            $row['last_username'] = $row['username'];
        if ($row['last_date'] == 0)
            $row['last_date'] = $row['date'];


        ?>
    <div class="d-flex mb-3 border p-3">
        <div class="me-3">
            <img src="https://avatar.serasera.org/<?php echo md5($row['username']) ?>.jpg" class="border rounded-circle"
                style="width: 50px">
        </div>
        <div class="flex-grow-1">
            <div>
                <b>
                    <?php echo $row['username'] ?>
                </b>
                <em>(
                    <?php echo date_ago($row['date']) ?>)
                </em>
            </div>
            <div>
                <div class="fw-bold">
                    <?php echo $row['title'] ?>
                </div>
                <div class="">
                    <?php echo substr(strip_tags($row['message']), 0, 200) ?>...
                    <?php echo anchor('forum/message/' . $row['last_mid'], lang('Forum.more')) ?>
                </div>
            </div>
            <?php if ($row['last_date'] != $row['date']) { ?>
                <div class="text-end">
                    <?php echo lang('Forum.last_change', ['last_date' => anchor('forum/message/' . $row['last_mid'], date_ago($row['last_date'])), 'last_username' => anchor('forum/message?username=' . $row['username'], $row['last_username'])]) ?>
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?>
<div class="text-center">
    <?php echo $pager->links() ?>
</div>

<?php $this->endSection(); ?>
<?php $this->section('scripts') ?>
<script>
    $(function() {
        $("#btn-expand").click(function(expand) {
            $('#search-more').toggle("slow", function() {
                if ($(this).is(":visible")) {
                    $('#expand').attr('value', '1');
                } else {
                    $('#expand').attr('value', '0');
                }
            });

        });
    });
</script>
<?php $this->endSection(); ?>