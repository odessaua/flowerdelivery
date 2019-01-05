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
                'alias'=>array(
                    'type'=>'text',
                    'placeholder' => 'Разделитель - запятая'
                ),
                'region_id' => array(
                    'type' => 'dropdownlist',
                    'items' => Region::model()->language(1)->getRegionList(true),
                ),
				'delivery'=>array(
					'type'=>'text',
				),
				'show_in_popup'=>array(
					'type'=>'checkbox',
				),
                'h1_header'=>array(
                    'type'=>'text',
                ),
                'firm_name'=>array(
                    'type'=>'text',
                ),
                'firm_address'=>array(
                    'type'=>'text',
                ),
                'firm_postcode'=>array(
                    'type'=>'text',
                ),
                'firm_phone'=>array(
                    'type'=>'text',
                ),
                'firm_comment'=>array(
                    'type'=>'textarea',
                ),
                'firm_show'=>array(
                    'type'=>'checkbox',
                ),
                'main_in_region'=>array(
                    'type'=>'checkbox',
                ),
			),
		),
		
		
	),
);

