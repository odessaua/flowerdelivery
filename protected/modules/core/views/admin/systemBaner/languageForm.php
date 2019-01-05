<?php

/**
 * Create/update language form 
 */

return array(
	'id'=>'languageUpdateForm',
    'enctype'=>'multipart/form-data',
	'elements'=>array(
		'name'=>array(
            'type'=>'text',
        ),
        'position'=>array(
            'type'=>'text',
        ),
		'url'=>array(
            'type'=>'text',
            ),
        'photo'=>array(
            'type'=>'file',
        ),
        'active'=>array(
            'type'=>'dropdownlist',
            'items'=>array(
                1=>'Да',
                0=>'Нет'
            ),
        ),
	),
);

