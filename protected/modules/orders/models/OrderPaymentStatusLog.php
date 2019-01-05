<?php

/**
 * This is the model class for table "OrderPaymentStatusLog".
 *
 * The followings are the available columns in table 'OrderPaymentStatusLog':
 * @property string $id
 * @property integer $order_id
 * @property string $payment_type
 * @property string $status
 * @property string $response_orig
 * @property string $response_used
 * @property string $response_date
 */
class OrderPaymentStatusLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'OrderPaymentStatusLog';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id', 'numerical', 'integerOnly'=>true),
			array('payment_type, status', 'length', 'max'=>255),
			array('response_orig, response_used, response_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, order_id, payment_type, status, response_date', 'safe', 'on'=>'search'),
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
			'payment_type' => 'Payment Type',
			'status' => 'Status',
			'response_orig' => 'Response Orig',
			'response_used' => 'Response Used',
			'response_date' => 'Response Date',
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
		$criteria->compare('payment_type',$this->payment_type,true);
		$criteria->compare('status',$this->status,true);
//		$criteria->compare('response_orig',$this->response_orig,true);
//		$criteria->compare('response_used',$this->response_used,true);
		$criteria->compare('response_date',$this->response_date,true);

        $sort=new CSort;
        $sort->defaultOrder = $this->getTableAlias().'.id DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort'=>$sort,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderPaymentStatusLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
