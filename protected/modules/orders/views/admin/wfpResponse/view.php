<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $model OrderPaymentStatusLog */
$title = Yii::t('OrdersModule.admin', 'Лог запроса WFP')
    . ' от '
    . CHtml::encode($model->response_ts);
$this->pageHeader =  $title;

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи запросов WFP')=>$this->createUrl('index'),
    $title,
);
?>



<?php
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'response_ts',
		'response_body',
        array(
            'label' => 'Readable',
            'type' => 'raw',
            'value' => function($data){
                return '<pre>' . var_export(json_decode($data->response_body, true), true) . '</pre>';
            },
        ),
	),
)); ?>
