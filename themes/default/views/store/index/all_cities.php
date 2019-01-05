<?php
/**
 * @var $cities
 * @var $regions
 */
?>
<style>
    .ncity-link{
        margin: 5px 0 5px 5px;
        display: block;
    }
    .ac-region{
        float: left;
        margin-bottom: 20px;
    }
    .ac-region-title{
        color: #7a1c4a;
    }
</style>
<h1 class="page-title" style="margin-bottom: 20px;"><?=Yii::t('main','We Deliver Across 350+ Cities in Ukraine');?></h1>
<?php
$parts = 5;
if(!empty($regions)){
    echo '<div class="ac-regions">';
    foreach($regions as $r_id => $region){
        if(empty($cities[$r_id])) continue;
        else{
        ?>
        <div class="ac-region" style="width: <?=(100/$parts);?>%;">
            <h3 class="ac-region-title"><?= $region['name']; ?></h3>
        <?php
        foreach($cities[$r_id] as $city){
            // замена пробелов на _ в названиях городов (krivoy rog --> krivoy_rog)
            echo CHtml::link($city['name'], strtolower(str_replace(' ', '_', '/'.$city['eng_name'].'/')), array('class' => 'ncity-link'));
        }
        ?>
        </div>
        <?php
        }
    }
    echo '</div>';
}
?>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('.ac-regions').masonry({
            // options
            itemSelector: '.ac-region'
        });
    });
</script>