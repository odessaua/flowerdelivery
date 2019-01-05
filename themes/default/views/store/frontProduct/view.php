	
<?php
/**
 * Product view
 * @var StoreProduct $model
 * @var $this Controller
 * @var $additional_images
 * @var $variants
 */
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// Set meta tags
$this->pageTitle = ($model->meta_title) ? $model->meta_title : $model->name;
$this->pageKeywords = $model->meta_keywords;
$this->pageDescription = $model->meta_description;

// Register main script
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/product.view.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/product.view.configurations.js', CClientScript::POS_END);

// Create breadcrumbs

$lang= Yii::app()->language;
if($lang == 'ua')
    $lang = 'uk';

$langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
 $categoryTrans=StoreCategoryTranslate::model()->findAllByAttributes(array('language_id'=>$langArray->id));
// Create breadcrumbs
$currentUrl = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$ancestors = $this->model->mainCategory()->excludeRoot()->ancestors()->findAll();

foreach($ancestors as $c){
    foreach($categoryTrans as $ct){
        if($ct->object_id==$c->id)
	   $this->breadcrumbs[$ct->name] = $c->getViewUrl();
    }
}

// get Main parent category for full breadcrumbs path
$parent_sql = 'select `spcr`.`category`, `sc`.`full_path`, `sct`.`name`
  from `StoreProductCategoryRef` `spcr`
  left join `StoreCategory` `sc` on `sc`.`id` = `spcr`.`category`
  left join `StoreCategoryTranslate` `sct` on `sct`.`object_id` = `spcr`.`category`
  where `spcr`.`product` = ' . (int)$model->id . '
    and `spcr`.`is_main` = 1
    and `sct`.`language_id` = ' . (int)$langArray->id . '
  limit 1';
$parent_command = Yii::app()->db->createCommand($parent_sql);
$parent = $parent_command->queryRow();

if(!in_array('/' . $parent['full_path'], $this->breadcrumbs)){
    $this->breadcrumbs[$parent['name']] = Yii::app()->createUrl('/' . $parent['full_path']);
}

$this->breadcrumbs[] = $model->name;



	$this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link(Yii::t('main','Home page'), array('/store/index/index')),
		'links'=>$this->breadcrumbs,
	));



// Fancybox ext
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.thumbnail',
    'config' => array('cyclic' => true),
));

// images alt & title
$img_alt = (!empty($model->img_alt)) ? $model->img_alt : $model->name;
$img_title = (!empty($model->img_title)) ? $model->img_title : $model->name;
?>


<div class="g-clearfix">
	
	<?php //$this->renderFile(Yii::getPathOfAlias('pages.views.pages.left_sidebar').'.php', array('popup'=>'city-product')); ?>
	
	<!-- products (begin) -->
	<div class="products">
	
	    <!-- h-pp (begin) -->
	    <div class="h-pp">
	        <div class="g-clearfix">
