<?php
//var_dump($this->uniqueid, $this->action->Id);
	Yii::import('application.modules.users.forms.UserLoginForm');
	Yii::import('application.modules.store.models.StoreCategory');

	$assetsManager = Yii::app()->clientScript;
	$assetsManager->registerCoreScript('jquery');
	$assetsManager->registerCoreScript('jquery.ui');

	// jGrowl notifications
	Yii::import('ext.jgrowl.Jgrowl');
	Jgrowl::register();

$meta_page_title = CHtml::encode($this->pageTitle);
// Fancybox ext
$this->widget('application.extensions.fancybox.EFancyBox', array(
	'target'=>'a.thumbnail',
    'config' => array('cyclic' => true),
));
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $meta_page_title; ?></title>
	<meta charset="UTF-8"/>
    <meta name="title" content="<?php echo $meta_page_title; ?>">
	<meta name="description" content="<?php echo CHtml::encode($this->pageDescription) ?>">
	<meta name="keywords" content="<?php echo CHtml::encode($this->pageKeywords) ?>">
	<meta name="viewport" content="width=device-width">
	<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <?=$this->rels['prev']; // rel="prev" ?>
    <?=$this->rels['next']; // rel="next" ?>
	
    <!--[if lte IE 8]>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <![endif]-->
	
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/css/style.css"/>
    <?php
    // определение языка и переводы сообщений для JS
    $js_lang = (!empty(Yii::app()->language) && (Yii::app()->language != 'en'))
        ? '/' . Yii::app()->language
        : '';
    $js_jgrowl_checkout = Yii::t('main',
        'Item successfully added to the cart. <a href="{cart_url}">Checkout</a>',
        array('{cart_url}' => $js_lang . '/cart'));
    ?>
    <script type="text/javascript">
        var urlLang = '<?= $js_lang; ?>';
        var jgrowlCheckout = '<?= $js_jgrowl_checkout; ?>'
    </script>
    <script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/common.js<?= (!empty(Yii::app()->params['is_local'])) ? '?v=' . time() : ''; ?>"></script>
	<meta name="google-site-verification" content="4qXFsnDdApJ5tFBJH_zEc-p-11hOjk0GwEPAScAqIL0" />
    <link rel="shortcut icon" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon.png" type="image/x-icon" />
	
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon/apple-touch-icon.png">
	<meta name = "apple-mobile-web-app-title" content = "7Roses Flowers">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon/favicon-16x16.png">
	<link rel="manifest" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon/site.webmanifest">
	<link rel="mask-icon" href="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#b91d47">
	<meta name="theme-color" content="#ffffff">
	<meta name="application-name" content="7Roses Flowers">

	<?= $this->canonical; // rel="canonical" ?>
	<?= $this->hreflang; // rel="alternate" ... hreflang= ?>
	<meta property="og:url" content="<?=Yii::app()->request->hostInfo . Yii::app()->request->url ?>" />
	<meta property="og:title"              content="Ukraine Flower Delivery" />
	<meta property="og:description"        content="7Roses offers same day flower delivery service Ukraine wide" />
	<meta property="og:image"              content="https://7roses.com/uploads/logo-facebook.png" />
	<meta property="og:image:type" content="image/png" />
	<meta property="og:image:width" content="260" />
	<meta property="og:image:height" content="260" />

</head>
<body>

