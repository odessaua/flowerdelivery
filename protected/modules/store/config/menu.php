<?php

Yii::import('application.modules.store.StoreModule');

/**
 * Admin menu items for store module
 */
return array(
	'catalog'=>array(
		'position'=>3,
		'items'=>array(
			array(
				'label'=>Yii::t('StoreModule.admin', 'Все товары'),
				'url'=>Yii::app()->createUrl('store/admin/products'),
				'position'=>1
			),
				array(
				'label'=>Yii::t('StoreModule.admin', 'Типы товаров'),
				'url'=>Yii::app()->createUrl('store/admin/productType'),
				'position'=>2
			),
			array(
				'label'=>Yii::t('StoreModule.admin', 'Варианты товара'),
				'url'=>Yii::app()->createUrl('store/admin/attribute'),
				'position'=>3
			),
			array(
				'label'=>Yii::t('StoreModule.admin', 'Категории'),
				'url'=>Yii::app()->createUrl('store/admin/category/create'),
				'position'=>4
			),
            array(
                'label'=>Yii::t('StoreModule.admin', 'Сортировка товаров'),
                'url'=>Yii::app()->createUrl('store/admin/productSorting'),
                'position'=>5
            ),
 
			array(
				'label'=>Yii::t('StoreModule.admin', 'Города Доставки'),
				'url'=>Yii::app()->createUrl('store/admin/deliveryRegions'),
				'position'=>6
			),

            array(
                'label'=>Yii::t('StoreModule.admin', 'Города - Meta Tags'),
                'url'=>Yii::app()->createUrl('store/admin/citySeo'),
                'position'=>7
            ),
           array(
                'label'=>Yii::t('StoreModule.admin', 'Области Украины'),
                'url'=>Yii::app()->createUrl('store/admin/region'),
                'position'=>8
            ),
			array(
				'label'=>Yii::t('StoreModule.admin', 'Открытка и Фото'),
				'url'=>Yii::app()->createUrl('store/admin/delivery'),
				'position'=>9
			),

			array(
				'label'=>Yii::t('StoreModule.admin', 'Варианты оплаты'),
				'url'=>Yii::app()->createUrl('store/admin/paymentMethod'),
				'position'=>10
			),
			array(
				'label'=>Yii::t('StoreModule.admin', 'Курсы валют'),
				'url'=>Yii::app()->createUrl('store/admin/currency'),
				'position'=>11
			),
			// array(
				// 'label'=>Yii::t('StoreModule.admin', 'Производители'),
				// 'url'=>Yii::app()->createUrl('store/admin/manufacturer'),
				// 'position'=>12
			// ),
		),
	),
);