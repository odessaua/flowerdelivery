<?php

return array(
	'id'=>'DeliveryRegionUpdateForm',
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
				'delivery'=>array(
					'type'=>'text',
				),
				
			),
		),
		
		
	),
);

