<?php
$full_path = (!empty($full_path)) ? $full_path : '/';
$id_prefix = (!empty($id_prefix)) ? $id_prefix : CFunc::get_random_string(3, 6);
$base_container_id = (!empty($base_container_id)) ? $base_container_id : $id_prefix . '_container';
$allowed_per_page = array('25', '50', '100', 'all'); // разрешенные значения количества товаров на странице

// для выбора типа сортировки формируем URL по принципу:
// если в $_GET указано количество на странице – то мы добавляем количество на странице в URL после сортировки
$get_per_page = (!empty($_GET['per_page']) && in_array($_GET['per_page'], $allowed_per_page))
    ? '/per_page/' . $_GET['per_page']
    : '';
$type_params = array(
    'default' => array(
        'url' => Yii::app()->createUrl($full_path . $get_per_page),
        'raw_url' => $full_path . $get_per_page,
        'text' => Yii::t('StoreModule.core', 'Default sorting'),
    ),
    'price' => array(
        'url' => Yii::app()->createUrl($full_path . '/sort/price' . $get_per_page),
        'raw_url' => $full_path . '/sort/price',
        'text' => Yii::t('StoreModule.core', 'Price: Low to High'),
    ),
    'price.desc' => array(
        'url' => Yii::app()->createUrl($full_path . '/sort/price.desc' . $get_per_page),
        'raw_url' => $full_path . '/sort/price.desc',
        'text' => Yii::t('StoreModule.core', 'Price: High to Low'),
    ),
);
// для выбора количества товаров на страницы формируем URL по принципу:
// если в $_GET указана сортировка – то в URL сначала сортировка, а потом количество на странице
$get_sort_type = (!empty($_GET['sort']) && in_array($_GET['sort'], array_keys($type_params)))
    ? (empty($_GET['per_page']))
        ? $type_params[$_GET['sort']]['url']
        : Yii::app()->createUrl($type_params[$_GET['sort']]['raw_url'])
    : Yii::app()->createUrl($full_path);
$per_page_params = array(
    '25' => array(
        'url' => $get_sort_type . '/per_page/25',
        'text' => Yii::t('StoreModule.core', 'Show') . ' 25',
    ),
    '50' => array(
        'url' => $get_sort_type . '/per_page/50',
        'text' => Yii::t('StoreModule.core', 'Show') . ' 50',
    ),
    '100' => array(
        'url' => $get_sort_type . '/per_page/100',
        'text' => Yii::t('StoreModule.core', 'Show') . ' 100',
    ),
    'all' => array(
        'url' => $get_sort_type . '/per_page/all',
        'text' => Yii::t('StoreModule.core', 'Show all'),
    ),
);
// цепляем ко всем URL поисковый параметр – если он указан (для результатов поиска)
if(!empty($search)){
    foreach ($type_params as $k => $p){
        $type_params[$k]['url'] .= '?q=' . $search;
    }
    foreach ($per_page_params as $k => $p){
        $per_page_params[$k]['url'] .= '?q=' . $search;
    }
}
?>
<script type="text/javascript">
    var typeParams = <?= json_encode($type_params); ?>;
    var perPageParams = <?= json_encode($per_page_params); ?>;
</script>
<?php /* сортировка товаров */ ?>
<div class="cat-sort-perpage" id="<?= $base_container_id; ?>">

    <select name="type_sort"
            class="cat-type-sort"
            id="<?= $id_prefix; ?>_type_list"
            onchange="sortCategorybyType('<?= $id_prefix; ?>');">
        <?php foreach ($type_params as $type_key => $type_param): ?>
            <?php
            $selected_type = (!empty($_GET['sort'] && ($_GET['sort'] == $type_key)))
                ? 'selected="selected"'
                : (empty($_GET['sort']) && ($type_key == 'default'))
                    ? 'selected="selected"'
                    : '';
            ?>
        <option value="<?= $type_key; ?>" <?= $selected_type; ?>><?= $type_param['text']; ?></option>
        <?php endforeach; ?>
    </select><select name="per_page"
            class="cat-per-page"
            id="<?= $id_prefix; ?>_per_page"
            onchange="setPerPage('<?= $id_prefix; ?>');">
        <?php foreach ($per_page_params as $per_page_key => $per_page_param): ?>
            <?php
            $selected_per_page = (!empty($_GET['per_page'] && ($_GET['per_page'] == $per_page_key)))
                ? 'selected="selected"'
                : (empty($_GET['per_page']) && ($per_page_key == '25'))
                    ? 'selected="selected"'
                    : '';
            ?>
            <option value="<?= $per_page_key; ?>" <?= $selected_per_page; ?>><?= $per_page_param['text']; ?></option>
        <?php endforeach; ?>
    </select>

</div>
<div id="<?= $id_prefix; ?>_fake_pager"></div>