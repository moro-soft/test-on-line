<?php // тестирование
include ('config.php');

include ('golova.php'); // начало разметки

if ( isset( $_POST['sad']) or isset( $_POST['menu']) and $_POST['menu'])
	include ('menu.php'); // навигация

include ('control.php'); // обработчик форм

include ('podval.php'); // конец разметки
?>