<div class="wrapper">

    <!-- header-top (begin) -->
    <div class="header-top">
        <div class="wrap g-clearfix">
            <div class="lang">
            	<?php 
                    $this->widget('application.components.widgets.LanguageSelector');
                ?>
                
            </div> 

            
            <!-- region-popup (begin) -->
            <div class="sort sort-reg del-reg">
                
                <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php', array('popup'=>'city-header')); ?>
                
            </div>
            <!-- region-popup (end) -->
            <div class="sort cabinet-enter">
            	
            	<?php if(Yii::app()->user->isGuest): ?>
            	
                <span class="drop-link link-cabinet-enter"><span><?=Yii::t('main',"My Account")?></span></span>
                
                <div class="sort-popup auth hidden">
                    <a href=<?=Yii::app()->createUrl('/users/register'); ?>><?= Yii::t('main','Not registered')?>?</a>
                    <?=Yii::t('main','Auth')?>
                    
                    <?php 
                    $model = new UserLoginForm;
                    $form=$this->beginWidget('CActiveForm', array(
						'id'=>'user-login-form',
						'action'=>Yii::app()->getBaseUrl(true) .'/users/login/',
						'enableAjaxValidation'=>true,
						'clientOptions'=>array(
					    'validateOnSubmit'=>true,
					     ),
					)); ?>
                        <div class="userdata">
                        	<?php echo $form->textField($model,'username', array('placeholder'=>Yii::t('main',"Login"),'title'=>Yii::t('main',"Login"))); ?>
                        	<?php echo $form->error($model,'username'); ?>
                        </div>
                        <div class="userdata">
                            <?php echo $form->passwordField($model,'password',array('placeholder'=>Yii::t('main',"Password"),'title'=>Yii::t('main',"Password"))); ?>
                            <?php echo $form->error($model,'password'); ?>
                        </div>
                        <div class="permanent">
                            <a href=<?=Yii::app()->createUrl('/users/remind'); ?>><?= Yii::t('main','Send password')?></a>
                            <?php echo CHtml::activeCheckBox($model,'rememberMe', array('id'=>'to-remember')); ?>
                            <label for="to-remember"><?=Yii::t('main',"Remember me")?></label>
                        </div>
                        <input class="btn-purple enter-btn" type="submit" value="<?=Yii::t('main',"Enter")?>" />
                    <?php $this->endWidget(); ?>
                </div>
                
                <?php else:?>
                	<span class="drop-link link-cabinet-enter profileLink" onclick="location.href='<?=Yii::app()->createUrl('/users/profile/orders'); ?>'"><span><?=Yii::t('main',"My orders")?></span></span>
                	<span class="drop-link link-cabinet-enter profileLink" onclick="location.href='<?=Yii::app()->createUrl('/users/profile'); ?>'"><span><?=Yii::t('main',"My Account")?></span></span>
                	<span class="drop-link link-cabinet-enter profileLink" onclick="location.href='<?=Yii::app()->createUrl('/users/logout'); ?>'"><span><?=Yii::t('main',"Exit")?></span></span>
                <?php endif;?>
                
            </div>
            <ul class="menu">
				<li><a title="Payment Methods" href="<?=Yii::app()->createUrl('/page/payment.html'); ?>"><?=Yii::t('main',"Payment")?></a></li>
                <li><a title="About Delivery" href="<?=Yii::app()->createUrl('/page/about-delivery.html'); ?>"><?=Yii::t('main',"About Delivery")?></a></li>
                <li><a title="Terms and Conditions" href="<?=Yii::app()->createUrl('/page/terms-conditions.html'); ?>"><?=Yii::t('main',"Terms&Conditions")?></a></li>
                <li><a title="Contacts" href="<?=Yii::app()->createUrl('/feedback'); ?>"><?=Yii::t("main", "Contacts")?></a></li>
            </ul>

        </div>
    </div>
    <!-- header-top (end) -->

    <!-- header (begin) -->
    <div class="header ">
        <div class="wrap">
            <a href="<?=Yii::app()->createUrl('/cart'); ?>" title="" class="b-cart" id="cart"><i><b></b></i>
            	<?php $this->renderFile(Yii::getPathOfAlias('orders.views.cart._small_cart').'.php'); ?> 
            </a>
            <span class="btn-search"><span><?=Yii::t('main','Search')?></span></span>
            
            <ul>
                <li>
                    <a href="<?= Yii::app()->createUrl('/'); ?>" class="logo" title="7roses">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/logo_<?=Yii::app()->language?>.png" alt="7roses" />
                    </a>
                </li>
                <li class="contact phones">
                    <div style="display:inline;"><a title="Call" href="tel:+380505620799">+38 050 562 0799</a><br>
					<? 
					echo CHtml::link(CHtml::image('/uploads/viber.png','viber', array('style'=>' display:inline;padding-right:5px;')), 'viber://chat?number=380505620799', array('rel'=>'nofollow'));
					echo CHtml::link(CHtml::image('/uploads/whatsapp.png','watsapp', array('style'=>' display:inline;padding-right:5px;')), 'https://wa.me/380505620799', array('rel'=>'nofollow'));
					echo CHtml::link(CHtml::image('/uploads/telegram.png','telegram', array('style'=>' display:inline;padding-right:5px;')), 'tg://resolve?domain=seven_roses', array('rel'=>'nofollow'));
					echo CHtml::link(CHtml::image('/uploads/skype.png','skype', array('style'=>' display:inline;padding-right:5px;')), 'skype:sevenrosesodessa?chat', array('rel'=>'nofollow'));
					?>
					</div>
                    
                </li>
                <li class="contact currency">
                    <div class="b-currency">
                    <h2 class="title"><?=Yii::t('main','Currency')?></h2>
            <?php
            foreach(Yii::app()->currency->currencies as $currency)
                {
                    echo CHtml::ajaxLink($currency->symbol, '/store/ajax/activateCurrency/'.$currency->id, array(
                        'success'=>'js:function(){window.location.reload(true)}',
                    ),array('id'=>'sw'.$currency->id,'class'=>Yii::app()->currency->active->id===$currency->id?'active':''));
                }
            ?>
             </div>
                </li>
				<li><a title="Visa-MasterCard" href="<?=Yii::app()->createUrl('/page/payment.html'); ?>"><img src="/uploads/visa_mastercard_100.png"></a>
				</li>
            </ul>
        </div>

        <!-- search-popup (begin) -->
        <div class="header-popup search-popup">
            <span class="popup-close"></span>
            <div class="search-form">
                <span><?=Yii::t('main','Site search')?></span>
                <?php echo CHtml::form(MLhelper::addLangToUrl('/store/category/search/')) ?>
	                <input class="search-field" type="text" placeholder="<?=Yii::t('main','Use keywords to find')?>" name="q" id="searchQuery" title="Site search">
	                <input class="btn-purple" type="submit" value="<?=Yii::t('main','Search')?>">
                <?php echo CHtml::endForm() ?>
            </div>
        </div>

        <!-- search-popup (end) -->
    </div>
    <!-- header (end) -->
