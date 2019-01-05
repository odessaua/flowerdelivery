<?php

Yii::import('application.modules.discounts.components.DiscountHelper');
Yii::import('zii.widgets.jui.CJuiDatePicker');

return array(
	'id'=>'discountUpdateForm',
	'elements'=>array(
		'common_info'=>array(
			'type'=>'form',
			'title'=>Yii::t('DiscountsModule.admin', 'Общая информация'),
			'elements'=>array(
				'name'=>array(
					'type'=>'text',
				),
				'code'=>array(
					'type'=>'text',

				),
				'code'=>array(
					'type'=> 'text',
					'hint'=> CHtml::ajaxLink(
											'Генерировать код', 
											Yii::app()->createUrl('/admin/discounts/default/generatecode'),
											array
											(
												'success' => 'js:function(data)'
												. '{'
												. '$("#Promo_code").val(data);'
												. '}'
											)
								
									),
				),
				'active'=>array(
					'type'=>'checkbox',
				),
				'sum'=>array(
					'type'=>'text',
					'hint'=>Yii::t('DiscountsModule.admin', 'Укажите целое число или процент. Например 10%.'),
				),
				'start_date'=>array(
					'type'=>'CJuiDatePicker',
					'options'=>array(
						'dateFormat'=>'yy-mm-dd '.date('H:i:s'),
					),
				),
				'end_date'=>array(
					'type'=>'CJuiDatePicker',
					'options'=>array(
						'dateFormat'=>'yy-mm-dd '.date('H:i:s'),
					),
				),
			)
		)
	),
);

