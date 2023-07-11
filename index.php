<?php

$days = 7;

function automaticRedirect(){
	echo '<meta ';
	if($_GET['input']){
		$addr = explode("input",$_SERVER['REQUEST_URI']);
		echo 'http-equiv="refresh" content="0; url = '.$addr[0].'"';
	} else {
		echo 'http-equiv="Content-Type" content="text/html; charset=utf-8"';
	}
	echo '/>';
}

function stylesheet(){
echo '<style>';
echo 'body {';
//echo 'background-image: url("bg.png");';
echo 'background-color: #eee7df;';
echo 'background-repeat: no-repeat;';
echo 'width: 1070px;';
echo 'height: 90px;';
echo 'margin: 5;';
echo 'padding: 0;';
echo '}';
echo '@media only screen and (max-width: 1080px) {';
echo 'html {';
echo 'font-size: 30px;';
echo '}';
echo 'input {';
echo 'font-size: 30px;';
echo '}';
echo 'input[type=radio] {';
echo 'height: 25px;';
echo 'width: 25px;';
echo 'border: 25px;';
echo '}';
echo 'button {';
echo 'font-size: 30px;';
echo '}';
echo 'select {';
echo 'font-size: 30px;';
echo '}';
echo '}';
echo '</style>';
}

function echo_header(){
	echo '<!DOCTYPE html>';
	echo '<html lang="de">';
	echo '<head>';
	echo "\r\n";
	
	automaticRedirect();
	echo "\r\n";
	
	stylesheet();
	echo "\r\n";
	
	echo '<script>
		function myFunction(category) {
			var table = document.getElementById(category);
			if (table.style.display === "none") {
				table.style.display = "inline";
			} else {
				table.style.display = "none";
			}
		}
	</script>';
	echo "\r\n";
	
	echo '</head>';
	echo '<body>';
	echo '<table width="100%" border="0"><tr><td>';
}


function footer(){
	echo '</td></tr></table>';
	echo '</body>';
	echo '</html>';
}

function list_by_line($file){
	if (!file_exists($file)){
		$handle = fopen ($file, "w");
		fwrite ($handle, "");
		fclose ($handle);
	}
	
	$line = file($file);
	return $line;
}

function enter_todo(){
	echo '<table width="100%">';
	echo '<form action="index.php?input=enter" method="post">';
	echo '<label for="categorie">Kategorie:</label><br>';
	echo '<select name="categorie" size="1">';
	
	$categories = list_by_line("categories.txt");
	for($i=0;$i < count($categories); $i++){
		echo '<option>'.htmlentities($categories[$i]).'</option>';
	}
	
	echo '</select><br>';
	echo '<label for="date">Datum:</label><br>';
	echo '<input type="date" id="date" name="date" value="'.date("Y-m-d").'" step="1"><br>';
	echo '<label for="Todo">Todo:</label><br>';
	echo '<input type="text" id="Todo" name="Todo" autocomplete="off">';
	echo '<button type="submit">Eingaben absenden</button>';
	echo '</form>';
	echo '</table>';
}

function save_todo(){
	if ($_GET['input'] == "enter" && count($_POST) > 0){
	
		$fileName = "notDone.txt";
		
		$newEntry = $_POST['categorie'].'<tab>';
		
		$date = explode("-",$_POST['date']);
		
		$newEntry .= mktime(0,0,0,$date[1],$date[2],$date[0]).'<tab>';
		$newEntry .= $_POST['Todo'].'<tab>';
		
		$notDone = '';
		if(file_exists($fileName)){
			$notDone = file_get_contents($fileName);
		}
		
		$notDone = explode("\n",$notDone);
		
		array_push($notDone,$newEntry);
		asort($notDone);
		$notDone = array_merge($notDone);
		
		$notDone = implode("\n",$notDone);
		
		$handle = fopen ($fileName, "w");
		fwrite ($handle, $notDone);
		fclose ($handle);
	}
}