<?php 
     $lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';

                    $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
?>
    <!-- page-content (begin) -->
    <div class="page-content wrap">

        <!-- nav (begin) -->
        <ul class="nav g-clearfix">
            <li>
                <?php $product = StoreCategory::model()->findByPk(230);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'230', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav01.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(230)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
            <li>
                <?php $product = StoreCategory::model()->findByPk(234);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'234', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav02.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(234)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
            <li>
                <?php $product = StoreCategory::model()->findByPk(232);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'232', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav03.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(232)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));

              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
            <li>
                <?php $product = StoreCategory::model()->findByPk(235);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'235', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav04.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(235)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
			<li>
                <?php $product = StoreCategory::model()->findByPk(276);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'276', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav07.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
				<ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(276)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul> 
            </li>
           <li>
                <?php $product = StoreCategory::model()->findByPk(236);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'236', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav05.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(236)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
            <li>
                <?php $product = StoreCategory::model()->findByPk(237);
                       $tansProduct = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>'237', 'language_id'=>$langArray->id));
                ?>
                <a title="" href="<?= Yii::app()->createUrl('/' . $product['url']); ?>">
                    <div class="visual">
                        <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/nav06.png" alt="<?php echo $tansProduct->name; ?>"/>
                    </div>
                    <div class="title"><?php echo $tansProduct->name; ?></div>
                </a>
             <ul>
              <?php
                   

                    $items = StoreCategory::model()->findByPk(237)->asCMenuArray();
                     foreach($items['items'] as $item):
                        $tansItem = StoreCategoryTranslate::model()->findByAttributes(array('object_id'=>$item['url']['id'], 'language_id'=>$langArray->id));
              ?>
                 <li><a href="<?= Yii::app()->createUrl('/' . $item['url']['url']); ?>"><?=$tansItem->name;?></a></li>
                 <?php endforeach;?>
                </ul>  
            </li>
        </ul>

        <!-- nav (end) -->
        
		<?php if(($messages = Yii::app()->user->getFlash('messages'))): ?>
			<div class="flash_messages">
				<button class="close">×</button>
				<?php
					if(is_array($messages))
						echo implode('<br>', $messages);
					else
						echo $messages;
				?>
			</div>
		<?php endif; ?>
	
		<?php echo $content; ?>

    </div>
	 <!-- page-content (begin) -->

    <div class="gag"></div>
</div>

<!-- footer (begin) -->

