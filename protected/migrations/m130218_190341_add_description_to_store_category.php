<?php

class m130218_190341_add_description_to_store_category extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('StoreCategory', 'description', 'text');
		}catch (Exception $e){
			
		};
	}

	public function down()
	{
		$this->dropColumn('StoreCategory', 'description');
	}

}