<?php

Yii::import('store.models.StoreDeliveryMethod');

/**
 * Used in cart to create new order.
 */
class OrderCreateForm extends CFormModel
{
	public $name;
	public $email;
	public $phone;
	public $address;
	public $country;
	public $city;
	public $delivery_price;
	public $comment;
	public $datetime_delivery;
	public $do_card;
	public $receiver_name;
	public $receiver_city;
	public $phone2;
	public $doPhoto;
	public $card_text;
	public $phone1;
	public $card_transl;
	public $coupon;

	public function init()
	{
		if(!Yii::app()->user->isGuest)
		{
			$profile=Yii::app()->user->getModel()->profile;
			$this->name=$profile->full_name;
			$this->phone=$profile->phone;
			$this->address=$profile->delivery_address;
			$this->email=Yii::app()->user->email;
		}
		
		$this->receiver_city = "Киев";
		
		if(isset(Yii::app()->session['_cityName']))
		{
			$this->receiver_city = Yii::app()->session['_cityName'];
		}
			
	}

	/**
	 * Validation
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('name, email, country, city, receiver_name, address, phone1, phone', 'required', 'message'=>'{attribute} cannot be blank'),
			array('email', 'filter', 'filter' => 'trim'),
			array('email', 'email'),
			array('comment', 'length', 'max'=>'500'),
			array('card_text', 'length', 'max'=>'1500'),
			array('address, city, receiver_city, datetime_delivery, country', 'length', 'max'=>'255'),
			array('email', 'length', 'max'=>'100'),
			array('phone, phone1, phone2', 'length', 'max'=>'30'),
			array('doPhoto,do_card, card_transl', 'numerical', 'integerOnly'=>true),
		);
	}

	public function attributeLabels()
	{
		return array(
			'name'        	=> Yii::t('OrdersModule.core', 'Your name'),
			'email'       	=> Yii::t('OrdersModule.core', 'E-mail'),
			'comment'     	=> Yii::t('OrdersModule.core', 'Additional Information'),
			'address'     	=> Yii::t('OrdersModule.core', 'Delivery address'),
			'phone'     	=> Yii::t('OrdersModule.core', 'Phone number'),	
			'phone1'		=> Yii::t('OrdersModule.core', 'Recipient phone &#8470;1'),
			'phone2'		=> Yii::t('OrdersModule.core', 'Recipient phone &#8470;2'),
			'country'		=> Yii::t('OrdersModule.core', 'Your country'),
			'city' 			=> Yii::t('OrdersModule.core', 'Your city'),	
			'receiver_city' => Yii::t('OrdersModule.core', 'Recipient City'),
			'datetime_delivery' => Yii::t('OrdersModule.core', 'Delivery Date'),
			'receiver_name' => Yii::t('OrdersModule.core', 'Recipient name'),
		);
	}

	/**
	 * Check if delivery method exists
	 */
	public function validateDelivery()
	{
		if(StoreDeliveryMethod::model()->countByAttributes(array('id'=>$this->delivery_id)) == 0)
			$this->addError('delivery_id', Yii::t('OrdersModule.core', 'Необходимо выбрать способ доставки.'));
	}
}
