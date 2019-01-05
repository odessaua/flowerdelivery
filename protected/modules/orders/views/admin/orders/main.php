<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder')
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'sourceLanguage'=>'en',
	'language'=>'ru',
	// pre-loading components
	'preload'=>array('log'),
	
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.payment.*',
		'application.components.validators.*',
		'application.modules.core.models.*',
		'application.modules.users.models.User',
		'application.modules.orders.models.*',
		// Rights module
		'application.modules.rights.*',
		'application.modules.rights.components.*',
	),
	
	'behaviors'=>array(
        'onBeginRequest' => array(
            'class' => 'application.components.behaviors.BeginRequest'
        ),
    ),

	'modules'=>array(
		
		'action_logger',
		'admin'=>array(),
		'rights'=>array(
			'layout'=>'application.modules.admin.views.layouts.main',
			'cssFile'=>false,
			'debug'=>YII_DEBUG,
		),
		'core',
		
		'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'carlositos',
            'ipFilters'=>array('*'),
        ),
	),

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class'=>'BaseUser',
			'loginUrl'=>'/users/login'
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'class'=>'SUrlManager',
			'showScriptName'=>false,
			'useStrictParsing'=>true,
			'rules'=>array(
				'/'=>'store/index/index',
				'admin/auth'=>'admin/auth',
				'admin/auth/logout'=>'admin/auth/logout',
				'admin/<module:\w+>'=>'<module>/admin/default',
				'admin/<module:\w+>/<controller:\w+>'=>'<module>/admin/<controller>',
				'admin/<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/admin/<controller>/<action>',
				'admin/<module:\w+>/<controller:\w+>/<action:\w+>/*'=>'<module>/admin/<controller>/<action>',

				'filemanager/connector' => 'admin/fileManager/index',

				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'<language:(ru|ua|en)>/' => 'site/index',
            	'<language:(ru|ua|en)>/<action:(contact|login|logout)>/*' => 'site/<action>',
	            '<language:(ru|ua|en)>/<controller:\w+>/<id:\d+>'=>'<controller>/view',
	            '<language:(ru|ua|en)>/<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
	            '<language:(ru|ua|en)>/<controller:\w+>/<action:\w+>/*'=>'<controller>/<action>',
	            '<language:(ru|ua|en)>/<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>/<id>',
				'<language:(ru|ua|en)>/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
				'admin'=>'admin/default/index',
				'rights'=>'rights/assignment/view',
				'rights/<controller:\w+>/<id:\d+>'=>'rights/<controller>/view',
				'rights/<controller:\w+>/<action:\w+>/<id:\d+>'=>'rights/<controller>/<action>',
				'rights/<controller:\w+>/<action:\w+>'=>'rights/<controller>/<action>',
				'gii'=>'gii',
				'gii/<controller:\w+>'=>'gii/<controller>',
				'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
			),
		),
		 'db'=>array(
		 	'connectionString'=>'mysql:host=localhost;dbname=7rosen1',
		 	'username'=>'root',
		 	'password'=>'569856',
		 	'enableProfiling'       => YII_DEBUG, // Disable in production
		 	'enableParamLogging'    => YII_DEBUG, // Disable in production
		 	'emulatePrepare'        => true,
		 	'schemaCachingDuration' => YII_DEBUG ? 0 : 3600,
		 	'charset'               => 'utf8',
		 ),
		// 'db'=>array(
		// 	'connectionString'=>'mysql:host=localhost;dbname=7rosesn',
		// 	'username'=>'u_7rosesn',
		// 	'password'=>'Kzz98Yzz',
		// 	'enableProfiling'       => YII_DEBUG, // Disable in production
		// 	'enableParamLogging'    => YII_DEBUG, // Disable in production
		// 	'emulatePrepare'        => true,
		// 	'schemaCachingDuration' => YII_DEBUG ? 0 : 3600,
		// 	'charset'               => 'utf8',
		// ),
		'request'=>array(
			'class'=>'SHttpRequest',
			'enableCsrfValidation'=>true,
			'enableCookieValidation'=>true,			
			'noCsrfValidationRoutes'=>array(
				'/processPayment',
				'/accounting1c/default/',
				'/filemanager/connector',
			)
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'authManager'=>array(
			'class'=>'RDbAuthManager',
			'connectionID'=>'db',
		),
		'cache'=>array(
			'class'=>'CFileCache',
		),
		'languageManager'=>array(
			'class'=>'SLanguageManager'
		),
		'fixture'=>array(
			'class'=>'system.test.CDbFixtureManager',
		),
		'cart'=>array(
			'class'=>'ext.cart.SCart',
		),
		'currency'=>array(
			'class'=>'store.components.SCurrencyManager'
		),
		'mail'=>array(
			'class'=>'ext.mailer.EMailer',
			'CharSet'=>'UTF-8',
		),
		'settings'=>array(
			'class'=>'application.components.SSystemSettings'
		),
		'log'=>YII_DEBUG===true ? require('logging.php') : null,
	),
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		 'adminEmail'=>'sadovaya@te.net.ua',
		//'adminEmail'=>'koakumasd@gmail.com',
		'adminPageSize'=>50,
		'languages'=>array('ru'=>'RU', 'ua'=>'UA', 'en'=>'EN'),
	),
);
