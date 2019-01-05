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
   		echo "Заказ №: ".$model->id." в город ".$model->receiver_city."\n";
		echo "Дата доставки: ".$model->datetime_del."\n";
   		echo "Сумма заказа: ".$model->total_price."\n\n";
		echo "Информация о получателе \n\n";
		echo "Город: ".$model->receiver_city."\n";
		echo "Имя: ".$model->receiver_name."\n";
		echo "Адрес: ".$model->user_address."\n";
		echo "Телефон моб: ".$model->phone1."\n";
   		echo "Телефон дом: ".$model->phone2."\n";
		echo "Дополнительная информация: ".$model->user_comment."\n";
		echo "Текст открытки: ".$model->card_text."\n\n";
		echo "Информация о заказчике \n\n";
   		echo "Имя: ".$model->user_name."\n";
   		echo "Email: ".$model->user_email."\n";
		echo "Страна: ".$model->country."\n";
		echo "Город :".$model->city."\n";
   		echo "Адрес: ".$model->user_address."\n";
   		echo "Телефон: ".$model->user_phone."\n\n";
		echo "Информация о Заказе \n\n";		
		
		   		foreach ($products as $one) {
				$variants = unserialize($one['variants']);
				
		if(!empty($variants)) {	foreach($variants as $key=>$value)	$result .= "<br/> - {$key}: {$value}"; }
				
   		echo "Название: ".$one['name']."(".$result.")"."\n Количество:".$one['quantity']."\n Стоимость:".$one['price']."\n";
					}
		if ($model->doPhoto==1)
		echo "Сделать фото заказа\n";
		echo "Состояние заказ можно посмотреть здесь: url:https://7roses.com/cart/view/".$model->secret_key."\n\n";
   		echo "Создан :".$model->created."\n";
   		echo "Скидка :".$model->discount."\n";
		echo "Payment id :".$model->payment_id."\n";
   		echo "Комментарий Админа:".$model->admin_comment."\n";   		
   		echo "Стоимость доставки :".$model->delivery_price."\n";
   		echo "Оплачен: ".$model->paid."\n";
   		
	}
	//Get an array with geoip-infodata
       public function getGeoIpInfo($ipAddress)
       {
       	
		    //$ip_key = "9a531e5be48d22f2df5d421eafbb87c2b376206e7314174e7e7c131104e44dae";
		    // var_dump($ipAddress);
		    //$query = "http://api.ipinfodb.com/v3/ip-city/?key=" . $ip_key . "&ip=" . $ipAddress . "&format=json";
	//$query = freegeoip.net/json/$ipAddress;
		    $json = file_get_contents($query);

		    $data = json_decode($json, true);
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
		$card_transl=StoreDeliveryMethod::model()->findByAttributes(array('id'=>19))['price'];
		$citys=City::model()->findAll();
		$photos=array();
		if(isset($getPhotos)){
			foreach ($getPhotos as $key=>$value) {
				$photos[$key]="/uploads/".$value['photo'];
			}
		}
		if(Yii::app()->request->isPostRequest)
		{

			$model->attributes = $_POST['Order'];
			// var_dump( $_POST['Order']);die;
			$model->total_price+=isset($model->photo_price)?$model->photo_price:0;
			$model->total_price+=isset($model->card_price)?$model->card_price:0;
			$model->total_price+=isset($model->card_price)?$model->card_transl:0;
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
				if(!empty($images[$i]))
					$names[$i]=$model->getOrderedProducts()->getData()[$i]->product_id;
			}

			$rand=rand(0,9999);
            if (isset($images)) {
 				

                // go through each uploaded image
                for ($i=0;$i<count($images);$i++) {
                	if (empty($names[$i])) {
                		continue;
                	}
                	else{
	                    if ($images[$i][0]->saveAs(Yii::getPathOfAlias('webroot').'/uploads/'.$rand.$images[$i][0]->name)) {
	                       	
	                       	$check=OrderPhoto::model()->findByAttributes(array('order_id'=>$model->id,'product_id'=>$names[$i]));
	                       
	                       	if($check){
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

				$model->save();
				if(sizeof(Yii::app()->request->getPost('quantity', array())))
					$model->setProductQuantities(Yii::app()->request->getPost('quantity'));

				$model->updateDeliveryPrice();
				$model->updateTotalPrice();

				$this->setFlashMessage(Yii::t('OrdersModule.admin', 'Изменения успешно сохранены'));

				if(isset($_POST['REDIRECT']))
					$this->smartRedirect($model);
				else
					$this->redirect(array('index'));
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
}
