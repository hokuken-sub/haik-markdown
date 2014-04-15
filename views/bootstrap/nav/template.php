<div class="<?php echo e($this->createClassAttribute())?>" role="navigation">
  <div class="container">

    <div class="navbar-header">
      <?php if ($this->forResponsive):?>
        <button type="button" data-toggle="collapse" data-target="#sample_nav_responsive_with_dropdown" class="navbar-toggle">
          <span class="sr-only">Toggle Navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      <?php endif ?>

      <?php echo $this->brandTitle ?>
    </div>

    <?php if ($this->forResponsive):?>

      <div class="collapse navbar-collapse" id="sample_nav_responsive_with_dropdown">
        <?php echo $this->contentBody?>
      </div>

    <?php else:?>

      <?php echo $this->contentBody?>

    <?php endif ?>

    <?php if ($this->actionButtons):?>
    
      <?php if ($this->wrapActionButtons):?>
        <div class="btn-group navbar-right">
          <?php echo $this->actionButtons?>
        </div>
      <?php else:?>

        <?php echo $this->actionButtons?>
      
      <?php endif ?>
        
    <?php endif ?>
  </div>
</div>
