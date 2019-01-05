<?php

/**
 * Display cart
 * @var Controller $this
 * @var SCart $cart
 * @var $totalPrice integer
 */

Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/cart.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('cartScript', "var orderTotalPrice = '$totalPrice';", CClientScript::POS_HEAD);

$this->pageTitle = Yii::t('OrdersModule.core', 'Checkout');

if(empty($items))
{
echo '<ul class="breadcrumbs">
        <li><a href="' . Yii::app()->createUrl('/') . '">'.Yii::t('main','Home page').'</a></li>
    </ul>';
    echo CHtml::openTag('h1');
        echo Yii::t('OrdersModule.core', 'Your shopping cart is currently empty.');
    echo CHtml::closeTag('h1');
	echo Yii::t('OrdersModule.core', '<br><br><p>Please add items to your cart before checking out.</p>');
    if(!empty($popular)){
        shuffle($popular);
?>
	 <!-- col-1 (begin)  -->
	  <div class="col-1">
	
	 <!-- products (begin) -->
<div class="products g-clearfix" style="margin: 20px auto;">
    <?php
    $lang= Yii::app()->language;
    if($lang == 'ua')
        $lang = 'uk';
    $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
    foreach($popular as $p)
        $this->renderPartial('_product', array('data'=>$p, 'langArray' => $langArray));
    ?>
</div>
	<div class="text-center btn-purple"><a href="<?=Yii::app()->createUrl('/flowers/');?>"><?=Yii::t('main','All Our Flowers')?></a></div><br>
	  <!-- products (end) -->
	  	        
	    </div>
	    <!-- col-1 (end) -->
<?php
    }
    return;
}
?>

<?php echo CHtml::form() ?>

	<script type="text/javascript" >
		$(document).ready(function() {
			$('.minus').click(function () {
				var $input = $(this).parent().find('input');
				var count = parseInt($input.val()) - 1;
				count = count < 1 ? 1 : count;
				$input.val(count);
				$input.change();
				return false;
			});
			$('.plus').click(function () {
				var $input = $(this).parent().find('input');
				$input.val(parseInt($input.val()) + 1);
				$input.change();
				return false;
			});
		});
	</script>
