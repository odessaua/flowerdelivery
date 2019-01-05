<?php

/**
 * This is the model class for table "citySeo".
 *
 * The followings are the available columns in table 'citySeo':
 * @property string $id
 * @property integer $city_id
 * @property integer $lang_id
 * @property string $seo_text
 * @property string $seo_keywords
 * @property string $seo_description
 * @property string $seo_title
 */
Yii::import('application.modules.store.models.*');
Yii::import('application.modules.core.models.SSystemLanguage');
class CitySeo extends CActiveRecord
{
    public $cities;
    public $langs;
    public $seo_cities;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'citySeo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, lang_id', 'numerical', 'integerOnly'=>true),
			array('seo_text, seo_keywords, seo_description, seo_title', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, city_id, lang_id, seo_text, seo_keywords, seo_description, seo_title', 'safe', 'on'=>'search'),
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
			'city_id' => 'Город',
			'lang_id' => 'Язык',
			'seo_text' => 'Seo текст',
			'seo_keywords' => 'Seo META-Keywords',
			'seo_description' => 'Seo META-Description',
			'seo_title' => 'Seo META-Title и Title страницы',
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
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('lang_id',$this->lang_id);
		$criteria->compare('seo_text',$this->seo_text,true);
		$criteria->compare('seo_keywords',$this->seo_keywords,true);
		$criteria->compare('seo_description',$this->seo_description,true);
		$criteria->compare('seo_title',$this->seo_title,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CitySeo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function getCityName($city_id)
    {
        if(empty($this->cities)){
            $this->cities = $this->getCityList();
        }
        return (!empty($this->cities[$city_id])) ? $this->cities[$city_id] : '';
    }

    public function getCityList($zero = false)
    {
        $cities = City::model()->with('translate')->findAll();
        if(!empty($cities)){
            $data = CHtml::listData($cities, 'id', 'name');
            if($zero){
                $data[0] = 'Выберите город';
            }
            ksort($data);
            return $data;
        }
        return array();
    }

    public function getLangName($lang_id)
    {
        if(empty($this->langs)){
            $this->langs = $this->getLangList();
        }
        return (!empty($this->langs[$lang_id])) ? $this->langs[$lang_id] : '';
    }

    public function getLangList($zero = false)
    {
        $langs = SSystemLanguage::model()->findAll();
        if(!empty($langs)){
            $data = CHtml::listData($langs, 'id', 'name');
            if($zero){
                $data[0] = 'Выберите язык';
            }
            ksort($data);
            return $data;
        }
        return array();
    }

    public function checkLang($city_id, $lang_id)
    {
        if(empty($this->seo_cities)){
            $sc = $this->findAll();
            if(!empty($sc)){
                foreach($sc as $row){
                    $this->seo_cities[$row->attributes['city_id']][$row->attributes['lang_id']] = $row->attributes['lang_id'];
                }
            }
        }
        // CHtml::link(CHtml::encode($data->name), array("/store/admin/deliveryRegions/update", "id"=>$data->id))
        $yes = CHtml::link('Да', array("/store/admin/citySeo/update", "city_id"=>$city_id, 'lang_id'=>$lang_id), array('style' => 'color: green; text-decoration: underline;'));
        $no = CHtml::link('Нет', array("/store/admin/citySeo/create", "city_id"=>$city_id, 'lang_id'=>$lang_id), array('style' => 'color: red; text-decoration: underline;'));
        return (!empty($this->seo_cities[$city_id][$lang_id])) ? $yes : $no;
    }
}