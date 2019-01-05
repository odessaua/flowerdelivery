<?php

Yii::import('orders.models.*');
Yii::import('store.models.*');
Yii::import('discounts.models.*');
  //ini_set('display_errors', 1);
  //ini_set('display_startup_errors', 1);
  //error_reporting(E_ALL);
/**
 * Cart controller
 * Display user cart and create new orders
 */
class CartController extends Controller
{

		public $photoPrice; 
		public $cardPrice;
		public $translPrice;
	/**
	 * @var OrderCreateForm
	 */
	public $form;

	/**
	 * @var bool
	 */
	protected $_errors = false;

	/**
	 * Display list of product added to cart
	 */
	public function setDeliveryPrices(){
		
		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$translPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
	} 
	
	public function actionGetCouponDiscount()
	{
		$total_price = $_POST['price'];
		$code = $_POST['code'];
		if(isset($code)){
			Yii::app()->request->cookies['coupon'] = new CHttpCookie('coupon', $code);
		}
		$rate =Yii::app()->currency->active->rate;
		
		$discount = Promo::getPromoDiscount($total_price*$rate, $code);
		
		$data = [
			'price' => StoreProduct::formatPrice((string)$discount['result'], true),
			'percent' => $discount['percent'],
			'minus' => StoreProduct::formatPrice((string)$discount['minus'], true),
		];
		echo json_encode($data);
	}
	 
	public function actionIndex()
	{
		
		$this->setDeliveryPrices();
		$total=0;
		$this->form = new OrderCreateForm;
		$rate=Yii::app()->currency->active['rate'];
		$symbol=Yii::app()->currency->active['symbol'];
		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$translPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
		if(Yii::app()->request->isPostRequest && Yii::app()->request->getPost('recount') && !empty($_POST['quantities']))
			$this->processRecount();
			$total_price = Yii::app()->currency->convert(Yii::app()->cart->getTotalPrice());
	
			$rate = Yii::app()->currency->active->rate;

			if(Yii::app()->request->isPostRequest && Yii::app()->request->getPost('create'))
			{
				$price = $_POST['price'];
				$price = mb_substr($price,1);

				$discount = $_POST['discount'];
				$discount_minus = $_POST['discount_minus'];
				
				$discount_promo = $_POST['discount_promo'];
				$discount_minus_promo = $_POST['discount_minus_promo'];
				
				if(isset($_POST['OrderCreateForm']))
				{
					$this->form->attributes = $_POST['OrderCreateForm'];
					$city=Yii::app()->session['_city'];
					if($this->form->validate())
					{
						$order = $this->createOrder($price, $discount, $discount_minus, $discount_promo, $discount_minus_promo);
						Yii::app()->cart->clear();
						$this->addFlashMessage(Yii::t('OrdersModule.core', 'Thank you. Your order is issued. Select a Payment Method.'));
						Yii::app()->request->cookies['new_order'] = new CHttpCookie('new_order', 'true');
						Yii::app()->request->redirect($this->createUrl('view', array('secret_key'=>$order->secret_key)));
					}
					
				}
	
			
		}
		$deliveryMethods = StoreDeliveryMethod::model()
			->applyTranslateCriteria()
			->active()
			->orderByName()
			->findAll();
		// var_dump($deliveryMethods);
        $items = Yii::app()->cart->getDataWithModels();
        if(!empty($items)){
            foreach ($items as $i_key => $item) {
                $items[$i_key]['translation'] = $this->translateProductInfo($item['product_id']);
            }
        }
		
		$this->render('index', array(
			'items'           => $items,
			'delivery_price'  => Yii::app()->session['_delivery_price'],
			'totalPrice'      => Yii::app()->currency->convert(Yii::app()->cart->getTotalPrice()),
			'deliveryMethods' => $deliveryMethods,
			'photoPrice'	=> $photoPrice,
			'cardPrice'		=> $cardPrice,
			'translPrice'	=> $translPrice,
			'rate'			=> $rate,
			'symbol'		=> $symbol,
            'popular' 		=> $this->getMainPage(),
			'discount'=>$discount
		));
	}

    /**
     * @param $limit
     * @return array
     */
    protected function getMainPage($limit = 0)
    {
        return StoreProduct::model()
            ->mainPage()
            ->active()
            ->findAll(array('limit'=>$limit));
    }

