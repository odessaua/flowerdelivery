<?php

return array(
	'page/<url:.*?>.html'  => 'pages/pages/view',
	'pages/<url:.*?>/page/<page:\d>' => 'pages/pages/list',
	'pages/<url:.*?>' => 'pages/pages/list',
);