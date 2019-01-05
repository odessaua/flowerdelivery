<?php
 // ini_set('display_errors', 1);
 // ini_set('display_startup_errors', 1);
 // error_reporting(E_ALL);
/**
 * Display category products
 * TODO: Add default sorting by rating, etc...
 *
 * @property $activeAttributes
 * @property $eavAttributes
 */
class CategoryController extends Controller
{

	/**
	 * @var StoreProduct
	 */
	public $query;

	/**
	 * @var StoreCategory
	 */
	public $model;

	/**
	 * @var array Eav attributes used in http query
	 */
	private $_eavAttributes;

	/**
	 * @var array
	 */
	public $allowedPageLimit = array();

	/**
	 * Current query clone to use in min/max price queries
	 * @var CDbCriteria
	 */
	public $currentQuery;

	/**
	 * @var CActiveDataProvider
	 */
	public $provider;

    /**
     * Дополнительные элементы для маркировки страниц Категорий (SEO)
     * @var array
     */
	public $pageMetaSeo = array(
	    'divider' => '',
	    'title' => '',
	    'num' => '',
    );

	/**
	 * @var string
	 */
	private $_minPrice;

	/**
	 * @var string
	 */
	private $_maxPrice;

	/**
	 * Load category model by url
	 *
	 * @param $action
	 * @return bool
	 */
	public function beforeAction($action)
	{
		$this->allowedPageLimit=explode(',',Yii::app()->settings->get('core', 'productsPerPage'));

		if(Yii::app()->request->getPost('min_price') || Yii::app()->request->getPost('max_price'))
		{
			$data=array();
			if(Yii::app()->request->getPost('min_price'))
				$data['min_price']=(int)Yii::app()->request->getPost('min_price');
			if(Yii::app()->request->getPost('max_price'))
				$data['max_price']=(int)Yii::app()->request->getPost('max_price');

			if($this->action->id==='search')
				$this->redirect(Yii::app()->request->addUrlParam('/store/category/search', $data));
			else
				$this->redirect(Yii::app()->request->addUrlParam('/store/category/view', $data));
		}

		return true;
	}

	/**
	 * Display category products
	 */
	public function actionView()
	{
		$lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';
		$page = isset($GET['page']) ? $GET['page'] : '';
        $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
		$this->model = $this->_loadModel(Yii::app()->request->getQuery('url'),$page);
		$model=$this->model;
		$categoryTrans=StoreCategoryTranslate::model()->findByAttributes(array('language_id'=>$langArray->id,'object_id'=>$model->id));
		$model->name=$categoryTrans->name;
		$view = $this->setDesign($this->model, 'view');
		$this->doSearch($this->model, $view);
	}

	/**
	 * Search products
	 */
	public function actionSearch()
	{
		if(Yii::app()->request->isPostRequest)
			$this->redirect( Yii::app()->request->url . '?q=' . Yii::app()->request->getPost('q'));
		$q=Yii::app()->request->getQuery('q');

		if(!$q)
			$this->render('search');
		$this->doSearch($q, 'search');
	}

	/**
	 * Search products
	 * @param $data StoreCategory|string
	 * @param string $view
	 */
	public function doSearch($data, $view)
	{
		$this->query = new StoreProduct(null);
//                $this->query=limit()
		$this->query->attachBehaviors($this->query->behaviors());
		$this->query->applyAttributes($this->activeAttributes)
			->active();
		$sort_prefix = '`categorization`.`order` ASC, ';
		if($data instanceof StoreCategory)
			$this->query->applyCategories($this->model);
		else
		{
		    $sort_prefix = '';
			$cr=new CDbCriteria;
//			$cr->with = array(
//				'translate'=>array('together'=>true),
//			);
            $cr->with = 'translate';
                        
			$cr->addSearchCondition('translate.name', $data);
			$this->query->getDbCriteria()->mergeWith($cr);
		}

		// Filter by manufacturer
		if(Yii::app()->request->getQuery('manufacturer'))
		{
			$manufacturers = explode(';', Yii::app()->request->getParam('manufacturer', ''));
			$this->query->applyManufacturers($manufacturers);
		}

		// Create clone of the current query to use later to get min and max prices.
		$this->currentQuery = clone $this->query->getDbCriteria();

		// Filter products by price range if we have min_price or max_price in request
		$this->applyPricesFilter();

		$per_page = $this->allowedPageLimit[0];
                // && in_array((int)$_GET['per_page'], $this->allowedPageLimit)
		if(isset($_GET['per_page'])){
		    if(is_numeric($_GET['per_page']))
			    $per_page = (int) $_GET['per_page'];
		    elseif ($_GET['per_page'] === 'all')
                $per_page = 10000;
        }
		$this->provider = new CActiveDataProvider($this->query, array(
			// Set id to false to not display model name in
			// sort and page params
			'id'=>false,
			'pagination'=>array(
				'pageSize'=>$per_page,
			)
		));

		$this->provider->sort = StoreProduct::getCSort($sort_prefix);
        $this->setRels($this->provider->totalItemCount, $per_page);

		$this->render($view, array(
			'provider'=>$this->provider,
			'itemView'=>(isset($_GET['view']) && $_GET['view']==='wide') ? '_product_wide' : '_product',
            'city_seo' => $this->getCitySeo(),
            'displayDescription' => $this->displayCategoryDescription(),
            'pageMetaSeo' => implode(' ', $this->pageMetaSeo),
		));
	}

