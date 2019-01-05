<?php

/**
 * This is the model class for table "wfp_response_detail".
 *
 * The followings are the available columns in table 'wfp_response_detail':
 * @property string $id
 * @property string $response_ts
 * @property string $response_body
 * @property string $orderReference
 * @property integer $order_id
 * @property string $response_data
 */
class WfpResponseDetail extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'wfp_response_detail';
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
			array('orderReference', 'length', 'max'=>255),
			array('response_ts, response_body, response_data', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, response_ts, response_body, orderReference, order_id, response_data', 'safe', 'on'=>'search'),
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
			'response_ts' => 'Response Ts',
			'response_body' => 'Response Body',
			'orderReference' => 'Order Reference',
			'order_id' => 'Order',
			'response_data' => 'Response Data',
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
		$criteria->compare('response_ts',$this->response_ts,true);
		$criteria->compare('response_body',$this->response_body,true);
		$criteria->compare('orderReference',$this->orderReference,true);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('response_data',$this->response_data,true);

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
	 * @return WfpResponseDetail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
