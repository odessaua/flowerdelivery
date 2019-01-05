<?php
Yii::import('application.modules.pages.models.Page');
		
class DefaultController extends Controller
{

	/**
	 * @return array
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
			),
		);
	}

	/**
	 * Display feedback form
	 */
	public function actionIndex()
	{
		Yii::import('feedback.models.FeedbackForm');
		$model = new FeedbackForm;

		if(isset($_POST['FeedbackForm']))
			$model->attributes = $_POST['FeedbackForm'];
		
		if(Yii::app()->request->isPostRequest )
		{
			if ($model->validate()) { 
			$model->sendMessage();
			}
			// Yii::app()->request->redirect($this->createUrl('index'));
		}
		// seo for feedback page
		$page = Page::model()->find('url = :url', array(':url' => 'feedback'));
        if(!empty($page)){
            $this->pageTitle = $page->meta_title;
            $this->pageKeywords = $page->meta_keywords;
            $this->pageDescription = $page->meta_description;
        }
        // Other cities
        Yii::import('feedback.models.CityTranslate');
        $criteria = new CDbCriteria();
        $criteria->condition = "`language_id` = " . (int)$this->language_info->id . " AND (`firm_name` != '' AND `firm_name` IS NOT NULL) AND `firm_show`=1 ";
        $criteria->order = '`name` ASC';
        $addresses = CityTranslate::model()->findAll($criteria);

		$this->render('index', array(
			'model'=>$model,
			'addresses' => $addresses,
		));
	}

}
