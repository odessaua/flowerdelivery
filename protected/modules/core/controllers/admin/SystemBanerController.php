<?php

/**
 * Manage system languages
 * @package core.systemLanguages
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
class SystemBanerController extends SAdminController
{

	public function actionIndex()
	{
		$model = new SSystemBaner('search');

		if (!empty($_GET['SSystemBaner']))
			$model->attributes = $_GET['SSystemBaner'];

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
			$model = new SSystemBaner;
		else{

			$model = SSystemBaner::model()->findByPk($_GET['id']);
			$photo = $model->photo;
		}

		if (!$model)
			throw new CHttpException(404, Yii::t('CoreModule.core', 'Слайдер не найден.'));
		$form = new SAdminForm('application.modules.core.views.admin.systemBaner.languageForm', $model);
		
		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['SSystemBaner'];
			
			$rand=rand(0,9999);
			
			$model->photo=CUploadedFile::getInstance($model,'photo');
			
			if ($model->validate())
			{
			    if($model->photo){
				    $model->photo->saveAs(Yii::getPathOfAlias('webroot').'/uploads/pic/'.$rand.$model->photo);
					$model->photo=$rand.$model->photo;
				}
				else $model->photo = $photo;
			    if($new === false)
				    $model->update();
			    else
			        $model->save();
				$this->setFlashMessage(Yii::t('CoreModule.core', 'Изменения успешно сохранены'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect(array('index'));
			}
			else $this->setFlashMessage(Yii::t('CoreModule.core', 'Изменения успешно не сохранены'));
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
			$model = SSystemBaner::model()->findAllByPk($_REQUEST['id']);

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