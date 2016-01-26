<?php

return [

	//путь к папке хранения файлов
	'file_root' => 'D:\laragon_3\www\first_php_7\files',
	
	//используя ajax
	'is_ajax' => true,
	
	//Кастомизация input[type=file]
	//Стандартно плагин для bootstrap http://plugins.krajee.com/file-input
	//Или указать view с кастомизацией input например 'partials.customieze_input_file'
	'customieze_input_file' => false,
	
	//Куда перенаправлять, если пользователь не авторизирован
	'redirect_where_not_login' => 'auth/login'
	
	/* не реализовано
	//запрашифать или нет проверку привилегий 
	'require_permissions' => 'false',
	
	'check_permissions_function' => function(){
		
	};
	*/
];