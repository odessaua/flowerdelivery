<?php
return array(
    'id'=>'paymentStatusUpdateForm',
    'elements'=>array(
        'tab1'=>array(
            'type'=>'form',
            'title'=>'',
            'elements'=>array(
                'name'=>array(
                    'type'=>'text',
                ),
                'message'=>array(
                    'type'=>'SRichTextarea',
                ),
                'active'=>array(
                    'type'=>'dropdownlist',
                    'items'=>array(
                        1=>Yii::t('StoreModule.admin', 'Да'),
                        0=>Yii::t('StoreModule.admin', 'Нет')
                    ),
                ),
            ),
        ),
    ),
);