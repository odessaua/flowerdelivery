<?php
$jsVariantsData = array();

	foreach($model->processVariants() as $variant)
	{
		$dropDownData=array();

		foreach($variant['options'] as $v)
		{
			$jsVariantsData[$v->id] = $v;
			$dropDownData[$v->id] = $v->option->value;
		}
	}
if($jsVariantsData):
?>

<div class="number g-clearfix">
	<?php echo CHtml::dropDownList('eav['.$variant['attribute']->id.']', null, $dropDownData, array('class'=>'variantData name',)); ?>	
	<?=Yii::t('StoreModule.core','quantity')?>
	<?php /*if($model->short_description): ?>
	<div class="sort sort-size">
	    <a class="drop-link" href="#" title=""><?=Yii::t('StoreModule.core','The composition and size')?></a>
	    <div class="sort-popup hidden">
	        <?=$model->short_description?>
	    </div>
	</div>
	<?php endif;*/?>
	
</div>

<?php
// Register variant prices script
Yii::app()->clientScript->registerScript('jsVariantsData','
		var jsVariantsData = '.CJavaScript::jsonEncode($jsVariantsData).';
	', CClientScript::POS_END);

endif;
?>

