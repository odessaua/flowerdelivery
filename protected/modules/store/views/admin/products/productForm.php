<?php

return array(
	'id'=>'productUpdateForm',
	'showErrorSummary'=>true,
	'enctype'=>'multipart/form-data',
	'elements'=>array(
		'content'=>array(
			'type'=>'form',
			'title'=>Yii::t('StoreModule.admin', 'Общая информация'),
			'elements'=>array(
				'name'=>array(
					'type'=>'text',
				),
				'price'=>array(
					'type'=>$this->model->use_configurations ? 'hidden' : 'text',
				),
				'old_price'=>array(
				    'type'=>'text',
                ),
				'type_id'=>array(
					'type'=>'dropdownlist',
					'items'=>StoreProductType::model()->getTypeList(),
				),
//				'sort'=>array(
//					'type'=>$this->model->use_configurations ? 'hidden' : 'text',
//				),
				'url'=>array(
					'type'=>'text',
				),
				'main_category_id'=>array(
					'type'=>'dropdownlist',
					'items'=>StoreCategory::flatTree(),
					'empty'=>'---',
				),
				'is_active'=>array(
					'type'=>'dropdownlist',
					'items'=>array(
						1=>Yii::t('StoreModule.admin', 'Да'),
						0=>Yii::t('StoreModule.admin', 'Нет')
					),
					'hint'=>Yii::t('StoreModule.admin', 'Отображать товар на сайте')
				),
                'sale_id'=>array(
                    'type'=>'dropdownlist',
                    'items'=>StoreProduct::getSales(),
                ),
				'long_delivery'=>array(
					'type'=>'dropdownlist',
					'items'=>array(
						1=>Yii::t('StoreModule.admin', 'Да'),
						0=>Yii::t('StoreModule.admin', 'Нет')
					),
				),
                'main_page'=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(
                        0=>Yii::t('StoreModule.admin', 'Нет'),
                        1=>Yii::t('StoreModule.admin', 'Да')
                    ),
                ),
                'img_alt'=>array(
                    'type'=>'text',
                ),
                'img_title'=>array(
                    'type'=>'text',
                ),
				'full_description'=>array(
					'type'=>'SRichTextarea',
				),
				'short_description'=>array(
					'type'=>'SRichTextarea',
				),
			),
		),
		'warehouse'=>array(
			'type'=>'form',
			'title'=>Yii::t('StoreModule.admin', 'Склад'),
			'elements'=>array(
				'sku'=>array(
					'type'=>'text',
				),
				'quantity'=>array(
					'type'=>'text',
				),
				'discount'=>array(
					'type'=>'text',
					'hint'=>Yii::t('StoreModule.admin', 'Укажите целое число или процент. Например 10%.'),
				),
				'auto_decrease_quantity'=>array(
					'type'=>'dropdownlist',
					'items'=>array(
						1=>Yii::t('StoreModule.admin', 'Да'),
						0=>Yii::t('StoreModule.admin', 'Нет')
					),
					'hint'=>Yii::t('StoreModule.admin', 'Автоматически уменьшать количество при создании заказа'),
				),
				'availability'=>array(
					'type'=>'dropdownlist',
					'items'=>StoreProduct::getAvailabilityItems()
				),
			),
		),
		'seo'=>array(
			'type'=>'form',
			'title'=>Yii::t('StoreModule.admin', 'Мета данные'),
			'elements'=>array(
				'meta_title'=>array(
					'type'=>'text',
					'hint'=>'Ограничение поле Title не более 70 символов',
					'maxlength'=>68,
				),
				'meta_keywords'=>array(
					'type'=>'textarea',
				),
				'meta_description'=>array(
					'type'=>'textarea',
					'maxlength'=>200,
					'hint'=>'Ограничение поле Description не более 200 символов',
				),
			),
		),
		'design'=>array(
			'type'=>'form',
			'title'=>Yii::t('StoreModule.admin', 'Дизайн'),
			'elements'=>array(
				'layout'=>array(
					'type'=>'text',
					'hint'=>Yii::t('StoreModule.admin', 'Пример: application.views.layouts.file_name'),
				),
				'view'=>array(
					'type'=>'text',
					'hint'=>Yii::t('StoreModule.admin', 'Пример: view_name')
				),
			),
		),
	),
);

