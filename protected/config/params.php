<?php
return array(
    // this is used in contact page
    'adminEmail'=>'order@7roses.com',
    'adminPageSize'=>50,
    'merchantAccount' => 'www_7roses_com', // www_7roses_com    test_merch_n1
    'merchantSecretKey' => '9847dcd24b0bd78c671b11001a32ab8f109642ed', // 9847dcd24b0bd78c671b11001a32ab8f109642ed    flk3409refn54t54t*FNJRET
    'languages'=>array('ru'=>'RU', 'uk'=>'UA', 'en'=>'EN'), // @lang замена ua -> uk для URL, при появлении нового языка в системе – обязательно добавить его сюда!
    'translatedLanguages'=>array( // @lang список языков, используемых на сайте, при появлении нового языка в системе – обязательно добавить его сюда!
        'en'=>'English',
        'uk'=>'Ukrainian',
        'ru'=>'Russian',
    ),
    'defaultLanguage'=>'en', // @lang язык по-умолчанию, не выводится в URL, должен совпадать с Yii::app()->language (см. выше)!
);