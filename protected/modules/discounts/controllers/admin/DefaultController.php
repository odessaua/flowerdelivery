<?php

/**
 * Discounts controller
 */
class DefaultController extends SAdminController
{

	/**
	 * Display discounts list
	 */
	public function actionIndex()
	{
		$model = new DiscountRegular('search');

		if (!empty($_GET['DiscountRegular']))
			$model->attributes = $_GET['DiscountRegular'];

		$dataProvider = $model->orderByName()->search();
		$dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

		$this->render('index', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * Display discounts list
	 */
	public function actionPromo()
	{
		$model = new Promo('search');

		if (!empty($_GET['Promo']))
			$model->attributes = $_GET['Promo'];

		$dataProvider = $model->orderByName()->search();
		$dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

		$this->render('promo', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Create new discount
	 */
	public function actionCreate()
	{
		$this->actionUpdate(true);
	}
	
	/**
	 * Create new promo code
	 */
	public function actionCreatePromo()
	{
		$this->actionUpdatePromo(true);
	}
	
	/**
	 * Update discount
	 * @param bool $new
	 * @throws CHttpException
	 */
	public function actionUpdate($new = false)
	{
		if ($new === true)
			$model = new DiscountRegular;
		else
			$model = DiscountRegular::model()->findByPk($_GET['id']);

		if (!$model)
			throw new CHttpException(404, Yii::t('DiscountsModule.admin', 'Скидка не найдена.'));

		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['DiscountRegular'];
		}

		$form = new STabbedForm('discounts.views.admin.default.discountForm', $model);

		$form->summaryOnEachTab = true;

		if (Yii::app()->request->isPostRequest)
		{
			if ($model->validate())
			{
				$model->save();
				$this->setFlashMessage(Yii::t('DiscountsModule.admin', 'Изменения успешно сохранены'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect('create');
			}
		}

		$this->render('update', array(
			'model'=>$model,
			'form'=>$form,
		));
	}
	
	public function actionUpdatePromo($new = false)
	{
		if ($new === true)
			$model = new Promo;
		else
			$model = Promo::model()->findByPk($_GET['id']);

		if (!$model)
			throw new CHttpException(404, Yii::t('DiscountsModule.admin', 'Скидка не найдена.'));

		if (Yii::app()->request->isPostRequest)
		{
			$model->attributes = $_POST['Promo'];
		}

		$form = new STabbedForm('discounts.views.admin.default.promoForm', $model);

		/* $form->additionalTabs = array(
			Yii::t('DiscountsModule.admin','Категории') => $this->renderPartial('_categories', array('model'=>$model), true),
		); */

		$form->summaryOnEachTab = true;

		if (Yii::app()->request->isPostRequest)
		{
			if ($model->validate())
			{
				$model->save();
				$this->setFlashMessage(Yii::t('DiscountsModule.admin', 'Изменения успешно сохранены'));

				if (isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect('createpromo');
			}
		}

		$this->render('update_promo', array(
			'model'=>$model,
			'form'=>$form,
		));
	}
	
	public function actionGenerateCode()
	{
		$code = CouponHelper::generate(6, "XYZ-", "-ABC");
		echo $code;
	}

	/**
	 * Delete discount
	 * @param array $id
	 */
	public function actionDelete($id = array())
	{
		if (Yii::app()->request->isPostRequest)
		{
			$model = Discount::model()->findAllByPk($_REQUEST['id']);

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