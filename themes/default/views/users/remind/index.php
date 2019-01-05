<?php
/**
 * Remind user password view
 */

$this->pageTitle = Yii::t('UsersModule.core','Remind password');
$this->pageTitle = Yii::t('UsersModule.core','Personal Account - Password Remind');
$this->pageDescription = Yii::t('UsersModule.core','If you have lost your account password please specify your email address to receive link for resetting it.');
?>

<h1 class="has_background"><?php echo Yii::t('main','Remind password'); ?></h1>

<div class="login_box rc5">
	<div class="data-form">
	<?php echo Yii::t('UsersModule.core','<p>Please enter your e-mail address to retrieve your password</p>'); ?>
		<?php
		echo CHtml::form();
		echo CHtml::errorSummary($model);
		?>

		<div class="s1">
			<?php echo CHtml::activeLabel($model,'email', array('required'=>true)); ?>
			<?php echo CHtml::activeTextField($model,'email'); ?>
		</div>

		<div class="s1">
			<?php echo CHtml::submitButton(Yii::t('UsersModule.core', 'Remind'),array('class'=>'btn-purple')); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::link(Yii::t('UsersModule.core', 'Registration'), array('register/register')) ?><br>
			
		</div>
		<?php echo CHtml::endForm(); ?>
	</div>
</div>