<div class="footer">
    <div class="wrap">
	 <!-- menu-bottom (begin) -->

	<div class="footer-col" style="width:170px;">
        <div class="foot-title-big"><?=Yii::t('main','Copyright')?></div>
			<div class="copyright">
            <p>&copy; 7Roses 2014 - <?= date('Y')?></p>
            <p><?=Yii::t('main','All rights reserved')?></p>
        </div>       
    </div>
	<div class="footer-col" style="width:230px;">
	<div class="foot-title-big"><?=Yii::t('main','Flower Delivery')?></div>
	            <ul>
                    <li><a title="Flowers" href="<?=Yii::app()->createUrl('/flowers'); ?>"><?=Yii::t('main','Flowers')?></a></li>
                    <li><a title="Flower arrangements" href="<?=Yii::app()->createUrl('/arrangements'); ?>"><?=Yii::t('main','Arrangements')?></a></li>
                    <li><a title="Gifts and soft toys" href="<?=Yii::app()->createUrl('/gifts'); ?>"><?=Yii::t('main','Gifts')?></a></li>
                    <li><a title="Sweets and chocolate" href="<?=Yii::app()->createUrl('/sweets'); ?>"><?=Yii::t('main','Sweets')?></a></li>
					<li><a title="Gourmet Basket" href="<?=Yii::app()->createUrl('/gourmet'); ?>"><?=Yii::t('main','Gourmet')?></a></li>
                    <li><a title="Occasion" href="<?=Yii::app()->createUrl('/reason'); ?>"><?=Yii::t('main','Occasion')?></a></li>
                </ul>
	</div>
	<div class="footer-col" style="width:230px;">
	<div class="foot-title-big"><?=Yii::t('main','Support')?></div>
					<a title="payment" href="<?= Yii::app()->createUrl('/page/payment.html'); ?>"><?=Yii::t('main','Payment')?></a><br>
                    <a title="about delivery" href="<?= Yii::app()->createUrl('/page/about-delivery.html'); ?>"><?=Yii::t('main','About Delivery')?></a><br>
                    <a title="terms and conditions" href="<?= Yii::app()->createUrl('/page/terms-conditions.html'); ?>"><?=Yii::t('main','Terms&Conditions')?></a><br>
					<a title="frequently asked questions" href="<?= Yii::app()->createUrl('/page/faq.html'); ?>"><?=Yii::t('main','FAQ')?></a><br>
                    <a title="Customer reviews" href="<?= Yii::app()->createUrl('/reviews'); ?>"><?=Yii::t('main','Customer reviews')?></a><br>
                   <a title="Contacts" href="<?= Yii::app()->createUrl('/feedback'); ?>"><?=Yii::t('main','Contacts')?></a><br>
                
	</div>
	<div class="footer-col" style="width:230px;">
	<div class="foot-title-big"><?=Yii::t('main','Contacts')?></div>
	            <div class="oocab-column">
                <div class="oocab-column-item">
                    <div class="ocabci-row"><?= Yii::t('main','Title'); ?>: <?= $this->layout_params['firm']['firm_name']; ?></div>
                    <div class="ocabci-row"><?= Yii::t('main','Address'); ?>: <?= $this->layout_params['firm']['firm_address']; ?></div>
					<div class="ocabci-row"><?= $this->layout_params['firm']['firm_city']; ?>, <?= $this->layout_params['firm']['firm_postcode']; ?></div>
					<div class="ocabci-row"><?= $this->layout_params['firm']['firm_region'] . ', ' . Yii::t('main','Ukraine'); ?></div>
                    <div class="ocabci-row"><?= Yii::t('main','Phone'); ?>: <?= $this->layout_params['firm']['firm_phone']; ?></div>
                </div>
            </div>
	</div>

        <!-- menu-bottom (end) -->
    </div>
</div>
<!-- footer (end) -->


<div class="hidden">
	
	<!-- modal (begin) -->
	<div id="cart-modal" class="box-modal cart-modal">
		
		<div class="added" id="popup-cart">
	   		<?php $this->renderFile(
	   		        Yii::getPathOfAlias('orders.views.cart._popup_cart').'.php',
                    array('lng' => Yii::app()->language)
            ); ?>
	   	</div>
	    
	    <div class="reg">
		    <div class="reg-sorts">
		        <div class="sort sort-reg">
		            <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php', array('popup'=>'city-popup', 'no_redirect' => true)); ?>
		        </div>
		    </div>
		</div>
		<span class="btn-purple arcticmodal-close"><?=Yii::t('main','Continue shopping')?></span>
		<a class="btn-green" href="<?=Yii::app()->createUrl('/cart'); ?>"><?=Yii::t('main','Checkout')?></a>
	</div>
	<!-- modal (end) -->
	
	
	<!-- modal (begin) -->
	<div id="notavailable-modal" class="box-modal cart-modal">
		<?php
        $cityMainInfo = $this->getCurrentCityInfo(true);
		?>
		<span style="font-size:18px; font-weight:bold; display: block; padding:10px; text-align:center;"><?=Yii::t('main','This product is not available for the region')?> : <?= $cityMainInfo->name; ?></span><br/>
		
		<span class="btn-purple arcticmodal-close"><?=Yii::t('main','Continue shopping')?></span>
	</div>
	<!-- modal (end) -->
	
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-92420651-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-92420651-1');
</script>
<script src="https://cdn.ywxi.net/js/1.js" async></script>
<script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/jquery.arcticmodal-0.3.min.js"></script>
<script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/main.js"></script>
<script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/jquery.hoverIntent.minified.js"></script> 
<script type="text/javascript">
$( "#accordion" ).accordion({
	active: false,
  collapsible: true,
   autoHeight: false
});

</script>
<?php /*script type="text/javascript">
$(document).ready(function(){
	$(".regions ul li a").click(function(e){
		e.preventDefault();
		var city = $(this).text();

		$.ajax({
			type: "GET",
			url: "/site/changeCity",
			data: {city : city},
			success: function(data){
			    $(".cityName").text(data);
			    $(".sort-popup").addClass('hidden');
			    location.reload();
			}
		})
	})
});
</script*/?>
</div>
</body>
</html>