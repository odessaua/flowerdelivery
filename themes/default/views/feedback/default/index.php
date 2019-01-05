<?php

/**
 * @var $this Controller
 */

?>
<ul class="breadcrumbs">
        <li><a href="/"/><?=Yii::t('main','Home page')?></a></li>
        <li>&nbsp;/&nbsp;</li>
        <li><?=Yii::t('FeedbackModule.core','Feedback')?></li>
    </ul>
<div class="" style="float: left; width: 100%;">
<h1 class="page-title"><?php echo Yii::t('FeedbackModule.core', 'Feedback') ?></h1>
<?=Yii::t('FeedbackModule.core','<h3>Ukrainian Flowers and Gifts delivery service - '. $_SERVER['HTTP_HOST'].'</h3>');?>
<div class="data-form">

<?=Yii::t('FeedbackModule.core','<br><p>If you wish to make an enquiry please email us using our Feedback form below</p><br>')?>
<?php $form=$this->beginWidget('CActiveForm'); ?>

		<!-- Display errors  -->
		<?php echo $form->errorSummary($model); ?>

		<div class="s3">
			
			<?php echo CHtml::activeTextField($model,'name', array('required'=>true, 'placeholder'=>''.Yii::t('FeedbackModule.core', 'Your Name').'')); ?>
		</div>

		<div class="s3">
			
			<?php echo CHtml::activeTextField($model,'email', array('required'=>true, 'placeholder'=>''.Yii::t('FeedbackModule.core', 'Email').'')); ?>
		</div>

		<div class="s3">
			
			<?php echo CHtml::activeTextArea($model,'message', array('rows'=>15, 'required'=>true, 'placeholder'=>''.Yii::t('FeedbackModule.core', 'Message').'')); ?>
		</div>

		<?php if(Yii::app()->settings->get('feedback', 'enable_captcha')): ?>
		<div class="s1">
			<label><?php $this->widget('CCaptcha', array('clickableImage'=>true,'showRefreshButton'=>false)) ?></label>
			<?php echo CHtml::activeTextField($model, 'code', array('required'=>true, 'placeholder'=>''.Yii::t('FeedbackModule.core', 'Enter secret code').'')); ?>
		</div>
		<?php endif; ?>

		<div class="row buttons">
			<button type="submit" class="btn-purple"><?php echo Yii::t('FeedbackModule.core', 'Submit') ?></button>
		</div>
	</fieldset>
<?php $this->endWidget(); ?>
</div>

<div class="contacts">
    <?=Yii::t('FeedbackModule.core','<h3>Address Details:</h3><p>Ukraine, Odessa</p><p>12, Deribasovskaya str, off 25</p><br><h3>Phone number:</h3><p>office:  +38048 716 5465</p><p>mobile: +38050 562 0799</p><br><h3>Skype</h3>sevenrosesodessa')?>
    <script type="text/javascript" src="https://secure.skypeassets.com/i/scom/js/skype-uri.js"></script>
	<div id="SkypeButton_Call_sevenrosesodessa_1">
	 <script type="text/javascript">
	 Skype.ui({
	 "name": "dropdown",
	 "element": "SkypeButton_Call_sevenrosesodessa_1",
	 "participants": ["sevenrosesodessa"],
	 "imageSize": 32
	 });
	 </script>
	</div>
</div>
<div class="contacts" style="float: right;">
    <h3><?=Yii::t('FeedbackModule.core','Operating Hours')?>:</h3><table cellpadding=5><tr><td><?=Yii::t('FeedbackModule.core','<strong>Mon</strong> - <strong>Sat</strong>')?></td><td> 09:00-20:00 (UTC+2)</td></tr>
    <tr><td><?=Yii::t('FeedbackModule.core','<strong>Sunday</strong>')?></td><td> 09:00-18:00 (UTC+2)</td></tr></table>
    <script type="text/javascript">

    //var currenttime = '<!--#config timefmt="%B %d, %Y %H:%M:%S"--><!--#echo var="DATE_LOCAL" -->' //SSI method of getting server date
    var currenttime = '<? print date("Y H:i:s", time())?>' //PHP method of getting server date

    ///////////Stop editting here/////////////////////////////////


    var serverdate=new Date(currenttime)

    function padlength(what){
    var output=(what.toString().length==1)? "0"+what : what
    return output
    }

    function displaytime(){
    serverdate.setSeconds(serverdate.getSeconds()+1)

    var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
    document.getElementById("servertime").innerHTML=timestring
    }

    window.onload=function(){
    setInterval("displaytime()", 1000)
    }

    </script>
    <!--<br><h3><?//=Yii::t('FeedbackModule.core','Local Time in Ukraine')?>:</h3>
    <div class="contact-time"><span id="servertime"></span></div>-->

</div>
</div>
<!--Other cities addresses-->
<style>
    .other-cities-adr-block{
        width: 100%;
        float: left;
        height: auto;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .ocab-column{
        width: 300px;
        height: 215px; /* 310 = 3 */
        overflow-y: scroll;
        float: left;
        margin-left: 30px;
    }
    .ocab-column-item{
        width: 100%;
        height: auto;
        padding: 10px 0 15px;
        border-bottom: 1px dashed #999;
    }
</style>
<div class="other-cities-adr-block">
    <h3><?= Yii::t('FeedbackModule.core','Addresses of our representative offices in other cities'); ?></h3>
    <div class="ocab-column">
    <?php
    if(!empty($addresses)):
        foreach($addresses as $item):
    ?>
        <div class="ocab-column-item">
            <div class="ocabci-row"><?= $item->name; ?>, <?= Yii::t('FeedbackModule.core','Ukraine'); ?></div>
            <div class="ocabci-row"><?= Yii::t('FeedbackModule.core','Title'); ?>: <?= $item->firm_name; ?></div>
            <div class="ocabci-row"><?= Yii::t('FeedbackModule.core','Address'); ?>: <?= $item->firm_address; ?></div>
            <div class="ocabci-row"><?= Yii::t('FeedbackModule.core','Phone'); ?>: <?= $item->firm_phone; ?></div>
        </div>
    <?php
        endforeach;
    endif;
    ?>
    </div>
</div>