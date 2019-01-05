<?php
if(!isset($popup))
	$popup = "city-simple";

$no_redirect = (!empty($no_redirect)) ? 1 : 0;
Yii::import('application.modules.store.models.Region');
$regions = Region::model()->language($this->language_info->id)->findAll(array('order' => 'translate.name ASC'));
$js_params = ', ' . $no_redirect . ', ' . $this->language_info->id . ', \'' . $this->language_info->code . '\'';
?>

<?=Yii::t('main','Recipient City')?>: 
<a href="#" title="" class="drop-link cityName">
	<?php
    $cityPopupInfo = $this->getCurrentCityInfo(true);
    echo $cityPopupInfo->name;
    ?>
</a>
<div class="sort-popup hidden">
    <h2 class="title"><?=Yii::t('main','Send flowers to any city')?></h2>
    <p><?=Yii::t('main','Start typing the name of the city, and we will help')?></p>
    
    <?php
	$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
		'name'=>$popup,
		'source'=>Yii::app()->createUrl('/site/autocompleteCity') . '/',
		// additional javascript options for the autocomplete plugin
		'options'=>array(
			'minLength' => '2',
			'showAnim'=>'fold',
			 'search' =>'js: function() {
	            var term = this.value.split(/,s*/).pop();
	            if(term.length < 2)
	                return false;
	         }',
	         'change' => 'js: function(event,ui){
	         	var city = ui.item.value;	
				$.ajax({
					type: "GET",
					url: "/site/changeCity/",
					data: {city : city, lang : "' . Yii::app()->language .'"},
					dataType: "text",
					success: function(data){
					    var city = data.split("_");
					    ' . (empty($no_redirect) ? '
					    if(city.length == 2){
					        document.location.href=city[1];
					    }
					    ' : '') . '
					    $(".cityName").text(city[0]);
						$(".sort-popup").addClass("hidden");
					}
				});
	         }',
	        'focus' =>'js: function() {
	            return false;
	         }',
		),
		'htmlOptions' => array(
			'placeholder'=>Yii::t('main','Enter the city of delivery'),
			'title' => Yii::t('main','Recipient City'),
		),
	));
	?>

    <div class="h-regions">
        <div class="regions pr-regions">
            <h2 class="title"><?=Yii::t('main','Ukraine')?></h2>
            <div class="hrr-content">
                <div class="pr-list-block">
                    <?php
                    if(!empty($regions)){
                        $part_size = ceil((count($regions) / 2));
                        $splitted = array_chunk($regions, $part_size);
                        foreach ($splitted as $chunk) {
                            ?>
                            <ul class="pr-regions-list">
                                <?php foreach ($chunk as $region): ?>
                                    <li>
                                        <span onclick="getCitiesList(<?= $region->id . $js_params; ?>);"><?= $region->name; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php
                        }
                    }
                    else
                        echo '<h5 style="text-align: center;">' . Yii::t('main', 'No data'). '</h5>';
                    ?>
                </div>
            </div>
        </div>
        <div class="regions pr-cities" style="display: none;">
            <h2 class="title" onclick="showRegions();"><span class="prc-header-arr">&lt;</span> <span class="prc-header"><?=Yii::t('main','Change Region')?></span></h2>
            <div class="hrc-content"></div>
        </div>
    </div>

	<?= CHtml::link(Yii::t('main','Didn\'t find city? Click here!'), Yii::app()->createUrl('/all-cities'), array('class' => 'all-cities')); ?>
</div>