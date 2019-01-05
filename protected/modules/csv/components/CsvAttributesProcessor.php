<?php

/**
 * Class CsvAttributesProcessor handles StoreProduct class attributes and
 * EAV attributes.
 */
class CsvAttributesProcessor extends CComponent
{

	/**
	 * @var StoreProduct
	 */
	public $model;

	/**
	 * @var array csv row.
	 */
	public $data;

	/**
	 * @var array
	 */
	public $skipNames = array('category', 'type', 'manufacturer', 'image', 'additionalCategories');

	/**
	 * @var array of StoreAttribute models.
	 */
	protected $attributesCache = array();

	/**
	 * @var array of StoreAttributeOption models.
	 */
	protected $optionsCache = array();

	/**
	 * @var array for eav attributes to be saved.
	 */
	protected $eav;

	/**
	 * @param StoreProduct $product
	 * @param array $data
	 */
	public function __construct(StoreProduct $product, array $data)
	{
		$this->model = $product;
		$this->data  = $data;
		$this->process();
	}

	/**
	 * Process each data row. First, try to assign value to products model,
	 * if attributes does not exists - handle like eav attribute.
	 */
	public function process()
	{
		foreach($this->data as $key=>$val)
		{
			try
			{
				// TODO: get_object_vars
				$this->model->$key = $val;
			}
			catch(CException $e)
			{
				// Process eav
				if(!in_array($key, $this->skipNames) && !empty($val))
					$this->eav[$key] = $this->processEavData($key, $val);
			}
		}
	}


	/**
	 * @param $attribute_name
	 * @param $attribute_value
	 * @return string StoreAttributeOption id
	 */
	public function processEavData($attribute_name, $attribute_value)
	{
		$result = array();
		$attribute = $this->getAttributeByName($attribute_name);

		$multipleTypes = array(StoreAttribute::TYPE_CHECKBOX_LIST, StoreAttribute::TYPE_DROPDOWN, StoreAttribute::TYPE_SELECT_MANY);

		if(in_array($attribute->type, $multipleTypes))
		{
			foreach(explode(',', $attribute_value) as $val)
			{
				$option = $this->getOption($attribute, $val);
				$result[] = $option->id;
			}
		}
		else
		{
			$option = $this->getOption($attribute, $attribute_value);
			$result[] = $option->id;
		}

		return $result;
	}

	/**
	 * Find or create option by attribute and value.
	 *
	 * @param StoreAttribute $attribute
	 * @param $val
	 * @return StoreAttributeOption
	 */
	public function getOption(StoreAttribute $attribute, $val)
	{
		$val = trim($val);
		$cacheKey = sha1($attribute->id . $val);

		if(isset($this->optionsCache[$cacheKey]))
			return $this->optionsCache[$cacheKey];

		// Search for option
		$cr = new CDbCriteria;
		$cr->with = 'option_translate';
		$cr->compare('option_translate.value', $val);
		$cr->compare('t.attribute_id', $attribute->id);
		$option = StoreAttributeOption::model()->find($cr);

		if(!$option) // Create new option
			$option = $this->addOptionToAttribute($attribute->id, $val);

		$this->optionsCache[$cacheKey] = $option;

		return $option;
	}

	/**
	 * @param $attribute_id
	 * @param $value
	 * @return StoreAttributeOption
	 */
	public function addOptionToAttribute($attribute_id, $value)
	{
		$option = new StoreAttributeOption;
		$option->attribute_id = $attribute_id;
		$option->value = $value;
		$option->save(false);

		return $option;
	}

	/**
	 * @param $name
	 * @return StoreAttribute
	 */
	public function getAttributeByName($name)
	{
		if(isset($this->attributesCache[$name]))
			return $this->attributesCache[$name];

		$attribute = StoreAttribute::model()->findByAttributes(array('name'=>$name));

		if(!$attribute)
		{
			// Create new attribute
			$attribute = new StoreAttribute;
			$attribute->name             = $name;
			$attribute->title            = ucfirst(str_replace('_',' ',$name));
			$attribute->type             = StoreAttribute::TYPE_DROPDOWN;
			$attribute->display_on_front = true;
			$attribute->save(false);

			// Add to type
			$typeAttribute = new StoreTypeAttribute;
			$typeAttribute->type_id      = $this->model->type_id;
			$typeAttribute->attribute_id = $attribute->id;
			$typeAttribute->save(false);
		}

		$this->attributesCache[$name] = $attribute;

		return $attribute;
	}

	/**
	 * Append and save product attributes.
	 */
	public function save()
	{
		if(!empty($this->eav))
			$this->model->setEavAttributes($this->eav, true);
	}
} 