<?php

/**
 * Manager urls
 */
class SUrlManager extends CUrlManager
{

	/**
	 * Init
	 * @access public
	 */
	public function init()
	{
		$this->_loadModuleUrls();
		parent::init();
	}

	/**
	 * Create url based on current language.
	 * @param mixed $route
	 * @param array $params
	 * @param string $ampersand
	 * @param boolean $respectLang
	 * @access public
	 * @return string
	 */
	public function createUrl($route,  $params=array(),  $ampersand='&', $respectLang = true)
	{
		$result = parent::createUrl($route, $params, $ampersand);

		if ($respectLang === true)
		{
			$langPrefix = Yii::app()->languageManager->getUrlPrefix();
			if ($langPrefix)
				$result = '/'.$langPrefix.$result;
		}

        // добавление языковой версии в URL
        $result = MLhelper::addLangToUrl($result);
		return $result;
	}

	/**
	 * Scan each module dir and include routes.php
	 * Add module urls at the beginning of $config['urlManager']['rules']
	 * @access protected
	 */
	protected function _loadModuleUrls()
	{
		$cacheKey = 'url_manager_urls';
		$rules    = Yii::app()->cache->get($cacheKey);

		if(YII_DEBUG || !$rules)
		{
			$rules       = array();
			$modules     = SystemModules::getEnabled();
			$modulesPath = Yii::getPathOfAlias('application.modules');

			foreach($modules as $module)
			{
				$configFilePath = implode(DIRECTORY_SEPARATOR, array($modulesPath, $module->name, 'config', 'routes.php'));
				if(file_exists($configFilePath))
					$rules = array_merge(require($configFilePath), $rules);
			}

			Yii::app()->cache->set($cacheKey, $rules, 3600);
		}

		$this->rules = array_merge($rules, $this->rules);
	}

}
