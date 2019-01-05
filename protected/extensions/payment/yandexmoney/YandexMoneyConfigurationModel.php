<?php

/**
 * Class YandexMoneyConfigurationModel
 */
class YandexMoneyConfigurationModel extends CModel
{

	/**
	 * @var integer YandexMoney account number
	 */
	public $uid;

	/**
	 * @var string YandexMoney secret word
	 */
	public $password;

	/**
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('uid, password', 'type', 'type'=>'string')
		);
	}

	/**
	 * @return array
	 */
	public function attributeNames()
	{
		return array(
			'uid'      => Yii::t('YandexMoneyPaymentSystem', 'Номер счета в Яндекс.Деньгах'),
			'password' => Yii::t('YandexMoneyPaymentSystem', 'Секретное слово'),
		);
	}

	/**
	 * @return array
	 */
	public function getFormConfigArray()
	{
		$id = Yii::app()->request->getQuery('payment_method_id');

		return array(
			'type'=>'form',
			'elements'=>array(
				'uid'=>array(
					'label' => Yii::t('YandexMoneyPaymentSystem', 'Номер счета в Яндекс.Деньгах'),
					'type'  => 'text'
				),
				'password'=>array(
					'label' => Yii::t('YandexMoneyPaymentSystem', 'Секретное слово'),
					'type'  => 'text',
				),
				'<div class="row">
					<label>Адрес, на который получать уведомления</label>
					<input type="text" value="'.$this->getCallbackUrl($id).'" disabled>
					<div class="hint">
						Сохраните этот адрес на странице <a href="https://sp-money.yandex.ru/myservices/online.xml" target="_blank">HTTP-уведомления</a>
					</div>
				</div>'
		));
	}

	/**
	 * Builds full url to accept callback requests.
	 *
	 * @param $id
	 * @return string
	 */
	public function getCallbackUrl($id)
	{
		if($id==='undefined')
			return 'Для получениия ссылки нужно сохранить запись.';

		return Yii::app()->createAbsoluteUrl('/orders/payment/process', array('payment_id'=>$id));
	}
}
