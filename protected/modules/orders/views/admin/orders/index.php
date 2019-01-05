<?php

Yii::import('orders.components.SProductsPreviewColumn');
Yii::import('orders.components.OrderQuantityColumn');

/**
 * Display orders list
 **/

$this->pageHeader = Yii::t('OrdersModule.admin', 'Заказы');

$this->breadcrumbs = array(
	'Home'=>$this->createUrl('/admin'),
	Yii::t('OrdersModule.admin', 'Заказы'),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
	'template'=>array('create'),
	'elements'=>array(
		'create'=>array(
			'link'=>$this->createUrl('create'),
			'title'=>Yii::t('OrdersModule.admin', 'Создать заказ'),
			'options'=>array(
				'icons'=>array('primary'=>'ui-icon-cart')
			)
		),
	),
));

$payments_methods = CHtml::listData(StorePaymentMethod::model()->active()->orderByPosition()->findAll(), 'id', 'name');
$payments_methods[0] = '–';

// Register scripts
Yii::app()->clientScript->registerScriptFile(
    $this->module->assetsUrl.'/admin/orders.update.js',
    CClientScript::POS_END
);

$this->widget('ext.sgridview.SGridView', array(
	'dataProvider'=>$dataProvider,
	'id'=>'ordersListGrid',
	'filter'=>$model,
	'afterAjaxUpdate'=>"function(){registerDatePickers()}",
	'customActions'=>array(
        array(
            'label'=>Yii::t('StoreModule.admin', 'Оплачен'),
            'url'=>'#',
            'linkOptions'=>array(
                'onClick'=>"return setOrderStatus(6, '" . Yii::app()->request->csrfToken . "');",
            ),
        ),
        array(
            'label'=>Yii::t('StoreModule.admin', 'Доставлен'),
            'url'=>'#',
            'linkOptions'=>array(
                'onClick'=>"return setOrderStatus(5, '" . Yii::app()->request->csrfToken . "');",
            ),
        ),
    ),
	'columns'=>array(
		array(
			'class'=>'CCheckBoxColumn',
		),
		array(
			'class'=>'SGridIdColumn',
			'name'=>'id'
		),
		array(
			'name'=>'receiver_city',
			'type'=>'raw',
			'value'=>'CHtml::link(CHtml::encode($data->receiver_city), array("/orders/admin/orders/update", "id"=>$data->id))',
		),

		array(
			'name'=>'country',
			'type'=>'raw',
			'value'=>'$data->country',
			//'value'=>'OrderQuantityColumn::transliterate($data->city)',
			'headerHtmlOptions'=>array('style'=>'width: 120px;'),
		),
		array(
			'name'=>'user_name',
			'headerHtmlOptions'=>array('style'=>'width: 120px;'),
		),
		array(
			'name'=>'datetime_del',
			'headerHtmlOptions'=>array('style'=>'width: 90px;'),
		),
		'user_email',
        array(
            'name' => 'payment_id',
            'filter' => $payments_methods,
            'value' => function($data) use ($payments_methods){
                return $payments_methods[$data->payment_id];
            },
			'headerHtmlOptions'=>array('style'=>'width: 100px;'),
        ),
		array(
		    'name' => 'payment_status',
		    'filter' => $model->payment_statuses,
			'headerHtmlOptions'=>array('style'=>'width: 100px;'),
        ),
		array(
			'name'=>'status_id',
			'filter'=>CHtml::listData(OrderStatus::model()->orderByPosition()->findAll(), 'id', 'name'),
			'value'=>'$data->status_name'
		),
		array(
			'type'=>'raw',
			'name'=>'full_price',
			'value'=>'StoreProduct::formatPrice($data->total_price)',
			'headerHtmlOptions'=>array('style'=>'width: 60px;'),
		),
		array(
			'class'=>'SProductsPreviewColumn'
		),
		
		array(
		'name'=>'created',
		'headerHtmlOptions'=>array('style'=>'width: 130px; text-align:center;'),
		
		),
		array(
		'name'=>'lang',
		'headerHtmlOptions'=>array('style'=>'width:10px;'),
		
		),
		'admin_id',
		// Buttons
		array(
			'class'=>'CButtonColumn',
			'template'=>'{update}{delete}',
		),
	),
));
Yii::app()->clientScript->registerScript("Datepickers", "
function registerDatePickers(){
    jQuery('input[name=\"Order[created]\"]').datepicker({'dateFormat':'yy-mm-dd',});
	jQuery('input[name=\"Order[datetime_del]\"]').datepicker({'dateFormat':'dd/mm/yy',});
}
registerDatePickers();
");