<html>
<body>

  <p>Dear, <?=$order->user_name?>.</p>
  <p>Thank you for placing order with 7Roses.</p>
  <p>Your order number is #<?=$order->id?>.</p>

  <p>
    You can check the status and details of your order here::<br>
   <a href="<?= $this->createAbsoluteUrl('view', array('secret_key'=>$order->secret_key)) ?>">
     <?= $this->createAbsoluteUrl('view', array('secret_key'=>$order->secret_key)) ?>
   </a>
  </p>

  <p>
    <ul>
    <?php foreach ($order->products as $product) {
        echo '<li>'.$product->getRenderFullName()."</li>";
		if ($product->quantity>1)
		echo "<p>Quantity:".$product->quantity."</p>"; 
		}
    ?>
    </ul>
    
    <p>
      <b>Total:</b>
      <?=Yii::app()->currency->main->symbol?><?=StoreProduct::formatPrice($order->total_price + $order->delivery_price)?> <?=Yii::app()->currency->main->iso?>
    </p>

    <p>
      <b>Best Regards,<br/>
    7Roses<br/>
    Ukraine<br/>
	+38(050)56 20 799
    </p>

  </p>
</body>
</html>