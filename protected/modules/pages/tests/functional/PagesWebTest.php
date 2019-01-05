<?php

Yii::import('application.modules.pages.PagesModule');
Yii::import('application.modules.pages.models.*');

class PagesWebTest extends WebTestCase
{

	public $fixtures = array(
		'Page'=>'Page',
		'PageTranslate'=>'PageTranslate',
		'PageCategory'=>'PageCategory',
		'PageCategoryTranslate'=>'PageCategoryTranslate'
	);

	public function setUp()
	{
		$this->getFixtureManager()->basePath = Yii::getPathOfAlias('application.modules.pages.tests.fixtures');
		parent::setUp();
	}

	public function testViewPage()
	{
		$this->open('page/page-1.html');
		$this->assertTextPresent($this->PageTranslate['page1']['title']);
	}

	public function testViewCategory()
	{
		$this->open('pages/knigi/nauchnie-knigi/page/1');
		$this->assertTextPresent('Научные книги');
	}

}
