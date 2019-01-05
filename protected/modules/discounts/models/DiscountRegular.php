<?php

class DiscountRegular extends BaseModel
{
	
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
		return 'DiscountRegular';
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
			array('name, sum, start_date, end_date, min_price, max_price', 'required'),
			array('active', 'boolean'),
			array('name', 'length', 'max'=>255),
			array('sum', 'length', 'max'=>10),
			array('start_date, end_date', 'date','format'=>'yyyy-M-d H:m:s'),

			array('id, name, active, sum, start_date, end_date, min_price, max_price', 'safe', 'on'=>'search'),
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
			'min_price'     => Yii::t('DiscountsModule.core', 'Минимальная сумма'),
			'max_price'     => Yii::t('DiscountsModule.core', 'Максимальная сумма'),
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
		$criteria->compare('t.min_price',$this->end_date, true);
		$criteria->compare('t.max_price',$this->end_date, true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function checkDiscount($price)
	{
		$regular_discount = DiscountRegular::calculateDiscount();
		$regular_discount = round($regular_discount);
		
		$models = Yii::app()->db->createCommand()->
        select( '*' )->
        from('DiscountRegular')->
        where('active = "1" AND start_date <= CURRENT_TIMESTAMP() AND end_date >= CURRENT_TIMESTAMP()')->
        queryAll();
		$coupon = $models;
		
		for($i = 0; $i < count($coupon); $i++) {
			if ($regular_discount >= $coupon[$i]['min_price'] && $regular_discount <= $coupon[$i]['max_price']) {
				$percent = $coupon[$i]['sum'];
				$result = $price - ($percent / 100 * $price);
				$minus = (string)$percent / 100 * $price;
				
				$data = [
					'result' => $result,
					'percent' => $percent,
					'minus' => $minus
				];
				return $data;
			}
		}
		
	}
	
	public function calculateDiscount()
	{
		$regular_discount = Order::model()->findAll('`user_id` = '.Yii::app(  )->user->id.' AND status_id = 6');
		if(!empty($regular_discount)){
			$eq = (string)0;
			foreach($regular_discount as $data){
				$eq += $data->total_price;
			}
			return (string)$eq;
		}else{
			return false;
		}
	}
	
	public function calculateDiscountById($id){
		$regular_discount = Order::model()->findAll('`user_id` = '.$id.' AND status_id = 6');
		if(!empty($regular_discount)){
			$eq = (string)0;
			foreach($regular_discount as $data){
				$eq += $data->total_price;
			}
			return (string)$eq;
		}
	}
}