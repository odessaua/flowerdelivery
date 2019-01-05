<?php

/**
 * This is the model class for table "region".
 *
 * The followings are the available columns in table 'region':
 * @property integer $id
 * @property string $name
 */
Yii::import('application.modules.store.models.*');
class Region extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
    public $translateModelName = 'RegionTranslate';
    public $name;
	public function tableName()
	{
		return 'region';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
            'translate' => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
		);
	}

    public function behaviors()
    {
        return array(
            'eavAttr' => array(
                'class'     => 'ext.behaviors.eav.EEavBehavior',
                'tableName' => 'StoreProductAttributeEAV',
            ),
            'STranslateBehavior'=>array(
                'class'=>'ext.behaviors.STranslateBehavior',
                'relationName'=>'translate',
                'translateAttributes'=>array(
                    'name',
                ),
            ),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Название области',
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
        $criteria->with = array(
            'translate',
        );

		$criteria->compare('id',$this->id);
		$criteria->compare('translate.name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Region the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getRegionList($zero = false){
        $data = $this->findAll();
        $array = (!empty($data)) ? CHtml::listData($data, 'id', 'name') : array();
        asort($array);
        if($zero) $array[0] = '-- Выберите область --';
        return $array;
    }
}
