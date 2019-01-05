<?php

/**
 * StoreModule routes
 */
return array(
	'product/<url>'                           => array('store/frontProduct/view', 'urlSuffix'=>'.html'),
	'product/photos/id/<id>'=>array('store/frontProduct/photos'),
	'product/captcha'                         => array('store/frontProduct/captcha'),
	'store/ajax/activateCurrency/<id>'        => array('store/ajax/activateCurrency'),
	'store/ajax/rateProduct/<id>'             => array('store/ajax/rateProduct'),
	'store/index/renderProductsBlock/<scope>' => array('store/index/renderProductsBlock'),
	'store/category/search' => array('store/category/search'),
	'store/category/search/sort/<sort>' => array('store/category/search'),
	'store/category/search/per_page/<per_page>' => array('store/category/search'),
	'store/category/search/sort/<sort>/per_page/<per_page>' => array('store/category/search'),
	array(
		'class' => 'application.modules.store.components.SStoreCategoryUrlRule'
	),
    array(
        'class' => 'application.modules.store.components.SStoreCityUrlRule'
    ),
	'products/search/*'                       => array('store/category/search'),

		// Compare
	'products/compare'             => array('store/compare/index'),
	'products/compare/add/<id>'    => array('store/compare/add'),
	'products/compare/remove/<id>' => array('store/compare/remove'),
	// WishList
	'wishlist'             => array('store/wishlist/index'),
	'wishlist/add/<id>'    => array('store/wishlist/add'),
	'wishlist/remove/<id>' => array('store/wishlist/remove'),
	'wishlist/view/<key>'  => array('store/wishlist/view'),
	'store/index'          => 'site/error',
	'store/compare'        => 'site/error',
	'store/wishlist'       => 'site/error',
	'manufacturer/<url>'   => array('/store/manufacturer/index'),
);