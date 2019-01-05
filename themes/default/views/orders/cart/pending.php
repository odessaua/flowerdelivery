<?php
//echo "<pre>";
 //var_dump($model, $products);
 
?>
<br>
<div class="content-text">
<h1 class="page-title"><?=Yii::t('OrdersModule.core','Your order')?> &#8470;<?=$model->id;?></h1> 
<p><?=Yii::t('OrdersModule.core','Dear')?> <?=$model->user_name?>,</p>
<p><?=Yii::t('OrdersModule.core','The status of your payment is')?> <strong>PENDING</strong>. <?=Yii::t('OrdersModule.core','The transaction is currently under review on the payment gateway and it may up to 15 min to check it.')?></p>
<p><?=Yii::t('OrdersModule.core','As soon as the transaction is cleared you will be informed by email.')?></p>
<p><?=Yii::t('OrdersModule.core','You can check the status of your order at this page')?> <a href="/cart/view/<?=$model->secret_key?>"><?=Yii::t('OrdersModule.core','Order &#8470;')?><?=$model->id;?></a></p>
<p><?=Yii::t('OrdersModule.core','Thank you for usinig our service!<br>
			7Roses Team')?></p>
</div>