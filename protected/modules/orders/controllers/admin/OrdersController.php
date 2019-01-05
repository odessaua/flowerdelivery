<?php

Yii::import('store.models.*');
Yii::import('orders.models.*');
Yii::import('store.StoreModule');
// ini_set('error_reporting',-1);
/**
 * Admin orders
 */
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
class OrdersController extends SAdminController {

	/**
	 * Display orders methods list
	 */
	public function actionIndex()
	{
	    // обновляем статусы платежей по заказам
//        $this->checkPaymentStatuses();

		$model = new Order('search');

		if (!empty($_GET['Order']))
			$model->attributes = $_GET['Order'];

		$dataProvider = $model->search();
		$dataProvider->pagination->pageSize = Yii::app()->settings->get('core', 'productsPerPageAdmin');

		$this->render('index', array(
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Create new order
	 */
	public function actionCreate()
	{
		$this->actionUpdate(true);
	}
	/**
	 * Update order
	 * @param bool $new
	 * @throws CHttpException
	 */
	public function actionImport(){
		$model = Order::model()->findByPk($_REQUEST['id']);

		$id=$_REQUEST['id'];
		$products = OrderProduct::model()->getOrderProducts($id);
		header("Content-type: text/plain");
   		header("Content-Disposition: attachment; filename=order-$id.txt");
   		echo "Order №: ".$model->id."r\n";
		echo "City: ".$model->receiver_city."\r\n";
		echo "Delivery Date: ".$model->datetime_del."\r\n";
		echo "Info about sender \r\n\r\n";
		echo "City: ".$model->receiver_city."\r\n";
		echo "Name: ".$model->receiver_name."\r\n";
		echo "Address: ".$model->user_address."\r\n";
		echo "Phone &#8470;1: ".$model->phone1."\r\n";
   		echo "Phone &#8470;2: ".$model->phone2."\r\n";
		echo "Additional Info: ".$model->user_comment."\r\n";
		echo "Greeting Card: ".$model->card_text."\r\n\r\n";
		echo "Info about sender \r\n\r\n";
   		echo "Name: ".$model->user_name."\r\n";
   		echo "Email: ".$model->user_email."\r\n";
		echo "Country: ".$model->country."\r\n";
		echo "City :".$model->city."\r\n";
   		echo "Address: ".$model->user_address."\r\n";
   		echo "Phone: ".$model->user_phone."\r\n\r\n";
		echo "Info about order \r\n\r\n";	
		echo "Total order sum: ".$model->total_price."\r\n\r\n";		
		
		   		foreach ($products as $one) {
				$variants = unserialize($one['variants']);
				
		if(!empty($variants)) {	foreach($variants as $key=>$value)	$result .= "<br/> - {$key}: {$value}"; }
				
   		echo "Item: ".$one['name']."(".$result.")"."\r\n Quantity:".$one['quantity']."\r\n Price:".$one['price']."\r\n";
					}
		if ($model->doPhoto==1)
		echo "Photo of the delivery: paid \r\n";
		echo "Order info: https://".$_SERVER['HTTP_HOST']."/cart/view/".$model->secret_key."\r\n\r\n";
   		echo "Creation date: ".$model->created."\r\n";
   		echo "Discount: ".$model->discount."\r\n";
		echo "Payment id: ".$model->payment_id."\r\n";
   		echo "Admin comments: ".$model->admin_comment."\r\n";   		
   		echo "Cost of the delivery: ".$model->delivery_price."\r\n";
   		echo "Paid: ".$model->paid."\r\n";
   		
	}
	//Get an array with geoip-infodata
       public function getGeoIpInfo($ipAddress)
       {
       	
		    //$ip_key = "9a531e5be48d22f2df5d421eafbb87c2b376206e7314174e7e7c131104e44dae";
		    // var_dump($ipAddress);		
			//  $query = "http://api.ipinfodb.com/v3/ip-city/?key=" . $ip_key . "&ip=" . $ipAddress . "&format=json";
			
			$access_key = '746cdb97e56fb008c55286b8b3c67cd6';
			
			// Initialize CURL:
			$query = curl_init('http://api.ipstack.com/'.$ipAddress.'?access_key='.$access_key.'');
			
			curl_setopt($query, CURLOPT_RETURNTRANSFER, true);

			// Store the data:
			$json = curl_exec($query);
			curl_close($query);

			// Decode JSON response:
			$data = json_decode($json, true);

			//var_dump($data);
		    // if ($data['statusCode'] == "OK") {
		    // 	array_push($data['ip'],array('ip'=>$ipAddress));
		    //     return $data;
		    // } else {
		        // echo $data['statusCode']." ".$data['statusMessage'];
		        return $data ;
		    // }	
       }
  


 	public function actionUpdate($new = false)
	{
		if(SLicenseChecker::check() === false && SLicenseChecker::isOnLocalhost() === false)
			throw new CHttpException(404, 'В ознакомительной версии редактирование заказов недоступно.');

		if ($new === true)
		{
			$model = new Order;
			$model->unsetAttributes();

		}
		else
			$model = $this->_loadModel($_GET['id']);
		
		// var_dump($model->ip_address);
		$geo=$this->getGeoIpInfo($model->ip_address);
		$names=array();
		$getPhotos=OrderPhoto::getPhotos($model->id);
		$total=0;
		$photoPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>17))['price'];
		$cardPrice=StoreDeliveryMethod::model()->findByAttributes(array('id'=>18))['price'];
		$citys=City::model()->findAll();
		$photos=array();
		if(isset($getPhotos)){
			foreach ($getPhotos as $key=>$value) {
				$photos[$key]="/uploads/delivery/".$value['photo'];
			}
		}

		$from_status = $model->payment_status;
		$photos_errors = array();

		if(Yii::app()->request->isPostRequest)
		{

			$model->attributes = $_POST['Order'];
			// var_dump( $_POST['Order']);die;
			$model->total_price+=isset($model->photo_price)?$model->photo_price:0;
			$model->total_price+=isset($model->card_price)?$model->card_price:0;
			$city=Yii::app()->db->createCommand()
				->select("*")
				->from("city")
				->where('name=:name',array(":name"=>$_POST['receiver_city']))

				->queryRow();
			$model->delivery_price=$city['delivery']; 
			// var_dump($model->doPhoto);die;
			// $model->total_price=$total;
			for($i=0;$i<count($model->getOrderedProducts()->getData());$i++) {
				$images[$i]=CUploadedFile::getInstancesByName('images_product'.$i);
				if(!empty($images[$i])){
				    if($_FILES['images_product'.$i]['size'][0] <= (250 * 1024)){
					    $names[$i]=$model->getOrderedProducts()->getData()[$i]->product_id;
                    }
                    else{
                        $photos_errors[$i] = 'Файл ' . $_FILES['images_product'.$i]['name'][0]
                            . ' весит больше 250 kb. Пожалуйста, загрузите файл меньшего размера!';
                        unset($images[$i]);
                    }
                }
			}

			$rand=rand(0,9999);
            if (isset($images)) {
 				

                // go through each uploaded image
                for ($i=0;$i<count($images);$i++) {
                	if (empty($names[$i])) {
                		continue;
                	}
                	else{
	                    if ($images[$i][0]->saveAs(Yii::getPathOfAlias('webroot').'/uploads/delivery/'.$rand.$images[$i][0]->name)) {
	                       	
	                       	$check=OrderPhoto::model()->findByAttributes(array('order_id'=>$model->id,'product_id'=>$names[$i]));
	                       
	                       	if($check){
                                @unlink(Yii::getPathOfAlias('webroot') . '/uploads/delivery/' . $check->photo);
	                       		$img_add=OrderPhoto::model()->findByPk($check->id);
	                       		$img_add->photo = $rand.$images[$i][0]->name; //it might be $img_add->name for you, filename is just what I chose to call it in my model
		                        $img_add->product_id = $names[$i]; // this links your picture model to the main model (like your user, or profile model)
		                        $img_add->order_id=$model->id;
		                        $img_add->update();
	                       	}
	                       	else{ 
	                        	$img_add = new OrderPhoto();
		                        $img_add->photo = $rand.$images[$i][0]->name; //it might be $img_add->name for you, filename is just what I chose to call it in my model
		                        $img_add->product_id = $names[$i]; // this links your picture model to the main model (like your user, or profile model)
		                        $img_add->order_id=$model->id;
		                        $img_add->save(); // DONE
	                    	}
	                    }
                    }
                    
                }
                
            }
			if($model->validate())
			{	
				$model->admin_id = Yii::app()->user->id;
				$model->save();
				if(sizeof(Yii::app()->request->getPost('quantity', array())))
					$model->setProductQuantities(Yii::app()->request->getPost('quantity'));

				$model->updateDeliveryPrice();
				$model->updateTotalPrice();

				$this->setFlashMessage(Yii::t('OrdersModule.admin', 'Изменения успешно сохранены'));

				// письмо клиенту – если статус заказа изменился на paid
				if(($from_status !== $model->payment_status) && ($model->payment_status == 'paid')){
                    // email клиенту о смене статуса на paid
                    $this->sendPaymentEmail($model->user_email, $model->id, $from_status, $model->payment_status);
                }

                if(empty($photos_errors)) {
                    if (isset($_POST['REDIRECT']))
                        $this->smartRedirect($model);
                    else
                        $this->redirect(array('index'));
                }
			}
		}

        $this->wfpStatus($model->id); // обновляем статус заказа в системе WayForPay
        // данные о заказе в системе WayForPay
        $wfp_order = WfpOrder::model()->find(array(
            'condition' => '`order_id` = ' . (int)$model->id,
            'order' => '`id` DESC'
        ));

		$this->render('update', array(
			'deliveryMethods' => StoreDeliveryMethod::model()->applyTranslateCriteria()->orderByName()->findAll(),
			'statuses'        => OrderStatus::model()->orderByPosition()->findAll(),
			'model'           => $model,
			'orderPhoto' 	  =>$photo,
			'photos' 		  =>$photos,
			'geoinfo'	      => $geo,
			'citys'=>$citys,
            'wfp_order' => (!empty($wfp_order)) ? $wfp_order : null,
            'photos_errors' => $photos_errors,
		));
	}

    /**
     * Получаем и обновляем статус заказа в системе WayForPay
     * @param int $order_id ID заказа в системе WayForPay
     * @return bool
     */
    public function wfpStatus($order_id)
    {
        $wfp_order = WfpOrder::model()->find(array(
            'condition' => '`order_id` = ' . (int)$order_id,
            'order' => '`id` DESC'
        ));
        if(empty($wfp_order)) return false; // такого заказа нет в таблице

        $string = Yii::app()->params['merchantAccount'] . ";" . $wfp_order->orderReference;
        $merchantSignature = hash_hmac("md5", $string, Yii::app()->params['merchantSecretKey']);
        $data = array(
            'transactionType' => 'CHECK_STATUS',
            'merchantAccount' => Yii::app()->params['merchantAccount'],
            'orderReference' => $wfp_order->orderReference,
            'merchantSignature' => $merchantSignature,
            'apiVersion' => 1,
        );

        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://api.wayforpay.com/api');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $out = curl_exec($curl);//var_dump($out);
            curl_close($curl);

            if(CJsn::isJson($out)){
                //var_dump(json_decode($out, true));
                $response = json_decode($out, true);
                if(!empty($response['orderReference'])){
                    unset($response['orderReference'], $response['merchantAccount']);
                    $response['createdDate'] = (!empty($response['createdDate'])) ? date('Y-m-d H:i:s', $response['createdDate']) : '0000-00-00 00:00:00';
                    $response['processingDate'] = (!empty($response['processingDate'])) ? date('Y-m-d H:i:s', $response['processingDate']) : '0000-00-00 00:00:00';
                    WfpOrder::model()->updateByPk($wfp_order->id, $response);
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Display gridview with list of products to add to order
	 */
	public function actionAddProductList()
	{
		$order_id     = Yii::app()->request->getQuery('id');
		$model        = $this->_loadModel($order_id);
		$dataProvider = new StoreProduct('search');
		if(isset($_GET['StoreProduct']))
			$dataProvider->attributes = $_GET['StoreProduct'];
		$this->renderPartial('_addProduct', array(
			'dataProvider' => $dataProvider,
			'order_id'     => $order_id,
			'model'        => $model,
		));
	}

	/**
	 * Add product to order
	 * @throws CHttpException
	 */
	public function actionAddProduct()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$order   = $this->_loadModel($_POST['order_id']);
			$product = StoreProduct::model()->findByPk($_POST['product_id']);
			if(!$product)
				throw new CHttpException(404, Yii::t('OrdersModule.admin', 'Ошибка. Продукт не найден.'));
			$order->addProduct($product, $_POST['quantity'], $_POST['price'],$_POST['variants']);
			
		}
	}

	/**
	 * Render ordered products after new product added.
	 * @param $order_id
	 */
	public function actionRenderOrderedProducts($order_id)
	{

		$this->renderPartial('_orderedProducts', array(
			'model'=>$this->_loadModel($order_id),
			'geoinfo' => array('domain'=>$geo->domain,'country'=>$geo->country,'state'=>$geo->state,'town'=>$geo->town)

		));
	}

	/**
	 * Get ordered products in json format.
	 * Result is displayed in the orders list.
	 */
	public function actionJsonOrderedProducts()
	{
		$model = $this->_loadModel(Yii::app()->request->getQuery('id'));
		$data  = array();

		foreach($model->getOrderedProducts()->getData() as $product)
		{
			$data[]=array(
				'name'     => $product->renderFullName,
				'quantity' => $product->quantity,
				'price'    => StoreProduct::formatPrice($product->price),
			);
		}

		echo CJSON::encode($data);
	}

	/**
	 * Load order model
	 * @param $id
	 * @return Order
	 * @throws CHttpException
	 */
	protected function _loadModel($id)
	{
		$model = Order::model()->findByPk($id);
		if (!$model)
			$this->error404();

		return $model;
	}

	/**
	 * Delete order
	 * @param array $id
	 */
	public function actionDelete($id = array())
	{
		if (Yii::app()->request->isPostRequest)
		{
			$model = Order::model()->findAllByPk($_REQUEST['id']);

			if (!empty($model))
			{
				foreach($model as $m)
					$m->delete();
			}

			if (!Yii::app()->request->isAjaxRequest)
				$this->redirect('index');
		}
	}

	/**
	 * Delete product from order
	 */
	public function actionDeleteProduct()
	{
		$order = Order::model()->findByPk(Yii::app()->request->getPost('order_id'));

		if(!$order)
			$this->error404();

		$order->deleteProduct(Yii::app()->request->getPost('id'));
	}
	
		/**
	 * Delete product from order
	 */
	public function actionDeletePhoto()
	{
		$photo = OrderPhoto::model()->findByPk(Yii::app()->request->getPost('id'));

		if(!$photo)
			$this->error404();

		$photo->deletePhoto(Yii::app()->request->getPost('id'));
	}

	/**
	 * Render order history tab
	 */
	public function actionHistory()
	{
		$id    = Yii::app()->request->getQuery('id');
		$model = Order::model()->findByPk($id);

		if(!$model)
			$this->error404();

		$this->render('_history', array(
			'model'=>$model
		));
	}

	/**
	 * @throws CHttpException
	 */
	public function error404()
	{
		throw new CHttpException(404, Yii::t('OrdersModule.admin', 'Заказ не найден.'));
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
        $theme = Yii::t('OrdersModule.core', 'Payment status by your order #').$order_id;

        $message = Yii::t('OrdersModule.core',
            'Payment status by your order #{order_id} was changed from «{from_status}» to «{to_status}».',
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
     * проверка и обновление статусов у заказов, у которых:
     * `payment_id` > 0            - указан способ оплаты
     * `status_id` = 1             - статус заказа «Новый»
     * `payment_status` != 'paid'  - статус платежа НЕ «оплачен»
     */
    public function checkPaymentStatuses()
    {
        // получаем заказы, у которых указан метод оплаты > 0 и статус заказа = 1 и статус платежа != paid
        $orders = Order::model()->findAll('`payment_id` > 0 AND `status_id` = 1 AND `payment_status` != :ps',
            array(
                ':ps' => 'paid',
            )
        );
        if(!empty($orders)){
            // способы оплаты
            $payment_methods = StorePaymentMethod::model()->active()->findAll();
            $payment_methods = (!empty($payment_methods)) ? CArray::toolIndexArrayBy($payment_methods, 'id') : array();
            // обрабатываем найденные заказы
            foreach ($orders as $order){
                // WayForPay
                if(!empty($payment_methods[$order->payment_id])
                    && (strtolower($payment_methods[$order->payment_id]->name) === 'wayforpay'))
                {
                    $this->wfpProceed($order);
                }
                // Portmone
                if(!empty($payment_methods[$order->payment_id])
                    && (strtolower($payment_methods[$order->payment_id]->name) === 'portmone'))
                {
                    $this->portmoneProceed($order);
                }
            }
        }
    }

    /**
     * работаем с WayForPay
     * @param object $model - модель заказа
     */
    public function wfpProceed($model)
    {
        // получаем orderReference для этого заказа
        $order_ref_command = Yii::app()->db
            ->createCommand("SELECT `orderReference` FROM `WfpOrder` WHERE `order_id` = " . (int)$model->id);
        $order_ref = $order_ref_command->queryScalar();
        if(!empty($order_ref)){
            // запрашиваем и обновляем статус платежа по этому заказу
            $transactionStatus = $this->getWfpStatus($order_ref);
            if(!empty($transactionStatus)){
                // формируем переменные для отображения страницы
                $payment_statuses = $this->paymentResponseStatus('wayforpay');
                if(!empty($payment_statuses[$transactionStatus]['status'])){
                    // обновляем статус платежа в самом заказе
                    // не обновляем – если у заказа уже стоит статус paid
                    if(($model->payment_status != 'paid') && ($model->payment_status != $payment_statuses[$transactionStatus]['status'])){
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
                    }
                }
            }
        }
    }

    /**
     * работаем с Portmone
     * @param object $model - модель заказа
     */
    public function portmoneProceed($model)
    {
        // запрашиваем и обновляем статус платежа по этому заказу
        $transactionStatus = $this->portmonePayment($model->id);
        if(!empty($transactionStatus)){
            // формируем переменные для отображения страницы
            $payment_statuses = $this->paymentResponseStatus('portmone');
            if(!empty($payment_statuses[$transactionStatus]['status'])){
                // обновляем статус платежа в самом заказе
                // не обновляем – если у заказа уже стоит статус paid
                if(($model->payment_status != 'paid') && ($model->payment_status != $payment_statuses[$transactionStatus]['status'])){
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
                }
            }
        }
    }

    /**
     * Получаем и обновляем статус заказа в системе WayForPay
     * @param $order_ref orderReference заказа в системе WayForPay
     * @return bool|string
     */
    public function getWfpStatus($order_ref)
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
                    return (!empty($response['transactionStatus'])) ? $response['transactionStatus'] : '';
                }
            }
        }
        return '';
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
        return (!empty($order_data['status'])) ? $order_data['status'] : '';
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

    public function actionSetStatus()
    {
        $ids       = Yii::app()->request->getPost('ids');
        $status    = (int)Yii::app()->request->getPost('status');
        $models    = Order::model()->findAllByPk($ids);
        foreach($models as $order)
        {
            $order->status_id = $status;
            $order->save();
        }
        echo Yii::t('StoreModule.admin', 'Изменения успешно сохранены.');
    }

    public function actionRemoveDeliveryPhoto()
    {
        $id = Yii::app()->request->getPost('id');
        $photo = OrderPhoto::model()->findByPk($id);
        if(!empty($photo)){
            @unlink(Yii::getPathOfAlias('webroot') . '/uploads/delivery/' . $photo->photo);
            $photo->delete();
            echo 1; return;
        }
        echo 0;
    }
}