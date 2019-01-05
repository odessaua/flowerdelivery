<?php
Yii::import('application.modules.orders.models.OrderPaymentStatusTranslate');

/**
 * This is the model class for table "OrderPaymentStatus".
 *
 * The followings are the available columns in table 'OrderPaymentStatus':
 * @property string $id
 * @property string $key
 * @property string $name
 * @property integer $active
 * @property string $message
 */
class OrderPaymentStatus extends BaseModel
{
    /**
     * @var string
     */
    public $translateModelName = 'OrderPaymentStatusTranslate';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $message;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'OrderPaymentStatus';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		    array('key, name', 'required'),
			array('active', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
            array('message', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, key, name, active', 'safe', 'on'=>'search'),
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
            'pm_translate' => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
		);
	}

    /**
     * @return array
     */
    public function behaviors()
    {
        return array(
            'STranslateBehavior'=>array(
                'class'=>'ext.behaviors.STranslateBehavior',
                'relationName'=>'pm_translate',
                'translateAttributes'=>array(
                    'name',
                    'message',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'active'              => array('condition'=>$alias.'.active=1'),
        );
    }

    /**
     * Before save event
     */
//    public function beforeSave()
//    {
//        if($this->position == '')
//        {
//            $max = StorePaymentMethod::model()->orderByPositionDesc()->find();
//            if($max)
//                $this->position = (int)$max->position + 1;
//            else
//                $this->position = 0;
//        }
//        return parent::beforeSave();
//    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'key' => 'Ключ',
			'name' => 'Название',
			'active' => 'Активен',
			'message' => 'Сообщение',
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
		$criteria->compare('key',$this->key,true);
		$criteria->compare('pm_translate.name',$this->name,true);
		$criteria->compare('active',$this->active);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OrderPaymentStatus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
