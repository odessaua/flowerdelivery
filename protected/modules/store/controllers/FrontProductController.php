<?php
/**
 * Display product view page.
 */
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
class FrontProductController extends Controller
{

	/**
	 * @var StoreProduct
	 */
	public $model;

	/**
	 * @return array
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
			),
		);
	}

	/**
	 * Display product
	 * @param string $url product url
	 */
	public function actionPhotos()
	{
		$id=$_GET['id'];
		$model= StoreProduct::model()->findByPk($id);
		$this->pageTitle =  Yii::t('StoreModule.core', 'Delivery photos of').' '.$model->name;
		$this->pageDescription =  Yii::t('StoreModule.core', 'Photos of the real flower and gifts deliveries in Ukraine. All photos was made by our couriers.').' '.$model->name;
		$allphotos=OrderPhoto::model()->findAllByAttributes(
			array('product_id'=>$id),
			array('order'=>'order_id DESC')
			);
		
		$this->render('photos', array(
			'model' => $this->model,
			'name'=>$model->name,
			'allPhotos'=>$allphotos,
			
		));
	}
	public function actionView($url)
	{

		$this->_loadModel($url);
		$lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';

                    $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
//        $translate = StoreProductTranslate::model()->findByAttributes(array('language_id'=>$langArray->id,'object_id'=>$model->id));
    	$model=$this->model;
        $translate = StoreProductTranslate::model()->findByAttributes(array('language_id'=>$langArray->id,'object_id'=>$model->id));
    	// var_dump($model->short_description);
    	!empty($translate->name)?$model->name=$translate->name:"";
    	!empty($translate->short_description)?$model->short_description=$translate->short_description:"";
    	!empty($translate->full_description)?$model->full_description=$translate['full_description']:"";
		$view = $this->setDesign($this->model, 'view');
		$photos=OrderPhoto::model()->findAllByAttributes(
			array('product_id'=>$this->model->id),
			array(
					'order'=>'order_id DESC',
					'limit' => 10
				)
			);
		// дополнительные изображения товара
        $additional_images = Image::model()->findAllByAttributes(array('product_id' => $model->id));
        // варианты товара
        $variants = Yii::app()->db->createCommand()
            ->select('spv.id AS variant_id, spv.attribute_id, spv.option_id, spv.price AS spv_price, spv.default, sat.title AS attr_title, sat.header AS attr_header, saot.value AS opt_value')
            ->from('StoreProductVariant spv')
            ->join('StoreAttributeTranslate sat', 'sat.object_id = spv.attribute_id AND sat.language_id = ' . (int)$langArray->id)
            ->join('StoreAttributeOptionTranslate saot', 'saot.object_id = spv.option_id AND saot.language_id = ' . (int)$langArray->id)
            ->where('spv.product_id = :pid', array(':pid' => $model->id))
            ->queryAll();

		$this->render($view, array(
			'model' => $model,
			'photos' =>$photos,
			'comments' => array(
        		'class'       => 'comments.components.CommentBehavior',
        		'class_name'  => 'store.models.StoreProduct', // Alias to commentable model
        		'owner_title' => 'name', // Attribute name to present comment owner in admin panel
    		),
            'langArray' => $langArray,
            'additional_images' => $additional_images,
            'variants' => $variants,
		));
	}

	/**
	 * Load StoreProduct model by url
	 * @param $url
	 * @return StoreProduct
	 * @throws CHttpException
	 */
	protected function _loadModel($url)
	{
		$this->model = StoreProduct::model()
			->withUrl($url)
			->find();
//			->active()

		if (!$this->model)
			throw new CHttpException(404, Yii::t('StoreModule.core', 'Product not found'));

		$this->model->views_count += 1;
		$this->model->saveAttributes(array('views_count'));
		return $this->model;
	}


	/**
	 * Get data to render dropdowns for configurable product.
	 * Used on product view.
	 * array(
	 *      'attributes' // Array of StoreAttribute models used for configurations
	 *      'prices'     // Key/value array with configurations prices array(product_id=>price)
	 *      'data'       // Array to render dropdowns. array(color=>array('Green'=>'1/3/5/', 'Silver'=>'7/'))
	 * )
	 * @todo Optimize. Cache queries.
	 * @return array
	 */
	public function getConfigurableData()
	{
		$attributeModels = StoreAttribute::model()->findAllByPk($this->model->configurable_attributes);
		$models = StoreProduct::model()->findAllByPk($this->model->configurations);

		$data = array();
		$prices = array();
		foreach($attributeModels as $attr)
		{
			foreach($models as $m)
			{
				$prices[$m->id] = $m->price;
				if(!isset($data[$attr->name]))
					$data[$attr->name] = array('---'=>'0');

				$method = 'eav_'.$attr->name;
				$value = $m->$method;

				if(!isset($data[$attr->name][$value]))
					$data[$attr->name][$value] = '';

				$data[$attr->name][$value] .= $m->id.'/';
			}
		}

		return array(
			'attributes'=>$attributeModels,
			'prices'=>$prices,
			'data'=>$data,
		);
	}
}