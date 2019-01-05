<?php 
$this->widget(
      'application.widget.emultiselect.EMultiSelect',
      array('sortable'=>true, 'searchable'=>true)
);?>

	
<div style="margin-bottom: 15px;">Выберите регион доставки</div>
<?php 
$allcities = City::model()->findAll();
$cities = CHtml::listData($allcities, 'id', 'name');

$select = array();

if($model->isNewRecord){

	foreach($allcities as $city)
		{
			$select[] = $city->id;
		}
	
}
else {
	$productCities = StoreProductCityRef::model()->findAll(array('condition'=>'product_id='.$model->id));


		foreach($productCities as $city)
		{
			$select[] = $city->city_id;
		}
}
echo CHtml::dropDownList('cities', $select, $cities,
	array(
		'multiple' => 'multiple',
		'style' => 'height:400px;',
		'class'=>'multiselect'
	));
?>