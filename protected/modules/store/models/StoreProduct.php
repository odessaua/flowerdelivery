<?php

Yii::import('application.modules.store.StoreModule');
Yii::import('application.modules.store.models.StoreProductTranslate');
Yii::import('application.modules.store.models.StoreProductType');
Yii::import('application.modules.store.models.StoreProductCategoryRef');
Yii::import('application.modules.store.models.StoreProductImage');
Yii::import('application.modules.store.models.components.StoreProductImageSaver');
/**
 * This is the model class for table "StoreProduct".
 *
 * The followings are the available columns in table 'StoreProduct':
 * @property integer $id
 * @property integer $manufacturer_id
 * @property boolean $use_configurations
 * @property array $configurations array of product pks
 * @property array $configurable_attributes array of StoreAttribute pks used to configure product
 * @property integer $type_id
 * @property string $name
 * @property string $url
 * @property float $price Product price. For configurable product its min_price
 * @property float $max_price for configurable products. Used in StoreProduct::priceRange to display prices on category view
 * @property float $old_price  «старая» цена товара – только для отображения на сайте
 * @property boolean $is_active
 * @property string $short_description
 * @property string $full_description
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $layout
 * @property string $view
 * @property string $sku
 * @property string $quantity
 * @property string $auto_decrease_quantity
 * @property string $availability
 * @property integer $views_count
 * @property integer $added_to_cart_count
 * @property string $created
 * @property string $updated
 * @property integer $votes // Star rating votes
 * @property integer $rating
 * @property string $discount
 * @property integer $main_page
 * @property string $img_alt
 * @property string $img_title
 * @property integer $sale_id
 * @method StoreProduct active() Find Only active products
 * @method StoreProduct newest() Order products by creating date
 * @method StoreProduct byViews() Order by views count
 * @method StoreProduct byAddedToCart() Order by views count
 * @method StoreProduct withEavAttributes
 */
class StoreProduct extends BaseModel
{

	/**
	 * @var null Id if product to exclude from search
	 */
	public $exclude = null;

	/**
	 * @var array of related products
	 */
	private $_related;

	/**
	 * @var array of attributes used to configure product
	 */
	private $_configurable_attributes;
	private $_configurable_attribute_changed = false;

	/**
	 * @var array
	 */
	private $_configurations;

	/**
	 * @var string
	 */
	public $translateModelName = 'StoreProductTranslate';

	/**
	 * Multilingual attrs
	 */
	public $name;
	public $short_description;
	public $full_description;
	public $meta_title;
	public $meta_description;
	public $meta_keywords;
	public $cities;

	/**
	 * @var float min/max price
	 */
	public $aggregation_price;

