<?php

/**
 * Display attributes list
 **/

$this->pageHeader = Yii::t('StoreModule.admin', 'Атрибуты');
$flagUrl = Yii::app()->getModule('admin')->assetsUrl.'/images/flags/png/';

$this->breadcrumbs = array(
	'Home'=>$this->createUrl('/admin'),
	Yii::t('StoreModule.admin', 'Атрибуты'),
);

$this->topButtons = $this->widget('application.modules.admin.widgets.SAdminTopButtons', array(
	'template'=>array('create'),
	'elements'=>array(
		'create'=>array(
			'link'=>$this->createUrl('create'),
			'title'=>Yii::t('StoreModule.admin', 'Создать атрибут'),
			'options'=>array(
				'icons'=>array('primary'=>'ui-icon-plus')
			)
		),
	),
));

$this->widget('ext.sgridview.SGridView', array(
	'dataProvider'=>$dataProvider,
	'id'=>'productsListGrid',
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
            'htmlOptions' => array(
                    'style' => 'width: 110px;',
            ),
            'type' => 'raw',
            'value'=>'CHtml::link(CHtml::encode($data->name), array("/store/admin/attribute/update", "id"=>$data->id))',
		),
        array(
            'name'=>'header',
            'header'=>'Заголовок на странице товара (en)',
        ),
		array(
			'name'=>'title',
            'header'=>'Единица товара (en)',
		),
		// Buttons
		array(
			'class'=>'CButtonColumn',
			'template'=>'{en} {ru} {ua} {delete}',
            'buttons' => array(
                'en' => array(
                    'url' => 'Yii::app()->urlManager->createUrl("/store/admin/attribute/update", array("id"=>$data->id, "lang_id"=>9))',
                    'imageUrl' => $flagUrl . 'us.png',
						'options' => array(
							'style' => 'margin-right: 10px;',
						),
                ),
                'ru' => array(
                    'url' => 'Yii::app()->urlManager->createUrl("/store/admin/attribute/update", array("id"=>$data->id, "lang_id"=>1))',
                    'imageUrl' => $flagUrl . 'ru.png',
                    'options' => array(
                        'style' => 'margin-right: 10px;',
                    ),
                ),
                'ua' => array(
                    'url' => 'Yii::app()->urlManager->createUrl("/store/admin/attribute/update", array("id"=>$data->id, "lang_id"=>10))',
                    'imageUrl' => $flagUrl . 'ua.png',
                    'options' => array(
                        'style' => 'margin-right: 10px;',
                    ),
                ),
            ),
            'htmlOptions' => array(
                'style' => 'width: 110px;',
            ),
		),
	),
));