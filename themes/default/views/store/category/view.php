<?php
$min_price = isset($GET['min_price']) ? $GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
/**
 * Category view
 * @var $this CategoryController
 * @var $model StoreCategory
 * @var $provider CActiveDataProvider
 * @var $categoryAttributes
 * @var $displayDescription
 * @var $pageMetaSeo
 */

// Set meta tags
$this->pageTitle = (($this->model->meta_title) ? $this->model->meta_title : $this->model->name) . $pageMetaSeo;
$this->pageKeywords = ($this->model->meta_keywords . ', ' . $city_seo['keywords']) . $pageMetaSeo;
$this->pageDescription = ($this->model->meta_description . ' ' . $city_seo['description']) . $pageMetaSeo;
$lang= Yii::app()->language;
if($lang == 'ua')
    $lang = 'uk';

$langArray = SSystemLanguage::model()->findByAttributes(array('code'=>$lang));
 $categoryTrans=StoreCategoryTranslate::model()->findAllByAttributes(array('language_id'=>$langArray->id));
// Create breadcrumbs
$ancestors = $this->model->excludeRoot()->ancestors()->findAll();

foreach($ancestors as $c){
    foreach($categoryTrans as $ct){
        if($ct->object_id==$c->id)
       $this->breadcrumbs[$ct->name] = $c->getViewUrl();
    }
}

$this->breadcrumbs[] = $this->model->name;

?>

<!-- breadcrumbs (begin) -->
<?php
    $this->widget('zii.widgets.CBreadcrumbs', array(
        'homeLink'=>CHtml::link(Yii::t('main','Home page'), array('/store/index/index')),
        'links'=>$this->breadcrumbs,
    ));
?>
<!-- breadcrumbs (end) -->

<div class="g-clearfix">

    <?php //$this->renderFile(Yii::getPathOfAlias('pages.views.pages.left_sidebar').'.php', array('popup'=>'city-catalog')); ?>

    <!-- products (begin) -->
    <div class="products">
        <!-- region-popup (begin) -->
        <div class="sorts">
            <div class="sort sort-reg">
                
                <?php $this->renderFile(Yii::getPathOfAlias('pages.views.pages.popup_regions').'.php'); ?>
                
            </div>
        </div>
        <!-- region-popup (end) -->
        <h1 class="page-title"><?php echo CHtml::encode($this->model->name); ?></h1>
        <!-- sorts (begin) -->
        <div class="sorts g-clearfix category-top-sorting">
            <?php $this->renderPartial('sort',
                array(
                    'full_path' => '/' . $this->model->full_path,
                    'id_prefix' => 'top',
                    'copy_pager' => true,
                )
            ); ?>
        </div>
        <!-- sorts (end) -->

        <!-- products (begin) -->
        <div class="products catalog g-clearfix category-products-list">
            
            <?php
                $this->widget('zii.widgets.CListView', array(
                    'dataProvider'=>$provider,
                    'ajaxUpdate'=>false,
                    'template'=>'{items} {pager}',
                    'itemView'=>$itemView,
                    'sortableAttributes'=>array(
                        'name', 'price'
                    ),
                    'viewData' => array('langArray' => $langArray),
                    'pager' => array(
                        'header' => false,
                        'prevPageLabel' => '&larr;',
                        'nextPageLabel' => '&rarr;',
                    ),
                ));
            ?>
            
        </div>
        <!-- products (end) -->

        <!-- sorts (begin) -->
        <?php $this->renderPartial('sort',
            array(
                'full_path' => '/' . $this->model->full_path,
                'id_prefix' => 'bottom',
                'base_container_id' => 'cat_bottom_sort',
            )
        ); ?>
		<div style="clear: left"></div>
        <!-- sorts (end) -->
        
        <!-- b-page-text (begin) -->
        <?php if(!empty($displayDescription)): ?>
        <div class="b-page-text text ">
        <?php if(!empty($this->model->description)): ?>
            <h2 class="title"><?php echo CHtml::encode($this->model->name); ?></h2>
			<div class="content-text">
            <?php echo $this->model->description ?>
        <?php endif ?>
            <?= '<br>' . $city_seo['text']; ?>
			</div>
        </div>
        <?php endif; ?>
		
        <!-- b-page-text (end) -->
    </div>
    <!-- products (end) -->
</div>

<script type="text/javascript">
    copyPager('top'); // 'top' должно соответствовать параметру 'id_prefix' при вызове сортировок
</script>