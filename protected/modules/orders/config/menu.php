<?php

Yii::import('orders.OrdersModule');

/**
 * Admin menu items for pages module
 */
return array(
	'orders'=>array(
		'label'       => Yii::t('OrdersModule.admin', 'Заказы'),
		'url'         => array('/orders/admin/orders'),
		'position'    => 2,
		'itemOptions' => array(
			'class'       => 'hasRedCircle circle-orders',
		),
		'items' => array(
			array(
				'label'       => Yii::t('OrdersModule.admin', 'Все заказы'),
				'url'         => array('/orders/admin/orders'),
				'position'    => 1
			),
			array(
				'label'    => Yii::t('OrdersModule.admin', 'Создать заказ'),
				'url'      => array('/orders/admin/orders/create'),
				'position' => 2
			),
			array(
				'label'    => Yii::t('OrdersModule.admin', 'Статусы заказов'),
				'url'      => array('/orders/admin/statuses'),
				'position' => 3
			),
            array(
                'label'    => Yii::t('OrdersModule.admin', 'Статусы платежей'),
                'url'      => array('/orders/admin/orderPaymentStatus'),
                'position' => 4
            ),
            array(
                'label'    => Yii::t('OrdersModule.admin', 'Логи статусов'),
                'url'      => array('/orders/admin/orderPaymentStatusLog'),
                'position' => 5
            ),
            array(
                'label'    => Yii::t('OrdersModule.admin', 'Логи запросов WFP'),
                'url'      => array('/orders/admin/wfpResponse'),
                'position' => 6
            ),
            array(
                'label'    => Yii::t('OrdersModule.admin', 'Логи запросов WFP детальные'),
                'url'      => array('/orders/admin/wfpResponseDetail'),
                'position' => 7
            ),
		),
	),
);