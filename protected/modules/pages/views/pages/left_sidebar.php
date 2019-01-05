<!-- col-11 (begin) -->
<div class="col-11">
<?php 
     $lang= Yii::app()->language;
                    if($lang == 'ua')
                        $lang = 'uk';

                    $langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
                    // var_dump($langArray->id);
?>
    <!-- sidebar (begin) -->
    <?php 
            $menu=SystemMenu::model()->findAll();
            $menuTrans=SystemMenuTranslate::model()->findAllByAttributes(array('language_id'=>$langArray->id));
            foreach ($menu as $value) {
                foreach ($menuTrans as $value1) {
                    if($value->id==$value1->object_id)
                        $value->name=$value1->name;
                }
            }
    ?>
   
    <div class="sidebar">
        <h3 class="title"><?=Yii::t('main','Menu')?></h3>
        
        <ul class="dropdown" id="nav">
             <?php if(isset($menu)){ ?>
                <?php foreach ($menu as $value) { ?>
                    <li><a href="<?=$value['url']?>"><span><?=$value['name']?> </span></a></li>
                <?php }?>
             <?php }?>
        </ul>
        
    </div>
    <!-- sidebar (end) -->
    <?php $banners=SSystemBaner::model()->findAll();
       // $path=Yii::getPathOfAlias('webroot').;
    // var_dump($banners[0]['url']);
    ?>
    
    <!-- banner (begin) -->
    <?php if(isset($banners[0])) {?>
    <div class="banner">
        <i class="i-new"></i>
        <a href="<?=$banners[0]['url']?>" title="">
            <img width="218" heigth="282" src="<?php echo isset($banners[0])?'/uploads/pic/'.$banners[0]['photo']:""?>" alt="" />
        </a>
    </div>
    <!-- banner (end) -->
    <?php }?>
     <?php if(isset($banners[1])) {?>
    <!-- banner (begin) -->
    <div class="banner">
        <i class="i-special"></i>
        <a href="<?=$banners[0]['url']?>" title="">
            <img  width="218" heigth="282"  src="<?php echo isset($banners[1])?'/uploads/pic/'.$banners[1]['photo']:""?>" alt="" />
        </a>
    </div>
    <!-- banner (end) -->
    <?php }?>
</div>
<!-- col-11 (end) -->