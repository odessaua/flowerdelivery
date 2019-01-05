<?php

Yii::import('application.modules.accounting1c.components.C1AbstractImport');
Yii::import('application.modules.accounting1c.components.C1ExternalFinder');
Yii::import('application.modules.orders.models.Order');
Yii::import('application.modules.orders.models.OrderProduct');

class C1Orders extends C1AbstractImport
{

	private $_STATUS_ACCEPTED  = 1;
	private $_STATUS_DELIVERED = 2;
	private $_STATUS_CANCELED  = 3;

	/**
	 * Handles 1c requests.
	 */
	public static function processRequest($mode)
	{
		$method = 'command'.ucfirst($mode);
		$class = new self;

		if(method_exists($class, $method))
			$class->$method();
		else
			throw new Exception('Unsupported command');
	}

	/**
	 * Called if 1C accepted all orders.
	 */
	public function commandSuccess()
	{
		$this->saveExportTime();
		echo 'ok';
	}

	public function commandQuery()
	{
		header("Content-type: text/xml; charset=utf-8");
		// Add byte order mask
		echo "\xEF\xBB\xBF";

		$writer = new XMLWriter;
		$writer->openURI('php://output');
		$writer->startDocument('1.0','utf-8');
		$writer->setIndent(4);

		$writer->startElement('КоммерческаяИнформация');
			$writer->writeAttribute('ВерсияСхемы', '2.04');
			$writer->writeAttribute('ДатаФормирования', date('Y-m-d' ));

		/** var $order Order */
		foreach($this->loadOrders() as $order)
		{
			$date = new DateTime($order->created);

			$writer->startElement('Документ');
				$writer->writeElement('Ид', $order->id);
				$writer->writeElement('Номер', $order->id);
				$writer->writeElement('ХозОперация', 'Заказ товара');
				$writer->writeElement('Роль', 'Продавец');
				$writer->writeElement('Курс', '1');
				$writer->writeElement('Сумма', $order->full_price);
				$writer->writeElement('Дата', $date->format('Y-m-d'));
				$writer->writeElement('Время',  $date->format('H:i:s'));
				$writer->writeElement('Комментарий', $order->user_comment);
				$writer->startElement('Контрагенты');
					$writer->startElement('Контрагент');
						$writer->writeElement('Ид', $order->user_name);
						$writer->writeElement('Наименование', $order->user_name);
						$writer->writeElement('Роль', 'Покупатель');
						$writer->writeElement('ПолноеНаименование', $order->user_name);
						$writer->startElement('АдресРегистрации');
							$writer->writeElement('Представление', $order->user_address);
							$writer->startElement('АдресноеПоле');
								$writer->writeElement('Тип', 'Страна');
								$writer->writeElement('Значение', 'RU');
							$writer->endElement();
							$writer->startElement('АдресноеПоле');
								$writer->writeElement('Тип', 'Регион');
								$writer->writeElement('Значение', $order->user_address);
							$writer->endElement();
						$writer->endElement();
						$writer->startElement('Контакты');
							$writer->startElement('Контакт');
								$writer->writeElement('Тип', 'Телефон');
								$writer->writeElement('Значение', $order->user_phone);
							$writer->endElement();
							$writer->startElement('Контакт');
								$writer->writeElement('Тип', 'Почта');
								$writer->writeElement('Значение', $order->user_email);
							$writer->endElement();
						$writer->endElement();
					$writer->endElement();
				$writer->endElement();

				$writer->startElement('Товары');
				foreach($order->products as $p)
				{
					$writer->startElement('Товар');
						$writer->writeElement('Ид', $this->getProductExternalId($p->product_id));
						$writer->writeElement('Артикул', $p->sku);
						$writer->writeElement('Наименование', $p->renderFullName);
						$writer->writeElement('ЦенаЗаЕдиницу', $p->price);
						$writer->writeElement('Количество', $p->quantity);
						$writer->writeElement('Сумма', $p->getTotalPrice());
						$writer->startElement('ЗначенияРеквизитов');
							$writer->startElement('ЗначенияРеквизита');
								$writer->writeElement('Наименование', 'ВидНоменклатуры');
								$writer->writeElement('Значение', 'Товар');
							$writer->endElement();
							$writer->startElement('ЗначенияРеквизита');
								$writer->writeElement('Наименование', 'ТипНоменклатуры');
								$writer->writeElement('Значение', 'Товар');
							$writer->endElement();
						$writer->endElement();
					$writer->endElement();
				}
				$writer->endElement();

				if($order->delivery_price > 0)
				{
					$writer->startElement('Товар');
						$writer->writeElement('Ид', 'ORDER_DELIVERY');
						$writer->writeElement('Наименование', 'Доставка');
						$writer->writeElement('ЦенаЗаЕдиницу', $order->delivery_price);
						$writer->writeElement('Количество', 1 );
						$writer->writeElement('Сумма', $order->delivery_price);
						$writer->startElement('ЗначенияРеквизитов');
							$writer->startElement('ЗначенияРеквизита');
								$writer->writeElement('Наименование', 'ВидНоменклатуры');
								$writer->writeElement('Значение', 'Услуга');
							$writer->endElement();
							$writer->startElement('ЗначенияРеквизита');
								$writer->writeElement('Наименование', 'ТипНоменклатуры');
								$writer->writeElement('Значение', 'Услуга');
							$writer->endElement();
						$writer->endElement();
					$writer->endElement();
				}

				$writer->startElement('ЗначенияРеквизитов');
					$writer->startElement('ЗначенияРеквизита');
						if($order->status_id == $this->_STATUS_ACCEPTED)
						{
							$writer->writeElement('Наименование', 'Статус заказа');
							$writer->writeElement('Значение', '[N] Принят');
						}
						elseif($order->status_id == $this->_STATUS_DELIVERED)
						{
							$writer->writeElement('Наименование', 'Статус заказа');
							$writer->writeElement('Значение', '[F] Доставлен');
						}
						elseif($order->status_id == $this->_STATUS_CANCELED)
						{
							$writer->writeElement('Наименование', 'Отменен');
							$writer->writeElement('Значение', 'true');
						}
					$writer->endElement();
				$writer->endElement();

			$writer->endElement();
		}

		$writer->endElement();
		$writer->endDocument();
		$writer->flush();
	}

