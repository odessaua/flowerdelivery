<?php

Yii::import('application.modules.comments.models.Comment');
Yii::import('application.modules.orders.models.*');
Yii::import('application.modules.store.models.*');

class SiteController extends Controller
{
    public $wfp_statuses = array(
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
    );
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

	public function actionIndex()
	{
	}
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', array('error'=>$error));
		}
	}
	
	public function actionAutocompleteCity() 
	{	
		$res =array();
		$lang= Yii::app()->language;
        $langs = array(
            'ru' => 1,
            'en' => 9,
            'ua' => 10,
            'uk' => 10,
        );
		if (isset($_GET['term'])) {
			$qtxt="";
			if(in_array($lang, array_keys($langs))){
                $qtxt ="SELECT object_id, name FROM cityTranslate WHERE name LIKE :name AND language_id=" . (int)$langs[$lang];
            }
			else
				$qtxt ="SELECT id as object_id, name FROM city WHERE name LIKE :name";
			// echo $lang;
			$command =Yii::app()->db->createCommand($qtxt);
			$command->bindValue(":name", '%'.$_GET['term'].'%', PDO::PARAM_STR);
			//$res =$command->queryColumn();
			$results =$command->queryAll();

			// поиск по алиасам
            if(empty($results) && in_array($lang, array_keys($langs))){
                $qs = "SELECT ca.object_id, ct.name 
                        FROM cityAlias ca 
                        JOIN cityTranslate ct ON ct.object_id = ca.object_id AND ct.language_id = " . (int)$langs[$lang] . "
                        WHERE ca.name LIKE :name AND ca.language_id=" . (int)$langs[$lang];
                $command =Yii::app()->db->createCommand($qs);
                $command->bindValue(":name", '%'.$_GET['term'].'%', PDO::PARAM_STR);
                $results = $command->queryAll();
            }
		}
        if(!empty($results)){
            $ids = array();
            foreach ($results as $row) {
                $ids[] = $row['object_id'];
            }
            if(!empty($ids)){
                $sql_lang = (!empty($langs[Yii::app()->language])) ? " AND rt.language_id = " . $langs[Yii::app()->language] : "";
                $sql = "SELECT c.id, c.region_id, rt.name as rt_name, r.name
                        FROM city c
                        LEFT JOIN regionTranslate rt ON rt.object_id = c.region_id " . $sql_lang . "
                        LEFT JOIN region r ON r.id = c.region_id
                        WHERE c.id IN (" . implode(",", $ids) . ")";
                $comm = Yii::app()->db->createCommand($sql);
                $rr = $comm->queryAll();
                if(!empty($rr)){
                    $rr = CArray::toolIndexArrayBy($rr, 'id');
                    foreach ($results as $r_row) {
                        $name = (!empty($rr[$r_row['object_id']]['rt_name'])) ? $rr[$r_row['object_id']]['rt_name'] : $rr[$r_row['object_id']]['name'];
                        $res[] = $r_row['name'] . ((!empty($name)) ? ' (' . $name . ')' : '');
                    }
                }
            }
        }
        // echo $command;
		echo CJSON::encode($res);
		Yii::app()->end();
	}
	
	public function actionChangeCity($city, $lang = 'en')
	{
		$app = Yii::app();
		
		if(!empty($city))
		{
            $city_ex = explode(' (', $city); // for entries like Ужгород (Zakarpattia Region)
            // получаем object_id города
            $trans_sql = "SELECT `object_id` FROM `cityTranslate` WHERE `name` = :name";
            $trans_command =Yii::app()->db->createCommand($trans_sql);
            $trans_command->bindValue(":name", $city_ex[0], PDO::PARAM_STR);
            $trans_res = $trans_command->queryScalar();
            // получаем название города из `city`
            $city_sql = "SELECT `name` FROM `cityTranslate` WHERE `object_id` = " . (int)$trans_res . " AND `language_id` = 9";
            $city_command = Yii::app()->db->createCommand($city_sql);
            $city_res = $city_command->queryScalar();
            $city_url = strtolower($city_res);
            $delivery_sql = "SELECT `delivery` FROM `city` WHERE `id` = " . (int)$trans_res;
            $delivery_command = Yii::app()->db->createCommand($delivery_sql);
            $delivery_res = $delivery_command->queryScalar();
            if(!empty($trans_res) && ($delivery_res !== false)){
                $app->session['_city'] = $trans_res; // теперь храним ID города
                $app->session['_cityName'] = $city_ex[0]; // название города
                $app->session['_delivery_price'] = $delivery_res; // стоимость доставки в этот город
            }
            else{
                // город по умолчанию
                $cityInfo = $this->getDefaultCityInfo(true);
                $app->session['_city'] =  $cityInfo->id; // теперь храним ID города
                $app->session['_cityName'] = $cityInfo->name; // название города
                $app->session['_delivery_price'] = $cityInfo->delivery; // стоимость доставки в этот город
            }
		}else{
            // город по умолчанию
		    $cityInfo = $this->getDefaultCityInfo(true);
			$app->session['_city'] =  $cityInfo->id; // теперь храним ID города
            $app->session['_cityName'] = $cityInfo->name; // название города
            $app->session['_delivery_price'] = $cityInfo->delivery; // стоимость доставки в этот город
		}

		$url_lang = ($lang !== $app->params['defaultLanguage']) ? $lang . '/' : '';
		echo $app->session['_cityName'] . ((!empty($city_url)) ? '_/' . $url_lang . $city_url : '');
		//Yii::app()->controller->refresh();
	}
	
	public function actionSetPaymentId()
	{
		$payment_id = $_GET['payment_id'];
		$order_id = $_GET['order_id'];
		
		$sql = "UPDATE `Order` SET payment_id = :payment_id WHERE id = :order_id";
		$command =Yii::app()->db->createCommand($sql);
		$command->bindValue(":payment_id", $payment_id, PDO::PARAM_INT);
		$command->bindValue(":order_id", $order_id, PDO::PARAM_INT);
		$command->query();
	}

    /**
     * страница отзывов
     */
	public function actionReviews()
	{
        $comment = new Comment;
        if(Yii::app()->request->isPostRequest)
        {
            $comment->attributes = Yii::app()->request->getPost('Comment');

            if(!Yii::app()->user->isGuest)
            {
                $comment->name = Yii::app()->user->name;
                $comment->email = Yii::app()->user->email;
            }

            if($comment->validate())
            {
                $comment->class_name = 'application.modules.store.models.StoreProduct';
                $comment->object_pk = 0;
                $comment->user_id = Yii::app()->user->isGuest ? 0 : Yii::app()->user->id;
                $comment->save();

                $url = Yii::app()->getRequest()->getUrl();

                if($comment->status==Comment::STATUS_WAITING)
                {
                    $url.='#';
                    Yii::app()->user->setFlash('messages', Yii::t('CommentsModule.core', 'Thank you for reviewing our website.'));
                }
                elseif($comment->status==Comment::STATUS_APPROVED)
                    $url.='#comment_'.$comment->id;

                // Refresh page
                Yii::app()->request->redirect($url, true);
            }
        }
        $comments = Comment::model()->approved()->orderByCreatedDesc()->findAll();
		Yii::import('application.modules.pages.models.Page');
		$page = Page::model()->find('url = :url', array(':url' => 'reviews'));
        if(!empty($page)){
            $this->pageTitle = $page->meta_title;
            $this->pageKeywords = $page->meta_keywords;
            $this->pageDescription = $page->meta_description;
			$this->pageShortdescription = $page->short_description;
        }
        $this->render('comments.views.comment.create', array(
            'comment' => $comment,
            'comments' => $comments,
            'reviews' => true,
            'model' => StoreProduct::model(),
        ));
		
		
	}

    /**
     * получение от WFP запросов при изменении статуса платежа
     * TODO: отправка ответа серверу при получении статуса платежа = paid
     */
    public function actionWfpResponse()
    {
        $json = file_get_contents('php://input');
        $ts = date('Y-m-d H:i:s');
        if(!empty($json)){
            $obj = json_decode($json, TRUE);
            // проверка статуса платежа, корректировка статуса платежа в таблице заказов и в логах
            if(!empty($obj['orderReference']) && !empty($obj['transactionStatus'])){
                $orderReference_ex = explode('_', $obj['orderReference']);
                $order = Order::model()->findByPk($orderReference_ex[1]);
                if(
                    !empty($order) && // найден заказ
                    ($order->payment_status != 'paid') && // его статус отличен от «paid»
                    !empty($this->wfp_statuses[$obj['transactionStatus']]['status']) // в запросе от WFP указан статус транзакции
                ){
                    // старый статус платежа
                    $from_status = $order->payment_status;
                    // новый статус платежа
                    $order->payment_status = $this->wfp_statuses[$obj['transactionStatus']]['status'];
                    // статус заказа: оплачен или старый
                    $order->status_id = ($this->wfp_statuses[$obj['transactionStatus']]['status'] == 'paid')
                        ? 6
                        : $order->status_id;
                    $order->save(); // обновили статус платежа в таблице заказов
                    // добавляем запись в таблицу логов статусов платежей
                    $this->savePaymentStatusLog(
                        $orderReference_ex[1],
                        'wayforpay',
                        $this->wfp_statuses[$obj['transactionStatus']]['status'],
                        (string)$json,
                        serialize($obj)
                    );
                }
                // сохраняем детальный запрос wfp
                $this->saveDetailWfpResponse(
                    (string)$json,
                    $ts,
                    $obj['orderReference'],
                    $orderReference_ex[1],
                    serialize($obj));
            }
            // сохраняем чистый запрос wfp
            $this->saveOriginWfpResponse((string)$json, $ts);
        }
    }

    /**
     * сохраняем чистый запрос wfp
     * @param $response
     * @param $ts
     */
    public function saveOriginWfpResponse($response, $ts)
    {
        $model = new WfpResponse();
        $model->response_ts = $ts;
        $model->response_body = $response;
        $model->save();
    }

    /**
     * сохраняем детальный запрос wfp
     * @param $response
     * @param $ts
     * @param $orderReference
     * @param $order_id
     * @param $data
     */
    public function saveDetailWfpResponse($response, $ts, $orderReference, $order_id, $data)
    {
        $model = new WfpResponseDetail();
        $model->response_ts = $ts;
        $model->response_body = $response;
        $model->orderReference = $orderReference;
        $model->order_id = $order_id;
        $model->response_data = $data;
        $model->save();
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
     * Получаем и обновляем статус заказа в системе WayForPay
     * @param $order_ref orderReference заказа в системе WayForPay
     * @return bool
     */
    public function actionWfpStatus($order_ref)
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
			//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $out = curl_exec($curl);//var_dump($out);
            curl_close($curl);
            if(CJsn::isJson($out)){
                //var_dump(json_decode($out, true));
                $response = json_decode($out, true);
                $wfp_order = WfpOrder::model()->findByAttributes(array('order_id' => $order_id));
                if(!empty($wfp_order) && !empty($response['orderReference'])){
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
     * Сохраняем ID заказа и orderReference в БД
     */
    public function actionWfpOrder()
    {
        if(!empty($_POST)){
            $orderReference = Yii::app()->request->getPost('orderReference', '');
            if(!empty($orderReference)){
                $order_ex = explode('_', $orderReference);
                $order_id = end($order_ex);
//                $wfp_order = WfpOrder::model()->findByAttributes(array('order_id' => $order_id));
//                if(!empty($wfp_order)) return; // сохраняем первую запись о заказе, с оригинальным order_reference
                //WfpOrder::model()->deleteAllByAttributes(array('order_id' => $order_id));
                $model = new WfpOrder();
                $model->order_id = $order_id;
                $model->orderReference = $orderReference;
                $model->save();
            }
        }
    }

    public function actionCities()
    {
        $region_id = Yii::app()->request->getPost('region_id', 0);
        $language_id = Yii::app()->request->getPost('language_id', 9);
        $language_code = Yii::app()->request->getPost('language_code', '');
        $data['language_code'] = (!empty($language_code) && ($language_code != 'en'))
            ? '/' . $language_code
            : '';
        $data['no_redirect'] = Yii::app()->request->getPost('no_redirect');
        $data['cities'] = Yii::app()->db->createCommand()
            ->select('c.name as ename,ct.name,ct.object_id,c.id,ct.language_id,ctt.name as eng_name, c.main_in_region')
            ->from('city c')
            ->join('cityTranslate ct', 'c.id=ct.object_id')
            ->join('cityTranslate ctt', 'c.id=ctt.object_id')
            ->where('ct.language_id=:id', array(':id'=>$language_id))
            ->andWhere('c.region_id=:region_id', array(':region_id' => $region_id))
            ->andWhere('ctt.language_id=:eid', array(':eid'=>9))
            ->order('c.main_in_region DESC, ct.name ASC, id DESC')
            ->queryAll();
        $html = $this->renderPartial('pages.views.pages._cities', $data, true);
        echo $html;
    }
}