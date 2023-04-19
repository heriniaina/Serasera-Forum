<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h3 class="my-3">
    <?php echo $page_title; ?>
</h3>

<div class="mb-3">
    <?php echo anchor('forum/message/new/' . $topic['tid'], '<i class="bi-pencil-square me-3"></i> ' . lang('Forum.create_message_here'), ['class' => 'btn btn-primary']); ?>
</div>
<?php foreach ($messages as $row) { ?>
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
                    <?php echo anchor('forum/message/' . $row['mid'], lang('Forum.more')) ?>
                </div>
            </div>
            <?php if ($row['last_date'] != $row['date']) { ?>
                <div class="text-end">
                    <?php echo lang('Forum.last_change', ['last_date' => anchor('forum/message/' . $row['last_mid'], date_ago($row['last_date'])), 'last_username' => anchor('forum/user/' . $row['username'], $row['last_username'])]) ?>
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?>
<div class="text-center">
    <?php echo $pager->links() ?>
</div>

<?php $this->endSection(); ?>