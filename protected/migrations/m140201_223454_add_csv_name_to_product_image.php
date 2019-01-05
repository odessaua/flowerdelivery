<?php

class m140201_223454_add_csv_name_to_product_image extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('StoreProductImage', 'original_file_name', 'string');
		}catch (Exception $e){};
	}

	public function down()
	{
		$this->dropColumn('StoreProductImage', 'original_file_name');
	}
}