<div class="haik-plugin-media-list">

  <?php foreach ($this->items as $i => $item):?>
    <div class="media">

      <span class="<?php echo isset($item['align']) ? $item['align'] : 'pull-left' ?>">
        <?php echo isset($item['image']) ? $item['image'] : $defaultImage ?>
      </span>

      <?php if ((isset($item['heading']) && $item['heading'] !== '') or $item['body'] !== ''):?>
        <div class="media-body">
          <?php echo isset($item['heading']) ? $item['heading'] : '' ?>
          <?php echo $item['body'] ?>
        </div>
      <?php endif ?>
  </div>
  <?php endforeach ?>

</div>