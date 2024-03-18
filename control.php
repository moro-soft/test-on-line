<?php // Обработчик команд

include ('forms.php'); //набор форм размети

if (isset($_POST['form'])) { 
	try { 
		switch ($_POST['form']) {
		case "list_opros"		: list_opros();		break;//запрос списка 	
		case "select_opros"		: select_opros();	break;//запрос списка 
		case "list_opros_res"	: list_opros_res();	break;//запрос списка результатов 
		case "start_opros"		: start_opros();	break;//запрос списка 
		case "stop_opros"		: stop_opros();		break;//завершение  
		case "total_opros"		: total_opros();		break;//история  
		case "list_opros_history": list_opros_history();	break;//список история  
		case "form_add"			: form_add();		break;//форма добавления  
		case "add_opros"		: add_opros();		break;//отправить данные формы на сервер 
		default: 
			list_opros();
			break;
			print 'Нет формы: '.$_POST['form'];
		}	
	}	
	catch (PDOException $e_stata) {
		print "Ошибка: " . $e_stata->GetMessage().'<br>'. $e_stata->getTraceAsString(); 
	}
}
?>