	/**
	 * Find order by secret_key and display.
	 * @throws CHttpException
	 */
	public function actionView()
	{
		$secret_key = Yii::app()->request->getParam('secret_key');
		$code = Yii::app()->request->getParam('code');
		$model = Order::model()->find('secret_key=:secret_key', array(':secret_key'=>$secret_key));

		$deliveryPrice=Yii::app()->db->createCommand()
							     ->select("c.delivery")
							     ->from("city c")
							     ->join('cityTranslate ct','ct.object_id=c.id')
							     ->where('ct.name=:name', array(':name'=>Yii::app()->session['_city']))
							     // ->where('ct.name=:name',':name'=>)_
							     ->queryRow();
		/* if (empty($deliveryPrice)) $deliveryPrice['delivery'] = 10; // стоимость доставки по умолчанию
		if(!empty($model->doPhoto)){$model->photo_price=$photoPrice;}
		if(!empty($model->do_card)){$model->card_price=$cardPrice;}
		$model->delivery_price=$deliveryPrice['delivery'];
		$model->update(); */
		
		$rate=Yii::app()->db->createCommand()
							     ->select("rate")
							     ->from("StoreCurrency")
							     ->where('id=2')
							     ->queryRow()['rate'];
		$symbol=Yii::app()->currency->active['symbol'];

		if($model->doPhoto == 1){
			$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		}
		if($model->do_card == 1){
			$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		}
		if($model->card_transl == 1){
			$translPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
		}
		
		$discount = $model->discount;
		$discount_minus = $model->discount_minus;
		$discount_promo = $model->discount_promo;
		$discount_minus_promo = $model->discount_minus_promo;
		
		$price = $model->total_price + $translPrice + $cardPrice + $photoPrice + $model->delivery_price;
		
		$rates =Yii::app()->currency->active->rate;

		if(!$model)
			throw new CHttpException(404, Yii::t('OrdersModule.core', 'Error. Order not found'));

		if(Yii::app()->request->cookies['new_order'] == true){
			$model->total_price = $model->total_price + $translPrice + $cardPrice + $photoPrice + $model->delivery_price;
			$model->update();
			Yii::app()->request->cookies->clear();
		}else{
			$model->update();
		}
		
		$this->render('view', array(
			'model'=>$model,
			'rate'=>$rate,
			'symbol'=>$symbol,
			'price'=>$price,
			'discount'=>$discount,
			'discount_minus'=>$discount_minus,
			'discount_promo'=>$discount_promo,
			'discount_minus_promo'=>$discount_minus_promo
		));
	}
	public function actionSuccess(){
		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$translPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];

		$secret_key = Yii::app()->request->getParam('secret_key');
		$model = Order::model()->find('secret_key=:secret_key', array(':secret_key'=>$secret_key));
		$deliveryPrice=Yii::app()->db->createCommand()
							     ->select("c.delivery")
							     ->from("city c")
							     ->join('cityTranslate ct','ct.object_id=c.id')
							     ->queryRow();
//		$model->status_id=6;
//		$model->update();
		$rate=Yii::app()->db->createCommand()
							     ->select("rate")
							     ->from("StoreCurrency")
							     ->where('id=2')
							     ->queryRow()['rate'];
		$symbol=Yii::app()->currency->active['symbol'];
		if(!$model)
			throw new CHttpException(404, Yii::t('OrdersModule.core', 'Error. Order not found'));
		
