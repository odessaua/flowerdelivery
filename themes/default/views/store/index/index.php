<?php $slider=SSystemSlider::model()->active()->orderByPosition()->findAll();

if(!empty($data['city_seo'])){
    $this->pageKeywords = (!empty($city_seo['keywords'])) ? $city_seo['keywords'] : '';
    $this->pageDescription = (!empty($city_seo['description'])) ? $city_seo['description'] : '';
    $this->pageTitle = (!empty($city_seo['title'])) ? $city_seo['title'] : '';
}
else{
    unset($city_seo);
    if(!empty($main_page)){
        $this->pageKeywords = (!empty($main_page['meta_keywords'])) ? $main_page['meta_keywords'] : '';
        $this->pageDescription = (!empty($main_page['meta_description'])) ? $main_page['meta_description'] : '';
        $this->pageTitle = (!empty($main_page['meta_title'])) ? $main_page['meta_title'] : '';
    }
}
// var_dump($slider);
?>
<div class="g-clearfix">
	<!-- col-1 (begin) -->
	<div class="col-1">
        <?php if(empty($data['h1_header'])): ?>
	    <div class="slider">
	        <?php /*div id="slider">
	            <ul>
                        <?php foreach ($slider as $one) { ?>
                            <li>
                                <a href="<?= Yii::app()->createUrl($one['url']); ?>" title="<?=$one['name']?>">
                                    <img width="812" height="282" src="<?= '/uploads/slider/'.$one['photo'] ?>" alt="<?=$one['name']?>"/>
                                </a>
                            </li>
                        <?php } ?>
	            </ul>
	        </div*/?>
            <?php if(!empty($slider)): ?>
                <script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/jquery.cycle.all.js"></script>
                <div id="new_slider">
                <?php foreach ($slider as $one) { ?>
                    <a href="<?= Yii::app()->createUrl($one['url']); ?>" title="<?=$one['name']?>">
                        <img width="812" height="282" src="<?= '/uploads/slider/'.$one['photo'] ?>" alt="<?=$one['name']?>"/>
                    </a>
                <?php } ?>
                </div>
                <span id="prevBtn"><a href="javascript:void(0);"></a></span>
                <span id="nextBtn" style="right: -9px;"><a href="javascript:void(0);"></a></span>
            <?php endif; ?>
	    </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
//                $("#slider").easySlider({
//                    auto: <?//= (Yii::app()->settings->get('core', 'sliderAutoRotate') > 0) ? 'true' : 'false';?>
//                });
                // new slider
                $('#new_slider').cycle({
                    fx: 'fade',
                    next: '#nextBtn',
                    prev: '#prevBtn',
                    timeout: <?= (int)Yii::app()->settings->get('core', 'sliderTimeout');?>
                });
            });
        </script>
        <?php else: ?>
        <h1 class="page-title" style="margin: 20px 0 30px;"><?= $data['h1_header']; ?></h1>
        <?php endif; ?>
	
	    <?php //$this->renderFile(Yii::getPathOfAlias('pages.views.pages.left_sidebar').'.php'); ?>
	
	    <!-- col-1 (begin)  -->
	    <div class="col-1">
	
	        <!-- products (begin) -->
	        <div class="products g-clearfix">
	        	<?php
                shuffle($popular);
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
	
	        <!-- b-page-text (begin) -->
	        <div class="b-page-text text ">
	            <?php
                if(!empty($city_seo['text'])){
                    echo $city_seo['text'];
                }
                elseif(!empty($mainContent->full_description)){
                    echo $mainContent->full_description;
                }
                ?>
	        </div>
	        <!-- b-page-text (end) -->
	    </div>
	    <!-- col-1 (end) -->
	</div>
	<!-- col-1 (end) -->
	
	<!-- col-22 (begin) -->
	<div class="col-22">
        <?php
        // активный баннер с position = 'main_top_right'
        $banner =  SSystemBaner::model()->active()->find("position = :position", array(':position' => 'main_top_right'));
        if(!empty($banner)):
        ?>
	    <div class="action">
	        <a href="<?= Yii::app()->createUrl($banner->url); ?>" title="<?= $banner->name; ?>">
	            <img width="218" height="282" src="<?= '/uploads/pic/'.$banner->photo; ?>" alt="<?= $banner->name; ?>" />
	        </a>
	    </div>
        <?php endif; ?>

	    <!-- b-comments (begin) -->
	    <div class="b-comments">
	        <h3 class="title">
                <a href="<?=Yii::app()->createUrl('/reviews'); ?>">
                    <?=Yii::t('main','Customer reviews')?>
                </a>
            </h3>
	        <ul>
	        	<?php foreach ($comments as $key => $value): ?>
	            <li>
	            	
	                <div class="visual">
	                    <img src="<?php echo Yii::app()->theme->baseUrl ?>/assets/img/avatar01.jpg" alt="Reviews"/>
	                </div>
	                <div class="info">
	                    <div class="name"><?=$value['name']?></div>
	                    <p>
	                        <? 
								$str = $value['text'];								
								  if (strlen($str)>150)
								  {
									  $str = substr ($str, 0,strpos ($str, " ", 120)); echo $str.'...';
								  }
								  else echo $str;

								?>
	                    </p>
	                </div>
	            
	            </li>
	            <?php endforeach;?>
	        </ul>
			<div class="all"><a href="<?=Yii::app()->createUrl('/reviews'); ?>">
                    <?=Yii::t('main','read more reviews')?>
                </a>
			</div>
			<div>
	    </div>
	    <!-- b-comments (end) -->
	
	    <!-- b-socials (begin) -->
	    <div class="b-socials">
	        <h3 class="title"><?=Yii::t('main','We are in social networks')?></h3>
	        <div>
			<a class="go" href="https://plus.google.com/u/1/109628640430677109024" title="Google+"></a>
			<a class="fb" href="https://www.facebook.com/7Roses.Ukraine/" title="Facebook"></a>
	             <!--<a class="fb" href="#" title="Facebook"></a>	            
	            <a class="ok" href="#" title="Одноклассники"></a>-->
	        </div>
	    </div>
	    <!-- b-socials (end) -->
	</div>
	<!-- col-22 (end) -->
</div>