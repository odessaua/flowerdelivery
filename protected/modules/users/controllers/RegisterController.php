<?php

/**
 * Realize user register
 */
class RegisterController extends Controller
{

	/**
	 * @return string
	 */
	public function allowedActions()
	{
		return 'register';
	}

	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}

	/**
	 * Creates account for new users
	 */
	public function actionRegister()
	{
		if(!Yii::app()->user->isGuest)
			Yii::app()->request->redirect('/');

		$user = new User('register');
		$profile = new UserProfile;

		if(Yii::app()->request->isPostRequest && isset($_POST['User'], $_POST['UserProfile']))
		{
			$user->attributes = $_POST['User'];
			$profile->attributes = $_POST['UserProfile'];

			$valid = $user->validate();
			$valid = $profile->validate() && $valid;

			if($valid)
			{
				$user->save();
				$profile->save();
				$profile->setUser($user);

				// Add user to authenticated group
				Yii::app()->authManager->assign('Authenticated', $user->id);

				$this->addFlashMessage(Yii::t('UsersModule.core', 'Thank you for registering on our site.'));

				// Authenticate user
				$identity = new UserIdentity($user->username, $_POST['User']['password']);
				if($identity->authenticate())
				{
					Yii::app()->user->login($identity, Yii::app()->user->rememberTime);
					Yii::app()->request->redirect($this->createUrl('/users/profile/index'));
				}
			}
		}

		$this->render('register', array(
			'user'    => $user,
			'profile' => $profile
		));
	}

}
