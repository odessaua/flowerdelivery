<?php

/**
 * This is the model class for table "Comments".
 *
 * The followings are the available columns in table 'Comments':
 * @property integer $id
 * @property integer $user_id
 * @property string $class_name
 * @property integer $object_pk
 * @property integer $status
 * @property string $email
 * @property string $name
 * @property string $text
 * @property string $created
 * @property string $updated
 * @property string $ip_address
 * @method approved()
 * @method orderByCreatedAsc()
 * @method orderByCreatedDesc()
 */

class Comment extends BaseModel
{

	const STATUS_WAITING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_SPAM = 2;

	/**
	 * @var string
	 */
	public $verifyCode;

	/**
	 * @var int status for new comments
	 */
	public $defaultStatus;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Initialize
	 */
	public function init()
	{
		$this->defaultStatus = Comment::STATUS_WAITING;
		return parent::init();
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Comments';
	}

	public function scopes()
	{
		$alias = $this->getTableAlias();
		return array(
			'orderByCreatedAsc'=>array(
				'order'=>$alias.'.created ASC',
			),
			'orderByCreatedDesc'=>array(
				'order'=>$alias.'.created DESC',
			),
			'approved'=>array(
				'condition'=>$alias.'.status='.self::STATUS_APPROVED,
			),
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$codeEmpty=!Yii::app()->user->isGuest;
		if(YII_DEBUG) // For tests
			$codeEmpty=true;
		return array(
			array('email, name, rating, text', 'required'),
			array('email', 'email'),
			array('status, created, updated', 'required', 'on'=>'update'),
			array('name', 'length', 'max'=>50),
			array('city', 'length', 'max'=>255),
			array('verifyCode','captcha','allowEmpty'=>$codeEmpty),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'         => 'ID',
			'user_id'    => Yii::t('CommentsModule.core','Автор'),
			'class_name' => Yii::t('CommentsModule.core','Модель'),
			'status'     => Yii::t('CommentsModule.core','Статус'),
			'email'      => Yii::t('CommentsModule.core','Email'),
			'city'		 => Yii::t('CommentsModule.core','City'),
			'rating'	 => Yii::t('CommentsModule.core','Assessment of quality of service'),
			'name'       => Yii::t('CommentsModule.core','Name'),
			'text'       => Yii::t('CommentsModule.core','Comment'),
			'created'    => Yii::t('CommentsModule.core','Дата создания'),
			'updated'    => Yii::t('CommentsModule.core','Дата обновления'),
			'owner_title'=> Yii::t('CommentsModule.core','Владелец'),
			'verifyCode' => Yii::t('CommentsModule.core','Verify code'),
			'ip_address' => Yii::t('CommentsModule.core','IP адрес'),
		);
	}

	/**
	 * Before save.
	 */
	public function beforeSave()
	{
		if($this->isNewRecord)
		{
			$this->status = $this->defaultStatus;
			$this->ip_address = Yii::app()->request->userHostAddress;
			$this->created = date('Y-m-d H:i:s');
		}
		$this->updated = date('Y-m-d H:i:s');
		return parent::beforeSave();
	}
}