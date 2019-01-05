<?php

return array(
	'id'=>'productCategoryUpdateForm',
	'showErrorSummary'=>true,
	'elements'=>array(
		'content'=>array(
			'type'=>'form',
			'title'=>Yii::t('StoreModule.admin', 'Общая информация'),
			'elements'=>array(
				'name'=>array(
					'type'=>'text',
				),
				'url'=>array(
					'type'=>'text',
				),
				'description'=>array(
					'type'=>'textarea',
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
					'size'=>70,
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
				),
				'view'=>array(
					'type'=>'text',
				),
			),
		),
	),
);