    /**
     * Отображать или нет описание Категории
     * Описание отображается только на начальной странице Категории
     * На последующих страницах, где в URL присутствует элемент page - описание не отображается
     * + формируются мета-данные для страницы $this->pageMetaSeo (например - Страница 2), соответственно языковой версии
     * @return bool
     */
    public function displayCategoryDescription()
    {
        $uri = explode('/', trim(Yii::app()->request->requestUri, '/'));
        if(!empty($uri) && in_array('page', $uri)){
            // мета-данные
            $this->pageMetaSeo['divider'] = ' -';
            $this->pageMetaSeo['num'] = (int)end($uri);
            $this->pageMetaSeo['title'] = Yii::t('main', 'Page');

            return false;
        }
        return true;
	}

    /**
     * Формируем (или нет) rel-элементы prev и next для навигации (SEO)
     * @param int $total - общее кол-во элементов
     * @param int $perPage - кол-во элементов на странице
     */
    public function setRels($total, $perPage)
    {
        $uri = explode('/', trim(Yii::app()->request->requestUri, '/'));
        if(!empty($uri)){
            $page = (in_array('page', $uri)) ? (int)end($uri) : 0;
            // если кол-во записей больше, чем кол-во элементов на странице
            // формируем rel-ссылки для SEO
            if($total > $perPage){
                $totalPages = ceil($total / $perPage); // общее кол-во страниц
                $newUri = (in_array('page', $uri))
                    ? '/' . implode('/', array_slice($uri, 0, -2))
                    : '/' . implode('/', $uri); // URL для rel-ссылок вида /flowers/roses
                // если общее кол-во страниц больше, чем номер текущей страницы - формируем next
                if($totalPages > $page){
                    $next = 'https://' . $_SERVER['HTTP_HOST'] . $newUri . '/page/' . (($page == 0) ? ($page + 2) : ($page + 1));
                    $this->rels['next'] = '<link rel="next" href="' . $next . '">';
                }
                // если номер текущей страницы больше 1 - формируем prev
                if($page > 1){
                    // ссылка на первую страницу - без указания /page/1
                    $prev = 'https://' . $_SERVER['HTTP_HOST'] . $newUri . (($page > 2) ? '/page/' . ($page - 1) : '');
                    $this->rels['prev'] = '<link rel="prev" href="' . $prev . '">';
                }
            }
        }
	}

	/**
	 * @return array of attributes used in http query and available in category
	 */
	public function getActiveAttributes()
	{
		$data = array();

		foreach(array_keys($_GET) as $key)
		{
			if(array_key_exists($key, $this->eavAttributes))
			{
				if((boolean) $this->eavAttributes[$key]->select_many === true)
					$data[$key] = explode(';', $_GET[$key]);
				else
					$data[$key] = array($_GET[$key]);
			}
		}

		return $data;
	}

	/**
	 * @return array of available attributes in category
	 */
	public function getEavAttributes()
	{
		if(is_array($this->_eavAttributes))
			return $this->_eavAttributes;

		// Find category types
		$model = new StoreProduct(null);
		$criteria = $model
			->active()
			->applyCategories($this->model)
			->getDbCriteria();

		unset($model);

		$builder = new CDbCommandBuilder(Yii::app()->db->getSchema());

		$criteria->select    = 'type_id';
		$criteria->group     = 'type_id';
		$criteria->distinct  = true;
		$typesUsed = $builder->createFindCommand(StoreProduct::model()->tableName(), $criteria)->queryColumn();

		// Find attributes by type
		$criteria = new CDbCriteria;
		$criteria->addInCondition('types.type_id', $typesUsed);
		$query = StoreAttribute::model()
			->useInFilter()
			->with(array('types', 'options'))
			->findAll($criteria);

		$this->_eavAttributes = array();
		foreach($query as $attr)
			$this->_eavAttributes[$attr->name] = $attr;
		return $this->_eavAttributes;
	}

	/**
	 * @return string min price
	 */
	public function getMinPrice()
	{
		if($this->_minPrice!==null)
			return $this->_minPrice;
		$this->_minPrice=$this->aggregatePrice();
		return $this->_minPrice;
	}

	/**
	 * @return string max price
	 */
	public function getMaxPrice()
	{

		$this->_maxPrice=$this->aggregatePrice('MAX');
		return $this->_maxPrice;
	}

	/**
	 * @param string $function
	 * @return mixed
	 */
	public function aggregatePrice($function = 'MIN')
	{
		$current_query = clone $this->currentQuery;
		$current_query->select =  $function.'(t.price) as aggregation_price';
		$current_query->limit = 1;

		if($function==='MIN')
			$current_query->order = 't.price';
		else
			$current_query->order = 't.price DESC';

		$query = StoreProduct::model();
		$query->getDbCriteria()->mergeWith($current_query);
		$query = $query->find();
		if($query)
		{
			return $query->aggregation_price;
		}
		return null;
	}

	public function applyPricesFilter()
	{
		$minPrice=Yii::app()->request->getQuery('min_price');
		$maxPrice=Yii::app()->request->getQuery('max_price');

		$cm=Yii::app()->currency;
		if($cm->active->id!==$cm->main->id && ($minPrice>0||$maxPrice>0))
		{
			$minPrice=$cm->activeToMain($minPrice);
			$maxPrice=$cm->activeToMain($maxPrice);
		}

		if($minPrice>0)
			$this->query->applyMinPrice($minPrice);
		if($maxPrice>0)
			$this->query->applyMaxPrice($maxPrice);
	}

	/**
	 * Load category by url
	 * @param $url
	 * @return StoreCategory
	 * @throws CHttpException
	 */
	public function _loadModel($url)
	{
		// Find category
		$page = isset($GET['page']) ? $GET['page'] : '';
		$model = StoreCategory::model()
			->excludeRoot()
			->withFullPath($url)
			->find()
			->productInPage($page);

		if (!$model) throw new CHttpException(404, Yii::t('PagesModule.core', 'Category not found.'));

		return $model;
	}
}
