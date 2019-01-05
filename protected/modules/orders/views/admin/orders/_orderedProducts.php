<?php
/**
 * List of order products
 *
 * @var $model Order
 * @var $this OrdersController
 */
Yii::import('application.modules.orders.components.OrderQuantityColumn');
Yii::app()->clientScript->registerScript('qustioni18n', '
	var deleteQuestion = "'.Yii::t('OrdersModule.admin', 'Вы действительно удалить запись?').'";
	var productSuccessAddedToOrder = "'.Yii::t('OrdersModule.admin', 'Продукт успешно добавлен к заказу.').'";
', CClientScript::POS_BEGIN);
$thumb373 = '/productThumbs/373x373/';
$thumb50 = '/productThumbs/50x50/';
$this->widget('zii.widgets.grid.CGridView', array(
	'id'               => 'orderedProducts',
	'enableSorting'    => false,
	'enablePagination' => false,
	'dataProvider'     => $model->getOrderedProducts(),
	'selectableRows'   => 0,
	'template'         => '{items}',
	'columns'          => array(
		array(
			'name'=>'renderFullName',
			'type'=>'raw',
			'header'=>Yii::t('OrdersModule.admin', 'Название')
		),
		array(
			'class'=>'OrderQuantityColumn',
			'name'=>'quantity',
			'header'=>Yii::t('OrdersModule.admin', 'Кол.')
		),
		//'sku',  //  убрал вывод в таблице артикула, т.к. значение артикула не выводится 
		array(
			'name'=>'price',
			'value'=>'StoreProduct::formatPrice($data->price)'
		),
		array(
			'name'=>'photo',
			'type'   => 'html',
			'value'=>'CHtml::link(CHtml::image(Yii::app()->assetManager->baseUrl."/productThumbs/50x50/".OrderProduct::getOrderProductsImage($data->product_id), "image", array ("title"=> "Click to enlarge")), array(Yii::app()->assetManager->baseUrl."/productThumbs/373x373/".OrderProduct::getOrderProductsImage($data->product_id)), array("class"=>"highslide"))'
		),
		array(
			'type'=>'raw',
			'value'=>'CHtml::link("&times", "#", array("style"=>"font-weight:bold;", "onclick"=>"deleteOrderedProduct($data->id, $data->order_id, \"'.Yii::app()->request->csrfToken.'\")"))',
		),
	),
));
?>


<script type="text/javascript">
	$('[name^=images_product]').map(function() {
                 return $(this).val();
    }).get();
	var orderTotalPrice = '<?php echo $model->total_price ?>';
	</script>

<div align="right">
	<table id="orderSummaryTable">
		<thead>
			<tr>
				<td ></td>
				<td></td>
			</tr>
		</thead>
		<tbody>
			<tr align="right">
				<td><b><?php echo Yii::t('OrdersModule.admin','Стоимость открытки') ?>:</b></td>
				<td><span id="orderCardPrice"><?php echo StoreProduct::formatPrice($model->card_price); ?></span><?php echo Yii::app()->currency->main->symbol; ?></td>
			</tr>
			<tr align="right">
				<td><b><?php echo Yii::t('OrdersModule.admin','Стоимость фото') ?>:</b></td>
				<td><span id="orderPhotoPrice"><?php echo StoreProduct::formatPrice($model->photo_price); ?></span><?php echo Yii::app()->currency->main->symbol; ?></td>
			</tr>
			<!--<tr align="right" style="font-size: 14px;">
				<td><b><?php  //echo Yii::t('OrdersModule.admin','Сумма товаров без учета доставки') ?>:</b></td>
				<td><span id="orderTotalPrice"><?php //echo StoreProduct::formatPrice($model->total_price) ?></span><?php //echo Yii::app()->currency->main->symbol ?></td>
			</tr>
			
			<tr align="right">
				<td><b><?php//echo Yii::t('OrdersModule.admin','Сумма скидки') ?>:</b></td>
				<td><span id="orderTotalPrice"><?php// echo StoreProduct::formatPrice($model->discount_price) ?></span><?php// echo Yii::app()->currency->main->symbol ?></td>
			</tr>
			<tr align="right">
				<td><b><?php// echo Yii::t('OrdersModule.admin','Сумма заказа с учетом скидки') ?>:</b></td>
				<td><span id="orderTotalPrice"><?php// echo StoreProduct::formatPrice($model->total_price - $model->discount_price) ?></span><?php// echo Yii::app()->currency->main->symbol ?></td>
			</tr>-->
			<tr align="right">
				<td><b><?php echo Yii::t('OrdersModule.admin','Стоимость доставки') ?>:</b></td>
				<td><span id="orderDeliveryPrice"><?php echo StoreProduct::formatPrice($model->delivery_price); ?></span><?php echo Yii::app()->currency->main->symbol; ?></td>
			</tr>
			<tr align="right"><td colspan=2><hr style="border: 1px solid #000;"></td></tr>
			<tr align="right" style="font-size: 16px;">
				<td><b><?php echo Yii::t('OrdersModule.admin','Итого') ?>:</b></td>
				<td><span id="orderSummary"><?php echo StoreProduct::formatPrice($model->total_price) ?></span><?php echo Yii::app()->currency->main->symbol ?></td>
			</tr>
			<td></td>
			<td><span id="orderSummary"><?php echo $uah_full_price = Yii::app()->currency->convert(StoreProduct::formatPrice($model->total_price), 2); ?></span> грн</td>
		</tbody>
	</table>
</div>
<p style="padding-top:50px;">
<hr>
<h4>Добавить Фото Доставки</h4>
<?php if(!empty($photos_errors)): ?>
<ul style="display: block; margin: 15px 0;">
    <?php foreach ($photos_errors as $photos_error): ?>
    <li style="color: red"><?= $photos_error; ?></li>
    <?php endforeach; ?>
    <li style="color: red">Эти фото НЕ загружены!</li>
</ul>
<?php endif; ?>
<?php if(isset($photos)){?>
		        	<?php foreach ($photos as $key => $value) { ?>
		            <div class="b-photo" id="photo_<?= $key; ?>">
		                <div class="visual">
		                    <div class="img">
		                        <img src="<?php echo $value;?>" width="250"alt=""/>
								<a href="#" onclick="removeOrderPhoto(<?= $key; ?>, '<?= Yii::app()->request->csrfToken; ?>'); return false;">Удалить</a>
		                    </div>
		                </div>
		                <!-- <div class="title">г. Одесса</div> -->
		            </div>
		            <?php } ?>
	             <?php } ?>
<?php
		//  блок загрузки фото заказа
		
		foreach ($model->getOrderedProducts()->getData() as $key => $value) {
			# code...
			echo "Добавить фото доставки товара: ".$value->name."(ID=".$value->product_id.")";
			$csrfTokenName = Yii::app()->request->csrfTokenName;
			$csrfToken = Yii::app()->request->csrfToken;
			 $this->widget('CMultiFileUpload', array(
	                'name' => 'images_product'.$key,
	                'accept' => 'jpeg|jpg|gif|png', // useful for verifying files
	                'duplicate' => 'Duplicate file!', // useful, i think
	                'denied' => 'Invalid file type', // useful, i think
					'remove'=>'[x]', // удалить
	                // 'options'=>array(
				        // 'onFileSelect'=>'function(e, v, m){ alert("onFileSelect - "+v) }',
				        // 'afterFileSelect'=>"function(e, v, m){ 
					                	// var id = '$value->product_id';
										// var json={id:id,photo:v}
										// $.ajax({
											// type:'POST',
											// url:'/admin/orders/orders/update?id='+'$model->id',
											// dataType:'json',
											// data:{data:json,'$csrfTokenName':'$csrfToken'},
											// success:function(msg){console.log('$model->id');},
											// error:function(msg){console.log(msg);}
										// })
				        					 // }",
				        // 'onFileAppend'=>'function(e, v, m){ alert("onFileAppend - "+v) }',
				        // 'afterFileAppend'=>'function(e, v, m){ alert("afterFileAppend - "+v) }',
				        // 'onFileRemove'=>'function(e, v, m){ alert("onFileRemove - "+v) }',
				        // 'afterFileRemove'=>'function(e, v, m){ alert("afterFileRemove - "+v) }',
						// ),
	     			
	            ));
			
			echo "<br>";
		} 
?>