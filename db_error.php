<?php
/*
 * $event->exception->getMessage()
 * $event->exception->getCode()
 * $event->exception->getFile()
 * $event->exception->getLine()
 *
 * оформляем страницу произвольно
 */
?>
<script>
    // вывод ошибок в консоль браузера
    console.log(
        "DB Error:\n",
        "<?= $event->exception->getCode(); ?> \n",
        "<?= $event->exception->getMessage(); ?> \n",
        "<?= $event->exception->getFile(); ?> \n",
        "<?= $event->exception->getLine(); ?> \n"
    );
</script>

<style>
	body {
		background: url(uploads/deadrose.jpg);
		
	}
    .oops {
        margin-top: 50px;
		text-align: center;
    }
    .oops h1 {
		font-size:72px;
        text-align: center;
        color: #a90c65;
    }  .oops h2 {
        text-align: center;
        color: #983e71;
    }
	
.btn-purple {
    margin-top:90px;
    padding:15px;
    text-align: center;
    color: #fff;
    font: bold 18px/20px "Verdana", serif;
    width: 220px;
    height: 50px;
    background-color: #af3583;
    border-radius: 5px;
    cursor: pointer;
}
a:link { text-decoration: none; } 
a:visited {font-size:16px; color:white}
.btn-purple:hover {  background-color: #a90c65;
}
</style>

<div class="oops">
    <h1>OOOPS!</h1> 
	<h2>We are sorry!</h2>
	<h2>Something went wrong.<br> We are working on it.</h2><br>
    <span class="btn-purple"><a href="https://www.7roses.com">Return to Homepage</a></span>
</div>
