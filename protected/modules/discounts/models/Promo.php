<?php

class Promo extends BaseModel
{

	public $generate = null;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Discount the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Promo';
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		$alias = $this->getTableAlias();
		return array(
			'orderByName'=>array('order'=>$alias.'.name ASC'),
			'activeOnly'=>array('condition'=>$alias.'.active=1'),
			'applyDate'=>array(
				'condition'=>'start_date <= :now AND end_date >= :now',
				'params'=>array(':now'=>date('Y-m-d H:i:s')),
			),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, sum, start_date, end_date, code', 'required'),
			array('active', 'boolean'),
			array('name', 'length', 'max'=>255),
			array('sum', 'length', 'max'=>10),
			array('start_date, end_date', 'date','format'=>'yyyy-M-d H:m:s'),

			array('id, name, active, sum, start_date, end_date, code', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'            => 'ID',
			'name'          => Yii::t('DiscountsModule.core', 'Название'),
			'active'        => Yii::t('DiscountsModule.core', 'Активен'),
			'sum'           => Yii::t('DiscountsModule.core', 'Скидка'),
			'start_date'    => Yii::t('DiscountsModule.core', 'Дата начала'),
			'end_date'      => Yii::t('DiscountsModule.core', 'Дата окончания'),
			'code'      => Yii::t('DiscountsModule.core', 'Промо код'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.active',$this->active);
		$criteria->compare('t.sum',$this->sum,true);
		$criteria->compare('t.start_date',$this->start_date, true);
		$criteria->compare('t.end_date',$this->end_date, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function getPromoDiscount($price, $code)
	{
		$model = Yii::app()->db->createCommand()->select( '*' )->from('Promo')->where('code=:code', array(':code' => $code))->queryRow();
		$timestamp_start = strtotime($model['start_date']);
		$timestamp_stop = strtotime($model['end_date']);
		
		if($model == true && $timestamp_start <= time() && $timestamp_stop >= time() && $model['active'] == '1')
		{
			$percent = $model['sum'];
			$number_percent = (string)$price / 100 * $percent;
			$result = $price - $number_percent;
			$data = [
					'result' => $result,
					'percent' => $percent,
					'minus' => $number_percent
				];
			return $data;
		}else{
			return false;
		}
		
	}
}