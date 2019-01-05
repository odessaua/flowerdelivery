<?php
/**
* Create/update delivery methods
*/
/* @var $this RegionController */
/* @var $model Region */

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
'form'=>$form,
'langSwitcher'=>!$model->isNewRecord,
'deleteAction'=>$this->createUrl('/store/admin/region/delete', array('id'=>$model->id))
));

$title = ($model->isNewRecord) ? Yii::t('StoreModule.admin', 'Создание области Украины') :
Yii::t('StoreModule.admin', 'Редактирование области Украины');

$this->breadcrumbs = array(
'Home'=>$this->createUrl('/admin'),
Yii::t('StoreModule.admin', 'Области Украины')=>$this->createUrl('index'),
($model->isNewRecord) ? Yii::t('StoreModule.admin', 'Создание области Украины') : CHtml::encode($model->name),
);

$this->pageHeader = $title;

?>

<div class="form wide padding-all">
    <?php echo $form ?>
</div>