<div id="step1">
    <!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="<?=Yii::app()->createUrl('/'); ?>" title=""><?=Yii::t('OrdersModule.core','Home')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?=Yii::t('OrdersModule.core','Cart')?></li>
    </ul>
    <!-- breadcrumbs (end) -->

    <!-- steps (begin) -->
    <div class="steps">
        <div class="step1 active">
            <b>1</b>
            <p><?php echo Yii::t('OrdersModule.core','Your order')?></p>
        </div>
        <div class="step2">
            <b>2</b>
            <p><?php echo Yii::t('OrdersModule.core','Checkout')?></p>
        </div>
        <div class="step3">
            <b>3</b>
            <p><?php echo Yii::t('OrdersModule.core','Payment')?></p>
        </div>
        <div class="step4">
            <b>4</b>
            <p><?php echo Yii::t('OrdersModule.core','Done')?></p>
        </div>
    </div>
    <!-- steps (end) -->

    <h1 class="page-title"><?php echo Yii::t('OrdersModule.core','Your order')?></h1>

    <!-- cart-table (begin) -->
    <div class="cart-table">
            <table>
                <thead>
                <tr>
                    <td class="ctab-img"><?php echo Yii::t('OrdersModule.core','Photo')?></td>
                    <td class="ctab-name"><?php echo Yii::t('OrdersModule.core','Title')?></td>
                    <td><?php echo Yii::t('OrdersModule.core','Quantity')?></td>
                    <td><?php echo Yii::t('OrdersModule.core','Sum')?></td>
                    <td><?php echo Yii::t('OrdersModule.core','Action')?></td>
                </tr>
                </thead>
                <tfoot>
                <tr>
                <td colspan="2">
                    <div style="text-align: left; padding:40px 0px 0px;">   
						<div id="mess"></div>
						<div class="tooltip" title="Greeting card text">
                        <div class="tip-info"><?php echo Yii::t('OrdersModule.core','Please register, to use gift coupon')?></div>
                    </div>
						<div style="display: inline-block; margin-left:5px;"><?php echo CHtml::activeTextField($this->form,'coupon', array('placeholder'=>Yii::t('OrdersModule.core','Enter Coupon or Promo code'))); ?>
						<?php echo CHtml::error($this->form,'coupon'); ?>
						</div>					
					<div style="display: inline-block;"><input disabled class="btn-green btn-to-buy recoun2" id="goStep3" type="submit" value="<?php echo Yii::t('OrdersModule.core','Apply')?>"/></div>
					</div>
				</td>
				<td>
					<div style="display: inline-block; padding:40px 0px 0px;"><button disabled  style="background-color:#999;" class="btn-green btn-to-buy recount" name="recount" type="" value="1"><?php echo Yii::t('OrdersModule.core','Update Cart')?></button></div>
                
				</td>
				<td colspan=2>
				<div class="total">
                            
							<?php if(!Yii::app()->user->isGuest){
								$regular_discount = DiscountRegular::checkDiscount($totalPrice);
							}
							if($regular_discount != false){
								echo '<span style="font-weight:bold; margin-right:10px;">'.Yii::t('OrdersModule.core','Total Order').':</span>';
								echo '<span  class="price" id="price_res">'.StoreProduct::formatPrice($regular_discount['result'], true).'</span>';
								echo '<span style="text-decoration:line-through; padding:0 10px 0;">'.StoreProduct::formatPrice($totalPrice, true).'</span>';
							}  else {
								echo '<span style="font-weight:bold; margin-right:10px;">'.Yii::t('OrdersModule.core','Total Order').':</span>';
								echo '<span  class="price">'.StoreProduct::formatPrice($totalPrice, true).'</span>';
							}?>
                </div>
				<?php if(Yii::app()->user->isGuest){?>
				<input type="hidden" id="hidden_price" name="price" value="<?php echo StoreProduct::formatPrice($totalPrice, true);?>">
				<?php }?>

				<input style="float:right; margin-right:40px;" class="btn-green btn-to-buy btntostep2" type="submit" id="goStep2" value="<?php echo Yii::t('OrdersModule.core','Proceed to Checkout')?>"/>

				
				</td>
                </tr>
                </tfoot>
				
				<tfoot>
				</tfoot>
				 
                <tbody>
                <?php foreach($items as $index=>$product): ?>
				
                <tr>
                    <td class="ctab-img">
                        <a href="#" title="" rel="nofollow">
                            <?php
                                if($product['model']->mainImage)
                                    $imgSource = $product['model']->mainImage->getUrl('69x69');
                                else
                                    $imgSource = 'http://placehold.it/69x69/ffffff?text=7Roses';
                                echo CHtml::image($imgSource, '');
                            ?>
                        </a>
                    </td>
                    <td>
                        <?php 
                        echo CHtml::link(CHtml::encode($product['translation']->name), array('/store/frontProduct/view', 'url'=>$product['model']->url));
                        if(!empty($product['variant_models']))
                        {
                            echo "<br/>".CHtml::openTag('span', array('class'=>'cartProductOptions'));
                            foreach($product['variant_models'] as $variant)
                                echo $variant->option->value.' '.$variant->attribute->title.'<br/>';
                            echo CHtml::closeTag('span');
                        }
                        
                        ?>
                        <div class="price">
                            <?php $price = StoreProduct::calculatePrices($product['model'], $product['variant_models'], $product['configurable_id']);
                            echo StoreProduct::formatPrice(Yii::app()->currency->convert($price), true);
                            ?>
							
                        </div>
                    </td>
					
					
                    <td class="ctab-input">
					
					<span class="minus">-</span>
                        <?= ($product['is_sale'] == 0)
                            ? CHtml::textField("quantities[$index]", $product['quantity'], array('class'=>'check-symbol'))
                            : CHtml::hiddenField("quantities[$index]", $product['quantity']);
                        ?>
					<span class="plus">+</span>
                    </td>
					

                    <td>
                        <div class="price">
                        <?php
                        echo StoreProduct::formatPrice(Yii::app()->currency->convert($price * $product['quantity']), true);
                        ?>
                        </div>
                    </td>
                    <td class="ctab-delete">
                        <?= ($product['is_sale'] == 0)
                            ? CHtml::link('&nbsp;', array('/orders/cart/remove', 'index'=>$index))
                            : '';
                        ?>
                    </td>
                </tr>
                <?php endforeach ?>

				<?php if($regular_discount != false){?>
				<tr>
					<td>
						<img style="width: 100px;" src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/discount.jpg" alt="7roses" />
                    </td>
                    <td>
						<?php echo'<div><a href="#">'.Yii::t('OrdersModule.core','Your Discount').'</a></div><div class="price">'. $regular_discount['percent'] .'% OFF </div>';?>
                    </td>
					<td></td>
					<td>
						<?php echo '<div class="price">-'.StoreProduct::formatPrice($regular_discount['minus'], true).'</div>';?>
                    </td>
					<td></td>
                </tr>
				<tr id="mess_dis" style="display: none;">
					<td>
						<img style="width: 100px;" src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/discount.jpg" alt="7roses" />
                    </td>
                    <td>
						<?php echo '<div><a href="#">' .Yii::t('OrdersModule.core','Your promo discount').'</a></div><div class="price" style="display: -webkit-inline-box;" id="discount_mess"></div>';?>
                    </td>
					<td></td>
					<td>
						<div class="price"><div id="minus"></div></div>
                    </td>
					<td></td>
                </tr>
				
				<?php }?>
                </tbody>
            </table>
    </div>
    <!-- cart-table (end) -->

