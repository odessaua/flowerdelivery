<?php

/**
 * This is the model class for table "SystemBaner".
 *
 * The followings are the available columns in table 'SystemBaner':
 * @property integer $id
 * @property string $name
 * @property string $photo
 * @property string $url
 * @property boolean $active
 * @property string $position
 */
class SSystemBaner extends BaseModel
{

    // private static $_languages;

    /**
     * Returns the static model of the specified AR class.
     * @return SSystemLanguage the static model class
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
        return 'SystemBaner';
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'active' => array('condition'=>$alias.'.active=1'),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('name, position', 'required'),
            array('name, url, photo, position', 'length', 'max'=>255),
            array('active', 'numerical', 'integerOnly'=>true),
            // search
            array('id, name, active, position', 'safe', 'on'=>'search'),
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
            'active'      => Yii::t('CoreModule.core', 'Активен'),
            'position'    => Yii::t('CoreModule.core', 'Позиция'),
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
        $criteria->compare('position',$this->active, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Before save event
     */
    public function beforeSave()
    {
        if(!empty($this->active))
        {
            // запись отмечена активной
            // делаем неактивными все другие записи с такой же позицией
            $this->updateAll(
                array('active' => 0),
                "position = :position",
                array(':position' => $this->position)
            );
        }
        return parent::beforeSave();
    }

}