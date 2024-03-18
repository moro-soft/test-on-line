<?php
function list_opros() {//запрос списка Опросов
	print '<h1>Выберите Опрос по теме:</h1><div class=row><ol>';
	$data = db_list_tem_voprosov();
	while ($r = $data->fetch() ) {
		$p='&kategoria='.$r['kategoria'].'&spec='.$r['spec'].'&tema='.$r['tema'].'&voprosov='.$r['voprosov'];
		//.'&kategoria_='.$r['kategoria_'].'&spec_='.$r['spec_'].'&tema_='.$r['tema_'];
		print "
<li>	
	<a href='?form=start_opros&menu=0$p' ><i class='ico icon-stack'></i> ".$r['tema_'].'</a>'.' / '.$r['spec_'].' / '.$r['kategoria_']." :
	<a href='?form=list_opros_history$p' title=История><i class='ico icon-table'></i></a>
	<a href='?form=total_opros&bot=Итоги$p' title=Итог ><i class='ico icon-stats-bars2'></i></a>
	<a href='?form=start_opros&voprosN=1&menu=0$p' title='Начать по шагам!'  ><i class='ico icon-play3'></i></a>
	<a href='?form=start_opros&fio=".$_SERVER['REMOTE_ADDR']."&gde=net&menu=0&notema$p' title='Инкогнито'  ><i class='ico icon-user-check'></i></a>
</li>" ;
	}
	print '</ol></div>';
}

function select_opros() {//запрос списка Опросов
	$data = db_list_tem_voprosov();
	$li='';
	$i=0;
	while ($r = $data->fetch() ) {
		$i++;
		$li.= "<div><input name=for type=radio value='".$r['spec'].'&'.$r['kategoria'].'&'.$r['tema'].'&'.$r['voprosov']."' id='$i' checked ><label for='$i'>".$r['spec_'].' : '.$r['tema_']."</label></div>";
	}
	print '
<h1>Выберите Опрос и укажите свои данные:</h1>
<div class=row>
	<form action="?form=start_opros" method="post">
		'.$li.'
	<div class="c50 v">
			<label> </label><input name="form_name" type="hidden" value="select_opros">
			<label>Ваши: </label><input name="fio" type="text" size=30 value="'.$_POST['fio'].'" placeholder="ФИО" >
			<label> </label><input name="email" type="text" size=30 value="'.$_POST['email'].'" placeholder="*@segezha-group.com" >
			<label> </label><input name="gde" type="text" size=50 value="'.$_POST['gde'].'" placeholder="Подразделение" >
	</div>
	<div class="c50 v">
			<input name="voprosN" type="checkbox" value=1 id="voprosN" ><label for="voprosN">по очереди (иначе - все вместе с выбором)</label></div>
			<button type="submit" name="bot" value="Выбор">Выбор</button>
	</div>
	</form>
</div>
';
}

function list_opros_res() {//запрос списка результатов Опросов
	$data = db_list_tests_tema(array($_POST['fio'],$_POST['email']));
	$li='';
	$i=0;
	$pravilno = 0; // показать как правильно
	print '<div class=row><form action="?form=total_opros" method="post">';
	while ($r = $data->fetch() ) {
		$i++;
		$li.= $pravilno ? 
		"<div><input name=for type=radio value='".$r['spec'].'&'.$r['kategoria'].'&'.$r['tema'].'&'.$r['voprosov_otv'].'&'.$r['start'].'&'.$r['email'].'&'.$r['sec']."' id='$i' ><label for='$i'>".$r['spec_'].' : '.$r['tema_']
		.'</label> за '.$r['sec'].' cек. '
		.$r['time'].' '.$r['ip'].' '.$r['fio'].' - верных: '
		.$r['verno_otv'].' не верных:'
		.$r['ne_verno_otv'].' из отвеченных: '
		.$r['voprosov_otv'].'</div>'
		: 
		"<div><input name=for type=radio value='".$r['spec'].'&'.$r['kategoria'].'&'.$r['tema'].'&0&0&'.$r['email'].'&0'."' id='$i' ><label for='$i'>".$r['spec_'].': '.$r['tema_'].' <i>'.$r['fio']
		.'</i></label></div>';
	}
	print $li.'</div>
	<div class=row>
		<input name="form_name" type="hidden" value="start_opros">
		<label>Ваши данные:</label><input name="fio" type="fio" size=25 value="'.$_POST['fio'].'" placeholder="ФИО" >
		<input name="email" type="email" size=25 value="'.$_POST['email'].'" >
		'.($pravilno 
		?'<button type="submit" name="bot" value="Выбор">Результаты</button>'
		:'<button type="submit" name="bot" value="Итоги">Итоги</button>'
		).'
	</div>
	</form>';
}

