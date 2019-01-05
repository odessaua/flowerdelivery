<?php

/**
 * Display delivery methods list
 **/

$this->pageHeader = Yii::t('StoreModule.admin', 'SEO для городов');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/index'),
    Yii::t('StoreModule.admin', 'SEO для городов'),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
    'template'=>array('create'),
    'elements'=>array(
        'create'=>array(
            'link'=>$this->createUrl('create'),
            'title'=>Yii::t('StoreModule.admin', 'Создать новый SEO для города'),
            'options'=>array(
                'icons'=>array('primary'=>'ui-icon-plus')
            )
        ),
    ),
));
$columns = array(
//    array(
//        'class'=>'CCheckBoxColumn',
//    ),
    array(
        'class'=>'SGridIdColumn',
        'name'=>'id',
        'filter' => ''
    ),
//    array(
//        'name' => 'name',
//        'type' => 'text',
//        'value' => 'CitySeo::model()->getCityName($data->id)',
//        'filter' => '',
//    ),
    array(
        'name' => 'name',
        'type' => 'text',
        'header' => 'Город'
    ),
    array(
        'name' => 'region_id',
        'type' => 'text',
        'value' => 'City::model()->getRegionName($data->region_id)',
        'filter' => '',
    ),
//    array(
//        'name' => 'lang_id',
//        'type' => 'text',
//        'value' => 'CitySeo::model()->getLangName($data->lang_id)',
//        'filter' => '',
//    ),
);
$languages = CitySeo::model()->getLangList();
foreach($languages as $l_id => $lang){
    $columns[] = array(
        'name' => 'region_id',
        'header' => $lang,
        'type' => 'html',
        'value' => 'CitySeo::model()->checkLang($data->id, ' . $l_id . ')',
        'filter' => '',
    );
}
//$columns[] = array(
//    'class'=>'CButtonColumn',
//    'template'=>'{update}{delete}',
//);
$this->widget('ext.sgridview.SGridView', array(
    'dataProvider'=>$dataProvider,
    'filter'=>$model,
    'columns'=>$columns,
));

?>


