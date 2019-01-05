 <?php

/**
 * Pages controller
 * @package modules.pages
 */
class PagesController extends Controller
{

	/**
	 * Filter pages by category
	 */
	public function actionList()
	{
		$model = $this->loadCategoryModel();

		if(!$model)
			throw new CHttpException(404, Yii::t('PagesModule.core', 'Category not found.'));

		$criteria = Page::model()
			->published()
			->filterByCategory($model)
			->getDbCriteria();

		$count = Page::model()->count($criteria);

		$pagination = new CPagination($count);
		$pagination->pageSize = ($model->page_size > 0) ? $model->page_size: $model->defaultPageSize;
		$pagination->applyLimit($criteria);

		$pages = Page::model()->findAll($criteria);
		$view  = $this->setDesign($model, 'list');

		$this->render($view, array(
			'model'      => $model,
			'pages'      => $pages,
			'pagination' => $pagination
		));
	}

	/**
	 * Display page by url.
	 * Example url: /page/some-page-url
	 *
	 * @throws CHttpException
	 */
	public function actionView($url=null)
	{

		$parts = explode('/', $url);
		$url   = end($parts);
		$model = Page::model()
			->published()
			->withUrl($url);
				
		if(sizeof($parts) > 1)
		{
			array_pop($parts);
			$model->filterByCategory($this->loadCategoryModel(implode('/', $parts)));
		}

		$model = $model->find(array('limit'=>1));
		
		$lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';

                    $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
		$translate = Yii::app()->db->createCommand()
			->select('title, full_description, short_description')
			->from('PageTranslate')
			->where('object_id=:id ', array(':id'=>$model->id))
			->andWhere('language_id=:lid ', array(':lid'=>$langArray->id))
			->queryRow();
		if(!$model)
			throw new CHttpException(404, Yii::t('PagesModule.core', 'Page not found'));
		$model->title=$translate['title'];
		$model->full_description=$translate['full_description'];
		$model->short_description=$translate['short_description'];
		$view = $this->setDesign($model, 'view');

		$this->render($view, array(
			'model'=>$model,
		));
	}

	public function loadCategoryModel($url=null)
	{
		if(!$url)
			$url = Yii::app()->request->getParam('url');

		return PageCategory::model()
			->withFullUrl($url)
			->find();
	}

	/**
	 * Override default method to return category full_url without encoded slash.
	 * TODO: Find right solution for '/' in url params.
	 */
	public function createUrl($route,$params=array(),$ampersand='&')
	{
		return urldecode(parent::createUrl($route,$params,$ampersand));
	}

}