<?php

class m140209_095522_fix_product_counters extends CDbMigration
{
	public function up()
	{
		$this->execute('ALTER TABLE StoreProduct ALTER views_count SET DEFAULT 0;');
		$this->execute('ALTER TABLE StoreProduct ALTER added_to_cart_count SET DEFAULT 0;');
	}
}