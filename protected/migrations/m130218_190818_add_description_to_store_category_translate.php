<?php

class m130218_190818_add_description_to_store_category_translate extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('StoreCategoryTranslate', 'description', 'text');
		}catch (Exception $e){

		};
	}

	public function down()
	{
		$this->dropColumn('StoreCategoryTranslate', 'description');
	}

}