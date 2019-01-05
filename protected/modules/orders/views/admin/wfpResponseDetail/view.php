<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $model OrderPaymentStatusLog */
$title = Yii::t('OrdersModule.admin', 'Лог запроса WFP детальный')
    . ' заказа #'.CHtml::encode($model->order_id)
    . ' от '
    . CHtml::encode($model->response_ts);
$this->pageHeader =  $title;

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи запросов WFP детальные')=>$this->createUrl('index'),
    $title,
);
?>



<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'order_id',
		'orderReference',
		'response_ts',
        'response_body',
		array(
		    'name' => 'response_data' ,
            'type' => 'raw',
            'value' => function($data){
		        return '<pre>' . var_export(unserialize($data->response_data), true) . '</pre>';
            },
        ),
	),
)); ?>
