<?php

/**
 * Search view
 * @var $this CategoryController
 */

// Set meta tags
$this->pageTitle = Yii::t('StoreModule.core', 'Search');
$this->breadcrumbs[] = Yii::t('StoreModule.core', 'Search');

?>

<div class="catalog">

	<div class="products_list">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>$this->breadcrumbs,
				'homeLink'=>Yii::t('main','Home page')
			));
		?>

		<h1 class="page-title"><?php
			echo Yii::t('StoreModule.core', 'Search results');
			if(($q=Yii::app()->request->getParam('q')))
				echo ' "'.CHtml::encode($q).'"';
		?></h1>

		 <div class="sorts g-clearfix category-top-sorting">
             <?php $this->renderPartial('sort',
                 array(
                     'full_path' => 'store/category/search',
                     'id_prefix' => 'top',
                     'copy_pager' => true,
                     'search' => CHtml::encode($q),
                 )
             ); ?>
        </div>
        <!-- sorts (end) -->

        <div class="products catalog g-clearfix category-products-list">
		<?php
			if(isset($provider))
			{
				$this->widget('zii.widgets.CListView', array(
					'dataProvider'=>$provider,
					'ajaxUpdate'=>false,
					'template'=>'{items} {pager}',
					'itemView'=>'_product',
					'sortableAttributes'=>array(
						'name', 'price'
					),
                    'pager' => array(
                        'header' => false,
                        'prevPageLabel' => '&larr;',
                        'nextPageLabel' => '&rarr;',
                    ),
				));
			}
			else
			{
				echo Yii::t('StoreModule.core', 'No results');
			}
		?>
        </div>

        <!-- sorts (begin) -->
        <?php $this->renderPartial('sort',
            array(
                'full_path' => 'store/category/search',
                'id_prefix' => 'bottom',
                'base_container_id' => 'cat_bottom_sort',
                'search' => CHtml::encode($q),
            )
        ); ?>
		<div style="clear: left"></div>
        <!-- sorts (end) -->
		
	</div>
</div>
<script type="text/javascript">
    copyPager('top'); // 'top' должно соответствовать параметру 'id_prefix' при вызове сортировок
</script>