<!-- Additional images -->
            <?php if(!empty($additional_images)): ?>
            <div style="width: 60px; float: left;">
                <?php
                $hide_add_images = ' style="display: none;"';
                $add_images_limit = 3; // 4 real
                ?>
                <?php foreach($additional_images as $a_key => $a_image): ?>
                    <a href="<?=$a_image->source; ?>" class="thumbnail" rel="pthumbs" <?=($a_key > $add_images_limit) ? $hide_add_images : ''; ?>>
                        <img src="/uploads/products/_thumbs/<?= $a_image->source_filename; ?>" alt="<?=$img_alt;?> image <?=$a_key;?>" style="max-width: 55px; max-height: 70px; border: 1px solid #E0D6D6; margin-bottom: 5px;" title="<?=$img_title; ?>  image <?=$a_key;?>" />
                    </a>    
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
<!-- /Additional images -->
	            <div class="pp-left">
	                <?php
					// Main product image
					if($model->mainImage) {
                        $mainImg = $model->mainImage->getUrl('373x373', 'resize');
                        if(file_exists('.' . $mainImg)) {
                            echo CHtml::link(CHtml::image($mainImg, $img_alt, array('title' => $img_title)), $model->mainImage->getUrl(), array('class' => 'thumbnail', 'rel' => 'pthumbs'));
                        }
                        else{
                            echo CHtml::image('http://placehold.it/340x350/ffffff?text=7Roses', $img_alt, array('title' => $img_title));
                        }
                    }
					else
                        echo CHtml::image('http://placehold.it/340x350/ffffff?text=7Roses', $img_alt);
					?>
                    <?php
                    // Скины: акционный товар или старая цена, приоритет – у акционного товара
                    // для смены приоритета – поменять местами условия
                    $sale_img = $sale_alt = $sale_title = '';
                    if(!empty($model->sale_id)){
                        $sale_product = StoreProduct::getSale($model->sale_id);
                        $sale_img = $sale_product->mainImage->getUrl('100x100');
                        $sale_alt = $sale_title = $sale_product['name'];
                    }
                    elseif (!empty($model->old_price)){
                        $sale_img = Yii::app()->theme->baseUrl . '/assets/img/sale.png';
                        $sale_alt = $sale_title = Yii::t('StoreModule.core', 'Sale');
                    }
                    if(!empty($sale_img)):
                        ?>
                        <div class="sale-product-skin">
                            <img src="<?= $sale_img; ?>" alt="<?= $sale_alt; ?>" title="<?= $sale_title; ?>"/>
                        </div>
                    <?php endif; ?>
                    <?php if($model->short_description): ?>
                    <!-- <div class="number g-clearfix">
                        <div class="sort sort-size" style="float: left; margin-left: 80px;">
                            <a class="drop-link" href="#" title=""><?=Yii::t('StoreModule.core','Product Details')?></a>
                            <div class="sort-popup hidden">
                                <?=$model->short_description?>
                            </div>
                        </div>
                    </div> -->
                    <?php endif;?>
	            </div>
	            <div class="pp-right">
					
	                <?php echo CHtml::form(array('/orders/cart/add'))?>
					<div class="article"><?=Yii::t('StoreModule.core','Product code:')?> <span><?=$model->id?></span></div>
	                    <h1 class="page-title"><?php echo CHtml::encode($model->name); ?></h1>
	                    <div class="pp-price">
	                        <div class="currency">
	                            <?=Yii::t('StoreModule.core','')?>
	                            <select name="currency" id="selectCurrencyProduct">
	                            	<?php foreach(Yii::app()->currency->currencies as $currency):?>
									<option value="<?=$currency->id?>" <?=(Yii::app()->currency->active->id===$currency->id) ? "selected='selected'" : ""?>><?=$currency->iso?></option>		
									<?php endforeach;?>
	                            </select>
	                        </div>
	                        
	                        <div class="price">
								<span id="productPrice"><?php echo StoreProduct::formatPrice($model->toCurrentCurrency(), true); ?></span>
                                <span id="productPriceFormat" style="display: none;"><?= Yii::app()->currency->active->price_format; ?></span>
                                <?php if(!empty($model->old_price)): ?>
								<span class="product-old-price">
                                    <?= StoreProduct::formatPrice($model->toCurrentCurrency('old_price'), true); ?>
                                </span>
                                <?php endif; ?>
							</div>
	                        
	                    </div>
	                   <div class="pp-right-left">
	                    
	                    <?php
	                    if($model->getEavAttributes())
						{
							$this->widget('application.modules.store.widgets.SAttributesTableRenderer', array(
								'model'=>$model,
								'htmlOptions'=>array(
									'class'=>'attributes'
								),
							));

						}
						
	                    ?>

                    <?php if(!empty($variants)): // новые варианты товаров ?>
                        <?php $this->renderPartial('_variants', array('model'=>$model, 'variants'=>$variants)); ?>
                    <?php endif;?>
	                    
						</div>
					<div class="pp-right-right">	
	                    <?php if($model->is_active > 0): // покупка доступна только для активных товаров ?>

	                    <div class="pp-reg">
	                        <div class="sort sort-reg">

	                            <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php', array('no_redirect' => true)); ?>

	                        </div>
	                        <p><?=Yii::t('StoreModule.core','Assortment depends on the city')?></p>
	                    </div>
	                    <?php
							echo CHtml::hiddenField('product_id', $model->id);
							echo CHtml::hiddenField('product_price', $model->price);
							echo CHtml::hiddenField('use_configurations', $model->use_configurations);
							echo CHtml::hiddenField('currency_rate', Yii::app()->currency->active->rate);
							echo CHtml::hiddenField('configurable_id', 0);
							echo CHtml::hiddenField('quantity', 1);

							if($model->isAvailable)
							{
								echo CHtml::ajaxSubmitButton(Yii::t('StoreModule.core','Order'), Yii::app()->getBaseUrl(true) . '/cart/add/', array(
									'dataType' => 'json',
									'type'=>'post',
									'success'  => 'js:function(data, textStatus, jqXHR){processCartResponse(data, textStatus, jqXHR)}',
								), array(
									'id'=>'buyButton',
									'class'=>'btn-purple'
								));
							}
							else
							{
								echo CHtml::link(Yii::t('StoreModule.core','Report appearance'), '#', array(
									'onclick' => 'showNotifierPopup('.$model->id.'); return false;',
								));
							}
						?>
	                    <span id="phoneOrder" class="btn-green call-back-order"><?=Yii::t('StoreModule.core','Order by phone')?></span>
	                    <span class="link-del-way"><?=Yii::t('StoreModule.core','Payment methods')?></span>
                        <?php else: ?>
                        <div class="pp-reg">
	                        <div class="sort sort-reg">

	                            <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php', array('no_redirect' => true)); ?>

	                        </div>
	                        <p><?=Yii::t('StoreModule.core','Assortment depends on the city')?></p>
	                    </div>
	                    <?php
							echo CHtml::hiddenField('product_id', $model->id);
							echo CHtml::hiddenField('product_price', $model->price);
							echo CHtml::hiddenField('use_configurations', $model->use_configurations);
							echo CHtml::hiddenField('currency_rate', Yii::app()->currency->active->rate);
							echo CHtml::hiddenField('configurable_id', 0);
							echo CHtml::hiddenField('quantity', 1);

							if($model->isAvailable)
							{
								echo '<span disabled class="btn-inactive">'. Yii::t('StoreModule.core','Currently unavailable').'</span>';
							}
							else
							{
								echo CHtml::link(Yii::t('StoreModule.core','Report appearance'), '#', array(
									'onclick' => 'showNotifierPopup('.$model->id.'); return false;',
								));
							}
						?>
						<span id="phoneOrder" class="btn-green call-back-order"><?=Yii::t('StoreModule.core','Order by phone')?></span>
	                    <span class="link-del-way"><?=Yii::t('StoreModule.core','Payment methods')?></span>
                        <?php endif; ?>

	               <?php echo CHtml::endForm();?>
	            </div>
				</div>
	        </div>
	        <!-- b-page-text (begin) -->
	        <div class="b-page-text text ">
			<input id="tab1" type="radio" name="tabs" checked>
			<label for="tab1"><?=Yii::t('StoreModule.core','DESCRIPTION')?></label>

			  <input id="tab2" type="radio" name="tabs">
			  <label for="tab2"><?=Yii::t('StoreModule.core','PRODUCT DETAILS')?></label>

			  <input id="tab3" type="radio" name="tabs">
			  <label for="tab3"><?=Yii::t('StoreModule.core','SUBSTITUTION INFO')?></label>
		<section id="content1">
	            <p>
	                <?=$model->full_description?>
	            </p>
		</section>
		<section id="content2">
			<p>
			  <?=$model->short_description?>
			</p>
		</section>
		<section id="content3">
			<p>
			  <?=Yii::t('StoreModule.core','In the rare event that a substitution is necessary when buying a gift, products of similar style and quality will be used, and the value of that substitution will always be of the same or higher value.')?>
			</p>
		</section>
	        </div>
	        <!-- b-page-text (end) -->
	    </div>
	    <!-- h-pp (end) -->

        <!-- related-products (begin) -->
        <?php $this->renderPartial('webroot.themes.default.views.orders.cart._extras', array('noreload' => true)); ?>
        <!-- related-products (end) -->
	
	    <!-- b-last-photos (begin) -->
	    <div class="b-last-photos">
	    	<?php if(isset($photos) && !empty($photos)){ ?>
	        <h3 class="title"><?=Yii::t('StoreModule.core','Our Delivery Photos')?>:</h3>
	        <div class="g-clearfix">
	        	
		        	<?php foreach ($photos as $key => $value) { ?>
		            <div class="b-photo">
		                <div class="visual">
		                    <div class="img">
		                        <a href="<?php echo '/uploads/delivery/'.$value->photo;?>" class="thumbnail" rel="pthumbs"><img src="<?php echo '/uploads/delivery/'.$value->photo;?>" alt="<?=Yii::t('StoreModule.core','Delivery Photo')?>"/></a>
		                    </div>
		                </div>
		                <!-- <div class="title">г. Одесса</div> -->
		            </div>
		            <?php } ?>
	        </div>
			<div style="text-align:right"><a href="<?=Yii::app()->createUrl('/product/photos/id/'.$model->id)?>" title=""><?=Yii::t('StoreModule.core','View all delivery photos')?></a></div>
		 <?php } ?>
	    </div>
	    <!-- b-last-photos (end) -->
