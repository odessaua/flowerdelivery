<?php

$this->pageHeader = Yii::t('OrdersModule.admin', 'Последние заказы');

$this->breadcrumbs = array(
	'Home'=>$this->createUrl('/admin'),
	Yii::t('OrdersModule.admin', 'Главная'),
);

// Side bar
$this->sidebarContent = $this->renderPartial('_sidebar', array(
	'ordersTotalPrice'=>$this->getOrdersTotalPrice()
), true);


// Orders list
$this->widget('ext.sgridview.SGridView', array(
	'dataProvider'=>$ordersDataProvider,
	'id'=>'ordersListGrid',
	'selectableRows'=>0,
	'template'=>'{items}',
	'columns'=>array(
		array(
			'class'=>'SGridIdColumn',
			'name'=>'id'
		),
		array ('name'=>'receiver_city',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->receiver_city), array("/orders/admin/orders/update", "id"=>$data->id))',
			),
			'datetime_del',
		array(
			'name'=>'user_name',
			'type'=>'raw',
			//'value'=>'CHtml::link(CHtml::encode($data->user_name), array("/orders/admin/orders/update", "id"=>$data->id))',
		),
		'country',
		'user_email',
		array(
			'name'=>'status_id',
			'value'=>'$data->status_name'
		),
		array(
			'class'=>'SProductsPreviewColumn'
		),
		array(
			'type'=>'raw',
			'name'=>'full_price',
			'value'=>'StoreProduct::formatPrice($data->full_price)',
		),
		'created',
	),
));