function read_todo($days){
	$fileName = "notDone.txt";
		
	$notDone = '';
	if(file_exists($fileName)){
		$notDone = file_get_contents($fileName);
	}
		
	$notDone = explode("\n",$notDone);
	
	for ($i=0;$i<count($notDone);$i++){
		$notDone[$i] = explode("<tab>",$notDone[$i]);
		unset($notDone[$i][count($notDone[$i])-1]);
	}
	
	$categories = file_get_contents("categories.txt");
	$categories = explode("\n",$categories);
	
	
	echo '<form action="index.php?input=modify" method="post">';
	echo "\r\n";
	echo '<div id="test" style="display:inline">Auswahl: </div>';
	echo "\r\n";
	echo '<button name="type" type="submit" value="delete" >Entfernen</button>';
	echo "\r\n";
	echo '<br><br>Verschieben um: ';
	echo "\r\n";
	echo '<input name="count" type="text" size="1" autocomplete="off" value="1" >';
	echo "\r\n";
	echo '<button name="type" type="submit" value="day" >Tag</button>';
	echo "\r\n";
	echo '<button name="type" type="submit" value="week" >Woche</button>';
	echo "\r\n";
	echo '<button name="type" type="submit" value="month" >Monat</button>';
	echo "\r\n";
	echo '<button name="type" type="submit" value="year" >Jahr</button>';
	echo "\r\n";
	echo '<table>';
	echo "\r\n";
	for($i=0;$i < count($categories); $i++){
		$tmp = 0;
		for($j=0;$j<count($notDone);$j++){
			if(strcmp($notDone[$j][0],$categories[$i]) == 0){
			//Zeitraum Filter
			
				$daysTmp = $days;
				$readMoreCount = 6;
				
				if(strcmp("HEUTE WICHTIG",$categories[$i]) == 0/* || strcmp("Urlaub",$categories[$i]) == 0*/){
					$daysTmp = 2;
					$readMoreCount = 9999;
				}
				if(strcmp("Arzttermine",$categories[$i]) == 0 || strcmp("Termine",$categories[$i]) == 0 || strcmp("Urlaub",$categories[$i]) == 0){
					$daysTmp = 9999;
				}
				if(strcmp("Anschaffungen",$categories[$i]) == 0 || strcmp("Hochzeit",$categories[$i]) == 0 || strcmp("Urlaub",$categories[$i]) == 0){
					$readMoreCount = 1;
				}
				if($_GET['days']){
					$daysTmp = $_GET['days'];
				}
				
				$today = mktime(0,0,0);
				
				if($today + (86400*$daysTmp) > $notDone[$j]['1']){
					if($tmp == 0){
						echo '<tr><th colspan="4">'.$categories[$i].'</th></tr>';
						echo "\r\n";
					}
					$tmp++;
					if($tmp == $readMoreCount){
						echo '<tr><td colspan="4"><div id="table'.$categories[$i].'" style="display:none"><table>';
						echo "\r\n";
					}
					echo '<tr>';
						echo '<td width="10"><input type="radio" id="'.$j.'" name="modify" value="'.$j.'"></td>';
						echo '<td width="10">'.date("d.m.Y",$notDone[$j]['1']).'</td>';
						echo '<td width="10">&nbsp;</td><td>'.$notDone[$j]['2'].'</td>';
					echo '</tr>';
					echo "\r\n";
				}
			}
		}
		if ($tmp >= $readMoreCount){
			echo '</table></div></tr></td>';
			echo "\r\n";
			echo '<tr><td colspan="4">';
			echo '<a id="myLink" href="#" onclick="'."myFunction('table".$categories[$i]."')".';return false;">Mehr Anzeigen</a>';
			echo '</td></tr>';
			echo "\r\n";
		}
	}
	echo '</table>';
	echo '</form>';
}

function done_todo(){
	if ($_GET['input'] == "modify" && count($_POST) > 0 && $_POST != array('count'=>1)){
		print_r($_POST);
		$fileName = "notDone.txt";
		
		$notDone = '';
		if(file_exists($fileName)){
			$notDone = file_get_contents($fileName);
		}
		
		$notDone = explode("\n",$notDone);
		if ($_POST['type'] != "delete"){
			$entry = explode("<tab>",$notDone[$_POST['modify']]);
			$shift = $entry['1'];
			if ($_POST['type'] == "day"){
				for ($i=0;$i<$_POST['count'];$i++){
					$shift = strtotime("+1 day", $shift);
				}
			}
			if ($_POST['type'] == "week"){
				for ($i=0;$i<$_POST['count'];$i++){
					$shift = strtotime("+1 week", $shift);
				}
			}
			if ($_POST['type'] == "month"){
				for ($i=0;$i<$_POST['count'];$i++){
					$shift = strtotime("+1 month", $shift);
				}
			}
			if ($_POST['type'] == "year"){
				for ($i=0;$i<$_POST['count'];$i++){
					$shift = strtotime("+1 year", $shift);
				}
			}
			$shift = date("d.m.Y",$shift);
			$shift = explode(".",$shift);
			
			
			$newEntry = $entry['0'].'<tab>';
			$newEntry .= mktime(0,0,0,$shift[1],$shift[0],$shift[2]).'<tab>';
			$newEntry .= $entry['2'].'<tab>';
			
			
			
			unset($notDone[$_POST['modify']]);
			array_push($notDone,$newEntry);
		} else {
			unset($notDone[$_POST['modify']]);
		}
		asort($notDone);
		$notDone = array_merge($notDone);
		$notDone = implode("\n",$notDone);
		
		$handle = fopen ($fileName, "w");
		fwrite ($handle, $notDone);
		fclose ($handle);
	}
}


function setFilter($days){
	echo '<form action="index.php" method="get">';
	echo '<label for="Zeitfilter">Zeitfilter:</label><br>';
	echo '<input type="number" id="days" name="days" autocomplete="off" min="1" value="'.$days.'">';
	echo '<button type="submit">Eingaben absenden</button>';
	echo '</form>';
}

echo_header();

save_todo();

done_todo();

enter_todo();

read_todo($days);

setFilter($days);

footer();

?>
