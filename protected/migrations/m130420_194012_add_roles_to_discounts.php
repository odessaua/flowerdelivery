<?php

class m130420_194012_add_roles_to_discounts extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('Discount','roles', 'string');
		}catch (Exception $e){

		};
	}

	public function down()
	{
		$this->dropColumn('Discount', 'roles');
	}
}