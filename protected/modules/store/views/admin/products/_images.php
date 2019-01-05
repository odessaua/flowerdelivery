<?php
/**
 * Images tabs
 */
Yii::import('ext.jqPrettyPhoto');
Yii::import('application.modules.store.components.StoreImagesConfig');

// Register view styles
Yii::app()->getClientScript()->registerCss('infoStyles', "
	table.imagesList {
		float: left;
		width: 45%;
		min-width:250px;
		margin-right: 15px;
		margin-bottom: 15px;
	}
	div.MultiFile-list {
		margin-left:190px
	}
");

// Upload button
echo CHtml::openTag('div', array('class'=>'row'));
echo CHtml::label(Yii::t('StoreModule.admin', 'Выберите главное изображение'), 'files');
	$this->widget('system.web.widgets.CMultiFileUpload', array(
		'name'=>'StoreProductImages',
		'model'=>$model,
		'attribute'=>'files',
		'accept'=>implode('|', StoreImagesConfig::get('extensions')),
	));
echo CHtml::closeTag('div');

// Images
if ($model->images)
{
	foreach($model->images as $image)
	{
		$this->widget('zii.widgets.CDetailView', array(
			'data'=>$image,
			'id'=>'ProductImage'.$image->id,
			'htmlOptions'=>array(
				'class'=>'detail-view imagesList',
			),
			'attributes'=>array(
				array(
					'label'=>Yii::t('StoreModule.admin', 'Изображение'),
					'type'=>'raw',
					'value'=>CHtml::link(
						CHtml::image($image->getUrl(false, false,true), CHtml::encode($image->name), array('height'=>'150px',)),
						$image->getUrl(false, false, true),
						array('target'=>'_blank', 'class'=>'pretty', 'title'=>CHtml::encode($model->name))
					),
				),
				'id',
				array(
					'name'=>'is_main',
					'type'=>'raw',
					'value'=>CHtml::radioButton('mainImageId', $image->is_main, array(
						'value'=>$image->id,
					)),
				),
				array(
					'name'=>'author',
					'type'=>'raw',
					'value'=>($image->author) ? CHtml::encode($image->author->username) : '',
				),
				'date_uploaded',
				array(
					'label'=>Yii::t('StoreModule.admin', 'Название'),
					'type'=>'raw',
					'value'=>CHtml::textField('image_titles['.$image->id.']', $image->title),
				),
				array(
					'label'=>Yii::t('StoreModule.admin', 'Действия'),
					'type'=>'raw',
					'value'=>CHtml::ajaxLink(Yii::t('StoreModule.admin', 'Удалить'),'/admin/store/products/deleteImage/?id='.$image->id,
						array(
							'type'=>'POST',
							'data'=>array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
							'success'=>"js:$('#ProductImage$image->id').hide().remove()",
						),
						array(
							'id'=>'DeleteImageLink'.$image->id,
							'confirm'=>Yii::t('StoreModule.admin', 'Вы действительно хотите удалить это изображение?'),
						)
					),
				),
			),
		));
	}
}

// Fancybox ext
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.pretty',
	'config'=>array(),
));

// xupload
?>
<link rel="stylesheet" href="/themes/default/assets/css/bootstrap_tb_css/bootstrap.css"/>
<div class="row">
<?php
echo CHtml::label(Yii::t('StoreModule.admin', 'Выберите дополнительные изображения'), 'files', array('style' => 'width: 100%; font-size: 14px; font-weight: bold; margin: 20px 0;'));
echo '<p>Оптимально: не более 4-х дополнительных изображений</p>';
echo '<p>Максимальный размер загружаемого файла: не более 250kb</p>';
$this->widget( 'xupload.XUpload', array(
        'url' => Yii::app( )->createUrl( "/admin/store/products/upload") . '/',
        //our XUploadForm
        'model' => $photos,
        //We set this for the widget to be able to target our own form
        'htmlOptions' => array('id'=>'productUpdateForm'),
        'attribute' => 'file',
        'multiple' => true,
        'autoUpload' => true,
        'options' => array(
            'maxNumberOfFiles' => 4,
            'limitMultiFileUploads' => 4,
            'fileInput' => '#XUploadForm_file',
            'maxFileSize' => (250 * 1024),
        ),
        //Note that we are using a custom view for our widget
        //Thats becase the default widget includes the 'form'
        //which we don't want here
        //'formView' => 'application.views.somemodel._form',
    )
);
?>
</div>
<script type="text/javascript">
    $(window).load(function() {
        $.getJSON('/admin/store/products/upload/?_method=list&id=<?=$model->id;?>', function (result) {
            var objForm = $('#productUpdateForm');
            if (result && result.length) {
                objForm.fileupload(); // !!!
                objForm.fileupload('option', 'done').call(objForm, null, {result: result});
                //console.log(result);
            }
        });
    });
</script>