	/**
	 * @var integer used only to render admin form
	 */
	public $main_category_id;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className
	 * @return StoreProduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'StoreProduct';
	}

	public function scopes()
	{
		$alias = $this->getTableAlias(true);
		return array(
			'active'=>array(
				'condition'=>$alias.'.is_active=1',
			),
			'newest'=>array('order'=>$alias.'.created DESC'),
			'byViews'=>array('order'=>$alias.'.views_count DESC'),
			'byAddedToCart'=>array('order'=>$alias.'.added_to_cart_count DESC'),
            'mainPage'=>array(
                'condition'=>$alias.'.main_page=1',
            ),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('price, old_price', 'commaToDot'),
			array('price, old_price, type_id, manufacturer_id, main_category_id, sale_id', 'numerical'),
			array('is_active', 'boolean'),
			array('use_configurations', 'boolean', 'on'=>'insert'),
			array('quantity, availability, manufacturer_id, long_delivery,sort, main_page', 'numerical', 'integerOnly'=>true),
			array('name, price, main_category_id', 'required'),
			array('url', 'LocalUrlValidator'),
			array('name, url, meta_title, meta_keywords, meta_description, layout, view, sku, auto_decrease_quantity, img_alt, img_title', 'length', 'max'=>255),
			array('short_description, full_description, discount', 'type', 'type'=>'string'),
			// Search
			array('id, name, url, price, short_description, full_description, created, updated, manufacturer_id, main_page', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'images'          => array(self::HAS_MANY, 'StoreProductImage', 'product_id'),
			'mainImage'       => array(self::HAS_ONE, 'StoreProductImage', 'product_id', 'condition'=>'is_main=1'),
			'imagesNoMain'    => array(self::HAS_MANY, 'StoreProductImage', 'product_id', 'condition'=>'is_main=0'),
			'manufacturer'    => array(self::BELONGS_TO, 'StoreManufacturer', 'manufacturer_id', 'scopes'=>'applyTranslateCriteria'),
			//'productsCount'   => array(self::STAT, 'StoreProduct', 'manufacturer_id', 'select'=>'count(t.id)'),
			'type'            => array(self::BELONGS_TO, 'StoreProductType', 'type_id'),
			'related'         => array(self::HAS_MANY, 'StoreRelatedProduct', 'product_id'),
			'relatedProducts' => array(self::HAS_MANY, 'StoreProduct', array('related_id'=>'id'), 'through'=>'related'),
			'relatedProductCount' => array(self::STAT, 'StoreRelatedProduct', 'product_id'),
			'categorization'  => array(self::HAS_MANY, 'StoreProductCategoryRef', 'product'),
			'categories'      => array(self::HAS_MANY, 'StoreCategory',array('category'=>'id'), 'through'=>'categorization'),
			'mainCategory'    => array(self::HAS_ONE, 'StoreCategory', array('category'=>'id'), 'through'=>'categorization', 'condition'=>'categorization.is_main = 1','scopes'=>'applyTranslateCriteria'),
			'translate'       => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
			// Product variation
			'variants'        => array(self::HAS_MANY, 'StoreProductVariant', array('product_id'), 'with'=>array('attribute', 'option'), 'order'=>'option.position'),
		);
	}

	/**
	 * Find product by url.
	 * Scope.
	 * @param string StoreProduct url
	 * @return StoreProduct
	 */
	public function withUrl($url)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>'url=:url',
			'params'=>array(':url'=>$url)
		));
		return $this;
	}

	/**
	 * Filter products by category
	 * Scope
	 * @param StoreCategory|string|array $categories to search products
	 * @return StoreProduct
	 */
	public function applyCategories($categories, $select = 't.*')
	{
		if($categories instanceof StoreCategory)
			$categories = array($categories->id);
		else
		{
			if(!is_array($categories))
				$categories = array($categories);
		}

		$criteria = new CDbCriteria;

		if($select)
			$criteria->select = $select;
		$criteria->join = 'LEFT JOIN `StoreProductCategoryRef` `categorization` ON (`categorization`.`product`=`t`.`id`)';
		$criteria->addInCondition('categorization.category', $categories);
		$this->getDbCriteria()->mergeWith($criteria);

		return $this;
	}

	/**
	 * Filter products by EAV attributes.
	 * Example: $model->applyAttributes(array('color'=>'green'))->findAll();
	 * Scope
	 * @param array $attributes list of allowed attribute models
	 * @return StoreProduct
	 */
	public function applyAttributes(array $attributes)
	{
		if(empty($attributes))
			return $this;
		return $this->withEavAttributes($attributes);
	}

	/**
	 * Filter product by manufacturers
	 * Scope
	 * @param string|array $manufacturers
	 * @return StoreProduct
	 */
	public function applyManufacturers($manufacturers)
	{
		if(!is_array($manufacturers))
			$manufacturers = array($manufacturers);

		if(empty($manufacturers))
			return $this;

		$criteria = new CDbCriteria;
		$criteria->addInCondition('manufacturer_id', $manufacturers);
		$this->getDbCriteria()->mergeWith($criteria);
		return $this;
	}

	/**
	 * Filter products by min_price
	 * @param $value
	 */
	public function applyMinPrice($value)
	{
		if($value)
		{
			$criteria = new CDbCriteria;
			$criteria->addCondition('t.price >= '.(int)$value);
			$this->getDbCriteria()->mergeWith($criteria);
		}
		return $this;
	}


	/**
	 * Filter products by man_price
	 * @param $value
	 */
	public function applyMaxPrice($value)
	{
		if($value)
		{
			$criteria = new CDbCriteria;
			$criteria->addCondition('t.price <= '.(int)$value);
			$this->getDbCriteria()->mergeWith($criteria);
		}
		return $this;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'                     => 'ID',
			'manufacturer_id'        => Yii::t('StoreModule.core', 'Производитель'),
			'type_id'                => Yii::t('StoreModule.core', 'Тип'),
			'sort'					 => Yii::t('StoreModule.core', 'Порядок отображения'),
			'use_configurations'     => Yii::t('StoreModule.core', 'Использовать конфигурации'),
			'name'                   => Yii::t('StoreModule.core', 'Название'),
			'url'                    => Yii::t('StoreModule.core', 'URL'),
			'price'                  => Yii::t('StoreModule.core', 'Цена'),
			'old_price'              => Yii::t('StoreModule.core', 'Старая цена'),
			'is_active'              => Yii::t('StoreModule.core', 'Активен'),
			'short_description'      => Yii::t('StoreModule.core', 'Состав и размер'),
			'full_description'       => Yii::t('StoreModule.core', 'Полное описание'),
			'meta_title'             => Yii::t('StoreModule.core', 'Meta Title'),
			'meta_keywords'          => Yii::t('StoreModule.core', 'Meta Keywords'),
			'meta_description'       => Yii::t('StoreModule.core', 'Meta Description'),
			'layout'                 => Yii::t('StoreModule.core', 'Макет'),
			'view'                   => Yii::t('StoreModule.core', 'Шаблон'),
			'sku'                    => Yii::t('StoreModule.core', 'Артикул')   ,
			'quantity'               => Yii::t('StoreModule.core', 'Количество'),
			'availability'           => Yii::t('StoreModule.core', 'Доступность'),
			'auto_decrease_quantity' => Yii::t('StoreModule.core', 'Автоматически уменьшать количество'),
			'created'                => Yii::t('StoreModule.core', 'Дата создания'),
			'updated'                => Yii::t('StoreModule.core', 'Дата обновления'),
			'discount'               => Yii::t('StoreModule.core', 'Скидка'),
			'main_category_id'       => Yii::t('StoreModule.core', 'Категория'),
			'cities'				 => 'Регион Доставки',
			'long_delivery'			 => 'Доставка товара 2 дня',
			'main_page'			     => 'Витрина',
            'img_alt'                => 'Alt для фото товара',
            'img_title'              => 'Title для фото товара',
            'sale_id'                => 'Акционный товар'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @param $params
	 * @param $additionalCriteria
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search($params = array(), $additionalCriteria = null)
	{
		$criteria=new CDbCriteria;

		$criteria->with = array(
			'categorization',
			'translate',
			'type',
		);
		
		if($additionalCriteria !== null)
			$criteria->mergeWith($additionalCriteria);

		if($this->manufacturer_id)
		{
			$manufacturerCr = new CDbCriteria;
			$manufacturerCr->with=array('manufacturer');
			$criteria->mergeWith($manufacturerCr);
		}

		$ids=$this->id;
		// Adds ability to accepts id as "1,2,3" string
		if(false !== strpos($ids, ','))
		{
			$ids = explode(',', $this->id);
			$ids = array_map('trim', $ids);
		}

		$criteria->compare('t.id', $ids);
		$criteria->compare('translate.name',$this->name,true);
		$criteria->compare('t.url',$this->url,true);
		$criteria->compare('t.price',$this->price);
		$criteria->compare('t.is_active',$this->is_active);
		$criteria->compare('translate.short_description',$this->short_description,true);
		$criteria->compare('translate.full_description',$this->full_description,true);
		$criteria->compare('t.sku',$this->sku,true);
		$criteria->compare('t.created',$this->created,true);
		$criteria->compare('t.updated',$this->updated,true);
		$criteria->compare('type_id', $this->type_id);
		$criteria->compare('manufacturer_id', $this->manufacturer_id);
        $criteria->compare('main_page', $this->main_page);

		if (isset($params['category']) && $params['category'])
		{
			$criteria->with=array('categorization'=>array('together'=>true));
			$criteria->compare('categorization.category', $params['category']);
		}

		// Id of product to exclude from search
		if($this->exclude)
			$criteria->compare('t.id !', array(':id'=>$this->exclude));

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
			// 'sort'       => StoreProduct::getCSort(),
		));
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'eavAttr' => array(
				'class'     => 'ext.behaviors.eav.EEavBehavior',
				'tableName' => 'StoreProductAttributeEAV',
			),
			'comments' => array(
				'class'       => 'application.modules.comments.components.CommentBehavior',
				'class_name'  => 'application.modules.store.models.StoreProduct',
				'owner_title' => 'name', // Attribute name to present comment owner in admin panel
			),
			'STranslateBehavior'=>array(
				'class'=>'ext.behaviors.STranslateBehavior',
				'translateAttributes'=>array(
					'name',
					'short_description',
					'full_description',
					'meta_title',
					'meta_description',
					'meta_keywords',
				),
			),
			'discounts'=>array(
				'class'=>'application.modules.discounts.components.DiscountBehavior'
			)
		);
	}

	/**
	 * Save related products. Notice, related product will be saved after save() method called.
	 * @param array $ids Array of related products
	 */
	public function setRelatedProducts($ids = array())
	{
		$this->_related = $ids;
	}

	public function beforeSave()
	{
		if (empty($this->url))
		{
			// Create slug
			Yii::import('ext.SlugHelper.SlugHelper');
			$this->url = SlugHelper::run(preg_replace('/\s{2,}/', ' ', $this->name));
		}

		// Check if url available
		if($this->isNewRecord)
		{
			$test = StoreProduct::model()
				->withUrl($this->url)
				->count();
			$this->created = date('Y-m-d H:i:s');
		}
		else
		{
			$test = StoreProduct::model()
				->withUrl($this->url)
				->count('t.id!=:id', array(':id'=>$this->id));
		}

		// Create unique url
		if ($test > 0)
			$this->url .= '-'.date('YmdHis');

		return parent::beforeSave();
	}

	public function beforeValidate()
	{
		// For configurable product set 0 price
		if($this->use_configurations)
			$this->price = 0;

		return parent::beforeValidate();
	}

	public function afterSave()
	{
		// Process related products
		if($this->_related !== null)
		{
			$this->clearRelatedProducts();

			foreach($this->_related as $id)
			{
				$related = new StoreRelatedProduct;
				$related->product_id = $this->id;
				$related->related_id = $id;
				$related->save();
			}
		}

		// Save configurable attributes
		if($this->_configurable_attribute_changed === true)
		{
			// Clear
			Yii::app()->db->createCommand()->delete('StoreProductConfigurableAttributes', 'product_id = :id', array(':id'=>$this->id));

			foreach($this->_configurable_attributes as $attr_id)
			{
				Yii::app()->db->createCommand()->insert('StoreProductConfigurableAttributes', array(
					'product_id'   => $this->id,
					'attribute_id' => $attr_id
				));
			}
		}

		// Process min and max price for configurable product
		if($this->use_configurations)
			$this->updatePrices($this);
		else
		{
			// Check if product is configuration
			$query = Yii::app()->db->createCommand()
				->from('StoreProductConfigurations t')
				->where(array('in', 't.configurable_id', array($this->id)))
				->queryAll();

			foreach ($query as $row)
			{
				$model = StoreProduct::model()->findByPk($row['product_id']);
				if($model)
					$this->updatePrices($model);
			}
		}

		$this->updated = date('Y-m-d H:i:s');

        // xupload
        $this->addImages();

		return parent::afterSave();
	}

	/**
	 * Update price and max_price for configurable product
	 */
	public function updatePrices(StoreProduct $model)
	{
		// Get min and max prices
		$query = Yii::app()->db->createCommand()
			->select('MIN(t.price) as min_price, MAX(t.price) as max_price')
			->from('StoreProduct t')
			->where(array('in', 't.id', $model->getConfigurations(true)))
			->queryRow();

		// Update
		Yii::app()->db->createCommand()
			->update('StoreProduct', array(
			'price'     => $query['min_price'],
			'max_price' => $query['max_price']
		), 'id=:id', array(':id'=>$model->id));
	}

	/**
	 * Delete related data.
	 */
	public function afterDelete()
	{
		// Delete related products
		$this->clearRelatedProducts();
		StoreRelatedProduct::model()->deleteAll('related_id=:id', array('id'=>$this->id));

		// Delete categorization
		StoreProductCategoryRef::model()->deleteAllByAttributes(array(
			'product'=>$this->id
		));

		// Delete images
		$images = $this->images;
		if(!empty($images))
		{
			foreach ($images as $image)
				$image->delete();
		}

		// Delete variants
		$variants = StoreProductVariant::model()->findAllByAttributes(array('product_id'=>$this->id));
		foreach ($variants as $v)
			$v->delete();

		// Clear configurable attributes
		Yii::app()->db->createCommand()->delete('StoreProductConfigurableAttributes', 'product_id=:id', array(':id'=>$this->id));

		// Delete configurations
		Yii::app()->db->createCommand()->delete('StoreProductConfigurations', 'product_id=:id', array(':id'=>$this->id));
		Yii::app()->db->createCommand()->delete('StoreProductConfigurations', 'configurable_id=:id', array(':id'=>$this->id));

		// Delete from wish lists
		Yii::import('application.modules.store.models.wishlist.StoreWishlistProducts');
		$wishlistProduct=StoreWishlistProducts::model()->findByAttributes(array('product_id'=>$this->id));
		if($wishlistProduct)
			$wishlistProduct->delete();

		return parent::afterDelete();
	}

	/**
	 * Clear all related products
	 */
	private function clearRelatedProducts()
	{
		StoreRelatedProduct::model()->deleteAll('product_id=:id', array('id'=>$this->id));
	}

	/**
	 * @return array
	 */
	public static function getAvailabilityItems()
	{
		return array(
			1=>Yii::t('StoreModule.core', 'Есть на складе'),
			2=>Yii::t('StoreModule.core', 'Нет на складе'),
		);
	}

	/**
	 * Set product categories and main category
	 * @param array $categories ids.
	 * @param integer $main_category Main category id.
	 */
	public function setCategories(array $categories, $main_category)
	{
		$dontDelete = array();

		if(!StoreCategory::model()->countByAttributes(array('id'=>$main_category)))
			$main_category = 1;

		if(!in_array($main_category, $categories))
			array_push($categories, $main_category);

		foreach($categories as $c)
		{
			$count = StoreProductCategoryRef::model()->countByAttributes(array(
				'category'=>$c,
				'product'=>$this->id
			));

			if($count == 0)
			{
				$record = new StoreProductCategoryRef;
				$record->category = (int)$c;
				$record->product = $this->id;
				$record->save(false);
			}

			$dontDelete[] = $c;
		}

		// Clear main category
		StoreProductCategoryRef::model()->updateAll(array(
			'is_main'=>0
		), 'product=:p', array(':p'=>$this->id));

		// Set main category
		StoreProductCategoryRef::model()->updateAll(array(
			'is_main'=>1
		), 'product=:p AND category=:c ', array(':p'=>$this->id,':c'=>$main_category));

		// Delete not used relations
		if(sizeof($dontDelete) > 0)
		{
			$cr = new CDbCriteria;
			$cr->addNotInCondition('category', $dontDelete);

			StoreProductCategoryRef::model()->deleteAllByAttributes(array(
				'product'=>$this->id,
			), $cr);
		}
		else
		{
			// Delete all relations
			StoreProductCategoryRef::model()->deleteAllByAttributes(array(
				'product'=>$this->id,
			));
		}
	}

	/**
	 * Prepare variations
	 * @return array product variations
	 */
	public function processVariants()
	{
		$result = array();
		foreach($this->variants as $v)
		{
			$result[$v->attribute->id]['attribute'] = $v->attribute;
			$result[$v->attribute->id]['options'][] = $v;
		};
		return $result;
	}

	/**
	 * @param $ids array of StoreAttribute pks
	 */
	public function setConfigurable_attributes(array $ids)
	{
		$this->_configurable_attributes = $ids;
		$this->_configurable_attribute_changed = true;
	}

	/**
	 * @return array
	 */
	public function getConfigurable_attributes()
	{
		if($this->_configurable_attribute_changed === true)
			return $this->_configurable_attributes;

		if($this->_configurable_attributes === null)
		{
			$this->_configurable_attributes = Yii::app()->db->createCommand()
				->select('t.attribute_id')
				->from('StoreProductConfigurableAttributes t')
				->where('t.product_id=:id', array(':id'=>$this->id))
				->group('t.attribute_id')
				->queryColumn();
		}

		return $this->_configurable_attributes;
	}

	/**
	 * @return array of product ids
	 */
	public function getConfigurations($reload = false)
	{
		if(is_array($this->_configurations) && $reload === false)
			return $this->_configurations;

		$this->_configurations = Yii::app()->db->createCommand()
			->select('t.configurable_id')
			->from('StoreProductConfigurations t')
			->where('product_id=:id', array(':id'=>$this->id))
			->group('t.configurable_id')
			->queryColumn();

		return $this->_configurations;
	}

	/**
	 * Calculate product price by its variants, configuration and self price
	 *
	 * @static
	 * @param $product
	 * @param array $variants
	 * @param $configuration
	 * @return integer
	 */
	public static function calculatePrices($product, array $variants, $configuration)
	{
		if(($product instanceof StoreProduct) === false)
			$product = StoreProduct::model()->findByPk($product);

		if(($configuration instanceof StoreProduct) === false && $configuration > 0)
			$configuration = StoreProduct::model()->findByPk($configuration);

		if($configuration instanceof StoreProduct)
			$result = $configuration->price;
		else
			$result = $product->price;

		// if $variants contains not models
		if(!empty($variants) && ($variants[0] instanceof StoreProductVariant) === false)
			$variants = StoreProductVariant::model()->findAllByPk($variants);

		foreach ($variants as $variant)
		{
			// Price is percent
			// if($variant->price_type == 1)
			// 	$result += ($result / 100 * $variant->price);
			// else
				$result = $variant->price;
		}

		return $result;
	}

	/**
	 * Apply price format
	 * @static
	 * @param $price             - цена
	 * @param $with_currency     - с форматированием по шаблону и указанием валюты
	 * @return string formatted price
	 */
	public static function formatPrice($price, $with_currency = false)
	{
	    $price = number_format($price, 2, '.', '');
		return (!empty($with_currency)) ? Yii::app()->currency->format($price) : $price;
	}

	/**
	 * Convert to active currency and format price.
	 * Display min and max price for configurable products.
	 * Used in product listing.
     * @param $with_currency     - с форматированием по шаблону и указанием валюты
	 * @return string
	 */
	public function priceRange($with_currency = false)
	{
		$price     = Yii::app()->currency->convert($this->price);
		$max_price = Yii::app()->currency->convert($this->max_price);

		if($this->use_configurations && $max_price > 0)
			return self::formatPrice($price, $with_currency).' - '.self::formatPrice($max_price, $with_currency);

		return self::formatPrice($price, $with_currency);
	}

	/**
	 * Replaces comma to dot
	 * @param $attr
	 */
	public function commaToDot($attr)
	{
		$this->$attr = str_replace(',','.', $this->$attr);
	}

	/**
	 * Convert price to current currency
	 *
	 * @param string $attr
	 * @return mixed
	 */
	public function toCurrentCurrency($attr='price')
	{
		return Yii::app()->currency->convert($this->$attr);
	}

	/**
	 * Add new image to product.
	 * First image image will be marked as main
	 * @param CUploadedFile $image
	 */
	public function addImage(CUploadedFile $image)
	{
		$saver = new StoreProductImageSaver($this, $image);
		return $saver->getModel();
	}

	/**
     * @param string $prefix preferred sort condition, like 'categorization.order, '
	 * @return CSort to use in gridview, listview, etc...
	 */
	public static function getCSort($prefix = '')
	{
		$sort = new CSort;
        $sort->defaultOrder = $prefix . 'sort';
		$sort->attributes=array(
			'*',
			'created'=>'t.created DESC',
			'name' => array(
				'asc'   => 'translate.name',
				'desc'  => 'translate.name DESC',
			),
		);
		return $sort;
	}

	/**
	 * Method to get main image title.
	 *
	 * @return string
	 */
	public function getMainImageTitle()
	{
		if($this->mainImage)
			return $this->mainImage->title;
	}

	/**
	 * Check if product is on warehouse.
	 *
	 * @return bool
	 */
	public function getIsAvailable()
	{
		return $this->availability == 1;
	}

	/**
	 * @return string
	 */
	public function getAbsoluteUrl()
	{
		return Yii::app()->createAbsoluteUrl('/store/frontProduct/view',array('url'=>$this->url));
	}

	/**
	 * @return string
	 */
	public function getRelativeUrl()
	{
		return Yii::app()->createUrl('/store/frontProduct/view',array('url'=>$this->url));
	}

	/**
	 * Decrease product quantity when added to cart
	 */
	public function decreaseQuantity()
	{
		if($this->auto_decrease_quantity && (int) $this->quantity > 0)
		{
			$this->quantity--;
			$this->save(false);
		}
	}

	/**
	 * Allows to access EAV attributes like normal model attrs.
	 * e.g $model->eav_some_attribute_name
	 *
	 * @todo Optimize, cache.
	 * @param $name
	 * @return null
	 */
	public function __get($name)
	{
		if(substr($name,0,4) === 'eav_')
		{
			if($this->getIsNewRecord())
				return null;

			$attribute = substr($name, 4);
			$eavData = $this->getEavAttributes();

			if(isset($eavData[$attribute]))
				$value = $eavData[$attribute];
			else
				return null;

			$attributeModel = StoreAttribute::model()->findByAttributes(array('name'=>$attribute));
			return $attributeModel->renderValue($value);
		}
		return parent::__get($name);
	}

    /**
     * xupload
     * @throws Exception
     */
    public function addImages( ) {
        //If we have pending images
        if( Yii::app( )->user->hasState( 'images' ) ) {
            $userImages = Yii::app( )->user->getState( 'images' );
            //Resolve the final path for our images
            //$path = Yii::app( )->getBasePath( )."/../uploads/products/{$this->id}/";                // images path here
            $path = Yii::app( )->getBasePath( )."/../uploads/products/";                // все в одну папку
            //Create the folder and give permissions if it doesnt exists
//            if( !is_dir( $path ) ) {
//                mkdir( $path );
//                chmod( $path, 0777 );
//            }

            //Now lets create the corresponding models and move the files
            foreach( $userImages as $image ) {
                if( is_file( $image["path"] ) ) {
                    if( rename( $image["path"], $path.$image["filename"] ) ) {
                        chmod( $path.$image["filename"], 0777 );
                        $img = new Image();
                        $img->size = $image["size"];
                        $img->mime = $image["mime"];
                        $img->name = $image["name"];
//                        $img->source = "/uploads/product_images/{$this->id}/".$image["filename"];         // images path here
                        $img->source = "/uploads/products/".$image["filename"];         // все в одной папке
                        $img->source_filename = $image["filename"];
                        $img->product_id = $this->id;
                        if( !$img->save( ) ) {
                            //Its always good to log something
                            Yii::log( "Could not save Image:\n".CVarDumper::dumpAsString(
                                    $img->getErrors( ) ), CLogger::LEVEL_ERROR );
                            //this exception will rollback the transaction
                            throw new Exception( 'Could not save Image');
                        }
                    }
                } else {
                    //You can also throw an execption here to rollback the transaction
                    Yii::log( $image["path"]." is not a file", CLogger::LEVEL_WARNING );
                }
            }
            //Clear the user's session
            Yii::app( )->user->setState( 'images', null );
        }
    }

    /**
     * все активные акционные товары - для выпадающего списка (пары id => name)
     * @return array
     */
    public static function getSales()
    {
        $return = array(0 => '---');
        // получаем категорию акционных товаров - важно название категории!!!
        $sale_type = StoreProductType::model()->findByAttributes(array('name' => 'Акционные товары'));
        if(!empty($sale_type['id'])) {
            // получаем ID и name
            $sales = Yii::app()->db->createCommand()
                ->select('p.id, pt.name')
                ->from('StoreProduct p')
                ->join('StoreProductTranslate pt', 'pt.object_id = p.id AND pt.language_id = 1')
                ->where('type_id = :type_id AND is_active = 1', array(':type_id' => $sale_type['id']))
                ->order('pt.name ASC')
                ->queryAll();
            if(!empty($sales)) {
                // форматируем данные для выпадающего списка
                foreach ($sales as $sale) {
                    $return[$sale['id']] = $sale['name'];
                }
            }
        }
        return $return;
    }

    /**
     * один акционный товар с переводом и главным фото
     * @param $pk ID акционного товара
     * @return array|mixed|null
     * $sale = StoreProduct::getSale(526); $sale->mainImage - главное фото товара
     */
    public static function getSale($pk)
    {
        return self::model()->with('translate', 'mainImage')->findByPk($pk);
    }

}