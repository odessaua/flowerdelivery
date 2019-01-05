<?php

Yii::import('application.modules.pages.models.PageCategory');

class SStoreCategoryUrlRule extends CBaseUrlRule
{
	public $connectionID = 'db';
	public $urlSuffix    = '';

	public function createUrl($manager,$route,$params,$ampersand)
	{
		if($route==='store/category/view')
		{
			$url=trim($params['url'],'/');
			unset($params['url'], $params['language']);

			$parts=array();
			if(!empty($params))
			{
				foreach ($params as $key=>$val)
					$parts[]=$key.'/'.$val;

				$url .= '/'.implode('/', $parts);
			}

			return $url.$this->urlSuffix;
		}
		return false;
	}

	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		if(empty($pathInfo))
			return false;

		if($this->urlSuffix)
			$pathInfo = strtr($pathInfo, array($this->urlSuffix=>''));

		foreach($this->getAllPaths() as $path)
		{
            // is product
            if(
                ($path !== '')
                && (strpos($pathInfo, 'page/') === false)
                && (strpos($pathInfo, '.html') !== false)
            ){
                if($this->checkProductUrl($pathInfo)) {
                    $ex_path = explode('/', $pathInfo);
                    $_GET['url'] = str_replace('.html', '', end($ex_path));
                    return 'store/frontProduct/view';//http://flowers3.loc/product/bouquet-the-trembling-heart.html
                }
                else
                    return false;
            }

            // is category
			if($path !== '' && strpos($pathInfo, $path) === 0)
			{
				$_GET['url'] = $path;

				$params = ltrim(substr($pathInfo,strlen($path)), '/');
				Yii::app()->urlManager->parsePathInfo($params);

				return 'store/category/view';
			}
		}

		return false;
	}

	protected function getAllPaths()
	{
		$allPaths = Yii::app()->cache->get('SStoreCategoryUrlRule');

		if($allPaths === false)
		{
			$allPaths = Yii::app()->db->createCommand()
				->from('StoreCategory')
				->select('full_path')
				->queryColumn();

			// Sort paths by length.
			usort($allPaths, function($a, $b) {
				return strlen($b) - strlen($a);
			});

			Yii::app()->cache->set('SStoreCategoryUrlRule', $allPaths);
		}

		return $allPaths;
	}

    /**
     * проверка URL товара на соответствие критерию:
     * url_главной_категории_товара/url_товара
     * @param $path
     * @return bool
     */
    public function checkProductUrl($path)
    {
        $ex_path = explode('/', $path);
        // параметров в URL не больше 2-х: roses/bouquet-yellow-roses-long-stem.html
        if(count($ex_path) > 2){
            return false;
        }

        $product_url = str_replace('.html', '', end($ex_path));
        // запрашиваем товар
        $product = StoreProduct::model()->find('url = :url', array(':url' => $product_url));
        if(!empty($product)){
            // проверяем главную категорию товара
            $main_category = Yii::app()->db->createCommand()
                ->select('sc.url')
                ->from('StoreCategory sc')
                ->join('StoreProductCategoryRef spcr', 'spcr.category = sc.id')
                ->where('spcr.product = :product AND spcr.is_main = 1', array(':product' => $product->id))
                ->queryScalar();
            // сравниваем полученный URL категории с первым параметром URL
            if(!empty($main_category) && ($main_category == $ex_path[0])){
                return true;
            }
        }
        return false;
	}

}