function list_opros_history() {//запрос списка истории Опросов
	$data = db_list_history_view(array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
	$l='';
	$i=0;
// <h2>Спец.: '.db_get_text_id($_POST['spec']).'</h2>
// <h3>Кат.:'.db_get_text_id($_POST['kategoria']).'</h3>

	print '
<h1>Итория опроса:'.db_get_text_id($_POST['tema']).'</h1>
<div class=row>
<div class=odd>
<table class=odd>
	<thead>
	<tr>
		<th>№№</th>
		<th>Рейтинг</th>
		<th>Причина</th>
		<th>Вариант№</th>
		<th>Вариант</th>
		<th>Вопрос№</th>
		<th>Вопрос</th>
		<th>ФИО</th>
		<th>Адрес</th>
		<th>Подразделение</th>
		<th>Дата</th>
	</tr>	
	</thead>
	<tbody>	
	';
	while ($r = $data->fetch() ) {
		$i++;
		$l.='
	<tr>
		<td>'.$i.'</td>
		<td>'.$r['rating'].'</td>
		<td>'.$r['mess'].'</td>
		<td>'.$r['test'].'</td>
		<td>'.$r['otvet_'].'</td>
		<td>'.$r['vopros'].'</td>
		<td>'.$r['vopros_'].'</td>
		<td>'.$r['fio'].'</td>
		<td>'.$r['email'].'</td>
		<td>'.$r['gde'].'</td>
		<td>'.$r['time'].'</td>
	</tr>';
	}
	print $l.'
</tbody>
</table>
';
}

function start_opros() {//запрос списка Опросов
	global $cur_time,$type_opros;
	if (!isset($_POST['fio'])) $_POST['fio']='';
	if (!isset($_POST['gde'])) $_POST['gde']='';
	if (!isset($_POST['email'])) $_POST['email']='';
	if (!isset($_POST['bot'])) $_POST['bot']='';
	if (isset($_POST['fio']) and $_POST['fio']==='host') $_POST['fio'] = $_SERVER['REMOTE_ADDR'];
	
	if (isset($_POST['for'])) {  //набор
		$a = explode("&",$_POST['for']);
		$_POST['spec']=$a[0];
		$_POST['kategoria']=$a[1];
		$_POST['tema']=$a[2];
		$_POST['voprosov']=$a[3];
	}	
	print (isset($_POST['notema']) ? '' : '<h1>Опрос: '.db_get_text_id($_POST['tema']).'</h1><h5>'.db_get_text_id($_POST['kategoria']).'</h5>');
	
if (isset($_POST['fio']) and $_POST['fio'] != '' ) { 
	print '
<form name=f action="?form=stop_opros" method="post">
<div>
	<input name="fio"	type="hidden" value="'.$_POST['fio']	.'" >
	<input name="gde"	type="hidden" value="'.$_POST['gde']	.'" >
	<input name="email"	type="hidden" value="'.$_POST['email']	.'" >
	<input name="form_name" type="hidden" value="start_opros">
	<input name="start" type="hidden" value="'.$cur_time.'">
	<input name="sec" type="hidden" value="'.time().'">
	<input name="tema" type="hidden" value='.$_POST['tema'].'>
	<input name="kategoria" type="hidden" value='.$_POST['kategoria'].'>
	<input name="spec" type="hidden" value='.$_POST['spec'].'>
	<input name="voprosov" type="hidden" value='.$_POST['voprosov'].'>
	'.(isset($_POST['notema']) ? '<input name="notema" type="hidden" value=1'.$_POST['notema'].'>':'').'
</div>
<div class=row>
	<div class="c80">
		<div class=row>
';
	$tek_vopros=-1;
	$i=0; // номер ответа
	$n=0; // номер вопроса
	$display=0; // показать вопрос
	$no_random=1; //не перемешивать
	$ord='';
	$p='';
	
	if ( isset($_POST['voprosN']) and isset($_POST['fio']) ) // если по этапам, то продолжим с 1-го неотвеченного этапа
		$voprosov = array_map( function($v){return $v[0];},	db_get_history_voprosov_id_user(array($_POST['fio'],$_POST['kategoria'],$_POST['spec'],$_POST['tema'])) );
	else $voprosov = array();
	//print_r($voprosov);
	
	$data = db_list_tests_view(array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
	while ($r = $data->fetch() ) {
		if ($r['vopros']!=$tek_vopros) { // смена вопроса
			if ($display and $tek_vopros > 0) print $p.'</div>';
			$n++;
			$p='';
			$tek_vopros=$r['vopros'];
			//print ' +'.$_POST['bot'].' N'.$_POST['voprosN'].' n'.$n;
			if ( $_POST['bot']!='Назад' and isset($_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov'] and count($voprosov)>0 and array_search($tek_vopros, $voprosov)!==false) {
				
				$_POST['voprosN']=max($_POST['voprosN'],1+$n); // продолжаем с следущего не отвеченного вопроса;
				
				$display = 0; // пропускаем
			} else 
				$display = (!isset( $_POST['voprosN']) or $n==$_POST['voprosN'] );
			if ($display) {
				$voprosov = array(); //больше не проверяем на уже отвеченные
				$open=( isset($_POST['voprosN']) );
				print ($open 
? '
	<div class="c c80">
		<h2 '.( $r['verno'] === '5' ? ' class="black bold"' : '').' >'.$r['vopros_'].'</h2>
	</div>
	'.( ( $r['verno'] === '5' and isset( $_POST['voprosN']) and $_POST['voprosN']>1 and $_POST['voprosN']<$_POST['voprosov'] ) ? '
	<div class="c20">
		<button type="submit" name="bot" value="Пропуск" >Пропустить<i class="ico icon-forward3"></i></button>
	</div>':'').'
'
: 
($n===1?
'
	<div class="row" >
		<h2 '.( $r['verno'] === '5' ? ' class="black bold"' : '').' >'.$r['vopros_'].'</h2>
	</div>
	<div class="row" >		
'
:
'
	<div class="accordion" name="accordion" style="width: 100%;" >
		<h2 '.( $r['verno'] === '5' ? ' class="black bold"' : '').' >'.$r['vopros_'].'</h2>
	</div>
	<div class="panel" >		
'
)
);
			}
		}
		if ($display) {
			$i++;
			$id = $r['id'];
			$oc = $n>1 ? ' onclick="javascript:fk(document.f.v_'.$i.',document.f.k_'.$i.',0)" ' : '';
			$oc1 = str_replace(",0",",1",$oc);
			$li = "".( $r['verno'] == '5' // для типа с рейтингом 5 звезд
			? "
<div class='row'>
	<div class='с c25'>
		".($n>1 ? "<textarea input name='k_$i' type='text' value='' readonly id='$id' placeholder='затрудняюсь с ответом' style='font-size:11px;'  ></textarea>" : '').'
	</div>	
	<div class="с c15 rating">		
		<div class="wrap">
			<input class="r_input" id="r_'.$i.'_5" type="radio" name="v_'.$i.'" value="5" '.$oc.' ><label class="r_ico icon-star-empty" for="r_'.$i.'_5" title="5"></label>
			<input class="r_input" id="r_'.$i.'_4" type="radio" name="v_'.$i.'" value="4" '.$oc.' ><label class="r_ico icon-star-empty" for="r_'.$i.'_4" title="4"></label>
			<input class="r_input" id="r_'.$i.'_3" type="radio" name="v_'.$i.'" value="3" '.$oc.' ><label class="r_ico icon-star-empty" for="r_'.$i.'_3" title="3"></label>
			<input class="r_input" id="r_'.$i.'_2" type="radio" name="v_'.$i.'" value="2" '.$oc.' ><label class="r_ico icon-star-empty" for="r_'.$i.'_2" title="2"></label>
			<input class="r_input" id="r_'.$i.'_1" type="radio" name="v_'.$i.'" value="1" '.$oc.' ><label class="r_ico icon-star-empty" for="r_'.$i.'_1" title="1"></label>
			<input class="k_input" id="r_'.$i.'_0" type="radio" name="v_'.$i.'" value="0" '.$oc1.' checked="checked" ><label class="r_ico_cansel icon-cancel-circle"  for="r_'.$i.'_0" title="затрудняюсь с ответом - сброс оценки и причины" ></label>
		</div>
	</div>
	<div class="с c60" >'	
			: // прочие типы
			"
<div class='row v'>
	<div class='v с c30' >	
		".( $r['verno'] === '4' ? // текстовый ввод
		"<textarea input name='k_$i' type='text' value='' id='$id' placeholder='введите текст...' style='font-size:12px;'  ></textarea>":'')."
	</div>
	<div class='v с c60' >	
		".( $r['verno'] === '3' // радио переключатель
				? 
					"<input name='r_$tek_vopros' type='radio' value='$id' id='$id' >"
				:
					"<input name='v_$i' type='".( $r['verno'] < '3' ? 'checkbox' : 'hidden' )."' value='$id' id='$id' >"
				)
			)."
		<label for='$id' > ".$r['otvet_']. "</label>
	</div>
</div>";
			$no_random = $r['verno']>1 ? 1 : 0 ;
			//print '='.$r['verno'].'('.$no_random.')';
			if ($no_random or rand(0,1)==1) { // вариант перемешивания ответов при вопросе
				$p=$p.$li;
				$ord.='1';
			} else {
				$p=$li.$p;
				$ord.='0';
			}
			
		}
	}
	if ($display and $tek_vopros > 0) print $p.'</div>';
	print '
	</div>	
</div>	
<div class="row">
	<div class="c">
		<input name="ord" type="hidden" value="'.$ord.'">
		<input name="menu" type="hidden" value="'.$_POST['menu'].'">
		'.(isset( $_POST['voprosN']) ? 
		'<input name="voprosN" type="hidden" value="'.(0+$_POST['voprosN']).'">'
		.( $_POST['voprosN'] > 1 ? '	<button type="submit" name="bot" value="Назад"  onclick="window.history.go(-1); return false;" ><i class="ico icon-backward2"></i> Назад</button> ':'')
		.$_POST['voprosN'].' шаг из '.$_POST['voprosov']
		: '' )
		.' <button type="submit" name="bot" value="Выбор" '.( isset( $_POST['menu']) ? ' onClick="javascript: return check_form()" ' :'').'>'.( ( isset( $_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov'] ) ? ' Далее <i class="ico icon-forward3"></i>':'Сохранить').'</button>
	</div>
</div>
</form>';

} else // Запросить начальные данные и начать опрос	: 
	print '
<form name=f action="?form=start_opros" method="post">
<div>
	<input name="form_name" type="hidden" value="start_opros">
	<input name="start" type="hidden" value="'.$cur_time.'">
	<input name="sec" type="hidden" value="'.time().'">
	<input name="tema" type="hidden" value='.$_POST['tema'].'>
	<input name="kategoria" type="hidden" value='.$_POST['kategoria'].'>
	<input name="spec" type="hidden" value='.$_POST['spec'].'>
	<input name="voprosov" type="hidden" value='.$_POST['voprosov'].'>
	<input name="menu" type="hidden" value='.$_POST['menu'].'>
	'.(isset( $_POST['voprosN']) ? '<input name="voprosN" type="hidden" value='.(0+$_POST['voprosN']).'>' : '' ) .'
</div>
<div class="row">
	<div class="c">
		<label>Ваши данные:</label>
		<input id="fio"  name="fio" type="text" size=30 value="'.$_POST['fio'].'" placeholder="ФИО"  >
		<input name="gde" type="text" size=50 value="'.$_POST['gde'].'" placeholder="Подразделение" >
		<input name="email" type="text" size=30 value="'.$_POST['email'].'" placeholder="*@segezha-group.com" >
	</div>
</div>
<div class="row">
	<button type="submit" name="bot" value="Выбор" onClick="return check_form()">Начать опрос</button>
</div>
</form>
';

print '
<script>

if (document.f.fio.value === "" && localStorage.getItem("fio") != null ) document.f.fio.value = localStorage.getItem("fio"); 
document.f.fio.onchange	= function(){localStorage.setItem("fio"	,document.f.fio.value);} 
if (document.f.gde.value === "" && localStorage.getItem("gde") != null ) document.f.gde.value = localStorage.getItem("gde"); 
document.f.gde.onchange	= function(){localStorage.setItem("gde"	,document.f.gde.value);} 
if (document.f.email.value === "" && localStorage.getItem("email") != null ) document.f.email.value = localStorage.getItem("email"); 
document.f.email.onchange	= function(){localStorage.setItem("email"	,document.f.email.value);} 

//# Polyfill
window.addEventListener = window.addEventListener || function (e, f) { window.attachEvent("on" + e, f); };

function fk(v,k,n) { 
	for(var i = 0; i < v.length; i++) {
		if(v[i].checked) { v = v[i];	break; }  
	}
	if (v.value >= "1" && v.value <= "3") {
		k.setAttribute("required",true);
		k.removeAttribute("readOnly");
		k.placeholder=""+v.value+"->укажите причину низкой оценки ";
	} else { 	
		k.removeAttribute("required");
		k.setAttribute("readOnly",true);
		k.placeholder=v.value;
	}
	if (n) {
		k.value=""; 
		k.placeholder="затрудняюсь с ответом"; 
	}
	return true;
}

function check_form() {
	var f=document.getElementsByName("f")[0];
	if (f.fio.value === "") {
		alert("Заполните ФИО!");
		return false;
	}
	if (f.gde.value === "") {
		alert("Заполните Подразделение!");
		return false;
	}
	if (
		(	
// отменим проверку всех кроме первого с пропуском всех 
//		f.v_1.value > "0" ||
//		f.v_2.value > "0" ||
//		f.v_3.value > "0" ||
//		f.v_4.value > "0" ||
//		f.v_5.value > "0" ||
//		f.v_6.value > "0" || 		
		f.voprosN.value === "1"
		) && (
		f.v_1.value === "0" ||
		f.v_2.value === "0" ||
		f.v_3.value === "0" ||
		f.v_4.value === "0" ||
		f.v_5.value === "0" ||
		f.v_6.value === "0" 
		)	
	) {
		alert("Поставьте все оценки!");
		return false;
	}
	return true;
}

var acc = document.getElementsByName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
	acc[i].addEventListener("click", function() {
		this.classList.toggle("active");
		var panel = this.nextElementSibling;
		if (panel.style.display === "block") 
			panel.style.display = "none";
		else panel.style.display = "block";
	});
}

</script>
';
}
	
function stop_opros() {//завершение Опроса
	global $cur_time,$type_opros;
	// добавить ответ пользователя на Опрос	
	//foreach( $_POST as $key => $value) 
	$no_end = ( isset( $_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov'] );

if ($_POST['bot']!="Назад" ) {
	$p = '';
	print (isset($_POST['notema']) ? '' : '
<h1>Опрос: '.db_get_text_id($_POST['tema']).'</h1>	<h5>'.db_get_text_id($_POST['kategoria']).'</h5>').'
<div class=row>
	<div class="c80">
		<div class=row>';
	$tek_vopros = -1;
	$i = 0; // номер ответа
	$iv = 1; // флаг правильности ответов 
	$n = 0; // номер вопроса
	$display = 0; // показать вопрос
	$id_user = 0; // пользовать
	$id_sess = 0; // сессия
	$pravilno = 0; // показать как правильно
	$sec=(time()-$_POST['sec']); //потрачено секунд
	if (!isset( $_POST['voprosN']) and isset($_POST['voprosov'])  and $_POST['voprosov']>0  ) $sec = (int) ($sec / $_POST['voprosov']); // учтем количество вопросов, если сразу все Опросили


	$data = db_list_tests_view(array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
	while ($r = $data->fetch() ) {
		$id=$r['id'];
		if ($r['vopros']!=$tek_vopros) {
			if ($display and $tek_vopros>0 ) {
				print $pravilno ? $p.'Отвечено '.( $iv==1 ? "<i class=green>СОВЕРШЕННО</i>" : "<i class=red>НЕ</i>" ).' точно!</div></div></div>' : '' ;
				// добавить неверно или верно на вопрос отвечено
				db_update_sess(array('verno',$iv,$id_sess)); 
			}
			$n++;
			$p='';
			$tek_vopros=$r['vopros'];
			$pravilno = ($r['verno']<'2'? 1 : 0);

			$display = (!isset( $_POST['voprosN']) or $n==$_POST['voprosN'] );
			if ($display) {
				$iv=1;
				print $r['verno']<'2' ? 
				'
<div class="row v">
	<div class="v с c80" >	
		<h2>'.$r['vopros_'].'</h2>
</div>
<div class="row v">
	<div class="v с c30" >
	
	</div>	
	<div class="v с c60" >	
				':'';
				// добавить факт ответа и неверно или верно
				$id_user=db_get_id_user($_POST['fio']);
				db_insert_sess(array($_SERVER['REMOTE_ADDR'], $id_user, $_POST['start'], $sec, $id, 0, $tek_vopros)); 
				$id_sess=db_get_id_sess_user_start_vopros(array($id_user, $_POST['start'], $tek_vopros ));
				//print ' #'.$id_user.' fio'.$_POST['fio'];
			}	
		}
		if ($display) {
			$i++;
			$save_hist = false;
			$v_reiting = 0;
			$k_i = '';
			$v_id = 0;
			$v = 0;
			// определение верного ответа
			if (isset($_POST['k_'.$i])) $k_i=$_POST['k_'.$i]; 
			
			if (isset($_POST['v_'.$i])) {
				if ($r['verno'] < 2) { // тесты
					$v_id = $_POST['v_'.$i];
					$v = ( ($v_id==$id) == ($r['verno']==1) ); //правильный ответ на вопрос?
					$iv= $iv * $v; // все ответы правильные на вопрос?
					$save_hist = true; 
				}
				elseif ($r['verno']==4 and $k_i!=='' ) 
					$save_hist = true; // текст
				elseif ($r['verno']==5) {
					$v_reiting = $_POST['v_'.$i]; // рейтинг
					$save_hist = $v_reiting > 0; // сохраняем только не нулевой
				}
			}
			elseif ($r['verno']==3 and isset($_POST['r_'.$tek_vopros]) and $_POST['r_'.$tek_vopros]===$id ) // радио
				$save_hist = true; 
			

			db_del_history(array($id, $id_user)); //удалиль старые ответы

			if ($save_hist) {
				db_insert_history(array($id, $v, $id_user, $id_sess, $v_reiting ,$k_i )); //записать ответы
			}
			if ($pravilno) {
				$li=' '.( ''.$r['verno']=='1'	? "<input type='checkbox' class='noborder' checked><label></label>" : '' )."
				<div class='v'><input type='checkbox' class='".( $v ? "green" : "red" )."' ".( $v_id==$id ? 'checked' :  ' ' ).'><label>'.$r['otvet_'].'</label>
				</div>';
				if (substr($_POST['ord'],$i-1,1)=='1') { // вариант повтора перемешивания ответов при вопросе
					$p=$p.$li;
				} else {
					$p=$li.$p;
				}
			}

		}		
	}
	if (( ! isset( $_POST['voprosN']) or $n==$_POST['voprosN']) and $tek_vopros>0 ) {
		print $pravilno ? $p.'Отвечено '.( $iv ? "<i class=green>СОВЕРШЕННО</i>" : "<i class=red>НЕ</i>" ).' точно!</div>' : '';
		// добавить неверно или верно на вопрос отвечено
		db_update_sess(array('verno',$iv,$id_sess)); 
	}
	
}
	
	if ($pravilno) print '</div></div></div>
<div class="row"><div class="c">
<form action="?form='.( $no_end ? 'start_opros':'total_opros').'" method="post">
	<input name="menu" type="hidden" value='.$_POST['menu'].'>
	<input name="form_name" type="hidden" value="start_opros">
	<input name="start" type="hidden" value="'.$cur_time.'">
	<input name="sec" type="hidden" value="'.time().'">
	<input name="tema" type="hidden" value='.$_POST['tema'].'>
	<input name="kategoria" type="hidden" value='.$_POST['kategoria'].'>
	<input name="spec" type="hidden" value='.$_POST['spec'].'>
	<input name="voprosov" type="hidden" value='.$_POST['voprosov'].'>
	'.(isset( $_POST['voprosN']) ? '<input name="voprosN" type="hidden" value='.(1+$_POST['voprosN']).'>' : '' ) .'
	<input name="fio" type="hidden" size=25 value="'.$_POST['fio'].'" placeholder="ФИО" >
	<input name="email" type="hidden" size=25 value="'.$_POST['email'].'" placeholder="*@segezha-group.com" >
	<button type="submit" name="bot" value="Выбор">'.( $no_end ? 'Далее...':'Итоги').'</button>
</form>
</div>';

	else { // вызов след. формы
		if($no_end) { //этап
			$_POST['start']=time();
			if(isset( $_POST['voprosN']) ){
				if($_POST['bot']=="Назад" and $_POST['voprosN'] > 1 )  
					$_POST['voprosN']--; 
				else 
					$_POST['voprosN']++;
			}	
			start_opros();
		} else { // результат завершения опроса - выставлен в users поле result=1
			db_update_user(array('result',1,$_POST['fio'])); 
			$_POST['bot']="Итоги";
			$_POST['menu']=0;
			unset($_POST['voprosN']);
			$_POST['for']=$_POST['spec'].'&'.$_POST['kategoria'].'&'.$_POST['tema'].'&0&'.$_POST['start'].'&'.$_POST['email'].'&0';
			total_opros();
		}	
	}	
}

function total_opros() {//Итоги Опроса p=1 среднее
	global $cur_time,$type_opros;
	// добавить ответ пользователя на Опрос	
	//foreach( $_POST as $key => $value) 
	if (isset($_POST['for'])) {  //набор
		$a = explode("&",$_POST['for']);
		$_POST['spec']=$a[0];
		$_POST['kategoria']=$a[1];
		$_POST['tema']=$a[2];
		$_POST['voprosov']=$a[3];
		$_POST['start']=$a[4];
		$_POST['email']=$a[5];
		$_POST['sec']=$a[6];
	}	
	$p  = (isset($_POST['notema']) ? '' : '<h1>Итоги Опроса: '.db_get_text_id($_POST['tema']).'</h1>').'<div class=row>';
	$tek_vopros=-1;
	$tek_test=-1;
	$i=0; // номер ответа
	$iv=1; // флаг правильности ответов 
	$n=0; // номер вопроса
	$display=0; // показать вопрос
	$pravilno = 0; // показать как правильно
	$id_user=0; // пользовать
	$id_sess=0; // сессия
	$sec=(time()-(isset($_POST['sec'])?$_POST['sec']:0)); //потрачено секунд
	if (!isset( $_POST['voprosN']) and isset($_POST['voprosov']) and $_POST['voprosov']>0 ) $sec= (int) ($sec / $_POST['voprosov']); // учтем количество вопросов, если сразу все Опросили
	$no_end = ( isset( $_POST['voprosN']) and $_POST['voprosN']<$_POST['voprosov'] );
	print $p; $p='';
	$data = db_list_tests_view(array($_POST['kategoria'],$_POST['spec'],$_POST['tema']));
	while ($r = $data->fetch() ) {
		$id=$r['id'];
		if ($r['vopros']!=$tek_vopros) {
			if ($display and $tek_vopros>0 ) {
				print $p.$total.'</div>';
			}
			$n++;
			$p='';
			$tek_vopros=$r['vopros'];
			$tek_test=$r['id'];
			$total=''; //опрошено
			$users_pool=db_get_USER_test(array($tek_test,$tek_vopros));
			if ($users_pool>0) $total = '<div class="row"><label class="l_ico icon-users"> '.$users_pool.'</label></div>';
			$pravilno = ($r['verno']<'2' ? 1 : 0);
			$display = (!isset( $_POST['voprosN']) or $n==$_POST['voprosN'] );
			if ($display) {
				$iv=1;
				print '<div class="row"><h2>'.$r['vopros_'].'</h2>';
				// добавить факт ответа и неверно или верно
				$id_user = db_get_id_user($_POST['fio']);
				//$id_sess=db_get_id_sess_user_start_vopros(array($id_user, $_POST['start'], $tek_vopros ));
				//print ''.$id_user.'/'.$id_user;
			}	
			
		}
		if ($display) {
			$i++;
			$li='';
			if ($_POST['bot']=="Итоги"  or  $_POST['email']) {
				if($r['verno']<2) { //тесты
					$s = db_get_pool_test(array($id,$tek_test,$tek_vopros));
					$li.= $s>0 ? '<label class="l_ico icon-stats-bars"></label><label class="l_ico">'. $s .'</label>' : '<label class="l_ico icon-eye-blocked"></label>';
				}
				elseif ($r['verno']<4) { //варианты
					$s = db_get_pool_test(array($id,$tek_test,$tek_vopros));
					$li.= '<label class="l_ico icon-stats-bars2">'. $s .'</label>';
				}
				elseif($r['verno']==4) { //текст
					$mess = db_get_mess_test(array($id));
					while ($m = $mess->fetch() ) {
						$li.= '<label class="l_ico icon-bubble"> '. $m[0].' </label> ';
					}	
				}	
				elseif($r['verno']==5) { //рейтинг
					$s = db_get_svg_test(array($id));
					$li.= $s>0 ? '<label class="l_ico icon-star-half"></label><label class="l_ico"> '. round( $s ,1) .'</label>' : '<label class="l_ico icon-star-empty"></label>';
				}
				
			} else {
				$sess = db_get_history_user(array($id_user, $id));
				while ($s = $sess->fetch() ) {
					$v_i=$s['test'];
					$v = ( ($v_i==$id) == ($r['verno']=='1') ); //правильный ответ на вопрос?
					$iv= $iv * $v; // все ответы правильные на вопрос?
					$li.=''
						.( $pravilno and  $r['verno']=='1'	? "<input type='checkbox' class='noborder' checked><label></label>" : '' )
						.( $pravilno ? "<input type='checkbox' class='".( $v ? "green" : "red" )."' ".( $v_i==$id ? 'checked' : ' ' ).'>' 
									: ( $s['rating'] ? '<label class="r_ico">'.$s['rating'].'</label><label class="r_ico icon-star-full"></label><i>'.($_POST['bot']=="Итоги"  or  $_POST['email'] ? $s['mess'] : '').'</i>' : '<label class="r_ico icon-star-empty"></label>' ) .' ') .'
					';
				}
			}
			$p.='<div class="row"><div class="c30" >'.($li ? $li : '<label class="icon icon-wondering">').'</div><div class="c60" ><label>'.$r['otvet_'].'</label></div></div>';
		}		
	}
	print $p.$total.'	</div></div>';
}
	
function form_add() {// форма добавления Опроса
	global $cons,$type_opros; 
	if (!isset($_GET['spec'])) $_GET['spec']=0;
	if (!isset($_GET['kategoria'])) $_GET['kategoria']=0;
	if (!isset($_GET['tema'])) $_GET['tema']=0;
	if (!isset($_GET['vopros'])) $_GET['vopros']=0;
	print '<h2>Форма добавления Опроса</h2>
<form action="?form=add_opros" method="post">
<div class=row>
	<div class=c50>
	<input name="form_name" type="hidden" value="form_add">';
	$a_tema		= db_list_vid($cons['tema']);
	$a_kategoria= db_list_vid($cons['kategoria']);
	$a_spec		= db_list_vid($cons['spec']);
	$t_tema		= db_get_text_id($_GET['tema']);
	$t_vopros	= db_get_text_id($_GET['vopros']);
	$a_list_otv	=  db_list_otv_vopros_tema(array($_GET['kategoria'],$_GET['spec'],$_GET['tema'],$_GET['vopros']));// db_list_otv($_GET['vopros']);
	$p='<div>Специализация: <i onclick="getNewValue(this,\'spec\',\'spec_new\')" title="Добавить новый" >✎</i>
	<select id="spec" name="spec" ondblclick="getNewValue(this,\'spec\',\'spec_new\')" >';
	for ($i= 0; $i < count($a_spec); $i ++) $p.="<option value='".$a_spec[$i][0]."' >".$a_spec[$i][1]."</option>";
	$p=str_replace("value='".$_GET['spec']."'","value='".$_GET['spec']."' selected ",$p).'</select>
	<input id="spec_new" name="spec_new" type="text" class="off" placeholder="укажите новый..." >
	</div>';
	$p.='<div>Категория: <i onclick="getNewValue(this,\'kategoria\',\'kategoria_new\')" title="Добавить новый" >✎</i>
	<select id="kategoria" name="kategoria" ondblclick="getNewValue(this,\'kategoria\',\'kategoria_new\')" >';
	for ($i= 0; $i < count($a_kategoria); $i ++) $p.="<option value='".$a_kategoria[$i][0]."' >".$a_kategoria[$i][1]."</option>";
	$p=str_replace("value='".$_GET['kategoria']."'","value='".$_GET['kategoria']."' selected ",$p).'</select>
	<input id="kategoria_new" name="kategoria_new" type="text"  class="off" placeholder="укажите новый..." >
	</div>';
	$p.='<div>Тема: 
	<input id="tema_new" name="tema_new" type="text" class="off" placeholder="укажите новый..." value="'.$t_tema.'" >
	<i onclick="getNewValue(this,\'tema\',\'tema_new\')" title="Добавить новый" >✎</i>
	<input onclick="getNewValue(this,\'tema\',\'tema_new\')" name="tema_edit" type="checkbox" value=1 id="tema_edit" title="Изменить текст"><label for="tema_edit" title="Изменить текст">🛠</label>
	<select id="tema" name="tema" ondblclick="getNewValue(this,\'tema\',\'tema_new\')">';
	for ($i= 0; $i < count($a_tema); $i ++) $p.="<option value='".$a_tema[$i][0]."' >".$a_tema[$i][1]."</option>";
	$p=str_replace("value='".$_GET['tema']."'","value='".$_GET['tema']."' selected ",$p).'</select>
	</div>';

	$p.='<div>Вопрос: <input name="multi" type="checkbox" value=1 id="multi"><label for="multi" title="ввод всех вопросов темы сразу  в формате:
Вопрос 1?
Ответ1	правильный+
Ответ2
Ответ3
	(вопросы разделены пустой строкой)
Вопрос 2?
Ответ1
Ответ2	правильный помечен+
Ответ3
	(вопросы разделены пустой строкой)
Вопрос 3: 	
Ответ1^	множественный выбор галка помечен^
Ответ2^	множественный выбор галка помечен^
Ответ3@	однозначный выбор точка помечен@
Ответ4~	текстовый вариант ответа помечен~	
Ответ5*	рейтнг	помечен*	
"> мультиввод ≋ </label> <i onclick="getNewValue(this,\'vopros\',\'vopros_new\')" title="Изменить вопрос">🛠</i>
	<textarea id="vopros" type="text" name="vopros" placeholder="поиск вопроса по слову...">'.$t_vopros.'</textarea>
	<textarea id="vopros_new" type="text" name="vopros_new" class="off"></textarea>
	</div>';
	$p.="<div>
		Вариант ответа:
		<input name='verno' type='radio' value='0' id='cb0' checked><label for='cb0'>$type_opros[0]</label> 
		<input name='verno' type='radio' value='1' id='cb1'><label for='cb1'>$type_opros[1]</label> 
		<input name='verno' type='radio' value='2' id='cb2'><label for='cb2'>$type_opros[2]</label> 
		<input name='verno' type='radio' value='3' id='cb3'><label for='cb3'>$type_opros[3]</label> 
		<input name='verno' type='radio' value='4' id='cb4'><label for='cb4'>$type_opros[4]</label> 
		<input name='verno' type='radio' value='5' id='cb5'><label for='cb5'>$type_opros[5]</label> 
		
	<textarea type=text name='otvet' value='' placeholder='поиск ответа по слову...'></textarea></div>";
	$p.='<div>
	<button type="submit" name="bot" value="Добавить">Добавить</button>
	<button type="submit" name="bot" value="Смотреть">Показать ответы</button>
	</div>';
	$p.='</div>
	<div class="c50">
	 <div class="T">
	  <div class="TB">
	   <div class=TR>
		<div class="TD">№</div>
		<div class="TD">X</div>
		<div class="TD">Варианты ответа</div>
		<div class="TD">Тип ответа</div>
	   </div>
	';
	$hids='';
	for ($i= 0; $i < count($a_list_otv); $i ++) {
		$id=$a_list_otv[$i][0];
		$hids.=','.$id;
		$p.="<div class=TR>
		<div class=TD>".($i+1)."</div>
		<div class=TD><input name='for_del' type='radio' value='$id' id='o$id' ><label for=o$id>&nbsp;</label></div>
		<div class=TD><textarea name='otvet$id' placeholder='пустой'>".$a_list_otv[$i][1]."</textarea></div>
		<div class=TD>
		<select name='verno$id' id='cb$id'>
			<option value='0' ".( $a_list_otv[$i][2] == 0 ? ' selected ' : '' ).">$type_opros[0]</option> 
			<option value='1' ".( $a_list_otv[$i][2] == 1 ? ' selected ' : '' ).">$type_opros[1]</option> 
			<option value='2' ".( $a_list_otv[$i][2] == 2 ? ' selected ' : '' ).">$type_opros[2]</option> 
			<option value='3' ".( $a_list_otv[$i][2] == 3 ? ' selected ' : '' ).">$type_opros[3]</option> 
			<option value='4' ".( $a_list_otv[$i][2] == 4 ? ' selected ' : '' ).">$type_opros[4]</option> 
			<option value='5' ".( $a_list_otv[$i][2] == 5 ? ' selected ' : '' ).">$type_opros[5]</option> 
		</select>
		</div>
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
	</div>
</div>
	'.( $hids==='' ? '' : '<input name="hids" type="hidden" value="'.substr($hids,1).'">' ).'
</form>'.$_POST['txt'];
	print $p; 			
?>
<script language="JavaScript">
<!-- 
var t=document.getElementById('tema');
var tx = document.getElementsByTagName('textarea');//РАСТЯГИВАЕМ_textarea
for (var i = 0; i < tx.length; i++) {
tx[i].setAttribute('style', 'height:' + Math.max(40,tx[i].scrollHeight) + 'px;'); //overflow-y:hidden;
tx[i].addEventListener("input", OnInput, false);
}
function OnInput() {
this.style.height = 'auto';
this.style.height = (this.scrollHeight) + 'px';//console.log(this.scrollHeight);
}

//var v=document.getElementById('vopros');
t.onchange = function(){
var t=document.getElementById('tema');
var idtema=t.options[t.options.selectedIndex].value;
$("[name=vopros]"	).autocomplete({ minLength: 1, appendTo: '', open: function(event, ui) { }, source: 'seek.php?key=short&vid=vopros&tema='+idtema});
$("[name=otvet]"	).autocomplete({ minLength: 1, appendTo: '', open: function(event, ui) { }, source: 'seek.php?key=description&vid=otvet&tema='+idtema});
}; 
t.onchange();
//-->
</script>
<?php
}

function add_opros() {// отправить данные формы на сервер 
	global $cons,$vopros,$otvet,$type_otvet; 
	if ( ! isset($_POST['verno'])) $_POST['verno']=0;
	if ( ! isset($_POST['form_name'])) $_POST['form_name']='form_add_new';

	
	if ( isset( $_POST['tema_edit']) and $_POST['tema_edit']===1 and !empty($_POST['tema_new']) and $_POST['tema_new']!=$_POST['tema'] ) { // изменить на новый текст Темы по id
		$id_tema = db_get_id_vid($cons['tema'],$_POST['tema']);
		db_update_texts_set_val_id(array('text',"'".$_POST['tema_new']."'",$id_tema));
		$_POST['tema']=$_POST['tema_new'];
	}else{	
		if ( !empty($_POST['tema_new']) )
			$_POST['tema']=$_POST['tema_new'];// добавть новый текст Темы 
		$id_tema = db_get_id_vid($cons['tema'],$_POST['tema']);
	}
	if ( ($_POST['kategoria_new'])	> '')	$_POST['kategoria']	=$_POST['kategoria_new'];
	if ( ($_POST['spec_new'])		> '')	$_POST['spec']		=$_POST['spec_new'];
	
	$id_kategoria	=db_get_id_vid($cons['kategoria']	,$_POST['kategoria']);
	$id_spec		=db_get_id_vid($cons['spec']		,$_POST['spec']);
	$id_vopros		=db_get_id_vid($cons['vopros']		,$_POST['vopros']);
	// изменить на новый текст вопроса по id
	if ($_POST['vopros_new']!==$_POST['vopros'] and !empty($_POST['vopros_new'])) db_update_texts_set_val_id(array('text',"'".$_POST['vopros_new']."'",$id_vopros));
	
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
								for ($t=count($type_otvet)-1; $t >=0; $t --) { // перебор обозначений
									$oo = str_replace($type_otvet[$t],"",$o);
									if ($oo !== $o) break; // найдено обозначение
								}	
								$id_otvet=db_get_id_vid($otvet,$oo);
								db_insert_tests(array($id_kategoria, $id_spec, $id_tema, $id_vopros, $id_otvet,$t)); // добавить вариант
							}	
						}
					}
				}
			}
		} else {
			$id_otvet	=	db_get_id_vid($otvet,$_POST['otvet']);
			if ($id_otvet > 0)	db_insert_tests(array($id_kategoria, $id_spec, $id_tema, $id_vopros, $id_otvet, $_POST['verno'])); // добавить вариант
		}
	}
	if ($_POST['bot']=="Изменить")	db_update_tests_verno(); // изменить вариант
	if ($_POST['bot']=="Удалить")	db_del_id('tests',array($_POST['for_del'])); // удалить один вариант
	go($_POST['form_name']."&kategoria=".$id_kategoria."&spec=".$id_spec."&tema=".$id_tema."&vopros=".$id_vopros."&txt=".$_POST['bot'].':готово!'.$_POST['mes']);		
}
?>