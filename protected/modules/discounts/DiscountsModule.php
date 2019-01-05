<?php

/**
 * Process product discounts
 */
class DiscountsModule extends BaseModule
{
	public $moduleName = 'discounts';

	public function init()
	{
		$this->setImport(array(
			'application.modules.discounts.models.*',
			'application.modules.discounts.components.*'
		));
	}
}
