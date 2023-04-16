<?php $this->extend(getenv('serasera.layout')) ?>
<?php $this->section('main'); ?>
<h1 class="mb-3">
    <?php echo $page_title; ?>
</h1>
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
                <?php echo $i . ' ' . $row['username'] ?>
                </b>
                <em>(
                    <?php echo date_ago($row['date']) ?>)
                </em>
            </div>
            <div>
                <div class="">
                    <?php echo nl2br(strip_tags($row['message'])) ?>
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

<?php $this->endSection(); ?>