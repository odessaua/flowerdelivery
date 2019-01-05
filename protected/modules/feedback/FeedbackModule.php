<?php

/**
 * Feedback modules
 */
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 //error_reporting(E_ALL);
 
class FeedbackModule extends BaseModule
{
	public $moduleName = 'feedback';

	/**
	 * Install default settings
	 */
	public function afterInstall()
	{
		Yii::app()->settings->set('feedback', array(
			'admin_email'        =>  Yii::app()->params['adminEmail'],
			'enable_captcha'     => '1',
			'max_message_length' => 1000
		));
	}

	/**
	 * Remove settings
	 */
	public function afterRemove()
	{
		Yii::app()->settings->clear('feedback');
	}
}
