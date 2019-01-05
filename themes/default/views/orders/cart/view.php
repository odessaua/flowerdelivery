<?php
/**
 * Display cart
 * @var CartController $model
 */

$rate =Yii::app()->currency->active->rate; // курс текущей валюты к USD
$uah_full_price = Yii::app()->currency->convert($model->total_price, 2); // полная стоимость заказа (товары + доставка) в UAH для платёжных систем Украины
?>
<style>

input[type=button], input[type=submit], input[type=reset], button, .button {
	padding: 10px 20px 5px 20px;
	margin: 3px 4px;
	display: inline-block;
	color: #6F6D6D;
	font-size: 25px;
	cursor: pointer;
	background: #F3F5F8;
	background: linear-gradient(top, #F3F5F8 0%, #dadcdf 100%);
	background: -moz-linear-gradient(top, #F3F5F8 0%, #dadcdf 100%);
	background: -webkit-linear-gradient(top, #F3F5F8 0%, #dadcdf 100%);
	background: -o-linear-gradient(top, #F3F5F8 0%, #dadcdf 100%);
	border: 1px solid #DDDDE0;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
	box-shadow:0px 0px 2px 1px rgba(141, 139, 139, 0.25), inset 1px 1px 0px 0px rgba(255, 255, 255, 0.25);
	-moz-box-shadow:0px 0px 2px 1px rgba(141, 139, 139, 0.25), inset 1px 1px 0px 0px rgba(255, 255, 255, 0.25);
	-webkit-box-shadow:0px 0px 2px 1px rgba(141, 139, 139, 0.25), inset 1px 1px 0px 0px rgba(255, 255, 255, 0.25);
	-o-box-shadow:0px 0px 2px 1px rgba(141, 139, 139, 0.25), inset 1px 1px 0px 0px rgba(255, 255, 255, 0.25);
	text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.50);
}
input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover, button:hover, .button:hover {
	background: linear-gradient(top, #ffffff 0%, #F3F5F8 100%);
	background: -moz-linear-gradient(top, #ffffff 0%, #F3F5F8 100%);
	background: -webkit-linear-gradient(top, #ffffff 0%, #F3F5F8 100%);
	background: -o-linear-gradient(top, #ffffff 0%, #F3F5F8 100%);
}
input[type=button]:active, input[type=submit]:active, input[type=reset]:active, button:active, .button:active{
	opacity:0.8;
}

</style>
<div>
<!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="<?= Yii::app()->createUrl('/'); ?>" title=""><?=Yii::t('OrdersModule.core','Home')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?=Yii::t('OrdersModule.core','Cart')?></li>
    </ul>
    <!-- breadcrumbs (end) -->

    <!-- steps (begin) -->
    <div class="steps">
        <div class="step1 ">
            <b>1</b>
            <p><?php echo Yii::t('OrdersModule.core','Your order')?></p>
        </div>
        <div class="step2 ">
            <b>2</b>
            <p><?php echo Yii::t('OrdersModule.core','Checkout')?></p>
        </div>
        <div class="step3 <?= ($model->status_id != 6) ? 'active' : ''; ?>">
            <b>3</b>
            <p><?php echo Yii::t('OrdersModule.core','Payment')?></p>
        </div>
        <div class="step4 <?= ($model->status_id == 6) ? 'active' : ''; ?>">
            <b>4</b>
            <p><?php echo Yii::t('OrdersModule.core','Done')?></p>
        </div>
    </div>
    <!-- steps (end) -->

    <h1 class="page-title"><?php echo Yii::t('OrdersModule.core','Your order')?></h1>
<?php if($model->status_id == 1): ?>
    <?php
    $payments = StorePaymentMethod::model()->active()->findAll();
    $payments = (!empty($payments)) ? CArray::toolIndexArrayBy($payments, 'name') : array();
    ?>

    <?php if(($error_messages = Yii::app()->user->getFlash('error_messages'))): ?>
        <style>
            div.error-flash {
                color: red;
                background-color: #ffe6e6;
                border-color: red;
                clear: both;
            }
        </style>
        <div class="flash_messages error-flash">
            <button class="close">×</button>
            <?php
            if(is_array($error_messages))
                echo implode('<br>', $error_messages);
            else
                echo $error_messages;
            ?>
        </div>
    <?php endif; ?>

    <div class="cart3 g-clearfix">
       
        <div class="data-form data-form-big">
            <table cellpadding="5px" border=0><tr><td valign=top colspan=3>
            <p class="title"><? echo Yii::t('OrdersModule.core','Select Payment Method');?>:</p>
			<tr><td valign=top>
<ul class="payment-list">


<?php if(!empty($payments['WayForPay'])): ?>
                   <li class="selected">
                        <input type="radio" name="payment" id="payment4" value="<?= (!empty($payments['WayForPay']->id)) ? $payments['WayForPay']->id : 0; ?>" checked /><label for="payment4">
<div class="button"><img src="\uploads\visa_wayforpay75-150.png"></div></label>
                       <div class="help-tip">
					    <p><strong><?= strip_tags($payments['WayForPay']->name); ?></strong>: 
							<?php echo (!empty($payments['WayForPay']->description))? (strip_tags($payments['WayForPay']->description)): "Online credit card processing. All credit card transactions are encrypted. Accept Visa and MasterCard.";
							?> 
						</p>
                       </div>
							<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
					</li>				
					
<hr width=100%>
<?php endif; ?>


<?php if(!empty($payments['Portmone'])): ?>
                   <li>
                        <input type="radio" name="payment" id="payment1" value="<?= (!empty($payments['Portmone']->id)) ? $payments['Portmone']->id : 0; ?>" /><label for="payment1">					   
<div class="button"><img src="\uploads\visa_portmone75-150.png"></div></label>
                        <div class="help-tip">
						 <p><strong><?= strip_tags($payments['Portmone']->name); ?></strong>: 
						 
                            <?php echo (!empty($payments['Portmone']->description))? (strip_tags($payments['Portmone']->description)): "Online credit card processing. All credit card transactions are encrypted. Accept Visa and MasterCard.";
							?> 
							</p>
                        </div>
						<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
					</li>
<hr width=100%>
<?php endif; ?>


<?php if(!empty($payments['TransferWise'])): ?>
					<li>
					<input type="radio" name="payment" id="payment3" value="<?= (!empty($payments['TransferWise']->id)) ? $payments['TransferWise']->id : 0; ?>" /><label for="payment3">
<div class="button"><img src="\uploads\money_transferwise75-150.png"></div></label>
                       <div class="help-tip">
                        <?php if(!empty($payments['TransferWise'])): ?>
                            <p>
                                <strong>TransferWise</strong>: <?= strip_tags($payments['TransferWise']->description); ?>
                                <br><a href="https://transferwise.com/" target=_blank>https://transferwise.com</a>
                            </p>
                        <?php else: ?>
                            <p>
                                <strong>TransferWise</strong>: is an online money transfer service, which allows you to transfer money from your credit card directly to our bank account. <br>TransferWise fee 2% of the amount that's converted but not less then $1.7 USD
                                <br><a href="https://transferwise.com" target=_blank>https://transferwise.com</a>
                            </p>
                        <?php endif; ?>
                        </div>
						<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
						</li>
<hr width=100%>
<?php endif; ?>
<?php if(!empty($payments['PrivatBankTransfer'])): ?>
					<li>
					<input type="radio" name="payment" id="payment6" value="<?= (!empty($payments['PrivatBankTransfer']->id)) ? $payments['PrivatBankTransfer']->id : 0; ?>" /><label for="payment6">
<div class="button"><img src="\uploads\privat_75-150.png"></div></label>
                       <div class="help-tip">
                        <?php if(!empty($payments['PrivatBankTransfer'])): ?>
                            <p>
                                <?= strip_tags($payments['PrivatBankTransfer']->description); ?>
                            </p>
                        <?php else: ?>
                            <p>
                                <strong><?$payments['PrivatBankTransfer']->name; ?></strong
                                
                            </p>
                        <?php endif; ?>
                        </div>
						<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
						</li>
<hr width=100%>
<?php endif; ?>
<?php if(!empty($payments['Paypal'])): ?>
					<li>
					<input type="radio" name="payment" id="payment2" value="<?= (!empty($payments['Paypal']->id)) ? $payments['Paypal']->id : 0; ?>" /><label for="payment2">
<div class="button"><img src="\uploads\paypal75-150.png"></div></label>
                       <div class="help-tip">
                        <?php if(!empty($payments['paypal'])): ?>
                            <p>
                                <strong>Paypal</strong>: <?= strip_tags($payments['Paypal']->description); ?>
                                
                            </p>
                        <?php else: ?>
                            <p>
                                <strong>Paypal</strong>: Please enter the total order sum in USD and send it to order@7roses.com as donation. Enter the paying order number as instruction.</a>
                            </p>
                        <?php endif; ?>
                        </div>
						<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
						</li>
<hr width=100%>
<?php endif; ?>
<?php if(!empty($payments['WesternUnion'])): ?>
					<li>
					<input type="radio" name="payment" id="payment5" value="<?= (!empty($payments['WesternUnion']->id)) ? $payments['WesternUnion']->id : 0; ?>" />	<label for="payment5">
<div class="button"><img src="\uploads\western_union75-150.png"></div></label>
                       <div class="help-tip">
                        <?php if(!empty($payments['WesternUnion'])): ?>
                            <p>
                                <strong>Western Union</strong>: <?= strip_tags($payments['WesternUnion']->description); ?>
                                <br><a href="https://westernunion.com/" target=_blank>https://westernunion.com</a>
                            </p>
                        <?php else: ?>
                            <p>
                                <strong>Western Union</strong>: is an online money transfer service, which allows you to transfer money from your credit card directly to our country. 
                                <br><a href="https://westernunion.com" target=_blank>https://westernunion.com</a>
                            </p>
                        <?php endif; ?>
                        </div>
						<span class="price sum"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?>
						</li>
<?php endif; ?>
</ul>
            <div class="links">
                <a class="link-next" href="#" title=""><?=yii::t('OrdersModule.core','Pay')?></a>
            </div>
        </td><td><div style="padding:20px;"></div></td>
		<td valign=top>
<?php
$wfp_p_names = $wfp_p_qtys = $wfp_p_prices = array(); // инфа для WayForPay
?>
		<div class="cart-table-result">
		<table  cellpadding=8 border=1>
		<tr>
		<th colspan="3" bgcolor="#eaeae8"><div class="sub-title"><? echo Yii::t('OrdersModule.core','Your order number is: 	&#8470;').$model->id; ?></div></th></tr>
     
            <?php foreach($model->getOrderedProducts()->getData() as $product): ?>
                <?php
                // инфа для WayForPay
                $wfp_p_names[$product->product_id] = str_replace('"', '', $product->name);
                $wfp_p_qtys[$product->product_id] = $product->quantity;
                $wfp_p_prices[$product->product_id] = $product->price*$rate;
                ?>
                <tr><td width="85px" align="center">
                    <div class="visual">
                        <?php
                        $pro_model = StoreProduct::model()->findByPk($product->product_id);
						//var_dump ($product);
                        if($pro_model->mainImage) {
                            $imgSource = $pro_model->mainImage->getUrl('85x85', 'resize');
                            if(!file_exists('./' . $imgSource)) $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        }
                        else
                            $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        ?>
                        <a href="<?=Yii::app()->createUrl('/product/' . $pro_model->url . '.html'); ?>" title="">
                            <img src="<?= $imgSource; ?>" alt="<?=$product->getRenderFullName(false)?>"  title="<?=$product->getRenderFullName(false)?>"/>
                        </a>
                    </div></td><td>
                    <div class="carttext">
                        <div class="name"><?php echo $product->getRenderFullName(false); 
											if ($product->quantity>1) { echo "<p><strong>(Qty : ".$product->quantity.")</strong></p>";}?></div>
                    </div>
                </td><td width="30%"><span class="price"><?=StoreProduct::formatPrice($product->price*$rate*$product->quantity, true)?></span></td></tr>
            <?php endforeach ?>
			           			
            <?php if(!empty($model->do_card)) { ?>
			<tr><td width="40px" align="center"><img src="/uploads/mark.png" alt="Greeting Card" title="Greeting card" width=24 height=24 /></td>
			<?php if(!empty($model->card_transl)) 
				{ 
					$cardPrice = $model->card_price+$model->transl_price;
					$translation = Yii::t('OrdersModule.core',' with translation'); 
				} else { $cardPrice = $model->card_price; }
				
					?>		
			
			<td><div class="carttext"><? echo Yii::t('OrdersModule.core','Greeting card')?><? if(isset($translation)) echo $translation;?></div></td>
			<td width="25%"><span class="price"><?=StoreProduct::formatPrice($cardPrice*$rate, true)."</span></td></tr>"; }?>
			
            <?php if(!empty($model->doPhoto)){ ?>
			<tr><td width="40px" align="center"><img src="/uploads/mark.png" alt="Photo of delivery" title="Photo of delivery" width=24 height=24 /></td>
			<td><div class="carttext"><? echo Yii::t('OrdersModule.core','Photo of delivery')?></div></td>
			<td width="25%"><span class="price"><?=StoreProduct::formatPrice($model->photo_price*$rate, true)."</span></td></tr>"; }?>
			
 
			<tr><td width="40px" align="center"><img src="/uploads/mark.png" alt="Delivery fee" title="Delivery fee" width=24 height=24 /></td>
			<td><div class="carttext"><?echo Yii::t('OrdersModule.core','Delivery fee');?>	</div></td>		
			<td width="25%"><span class="price"><?php $delivery=$model->delivery_price; if ($delivery=='0') echo "FREE"; else echo StoreProduct::formatPrice($delivery*$rate, true)."</span></td></tr>";?>
			
			<?php if(!empty($discount)){ ?>

			<tr><td width="40px" align="center"><img src="/uploads/mark.png" alt="Delivery fee" title="Delivery fee" width=24 height=24 /></td>
			<td><div class="carttext"><?echo Yii::t('OrdersModule.core','Your Discount: ');?>	<?php echo $discount."%"?></div></td>		
			<td width="25%"><span class="price">-<?php echo StoreProduct::formatPrice($discount_minus, true);?></span></td></tr>
			<?php }?>
			<?php if(!empty($discount_promo)){ ?>

			<tr><td width="40px" align="center"><img src="/uploads/mark.png" alt="Delivery fee" title="Delivery fee" width=24 height=24 /></td>
			<td><div class="carttext"><?echo Yii::t('OrdersModule.core','Your promo discount: ');?><?php echo $discount_promo."%"?></div></td>		
			<td width="25%"><span class="price">-<?php echo StoreProduct::formatPrice($discount_minus_promo, true);?></span></td></tr>
			<?php }?>
			<tr>
			<td width="40px" align="center"><img src="/uploads/sum.png" alt="Total sum" title="Total sum" width=24 height=24 /></td>
			<td><span class="total"><?php echo Yii::t('OrdersModule.core','Order Total');?></span></td>
			<td width="25%"><div class="sum"><span class="price"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?></div>

			</td></tr>
			</table>
			</div>
        </td>
        </tr>
    </table>
        </div>
        <!-- data-form (end) -->
    </form>
</div>
<?php elseif($model->status_id == 6): ?>
<div class="cart4">
    <h2 class="title"><?=Yii::t('OrdersModule.core','Congratulations! Your order is accepted for processing.')?></h2>

    <div class="g-clearfix">
        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','Recipient details:')?></div>
            <ul class="cart-details">
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient name:')?> <b><?=$model->receiver_name ?></b></p>
                </li>
               
                <li>
                    <p><?=Yii::t('OrdersModule.core','City:')?> <b><?=$model->receiver_city ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient adress:')?> <b><?=$model->user_address ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;1:')?> <b><?=$model->phone1 ?></b></p>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;2:')?> <b><?=$model->phone2?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Delivery Date:')?> <b><?=$model->datetime_del?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Additional Information:')?><b><?=$model->user_comment ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Photo of the recipient:')?> <b><?php if($model->doPhoto) echo Yii::t('OrdersModule.core','Yes'); else echo Yii::t('OrdersModule.core','No'); ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card:')?> <b><?php if($model->do_card) echo Yii::t('OrdersModule.core','Yes'); else echo Yii::t('OrdersModule.core','No'); ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card text:')?><b><?=$model->card_text?></b></p>
                </li>
            </ul>
        </div>

        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','Order details')?>:</div>
        <ul class="cart-products">
            <?php foreach($model->getOrderedProducts()->getData() as $product): ?>
                <li>
                    <div class="visual">
                        <?php
                        $pro_model = StoreProduct::model()->findByPk($product->product_id);
                        if($pro_model->mainImage) {
                            $imgSource = $pro_model->mainImage->getUrl('85x85', 'resize');
                            if(!file_exists('./' . $imgSource)) $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        }
                        else
                            $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        ?>
                        <a href="<?=Yii::app()->createUrl('/product/' . $pro_model->url . '.html'); ?>" title="">
                            <img src="<?= $imgSource; ?>"/>
                        </a>
                    </div>
                    <div class="text">
                        <div class="name"><?php echo $product->getRenderFullName(false); ?></div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
            <div class="thanks">
                <?=Yii::t('OrdersModule.core','Thank you for usinig our service!<br>
			7Roses Team')?>
            </div>
        </div>
    </div>
</div>

<?php elseif($model->status_id == 5): ?>
<div class="cart4">
    <h2 class="title"><?=Yii::t('OrdersModule.core','The Order &#8470;'.$model->id.' is delivered.')?></h2>

    <div class="g-clearfix">
        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','Recipient details:')?></div>
            <ul class="cart-details">
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient name:')?> <b><?=$model->receiver_name ?></b></p>
                </li>
               
                <li>
                    <p><?=Yii::t('OrdersModule.core','City:')?> <b><?=$model->receiver_city ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient adress:')?> <b><?=$model->user_address ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;1:')?> <b><?=$model->phone1 ?></b></p>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;2:')?> <b><?=$model->phone2?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Delivery Date:')?> <b><?=$model->datetime_del?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Additional Information:')?><b><?=$model->user_comment ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Photo of the recipient:')?> <b><?php if($model->doPhoto) echo Yii::t('OrdersModule.core','Yes'); else echo Yii::t('OrdersModule.core','No'); ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card:')?> <b><?php if($model->do_card) echo Yii::t('OrdersModule.core','Yes'); else echo Yii::t('OrdersModule.core','No'); ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card text:')?><b><?=$model->card_text?></b></p>
                </li>
            </ul>
        </div>

        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','Order details')?>:</div>
        <ul class="cart-products">
            <?php foreach($model->getOrderedProducts()->getData() as $product): ?>
                <li>
                    <div class="visual">
                        <?php
                        $pro_model = StoreProduct::model()->findByPk($product->product_id);
                        if($pro_model->mainImage) {
                            $imgSource = $pro_model->mainImage->getUrl('85x85', 'resize');
                            if(!file_exists('./' . $imgSource)) $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        }
                        else
                            $imgSource = 'http://placehold.it/85x85/ffffff?text=7Roses';
                        ?>
                        <a href="<?=Yii::app()->createUrl('/product/' . $pro_model->url . '.html'); ?>" title="">
                            <img src="<?= $imgSource; ?>"/>
                        </a>
                    </div>
                    <div class="text">
                        <div class="name"><?php echo $product->getRenderFullName(false); ?></div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
            <div class="thanks">
                <?=Yii::t('OrdersModule.core','Thank you for usinig our service!<br>
			7Roses Team')?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>   


<div class="cart5" style="display: none;">
    <h2 class="title"><?=Yii::t('OrdersModule.core','TransferWise money transfer')?></h2>

    <div class="g-clearfix">
        
<p>You can pay for the delivery via a bank transfer, credit or debit card using TransferWise. </p>
<p>Once you setup an account you can add us as recipient. Here is the details for the money recipient information that TransferWise will request of you.</p>

<dl class="table-display">
  <dt>Full Name:</dt>
   <dd>Varetskaya Natalia</dd>
  <dt>Email:</dt>
   <dd>order@7roses.com</dd>
  <dt>Recipient Address:</dt>
   <dd>Deribasovskaya str. #12, apt. 25<br>65026, Odessa, Ukraine</dd>
  <dt>Phone number:</dt>
   <dd>+380505620799</dd>
  <dt>PrivatBank card issued	:</dt>
   <dd>5168 7573 7156 9205</dd>
</dl>
    </div>
	<hr style="width:500px; float: left;"><br>
<h2><?php echo Yii::t('OrdersModule.core','Order Total');?>: <span class="price"><?echo "&#36;".StoreProduct::formatPrice($model->total_price)."</span>" ;?></h2><br>
<p>After submiting this information, you will be directed to a page to send the
funds to a TransferWise account first and then they send the payment to Varetskaya Natalia. 

<div class="links">
                <a class="link-prev" href="#" onclick="window.location.reload(true);" title=""><?=yii::t('OrdersModule.core','BACK')?></a>
            
                <a class="link-next" href="https://transferwise.com/" target=_blank title=""><?=yii::t('OrdersModule.core','TransferWise')?></a>
            </div>
</div>

<div class="privat" style="display: none;">
    <h2 class="title"><?=Yii::t('OrdersModule.core','PrivatBank Money Transfer')?></h2>

    <div class="g-clearfix">

<dl class="table-display">
  <?=$payments['PrivatBankTransfer']->description?>
</dl>
    </div>
	<hr style="width:500px; float: left;"><br>
<h2><?php echo Yii::t('OrdersModule.core','Order Total');?>: <span class="price"><?echo StoreProduct::formatPrice($model->total_price*$rate, true)."</span> " ;?></h2><br>


<div class="links">
                <a class="link-prev" href="#" onclick="window.location.reload(true);" title=""><?=yii::t('OrdersModule.core','BACK')?></a>
            </div>
</div>
 
 <div class="wu" style="display: none;">
    <h2 class="title"><?=Yii::t('OrdersModule.core','Western Union money transfer')?></h2>

    <div class="g-clearfix">
        
<?=$payments['WesternUnion']->description?>
    </div>
	<hr style="width:500px; float: left;"><br>
<h2><?php echo Yii::t('OrdersModule.core','Order Total');?>: <span class="price"><?echo "&#36;".StoreProduct::formatPrice($model->total_price)."</span>" ;?></h2><br>
<p>After submiting this information, you will be directed to a page to send the money through Western Union. 

<div class="links">
                <a class="link-prev" href="#" onclick="window.location.reload(true);" title=""><?=yii::t('OrdersModule.core','BACK')?></a>
            
                <a class="link-next" href="https://www.westernunion.com/" target=_blank title=""><?=yii::t('OrdersModule.core','WesternUnion')?></a>
            </div>
</div>


</div>

<?php $formUrl = 'http' . ((strpos($_SERVER['HTTP_HOST'], '.loc') !== false) ? '' : 's') . '://' . $_SERVER['HTTP_HOST']; ?>

<form class="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blanc">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="M5BMF2Y4XWPBC">
    <input type="image" src="/uploads/payment-paypal.jpg" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<form class="portmone" action="https://www.portmone.com.ua/gateway/" method="post" name="paymentform">
    <input type="hidden" name="payee_id" value="2046">
    <input type="hidden" name="shop_order_number" value="<?=$model->id?>">
    <input type="hidden" name="bill_amount" value="<?= $uah_full_price; ?>">
    <input type="hidden" name="description" value="ATTENTION! Amount above is given in Ukraine currency calculated automatically according to the current rate of the Ukraine National Bank">
    <input type="hidden" name="success_url" value="<?= $formUrl; ?>/cart/view/<?=$model->secret_key?>/status/">
    <input type="hidden" name="failure_url" value="<?= $formUrl; ?>/cart/view/<?=$model->secret_key?>/status/">
    <INPUT TYPE="hidden" NAME="lang" VALUE="<?=Yii::app()->language?>">
    <input type="hidden" name="encoding" value="UTF-8" /> 
    <image width="282" height="100" src='/uploads/image.jpg' />
    <input type="submit" class="btn-purple" value="<?=Yii::t('main','Pay')?> ">
</form>

<?php
// WayForPay merchant info
$merchantDomainName = $_SERVER['HTTP_HOST'];
$wfp_type = 'form'; // form or widget
// merchant signature compilation
//$orderReference = $model->id;
//$orderReference = "ord_" . $model->id; // без префиксов ругается (1112) Duplicate Order ID
$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5); // length = 5
$orderReference = $randomString . "_" . $model->id; // рандомный префикс
$orderDate = strtotime($model->created);
$orderFullPrice = $uah_full_price;
//$orderFullPrice = "1"; // temp
$orderCurrency = "UAH";
$string = Yii::app()->params['merchantAccount'] . ";" . $merchantDomainName . ";" . $orderReference . ";" . $orderDate . ";" . $orderFullPrice . ";" . $orderCurrency;
$string .= (!empty($wfp_p_names)) ? ";" . implode(";", $wfp_p_names) : "";
$string .= (!empty($wfp_p_qtys)) ? ";" . implode(";", $wfp_p_qtys) : "";
$string .= (!empty($wfp_p_prices)) ? ";" . implode(";", $wfp_p_prices) : "";
//var_dump($string);
$merchantSignature = hash_hmac("md5", $string, Yii::app()->params['merchantSecretKey']);
?>

