<?php

/**
 * @var StoreProduct $data
 */

// product Main category
$mc_sql = "SELECT sc.url
FROM StoreCategory sc
LEFT JOIN StoreProductCategoryRef spcr ON spcr.category = sc.id
WHERE spcr.product = " . $data->id . "
AND spcr.is_main = 1
LIMIT 1";
$mainCat = Yii::app()->db->createCommand($mc_sql)->queryRow();
// product url
$product_url = (!empty($mainCat['url']))
    ? Yii::app()->createUrl('/' . $mainCat['url'] . '/' . $data->url . '.html')
    : array('frontProduct/view', 'url'=>$data->url);
// product name
$trans=Yii::app()->db->createCommand()
    ->select('name,')
    ->from('StoreProductTranslate')
    ->where('language_id=:lang_id',array('lang_id'=>$langArray->id))
    ->andWhere('object_id=:prod_id',array('prod_id'=>$data->id))
    ->queryRow();
// images alt & title
$img_alt = (!empty($data->img_alt)) ? $data->img_alt : $trans['name'];
$img_title = (!empty($data->img_title)) ? $data->img_title : $trans['name'];

?>

<div class="b-product">
    <div class="visual">
    	<?php
        if($data->mainImage) {
            $imgSource = $data->mainImage->getUrl('135x19950');
            if(!file_exists('./' . $imgSource)) $imgSource = 'http://placehold.it/135x199/ffffff?text=7Roses';
        }
        else
            $imgSource = 'http://placehold.it/135x199/ffffff?text=7Roses';
		echo CHtml::link(CHtml::image($imgSource, $img_alt, array('title' => $img_title)), $product_url, array('rel'=>'nofollow'));
		?>
        <?php
        // Скины: акционный товар или старая цена, приоритет – у акционного товара
        // для смены приоритета – поменять местами условия
        $sale_img = $sale_alt = $sale_title = '';
        if(!empty($data->sale_id)){
            $sale_product = StoreProduct::getSale($data->sale_id);
            $sale_img = $sale_product->mainImage->getUrl('100x100');
            $sale_alt = $sale_title = $sale_product['name'];
        }
        elseif (!empty($data->old_price)){
            $sale_img = Yii::app()->theme->baseUrl . '/assets/img/sale.png';
            $sale_alt = $sale_title = Yii::t('StoreModule.core', 'Sale');
        }
        if(!empty($sale_img)):
            ?>
            <div class="sale-grid-skin">
                <img src="<?= $sale_img; ?>" alt="<?= $sale_alt; ?>" title="<?= $sale_title; ?>"/>
            </div>
        <?php endif; ?>
    </div>
    <div class="title">
        <?php echo CHtml::link(CHtml::encode($trans['name']), $product_url) ?>
    </div>
    <div class="price">
        <?php if(!empty($data->old_price)): ?>
        <span class="product-grid-old-price">
            <?= StoreProduct::formatPrice($data->toCurrentCurrency('old_price'), true); ?>
        </span>
        <?php endif; ?>
        <?php echo $data->priceRange(true) ?>
    </div>

</div>