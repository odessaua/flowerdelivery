<?php

/**
 * @var UserLoginForm $model
 * @var Controller $this
 */

$this->pageTitle = Yii::t('UsersModule.core','Personal Account | Ukraine Flower delivery');
$this->pageDescription = Yii::t('UsersModule.core','Log in to your account to get all orders history and tracking information');
?>

<h1 class="has_background"><?php echo Yii::t('UsersModule.core','Log in to your personal account'); ?></h1>
<div class="login_box rc5">
	<div class="data-form">
		<?php
			echo CHtml::form((Yii::app()->getBaseUrl(true) .'/users/login/'),'post', array('id'=>'user-login-form'));
			echo CHtml::errorSummary($model);
		?>
	<table cellpadding="4" cellspacing="0" border="0"><tr><td>
		<div class="s1">
			<?php echo CHtml::activeLabel($model,Yii::t('UsersModule.core','Login'), array('required'=>true)); ?></td><td>
			<?php echo CHtml::activeTextField($model,'username'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div class="s1">
			<?php echo CHtml::activeLabel($model,Yii::t('UsersModule.core','Password'), array('required'=>true)); ?></td><td>
			<?php echo CHtml::activePasswordField($model,'password'); ?>
		</div>
		</td></tr>
		<tr><td>
		<div>
			<?php echo CHtml::activeLabel($model,Yii::t('UsersModule.core','Remember me')); ?></td><td>
			<?php echo CHtml::activeCheckBox($model,'rememberMe'); ?>
		</div>
		</td></tr>
		<tr><td colspan=2>
			<div class="row buttons">
				<input type="submit" class="btn-purple" value="<?php echo Yii::t('UsersModule.core','Enter'); ?>"></td>
			</div>
		</tr></table>

			<div>
				<div class="row text"><?php echo CHtml::link(Yii::t('UsersModule', Yii::t('UsersModule.core','Registration')), array('register/register')) ?></div>
				<div class="row text"><?php echo CHtml::link(Yii::t('UsersModule.core', 'Forgot your password'), array('/users/remind')) ?></div>
			</div>
		<?php echo CHtml::endForm(); ?>
	</div>
</div>
