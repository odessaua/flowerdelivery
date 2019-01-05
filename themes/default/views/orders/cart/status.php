<?php
/**
 * @var $content
 * @var $status
 * @var $trans
 */
//var_dump($content, $status, $trans[Yii::app()->language]);
?>
<h3 style="text-align: center;"><?= Yii::t('main', 'Status of your payment'); ?>: <?= Yii::t('main', $status); ?></h3>
<p style="margin: 20px; text-align: center;"><?= $trans[Yii::app()->language]; ?></p>
<p style="margin: 20px; text-align: center;">
    <?= Yii::t('main', 'You can use another payment method for your order. To do this, please follow this link'); ?>:
    <a href="<?= Yii::app()->createUrl('/cart/view/' . $secret_key); ?>">Other payment methods</a>
</p>