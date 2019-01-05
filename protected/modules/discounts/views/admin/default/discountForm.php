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
				'active'=>array(
					'type'=>'checkbox',
				),
				'sum'=>array(
					'type'=>'text',
					'hint'=>Yii::t('DiscountsModule.admin', 'Укажите целое число или процент. Например 10%.'),
				),
				'min_price'=>array(
					'type'=>'text',
					'hint'=>Yii::t('DiscountsModule.admin', 'Минимальная cумма оплаченых заказов пользователя после которой будет действовать скидка.'),
				),
				'max_price'=>array(
					'type'=>'text',
					'hint'=>Yii::t('DiscountsModule.admin', 'Сумма после которой скидка перестанет действовать. <span style="color: #f50d0d;">Важно!</span> Сумму нужно указывать на еденицу меньше предыдущей скидки что бы мнинмальная и максимальная не совпадали на пример: скидка 10% мин. цена 1000, макс. цена 1999. скидка 20% мин.цена 2000, макс цена 3000 или 2999 и т.д'),
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

