<div class="related-products">
    <h3 class="title"><?=Yii::t('main','Add a little something extra:')?></h3>
    <div class="bg-pr-slider">
        <div class="pr-slider" id="product-slider">
            <ul>

                <?php
                $products = StoreProduct::model()
                    ->applyCategories(270)
                    ->active()
                    ->findAll();

                foreach($products as $data): ?>
                    <li>
                        <div class="b-rel-prod">
                            <div class="visual">
                                <?php
                                if($data->mainImage)
                                    $imgSource = $data->mainImage->getUrl('85x85');
                                else
                                    $imgSource = 'http://placehold.it/85x85';
                                echo CHtml::link(CHtml::image($imgSource, $data->mainImageTitle), array('/store/frontProduct/view', 'url'=>$data->url), array('rel'=>'nofollow', 'style' => 'line-height:85px'));
                                echo '<div class="price">';
                                echo StoreProduct::formatPrice(Yii::app()->currency->convert($data->price), true);
                                echo '</div>';
                                ?>
                            </div>
                            <?php
                            echo CHtml::form(array('/cart/add/'));
                            echo CHtml::hiddenField('product_id', $data->id);
                            echo CHtml::hiddenField('product_price', $data->price);
                            echo CHtml::hiddenField('use_configurations', $data->use_configurations);
                            echo CHtml::hiddenField('currency_rate', Yii::app()->currency->active->rate);
                            echo CHtml::hiddenField('configurable_id', 0);
                            echo CHtml::hiddenField('quantity', 1);

                            $redirect = (empty($noreload))
                                ? 'go'
                                : '';
                            echo CHtml::ajaxSubmitButton(Yii::t('StoreModule.core',Yii::t('main','Add')), Yii::app()->getBaseUrl(true) . '/cart/add/', array(
                                'id'=>'addProduct'.$data->id,
                                'dataType'=>'json',
                                'success'=>'js:function(data, textStatus, jqXHR){processCartResponseFromCart(data, textStatus, jqXHR, "'.$redirect.'")}',
                            ), array('class'=>'btn-purple btn-add'));

                            ?>
                            <?php echo CHtml::endForm() ?>

                        </div>
                    </li>
                <?php endforeach;?>

            </ul>
        </div>
    </div>
</div>