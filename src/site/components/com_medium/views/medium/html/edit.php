<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<form action="<?= @route($flyer->getURL()) ?>" method="post" >

<div class="control-group">
<div class="controls">
<input type="text" class="input-block-level" name="title" value="<?= @escape( $flyer->title ) ?>" size="50" maxlength="255" required>
</div>
</div>

<div class="control-group">
<div class="controls">
<textarea name="body" class="input-block-level" maxlength="5000" rows="5" cols="25">
<?= @escape( $flyer->body ) ?>
</textarea>
</div>
</div>
<div class="control-group">
<label class="control-label" for="startDate"><?= @text('COM-FLYERS-FLYER-FORM-START-DATE') ?></label>
<div class="controls">
<?= @helper('datetimepicker', 'startDate', array('date'=>$flyer->startDate)) ?>
</div>
</div>

<div class="control-group">
<label class="control-label" for="startDate"><?= @text('COM-FLYERS-FLYER-FORM-END-DATE') ?></label>
<div class="controls">
<?= @helper('datetimepicker', 'endDate', array('date'=>$flyer->endDate)) ?>
</div>
</div>
<div class="form-actions">
<a data-trigger="EditableCancel" class="btn" href="<?= @route($flyer->getURL()) ?>">
<?= @text('LIB-AN-ACTION-CANCEL') ?>
</a>

<button type="submit" class="btn btn-primary">
<?= @text('LIB-AN-ACTION-UPDATE') ?>
</button>
</div>
</form>