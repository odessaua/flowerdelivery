<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $dataProvider CActiveDataProvider */

$this->pageHeader = Yii::t('OrdersModule.admin', 'Логи запросов WFP');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи запросов WFP'),
);

$this->widget('ext.sgridview.SGridView', array(
    'dataProvider'=>$dataProvider,
    'id'=>'wfpResponseListGrid',
    'filter'=>$model,
    'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
        ),
        array(
            'class'=>'SGridIdColumn',
            'name'=>'id',
            'type'=>'html',
            'value'=>'CHtml::link(CHtml::encode($data->id), array("/orders/admin/wfpResponse/view", "id"=>$data->id))',
        ),
        array(
            'name'=>'response_body',
            'type'=>'raw',
        ),
        'response_ts',
        // Buttons
//        array(
//            'class'=>'CButtonColumn',
//            'template'=>'{update}{delete}',
//        ),
    ),
));