</div>

<div id="step2">
    <!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="/" title=""><?=Yii::t('OrdersModule.core','Home')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?=Yii::t('OrdersModule.core','Cart')?></li>
    </ul>
    <!-- breadcrumbs (end) -->

    <!-- steps (begin) -->
    <div class="steps">
        <div class="step1">
            <b>1</b>
            <p><?php echo Yii::t('OrdersModule.core','Your order')?></p>
        </div>
        <div class="step2 active">
            <b>2</b>
            <p><?php echo Yii::t('OrdersModule.core','Checkout')?></p>
        </div>
        <div class="step3">
            <b>3</b>
            <p><?php echo Yii::t('OrdersModule.core','Payment')?></p>
        </div>
        <div class="step4">
            <b>4</b>
            <p><?php echo Yii::t('OrdersModule.core','Done')?></p>
        </div>
    </div>
    <!-- steps (end) -->

    <h1 class="page-title"><?php echo Yii::t('OrdersModule.core','Your order')?></h1>

    <div class="cart2 g-clearfix">
        
            <?php echo CHtml::errorSummary($this->form); ?>
            
            <!-- data-form (begin) -->
            <div class="data-form">
                <b class="title"><?=Yii::t('OrdersModule.core','Recipient details')?></b>
                <div class="s2">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Recipient name')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'receiver_name'); ?>
                    <?php echo CHtml::error($this->form,'receiver_name'); ?>
                </div>
                <div class="s1">
                    <?php /*div class="sort sort-reg">
                        <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php'); ?>
                    </div*/?>
                    <?php $this->renderPartial('_cities'); ?>
                </div>
                <div class="s2">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Delivery address')?>:</span>
                    <?php echo CHtml::activeTextArea($this->form,'address'); ?>
                    <?php echo CHtml::error($this->form,'address'); ?>
                </div>
                <div class="s1">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Recipient phone &#8470;1')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'phone1'); ?>
                    <?php echo CHtml::error($this->form,'phone1'); ?>
                </div>
                <div class="s1">
                    <span class="input-title"><?=Yii::t('OrdersModule.core','Recipient phone &#8470;2')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'phone2'); ?>
                    <?php echo CHtml::error($this->form,'phone2'); ?>
                </div>
                <div class="s1">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Delivery Date')?>:</span>
                    
                    <?php $lang="ru";
                            if(Yii::app()->language=='en')
                                $lang="";
                            else if(Yii::app()->language=='ua')
                                $lang="uk";

                    date_default_timezone_set('Europe/Kiev');?>
					
					<?
                    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                        $this->widget('CJuiDateTimePicker',array(
                            'model'=>$this->form, 
                            'attribute'=>'datetime_delivery', 
                            'mode'=>'date',
                            'language' => $lang, 
                            'options'=>array(
                            'minDate' => ((int)date('G') < 14) ? '0' : '1',
							'dateFormat' => 'dd/mm/yy',
							'showOtherMonths'=>true,// Show Other month in jquery
							'selectOtherMonths'=>true,// Select Other month in jquery
                            ),
                            'htmlOptions' => array(
                                'class' => 'datepicker'
                            ), 
                        ));
                    ?>
                    <?php echo CHtml::error($this->form,'datetime_delivery'); ?>
                </div>
				<div class="s2">
                    <?php echo CHtml::activeCheckBox($this->form,'doPhoto'); ?>
                    <label for="photo"><?=Yii::t('OrdersModule.core','Photo of the recipient')." <span class='price'>".StoreProduct::formatPrice($photoPrice*$rate, true)?></span></label>
                </div>
				<br>
                 <div class="s2">
                    <?php echo CHtml::activeCheckBox($this->form,'do_card'); ?>
                    <label for="photo"><?=Yii::t('OrdersModule.core','Greeting card')." <span class='price'>".StoreProduct::formatPrice($cardPrice*$rate, true)?></span></label>
                </div>
				  <div class="s2">
                    <span class="input-title"><?=Yii::t('OrdersModule.core','Greeting card text')?>:</span>
                    <?php echo CHtml::activeTextArea($this->form,'card_text'); ?>
                    <?php echo CHtml::error($this->form,'card_text'); ?>
                    <div class="tooltip" title="Greeting card text">
                        <div class="tip-info"><?=Yii::t('OrdersModule.core','Please enter the message for you greeting card')?></div>
                    </div>
                </div>
				<?php if ($lang=='') { ?>
				<div class="s2">
                    <?php echo CHtml::activeCheckBox($this->form,'card_transl'); ?>
                    <label for="note"><?=Yii::t('OrdersModule.core','Please translate this message from English to Russian')." <span class='price'>".StoreProduct::formatPrice($translPrice*$rate, true)?></span></label>
                </div>
			<?	} ?>
                <div class="s2">
                    <span class="input-title"><?=Yii::t('OrdersModule.core','Additional Information')?>:</span>
                    <?php echo CHtml::activeTextArea($this->form,'comment'); ?>
                    <?php echo CHtml::error($this->form,'comment'); ?>
                    <div class="tooltip" title="Additional info">
                        <div class="tip-info"><?=Yii::t('OrdersModule.core','Please add information in case you have it')?></div>
                    </div>
                </div> 
            </div>
            <!-- data-form (end) -->
            <!-- data-form (begin) -->
            <div class="data-form">
                <b class="title"><?=Yii::t('OrdersModule.core','Your contact information')?>:</b>
                <div class="s2">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Your name')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'name'); ?>
                    <?php echo CHtml::error($this->form,'name'); ?>
                </div>
                <div class="s1">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Country')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'country'); ?>
                    <?php echo CHtml::error($this->form,'country'); ?>
                </div>
                <div class="s1">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','City')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'city'); ?>
                    <?php echo CHtml::error($this->form,'city'); ?>
                </div>
                <div class="s2">
                    <span class="input-title"><span class="req">*</span>E-mail:</span>
                    <?php echo CHtml::activeTextField($this->form,'email'); ?>
                    <?php echo CHtml::error($this->form,'email'); ?>
                </div>
                <div class="s2">
                    <span class="input-title"><span class="req">*</span><?=Yii::t('OrdersModule.core','Phone')?>:</span>
                    <?php echo CHtml::activeTextField($this->form,'phone'); ?>
                    <?php echo CHtml::error($this->form,'phone'); ?>
                </div>  
            </div>
			<input type="hidden" id="hidden_price" name="price" value="<?php echo StoreProduct::formatPrice($totalPrice, true);?>">
			<?php if($regular_discount != false){?>
			<input type="hidden" id="discount_price" name="discount" value="<?php echo $regular_discount['percent'];?>">
			<input type="hidden" id="discount_price" name="discount_minus" value="<?php echo $regular_discount['minus'];?>">
			<input type="hidden" id="new_order" name="new_order" value="true">
			<?php }?>
			
            <!-- data-form (end) -->
            <div class="links">
                <a class="link-prev" href="#" title="" id="backToStep1"><?=Yii::t('OrdersModule.core','Back')?></a>
                <button class="link-next" type="submit" name="create" value="1"><?=Yii::t('OrdersModule.core','Order')?></button>
            </div>
    </div>
