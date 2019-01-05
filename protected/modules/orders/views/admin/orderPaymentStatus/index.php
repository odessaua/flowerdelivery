<?php
/* @var $this OrderPaymentStatusController */
/* @var $dataProvider CActiveDataProvider */

$this->pageHeader = Yii::t('OrdersModule.admin', 'Статусы платежей');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Статусы платежей'),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
    'template'=>array('create'),
    'elements'=>array(
        'create'=>array(
            'link'=>$this->createUrl('create'),
            'title'=>Yii::t('OrdersModule.admin', 'Создать статус платежа'),
            'options'=>array(
                'icons'=>array('primary'=>'ui-icon-cart')
            )
        ),
    ),
));

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
            'name'=>'name',
            'type'=>'raw',
            'value'=>'CHtml::link(CHtml::encode($data->name), array("/orders/admin/orderPaymentStatus/update", "id"=>$data->id))',
        ),
        array(
            'name'=>'active',
            'type'=>'raw',
            'filter' => array('0' => 'Нет', '1' => 'Да'),
            'value'=>'($data->active > 0) ? "Да" : "Нет"',
        ),
        // Buttons
//        array(
//            'class'=>'CButtonColumn',
//            'template'=>'{update}{delete}',
//        ),
    ),
));