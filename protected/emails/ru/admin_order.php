<?php
header("Content-type: text/html");
echo "<html><body>";
	echo "<h2>New Order #: ".$order->id." to ".$order->receiver_city."</h2>";
    echo "<p>Delivery Date: ".$order->datetime_del."<br>";
	echo "Delivery Fee: ".$model->delivery_price." USD</p><br>";
	echo "<h3>Recipient info</h3>";
    echo "Recipient name: ".$order->receiver_name."<br>";
    echo "Address: ".$order->user_address."<br>";
    echo "Phone #1: ".$order->phone1."<br>";
    echo "Phone #2: ".$order->phone2."<br>";
    echo "Additional Info: ".$order->user_comment."<br>";
    echo "Greeting Card: ".$order->card_text;

    echo "<h3>Client info</h3>";
    echo "Name: ".$order->user_name."<br>";
    echo "Email: ".$order->user_email."<br>";
    echo "Country: ".$order->country."<br>";
    echo "City : ".$order->city."<br>";
    echo "Phone: ".$order->user_phone;
	
	echo "<h3>Order summary</h3>";
		$i = 1;
	 foreach($model->getOrderedProducts()->getData() as $product): 
	 
      $pro_model = StoreProduct::model()->findByPk($product->product_id);
		echo "<p>$i. ".$product->getRenderFullName(false);   echo " - ".$product->price." USD</p>";
		if ($product->quantity>1)
		echo "<p>Quantity:".$product->quantity."</p>";
		$i++;
     endforeach;
	 
	if ($order->doPhoto == 1)	echo "<p>$i. Photo of the delivery! </p>";
	$i++;
	if ($order->do_card == 1)	echo "<p>$i. Greeting card! </p>";
	$i++;
	if ($order->card_transl == 1)	echo "<p>$i. Translation! </p>";
	 echo "<p>Total: ".$order->total_price." USD<br>";
	 $url = $this->createAbsoluteUrl('view', array('secret_key'=>$order->secret_key));
	 echo "<br><a href='".$url."'>Order Details</a>";
	 echo "</body></html>";
?>