</div>
<input type="hidden" id="discount_promo" name="discount_promo" value="">
			<input type="hidden" id="discount_minus_promo" name="discount_minus_promo" value="">
<?php echo CHtml::endForm() ?>

<!-- related-products (begin) -->
<?php $this->renderPartial('_extras'); ?>
<!-- related-products (end) -->

<script type="text/javascript">
$(document).ready(function(){
    
    <?php if(Yii::app()->request->isPostRequest && Yii::app()->request->getPost('create')):?>
        
        $("#step1").hide();
        $("#step2").show();
        
    <?php else:?>
    
    $("#step2").hide();
    //$(".recount").hide();
    
    <?php endif;?>
    
    $("#goStep2").click(function(e){
        e.preventDefault();
        
        $("#step1").hide();
        $(".related-products").hide();
        $("#step2").show();
		price_1 = $("#price_res").text();
		$("#hidden_price").val(price_1);
		
    });
	
    
    $("#backToStep1").click(function(e){
        e.preventDefault();
        
        $("#step2").hide();
        $("#step1").show();
        $(".related-products").show();
    });
    $(".plus").click(function(e){
        e.preventDefault();
		$(".recount").prop('disabled', false);
		$(".recount").css('background-color', '#45ae5b');
		//$(".btntostep2").hide();
        //$(".recount").show();
    });
	$(".minus").click(function(e){
        e.preventDefault();
		$(".recount").prop('disabled', false);
		$(".recount").css('background-color', '#45ae5b');
        //$(".btntostep2").hide();
        //$(".recount").show();
        //location.reload();
        
    });

	$("#OrderCreateForm_coupon").on('input', function () {
		$("#goStep3").prop('disabled', false);
    });
	
	$("#goStep3").click(function(e){
        e.preventDefault();
		$("#goStep2").show();

		code = $("#OrderCreateForm_coupon").val();
		price_1 = $("#price_res").text();
		price = price_1.substring(1);
		postForm = {
            'code': code,
			'price': price,
        };

		$.ajax({
        url: "/cart/GetCouponDiscount",
        type: "post",
        data: postForm ,
        success: function (response) {
			var returnedData = JSON.parse(response);
			var trimmed = response.substring(1);
			if(trimmed != '0.00'){
				if(returnedData['percent'] == null){
					$("#mess").text("We're sorry, this code is invalid or has expired.");
					$("#mess").show();
				}else{
					console.log(returnedData['minus'].substring(1));
					$("#discount_minus_promo").val(returnedData['minus'].substring(1));
					$("#discount_promo").val(returnedData['percent']);
					$("#mess").hide();
					$("#price_res").text(returnedData['price']);
					$("#discount_mess").text(returnedData['percent']+'% OFF');
					$("#minus").text('-'+returnedData['minus']);
					$("#mess_dis").css({"display": "table-row"});
					$("#goStep3").prop('disabled', true);
					//$("#OrderCreateForm_coupon").prop('disabled', true);
					//history.pushState(null, null, '/cart?code='+code);
				}
			}else{
				console.log('error');
			}
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }


    });
    });
})
</script>