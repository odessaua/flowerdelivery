<?php

abstract class C1AbstractImport extends CComponent
{

	/**
	 * @var string alias where to save uploaded files
	 */
	public $tempDirectory='application.runtime.accounting1c';

	/**
	 * Auth orders
	 */
	public function commandCheckauth()
	{
		echo "success\n";
		echo Yii::app()->session->sessionName."\n";
		echo Yii::app()->session->sessionId;
	}

	public function commandInit()
	{
		$fileSize=(int)(ini_get('upload_max_filesize'))*1024*1024;
		echo "zip=no\n";
		echo "filelimit={$fileSize}\n";
	}

	/**
	 * Builds path to 1C downloaded files. E.g: we receive
	 * file with name 'import/df3/fl1.jpg' and build path to temp dir,
	 * protected/runtime/fl1.jpg
	 *
	 * @param $fileName
	 * @return string
	 */
	public function buildPathToTempFile($fileName)
	{
		$fileName = explode('/', $fileName);
		$fileName = end($fileName);
		return Yii::getPathOfAlias($this->tempDirectory).DIRECTORY_SEPARATOR.$fileName;
	}
} 