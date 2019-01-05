<?php
/* @var $this OrderPaymentStatusLogController */
/* @var $dataProvider CActiveDataProvider */

$this->pageHeader = Yii::t('OrdersModule.admin', 'Логи статусов');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Логи статусов'),
);

$this->widget('ext.sgridview.SGridView', array(
    'dataProvider'=>$dataProvider,
    'id'=>'orderPaymentStatusListGrid',
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
            'name'=>'order_id',
            'type'=>'raw',
            'value'=>'CHtml::link(CHtml::encode($data->order_id), array("/orders/admin/orderPaymentStatusLog/view", "id"=>$data->id))',
        ),
        array(
            'name'=>'payment_type',
            'type'=>'raw',
            'filter' => array('portmone' => 'Portmone', 'wayforpay' => 'WayForPay'),
        ),
        array(
            'name'=>'status',
            'type'=>'raw',
            'filter' => array(
                'Unknown' => 'Unknown', 'InProcessing' => 'InProcessing', 'Approved' => 'Approved',
                'Pending' => 'Pending', 'Expired' => 'Expired', 'Declined' => 'Declined', 'PAYED' => 'PAYED',
                'CREATED' => 'CREATED', 'REJECTED' => 'REJECTED', 'WaitingAuthComplete' => 'WaitingAuthComplete',
                'Refunded' => 'Refunded', 'Voided' => 'Voided', 'RefundInProcessing' => 'RefundInProcessing'),
        ),
        'response_date',
        // Buttons
//        array(
//            'class'=>'CButtonColumn',
//            'template'=>'{update}{delete}',
//        ),
    ),
));
