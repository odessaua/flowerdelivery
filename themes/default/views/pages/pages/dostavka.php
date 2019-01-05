<div>
    <!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="/"><?=Yii::t('main','Home page')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?=Yii::t('main','About Delivery')?></li>
    </ul>
    <!-- breadcrumbs (end) -->


    <h1 class="page-title"><?=$model->title?></h1>

    <div class="g-clearfix">
        <div style=" float: right; border: 1px solid #7a1c4a;  margin: 20px;">
            <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/delivery.jpg" width="304px" height="566px" alt="7Roses delivery"/>
        </div>
        <div class="content-text delivery-content-text">
            <?php echo $model->full_description; ?>
        </div>
    </div>
</div>
