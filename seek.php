<?php header('Content-Type: text/html; charset=utf-8');
include ('config.php');
// $bd = @mysqli_connect('localhost', 'soz', 'soz', 'soz') or die("Ошибка соединения с базой данных: ".mysqli_error($this->link));
// mysqli_query($bd, "SET NAMES utf8");
//$return_arr[] =  'Есть варианты:'.$_GET['tema']; 
if( isset($_GET['key'])) {   //Тип поиска - таких может быть бесконечно много - передается с autocomplete
	$a = db_list_vid_like_tema(( $_GET['vid']=='vopros' ? $vopros : $otvet ), ( $_GET['term']>' ' ? $_GET['term'] : '' ),$_GET['tema']);
	if (count($a) == 0) $return_arr[] =  'Не найдено...'; 
	for ($i= 0; $i < count($a); $i ++) {
		  $return_arr[] =  $a[$i][1]; 
	}
	//echo json_encode($a);
	 // $sql = "SELECT DISTINCT ".$_GET['key']." FROM soz_tickets WHERE ".$_GET['key']." LIKE '%". mysqli_real_escape_string($bd,$_GET['term']) ."%' ORDER BY 1 LIMIT 30"; 
	 // $fetch = mysqli_query($bd, $sql);  //$_GET['term'] - поисковый запрос от autocomplete
	 // while ($podrow = mysqli_fetch_array($fetch))    {
		//формируем ассоциативный массив результата поиска
		  // $return_arr[] =  $podrow[0]; // array(  'label' => $tt,  'value' => $podrow['keywords']);
		  // $count++;
	// }
	echo json_encode($return_arr);     //возвращает результаты поиска скрипту , JSON_UNESCAPED_UNICODE
}
?>