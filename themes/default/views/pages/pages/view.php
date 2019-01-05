<?php

/**
 * View page
 * @var Page $model
 */

// Set meta tags
$this->pageTitle       = ($model->meta_title) ? $model->meta_title : $model->title;
$this->pageKeywords    = $model->meta_keywords;
$this->pageDescription = $model->meta_description;
?>

<div>
    <!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="/" title="Главная"><?=Yii::t('main','Home page')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?php echo $model->title; ?></li>
    </ul>
    <!-- breadcrumbs (end) -->


    <h1 class="page-title"><?php echo $model->title; ?></h1>

    <div class="g-clearfix">
        <div class="content-text">
            
            <?php echo $model->full_description; ?>
                
        </div>
        
    </div>
</div>