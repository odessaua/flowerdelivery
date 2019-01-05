<?php
/**
 * страница сортировки товаров в категории
 *
 * @var $categories
 * @var $category_id
 * @var $products
 */

$this->breadcrumbs = array(
    'Home'=>$this->createUrl('/admin'),
    Yii::t('StoreModule.admin', 'Сортировка товаров')=>$this->createUrl('index'),
);

$this->pageHeader = $title;
?>

<?php if(!empty($categories)): ?>
<link href="<?php echo Yii::app()->theme->baseUrl ?>/assets/css/select2.min.css" rel="stylesheet" />
<script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/select2.min.js"></script>
<style>
    .select2-label{
        display: block;
        margin: 15px 15px 0 0;
        font-weight: bold;
    }
    .sortable-images-wrap{
        clear: both;
        margin-bottom: 20px;
        height: auto;
        float: left;
        width: 80%;
    }
    .thumb-img{
        width: 150px; /* размеры фото товара */
        height: 150px;
        border-radius: 5px;
    }
    #products_list {
        list-style-type: none;
        margin: 15px;
        padding: 0;
        cursor: move;
        /*width: 900px;*/ /* можно ограничить ширину блока с плашками товаров */
    }
    #products_list li {
        margin: 10px;
        padding: 1px;
        float: left;
        width: 152px; /* размеры блока одного товара */
        height: 152px;
        border: 1px solid #999;
        border-radius: 5px;
    }
    .sorting-btn-wrap{
        width: 100%;
        height: auto;
        margin: 15px 0 20px 25px;
        float: left;
    }
    .sorting-btn{
        padding: 5px 10px;
        border: 1px solid #222;
        border-radius: 3px;
        background-color: #fff;
        font-size: 18px;
    }
</style>

<form action="" name="categoryForm" method="get" style="padding: 20px;">
    <label class="select2-label" for="category_id">Выберите категорию для сортировки товаров:</label><br>
    <select name="category_id" id="category_id" onchange="document.categoryForm.submit();" style="width: 300px; margin: 15px auto;">
        <option value="0">Выберите категорию для сортировки</option>
        <?php
        foreach ($categories as $ck => $cv):
            $selected = ($ck == $category_id) ? 'selected="selected"' : '';
        ?>
            <option value="<?= $ck; ?>" <?= $selected; ?>><?= $cv; ?></option>
        <?php endforeach; ?>
    </select>
</form>

<script>
    $(document).ready(function() {
        $('#category_id').select2();
    });
</script>
<?php endif; ?>

<?php if(!empty($products)): ?>
<div class="sortable-images-wrap">
    <?php
    $this->widget('zii.widgets.jui.CJuiSortable', array(
        'id' => 'products_list',
        'items' => $products,
        'options' => array(
            'opacity' => 0.6,
            'placeholder' => 'ui-state-highlight',
            'cursor' => 'move',
        ),
    ));
    ?>
</div>
<div class="sorting-btn-wrap">
    <?= CHtml::ajaxLink('Сохранить', Yii::app()->getBaseUrl(true) . 
        '/admin/store/productSorting/sorting/',
        array(
            'type' => 'POST',
            'success' => 'function(data){ $.jGrowl("Последовательность товаров успешно сохранена.", {position:"bottom-right"}); }',
            'data' => new CJavaScriptExpression('{"items": $("#products_list").sortable("toArray")}'),
        ),
        array(
            'class' => 'sorting-btn',
        )
    ); ?>
</div>
<?php endif; ?>