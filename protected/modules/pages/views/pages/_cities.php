<?php
/**
 * @var $cities
 * @var $language_code
 * @var $no_redirect
 */

$city_link_class = (!empty($no_redirect)) ? 'no-redirect-link' : '';
?>
<div class="pr-list-block">
<?php
if(!empty($cities)){
    $part_size = ceil((count($cities) / 3));
    $splitted = array_chunk($cities, $part_size);
    foreach ($splitted as $chunk) {
?>
<ul class="pr-cities-list">
   <?php foreach ($chunk as $city): ?>
       <?php
       $city_class = (!empty($city['main_in_region']))
           ? ' bold-city'
           : '';
       ?>
       <li>
           <?= CHtml::link(
                   $city['name'],
                   $language_code . Yii::app()->createUrl(
                       '/' . strtolower(str_replace(' ', '_', $city['eng_name']))
                   ).'/',
                   array('class' => $city_link_class . $city_class)
           );
           ?>
       </li>
   <?php endforeach; ?>
</ul>
<?php
    }
}
?>
</div>
<script>
    jQuery(document).ready(function ($) {
        $('.no-redirect-link').click(function (e) {
            e.preventDefault();
            var city = $(this).html();
            $.ajax({
                type: "GET",
                url: "/site/changeCity/",
                data: {city : city, lang : "<?= Yii::app()->language; ?>"},
                dataType: "text",
                success: function(data){
                    var city = data.split("_");
                    $(".cityName").text(city[0]);
                    $(".sort-popup").addClass("hidden");
                }
            });
        });
    });
</script>