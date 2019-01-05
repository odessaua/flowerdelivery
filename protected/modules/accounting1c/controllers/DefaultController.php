<?php

Yii::import('application.modules.accounting1c.components.C1ProductsImport');
Yii::import('application.modules.accounting1c.components.C1Orders');

/**
 * Accepts request from 1C
 */
class DefaultController extends Controller
{

	public function actionIndex()
	{
		ob_start();
		$content = ob_get_contents();
		ob_end_clean();
		file_put_contents( Yii::getPathOfAlias('application.runtime') .'/temp.html', $content, FILE_APPEND );

		$request=Yii::app()->request;

		if($request->getQuery('password') != Yii::app()->settings->get('accounting1c', 'password'))
			exit('ERR_WRONG_PASS');

		if($request->getQuery('type') && $request->getQuery('mode'))
		{
			$type = $request->getQuery('type');

			if($type === 'catalog')
				C1ProductsImport::processRequest($request->getQuery('mode'));
			if($type === 'sale')
				C1Orders::processRequest($request->getQuery('mode'));
		}

		exit;
	}

}
