<?php

/**
 * Admin order statuses
 */
class StatusesController extends SAdminController
{
	/**
	 * Display statuses list
	 */
	public function actionIndex()
	{
		$model = new OrderStatus('search');
		$model->unsetAttributes();

		if (!empty($_GET['OrderStatus']))
			$model->attributes = $_GET['OrderStatus'];

		$dataProvider = $model->search();
		$dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');
//		$geo=$this->getGeoIpInfo($model->ip_address);
		$this->render('index', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
//			'geoinfo' => array('domain'=>$geo->domain,'country'=>$geo->country,'state'=>$geo->state,'town'=>$geo->town)

		));
	}

	/**
	 * Create new status
	 */
	public function actionCreate()
	{
		$this->actionUpdate(true);
	}
	/**
	 * Update status
	 * @param bool $new
	 * @throws CHttpException
	 */
	public function actionUpdate($new = false)
	{
		if ($new === true)
		{
			$model = new OrderStatus;
			$model->unsetAttributes();
		}
		else
			$model = OrderStatus::model()->findByPk($_GET['id']);

		if (!$model)
			throw new CHttpException(404, Yii::t('OrdersModule.admin', 'Статус не найден.'));

		$form = new SAdminForm('application.modules.orders.views.admin.statuses.statusForm', $model);
//		$geo=$this->getGeoIpInfo($model->ip_address);
		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['OrderStatus'];

			if ($model->validate())
			{
				$model->save();
				$this->setFlashMessage(Yii::t('OrdersModule.admin', 'Изменения успешно сохранены'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect('create');
			}
		}

		$this->render('update', array(
			'model'=>$model,
			'form'=>$form,
//			'geoinfo' => array('domain'=>$geo->domain,'country'=>$geo->country,'state'=>$geo->state,'town'=>$geo->town)
			
		));
	}

	/**
	 * Delete status
	 * @param array $id
	 */
	public function actionDelete($id = array())
	{
		if (Yii::app()->request->isPostRequest)
		{
			$model = OrderStatus::model()->findAllByPk($_REQUEST['id']);

			if (!empty($model))
			{
				foreach($model as $m)
				{
					if($m->countOrders() == 0 && $m->id != 1)
						$m->delete();
					else
						throw new CHttpException(409, Yii::t('OrdersModule.admin','Ошибка удаления статуса. Он используется заказами.'));
				}
			}

			if (!Yii::app()->request->isAjaxRequest)
				$this->redirect('index');
		}
	}
}
