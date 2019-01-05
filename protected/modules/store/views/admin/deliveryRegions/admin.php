<?php

/**
 * Display delivery methods list
 **/

$this->pageHeader = Yii::t('StoreModule.admin', 'Регионы доставки');

$this->breadcrumbs = array(
	'Home'=>$this->createUrl('/index'),
	Yii::t('StoreModule.admin', 'Регионы доставки'),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
	'template'=>array('create'),
	'elements'=>array(
		'create'=>array(
			'link'=>$this->createUrl('create'),
			'title'=>Yii::t('StoreModule.admin', 'Создать новый регион'),
			'options'=>array(
				'icons'=>array('primary'=>'ui-icon-plus')
			)
		),
	),
));

$this->widget('ext.sgridview.SGridView', array(
	'dataProvider'=>$dataProvider,
	'filter'=>$model,
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
		),
		array(
			'class'=>'SGridIdColumn',
			'name'=>'id'
		),
		array(
			'name'=>'name',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->name), array("/store/admin/deliveryRegions/update", "id"=>$data->id))',
		),
        array(
            'name' => 'region_id',
            'type' => 'text',
            'value' => 'City::model()->getRegionName($data->region_id)',
            'filter' => '',
        ),
		'delivery',
        array(
            'name'=>'show_in_popup',
            'header' => 'Во всплывающем окне',
            'type'=>'text',
            'value'=>'($data->show_in_popup > 0) ? "Да" : "Нет"',
            'filter' => array(0 => 'Нет', 1 => 'Да'),
        ),
        array(
            'name'=>'firm_show',
            'header' => 'Компания-представитель',
            'type'=>'text',
            'value'=>'City::model()->checkContacts($data->id)',
            'filter' => '',
        ),
        array(
            'name'=>'h1_header',
            'type'=>'text',
            'value'=>'City::model()->checkH1($data->id)',
            'filter' => '',
        ),
        array(
            'name'=>'main_in_region',
            'type'=>'text',
            'value'=>'($data->main_in_region > 0) ? "Да" : "Нет"',
            'filter' => array(0 => 'Нет', 1 => 'Да'),
        ),
		// Buttons
		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
			'buttons' => array(
				'delete' => array(
                	'url'=>'Yii::app()->createUrl("/admin/store/deliveryRegions/delete/?id=$data->id")',
				),
			),
		),
	),
));

?>


