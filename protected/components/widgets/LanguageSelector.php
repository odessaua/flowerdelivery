<?php
class LanguageSelector extends CWidget
{
    public function run()
    {
        $currentLang = Yii::app()->language;
        $languages = Yii::app()->params['languages'];
        $defaultLanguage = Yii::app()->params['defaultLanguage'];
        $this->render('languageSelector', array(
            'currentLang' => $currentLang,
            'languages'=>$languages,
            'defaultLanguage' => $defaultLanguage)
        );
    }
}
?>