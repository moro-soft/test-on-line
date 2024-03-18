<?php
date_default_timezone_set('Europe/Moscow');
$cur_time=date('Y-m-d h:i:s', time());
$db_seans	= new PDO('mysql:host=localhost;dbname=test', 'webuser', 'webuser');
$pref ='';
$otvet='О';
$vopros='В';
$tema='Т';
$spec='С';
$kategoria='К';
$name='admin'; 
$pass='pass';
$type_opros	= array('0'=>'неверный','1'=>'верный','2'=>'галка','3'=>'точка','4'=>'текст','5'=>'рейтинг');
$type_otvet	= array('0'=>'','1'=>'+','2'=>'^','3'=>'@','4'=>'~','5'=>'*');
/* 
Ответ2^	множественный выбор галка помечен^
Ответ3@	однозначный выбор точка помечен@
Ответ4~	текстовый вариант ответа помечен~	
Ответ5*	рейтнг	помечен*	
 */
$cons= array('otvet'	=>$otvet,
			'vopros'	=>$vopros,
			'tema'		=>$tema,
			'kategoria'	=>$kategoria, 
			'spec'		=>$spec
			);

if (!isset($_GET['txt'])) $_GET['txt'] = ''; //Сообщение обработчика
if (!isset($_GET['mes'])) $_GET['mes'] = ''; //по ошибке
if (!isset($_GET['form'])) $_GET['form'] = ''; // команда по умолчанию
if (!isset($_GET['fio'])) $_GET['fio'] = '';
if (!isset($_GET['email'])) $_GET['email'] = '';

$_POST+=$_GET; //print_r($_POST);

function go($form){ // преход на форму
	echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=?form=$form'>";
}

function go_back(){ // переход назад
	echo '<input action="action" onclick="window.history.go(-1); return false;" type="submit" value="Продолжить" />';
}

function db($q, $a = array()) { // получить данные запроса с массивом параметров
	$db_seans = new PDO('mysql:host=localhost;dbname=test', 'webuser', 'webuser');
	$s1 = $db_seans->prepare($q);
	$s1->execute($a); 
	return $s1; 
}
function db_soz($q) { // получить из СОЗ
	$db_soz	= new PDO('mysql:host=localhost;dbname=soz'	, 'webuser', 'webuser');
	$s2=$db_soz->prepare($q);
	$s2->execute(); 
	return $s2; 
}

//списки данных
function db_list_vid($vid) {
	return db("SELECT `id`,`text` FROM `texts` where `vid`='$vid' ")->fetchAll(); 
}
function db_list_soz($prefix,$tabl,$field) {
	return db_soz("SELECT DISTINCT $field FROM `".$prefix."_".$tabl."` order by $field asc ")->fetchAll(); 
}
function db_list_vid_like($vid,$like) {
	return db("SELECT DISTINCT X.`id`,X.`text` FROM `texts` X  where `vid`='$vid' and `text` LIKE '%". $like ."%' ORDER BY 2 LIMIT 30 ")->fetchAll(); 
}
function db_list_vid_like_tema($vid,$like,$tema) {
	return db("SELECT DISTINCT X.`id`,X.`text` FROM `tests` S LEFT JOIN `texts` X on (S.`tema`='$tema' and X.`id` = S.`".($vid=='В'?'vopros':'otvet')."`) where `vid`='$vid' and `text` LIKE '%$like%' ORDER BY 2 LIMIT 30 ")->fetchAll(); 
}
function db_list_otv($id) {
	return db("SELECT `B`.`id`,`C`.`text`,`B`.`verno` FROM `tests` `B` JOIN `texts` `C` ON  `B`.`otvet`= `C`.`id` where `vopros`='$id' ")->fetchAll(); 
}
function db_list_otv_vopros_tema($par) {
	return db("SELECT `id`,`otvet_`,`verno` FROM `tests_view` WHERE  `kategoria`= ? and `spec`= ? and `tema`= ? and `vopros`=? ORDER BY `otvet` ",$par)->fetchAll(); 
}

function db_list_tem_voprosov() {
	return db("SELECT * FROM `tests_view_voprosov_v_teme` WHERE  `tema` > 0 ;");
}
function db_list_tests_view($par) {
	return db("SELECT DISTINCT * FROM `tests_view` WHERE  `kategoria`= ? and `spec`= ? and `tema`= ? ORDER BY `vopros_`,`otvet` ",$par);
}

