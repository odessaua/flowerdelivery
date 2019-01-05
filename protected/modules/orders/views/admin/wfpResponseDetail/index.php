<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $dataProvider CActiveDataProvider */

$this->pageHeader = Yii::t('OrdersModule.admin', 'Логи запросов WFP детальные');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи запросов WFP детальные'),
);

$this->widget('ext.sgridview.SGridView', array(
    'dataProvider'=>$dataProvider,
    'id'=>'wfpResponseDetailListGrid',
    'filter'=>$model,
    'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
        ),
        array(
            'class'=>'SGridIdColumn',
            'name'=>'id',
            'type'=>'html',
            'value'=>'CHtml::link(CHtml::encode($data->id), array("/orders/admin/wfpResponseDetail/view", "id"=>$data->id))',
        ),
        array(
            'name'=>'order_id',
            'type'=>'raw',
            'value'=>'CHtml::link(CHtml::encode($data->order_id), array("/orders/admin/wfpResponseDetail/view", "id"=>$data->id))',
        ),
        array(
            'name'=>'orderReference',
            'type'=>'raw',
            'filter' => false,
        ),
        array(
            'name'=>'response_ts',
            'type'=>'raw',
            'filter' => false,
        ),
        // Buttons
//        array(
//            'class'=>'CButtonColumn',
//            'template'=>'{update}{delete}',
//        ),
    ),
));
