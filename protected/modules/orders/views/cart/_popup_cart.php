<p><?php echo Yii::t('main','Shopping cart', array(), null, $lng);?> <b><?php echo Yii::t('main','items', array(), null, $lng);?>: <?php echo Yii::app()->cart->countItems() ?></b></p>
<p><?php echo Yii::t('main','for the amount', array(), null, $lng);?> <b class="price">
	<?php echo StoreProduct::formatPrice(Yii::app()->currency->convert(Yii::app()->cart->getTotalPrice()), true) ?>
</b></p>



