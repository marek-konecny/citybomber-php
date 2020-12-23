<?php
session_start();
header("Refresh: 0.4"); /* Rychlost herního cyklu */
?>
<!DOCTYPE html>
<html>
<head>
	<title>City bomber</title>
	<meta charset="UTF-8">

	<style type="text/css">
		*{
			font-family: arial, sans-serif;
			margin: 0;
			padding: 0;
			background-color: black;
		}
		table{
			border-collapse: collapse;
			table-layout: fixed;
		}
		td{
			border: none;
			width: 60px;
			height: 60px;
		}
		.air{
			background-color: #0C1425;
		}
		.player{
			background-color: #0C1425;
			color: white;
			font-size: 30pt;
			text-align:center;
			text-shadow: 0px 0px 20px rgba(255, 255, 255, 0.5);
		}
		.building{
			border-top: solid #3A4C5E 1px;
			background-color: #222;
			color: #F8FF9E;
			text-align:center;
		}
		.buildingExploded{
			background-color: red;
		}
		.bomb{
			background-color: #0C1425;
			color: transparent;
			font-size: 15pt;
			text-align:center;
			text-shadow: 0px 0px 1px #F8FF9E;
		}
		input{
			position: absolute;
			opacity: 0%; /* Skryju formulář, který používám k inputu. */
		}
		p{
			padding: 2px;
			color: silver;
		}
	</style>
</head>

<body>
<form action=# method="post">
	<input value=<?php echo "\"".$_SESSION["inputNumber"]."\"";?> type="text" id="textbar" name="textbar" autofocus>
	<!-- Výchozí hodnota formuláře se každým úhozem zvyšuje.
	Kromě toho že tím můžu kontrolovat úhoz se to také hodí jako počitadlo úhozů pro debugging. -->
	<input type="submit" value="Submit">
</form>


<?php
class Cell{
	public $x;
	public $y;
	public $typ;
	function __construct($x,$y,$typ){
		$this->x = $x;
		$this->y = $y;
		$this->typ = $typ; /* Třída pro každou buňku tabulky/herního pole, jež obsahuje informaci o typu a souřadnice. */
}}

$radku = 15;
$sloupcu = 33; /* Tyto hodnoty lze libovolně upravit, bude ale třeba upravit i rozměry buňky v CSS. */

if (!isset($_SESSION["inputNumber"])){ /* Pokud začíná nová session, vytvořím všechny potřebné proměnné, jako: */
	$_SESSION["inputNumber"] = 0; /* počitadlo úhozů, */
	$_SESSION["player"] = array("x" => 0,"y" => 0, "direction" => 1); /* proměnná hráč, která obsahuje souřadnice a směr letadla, */
	$_SESSION["cycle"] = 0; /* počitadlo herního cyklu, */

	$_SESSION["field"] = array(); /* herní pole všech buněk, */
	$_SESSION["buildingCollums"] = array(); /* čísla sloupců, ve kterých se nachází budova (potřebné ke generaci budov níže). */

	for ($radekNum=0; $radekNum < $radku; $radekNum++){
		array_push($_SESSION["field"],array()); /* Do herního pole natlačím řádky. */

		for ($sloupecNum=0; $sloupecNum < $sloupcu; $sloupecNum++){ /* Do řádků natlačím sloupce. */

			if ((mt_rand(1, 15) == 1 || in_array($sloupecNum, $_SESSION["buildingCollums"])) && $radekNum > $radku/1.8){
				/* Buňka bude typu budova na základě náhodné funkce anebo pokud už je v daném sloupci budova před ní. */
				/* Druhý argument v mt_rand lze snížit pro zvýšení hustoty budov a naopak. */
				$_SESSION["field"][$radekNum][$sloupecNum] = new Cell($radekNum, $sloupecNum, "building");
				array_push($_SESSION["buildingCollums"], $sloupecNum); /* Uložím informaci o "stavebním sloupci" */
			}
			else{
				$_SESSION["field"][$radekNum][$sloupecNum] = new Cell($radekNum, $sloupecNum, "air");
}}}}

if($_SESSION["inputNumber"] == $_POST["textbar"] && isset($_POST["textbar"])){
	$_SESSION["inputNumber"]++;
	/* Když detekuji úhoz (tj. při odeslání formuláře enterem), změním typ buňky na pozici letadla na bombu. */
	$_SESSION["field"][$_SESSION["player"]["x"]][$_SESSION["player"]["y"]-$_SESSION["player"]["direction"]]->typ = "bomb";
}

