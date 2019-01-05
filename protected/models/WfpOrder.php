<?php

/**
 * This is the model class for table "WfpOrder".
 *
 * The followings are the available columns in table 'WfpOrder':
 * @property string $id
 * @property integer $order_id
 * @property string $orderReference
 * @property string $reason
 * @property integer $reasonCode
 * @property double $amount
 * @property string $currency
 * @property string $createdDate
 * @property string $processingDate
 * @property string $cardPan
 * @property string $cardType
 * @property string $issuerBankCountry
 * @property string $issuerBankName
 * @property string $transactionStatus
 * @property string $authCode
 * @property double $refundAmount
 * @property string $settlementDate
 * @property double $settlementAmount
 * @property double $fee
 * @property string $merchantSignature
 */
class WfpOrder extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'WfpOrder';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, reasonCode', 'numerical', 'integerOnly'=>true),
			array('amount, refundAmount, settlementAmount, fee', 'numerical'),
			array('orderReference, cardType', 'length', 'max'=>20),
			array('reason, cardPan, issuerBankCountry, issuerBankName, transactionStatus, authCode, merchantSignature', 'length', 'max'=>255),
			array('currency', 'length', 'max'=>10),
			array('settlementDate', 'length', 'max'=>50),
			array('createdDate, processingDate', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, orderReference, reason, reasonCode, amount, currency, createdDate, processingDate, cardPan, cardType, issuerBankCountry, issuerBankName, transactionStatus, authCode, refundAmount, settlementDate, settlementAmount, fee, merchantSignature', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'order_id' => 'Order',
			'orderReference' => 'Order Reference',
			'reason' => 'Reason',
			'reasonCode' => 'Reason Code',
			'amount' => 'Amount',
			'currency' => 'Currency',
			'createdDate' => 'Created Date',
			'processingDate' => 'Processing Date',
			'cardPan' => 'Card Pan',
			'cardType' => 'Card Type',
			'issuerBankCountry' => 'Issuer Bank Country',
			'issuerBankName' => 'Issuer Bank Name',
			'transactionStatus' => 'Transaction Status',
			'authCode' => 'Auth Code',
			'refundAmount' => 'Refund Amount',
			'settlementDate' => 'Settlement Date',
			'settlementAmount' => 'Settlement Amount',
			'fee' => 'Fee',
			'merchantSignature' => 'Merchant Signature',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('orderReference',$this->orderReference,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('reasonCode',$this->reasonCode);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('currency',$this->currency,true);
		$criteria->compare('createdDate',$this->createdDate,true);
		$criteria->compare('processingDate',$this->processingDate,true);
		$criteria->compare('cardPan',$this->cardPan,true);
		$criteria->compare('cardType',$this->cardType,true);
		$criteria->compare('issuerBankCountry',$this->issuerBankCountry,true);
		$criteria->compare('issuerBankName',$this->issuerBankName,true);
		$criteria->compare('transactionStatus',$this->transactionStatus,true);
		$criteria->compare('authCode',$this->authCode,true);
		$criteria->compare('refundAmount',$this->refundAmount);
		$criteria->compare('settlementDate',$this->settlementDate,true);
		$criteria->compare('settlementAmount',$this->settlementAmount);
		$criteria->compare('fee',$this->fee);
		$criteria->compare('merchantSignature',$this->merchantSignature,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WfpOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
