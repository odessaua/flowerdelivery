<?php

return array(
	'cart'=>'orders/cart/index',
	'cart/GetCouponDiscount'=>'orders/cart/GetCouponDiscount',

	'/orders/cart/phone'=>'orders/cart/Phone',
	'cart/add'=>'orders/cart/add',
	// 'orders/cart/phone'=>'orders/cart/phone',
	'cart/remove/<index>'=>'orders/cart/remove',
	'cart/clear'=>'orders/cart/clear',
	'cart/renderSmallCart'=>'orders/cart/renderSmallCart',
	'cart/renderPopupCart'=>'orders/cart/renderPopupCart',
	'cart/view/<secret_key>'=>'orders/cart/view',
	'cart/view/<secret_key>/success/'=>'orders/cart/success',
	'cart/view/<secret_key>/status/'=>'orders/cart/status',
	'cart/pending/<secret_key>'=>'orders/cart/pending',
	'processPayment/*'=>'orders/payment/process',
	// 'orders/cart/endEmailAdmin'=>'orders/cart/admin'
	// 'orders/cart'=>'site/error',
);