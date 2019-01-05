<?php

/**
 * Create/update discount
 *
 * @var $model Discount
 */

$this->topButtons = $this->widget('application.modules.admin.widgets.SAdminTopButtons', array(
	'form'=>$form,
	'deleteAction'=>$this->createUrl('/discounts/admin/default/delete', array('id'=>$model->id))
));

$title = ($model->isNewRecord) ? Yii::t('DiscountsModule.admin', 'Создание промо кода') :
	Yii::t('DiscountsModule.admin', 'Редактирование промо кода');

$this->breadcrumbs = array(
	'Home'=>$this->createUrl('/admin'),
	Yii::t('DiscountsModule.admin', 'Промо коды')=>$this->createUrl('promo'),
	($model->isNewRecord) ? Yii::t('DiscountsModule.admin', 'Создание промо кода') : CHtml::encode($model->name),
);

$this->pageHeader = $title;

Yii::app()->clientScript->registerScriptFile(
	$this->module->assetsUrl.'/admin/default.update.js',
	CClientScript::POS_END
);

?>

<div class="form wide padding-all">
	<?php echo $form->asTabs(); ?>
</div>