function db_list_tests_tema($par) {
	return db("SELECT DISTINCT * FROM `session_view_rez_tema`".( $par[0] > ' ' ? "WHERE `fio`= ? or `email` = ? ;" : "" ),$par);
}
function db_get_text_id($id) {// получить тексты данных по коду
	$s=db("SELECT `text` FROM `texts` where `id`='$id' ")->fetchAll();
	if (count($s)>0) return $s[0]['text']; 
	return ''; 
}
function db_get_id_sess_user_start_vopros($parr) { // получить сессию по коду
	//проверим наличие 
	$s=db("SELECT `id` FROM `sessions` where `user`='".$parr[0]."' and `start`='".$parr[1]."' and `vopros`='".$parr[2]."' ")->fetchAll(); 
	if (count($s)>0) return $s[0]['id']; // выдадим найденный id
	return 0; // неудача
}
function db_get_history_id_in_sess($parr) { // получить код теста по сессии
	//проверим наличие 
	$s=db("SELECT `test` FROM `history` where `test`='".$parr[0]."' and `session`='".$parr[1]."' ")->fetchAll(); 
	if (count($s)>0) return $s[0][0]; // выдадим найденный id
	return 0; // неудача
}
function db_get_history_in_sess($parr) { // получить историю ответов на вопрос1 по сессии0 
	return db("SELECT * FROM `history` where `test`='".$parr[1]."' and `session`='".$parr[0]."' "); 
}
function db_get_history_user($parr) { // получить историю ответов на вопрос1 по пользователю0 
	return db("SELECT * FROM `history` where `test`='".$parr[1]."' and `user`='".$parr[0]."' "); 
}
function db_get_history_voprosov_id_user($parr) { // получить историю ответов на вопросы Категории1,Спец2,Темы3 по пользователю0 
	$q="SELECT DISTINCT `vopros` FROM `history_view` WHERE `fio`= '".$parr[0]."' and `kategoria`= ".$parr[1]." and `spec`= ".$parr[2]." and `tema`= ".$parr[3]."  ";
	return db($q)->fetchAll();
}

function db_get_mess_test($parr) { // получить историю ответов на вопросы Категории1,Спец2,Темы3 по пользователю0 
	$q=db("SELECT DISTINCT `mess` FROM `history` where `test`='".$parr[0]."' "); 
	return $q;
}


function db_get_svg_test($parr) { // получить средний ретинг ответа0
	$q=db("SELECT AVG(`rating`) as `rating` FROM `history` where `test`='".$parr[0]."' ")->fetchAll(); 
	if (count($q)>0) return $q[0][0]; 
	else return 0;
}

function db_get_USER_test($parr) { // получить средний ретинг ответа0
	$q	= db("SELECT DISTINCT `user` FROM `history_view` where `test`='".$parr[0]."' and `vopros`='".$parr[1]."' ")->fetchAll(); 
	return count($q);
}
function db_get_pool_test($parr) { // получить средний ретинг ответа0
	$tek_otvet	= db("SELECT count(1) as `tek` FROM `history_view` where `test_otvet`='".$parr[0]."' ")->fetchAll(); 
	$all_otvet	= db("SELECT count(1) as `all` FROM `history_view` where `test`='".$parr[1]."' and `vopros`='".$parr[2]."' ")->fetchAll(); 
	if (count($all_otvet)>0 and count($tek_otvet)>0 and $all_otvet[0][0] > 0 ) return ''. $tek_otvet[0][0].' ('. round( 100*$tek_otvet[0][0]/$all_otvet[0][0] ,0) . '%)'; 
	else return 0;
}

function db_list_history_view($par) {// получить историю ответов на вопросы Категории0,Спец1,Темы2
	return db("SELECT DISTINCT * FROM `history_view` WHERE `kategoria`= ? and `spec`= ? and `tema`= ? ORDER BY `vopros`,`otvet_` ",$par);
}

function db_get_id_user($id) { //получить код пользователя с добавлением нового
	//проверим наличие 
	$s=db("SELECT `id` FROM `users` where id='$id' or `fio`='$id' ")->fetchAll(); 
	if (count($s)>0) return $s[0]['id']; // выдадим найденный id
	else db_insert_user(array($_POST['fio'],$_POST['email'],$_POST['start'],$_POST['gde'],0)); // новая запись
	$s=db("SELECT `id` FROM `users` where id='$id' or `fio`='$id' ")->fetchAll(); 
	if (count($s)>0) return $s[0]['id']; // выдадим найденный id
	return 0; // неудача
}
function db_get_id_vid($vid,$val) { //получить код текста вида по значению с добавлением нового
	if (empty($val)) return 0;
	//проверим наличие текста этого вида
	$s=db("SELECT `id` FROM `texts` where `vid`='$vid' and id='$val' ")->fetchAll(); 
	if (count($s)>0) return $s[0]['id']; // выдадим найденный id
	else { 
		$s=db("SELECT `id` FROM `texts` where `vid`='$vid' and text='$val' ")->fetchAll(); 
		if (count($s)>0) return $s[0]['id']; // выдадим найденный id по значению
		else { 
			//добавим новый текст вида
			db_insert_texts(array($vid,$val));
			//проверим наличия текста этого вида
			$s=db("SELECT `id` FROM `texts` where `vid`='$vid' and id='$val' ")->fetchAll(); 
			if (count($s)>0) return $s[0]['id']; // выдадим найденный id
			else { 
				$s=db("SELECT `id`,`text` FROM `texts` where `vid`='$vid' and text='$val' ")->fetchAll(); 
				if (count($s)>0) return $s[0]['id']; // выдадим найденный id по значению
			}
		}
	}
	return 0;
}

