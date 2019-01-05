<?php
/* @var $this OrderPaymentStatusController */
/* @var $model OrderPaymentStatus */
$title = ($model->isNewRecord) ? Yii::t('OrdersModule.admin', 'Создание статуса платежа') :
    Yii::t('OrdersModule.admin', 'Редактирование статуса платежа');
$this->pageHeader = $title;

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('OrdersModule.admin', 'Статусы платежей')=>$this->createUrl('index'),
    ($model->isNewRecord) ? Yii::t('OrdersModule.admin', 'Создание статуса платежа') :'# '.CHtml::encode($model->id),
);

$this->topButtons = $this->widget('admin.widgets.SAdminTopButtons', array(
    'form'=>$form,
    'langSwitcher'=>!$model->isNewRecord,
    'deleteAction'=>$this->createUrl('/store/admin/paymentMethod/delete', array('id'=>$model->id))
));
?>

<div class="form wide padding-all">
    <?php echo $form; ?>
</div>