	/**
	 * Save order file and execute import.
	 */
	public function commandFile()
	{
		$xml = simplexml_load_string(file_get_contents('php://input'));
		$this->importOrder($xml);

		echo "success\n";
	}

	public function importOrder(SimpleXMLElement $xml)
	{
		foreach($xml->{'Документ'} as $xmlOrder)
		{
			$order = $this->getOrder($xmlOrder->{'Номер'});

			$order->created   = $xmlOrder->{'Дата'}.' '.$xmlOrder->{'Время'};
			$order->user_name = $xmlOrder->{'Контрагенты'}->{'Контрагент'}->{'Наименование'};

			$deleted   = false;
			$delivered = false;

			if(isset($xmlOrder->{'ЗначенияРеквизитов'}->{'ЗначениеРеквизита'}))
			{
				foreach($xmlOrder->{'ЗначенияРеквизитов'}->{'ЗначениеРеквизита'} as $r)
				{
					if($r->{'Наименование'} == 'ПометкаУдаления')
						$deleted = $r->{'Значение'} == 'true';
					if($r->{'Наименование'} == 'Проведен')
						$delivered = $r->{'Значение'} == 'true';
				}
			}

			// Handle status
			if($deleted)
				$order->status_id = $this->_STATUS_CANCELED;
			elseif($delivered)
				$order->status_id = $this->_STATUS_DELIVERED;
			elseif(!$delivered)
				$order->status_id = $this->_STATUS_ACCEPTED;

			$order->save(false);

			foreach($xmlOrder->{'Товары'}->{'Товар'} as $xmlProduct)
			{
				// Skip delivery
				if($xmlProduct->{'Наименование'} == 'Доставка')
					continue;

				// Find original product by external id
				$originalProduct = C1ExternalFinder::getObject(C1ExternalFinder::OBJECT_TYPE_PRODUCT, $xmlProduct->{'Ид'}, true);
				if($originalProduct)
					$originalProductId = $originalProduct->id;
				else
					$originalProductId = 0;

				$orderProduct = new OrderProduct;

				// TODO: Refactor here. Move logic to model.
				foreach($order->products as $op)
				{
					if($op->product_id == $originalProductId)
						$orderProduct = $op;
				}

				$orderProduct->order_id        = $order->id;
				$orderProduct->product_id      = $originalProductId;
				$orderProduct->configurable_id = 0;
				$orderProduct->name            = $xmlProduct->{'Наименование'};
				$orderProduct->price           = (float)$xmlProduct->{'ЦенаЗаЕдиницу'};
				$orderProduct->quantity        = $xmlProduct->{'Количество'};
				$orderProduct->sku             = $xmlProduct->{'Артикул'};

				$orderProduct->save(false);
			}
		}
	}

	/**
	 * Load orders created during last export.
	 */
	public function loadOrders()
	{
		$cr = new CDbCriteria;
		$cr->addCondition('t.created > "'.$this->getExportTime().'"');

		return Order::model()->findAll($cr);
	}

	/**
	 * Find order by id. If not found - return new instance.
	 *
	 * @param $id
	 * @return Order
	 */
	public function getOrder($id)
	{
		$model = Order::model()->findByPk($id);

		if(!$model)
		{
			$model = new Order;
			$model->id = $id;
		}

		return $model;
	}

	public function getProductExternalId($product_id)
	{
		$query = Yii::app()->db->createCommand()
			->select('*')
			->from('accounting1c')
			->where('object_type=:type AND object_id=:object_id', array(
				':type'      => C1ExternalFinder::OBJECT_TYPE_PRODUCT,
				':object_id' => $product_id
			))
			->limit(1)
			->queryRow();

		if(!$query)
			return $product_id;

		return $query['external_id'];
	}

	public function getExportTime()
	{
		return Yii::app()->settings->get(__CLASS__, 'export_time', 0);
	}

	/**
	 * Saves time of last orders export to 1c.
	 */
	public function saveExportTime()
	{
		Yii::app()->settings->set(__CLASS__, array(
			'export_time'=>date('Y-m-d H:i:s')
		));
	}
}
