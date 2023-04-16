<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h1 class="mb-3">
    <?php echo $page_title; ?>
</h1>

<?php foreach ($topics as $row) { ?>
    <div class="d-flex mb-3 border p-3">
        <div class="flex-grow-1">
            <div class="row">
                <div class="col">
                <b>
                    <?php echo anchor('forum/topic/' . $row['tid'], $row['title'] )?>
                </b>
                <em>(
                    <?php echo lang('Forum.topic_messages_nb', [$row['messages']]) ?>)
                </em>
                </div>
                <div class="col text-end">
                    <?php echo anchor('forum/topic/' . $row['tid'], '<i class="bi-eye me-2"></i>' . lang('Forum.hamaky'), ['class' => 'me-3']) ?>
                    <?php echo anchor('forum/message/new/' . $row['tid'], '<i class="bi-pencil-square me-2"></i>' . lang('Forum.hanoratra')) ?>
                    
                </div>
                
            </div>
            <div>
                <div class="">
                    <?php echo strip_tags($row['description']) ?>

                </div>
            </div>
            <?php if ($row['last_date'] != $row['date']) { ?>
                <div class="text-end">
                    <?php echo lang('Forum.topic_last_change', ['last_date' => anchor('forum/message/' . $row['last_mid'], date_ago($row['last_date'])), 'last_username' => anchor('forum/user/' . $row['username'], $row['last_username'])]) ?>
                </div>
            <?php } ?>
        </div>
    </div>

<?php } ?>


<?php $this->endSection(); ?>