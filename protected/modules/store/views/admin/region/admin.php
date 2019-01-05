<?php

/**
 * Display delivery methods list
 **/

$this->pageHeader = Yii::t('StoreModule.admin', 'Области Украины');

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/index'),
    Yii::t('StoreModule.admin', 'Области Украины'),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
    'template'=>array('create'),
    'elements'=>array(
        'create'=>array(
            'link'=>$this->createUrl('create'),
            'title'=>Yii::t('StoreModule.admin', 'Создать новую область'),
            'options'=>array(
                'icons'=>array('primary'=>'ui-icon-plus')
            )
        ),
    ),
));

$this->widget('ext.sgridview.SGridView', array(
    'dataProvider'=>$dataProvider,
    'filter'=>$model,
    'columns'=>array(
        array(
            'class'=>'CCheckBoxColumn',
        ),
        array(
            'class'=>'SGridIdColumn',
            'name'=>'id'
        ),
        array(
            'name'=>'name',
            'type'=>'raw',
            'value'=>'CHtml::link(CHtml::encode($data->name), array("/store/admin/region/update", "id"=>$data->id))',
        ),

        // Buttons
        array(
            'class'=>'CButtonColumn',
            'template'=>'{update}{delete}',
        ),
    ),
));

?>