<?php if($wfp_type == 'widget'): ?>
<script id="widget-wfp-script" language="javascript" type="text/javascript" src="https://secure.wayforpay.com/server/pay-widget.js"></script>
<script type="text/javascript">
    var wayforpay = new Wayforpay();
    var wfpay = function () {
        wayforpay.run({
                merchantAccount : "<?=Yii::app()->params['merchantAccount'];?>",
                merchantDomainName : "<?=$merchantDomainName;?>",
                authorizationType : "SimpleSignature",
                merchantSignature : "<?=$merchantSignature;?>",
                orderReference : "<?=$orderReference;?>",
                orderDate : "<?=$orderDate;?>",
                amount : "<?=$orderFullPrice;?>",
                currency : "<?=$orderCurrency;?>",
                productName : [<?='"' . implode('","', $wfp_p_names) . '"';?>],
                productPrice : [<?=implode(',', $wfp_p_prices);?>],
                productCount : [<?=implode(',', $wfp_p_qtys);?>],
                clientFirstName : "<?=$model->user_name;?>",
                clientLastName : "<?='.';?>",
                clientEmail : "<?=$model->user_email;?>",
                clientPhone: "<?=(!empty($model->user_phone)) ? $model->user_phone : '380631234567';?>",
                language: "<?=strtoupper(Yii::app()->language);?>",
                returnUrl: "<?= $formUrl; ?>/cart/view/<?=$model->secret_key?>/status/"
            },
            function (response) {
                // on approved
                document.location.href = "<?= $formUrl; ?>/cart/view/<?=$model->secret_key?>/status/";
                //console.log('Approved: '+response);
            },
            function (response) {
                // on declined
                console.log('Declined: '+response);
            },
            function (response) {
                // on pending or in processing
                console.log('Pending or in Processing: '+response);
            }
        );
    }
