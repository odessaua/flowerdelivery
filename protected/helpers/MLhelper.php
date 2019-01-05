<?php
class MLhelper
{
    /**
     * Функционал работает только если языков больше 1
     * @return bool
     */
    public static function enabled()
    {
        return count(Yii::app()->params['translatedLanguages']) > 1;
    }

    /**
     * Список языковых версий
     * @return array
     */
    public static function suffixList()
    {
        $list = array();
        $enabled = self::enabled();

        foreach (Yii::app()->params['translatedLanguages'] as $lang => $name)
        {
            if ($lang === Yii::app()->params['defaultLanguage']) {
                $suffix = '';
                $list[$suffix] = $enabled ? $name : '';
            } else {
                $suffix = '_' . $lang;
                $list[$suffix] = $name;
            }
        }

        return $list;
    }

    /**
     * Обработка языка в URL
     * @param string $url
     * @return string
     */
    public static function processLangInUrl($url)
    {
        if (self::enabled())
        {
            $domains = explode('/', ltrim($url, '/'));

            $isLangExists = in_array($domains[0], array_keys(Yii::app()->params['translatedLanguages']));
            $isDefaultLang = $domains[0] == Yii::app()->params['defaultLanguage'];

            if ($isLangExists && !$isDefaultLang)
            {
                $lang = array_shift($domains);
                self::setLang($lang);
            }

            $url = '/' . implode('/', $domains);
        }

        return $url;
    }

    /**
     * Устанавливаем язык – если он не соответствует уже установленному
     * @param string $lang
     * @return bool
     */
    public static function setLang($lang)
    {
        if(self::enabled()){
            $currentLang = Yii::app()->language;
            if($lang !== $currentLang){
                // меняем язык
                Yii::app()->setLanguage($lang); // система
                $_GET['language'] = $lang; // GET
                Yii::app()->user->setState('language', $_GET['language']); // пользователь
                $cookie = new CHttpCookie('language', $_GET['language']);
                $cookie->expire = time() + (60*60*24*365); // (1 year)
                Yii::app()->request->cookies['language'] = $cookie; // cookies
            }
            else{
                $_GET['language'] = $lang; // GET
            }
        }
        return true;
    }

    /**
     * Добавление языка в URL при формировании с использованием createUrl например
     * @param string $url
     * @return string
     */
    public static function addLangToUrl($url)
    {
        if(strpos($url, 'http') !== false){
            return $url;
        }
        if (self::enabled())
        {
            $domains = explode('/', ltrim($url, '/'));
            $isHasLang = in_array($domains[0], array_keys(Yii::app()->params['translatedLanguages']));
            $isDefaultLang = Yii::app()->language == Yii::app()->params['defaultLanguage'];

            if ($isHasLang && $isDefaultLang)
                array_shift($domains);

            if (!$isHasLang && !$isDefaultLang)
                array_unshift($domains, Yii::app()->language);

            $url = '/' . implode('/', $domains);
        }

        return $url;
    }

    /**
     * Добавление указанного языка в URL
     * @param string $url - полный URL (Yii::app()->request->uri)
     * @param string $lang - язык, который требуется указать в URL
     * @return string - полный URL с требуемым языком в начале
     */
    public static function addSpecifiedLangToUrl($url, $lang = 'en')
    {
        if (self::enabled())
        {
            // разбор URL по параметрам
            $domains = explode('/', ltrim($url, '/'));
            // указан ли какой-либо язык в URL
            $isHasLang = in_array($domains[0], array_keys(Yii::app()->params['translatedLanguages']));
            // является ли требуемый язык языком по-умолчанию (он не добавляется в URL)
            $isDefaultLang = $lang == Yii::app()->params['defaultLanguage'];

            // язык указан – и это не требуемый язык
            // заменяем старый язык на требуемый
            if($isHasLang && ($lang !== $domains[0])){
                $domains[0] = $lang;
            }

            // какой-то язык указан в URL - и требуемый язык является языком по-умолчанию
            // удаляем язык из URL
            if ($isHasLang && $isDefaultLang)
                array_shift($domains);

            // язык не указан в URL – и требуемый язык не является языком по-умолчанию
            // добавляем требуемый язык в URL
            if (!$isHasLang && !$isDefaultLang)
                array_unshift($domains, $lang);

            // формируем новый URL
            $url = '/' . implode('/', $domains);
        }

        return $url;
    }
}