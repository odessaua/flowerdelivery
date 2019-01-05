<?php

class m130421_095545_add_personal_discount_to_product extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('StoreProduct', 'discount', 'string');
		}catch (Exception $e){};
	}

	public function down()
	{
		$this->dropColumn('StoreProduct', 'discount');
	}
}