<?php

/**
 * Admin site comments
 */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
class CommentsController extends SAdminController
{

	/**
	 * Display all site comments
	 */
	public function actionIndex()
	{
		$model = new Comment('search');

		if(!empty($_GET['Comment']))
			$model->attributes = $_GET['Comment'];

		$dataProvider = $model->search();
		$dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

		$this->render('index', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider
		));
	}

	/**
	 * Update comment
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionUpdate($id)
	{
		$model = Comment::model()->findByPk($id);

		if(!$model)
			throw new CHttpException(404, Yii::t('CommentsModule.admin', 'Comment not found'));

		$form = new CForm('comments.views.admin.comments.commentForm', $model);

		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['Comment'];
			if($model->validate())
			{
				$model->save();

				$this->setFlashMessage(Yii::t('CommentsModule.admin', 'Changes saved successfully'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect(array('index'));
			}
		}

		$this->render('update',array(
			'model' => $model,
			'form'  => $form
		));
	}

	public function actionUpdateStatus()
	{
		$ids    = Yii::app()->request->getPost('ids');
		$status = Yii::app()->request->getPost('status');
		$models = Comment::model()->findAllByPk($ids);

		if(!array_key_exists($status, Comment::getStatuses()))
			throw new CHttpException(404, Yii::t('CommentsModule.admin', 'Error checking the status.'));

		if(!empty($models))
		{
			foreach ($models as $comment)
			{
				$comment->status = $status;
				$comment->save();
			}
		}

		echo Yii::t('CommentsModule', 'Status changed successfully');
	}

	/**
	 * Delete comments
	 * @param array $id
	 */
	public function actionDelete($id = array())
	{
		if (Yii::app()->request->isPostRequest)
		{
			$model = Comment::model()->findAllByPk($_REQUEST['id']);

			if (!empty($model))
			{
				foreach($model as $m)
					$m->delete();
			}

			if (!Yii::app()->request->isAjaxRequest)
				$this->redirect('index');
		}
	}

}
