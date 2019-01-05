<?php

/**
 * Manage system languages
 * @package core.systemLanguages
 */
error_reporting(-1);
ini_set('display_errors', '1');
class SystemSliderController extends SAdminController
{

	public function actionIndex()
	{
		$model = new SSystemSlider('search');

		if (!empty($_GET['SSystemSlider']))
			$model->attributes = $_GET['SSystemSlider'];

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
			$model = new SSystemSlider;
		else{
			
			$model = SSystemSlider::model()->language($_GET)->findByPk($_GET['id']);
			$photo = $model->photo;
		}

		if (!$model)
			throw new CHttpException(404, Yii::t('CoreModule.core', 'Слайдер не найден.'));
		$form = new SAdminForm('application.modules.core.views.admin.systemSlider.languageForm', $model);
		
		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['SSystemSlider'];
			$rand=rand(0,9999);
			$model->photo=CUploadedFile::getInstance($model,'photo');
			if ($model->validate())
			{
				if($model->photo){
					$model->photo->saveAs(Yii::getPathOfAlias('webroot').'/uploads/slider/'.$rand.$model->photo);				
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
			$model = SSystemSlider::model()->findAllByPk($_REQUEST['id']);

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