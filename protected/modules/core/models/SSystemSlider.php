<?php
Yii::import('application.modules.core.models.SystemSliderTranslate');
/**
 * This is the model class for table "SystemSlider".
 *
 * The followings are the available columns in table 'SystemSlider':
 * @property integer $id
 * @property string $name
 * @property string $photo
 * @property string $url
 * @property int $position
 * @property int $active
 */
class SSystemSlider extends BaseModel
{

    private static $_sliders;
	
	public $translateModelName = 'SystemSliderTranslate';

    /**
     * Returns the static model of the specified AR class.
     * @return SystemSlider the static model class
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
        return 'SystemSlider';
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'active'              => array('condition'=>$alias.'.active=1'),
            'orderByPosition'     => array('order'=>$alias.'.position ASC'),
            'orderByPositionDesc' => array('order'=>$alias.'.position DESC'),
        );
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name', 'required'),
            array('position, active', 'numerical', 'integerOnly'=>true),
            array('name, url, photo', 'length', 'max'=>255),
            // search attributes
            array('id, name', 'safe', 'on'=>'search'),
        );
    }
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'slide_translate' => array(self::HAS_ONE, $this->translateModelName, 'object_id'),
		);
	}
	
	public function behaviors()
	{
		return array(
			'STranslateBehavior'=>array(
				'class'=>'ext.behaviors.STranslateBehavior',
				'relationName'=>'slide_translate',
				'translateAttributes'=>array(
					'name',
					'photo',
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
            'id'        => 'ID',
            'name'      => Yii::t('CoreModule.core', 'Название'),
            'photo'      => Yii::t('CoreModule.core', 'Фото'),
            'url'      => Yii::t('CoreModule.core', 'Ссылка'),
            'position'      => Yii::t('CoreModule.core', 'Позиция'),
            'active'      => Yii::t('CoreModule.core', 'Активен'),

        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('active',$this->active,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Before save event
     */
    public function beforeSave()
    {
        if($this->position == '')
        {
            // позиция не указана
            // находим максимальное значение позиции
            // присваиваем текущей записи значение max + 1
            $max = SSystemSlider::model()->orderByPositionDesc()->find();
            if($max)
                $this->position = (int)$max->position + 1;
            else
                $this->position = 0;
        }
        else{
            if($this->getIsNewRecord()) {
                // указана новая позиция для новой записи
                // увеличиваем значение всех записей, у которых position >= указанной позиции
                // это позволит вставить новую запись «между» другими позициями в списке
                $this->updateCounters(
                    array('position' => 1),
                    'position >= :pos',
                    array(':pos' => $this->position)
                );
            }
            else{
                // запись обновляется
                // проверяем старую позицию записи
                $row = SSystemSlider::model()->findByPk($this->id);
                if($row->position != $this->position){
                    // позиция изменилась – обновляем записи по принципу, описанному выше
                    $this->updateCounters(
                        array('position' => 1),
                        'position >= :pos',
                        array(':pos' => $this->position)
                    );
                }
            }
        }
        return parent::beforeSave();
    }
}