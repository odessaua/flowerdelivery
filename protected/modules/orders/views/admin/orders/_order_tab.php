<style type="text/css">
	div.userData input[type=text] {
		width: 385px;
	}
	div.userData textarea {
		width: 385px;
	}
	#orderedProducts {
		padding: 0 0 5px 0;
	}
	.ui-dialog .ui-dialog-content {
		padding: 0;
	}
	#dialog-modal .grid-view {
		padding: 0;
	}
	#orderSummaryTable tr td {
		padding: 3px;
	}
    div.userData div.row{
        margin-bottom: 10px;
    }
</style>

<div class="form wide padding-all">
<?php
if($model->isNewRecord)
    $action='create';
else
    $action='update';

$form_url = $this->createUrl($action) . ((!$model->isNewRecord) ? '/?id=' . $model->id : '/');
echo CHtml::form($form_url, 'post', array('id'=>'orderUpdateForm','enctype'=>'multipart/form-data'));

if($model->hasErrors())
    echo CHtml::errorSummary($model);
	
?>
	<table width="100%">
		<tr valign="top">
			<td width="50%">
				<!-- User data -->
				<div class="userData">
				<table class="table table-bordered table-striped table-hover">
					<thead class="thead-dark">
						<tr><th><?php echo "<h2>Order #".$model->id.": ".$model->receiver_city." - ".$model->datetime_del."</h2>";?></th>
						</tr>
					</thead>
					<tbody><tr>
					<?php if(!$model->isNewRecord): ?>
						<?php 
							//echo Yii::t('OrdersModule.admin', 'Данные пользователя');
							echo "<td>IP Address: " . $model->ip_address. "</td></tr><tr>";

							echo "<td>Country: " . $geoinfo['country_name']. ", ". $geoinfo['region_name']."</td></tr><tr>";

							echo "<td>City: " . $geoinfo['city']."</td></tr><tr>";

							echo "<td>Latitude: " . $geoinfo['latitude'].";";
							echo " Longitude: " . $geoinfo['longitude'].";</td></tr><tr>";
							echo "<td>";
							echo !empty($model['user_id'])?"Пользователь зарегистрирован":"Пользователь не зарегистрирован";
							echo "</td></tr><tr><td>";
							echo CHtml::link('https://7roses.com/cart/view/'.$model->secret_key ,array('/cart/view/'.$model->secret_key));
							echo "</td></tr><tr><td><div id='widget1' class='ui-widget'>";
							if (isset($_REQUEST['id']))
							echo CHtml::link('Импорт Заказа',array('/orders/admin/orders/import','id'=>$_REQUEST['id']),  array('class'=>'ui-button ui-widget ui-corner-all'));
							 ?>
					<?php endif;?>
					</div></td></tr >
					</tbody></table>
					<div class="row">
						<?php echo CHtml::activeLabel($model,'status_id', array('required'=>true)); ?>
						<?php echo CHtml::activeDropDownList($model, 'status_id', CHtml::listData($statuses, 'id', 'name')); ?>
					</div>

					<div class="row">
						<?php echo CHtml::activeLabel($model,'user_name', array('required'=>true)); ?>
						<?php echo CHtml::activeTextField($model,'user_name'); ?>
						<?php if($model->user_id): ?>
						<div class="hint">
							<?php echo CHtml::link(Yii::t('OrdersModule.admin', 'Редактировать пользователя'), array(
								'/users/admin/default/update',
								'id'=>$model->user_id,
							));
							?>
						</div>
						<?php endif; ?>
					</div>
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'country', array('required'=>true)); ?>
						<?php echo CHtml::activeTextField($model,'country'); ?>
					</div>
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'city', array('required'=>true)); ?>
						<?php echo CHtml::activeTextField($model,'city'); ?>
					</div>

					<div class="row">
						<?php echo CHtml::activeLabel($model,'user_email', array('required'=>true)); ?>
						<?php echo CHtml::activeTextField($model,'user_email'); ?>
					</div>

                    <?php
                    $payments_methods = CHtml::listData(StorePaymentMethod::model()->active()->orderByPosition()->findAll(), 'id', 'name');
                    $payments_methods[0] = '–';
                    ?>
                    <div class="row">
                        <?php echo CHtml::activeLabel($model,'payment_id', array()); ?>
                        <?php echo CHtml::activeDropDownList($model, 'payment_id', $payments_methods); ?>
                    </div>

                    <div class="row">
                        <?php echo CHtml::activeLabel($model,'payment_status', array()); ?>
                        <?php echo CHtml::activeDropDownList($model, 'payment_status', $model->payment_statuses); ?>
                    </div>

					<div class="row">
						<?php echo CHtml::activeLabel($model,'user_phone'); ?>
						<?php echo CHtml::activeTextField($model,'user_phone'); ?>
					</div>
					
					<!-- Получатель -->
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'receiver_name'); ?>
						<?php echo CHtml::activeTextField($model,'receiver_name'); ?>
					</div>
					<div class="row">
					<?php if($model->isNewRecord){ ?>
						
							<?php echo CHtml::activeLabel($model,'receiver_city'); ?>
							<?php echo CHtml::dropDownList("receiver_city",Yii::app()->db->createCommand()
									->select('object_id, name')
									->from('cityTranslate')
									->where('language_id = 1')
									->order('name ASC')
									->queryAll(), 
								              CHtml::listData(Yii::app()->db->createCommand()
								      ->select('object_id, name')
									->from('cityTranslate')
									->where('language_id = 1')
									->order('name ASC')
									->queryAll(),
								              "name","name"
								         )); 
								}  ?>
					</div>
					<div class="row">
						<?php echo CHtml::activeLabel($model,'user_address'); ?>
						<?php echo CHtml::activeTextField($model,'user_address'); ?>
					</div>
					
					<div class="row">
							<?php echo CHtml::activeLabel($model,'phone1'); ?>
							<?php echo CHtml::activeTextField($model,'phone1'); ?>
					</div>
						
					<div class="row">
							<?php echo CHtml::activeLabel($model,'phone2'); ?>
							<?php echo CHtml::activeTextField($model,'phone2'); ?>
					</div>
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'datetime_del'); ?>
						<?php echo CHtml::activeTextField($model,'datetime_del'); ?>
					</div>
					<div class="row">
						<?php echo CHtml::activeLabel($model,'doPhoto'); ?>
						<?php echo CHtml::activeCheckBox($model, 'doPhoto',array('checked'=>$model->doPhoto?"1":"0")); ?>
					</div>
					<div class="row">
						<?php echo CHtml::activeLabel($model,'do_card'); ?>
						<?php echo CHtml::activeCheckBox($model, 'do_card',array('checked'=>$model->do_card?"1":"0")); ?>
					</div>
					<?php if(!$model->isNewRecord): ?>
					<div class="row">
							<?php echo CHtml::activeLabel($model,'card_transl'); ?>
							<?php echo CHtml::activeCheckBox($model, 'card_transl',array('checked'=>$model->card_transl?"1":"0")); ?>
					</div>
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'user_comment'); ?>
						<?php echo CHtml::activeTextArea($model,'user_comment'); ?>
					</div>
					
					<div class="row">
						<?php echo CHtml::activeLabel($model,'card_text'); ?>
						<?php echo CHtml::activeTextArea($model,'card_text'); ?>
					</div>
					


						<div class="row">
							<?php echo CHtml::activeLabel($model,'admin_comment'); ?>
							<?php echo CHtml::activeTextArea($model,'admin_comment'); ?>
							<div class="hint"><?php echo Yii::t('OrdersModule.admin', 'Этот текст не виден для пользователя.'); ?></div>
						</div>
					<?php endif;?>
				</div>
			</td>
			<td>
				<!-- Right block -->
				<?php if(!$model->isNewRecord): ?>
					<div style="float: right;padding-right: 10px">
						<a href="javascript:openAddProductDialog(<?php echo $model->id ?>);"><?php echo Yii::t('OrdersModule.admin','Добавить продукт') ?></a>
					</div>
					<div id="dialog-modal" style="display: none;" title="<?php echo Yii::t('OrdersModule.admin','Добавить продукт') ?>">
						<?php
						$this->renderPartial('_addProduct', array(
							'model'=>$model,
						));
						?>
					</div>

					<h4><?php echo Yii::t('OrdersModule.admin','Продукты') ?></h4>

					<div id="orderedProducts">
						<?php
						
						$this->renderPartial('_orderedProducts', array(
							'model'=>$model,
							'photos'=>$photos,
							'orderPhoto' => $orderPhoto,
                            'photos_errors' => $photos_errors,
						));
						?>
					</div>

                    <?php if(!empty($wfp_order)): ?>
                    <!--WayForPay info-->
                    <?php
                    $wfp_trans = array(
                            'Created' => 'Новый платеж',
                            'InProcessing' => 'В обработке',
                            'WaitingAuthComplete' => 'Успешный Hold',
                            'Approved' => 'Успешный платеж',
                            'Pending' => 'На проверке Antifraud',
                            'Expired' => 'Истек срок оплаты',
                            'Refunded' => 'Возврат платежа',
                            'Voided' => 'Платеж аннулирован',
                            'Declined' => 'Отклонен',
                            'RefundInProcessing' => 'Возврат в обработке',
                            'Ok' => 'Операция выполнена без ошибок',
                            'Declined  To Card Issuer' => 'Отказ Банка эмитента проводить операцию',
                            'Bad CVV2' => 'Неверный CVV2 код',
                            'Expired card' => 'Карта просрочена или неверно указан срок действия',
                            'Insufficient Funds' => 'Недостаточно средств',
                            'Invalid Card' => 'Введен неверный номер карты, либо карта в недопустимом состоянии',
                            'Exceed Withdrawal Frequency' => 'Превышен лимит операций по карте - возможно карта не открыта для оплаты в интернет',
                            'Three Ds Fail' => 'Невозможно выполнить 3DS транзакцию, либо неверный код подтверждения 3DS',
                            'Format Error' => 'Ошибка на стороне мерчанта — неверно сформирована транзакция',
                            'invalid Currency' => 'Ошибка на стороне мерчанта- неверная валюта',
                            'Invalid Currency' => 'Ошибка на стороне мерчанта- неверная валюта',
                            'Duplicate Order ID' => 'Дублирующий номер заказа',
                            'Invalid signature' => 'Неправильная подпись мерчанта',
                            'Invalid Signature' => 'Неправильная подпись мерчанта',
                            'Fraud' => 'Фродовая транзакция согласно антифрод фильтров',
                            'Parameter `{param_name}` is missing' => 'Один или несколько обязательных параметров не переданы',
                            'Token not found' => 'Попытка списания с карты клиента по токену неуспешна - используется неверное значение',
                            'API Not Allowed' => 'Данный API не разрешен к использованию для мерчанта',
                            'Merchant Restriction' => 'Превышен лимит Магазина или транзакции запрещены Магазину',
                            'Authentication unavailable' => '3-D Secure авторизация недоступна',
                            'Account Not Found' => 'Аккаунт не найден',
                            'Gate Declined' => 'Отказ шлюза в выполнении операции',
                            'Refund Not Allowed' => 'Возврат не может быть выполнен',
                            'Cardholder session expired' => 'Сессия пользователя истекла',
                            'Cardholder canceled the request' => 'Транзакция отменена пользователем',
                            'Illegal Order State' => 'Попытка выполнения недопустимой операции для текущего состояния платежа',
                            'Order Not Found' => 'Транзакция не найдена',
                            'Refund Limit Excended' => 'Превышено допустимое число попыток произвести возврат (Refund)',
                            'ScriptError' => 'Ошибка сценария',
                            'nvalid Amount' => 'Неправильная сумма',
                            'Transaction in processing' => 'Заказ обрабатывается. Заказ все еще находится в процессе обработки платежным шлюзом',
                            'Transaction Is Delayed' => 'Клиент решил отложить оплату, ему на почту отправлена ссылка для завершения платежа',
                            'Invalid commission' => 'Неверная комиссия',
                            'Invalid Commission' => 'Неверная комиссия',
                            'Transaction is pending' => 'Транзакция на проверке Antifraud',
                            'Transaction Is Pending' => 'Транзакция на проверке Antifraud',
                            'Card limits failed' => 'Превышен лимит по карте',
                            'Card Limits Failed' => 'Превышен лимит по карте',
                            'Merchant Balance Is Very Small' => 'Недостаточно средств на балансе мерчанта',
                            'Invalid Confirmation Amount' => 'Неправильная сумма подтверждения верификации карты',
                            'RefundInProcessing' => 'Запрос на возврат принят и будет проведен как только на балансе магазина будет достаточно денег для его проведения',
                            'Refund In Processing' => 'Запрос на возврат принят и будет проведен как только на балансе магазина будет достаточно денег для его проведения',
                            'External decline while credit' => 'Отказ в зачислении средств на карту получателя',
                            'External Decline While Credit' => 'Отказ в зачислении средств на карту получателя',
                            'Exceed Withdrawal Frequency While Credit' => 'Превышен лимит при зачислении средств на карту получателя',
                            'Partial void is not supported' => 'Частичная отмена холда не доступна',
                            'Partial Void Is Not Supported' => 'Частичная отмена холда не доступна',
                            'Refused a credit' => 'Отказано в кредите',
                            'Refused A Credit' => 'Отказано в кредите',
                            'Invalid phone number' => 'Неверный номер телефона',
                            'Invalid Phone Number' => 'Неверный номер телефона',
                            'Transaction is awaiting credit decision' => 'Ожидание решения о предоставлении кредита',
                            'Transaction Is Awaiting Credit Decision' => 'Ожидание решения о предоставлении кредита',
                            'Restricted card' => 'Карта заблокирована в Банке',
                            'Wait 3ds data' => 'Ожидание 3d secure верификации',
                            'Wait 3ds Data' => 'Ожидание 3d secure верификации',
                        );
                    ?>
                    <div style="margin-top: 30px;">
                        <h4>WayForPay info:</h4>
                        <div style="margin: 0 0 10px;">
                            <b>Код ответа сервера: </b>
                            <span><?= $wfp_order->reasonCode . ' – ' . ((!empty($wfp_trans[$wfp_order->reason])) ? $wfp_trans[$wfp_order->reason] : $wfp_order->reason); ?></span>
                        </div>
                        <div style="margin: 0 0 10px;">
                            <b>Статус транзакции: </b>
                            <span><?= (!empty($wfp_trans[$wfp_order->transactionStatus])) ? $wfp_trans[$wfp_order->transactionStatus] : $wfp_order->transactionStatus; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

				<?php endif;?>

			</td>
		</tr>
	</table>
	<?php echo CHtml::endForm(); ?>