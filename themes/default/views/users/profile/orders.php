<?php

/**
 * View user orders
 * @var $orders
 */

$this->pageTitle=Yii::t('main', 'My orders history | Ukraine Flower delivery');
$this->pageDescription=Yii::t('main', 'My orders history. Check your orders status and payment');

?>
<h1 class="has_background"><?php echo Yii::t('OrdersModule.core', 'My orders'); ?></h1>
<?php
function replaceStatus($id)
{
    $statuses = array(
        1 => 'Pending',
        5 => 'Delivered',
        6 => 'Paid',
    );
    return (!empty($statuses[$id])) ? $statuses[$id] : 'Not Defined';
}
$payment_statuses = array(
    'new' => 'new',
    'pending' => 'pending',
    'paid' => 'paid',
    'rejected' => 'rejected',
);
?>
    <style>
        .blue-row{
            background-color: #fde7e7;
        }
        .yellow-row{
            background-color: #97F79A;
        }
        .green-row{
            background-color: #97F79A;
        }
        .blue-row > td,
        .yellow-row > td,
        .green-row > td{
            padding: 10px 5px !important;
        }
    </style>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dp,
    //'filter'=>$model,
    'rowCssClassExpression' => function($row, $data) {
        $row_colors = array(
            1 => 'blue-row',
            5 => 'green-row',
            6 => 'yellow-row',
        );
        return $row_colors[(int)$data->status_id];
    },
    'columns'=>array(
        array(
            'name' => 'id',
            'type' => 'raw',
            'header' => Yii::t('main', 'Order №'),
            'value'=>'CHtml::link($data->id, array("/orders/cart/view", "secret_key"=>$data->secret_key))',
            'htmlOptions' => array('style' => 'width: 75px;'),
        ),
        array(
            'name'=>'user_name',
            'header' => Yii::t('main', 'Sender Name'),
            'htmlOptions' => array('style' => 'width: 115px;'),
        ),
        array(
            'name'=>'receiver_name',
            'header' => Yii::t('main', 'Receiver Name'),
            'htmlOptions' => array('style' => 'width: 115px;'),
        ),
        array(
            'name'=>'datetime_del',
            'header' => Yii::t('main', 'Delivery Date'),
            'htmlOptions' => array('style' => 'width: 120px;'),
        ),
        array(
            'name'=>'receiver_city',
            'header' => Yii::t('main', 'Receiver City'),
            'htmlOptions' => array('style' => 'width: 130px;'),
        ),
        array(
            'type'=>'raw',
            'name'=>'full_price',
            'header' => Yii::t('main', 'Full price'),
            'value'=>'StoreProduct::formatPrice($data->full_price)',
            'htmlOptions' => array('style' => 'width: 70px;'),
        ),
        array(
            'name'=>'status_id',
            'header' => Yii::t('main', 'Order status'),
            'filter'=>CHtml::listData(OrderStatus::model()->orderByPosition()->findAll(), 'id', 'name'),
//            'value'=>'$data->status_name',
            'value'=>function($data){
                return Yii::t('main', replaceStatus($data->status_id));
            },
            'htmlOptions' => array('style' => 'width: 80px;'),
        ),
        array(
            'name' => 'payment_status',
			'header' => Yii::t('main', 'Payment status'),
            'filter' =>$payment_statuses,
            'type' => 'raw',
            'value'=>function($data){
                if($data->payment_status == 'paid')
                    return Yii::t('main', $data->payment_status);
                else
                    return CHtml::link($data->payment_status, array("/orders/cart/view", "secret_key"=>$data->secret_key));
            },
            'htmlOptions' => array('style' => 'width: 80px;'),
        ),
        array(
            'name'=>'paid',
            'header' => Yii::t('main', 'Ordered Products'),
            'value'=>'OrderProduct::getProducts($data->products, ' . $langArray->id . ')',
        )
    ),
)); ?>

<?php
	/*$this->widget('zii.widgets.grid.CGridView', array(
		'id'           => 'ordersListGrid',
		'dataProvider' => $orders,
		'template'     => '{items}',
        'rowCssClassExpression' => function($row, $data) {
            $row_colors = array(
                1 => 'blue-row',
                5 => 'green-row',
                6 => 'yellow-row',
            );
            return $row_colors[(int)$data->status_id];
        },
		'columns' => array(
			array(
				'name'=>'user_name',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->user_name), array("/orders/cart/view", "secret_key"=>$data->secret_key))',
			),
			'receiver_name',
			'datetime_del',
			'receiver_city',
			'country',
			array(
				'name'=>'status_id',
				'filter'=>CHtml::listData(OrderStatus::model()->orderByPosition()->findAll(), 'id', 'name'),
				'value'=>'$data->status_name'
			),
			array(
				'name'=>'id',
				'filter'=>CHtml::listData(StoreDeliveryMethod::model()->orderByPosition()->findAll(), 'id', 'name'),
				'value'=>'$data->id'
			),
			array(
				'type'=>'raw',
				'name'=>'full_price',
				'value'=>'StoreProduct::formatPrice($data->full_price)',
			),
				array(
					'name'=>Yii::t("OrdersModule.core","Products"),
					'value'=>'OrderProduct::getProducts($data->products)'
				)
		),
	));*/
?>