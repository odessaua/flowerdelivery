<?php

/**
 * Manage system languages
 * @package core.systemLanguages
 */
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
class SystemMenuController extends SAdminController
{

	public function actionIndex()
	{
		$model = new SystemMenu('search');
		// var_dump($model);die;
		if (!empty($_GET['SystemMenu']))
			$model->attributes = $_GET['SystemMenu'];

		$this->render('index', array(
			'model'=>$model,
		));
	}

	public function actionCreate()
	{
		$this->actionUpdate(true);
	}

	public function actionUpdate($new = false)
	{
		if ($new === true)
			$model = new SystemMenu;
		else
			$model = SystemMenu::model()
						->language($_GET)
						->findByPk($_GET['id']);

		if (!$model)
			throw new CHttpException(404, Yii::t('CoreModule.core', 'Язик не найден.'));

		$form = new SAdminForm('application.modules.core.views.admin.systemMenu.languageForm', $model);

		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['SystemMenu'];

			if ($model->validate())
			{
				$model->save();

				$this->setFlashMessage(Yii::t('CoreModule.core', 'Изменения успешно сохранены'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect(array('index'));
			}
		}

		$this->render('update', array(
			'model'=>$model,
			'form'=>$form,
		));
	}

	/**
	 * Delete language
	 */
	public function actionDelete()
	{
		if (Yii::app()->request->isPostRequest)
		{
			$model = SystemMenu::model()->findAllByPk($_REQUEST['id']);

			if(!empty($model))
			{
				foreach($model as $page)
					$page->delete();
			}

			if (!Yii::app()->request->isAjaxRequest)
				$this->redirect('index');
		}
	}

}