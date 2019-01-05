<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $model OrderPaymentStatusLog */
$title = Yii::t('OrdersModule.admin', 'Лог платежа')
    . ' заказа #'.CHtml::encode($model->order_id)
    . ' '
    . CHtml::encode($model->payment_type)
    . ' от '
    . CHtml::encode($model->response_date
    );
$this->pageHeader =  $title;

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи платежей')=>$this->createUrl('index'),
    $title,
);
?>



<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'order_id',
		'payment_type',
		'status',
        'response_orig',
		array(
		    'name' => 'response_used' ,
            'type' => 'raw',
            'value' => function($data){
		        return '<pre>' . var_export(unserialize($data->response_used), true) . '</pre>';
            },
        ),
		'response_date',
	),
)); ?>