<!-- 	</div> -->
	<!-- products (end) -->
	
	<!-- comments (start) -->
	<h2 id="comments_title"><?=Yii::t('CommentsModule.core','Customer Reviews');?></h2>
<?php $this->renderPartial('comments.views.comment.create', array(
       	 'model'=>$model, // Commentable model instance
    	));?>
	<!-- comments (end) -->
</div>





<div class="hidden" >
    <!-- modal (begin) -->
    <div id="call-back-modal" class="box-modal call-back-modal">
        <div class="title"><?=Yii::t('StoreModule.core','Order by phone')?></div>
       <form id="phone-order-form" enctype = "multipart/form-data" action="<?=$currentUrl?>" method="POST">
            <input class="orderName" name= "orderName" type="text" placeholder="<?=Yii::t('StoreModule.core','Name')?>" required="required" />
            <input class="orderEmail" name="orderEmail" type="email" placeholder="E-mail" required="required" />
            <input class="orderPhone" name="orderPhone" type="text" placeholder="<?=Yii::t('StoreModule.core','Phone')?>" required="required" />
            <input id="submit_button" class="btn-purple" type="submit" value="<?=Yii::t('StoreModule.core','Submit')?>" />
		</form>
        </div>
    
    <!-- modal (end) -->
    <!-- modal (begin) -->
    <div id="payment-modal" class="box-modal payment-modal">
        <?php
        $this->renderPartial('_payments')// var_dump($this->language_info['id']);
        ?>
        
    </div>
    <!-- modal (end) -->
    
</div>

<script type="text/javascript">
$(document).ready(function(){
	
	var qty = $(".attributes td:eq(1)").text()+" роз";
	var position = $(".variantData option:contains('"+qty+"')").val()
	
	if(position){
		$(".variantData").val(position);
	}
	
});
var csrf='<?=Yii::app()->request->csrfToken?>';
var id='<?=$model->id?>';
var quantity=$('.number option:selected').text();
$('#submit_button').click(function(){
	$.ajax({
		type:'post',
		dataType:'json',
		data:{'YII_CSRF_TOKEN':csrf ,'email':$('.orderEmail').val(),'name':$('.orderName').val(),'phone':$('.orderPhone').val(),'id':id,'quantity':quantity},
		url:'/orders/cart/phone/',
		success:function(ev){console.log(ev);},
		error:function(er){console.log(er);}
	});

});
//
</script>