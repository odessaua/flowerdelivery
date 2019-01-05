<?php

/**
 * сортировка товаров в категориях
 */
class ProductSortingController extends SAdminController
{
    /**
     * выбор категории для сортировки в ней товаров
     * @param int $category_id
     */
    public function actionIndex($category_id = 0)
    {
        $categories = StoreCategory::flatTree();
        $products = array();
        if(!empty($category_id)){
            // получаем список товаров в категории
            $results = Yii::app()->db->createCommand()
                ->select('spcr.id, spcr.product, spi.name')
                ->from('StoreProductCategoryRef spcr')
                ->join('StoreProductImage spi', 'spi.product_id = spcr.product AND spi.is_main = 1')
                ->join('StoreProduct sp', 'sp.id = spcr.product')
                ->where('spcr.category=:category AND sp.is_active=1', array(':category' => $category_id))
                ->order('spcr.order ASC, spcr.product DESC')
                ->queryAll();

            // формируем элементы для сортировки
            if(!empty($results)){
                foreach ($results as $rk => $result) {
                    $products[$result['id']] .= '<img class="thumb-img" src="/uploads/product/'
                        . $result['name'] . '" title="Product #'
                        . $result['product'] . '"/>';
                }
            }
        }

        $this->render('index',
            array(
                'categories' => $categories,
                'category_id' => $category_id,
                'products' => $products,
            )
        );
    }

    /**
     * обработка сортировки товаров
     */
    public function actionSorting()
    {
        if(!empty($_POST['items'])){
            // обновляем порядок сортировки
            foreach ($_POST['items'] as $ik => $item) {
                StoreProductCategoryRef::model()
                    ->updateByPk($item, array('order' => ++$ik));
            }
            echo 1;
        }
    }
}