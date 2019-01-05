<?php

class OrderPaymentStatusController extends SAdminController
{
    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new OrderPaymentStatus('search');

        if (!empty($_GET['OrderPaymentStatus']))
            $model->attributes = $_GET['OrderPaymentStatus'];

        $dataProvider = $model->search();
        $dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

        $this->render('index', array(
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $this->actionUpdate(true);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($new = false)
    {
        if ($new === true)
        {
            $model = new OrderPaymentStatus();
            $model->unsetAttributes();
        }
        else
            $model = OrderPaymentStatus::model()->language($_GET)->findByPk($_GET['id']);

        $form = new CForm('orders.views.admin.orderPaymentStatus.paymentStatusForm', $model);

        if(Yii::app()->request->isPostRequest)
        {
            $model->attributes = $_POST['OrderPaymentStatus'];

            if($model->validate())
            {
                $model->save();
                $this->setFlashMessage(Yii::t('OrdersModule.admin', 'Изменения успешно сохранены'));

                if(isset($_POST['REDIRECT']))
                    $this->smartRedirect($model);
                else
                    $this->redirect(array('index'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'form' => $form,
        ));
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return OrderPaymentStatus the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=OrderPaymentStatus::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param OrderPaymentStatus $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='order-payment-status-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
