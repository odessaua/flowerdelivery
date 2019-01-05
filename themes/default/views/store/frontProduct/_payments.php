<?php
$payments = StorePaymentMethod::model()->findAll(array(
    'condition' => 'active = 1',
    'order' => 'position ASC',
));
?>

<div class="title"><?=Yii::t('StoreModule.core', 'Payment methods'); ?></div>
<div class="content-text">
    <?php
    if(!empty($payments)):
        foreach($payments as $payment):
    ?>
    <div class="payment-row">
        <div class="payment-row-name"><?=$payment->name; ?></div>
        <div class="payment-row-description"><?=$payment->description; ?></div>
    </div>
    <?php
        endforeach;
    endif;
    ?>
</div>
