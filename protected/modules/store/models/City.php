<?php

/**
 * This is the model class for table "city".
 *
 * The followings are the available columns in table 'city':
 * @property integer $id
 * @property integer $region_id
 * @property string $name
 * @property string $phone_code
 * @property double $delivery
 * @property string $firm_name
 * @property string $firm_address
 * @property integer $firm_postcode
 * @property string $firm_phone
 * @property integer $firm_show
 * @property string $h1_header
 * @property integer $firm_show
 * @property integer $main_in_region
 */
Yii::import('application.modules.store.models.*');
class City extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public $translateModelName = 'CityTranslate';
	public $name;
    public $regions;
    public $alias; // псевдонимы городов

    /**
     * Translate-able
     */
    public $firm_name;
    public $firm_address;
	public $firm_postcode;
    public $firm_phone;
    public $firm_show;
    public $h1_header;
    public $firm_comment;

    public function tableName()
	{
		return 'city';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('name', 'filter', 'filter'=>'trim'),
			array('alias, firm_comment', 'safe'),
			array('delivery,show_in_popup,region_id, firm_show, main_in_region', 'numerical'),
			array('name', 'length', 'max'=>50),
            array('firm_name, firm_phone, firm_address, firm_postcode, h1_header', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, phone_code, delivery, main_in_region, show_in_popup', 'safe', 'on'=>'search'),
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

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'region_id' => 'Область',
			'name' => 'Название региона',
			'alias' => 'Псевдонимы региона',
			'phone_code' => 'Phone Code',
			'delivery' => 'Стоимость доставки ($)',
            'show_in_popup' => 'Показать во всплывающем окне',
            'firm_name' => 'Название компании-представителя',
            'firm_address' => 'Адрес компании-представителя',
			'firm_postcode' => 'Почтовый индекс',
            'firm_phone' => 'Телефоны компании-представителя',
            'firm_show' => 'Показывать контакты компании-представителя на сайте',
            'h1_header' => 'Заголовок h1',
            'firm_comment' => 'Комментарий',
            'main_in_region' => 'Областной центр'
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
                    'firm_name',
                    'firm_address',
					'firm_postcode',
                    'firm_phone',
                    'firm_show',
                    'h1_header',
                    'firm_comment',
				),
			),
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
			// 'categorization',
			'translate',
			// 'type',
		);
		
		$criteria->compare('t.id',$this->id);
//		$criteria->compare('region_id',$this->region_id);
		$criteria->compare('translate.name',$this->name,true);
		$criteria->compare('phone_code',$this->phone_code,true);
		$criteria->compare('firm_postcode',$this->firm_postcode,true);
		$criteria->compare('delivery',$this->delivery);
        $criteria->compare('translate.firm_name',$this->firm_name,true);
        $criteria->compare('translate.firm_address',$this->firm_address,true);
        $criteria->compare('translate.firm_phone',$this->firm_phone,true);
        $criteria->compare('translate.firm_show',$this->firm_show,true);
        $criteria->compare('translate.firm_comment',$this->firm_show,true);
        $criteria->compare('translate.h1_header',$this->firm_show,true);
        $criteria->compare('main_in_region',$this->main_in_region);
        $criteria->compare('show_in_popup',$this->show_in_popup);

        $sort = new CSort;
        $sort->attributes=array(
            'name' => array(
                'asc'   => 'translate.name',
                'desc'  => 'translate.name DESC',
            ),
        );

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort' => $sort,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return City the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getRegionName($region_id)
    {
        if(empty($this->regions)){
            $this->regions = Region::model()->language(1)->getRegionList();
        }
        return (!empty($this->regions[$region_id])) ? $this->regions[$region_id] : '';
    }

    public function checkContacts($id)
    {
        $return = '- - -';
        if(!empty($id)){
            $languages = SSystemLanguage::model()->findAll();
            $contacts = CityTranslate::model()->findAllByAttributes(array('object_id' => $id));
            if(!empty($contacts) && !empty($languages)){
                $results = array();
                $languages = CArray::toolIndexArrayBy($languages, 'id');
                foreach($contacts as $contact){
                    if(!empty($contact->firm_name)){
                        $results[] = $languages[$contact->language_id]->code;
                    }
                }
                $return = (!empty($results)) ? implode('-', $results) : $return;
            }
        }
        return $return;
    }

    public function checkH1($id)
    {
        $return = '- - -';
        if(!empty($id)){
            $languages = SSystemLanguage::model()->findAll();
            $contacts = CityTranslate::model()->findAllByAttributes(array('object_id' => $id));
            if(!empty($contacts) && !empty($languages)){
                $results = array();
                $languages = CArray::toolIndexArrayBy($languages, 'id');
                foreach($contacts as $contact){
                    if(!empty($contact->h1_header)){
                        $results[] = $languages[$contact->language_id]->code;
                    }
                }
                $return = (!empty($results)) ? implode('-', $results) : $return;
            }
        }
        return $return;
    }
}
