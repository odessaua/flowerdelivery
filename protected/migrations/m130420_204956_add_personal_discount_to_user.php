<?php

class m130420_204956_add_personal_discount_to_user extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('user', 'discount', 'string');
		}catch (Exception $e){};
	}

	public function down()
	{
		$this->dropColumn('user', 'discount');
	}
}