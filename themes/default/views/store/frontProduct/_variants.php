<?php
/**
 * @var $variants
 * @var StoreProduct $model
 */
$variantLimit = 12;
?>
<?php
if(!empty($variants)):
    $variants = CArray::array_order_by($variants, 'spv_price', SORT_ASC); // сортируем варианты по возрастанию цены
?>
<style>
    .product-variants-block{
        width: 100%;
        margin-top: 0px;
        margin-bottom: 20px;
    }
    .pvb-header{
        margin-bottom: 10px;
    }
</style>

<div class="product-variants-block">

    <?php if(!empty($variants[0]['attr_header'])): // Attribute header ?>
        <h3 class="pvb-header"><?= $variants[0]['attr_header']; ?>:</h3>
    <?php endif; ?>

<?php if(count($variants) <= $variantLimit): // radio-buttons ?>

    <style>
        .pvb-radio-item{
			width:80%;
            margin-bottom: 5px;
            clear: both;
        }
        .pvb-radio-item-text, .pvb-radio-item-price{
            position: relative;
        }
        .pvb-radio-item-text{
            margin-left: 10px;
            top: -4px;
        }
        .pvb-radio-item-price{
            display: block;
            float: right;
			color:#29a943;			
        }
        /* Radio buttons */
        input[type='radio'].product-radio {
            -webkit-appearance:none;
            width:18px;
            height:18px;
            border:1px solid darkgray;
            border-radius:50%;
            outline:none;
            box-shadow:0 0 3px 0px gray inset;
        }
        input[type='radio'].product-radio:hover {
            box-shadow:0 0 5px 0px white inset;
        }
        input[type='radio'].product-radio:before {
            content:'';
            display:block;
            width:50%;
            height:50%;
            margin: 25% auto;
            border-radius:50%;
        }
        input[type='radio'].product-radio:checked:before {
            background:#af3583;
        }
    </style>

    <?php foreach($variants as $vk => $variant): ?>
        <?php
        $checked = '';
        if(($variant['default'] > 0) || ($variant['spv_price'] == $model->price)){
            $checked = 'checked="checked"'; // отмечаем дефолтный вариант
            $model->price = $variant['spv_price']; // устанавливаем стоимость товара = стоимости дефолтного варианта
        }
        $variantPrice = StoreProduct::formatPrice(Yii::app()->currency->convert($variant['spv_price']));
        ?>

    <div class="pvb-radio-item">
        <label for="product_radio_<?= $vk; ?>">
            <input type="radio" name="eav[<?= $variant['attribute_id']; ?>]" class="product-radio" data-price="<?= $variantPrice; ?>" id="product_radio_<?=$vk; ?>" value="<?= $variant['variant_id']; ?>" <?= $checked; ?>>
            <span class="pvb-radio-item-text">
                <?= $variant['opt_value']; ?> <?= $variant['attr_title']; ?>
            </span>
            <span class="pvb-radio-item-price">
                <?= StoreProduct::formatPrice($variantPrice, true); ?>
            </span>
        </label>
    </div>

    <?php endforeach; // ($variants as $vk => $variant) ?>

    <script type="text/javascript">
        // обработка radio-кнопок
        jQuery(document).ready(function ($) {
            $('.product-radio').on('click', function () {
                var dprice = $('#'+this.id).data('price');
                $('#product_price').val(dprice);
                // форматирование отображения цены товара по шаблонам
                var priceTmpl = $('#productPriceFormat').html();
                if(priceTmpl !== undefined && priceTmpl !== ''){
                    dprice = priceTmpl.replace('{sum}', dprice);
                }
                $('#productPrice').html(dprice);
            });
        });
    </script>

<?php elseif(count($variants) > $variantLimit): // select ?>

    <link href="<?php echo Yii::app()->theme->baseUrl ?>/assets/css/select2.min.css" rel="stylesheet" />
    <script src="<?php echo Yii::app()->theme->baseUrl ?>/assets/js/select2.min.js"></script>
    <style>
        .select2-container--default .select2-selection--single {
            border-color: #dcb2c7;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #000 transparent transparent transparent;
        }
    </style>
    <script type="text/javascript">
        var productVariants = [];
    </script>

    <script type="text/javascript">
    <?php
    // формируем JS-массив для обработки цены товара
    foreach($variants as $vvk => $vvariant):
        $vvariantPrice = StoreProduct::formatPrice(Yii::app()->currency->convert($vvariant['spv_price']));
    ?>
        productVariants[<?= $vvariant['variant_id']; ?>] = '<?= $vvariantPrice; ?>';
    <?php endforeach; ?>
    </script>

    <select class="product-select" name="eav[<?= $variants[0]['attribute_id']; ?>]" id="eav_select">
    <?php foreach($variants as $vk => $variant): ?>
        <?php
        $checked = '';
        if(($variant['default'] > 0) || ($variant['spv_price'] == $model->price)){
            $checked = 'selected="selected"'; // отмечаем дефолтный вариант
            $model->price = $variant['spv_price']; // устанавливаем стоимость товара = стоимости дефолтного варианта
        }
        $variantPrice = StoreProduct::formatPrice(Yii::app()->currency->convert($variant['spv_price']));
        ?>
        <option value="<?= $variant['variant_id']; ?>" <?=$checked; ?>><?= $variant['opt_value'] . ' ' . $variant['attr_title'] . str_repeat('&nbsp;', 10) . StoreProduct::formatPrice($variantPrice, true); ?></option>
    <?php endforeach; ?>
    </select>

    <script type="text/javascript">
        // обработка выпадающего списка
        jQuery(document).ready(function ($) {
            $('.product-select').select2({
                minimumResultsForSearch: Infinity
            });

            $('.product-select').on('change', function () {
                var sel = $('#'+this.id).val();
                var dprice = productVariants[sel];
                $('#product_price').val(dprice);
                // форматирование отображения цены товара по шаблонам
                var priceTmpl = $('#productPriceFormat').html();
                if(priceTmpl !== undefined && priceTmpl !== ''){
                    dprice = priceTmpl.replace('{sum}', dprice);
                }
                $('#productPrice').html(dprice);
            });
        });
    </script>

<?php endif; // (count($variants) <= 3) ?>
</div>
<?php endif; // (!empty($variants)) ?>