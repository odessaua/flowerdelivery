<?php
    // Language create/edit view

    $this->topButtons = $this->widget('application.modules.admin.widgets.SAdminTopButtons', array(
        'form'=>$form,
        'deleteAction'=>$this->createUrl('/pages/admin/default/delete', array('id'=>$model->id)),
		'langSwitcher'=>!$model->isNewRecord,
    ));

    $title = ($model->isNewRecord) ? Yii::t('CoreModule.admin', 'Создание слайда') :
        Yii::t('CoreModule.admin', 'Редактирование слайда');

    $this->breadcrumbs = array(
        'Home'=>$this->createUrl('/admin'),
        Yii::t('CoreModule.admin', 'Слайдер')=>$this->createUrl('index'),
        ($model->isNewRecord) ? Yii::t('CoreModule.admin', 'Создание слайда') : CHtml::encode($model->name),
    );

    $this->pageHeader = $title;
?>

<div class="form wide padding-all">
    <?php echo $form ?>
</div>

