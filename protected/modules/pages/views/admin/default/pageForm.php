<?php

/*** Create/update page form ***/
Yii::import('zii.widgets.jui.CJuiDatePicker');

return array(
	'id'=>'pageUpdateForm',
	'showErrorSummary'=>true,
	'elements'=>array(
		'content'=>array(
			'type'=>'form',
			'title'=>Yii::t('PagesModule.core', 'Содержимое'),
			'elements'=>array(
				'title'=>array(
					'type'=>'text',
				),
				'url'=>array(
					'type'=>'text',
				),
				'category_id'=>array(
					'type'=>'dropdownlist',
					'items'=>PageCategory::keyValueList(),
					'empty'=>'---',
				),
				'short_description'=>array(
					'type'=>'SRichTextarea',
				),
				'full_description'=>array(
					'type'=>'SRichTextarea',
				),
			),
		),
		'seo'=>array(
			'type'=>'form',
			'title'=>Yii::t('PagesModule.core', 'Мета данные'),
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
		'additional'=>array(
			'type'=>'form',
			'title'=>Yii::t('PagesModule.core', 'Дополнительно'),
			'elements'=>array(
				'status'=>array(
					'type'=>'dropdownlist',
					'items'=>Page::statuses()
				),
				'publish_date'=>array(
					'type'=>'CJuiDatePicker',
					'options'=>array(
						'dateFormat'=>'yy-mm-dd '.date('H:i:s'),
					),
				),
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

