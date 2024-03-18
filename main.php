<?php
include ('config.php');
include ('htm.php');
include ('menu.php');

if (!isset($_GET['txt'])) $_GET['txt'] = ''; //Сообщение обработчика
if (!isset($_GET['do'])) $_GET['do'] = 'list_test'; // команда по умолчанию
$_POST+=$_GET; //print_r($_POST);

// Обработчик команд

if (isset($_POST['do'])) { 
	try { 
		// print_r($_POST);
		// print_r($_GET);
		switch ($_POST['do']) {
		case "user" : // форма  теста
			include ('user.php');

		case "list_test" : //запрос списка тестов
			$data = db("SELECT `spec_`,`kategoria_`,`tema_`,`spec`,`kategoria`,`tema`, count(distinct `vopros`) as `voprosov` FROM `tests_view` GROUP BY 1,2,3;"); //ORDER
			//print_r($data->fetchAll());
			print '<h1>Выберите тест по теме:</h1><div class=c0><ol>';
			while ($r = $data->fetch() ) {
				$p='?do=start_test&kategoria='.$r['kategoria'].'&spec='.$r['spec'].'&tema='.$r['tema'].'&voprosov='.$r['voprosov'];
				print '<li><a href="'.$p.'" >все вопросы</a> или <a href="'.$p.'&voprosN=1'.'" >с первого</a> '.$r['spec_'].' / '.$r['kategoria_'].' / '.$r['tema_'].'</li>' ;
			}
			print '</ol></div>';
			break;

		case "list_test_res" : //запрос списка результатов тестов
			$data = db("SELECT DISTINCT * FROM `tests_view_rez_tema`".( $_POST['fio'] > ' ' ? "WHERE `fio`= ? or `email` = ? ;" : "" ),array($_POST['fio'],$_POST['email']));
			//print_r($data->fetchAll());
			print '<div class=c0><ol>';
			while ($r = $data->fetch() ) {
				print '<li>'.$r['time'].' '.$r['ip'].' '.$r['fio'].' <a href="?do=start_test&kategoria='.$r['kategoria'].'&spec='.$r['spec'].'&tema='.$r['tema'].'" >'
				.$r['spec_'].' / '.$r['kategoria_'].' / '.$r['tema_'].'</a> - из '
				.$r['verno_v_voprose'].' - правильных:'
				.$r['verno_otv'].' не верных:'
				.$r['ne_verno_otv'].' всего не точно: '
				.$r['ne_tochno'].'</li>';
			}
			print '</div>
			<div class=c0>
				<form action="?do=list_test_res" method="post">
				<input name="form" type="hidden" value="start_test">
				<label>Ваши данные:</label><input name="fio" type="fio" size=25 value="'.$_POST['fio'].'" placeholder="ФИО" >
				<input name="email" type="email" size=25 value="'.$_POST['email'].'" >
				<button type="submit" name="bot" value="Выбор">Результаты</button>
			</div>
			</form></ol>';
			break;
		case "start_test" : //запрос списка тестов
			$data = db("SELECT DISTINCT `vopros`,`otvet`,`vopros_`,`otvet_`,`verno`,`id` FROM `tests_view` WHERE kategoria= ? and spec= ? and tema= ? ORDER BY 1,2 ;"
					,array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
			//print_r($data->fetchAll());		
			print '<h1>Тест по теме: '.db_get_text_id($_POST['tema']).'</h1><h4>Выберите правильные варианты:</h4>
			<form action="?do=stop_test" method="post">
			<div>
			<input name="form" type="hidden" value="start_test">
			<input name="start" type="hidden" value="'.$cur_time.'">
			<input name="sec" type="hidden" value="'.time().'">
			<input name="tema" type="hidden" value='.$_POST['tema'].'>
			<input name="kategoria" type="hidden" value='.$_POST['kategoria'].'>
			<input name="spec" type="hidden" value='.$_POST['spec'].'>
			<input name="voprosov" type="hidden" value='.$_POST['voprosov'].'>
			'.(isset( $_POST['voprosN']) ? '<input name="voprosN" type="hidden" value='.(0+$_POST['voprosN']).'>' : '' ) .'
			</div>
			<div class=c0>
			';
			$tek_vopros=-1;
			$i=0; // номер ответа
			$n=0; // номер вопроса
			$ord='';
			$p='';
			while ($r = $data->fetch() ) {
				if ($r['vopros']!=$tek_vopros) {
					if (( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) and $tek_vopros > 0) print $p.'</ol></div>';
					$n++;
					if ( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) {
						print '<div class=c1><h2>'.$r['vopros_'].'</h2><ol>';
					}
					$tek_vopros=$r['vopros'];
					$p='';
				}
				if ( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) {
					$i++;
					$id=$r['id'];
					$li="<li><div><input name='v_$i' type='checkbox' value='$id' id='$id' ><label for='$id' >".$r['otvet_']."</label></div></li>";
					if (rand(0,1)==1) {
						$p=$p.$li;
						$ord.='1';
					} else {
						$p=$li.$p;
						$ord.='0';
					}
				}
			}
			if ( $tek_vopros>0 and (! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) ) print $p.'</ol></div>';
			print '<div class=c0>
			<label>Тестируемый : </label><input name="fio" type="fio" size=25 value="Болотов А.А." placeholder="ФИО" >
			<input name="email" type="email" size=25 value="bolotov_aa@segezha-group.com" >
			<input name="ord" type="hidden" value='.$ord.'>
			<button type="submit" name="bot" value="Выбор">Выбрать</button>
			</div></div>
			</form>';
			break;
			
		case "stop_test" : //завершение теста
			foreach( $_POST as $key => $value) 
				if (substr($key,0,2)=='v_')	db_insert_sess(array($_SERVER[REMOTE_ADDR], db_get_id_user($_POST['email']), $_POST['start'], (time()-$_POST['sec']), $value )); // добавить ответ пользователя на тест	
			//print_r($_SERVER);
			print 'Тест принят и отправлен на вашу почту!';
			$data =		db("SELECT DISTINCT `vopros`,`otvet`,`vopros_`,`otvet_`,`verno`,`id` FROM `tests_view` WHERE kategoria= ? and spec= ? and tema= ? ORDER BY 1,2 ;"
						,array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
			print '<h1>Правильные ответы теста: '.db_get_text_id($_POST['tema']).'</h1><h4>Ваш выбор вариантов:</h4>
			<div class=c0>';
			$tek_vopros=-1;
			$i=0; // номер ответа
			$iv=1; // флаг правильности ответов 
			$n=0; // номер вопроса
			$p='';
			while ($r = $data->fetch() ) {
				if ($r['vopros']!=$tek_vopros) {
					if (( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) and $tek_vopros>0 ) print $p.'</ol>Отвечено '.( $iv ? "<i class=green>СОВЕРШЕННО</i>" : "<i class=red>НЕ</i>" ).' точно!</div>';
					$n++;
					if ( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) {
						$iv=1;
						print '<div class=c1><h2>'.$r['vopros_'].'</h2><ol>';
					}	
					$tek_vopros=$r['vopros'];
					$p='';
				}
				if ( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) {
					$i++;
					$id=$r['id'];
					$v=($_POST['v_'.$i]==$id) == ($r['verno']==1);
					$iv=$iv * $v;
					$li=''.( $r['verno']==1	? "<input type='checkbox' class='noborder' checked><label></label>" : '' )."<li>
					<div><input type='checkbox' class='".( $v ? "green" : "red" )."' ".( $_POST['v_'.$i]==$id ? 'checked' :  ' ' ).'><label>'.$r['otvet_'].'</label>
					</div></li>';//.substr($_POST['ord'],$i-1,1).'/'.$_POST['ord'].'/'.$i;
					if (substr($_POST['ord'],$i-1,1)=='1') {
						$p=$p.$li;
					} else {
						$p=$li.$p;
					}

				}		
			}
			if ( $tek_vopros>0 and (! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) ) print $p.'</ol>Отвечено '.( $iv ? "<i class=green>СОВЕРШЕННО</i>" : "<i class=red>НЕ</i>" ).' точно!</div>';
			print '
			</div></ol>
			<div class=c0>
				<form action="?do='.( (isset( $_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov']) ? 'start_test':'list_test_res').'" method="post">
				<input name="form" type="hidden" value="start_test">
				<input name="start" type="hidden" value="'.$cur_time.'">
				<input name="sec" type="hidden" value="'.time().'">
				<input name="tema" type="hidden" value='.$_POST['tema'].'>
				<input name="kategoria" type="hidden" value='.$_POST['kategoria'].'>
				<input name="spec" type="hidden" value='.$_POST['spec'].'>
				<input name="voprosov" type="hidden" value='.$_POST['voprosov'].'>
				'.(isset( $_POST['voprosN']) ? '<input name="voprosN" type="hidden" value='.(1+$_POST['voprosN']).'>' : '' ) .'
				<label>Ваши данные:</label><input name="fio" type="fio" size=25 value="'.$_POST['fio'].'" placeholder="ФИО" >
				<input name="email" type="email" size=25 value="'.$_POST['email'].'" >
				<button type="submit" name="bot" value="Выбор">'.( ( isset( $_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov'] ) ? 'Далее':'Результаты').'</button>
			</div>
			</form>';
			break;
			
		case "form_add" : // форма добавления теста
			if (!isset($_GET['spec'])) $_GET['spec']=0;
			if (!isset($_GET['kategoria'])) $_GET['kategoria']=0;
			if (!isset($_GET['tema'])) $_GET['tema']=0;
			if (!isset($_GET['vopros'])) $_GET['vopros']=0;
			print '<h2>Форма добавления теста</h2>
			<form action="?do=add_test" method="post">
			<div class=c0>
			<div class=c1>
			<input name="form" type="hidden" value="form_add">';
			$a_tema		= db_list_vid($tema);
			$a_kategoria= db_list_vid($kategoria);
			$a_spec		= db_list_vid($spec);
			$tema		= db_get_text_id($_GET['$tema']);
			$vopros		= db_get_text_id($_GET['vopros']);
			//if ($vopros	=='') $vopros='   ';
			$a_list_otv	= db_list_otv($_GET['vopros']);
			$p='<div>Специализация: 
			<select id="spec" name="spec" ondblclick="getNewValue(\'spec\',\'spec_new\')" >';
			for ($i= 0; $i < count($a_spec); $i ++) $p.="<option value='".$a_spec[$i][0]."' >".$a_spec[$i][1]."</option>";
			$p=str_replace("value='".$_GET['spec']."'","value='".$_GET['spec']."' selected ",$p).'</select>
			<input id="spec_new" name="spec_new" type="text" class="off" >
			<i onclick="getNewValue(\'spec\',\'spec_new\')" >+</i>
			</div>';
			// <input name="spec" type="text" list="spec"><datalist id="spec" >';
			// for ($i= 0; $i < count($a_spec); $i ++) $p.="<option value='".$a_spec[$i][0]."' >".$a_spec[$i][1]."</option>";
			// $p=str_replace("value='".$_GET['spec']."'","value='".$_GET['spec']."' selected ",$p).'</datalist></div>';

			$p.='<div>Категория: 
			<select id="kategoria" name="kategoria" ondblclick="getNewValue(\'kategoria\',\'kategoria_new\')" >';
			for ($i= 0; $i < count($a_kategoria); $i ++) $p.="<option value='".$a_kategoria[$i][0]."' >".$a_kategoria[$i][1]."</option>";
			$p=str_replace("value='".$_GET['kategoria']."'","value='".$_GET['kategoria']."' selected ",$p).'</select>
			<input id="kategoria_new" name="kategoria_new" type="text"  class="off">
			<i onclick="getNewValue(\'kategoria\',\'kategoria_new\')" >+</i>
			</div>';
			$p.='<div>Тема: 
			<select id="tema" name="tema" ondblclick="getNewValue(\'tema\',\'tema_new\')">';
			for ($i= 0; $i < count($a_tema); $i ++) $p.="<option value='".$a_tema[$i][0]."' >".$a_tema[$i][1]."</option>";
			$p=str_replace("value='".$_GET['tema']."'","value='".$_GET['tema']."' selected ",$p).'</select>
			<input id="tema_new" name="tema_new" type="text"  class="off">
			<i onclick="getNewValue(\'tema\',\'tema_new\')" >+</i>
			</div>';
			// <input id="tema" name="tem" type="hidden" value="'.$_GET['$tema'].'">
			// <input name="tema" type="text" placeholder="" value="'.$tema.'" list="tema"><datalist id="tema" >';
			// for ($i= 0; $i < count($a_tema); $i ++) $p.="<option>".$a_tema[$i][1]."</option>";
			// $p=str_replace("value='".$_GET['tema']."'","value='".$_GET['tema']."' selected ",$p).'</datalist></div>';

			$p.='<div>Вопрос: <input name="multi" type="checkbox" value=1 id="multi"><label for="multi"> мульти ввод с форматом: ?/!+/!/!//?/!/!/!+//</label> 
			<textarea id="vopros"  type=text name="vopros" placeholder="поиск вопроса по слову...">'.$vopros.'</textarea></div>';
			$p.='<div>Вариант ответа:<input name="verno" type="checkbox" value=1 id="cb"><label for="cb">- верный</label> 
			<textarea type=text name="otvet" value="" placeholder="поиск ответа по слову..."></textarea></div>';
			$p.='<div><button type="submit" name="bot" value="Добавить">Добавить</button></div>';
			$p.='</div>
			<div class="c5">
			 <div class="T">
			  <div class="TB">
			   <div class=TR>
				<div class="TD">№</div>
				<div class="TD">X</div>
				<div class="TD">Варианты ответа</div>
				<div class="TD">верный</div>
			   </div>
			';
			$hids='';
			for ($i= 0; $i < count($a_list_otv); $i ++) {
				$id=$a_list_otv[$i][0];
				$hids.=','.$id;
				$p.="<div class=TR>
				 <div class=TD>".($i+1)."</div>
				 <div class=TD><input name=for_del type=radio value=$id id='o$id' ><label for=o$id>&nbsp;</label></div>
				 <div class=TD><textarea name=otvet$id placeholder='пустой'>".$a_list_otv[$i][1]."</textarea></div>
				 <div class=TD><input name=verno$id type=checkbox value=1 id=cb$id ".( $a_list_otv[$i][2] == 1 ? ' checked ' : '' )."><label for=cb$id>&nbsp;</label></div>
				</div>";
			}	
			$p.='
				<div class=TR>
				 <div class="TD"> </div>
				 <div class="TD"><button type=submit name=bot value="Удалить">Удалить</button></div>
				 <div class="TD"> </div>
				 <div class="TD"><button type=submit name=bot value="Изменить">Изменить</button></div>
				</div>
			   </div>
			  </div>
			 </div>
			</div></div>
			'.( $hids==='' ? '' : '<input name="hids" type="hidden" value="'.substr($hids,1).'">' ).'
			</form>'.$_POST['txt'];
			print $p; 			
?>
<script language="JavaScript">
<!-- 
var t=document.getElementById('tema');
//var v=document.getElementById('vopros');
t.onchange = function(){
var t=document.getElementById('tema');
var idtema=t.options[t.options.selectedIndex].value;
$("[name=vopros]"	).autocomplete({ minLength: 1, appendTo: '', open: function(event, ui) { }, source: 'seek.php?key=short&vid=vopros&tema='+idtema});
$("[name=otvet]"	).autocomplete({ minLength: 1, appendTo: '', open: function(event, ui) { }, source: 'seek.php?key=description&vid=otvet&tema='+idtema});
}; 
t.onchange();
var tx = document.getElementsByTagName('textarea');//РАСТЯГИВАЕМ_textarea
for (var i = 0; i < tx.length; i++) {
tx[i].setAttribute('style', 'height:' + (tx[i].scrollHeight) + 'px;overflow-y:hidden;');
tx[i].addEventListener("input", OnInput, false);
}
function OnInput() {
this.style.height = 'auto';
this.style.height = (this.scrollHeight) + 'px';//console.log(this.scrollHeight);
}
function getNewValue(name1,name2) {
	document.getElementById(name2).classList.remove('off');	
	document.getElementById(name1).classList.add('off');	
//	alert(document.getElementById(name1).classList);
}
//-->
</script>
<?php
			break;
		case "add_test" : // отправить данные формы на сервер 
			//print_r($_POST);
			if ( ! isset($_POST['verno'])) $_POST['verno']=0;
			if ( ! isset($_POST['form'])) $_POST['form']='form_add_new';
			if ( ($_POST['tema_new'])		> '')	$_POST['tema']=$_POST['tema_new'];
			if ( ($_POST['kategoria_new'])	> '')	$_POST['kategoria']=$_POST['kategoria_new'];
			if ( ($_POST['spec_new'])		> '')	$_POST['spec']=$_POST['spec_new'];
			
			$id_kategoria	=db_get_id_vid($kategoria,$_POST['kategoria']);
			$id_spec		=db_get_id_vid($spec,$_POST['spec']);
			$id_tema		=db_get_id_vid($tema,$_POST['tema']);
			$id_vopros		=db_get_id_vid($vopros,$_POST['vopros']);
			
			if ($_POST['bot']=="Добавить") {
				if ( isset($_POST['multi'])) {
					$a = array_unique(explode("\r\n\r\n", $_POST['vopros'])); // абзац
					for ($i= 0; $i < count($a); $i ++) {
						if ($a[$i] > '') {
							$ar = array_unique(explode("\r\n",$a[$i])); //строчки
							$flag=1;
							for ($ir= 0; $ir < count($ar); $ir ++) {
								$o=$ar[$ir];
								if ($o > '') {
									if ($flag) { 
										$id_vopros=db_get_id_vid($vopros,$o);
										$flag=0;									
									} else {
										$o=	str_replace("а) ","", 
											str_replace("б) ","",
											str_replace("в) ","",
											str_replace("г) ","",
											str_replace("д) ","",
											str_replace("ж) ","",$o))))));
										$oo=str_replace("+","",$o);
										$id_otvet=db_get_id_vid($otvet,$oo);
										db_insert_tests(array($id_kategoria, $id_spec, $id_tema, $id_vopros, $id_otvet,($o===$oo?0:1))); // добавить вариант
									}	
								}
							}
						}
					}
				} else {
					$id_otvet		=db_get_id_vid($otvet,$_POST['otvet']);
					db_insert_tests(array($id_kategoria, $id_spec, $id_tema, $id_vopros, $id_otvet, $_POST['verno'])); // добавить вариант
				}
			}
			if ($_POST['bot']=="Изменить")	db_update_tests_verno(); // изменить вариант
			if ($_POST['bot']=="Удалить")	db_del_id('tests',array($_POST['for_del'])); // удалить один вариант
			go("&do=".$_POST['form']."&kategoria=".$id_kategoria."&spec=".$id_spec."&tema=".$id_tema."&vopros=".$id_vopros."&txt=".$_POST['bot'].':готово!');		
			break;

		case "add" : // отправить данные формы на сервер 
			if ( ! isset($_POST['form'])) $_POST['form']='form_add_new';
			if ( ! isset($_POST['verno'])) $_POST['verno']=0;
			$a = array( $_POST['kategoria'] , $_POST['spec'] ,$_POST['tema'],$_POST['vopros'],$_POST['otvet'] ,$_POST['verno']);
			//var_export($a);
			db_insert_tests($a);
			go("&do=".$_POST['form']."&vopros=".$_POST['vopros']."&txt=Добавлено,продолжаем!");			
			break;
		case "form_add_soz" : // форма добавления теста из SOZ
			// kategoriaegory	platform	short	description	project	why	keywords
			$a_tema		= db_list_soz("soz","tprojects","project");
			$a_kategoria= db_list_soz("soz","kkategoriaegories","kategoriaegory");
			$a_spec		= db_list_soz("soz","platforms","platform");

		}	
	}	
	catch (PDOException $e_stata) {
		$say = "Ошибка: " . $e_stata->POSTMessage(); 
	}
}
?>