if ($_SESSION["player"]["y"] == -1 && $_SESSION["player"]["direction"] == -1){
	$_SESSION["player"]["direction"] = 1;
	$_SESSION["player"]["x"]++;
}
elseif ($_SESSION["player"]["y"] == $sloupcu && $_SESSION["player"]["direction"] == 1){
	$_SESSION["player"]["direction"] = -1;
	$_SESSION["player"]["x"]++;
	/* Kontroluji jestli je letadlo mimo pole a pokud ano, prohodím směr a pošlu ho o řádek níž.
	Ukládat směr jako 1 a -1 místo booleanu nebo "right" a "left" se bude později hodit víc než jednou. */
}

$_SESSION["field"] [$_SESSION["player"]["x"]] [$_SESSION["player"]["y"]]->typ = "player";
/* Buňku na pozici letadla měním na typ player abych mohl snáze použít switch case. For cyklus níže vykresluje tabulku. */
echo "<table>".PHP_EOL;
for ($radkuDrawn=0; $radkuDrawn < $radku; $radkuDrawn++){
	echo "<tr>";

	for ($sloupcuDrawn=0; $sloupcuDrawn < $sloupcu; $sloupcuDrawn++){

		switch($_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ){
			/* Switch case pro vykreslení různých typů buněk */
			case "player":
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
				echo "<td class=\"player\" style=\"transform: scale(".$_SESSION["player"]["direction"].", 1);\">✈</td>";
				/* Zde se poprvé uplatňuje ukládání směru v 1 a -1. Můžu na základě směru zrcadlit letadlo pomocí transform:scale. */
				break;

			case "building":
				echo "<td class=\"building\">| |</td>";
				break;

			case "buildingDestroyed":
				echo "<td class=\"building\">| |</td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "buildingExploded";
				break;

			case "buildingExploded":
				echo "<td class=\"buildingExploded\"></td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
				break;

			case "bomb":
				echo "<td class=\"bomb\">‍🌰</td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
				/* Buňka typu bomba se automaticky mění na vzduch - */

				if ($_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ == "building"){
					$_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ = "buildingDestroyed";
					/* - a mění buňku pod sebou na typ buildingDestroyed v případě že se tam nacházela budova. */
				}
				else{
					$_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ = "bombShadow";
					/* - nebo mění buňku pod sebou na typ bombShadow. */
				}
				break;

			case "bombShadow":
				/* bombShadow typ je buňka, která se bombou stane v příštím cyklu. Slouží to pro postupný sestup bomb dolů po herním poli.
				Stejný princip platí pro building -> buildingDestroyed -> buildingExploded pro postupnou destrukci budovy.*/
				echo "<td class=\"air\"></td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "bomb";
				break;

			case "air":
				echo "<td class=\"air\"></td>";
				break;
	}}

	echo "</tr>".PHP_EOL;
}
echo "</table>";


if($_SESSION["field"] [$_SESSION["player"]["x"]] [$_SESSION["player"]["y"] + $_SESSION["player"]["direction"]]->typ == "building"
 ||$radku * ($sloupcu+1) == $_SESSION["cycle"]
 ||$_POST["textbar"][0] == "r" || $_POST["textbar"][0] == "R"){ /* Všechny podmínky pro reset hry. Kombinace R+Enter je jen odesláni formuláře se znakem R. */
	session_destroy();
}

$_SESSION["player"]["y"] += $_SESSION["player"]["direction"];
/* Zde se podruhé uplatňuje ukládání směru v 1/-1. Vyhnu se alespoň jednomu if statementu a místo toho jen přičtu směr do pozice letadla. */
$_SESSION["cycle"]++;
?>

<p>Ovládání: Enter pro spuštění bomby, R+Enter pro reset hry (je třeba kombinaci provést rychle, někdy je třeba víc pokusů)</p>
<p>Pravidla: Naražení do budovy a dolet do cíle resetuje hru</p>
<p>Zobrazení: Pro výchozí nastavení 1920x1080 rozlišení a prohlížeč ve fullscreenu <a href="index_debug.php">Debugging varianta</a></p>
</body>
</html>