// изменение:

function db_update_sess($parr) { 
	global $db_seans;
	$q = "UPDATE `sessions` SET `".$parr[0]."` = ".$parr[1]."  WHERE `id` = ".$parr[2]." LIMIT 1 ;";
	$s=$db_seans->prepare($q);
	$s->execute(); 
}
function db_update_tests_verno() { // обновление ответов тестов
	global $otvet;
	if ( isset($_POST['hids']) ) { 
		$arr_id=explode(',', $_POST['hids']);
		for ($i= 0; $i < count($arr_id); $i ++) {
			$id=$arr_id[$i];
			db_update_tests_set_val_id(array('verno', isset($_POST["verno$id"]) ? $_POST["verno$id"] : '0', $id));  // обновить тип ответа
			if ( isset($_POST["otvet$id"]) ) db_update_tests_set_val_id(array('otvet', db_get_id_vid($otvet,$_POST["otvet$id"]), $id)); // обновить вариант ответа
		}	
	}
}	
function db_update_tests_set_val_id($parr) { // поля теста на значние для кода
	global $db_seans;
	$q = "UPDATE `tests` SET `".$parr[0]."` = ".$parr[1]."  WHERE `id` = ".$parr[2]." LIMIT 1 ;";
	$s=$db_seans->prepare($q);
	$s->execute(); 
}
function db_update_texts_set_val_id($parr) {// поля текста на значние для кода
	global $db_seans;
	$q = "UPDATE `texts` SET `".$parr[0]."`=".$parr[1]." WHERE `id`=".$parr[2]." LIMIT 1 ;";
	//$q = "UPDATE `texts` SET `?` = ? WHERE `texts`.`id` = ? LIMIT 1 ;";
	//$_POST['mes'] = $q;
	$s=$db_seans->prepare($q);
	$s->execute(); 
}
//добавление данных:
function db_insert_tests($parr) { //тесты
	global $db_seans;
	$q = "INSERT INTO `tests` (`id` ,`kategoria` ,`spec` ,`tema`,`vopros` ,`otvet` ,`verno` ) VALUES (NULL,?,?,?,?,?,?);";
	$s=$db_seans->prepare($q);
	$s->execute($parr); 
	return $s; 
}
function db_insert_texts($parr) { //тексты
	global $db_seans;
	$s=$db_seans->prepare("INSERT INTO `texts` (`id` ,`vid` ,`text`) VALUES (NULL ,?,?);");
	$s->execute($parr); 
	return $s; 
}

function db_insert_sess($parr) { //сессии тестироемых
	global $db_seans;
	$s=$db_seans->prepare("INSERT INTO `sessions` (`id` ,`time`,`ip`,`user`,`start`,`sec`,`test`,`verno`,`vopros`) VALUES (NULL,NOW( ),?,?,?,?,?,?,?);");
	$s->execute($parr); 
	return $s; 
}
function db_insert_user($parr) { //тестируемые
	global $db_seans;
	$s=$db_seans->prepare("INSERT INTO `users` (`id` ,`fio`,`email`,`last`,`gde`,`result`) VALUES (NULL , ?, ?, ?, ?, ?);");
	$s->execute($parr); 
	return $s; 
}

function db_update_user($parr) { 
	global $db_seans;
	$q = "UPDATE `users` SET `".$parr[0]."` = ".$parr[1]."  WHERE `fio` = '".$parr[2]."' LIMIT 1 ;";
	$s=$db_seans->prepare($q);
	$s->execute($parr); 
}
function db_insert_history($parr) { //тестируемые
	global $db_seans;
	$s=$db_seans->prepare("INSERT INTO `history` (`test` ,`verno` ,`user` ,`session` ,`rating` ,`mess`) VALUES (?, ?, ?, ?, ?, ?);");
	$s->execute($parr); 
	return $s; 
}
function db_del_history($parr) { //удаление старых данных 
	global $db_seans;
	$s = $db_seans->prepare("DELETE FROM `history` WHERE `test` = ? and `user` = ?;");
	$s->execute($parr); 
}
//удаление:
function db_del_id($ptabl,$parr) { // в таблице по коду
	return db("DELETE FROM `".$ptabl."` WHERE `id` = ? ;", $parr);
}

?>