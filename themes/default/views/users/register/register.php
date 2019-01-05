<?php

/**
 * @var UserProfile $profile
 * @var User $user
 * @var Controller $this
 */

$this->pageTitle = Yii::t('UsersModule.core','Personal Account Registration| Ukraine Flower delivery');
$this->pageDescription = Yii::t('UsersModule.core','After registration you will be able to get an access to the order history and ordert status information.');
?>

<h1 class="has_background"><?php echo Yii::t('UsersModule.core','Registration'); ?></h1>

<div class="login_box rc5">
	<div class="data-form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'user-register-form',
			'enableAjaxValidation'=>false,
		)); ?>
		<?php echo Yii::t('UsersModule.core','<p><strong>Benefits of an account:</strong></p><br>			
			<ul class="yes"><li>Check the statuses of your order and payment</li>
			<li>Keep track of all orders</li>
			<li>Store your details for quicker ordering</li>
			<li>Change your personal data</li>
			<li>Receive special discounts and offers </li></ul>','Register'); ?>
<br>
		<?php echo $form->errorSummary(array($user, $profile)); ?>
		
	<table cellpadding="3" cellspacing="0" border="0"><tr><td>
			<div class="s1">
			<?php echo $form->labelEx($user,'username'); ?></td><td>
			<?php echo $form->textField($user,'username'); ?>
			</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo $form->labelEx($user,'password'); ?></td><td>
			<?php echo $form->passwordField($user,'password'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo $form->labelEx($user,'email'); ?></td><td>
			<?php echo $form->textField($user,'email'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo $form->labelEx($profile,'full_name'); ?></td><td>
			<?php echo $form->textField($profile,'full_name'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo $form->labelEx($profile,'phone'); ?></td><td>
			<?php echo $form->textField($profile,'phone'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo $form->labelEx($profile,'delivery_address'); ?></td><td>
			<?php echo $form->textField($profile,'delivery_address'); ?>
		</div>
		</td></tr>
	
	
		<?php if(CCaptcha::checkRequirements()): ?>
		<tr><td>
			<div class="s1">
				<?php echo $form->labelEx($user,'verifyCode'); ?></td><td>
				<?php echo $form->textField($user,'verifyCode'); ?>
				<?php echo $form->error($user,'verifyCode'); ?>
			</div>	
			</td></tr>
			<tr><td></td><td>
			<div class="row">
				<label></label>
				<?php $this->widget('CCaptcha', array(
					'clickableImage' => true,
					'showRefreshButton' => false
				)); ?>
			</div>
			</td></tr>
		<?php endif; ?>
		
		</table>
		<table cellpadding="15"><tr><td>
		<div class="row buttons">
			<?php echo CHtml::submitButton(Yii::t('UsersModule.core', 'Register'),array('class'=>'btn-purple')); ?>
		</div>
</td><td>
		<div class="row buttons">
			<?php echo CHtml::link(Yii::t('UsersModule.core', 'Have an account?'), array('login/login')) ?><br>
		</div>
</td></tr></table>
		<?php $this->endWidget(); ?>
	</div><!-- form -->
</div>
