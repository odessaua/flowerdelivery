<?php

return array(
    'id'=>'CitySeoUpdateForm',
    'showErrorSummary'=>true,
    'enctype'=>'multipart/form-data',
    'elements'=>array(
        'content'=>array(
            'type'=>'form',
            'title'=>Yii::t('StoreModule.admin', 'Общая информация'),
            'elements'=>array(
                'city_id' => array(
                    'type' => 'dropdownlist',
                    'items' => CitySeo::model()->getCityList(true),
                ),
                'lang_id' => array(
                    'type' => 'dropdownlist',
                    'items' => CitySeo::model()->getLangList(true),
                ),
                'seo_title'=>array(
                    'type'=>'text',
                ),
                'seo_text'=>array(
                    'type'=>'textarea',
                ),
                'seo_keywords'=>array(
                    'type'=>'textarea',
                ),
                'seo_description'=>array(
                    'type'=>'textarea',
                ),
            ),
        ),


    ),
);

