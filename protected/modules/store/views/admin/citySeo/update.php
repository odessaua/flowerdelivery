<?php

/**
 * Create/update delivery methods
 */

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
    'form'=>$form,
    'langSwitcher'=>!$model->isNewRecord,
    'deleteAction'=>$this->createUrl('/store/admin/citySeo/delete', array('id'=>$model->id)),
    'updateAction'=>$this->createUrl('/store/admin/citySeo/update', array('city_id'=>$model->city_id, 'lang_id' => $_GET['lang_id'])),
    'defaultUpdateAction'=>$this->createUrl('/store/admin/citySeo/update', array('city_id'=>$model->city_id, 'lang_id' => $_GET['lang_id'])),
));
$city_name = (!$model->isNewRecord) ? CitySeo::model()->getCityName($model->city_id) : '';
$title = ($model->isNewRecord) ? Yii::t('StoreModule.admin', 'Создание SEO для города') :
    Yii::t('StoreModule.admin', 'Редактирование SEO для города') . ' ' . $city_name;

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('StoreModule.admin', 'SEO для городов')=>$this->createUrl('index'),
    ($model->isNewRecord) ? Yii::t('StoreModule.admin', 'Создание SEO для города') : 'Редактирование SEO для города ' . $city_name,
);

$this->pageHeader = $title;

?>

<div class="form wide padding-all">
    <?php echo $form ?>
</div>