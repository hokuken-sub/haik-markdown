<div id="haik_plugin_carousel_<?php echo e($this->getId())?>" class="haik-plugin-carousel carousel slide" data-ride="carousel">

    <?php if ($this->options['indicatorsSet']): ?>
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <?php foreach ($this->items as $i => $item): ?>
        <li data-target="#haik_plugin_carousel_<?php echo e($i)?>" data-slide-to="<?php echo e($i)?>"></li>
      <?php endforeach ?>
    </ol>
    <?php endif ?>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
      <?php foreach ($this->items as $i => $item): ?>
        <div class="item<?php echo $i === 0 ? " active" : ''?>">
          <?php echo isset($item['image']) ? $item['image']: $data['defaultImage'] ?>
          <?php if ((isset($item['heading']) && $item['heading'] !== '') OR $item['body'] !== ''):?>
            <div class="carousel-caption">
              <?php echo isset($item['heading']) ? $item['heading'] : '' ?>
              <?php echo $item['body']?>
            </div>
          <?php endif ?>
        </div>
      <?php endforeach ?>
    </div>

    <?php if ($this->options['controlsSet']): ?>
    <!-- Controls -->
    <a class="left carousel-control" href="#haik_plugin_carousel_<?php echo e($this->getId())?>" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left"></span>
    </a>
    <a class="right carousel-control" href="#haik_plugin_carousel_<?php echo e($this->getId())?>" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right"></span>
    </a>
    <?php endif ?>

</div>
