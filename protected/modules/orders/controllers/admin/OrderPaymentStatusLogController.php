<?php

class OrderPaymentStatusLogController extends SAdminController
{
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $model = new OrderPaymentStatusLog('search');

        if (!empty($_GET['OrderPaymentStatusLog']))
            $model->attributes = $_GET['OrderPaymentStatusLog'];

        $dataProvider = $model->search();
        $dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

        $this->render('index', array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return OrderPaymentStatusLog the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=OrderPaymentStatusLog::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param OrderPaymentStatusLog $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='order-payment-status-log-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
