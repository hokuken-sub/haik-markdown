<div class="<?php echo $this->createClassAttribute()?>">

<?php if ($this->partialHead):?>
  <div class="panel-head">
    <?php echo $this->partialHead?>
  </div>
<?php endif ?>

<?php if ($this->partialBody):?>
  <div class="panel-body">
    <?php echo $this->partialBody?>
  </div>
<?php endif ?>

<?php if ($this->partialFooter):?>
  <div class="panel-footer">
    <?php echo $this->partialFooter?>
  </div>
<?php endif ?>

</div>