</script>
<?php else: ?>
    <form action="https://secure.wayforpay.com/pay" method="post" style="float: left;" class="wayforpay">
        <input type="hidden" name="merchantAccount" value="<?=Yii::app()->params['merchantAccount']; ?>">
        <input type="hidden" name="merchantDomainName" value="<?=$merchantDomainName; ?>">
        <input type="hidden" name="merchantSignature" value="<?=$merchantSignature; ?>">
        <input type="hidden" name="merchantTransactionType" value="AUTO">
        <input type="hidden" name="merchantTransactionSecureType" value="AUTO">
        <input type="hidden" name="orderReference" value="<?=$orderReference; ?>">
        <input type="hidden" name="orderDate" value="<?=$orderDate; ?>">
        <input type="hidden" name="amount" value="<?=$orderFullPrice; ?>">
        <input type="hidden" name="currency" value="<?=$orderCurrency; ?>">
        <?php /*input type="hidden" name="productName[]" value="Apple iPhone 6 16GB">
        <input type="hidden" name="productPrice[]" value="1">
        <input type="hidden" name="productCount[]" value="1"*/?>
        <?php
        if(!empty($wfp_p_names)){
            foreach ($wfp_p_names as $w_name) {
                echo '<input type="hidden" name="productName[]" value="' . $w_name . '">' . "\n";
            }
        }
        if(!empty($wfp_p_prices)){
            foreach ($wfp_p_prices as $w_price) {
                echo '<input type="hidden" name="productPrice[]" value="' . $w_price . '">' . "\n";
            }
        }
        if(!empty($wfp_p_qtys)){
            foreach ($wfp_p_qtys as $w_qty) {
                echo '<input type="hidden" name="productCount[]" value="' . $w_qty . '">' . "\n";
            }
        }
        ?>
        <input type="hidden" name="clientFirstName" value="<?=$model->user_name;?>">
        <input type="hidden" name="clientLastName" value=".">
        <input type="hidden" name="clientPhone" value="<?=(!empty($model->user_phone)) ? $model->user_phone : '380631234567';?>">
        <input type="hidden" name="clientEmail" value="<?=$model->user_email;?>">
        <input type="hidden" name="returnUrl" value="<?= $formUrl; ?>/cart/view/<?=$model->secret_key?>/status/">
        <input type="hidden" name="serviceUrl" value="https://www.7roses.com/site/wfpresponse/">
        <input type="hidden" name="language" value="<?=strtoupper(Yii::app()->language);?>">
        <button type="submit" style="visibility: hidden;" class="btn btn-special btn-color">Оплатить</button>
    </form>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function(){
    // первый способ оплаты – активен (?)
//    $('.payment-list li').removeClass('selected'); // removes the "selected" class from all tabs
//    $('ul.payment-list li').first().addClass('selected');
//    $('ul.payment-list li').first().find('input[type=radio]').attr('checked', 'checked');

    // управление отображением элементов страницы
    $('.portmone').css('display','none');
    <?php if($model->status_id == 1): ?>
        $('.cart4').css('display','none');
    <?php elseif($model->status_id >= 1): ?>
        $('.cart3').css('display','none');
        $('.cart4').css('display','block');
    <?php endif; ?>
    $('.paypal').css('display','none');

    // выбор способа оплаты
    $('.payment-list li').click(function() {
        $('.payment-list li').removeClass('selected'); // removes the "selected" class from all tabs
        $(this).addClass('selected');
    });

    // отправка соответствующей формы для выбранной платёжной системы
    $(".link-next").click(function(){
        // фиксируем для заказа выбранный способ оплаты
        var spayment_id = $($('.selected').children()[0]).val(); // ID способа оплаты
        var sorder_id = <?=$model->id?>; // ID заказа
        $.ajax({
            type: "GET",
            url: "/site/setPaymentId/",
            data: { payment_id : spayment_id, order_id : sorder_id }
        });

        // отправляем форму
        if($($('.selected').children()[0]).attr('id')=="payment1")
            $('.portmone').submit(); // Portmone
        else if($($('.selected').children()[0]).attr('id')=="payment2")
            $('.paypal').submit(); // Paypal
		else if($($('.selected').children()[0]).attr('id')=="payment6"){
			 $('.cart3').css('display','none');
			 $('.cart4').css('display','block');
            $('.privat').css('display','block'); // Privat
		}
        else if($($('.selected').children()[0]).attr('id')=="payment3"){
            $('.cart3').css('display','none'); // TransferWise
            $('.cart4').css('display','block');
            $('.cart5').css('display','block');
        }
		else if($($('.selected').children()[0]).attr('id')=="payment5"){
            $('.cart3').css('display','none'); // WesternUnion
            $('.cart4').css('display','block');
            $('.wu').css('display','block');
        }
        else if($($('.selected').children()[0]).attr('id')=="payment4") {
            // WayForPay
            // сохраняем $orderReference и ID заказа в БД
            $.post(
                '/site/wfporder/',
                { orderReference : '<?=$orderReference;?>' }
            );
            // вызываем виджет – или отправляем форму
            <?=($wfp_type == 'widget') ? 'wfpay();' . "\n" : '$(\'.wayforpay\').submit();' . "\n";?>
        }
    });
});
</script>