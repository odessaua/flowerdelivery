<?php

/**
 * Class to access product translations
 *
 * @property int $id
 * @property int $object_id
 * @property int $language_id
 * @property string $firm_name
 * @property string $firm_address
 * @property string $firm_phone
 * @property integer $firm_show
 * @property string $h1_header
 */
class CityTranslate extends CActiveRecord {

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'cityTranslate';
	}

}