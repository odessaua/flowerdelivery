<?php

/**
 * @var $profile UserProfile
 * @var $user User
 * @var $form CActiveForm
 * @var $changePasswordForm ChangePasswordForm
 */

$this->pageTitle=Yii::t('UsersModule.core', 'My account');
?>
<h1 class="has_background"><?php echo Yii::t('UsersModule.core', 'My account'); ?></h1>

<div class="data-form">
	<?php $form=$this->beginWidget('CActiveForm'); ?>

	<?php echo $form->errorSummary(array($profile, $user)); ?>
	<table cellpadding="3" cellspacing="0" border="0"><tr><td>
	<div class="s1">		
		<?php echo $form->label($profile,'full_name'); ?></td><td>
		<?php echo $form->textField($profile,'full_name'); ?>
	</div>
		</td></tr>
		<tr><td>
	<div class="s1">
		<?php echo $form->label($user,'email'); ?></td><td>
		<?php echo $form->textField($user,'email'); ?>		
	</div>
		</td></tr>
		<tr><td>
	<div class="s1">		
		<?php echo $form->label($profile,'phone'); ?></td><td>
		<?php echo $form->textField($profile,'phone') ?>
	</div>
		</td></tr>
		<tr><td>
	<div class="s1">
		<?php echo $form->label($profile,'delivery_address'); ?></td><td>
		<?php echo $form->textField($profile,'delivery_address') ?>
	</div>
		</td></tr>
		<tr><td colspan=2>
	<div class="row submit">
		<?php echo CHtml::submitButton(Yii::t('UsersModule.core', 'Save'),array('class' => 'btn-purple')); ?>
	</div>

	<?php $this->endWidget(); ?>
	</td></tr>
	</table>
</div><!-- form -->

<div style="clear: both;"></div>

<div class="data-form">
	<?php $form=$this->beginWidget('CActiveForm'); ?>

	<?php echo $form->errorSummary($changePasswordForm); ?>

	<div class="s1">
		<label></label>
		<div class="title"><?php echo Yii::t('UsersModule.core', 'Change password'); ?></div>
	</div>
<table cellpadding="3" cellspacing="0" border="0"><tr><td>
	<div class="s1">		
		<?php echo $form->label($changePasswordForm,Yii::t('UsersModule.core', 'Current password')); ?></td><td>
		<?php echo $form->passwordField($changePasswordForm,'current_password') ?>
	</div>
		</td></tr>
		<tr><td>
	<div class="s1">
		<?php echo $form->label($changePasswordForm,Yii::t('UsersModule.core', 'New password')); ?></td><td>
		<?php echo $form->passwordField($changePasswordForm,'new_password') ?>
	</div>
		</td></tr>
		<tr><td colspan=2>
	<div class="row submit">
		<?php echo CHtml::submitButton(Yii::t('UsersModule.core', 'Submit'),array('class' => 'btn-purple')); ?>
	</div>
		</td></tr></table>
	<?php $this->endWidget(); ?>
</div><!-- form -->
