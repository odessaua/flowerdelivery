<?php
//Yii::import('application.modules.pages.models.PageCategory');
/**
 * Обработка городов в URL для главных страниц
 * Class SStoreCityUrlRule
 */
class SStoreCityUrlRule extends CBaseUrlRule
{
	public $connectionID = 'db';
	public $urlSuffix    = '';
    // этот метод не проверял!!!
	public function createUrl($manager,$route,$params,$ampersand)
	{
		if($route==='store/index/city')
		{
            $url=trim($params['url'],'/');
			unset($params['city_id'], $params['url']);
			if(!empty($params))
			{
                $qs = http_build_query($params);
                $url .= '?' . $qs;
			}
			return $url;
		}
		return false;
	}
	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if(empty($pathInfo))
			return false;
		if($this->urlSuffix)
			$pathInfo = strtr($pathInfo, array($this->urlSuffix=>''));

        // обратная замена _ на пробелы в названиях городов в URL для
        // сравнения с оригиналами (krivoy_rog --> krivoy rog)
		$pathInfo = str_replace('_', ' ', $pathInfo);
		foreach($this->getAllPaths() as $city_id => $path)
		{
			if(($path !== '') && (strlen($path) == strlen($pathInfo)) && (strpos($path, $pathInfo) !== false))
			{
				$_GET['url'] = $path;
                $_GET['city_id'] = $city_id;
				$params = ltrim(substr($pathInfo,strlen($path)), '/');
				Yii::app()->urlManager->parsePathInfo($params);
				return 'store/index/city';
			}
		}
        return false;
	}
	protected function getAllPaths()
	{
//		$allPaths = Yii::app()->cache->get('SStoreCityUrlRule');
//
//		if($allPaths === false)
//		{
			$cities = Yii::app()->db->createCommand()
				->from('cityTranslate')
				->select('object_id, name')
                ->where('language_id = 9')
				->queryAll();
			if(!empty($cities)){
                foreach ($cities as $city) {

                    $allPaths[$city['object_id']] = strtolower($city['name']);
                }
            }
//			Yii::app()->cache->set('SStoreCityUrlRule', $allPaths);
//		}
		return $allPaths;
	}
}