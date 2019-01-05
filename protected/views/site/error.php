<?php /*if (YII_DEBUG===true && $error): ?>
	<h2><?=$error['type']?></h2>

	<div class="error">
		<?=$error['message']?>
	</div*/?>
<?php
$error_codes = array(
    400 => array(
        'title' => 'Bad Request',
        'message' => 'The request made to the server is not true',
    ),
    403 => array(
        'title' => 'Access Denied',
        'message' => 'You are not allowed access to this section',
    ),
    404 => array(
        'title' => 'Page Not Found',
        'message' => 'The requested page is not on our website',
    ),
    500 => array(
        'title' => 'Internal Server Error',
        'message' => 'Internal server error - please contact Administrator',
    ),
    503 => array(
        'title' => 'Service Unavailable',
        'message' => 'Service is not available - please contact Administrator',
    ));
?>

<?php if(!empty($error) && in_array((int)$error['code'], array_keys($error_codes))): ?>

    <h2 class="perror-header"><?=Yii::t('main', $error_codes[$error['code']]['title']); ?>!</h2>
    <div class="error perror-message">
        <?=Yii::t('main', $error_codes[$error['code']]['message']); ?>.
    </div>

<?php else: ?>

	<h2><?=Yii::t('core','Ошибка')?></h2>

	<div class="error">
		<?=Yii::t('core','Ошибка обработки запроса.')?>
	</div>
<?php endif ?>