<?php

/**
 * Class to save system and modules settings.
 * Usage
 * <pre>
 *      Yii::app()->settings->set('product', array(
 *         'per_page'=>15,
 *         'comments'=>true
 *      ));
 *
 *      Yii::app()->settings->get('category'); //get all category settings as array
 *      Yii::app()->settings->get('category', 'per_page'); //get `per_page` value
 * </pre>
 */
class SSystemSettings extends CComponent
{

	/**
	 * @var array
	 */
	protected $data = array();

	public $cache_key = 'cached_system_settings';

	/**
	 * Initialize component
	 */
	public function init()
	{
		$this->data = Yii::app()->cache->get($this->cache_key);

		if(!$this->data)
		{
			// Load settings
			$settings = Yii::app()->db->createCommand()
				->from('SystemSettings')
				->order('category')
				->queryAll();

			if(!empty($settings))
			{
				foreach($settings as $row)
				{
					if(!isset($this->data[$row['category']]))
						$this->data[$row['category']] = array();
					$this->data[$row['category']][$row['key']] = $row['value'];
				}
			}

			Yii::app()->cache->set($this->cache_key, $this->data);
		}
	}

	/**
	 * @param $category string component unique id. e.g: feedback, store, store.front
	 * @param array $data key-value array. e.g array('items_per_page'=>10)
	 */
	public function set($category, array $data)
	{
		if(!empty($data))
		{
			foreach($data as $key=>$value)
			{
				if($this->get($category, $key)!==null)
				{
					Yii::app()->db->createCommand()->update('SystemSettings', array(
						'value'=>$value,
					), 'SystemSettings.category=:category AND SystemSettings.key=:key', array(':category'=>$category,':key'=>$key));
				}
				else
				{
					Yii::app()->db->createCommand()->insert('SystemSettings', array(
						'category' => $category,
						'key'      => $key,
						'value'    => $value
					));
				}
			}

			if(!isset($this->data[$category]))
				$this->data[$category]=array();
			$this->data[$category]=CMap::mergeArray($this->data[$category], $data);

			// Update cache
			Yii::app()->cache->set($this->cache_key, $this->data);
		}
	}

	/**
	 * @param $category string component unique id.
	 * @param null $key option key. If not provided all category settings will be returned as array.
	 * @param null|string $default default value if original does not exists
	 * @return mixed
	 */
	public function get($category, $key=null, $default=null)
	{
		if(!isset($this->data[$category]))
			return $default;

		if($key===null)
			return $this->data[$category];
		if(isset($this->data[$category][$key]))
			return $this->data[$category][$key];
		else
			return $default;
	}

	/**
	 * Remove category from DB
	 * @param $category
	 */
	public function clear($category)
	{
		Yii::app()->db->createCommand()->delete('SystemSettings', 'category=:category', array(':category'=>$category));
		if(isset($this->data[$category]))
			unset($this->data[$category]);

		Yii::app()->cache->delete($this->cache_key);
	}

}
