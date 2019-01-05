<?php

class m130504_170119_add_discout_to_order extends CDbMigration
{
	public function up()
	{
		try{
			$this->addColumn('Order','discount', 'string');
		}catch (Exception $e){};
	}

	public function down()
	{
		$this->dropColumn('Order', 'discount');
	}

}