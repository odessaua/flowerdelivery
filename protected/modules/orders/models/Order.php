<?php

Yii::import('application.modules.orders.OrdersModule');

/**
 * This is the model class for table "Order".
 *
 * The followings are the available columns in table 'Order':
 * @property integer $id
 * @property integer $user_id
 * @property string $secret_key
 * @property float $delivery_price
 * @property float $total_price Sum of ordered products
 * @property float $full_price Total price + delivery price
 * @property integer $status_id
 * @property integer $paid
 * @property string $user_name
 * @property string $user_email
 * @property string $user_address
 * @property string $user_phone
 * @property string $user_comment
 * @property string $admin_comment
 * @property string $ip_address
 * @property string $created
 * @property string $updated
 * @property float $photo_price
 * @property string $discount
 * @property string $discount_price
 * @property string $payment_id
 * @property string $payment_status
 * @property string $receiver_city
 */
class Order extends BaseModel
{
    public $payment_statuses = array(
        'new' => 'новый',
        'pending' => 'неопределенный',
        'paid' => 'оплачен',
        'rejected' => 'не оплачен',
    );

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Order the static model class
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
		return 'Order';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public $image;
	public function rules()
	{
		return array(
			array('user_name, user_email, city, receiver_name, datetime_del', 'required'),
			array('user_name, user_email, discount, payment_status', 'length', 'max'=>100),
			array('user_phone', 'length', 'max'=>30),
			array('user_email', 'email'),
			array('payment_id, doPhoto,do_card, card_transl', 'numerical', 'integerOnly'=>true),
			array('user_comment, admin_comment', 'length', 'max'=>500),
			array('card_text', 'length', 'max'=>1500),
			array('user_address, city, country, receiver_name, receiver_city, phone1, phone2, datetime_del', 'length', 'max'=>255),
			array('status_id', 'validateStatus'),
			array('paid', 'boolean'),
			array('image', 'file', 'types'=>'png, jpg, jpeg, gif', 'allowEmpty' => true),
			// Search
			array('id, user_id, payment_id, payment_status, delivery_price, total_price, status_id, paid, user_name, user_email, user_address, user_phone, user_comment, receiver_city, datetime_del, ip_address, created, updated, lang, admin_id, discount_minus_promo, discount_promo, discount_minus', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array
	 */
	public function relations()
	{
		return array(
			'products'=>array(self::HAS_MANY, 'OrderProduct', 'order_id'),
			'status'=>array(self::BELONGS_TO, 'OrderStatus', 'status_id'),
		);
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		$alias = $this->getTableAlias(true);
		return array(
			'new'=>array('condition'=>$alias.'.status_id=1'),
		);
	}

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'historical' => array(
				'class' => 'application.modules.orders.behaviors.HistoricalBehavior',
			)
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'             => Yii::t('OrdersModule.core','&#8470;'),
			'user_id'        => Yii::t('OrdersModule.core','Client ID'),
			'delivery_price' => Yii::t('OrdersModule.core','Delivery price'),
			'total_price'    => Yii::t('OrdersModule.core','Total price'),
			'full_price'     => Yii::t('OrdersModule.core','Full price'),
			'status_id'      => Yii::t('OrdersModule.core','Status'),
			'paid'           => Yii::t('OrdersModule.core','Paid'),
			'user_name'      => Yii::t('OrdersModule.core','Sender Name'),
			'user_email'     => Yii::t('OrdersModule.core','Sender Email'),
			'user_address'   => Yii::t('OrdersModule.core','Delivery Adress'),
			'user_phone'     => Yii::t('OrdersModule.core','Sender Phone'),
			'user_comment'   => Yii::t('OrdersModule.core','Sender Comment'),
			'admin_comment'  => Yii::t('OrdersModule.core','Admin comment'),
			'ip_address'     => Yii::t('OrdersModule.core','IP adress'),
			'created'        => Yii::t('OrdersModule.core','Created'),
			'updated'        => Yii::t('OrdersModule.core','Updated'),
			'discount'       => Yii::t('OrdersModule.core','Discount'),
			'payment_id'     => Yii::t('OrdersModule.core','Payment type'),
			'payment_status' => Yii::t('OrdersModule.core','Payment status'),
			'receiver_name'  => Yii::t('OrdersModule.core','Receiver Name'),
			'receiver_city'  => Yii::t('OrdersModule.core','Order to'),
			'phone1'		 => Yii::t('OrdersModule.core','Receiver Phone #1'),
			'phone2'		 => Yii::t('OrdersModule.core','Receiver Phone #2'),
			'datetime_del'	 => Yii::t('OrdersModule.core','Delivery Date'),
			'card_text'		 => Yii::t('OrdersModule.core','Card text'),
			'doPhoto'		 => Yii::t('OrdersModule.core','Photo of the delivery'),
			'card_transl'	 => Yii::t('OrdersModule.core','Card translation'),
			'country'		 => Yii::t('OrdersModule.core','Sender Country'),
			'city'			 => Yii::t('OrdersModule.core','Sender City'),
			'do_card'		 => Yii::t('OrdersModule.core','Greeting Card'),
			'lang'		 => Yii::t('OrdersModule.core','Lang'),
			'admin_id'   => Yii::t('OrdersModule.core','Admin ID'),
			
		);
	}

			

	/**
	 * Check if status exists
	 */
	public function validateStatus()
	{
		if($this->status_id && OrderStatus::model()->countByAttributes(array('id'=>$this->status_id)) == 0)
			$this->addError('status_id', Yii::t('OrdersModule.core', 'Ошибка проверки статуса.'));
	}

	/**
	 * @return bool
	 */
	public function beforeSave()
	{
		if($this->isNewRecord)
		{
			$this->secret_key = $this->createSecretKey();
			$this->ip_address = Yii::app()->request->userHostAddress;
			$this->created    = date('Y-m-d H:i:s');

			if(!Yii::app()->user->isGuest)
				$this->user_id = Yii::app()->user->id;
		}
		// $this->updated = date('Y-m-d H:i:s');

		// Set `New` status
		if(!$this->status_id)
			$this->status_id = 1;

		return parent::beforeSave();
	}

	/**
	 * @return bool
	 */
	public function afterDelete()
	{
		foreach($this->products as $ordered_product)
			$ordered_product->delete();

		return parent::afterDelete();
	}

	/**
	 * Create unique key to view orders
	 * @param int $size
	 * @return string
	 */
	public function createSecretKey($size=10)
	{
		$result = '';
		$chars = '1234567890qweasdzxcrtyfghvbnuioplkjnm';
		while(mb_strlen($result,'utf8') < $size)
		{
			$result .= mb_substr($chars, rand(0, mb_strlen($chars,'utf8')), 1);
		}

		if(Order::model()->countByAttributes(array('secret_key'=>$result))>0)
			$this->createSecretKey($size);

		return $result;
	}

	/**
	 * Update total
	 */
	public function updateTotalPrice()
	{
		$this->total_price = 0;
		$products = OrderProduct::model()->findAllByAttributes(array('order_id'=>$this->id));

		foreach($products as $p)
			$this->total_price += $p->price * $p->quantity;
		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$card_transl=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
			
		if($this->doPhoto==1){$this->total_price+=$photoPrice;}
		if($this->do_card==1){$this->total_price+=$cardPrice;}
		if($this->card_transl==1){$this->total_price+=$card_transl;}
		$this->save(false);
	}

	/**
	 * @return int
	 */
	public function updateDeliveryPrice()
	{
		
	}

	/**
	 * @return mixed
	 */
	public function getStatus_name()
	{
		if($this->status)
			return $this->status->name;
	}

	/**
	 * @return mixed
	 */
	public function getDelivery_name()
	{
		return "";
	}

	/**
	 * @return mixed
	 */
	public function getFull_price()
	{
		if(!$this->isNewRecord)
		{
			$result = $this->total_price;
			$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
			$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
			$card_transl=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
			
			// if($this->doPhoto==1){$this->total_price+=$photoPrice;}
			// if($this->do_card==1){$this->total_price+=$cardPrice;}
			// var_dump($this->total_price);die;
			if($this->discount)
			{
				$sum = $this->discount;
				if('%'===substr($this->discount,-1,1))
					$sum = $this->total_price * (int)$this->discount / 100;
				$result -= $sum;
			}
			$result += $this->delivery_price;

			return $result;
		}
	}

	public function getDiscount_price()
	{
		$result = 0;
		if(!$this->isNewRecord)
		{
			if($this->discount)
			{
				$result = $this->discount;
				if('%'===substr($this->discount,-1,1))
					$result = $this->total_price * (int)$this->discount / 100;
			}
		}

		return $result;
	}

	/**
	 * Add product to existing order
	 *
	 * @param StoreProduct $product
	 * @param integer $quantity
	 * @param float $price
	 */
	public function addProduct($product, $quantity, $price,$variants)
	{
		if(!$this->isNewRecord)
		{
			$json=array(''=>$variants);
			$ordered_product = new OrderProduct;
			$ordered_product->order_id   = $this->id;
			$ordered_product->product_id = $product->id;
			$ordered_product->name       = $product->name;
			$ordered_product->quantity   = $quantity;
			$ordered_product->variants=serialize($json);
			$ordered_product->sku        = $product->sku;
			$ordered_product->price      = $price;
			$ordered_product->save();
			// Raise event
			$event = new CModelEvent($this, array(
				'product_model'   => $product,
				'ordered_product' => $ordered_product,
				'quantity'        => $quantity
			));
			$this->onProductAdded($event);
		}
	}

	/**
	 * Delete ordered product from order
	 *
	 * @param $id
	 */
	public function deleteProduct($id)
	{
		$model = OrderProduct::model()->findByPk($id);

		if($model)
		{
			$model->delete();

			$event = new CModelEvent($this, array(
				'ordered_product' => $model
			));
			$this->onProductDeleted($event);
		}
	}

	/**
	 * @param $event
	 */
	public function onProductAdded($event)
	{
		// var_dump($_POST);die;
		//$this->raiseEvent('onProductAdded', $event);
	}

	/**
	 * @param $event
	 */
	public function onProductDeleted($event)
	{
		$this->raiseEvent('onProductDeleted', $event);
	}

	/**
	 * @param $event
	 */
	public function onProductQuantityChanged($event)
	{
		$this->raiseEvent('onProductQuantityChanged', $event);
	}

	/**
	 * @return CActiveDataProvider
	 */
	public function getOrderedProducts()
	{
		$products = new OrderProduct;
		$products->order_id = $this->id;
		return $products->search();
	}

	/**
	 * @param array $data
	 */
	public function setProductQuantities(array $data)
	{
		foreach($this->products as $product)
		{
			if(isset($data[$product->id]))
			{
				if((int)$product->quantity !== (int)$data[$product->id])
				{
					$event = new CModelEvent($this, array(
						'ordered_product' => $product,
						'new_quantity'    => (int)$data[$product->id]
					));
					$this->onProductQuantityChanged($event);
				}

				$product->quantity = (int)$data[$product->id];
				$product->save();
			}
		}
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('payment_id',$this->payment_id);
		$criteria->compare('payment_status',$this->payment_status);
		$criteria->compare('receiver_city',$this->receiver_city);
		$criteria->compare('datetime_del',$this->datetime_del);
		$criteria->compare('delivery_price',$this->delivery_price);
		$criteria->compare('total_price',$this->total_price);
		$criteria->compare('status_id',$this->status_id);
		$criteria->compare('paid',$this->paid);
		$criteria->compare('user_name',$this->user_name,true);
		$criteria->compare('user_email',$this->user_email,true);
		$criteria->compare('user_address',$this->user_address,true);
		$criteria->compare('user_phone',$this->user_phone,true);
		$criteria->compare('user_comment',$this->user_comment,true);
		$criteria->compare('ip_address',$this->ip_address,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('lang',$this->lang,true);
		$criteria->compare('admin_id',$this->admin_id,true);

		$sort=new CSort;
		$sort->defaultOrder = $this->getTableAlias().'.created DESC';

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
			'sort'     => $sort
		));
	}

	/**
	 * Load history
	 *
	 * @return array
	 */
	public function getHistory()
	{
		$cr        = new CDbCriteria;
		$cr->order = 'created ASC';

		return OrderHistory::model()->findAllByAttributes(array(
			'order_id'=>$this->id,
		),$cr);
	}
}