<?php

/**
 * This is the model class for table "OrderPhoto".
 *
 * The followings are the available columns in table 'OrderPhoto':
 * @property integer $id
 * @property integer $product_id
 * @property string $photo
 *
 * The followings are the available model relations:
 * @property OrderPhotoProducts[] $orderPhotoProducts
 */
class OrderPhoto extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'OrderPhoto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public $photo;
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('product_id, photo', 'required'),
			array('product_id', 'numerical', 'integerOnly'=>true),
            array('photo', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, product_id, photo', 'safe', 'on'=>'search'),
			
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
			'orderPhotoProducts' => array(self::HAS_MANY, 'OrderPhotoProducts', 'product_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'product_id' => 'Product',
			'photo' => 'Photo',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('product_id',$this->product_id);
		$criteria->compare('photo',$this->photo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function getPhotos($id)
	{
		$photos = Yii::app()->db->createCommand()
		    ->select('id, photo')
		    ->from('OrderPhoto')
		    ->where('order_id=:order_id', array(':order_id'=>$id))
		    ->queryAll();
		    return (!empty($photos)) ? CArray::toolIndexArrayBy($photos, 'id') : array();
		
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderPhoto the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
