<div>
<!-- breadcrumbs (begin) -->
    <ul class="breadcrumbs">
        <li><a href="/" title=""><?=Yii::t('OrdersModule.core','Home')?></a></li>
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
        <div class="step3">
            <b>3</b>
            <p><?php echo Yii::t('OrdersModule.core','Payment')?></p>
        </div>
        <div class="step4 active">
            <b>4</b>
            <p><?php echo Yii::t('OrdersModule.core','Done')?></p>
        </div>
    </div>
    <!-- steps (end) -->
	<h1 class="page-title"><?php echo Yii::t('OrdersModule.core','Your order')?></h1>
	<div class="cart3 g-clearfix"></div>
<div class="cart4">
    <h2 class="title"><?=Yii::t('OrdersModule.core','Congratulations! Your order is successfully placed and paid.')?></h2>

    <div class="g-clearfix">
        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','Order details')?></div>
            <ul class="cart-details">
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient name:')?><b><?=$model->receiver_name ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Country:')?><b><?=$model->country?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','City:')?> <b><?=$model->city ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Recipient address:')?> <b><?=$model->user_address ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;1:')?> <b><?=$model->phone1 ?></b></p>
                    <p><?=Yii::t('OrdersModule.core','Phone &#8470;2:')?> <b><?=$model->phone2?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Date and time of delivery:')?> <b><?=$model->datetime_del?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Additional Information:')?><b><? if($model->user_comment) echo $model->user_comment; else echo "---" ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Make a photo of the recipient:')?> <b><?php if($model->doPhoto) echo Yii::t('OrdersModule.core', 'Yes'); else echo Yii::t('OrdersModule.core', 'No');?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card:')?> <b><?php if($model->do_card) echo Yii::t('OrdersModule.core', 'Yes'); else echo Yii::t('OrdersModule.core', 'No'); ?></b></p>
                </li>
                <li>
                    <p><?=Yii::t('OrdersModule.core','Greeting card text:')?><b><? if($model->do_card) echo $model->card_text; else echo "---";?></b></p>
                </li>
            </ul>
        </div>
        <div class="cart-col">
            <div class="sub-title"><?=Yii::t('OrdersModule.core','The recipient will be awarded:')?></div>
        <ul class="cart-products">
            <?php foreach($model->getOrderedProducts()->getData() as $product): ?>
                <li>
                    <div class="visual">
                        <?php
                        $pro_model = StoreProduct::model()->findByPk($product->product_id);
                        ?>
                        <a href="/product/<?=$pro_model->url?>.html" title="">
                            <img src="<?=$pro_model->mainImage->getUrl('85x85', 'resize')?>"/>
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

</div>