			$this->render('success', array(
				'model'=>$model,
				'photoPrice'=>$photoPrice,
				'cardPrice'=>$cardPrice,
				'translPrice'=>$translPrice,
				'rate'=>$rate,
				'symbol'=>$symbol
			));
	}
	/**
	 * Validate POST data and add product to cart
	 */
	
	public function actionAdd()
	{
		$variants = array();

		// Load product model
		$model = StoreProduct::model()
			->active()
			->findByPk(Yii::app()->request->getPost('product_id', 0));

		// Check product
		if(!isset($model))
			$this->_addError(Yii::t('OrdersModule.core', 'Error. Product not found'), true);
		
		//if product isset in Region
        $cityInfo = $this->getCurrentCityInfo(true);
        $city = $cityInfo->id;
        $cityName = $cityInfo->name;

        // временно отключили проверку товара по регионам доставки
		//Yii::import('application.modules.store.models.StoreProductCityRef');
		//$productInCity = StoreProductCityRef::model()->find(array('condition'=>'product_id='.$model->id.' AND city_id='.$city));
		//if(!$productInCity)
		//	$this->_addError(Yii::t('OrdersModule.core', 'Product not available for your region'.$cityName), true);
		
		// Update counter
		$model->added_to_cart_count += 1;
		$model->saveAttributes(array('added_to_cart_count'));

		// Process variants
		if(!empty($_POST['eav']))
		{
			foreach($_POST['eav'] as $attribute_id=>$variant_id)
			{
				if(!empty($variant_id))
				{
					// Check if attribute/option exists
					if(!$this->_checkVariantExists($_POST['product_id'], $attribute_id, $variant_id))
						$this->_addError(Yii::t('OrdersModule.core', 'Error. Product variant not found'));
					else
						array_push($variants, $variant_id);
				}
			}
		}

		// Process configurable products
		if($model->use_configurations)
		{
			// Get last configurable item
			$configurable_id = Yii::app()->request->getPost('configurable_id', 0);

			if(!$configurable_id || !in_array($configurable_id , $model->configurations))
				$this->_addError(Yii::t('OrdersModule.core', 'Error. Choose product variant'), true);
		}else
			$configurable_id  = 0;
		// var_dump($variants);isset($variants)?die:"";
		Yii::app()->cart->add(array(
			'product_id'      => $model->id,
			'variants'        => $variants,
			'configurable_id' => $configurable_id,
			'quantity'        => (int) Yii::app()->request->getPost('quantity', 1),
			'price'           => $model->price,
            'sale_id'         => (!empty($model->sale_id)) ? $model->sale_id : 0,
            'is_sale'         => 0,
		));

		// акционный товар
        if(!empty($model->sale_id)){
            $sale_product = StoreProduct::model()
                ->active()
                ->findByPk($model->sale_id);
            if(!empty($sale_product)) {
                // Update counter
                $sale_product->added_to_cart_count += 1;
                $sale_product->saveAttributes(array('added_to_cart_count'));
                Yii::app()->cart->add(array(
                    'product_id'      => $sale_product->id,
                    'variants'        => array(),
                    'configurable_id' => 0,
                    'quantity'        => 1,
                    'price'           => $sale_product->price,
                    'sale_id'         => 0,
                    'is_sale'         => 1,
                ));
            }
        }

		$this->_finish();
	}
	
	/**
	 * Remove product from cart and redirect
	 */
	public function actionRemove($index)
	{
	    // удаляем акционный продукт – если он идёт с данным товаром
	    $cart_data = Yii::app()->cart->getData();
	    if(!empty($cart_data[$index]['sale_id'])){
	        foreach ($cart_data as $idx => $item){
	            if($item['product_id'] == $cart_data[$index]['sale_id']){
                    Yii::app()->cart->remove($idx);
	                break;
                }
            }
        }

		Yii::app()->cart->remove($index);

		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->request->redirect($this->createUrl('index'));
	}

	/**
	 * Clear cart
	 */
	public function actionClear()
	{
		Yii::app()->cart->clear();

		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->request->redirect($this->createUrl('index'));
	}

	/**
	 * Render data to display in theme header.
	 */
	public function actionRenderSmallCart()
	{
		$this->renderPartial('_small_cart', array('lng' => Yii::app()->language));
	}
	
	/**
	 * Render data to display in popup.
	 */
	public function actionRenderPopupCart()
	{
		$this->renderPartial('_popup_cart', array('lng' => Yii::app()->language));
	}

	/**
	 * Create new order
	 * @return Order
	 */

	public function createOrder($price, $discount, $discount_minus, $discount_promo, $discount_minus_promo)
	{
		if(Yii::app()->cart->countItems() == 0)
			return false;

		$order = new Order;

        $cityInfo = $this->getCurrentCityInfo(true);
        $receiver_city = $cityInfo->name;
        $deliveryPrice  = $cityInfo->delivery;

		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$translPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
		$lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';
					
		// Set main data
		$order->user_id      = Yii::app()->user->isGuest ? null : Yii::app()->user->id;
		$order->user_name    = $this->form->name;
		$order->payment_status = 'new'; // для всех новых заказов – одинаковый статус
		$order->country = $this->form->country;
		$order->city = $this->form->city;
		$order->total_price = $price;
		$order->discount = $discount;
		$order->discount_minus = $discount_minus;
		$order->discount_promo = $discount_promo;
		$order->discount_minus_promo = $discount_minus_promo;
		$order->user_email   = $this->form->email;
		$order->user_phone   = $this->form->phone;
		$order->user_address = $this->form->address;
		$order->user_comment = $this->form->comment;		
		$order->receiver_name = $this->form->receiver_name;
		$order->receiver_city = $receiver_city;
		$order->phone1 = $this->form->phone1;
		$order->phone2 = $this->form->phone2;
		$order->datetime_del = $this->form->datetime_delivery;
		$order->doPhoto = $this->form->doPhoto;
		$order->do_card = $this->form->do_card;
		$order->card_text = $this->form->card_text;
		$order->card_transl = $this->form->card_transl;
		$order->delivery_price = $deliveryPrice;
		$order->lang = $lang;
		if(!empty($order->doPhoto)){$order->photo_price=$photoPrice;}
		if(!empty($order->do_card)){$order->card_price=$cardPrice;}
		if(!empty($order->card_transl)){$order->transl_price=$translPrice;}

		
				
		// $deliveryPrice=Yii::app()->db->createCommand()
							     // ->select("c.delivery")
							     // ->from("city c")
							     // ->join('cityTranslate ct','ct.object_id=c.id')
							     // ->where('ct.name=:name', array(':name'=>$order->receiver_city))
							     // ->queryRow();								
		//if (empty($deliveryPrice)) $deliveryPrice = 10; // стоимость доставки по умолчанию
		

		if($order->validate())
			$order->save();
		else
			throw new CHttpException(503, Yii::t('OrdersModule.core', 'Creating order error'));

		// Process products
		foreach(Yii::app()->cart->getDataWithModels() as $item)
		{
            $translate = $this->translateProductInfo($item['model']->id);
			$ordered_product = new OrderProduct;
			$ordered_product->order_id        = $order->id;
			$ordered_product->product_id      = $item['model']->id;
			$ordered_product->configurable_id = $item['configurable_id'];
			$ordered_product->name            = (!empty($translate->name)) ? $translate->name : $item['model']->name;
			$ordered_product->quantity        = $item['quantity'];
			$ordered_product->sku             = $item['model']->sku;
			$ordered_product->price           = StoreProduct::calculatePrices($item['model'], $item['variant_models'], $item['configurable_id']);

			// Process configurable product
			if(isset($item['configurable_model']) && $item['configurable_model'] instanceof StoreProduct)
			{
				$configurable_data=array();

				$ordered_product->configurable_name = $item['configurable_model']->name;
				// Use configurable product sku
				$ordered_product->sku = $item['configurable_model']->sku;
				// Save configurable data

				$attributeModels = StoreAttribute::model()->findAllByPk($item['model']->configurable_attributes);
				foreach($attributeModels as $attribute)
				{
					$method = 'eav_'.$attribute->name;
					$configurable_data[$attribute->title]=$item['configurable_model']->$method;
				}
				$ordered_product->configurable_data=serialize($configurable_data);
			}

			// Save selected variants as key/value array
			if(!empty($item['variant_models']))
			{
				$variants = array();
				foreach($item['variant_models'] as $variant)
					$variants[$variant->attribute->title] = $variant->option->value;
				$ordered_product->variants = serialize($variants);
			}

			$ordered_product->save();
		}

		// Reload order data.
		$order->refresh();

		// All products added. Update delivery price.
		$order->updateDeliveryPrice();

		// Send email to user.
		$this->sendEmail($order, $price);
		if(empty(Yii::app()->params['is_local'])) {
            $this->sendEmailAdmin($order, Yii::app()->params['adminEmail']);
            $this->sendEmailAdmin($order, '7roses.office@gmail.com');
        }
		return $order;
	}

	/**
	 * Check if product variantion exists
	 * @param $product_id
	 * @param $attribute_id
	 * @param $variant_id
	 * @return string
	 */
	protected function _checkVariantExists($product_id, $attribute_id, $variant_id)
	{
		return StoreProductVariant::model()->countByAttributes(array(
			'id'           => $variant_id,
			'product_id'   => $product_id,
			'attribute_id' => $attribute_id
		));
	}

	/**
	 * Recount product quantity and redirect
	 */
	public function processRecount()
	{
		Yii::app()->cart->recount(Yii::app()->request->getPost('quantities'));

		if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->request->redirect($this->createUrl('index'));
	}

	/**
	 * Add message to errors array.
	 * @param string $message
	 * @param bool $fatal finish request
	 */
	protected function _addError($message, $fatal = false)
	{
		if($this->_errors === false)
			$this->_errors = array();

		array_push($this->_errors, $message);

		if($fatal === true)
			$this->_finish();
	}

	/**
	 * Process result and exit!
	 */
	protected function _finish()
	{
		echo CJSON::encode(array(
			'errors'=>$this->_errors,
			'message'=>Yii::t('OrdersModule.core','Product successfully added in {cart}', array(
				'{cart}'=>CHtml::link(Yii::t('OrdersModule', 'корзину'), array('/orders/cart/index'))
			)),
		));
		exit;
	}

	/**
	 * Sends email to user after create new order.
	 */
	private function sendEmail(Order $order, $price)
	{
		$theme=Yii::t('OrdersModule.core', 'Your order #').$order->id;

		$lang=Yii::app()->language;
		$emailBodyFile=Yii::getPathOfAlias("application.emails.$lang").DIRECTORY_SEPARATOR.'new_order.php';
		
		// If template file does not exists use default russian translation
		if(!file_exists($emailBodyFile))
			$emailBodyFile=Yii::getPathOfAlias("application.emails.ru").DIRECTORY_SEPARATOR.'new_order.php';
		$body = $this->renderFile($emailBodyFile, array('order'=>$order, 'price'=>$price), true);

		$mailer           = Yii::app()->mail;
		// $mailer->IsSMTP();
		$mailer->From     = Yii::app()->params['adminEmail'];
		$mailer->FromName = Yii::app()->settings->get('core', 'siteName');
		$mailer->Subject  = $theme;
		$mailer->Body     = $body;
		$mailer->AddAddress($order->user_email);
		$mailer->AddReplyTo(Yii::app()->params['adminEmail']);
		$mailer->isHtml(true);
		$mailer->Send();
		$mailer->ClearAddresses();
		$mailer->ClearReplyTos();
	}
	private function sendEmailAdmin(Order $order,$address)
	{
		$theme=Yii::t('OrdersModule.core', 'MI# /').$order->id;
		$model = Order::model()->find('id=:id', array(':id'=>$order->id));
		$lang=Yii::app()->language;
		$emailBodyFile=Yii::getPathOfAlias("application.emails.ru").DIRECTORY_SEPARATOR.'admin_order.php';
		
		// If template file does not exists use default russian translation
		if(!file_exists($emailBodyFile))
			$emailBodyFile=Yii::getPathOfAlias("application.emails.ru").DIRECTORY_SEPARATOR.'admin_order.php';
		$body = $this->renderFile($emailBodyFile, array('order'=>$order, 'model'=>$model), true);

		$mailer           = Yii::app()->mail;
		$mailer->From     = $order->user_email;
		//$mailer->From     = Yii::app()->params['adminEmail'];
		$mailer->FromName = $order->user_name;
		$mailer->Subject  = $theme;
		$mailer->Body     = $body;
		$mailer->AddAddress($address);
		// $mailer->AddAddress('7roses.office@gmail.com');
		$mailer->AddReplyTo($order->user_email);
		//$mailer->AddReplyTo=Yii::app()->params['adminEmail']);
		$mailer->isHtml(true);
		$mailer->SetMessageType = 'html';
		$mailer->Send();
		$mailer->ClearAddresses();
		$mailer->ClearReplyTos();
	}
	public function actionPhone()
	{
		 $theme=Yii::t('OrdersModule', 'Order for CallBack');

		$lang=Yii::app()->language;
		$emailBodyFile=Yii::getPathOfAlias("application.emails.$lang").DIRECTORY_SEPARATOR.'new_order_admin.php';
		
		// If template file does not exists use default russian translation
		if(!file_exists($emailBodyFile))
			$emailBodyFile=Yii::getPathOfAlias("application.emails.ru").DIRECTORY_SEPARATOR.'new_order_admin.php';
		$body = $this->renderFile($emailBodyFile, array(
			'username'=>$_POST['name'],'phone'=>$_POST['phone'],'email'=>$_POST['email'],
			'id'=>$_POST['id'],'quantity'=>$_POST['quantity']
			), true);
		$mailer           = Yii::app()->mail;
		$mailer->From     = $_POST['email'];
		$mailer->FromName = $_POST['name'];
		$mailer->Subject  = $theme;
		$mailer->Body     = $body;
		$mailer->AddAddress(Yii::app()->params['adminEmail']);
		$mailer->AddReplyTo($_POST['email']);
		$mailer->isHtml(true);
		$mailer->SetMessageType = 'html';
		$mailer->Send();
		$mailer->ClearAddresses();
		$mailer->ClearReplyTos();
	}

    public function translateProductInfo($product_id)
    {
        $return = array();
        if(!empty($product_id)){
            $lang= Yii::app()->language;
            if($lang == 'ua')
                $lang = 'uk';
            $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
            $translate = StoreProductTranslate::model()->findByAttributes(array('language_id'=>$langArray->id,'object_id'=>$product_id));
        }

        return (!empty($translate)) ? $translate : $return;
    }

    /**
     * посадочная страница после оплаты с использованием одной из платёжных систем
     */
    public function actionStatus()
    {
        $secret_key = Yii::app()->request->getParam('secret_key');
        $model = Order::model()->find('secret_key=:secret_key', array(':secret_key'=>$secret_key));
        if($model->payment_status == 'paid'){
            $model->status_id = 6;
            $model->update();
            // при успешной оплате – переадрессация на /success
            Yii::app()->request->redirect($this->createUrl('/cart/view/'.$model->secret_key.'/success'));
        }
        $payment_methods = StorePaymentMethod::model()->active()->findAll();
        $payment_methods = (!empty($payment_methods)) ? CArray::toolIndexArrayBy($payment_methods, 'id') : array();
        if(!empty($model->payment_id)){
            // если указан способ оплаты – обрабатываем статус платежа
            // WayForPay
            if(!empty($payment_methods[$model->payment_id])
                && (strtolower($payment_methods[$model->payment_id]->name) === 'wayforpay'))
            {
                $this->wfpProceed($model);
            }
            // Portmone
            if(!empty($payment_methods[$model->payment_id])
                && (strtolower($payment_methods[$model->payment_id]->name) === 'portmone'))
            {
                $this->portmoneProceed($model);
            }
            // если после обработки статус платежа pending – показываем отдельную страницу
            if($model->payment_status == 'pending'){
                Yii::app()->request->redirect($this->createUrl('pending', array('secret_key'=>$model->secret_key)));
            }
            else
                Yii::app()->request->redirect($this->createUrl('view', array('secret_key'=>$model->secret_key)));
        }
        else{
            // если способ оплаты НЕ указан – перенаправляем на просмотр заказа
            Yii::app()->request->redirect($this->createUrl('view', array('secret_key'=>$model->secret_key)));
        }
    }

    /**
     * работаем с WayForPay
     * @param object $model - модель заказа
     */
    public function wfpProceed($model)
    {
        // дефолтные параметры
        $trans = array(
            'en' => 'The status of payment is uncertain, probably the payment was not accepted',
            'ru' => 'Статус платежа неопределён, вероятно платёж не был принят',
            'uk' => 'Статус платежу невизначений, ймовірно платіж не був прийнятий',
        );
        $status_message = $trans[Yii::app()->language];
        // получаем orderReference для этого заказа
        $order_ref_command = Yii::app()->db
            ->createCommand("SELECT `orderReference` FROM `WfpOrder` WHERE `order_id` = " . (int)$model->id);
        $order_ref = $order_ref_command->queryScalar();
        if(!empty($order_ref)){
            // запрашиваем и обновляем статус платежа по этому заказу
            $transactionStatus = $this->wfpStatus($order_ref, $model->id);
            if(!empty($transactionStatus)){
                // формируем переменные для отображения страницы
                $payment_statuses = $this->paymentResponseStatus('wayforpay');
                if(!empty($payment_statuses[$transactionStatus]['status'])){
                    // получаем информацию по статусу
                    $status = $this->getPaymentStatus($payment_statuses[$transactionStatus]['status']);
                    $status_message = (!empty($status->message)) ? $status->message : '';
                    // обновляем статус платежа в самом заказе
                    // не обновляем – если у заказа уже стоит статус paid
                    if($model->payment_status != 'paid'){
                        $from_status = $model->payment_status; // старый статус
                        $model->payment_status = $payment_statuses[$transactionStatus]['status'];
                        $model->save();
                        // письмо клиенту – если статус заказа изменился на paid
                        if($model->payment_status == 'paid'){
                            // email клиенту о смене статуса на paid
                            $this->sendPaymentEmail($model->user_email, $model->id, $from_status, $model->payment_status);
                        }
                    }
                    if($payment_statuses[$transactionStatus]['status'] == 'paid'){
                        $model->status_id = 6; // обновляем статус самого заказа
                        $model->update();
                        // при успешной оплате – переадрессация на /success
                        Yii::app()->request->redirect($this->createUrl('/cart/view/'.$model->secret_key.'/success'));
                    }
                }
            }
        }
        // flash-сообщение для клиента
        $this->addErrorFlashMessage($status_message);
    }

    /**
     * работаем с Portmone
     * @param object $model - модель заказа
     */
    public function portmoneProceed($model)
    {
        // дефолтные параметры
        $trans = array(
            'en' => 'The status of payment is uncertain, probably the payment was not accepted',
            'ru' => 'Статус платежа неопределён, вероятно платёж не был принят',
            'uk' => 'Статус платежу невизначений, ймовірно платіж не був прийнятий',
        );
        $status_message = $trans[Yii::app()->language];
        // запрашиваем и обновляем статус платежа по этому заказу
        $transactionStatus = $this->portmonePayment($model->id);
        if(!empty($transactionStatus)){
            // формируем переменные для отображения страницы
            $payment_statuses = $this->paymentResponseStatus('portmone');
            if(!empty($payment_statuses[$transactionStatus]['status'])){
                // получаем информацию по статусу
                $status = $this->getPaymentStatus($payment_statuses[$transactionStatus]['status']);
                $status_message = (!empty($status->message)) ? $status->message : '';
                // обновляем статус платежа в самом заказе
                // не обновляем – если у заказа уже стоит статус paid
                if($model->payment_status != 'paid'){
                    $from_status = $model->payment_status; // старый статус
                    $model->payment_status = $payment_statuses[$transactionStatus]['status'];
                    $model->save();
                    // письмо клиенту – если статус заказа изменился на paid
                    if($model->payment_status == 'paid'){
                        // email клиенту о смене статуса на paid
                        $this->sendPaymentEmail($model->user_email, $model->id, $from_status, $model->payment_status);
                    }
                }
                if($payment_statuses[$transactionStatus]['status'] == 'paid'){
                    // при успешной оплате – переадрессация на /success
                    $model->status_id = 6; // обновляем статус самого заказа
                    $model->update();
                    Yii::app()->request->redirect($this->createUrl('/cart/view/'.$model->secret_key.'/success'));
                }
            }
        }
        // flash-сообщение для клиента
        $this->addErrorFlashMessage($status_message);
    }

    /**
     * Получаем и обновляем статус заказа в системе WayForPay
     * @param $order_ref orderReference заказа в системе WayForPay
     * @param $order_id
     * @return bool|string
     */
    public function wfpStatus($order_ref, $order_id)
    {
        $string = Yii::app()->params['merchantAccount'] . ";" . $order_ref;
        $merchantSignature = hash_hmac("md5", $string, Yii::app()->params['merchantSecretKey']);
        $data = array(
            'transactionType' => 'CHECK_STATUS',
            'merchantAccount' => Yii::app()->params['merchantAccount'],
            'orderReference' => $order_ref,
            'merchantSignature' => $merchantSignature,
            'apiVersion' => 1,
        );
        $status = '';
        $order_ex = explode('_', $order_ref);
        $order_id = end($order_ex);
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://api.wayforpay.com/api');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $out = curl_exec($curl);
            curl_close($curl);
            if(CJsn::isJson($out)){
                $response = json_decode($out, true);
                $wfp_order = WfpOrder::model()->findByAttributes(array('order_id' => $order_id));
                if(!empty($wfp_order) && !empty($response['orderReference'])){
                    unset($response['orderReference'], $response['merchantAccount']);
                    $response['createdDate'] = (!empty($response['createdDate'])) ? date('Y-m-d H:i:s', $response['createdDate']) : '0000-00-00 00:00:00';
                    $response['processingDate'] = (!empty($response['processingDate'])) ? date('Y-m-d H:i:s', $response['processingDate']) : '0000-00-00 00:00:00';
                    WfpOrder::model()->updateByPk($wfp_order->id, $response);
                    $status = (!empty($response['transactionStatus'])) ? $response['transactionStatus'] : '';
                }
                $this->savePaymentStatusLog($order_id, 'wayforpay', (!empty($status) ? $status : 'Unknown'), (string)$out, serialize($response));
            }
        }
        return $status;
    }

    /**
     * получаем информацию о платеже, возвращаем статус платежа
     * @param $order_id
     * @return mixed|string
     */
    public function portmonePayment($order_id)
    {
        $data = array(
            "method"            => "result",
            "payee_id"          => 2046,
            "login"             => 'SHP_7ROSES',
            "password"          => '7ro1310',
            "shop_order_number" => $order_id,
        );

        $result_portmone = $this->curlRequest('https://www.portmone.com.ua/gateway/', $data);
        $parseXml = $this->parseXml($result_portmone);
        $order_data = (array)$parseXml->orders->order; // $order_data['status'] - статус платежа
        $status = (!empty($order_data['status'])) ? $order_data['status'] : '';
        $saved = array(
            'shop_bill_id' => (!empty($order_data['shop_bill_id'])) ? $order_data['shop_bill_id'] : '',
            'shop_order_number' => (!empty($order_data['shop_order_number'])) ? $order_data['shop_order_number'] : '',
            'bill_date' => (!empty($order_data['bill_date'])) ? $order_data['bill_date'] : '',
            'pay_date' => (!empty($order_data['pay_date'])) ? $order_data['pay_date'] : '',
            'bill_amount' => (!empty($order_data['bill_amount'])) ? $order_data['bill_amount'] : '',
            'status' => (!empty($order_data['status'])) ? $order_data['status'] : '',
            'error_code' => (!empty($order_data['error_code'])) ? $order_data['error_code'] : '',
            'error_message' => (!empty($order_data['error_message'])) ? $order_data['error_message'] : '',
        );
        $this->savePaymentStatusLog($order_id, 'portmone', (!empty($status) ? $status : 'Unknown'), (string)$result_portmone, serialize($saved));
        return $status;
    }

    /**
     * A request to verify the validity of payment in Portmone
     **/
    private function curlRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (200 !== intval($httpCode)) {
            return false;
        }
        return $response;
    }

    /**
     * Parsing XML response from Portmone
     **/
    private function parseXml($string) {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (false !== $xml) {
            return $xml;
        } else {
            return false;
        }
    }

    /**
     * статусы платежей для платёжных систем - ассоциации со статусами в БД
     * @param $payment_system
     * @return array|mixed
     */
    public function paymentResponseStatus($payment_system)
    {
        $statuses = array(
            'wayforpay' => array(
                'InProcessing' => array(
                    'status' => 'pending',
                ),
                'Approved' => array(
                    'status' => 'paid',
                ),
                'Pending' => array(
                    'status' => 'pending',
                ),
                'Expired' => array(
                    'status' => 'rejected',
                ),
                'Declined' => array(
                    'status' => 'rejected',
                ),
            ),
            'portmone' => array(
                'PAYED' => array(
                    'status' => 'paid',
                ),
                'CREATED' => array(
                    'status' => 'pending',
                ),
                'REJECTED' => array(
                    'status' => 'rejected',
                ),
            ),
        );

        return (!empty($statuses[$payment_system])) ? $statuses[$payment_system] : array();
    }

    public function addErrorFlashMessage($message)
    {
        $currentMessages = Yii::app()->user->getFlash('error_messages');

        if (!is_array($currentMessages))
            $currentMessages = array();

        Yii::app()->user->setFlash('error_messages', CMap::mergeArray($currentMessages, array($message)));
    }

    /**
     * получаем информацию по статусу
     * @param string $key
     */
    public function getPaymentStatus($key)
    {
        return OrderPaymentStatus::model()
            ->language($this->language_info->id)
            ->active()
            ->find('`key` = :key', array(':key' => $key));
    }

    /**
     * письмо клиенту после смены статуса платежа
     *
     * @param $address
     * @param $order_id
     * @param $from_status
     * @param $to_status
     */
    private function sendPaymentEmail($address, $order_id, $from_status, $to_status)
    {
        $theme = Yii::t('OrdersModule.core', 'Your payment status by order #').$order_id;

        $message = Yii::t('OrdersModule.core',
            'Your payment status by order #{order_id} was changed from «{from_status}» to «{to_status}».',
            array(
                '{order_id}' => $order_id,
                '{from_status}' => $from_status,
                '{to_status}' => $to_status,
            )
        );

        $mailer           = Yii::app()->mail;
        // $mailer->IsSMTP();
        $mailer->From     = Yii::app()->params['adminEmail'];
        $mailer->FromName = Yii::app()->settings->get('core', 'siteName');
        $mailer->Subject  = $theme;
        $mailer->Body     = $message;
        $mailer->AddAddress($address);
        $mailer->AddReplyTo(Yii::app()->params['adminEmail']);
        $mailer->isHtml(true);
        $mailer->Send();
        $mailer->ClearAddresses();
        $mailer->ClearReplyTos();
    }

    /**
     * сохраняем логи статусов платежей, полученные от платёжных агрегаторов
     * @param $order_id
     * @param $payment_type
     * @param $status
     * @param $original
     * @param $used
     */
    public function savePaymentStatusLog($order_id, $payment_type, $status, $original, $used)
    {
        $log = new OrderPaymentStatusLog();
        $log->order_id = $order_id;
        $log->payment_type = $payment_type;
        $log->status = $status;
        $log->response_orig = $original;
        $log->response_used = $used;
        $log->response_date = date('Y-m-d H:i:s');
        $log->save();
    }

    /**
     * страница для заказов со статусом платежа pending
     */
    public function actionPending()
    {
        $secret_key = Yii::app()->request->getParam('secret_key');
        // текущий заказ
        $model = Order::model()->find('secret_key=:secret_key', array(':secret_key'=>$secret_key));
        // товары в заказе
        $products = OrderProduct::model()->getOrderProducts($model->id);
        $this->render('pending',
            array(
                'model' => $model,
                'products' => $products,
            )
        );
    }
}
