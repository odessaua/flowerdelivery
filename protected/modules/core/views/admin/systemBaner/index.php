<?php

	/** Display pages list **/
	$this->pageHeader = Yii::t('CoreModule.core', 'Банеры');

	$this->breadcrumbs = array(
		'Home'=>$this->createUrl('/admin'),
		Yii::t('CoreModule.core', 'Банеры'),
	);

	$this->topButtons = $this->widget('application.modules.admin.widgets.SAdminTopButtons', array(
		'template'=>array('create'),
		'elements'=>array(
			'create'=>array(
				'link'=>$this->createUrl('create'),
				'title'=>Yii::t('CoreModule.core', 'Создать банер'),
				'icon'=>'plus',
			),
		),
	));

	$this->widget('ext.sgridview.SGridView', array(
		'dataProvider'=>$model->search(),
		'id'=>'languagesListGrid',
		'filter'=>$model,
		//'ajaxUpdate'=>false,
		'columns'=>array(
			array(
				'class'=>'CCheckBoxColumn',
			),
			array(
				'class'=>'SGridIdColumn',
				'name'=>'id',
			),
			array(
				'name'=>'name',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->name), array("update", "id"=>$data->id))',
			),

			array(
				'name'=>'url',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->url), array("update", "id"=>$data->id))',
			),
			array(
				'name'=>'photo',
				'type'=>'raw',
				'value'=>'CHtml::image("/uploads/pic/".$data->photo,"", array("width"=>"75px" ,"height"=>"100px"))',
			),
            array(
                'name'=>'position',
                'type'=>'raw',
            ),
            array(
                'name' => 'active',
                'filter' => array(1 => 'Да', 0 => 'Нет'),
                'value' => '$data->active > 0 ? "Да" : "Нет"',
            ),
		),
	));
