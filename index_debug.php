<?php
session_start();
header("Refresh: 0.4");
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
		}
		table{
			table-layout: fixed;
		}
		td{
			border: solid black 1px;
			width: 50px;
			height: 50px;
		}
		.air{
			background-color: white;
		}
		.player{
			background-color: lime;
			font-size: 10pt;
			text-align:center;
		}
		.building{
			background-color: black;
		}
		.buildingDestroyed{
			background-color: darkblue;
		}
		.buildingExploded{
			background-color: blue;
		}
		.bomb{
			background-color: pink;
			font-size: 10pt;
			text-align:center;
			color: transparent;
			text-shadow: 0px 0px 0px black;
		}
		.bombShadow{
			background-color: silver;
			font-size: 10pt;
			text-align:center;
			color: transparent;
			text-shadow: 0px 0px 0px white;
		}
		input{
			position: relative;
			opacity: 100%;
		}
		p{
			padding: 2px;
		}
	</style>
</head>

<body>
<form action=# method="post">
	<input value=<?php echo "\"".$_SESSION["inputNumber"]."\"";?> type="text" id="textbar" name="textbar" autofocus>
	<input type="submit" value="Submit"> <?php echo "inputNumber: ".$_SESSION["inputNumber"]." | _POST[textbar]: ".$_POST["textbar"]; ?>
</form>


<?php
class Cell{
	public $x;
	public $y;
	public $typ;
	function __construct($x,$y,$typ){
		$this->x = $x;
		$this->y = $y;
		$this->typ = $typ;
}}

$radku = 15;
$sloupcu = 33;

if (!isset($_SESSION["inputNumber"])){
	$_SESSION["inputNumber"] = 0;
	$_SESSION["player"] = array("x" => 0,"y" => 0, "direction" => 1);
	$_SESSION["cycle"] = 0;

	$_SESSION["field"] = array();
	$_SESSION["buildingCollums"] = array();

	for ($radekNum=0; $radekNum < $radku; $radekNum++){
		array_push($_SESSION["field"],array());

		for ($sloupecNum=0; $sloupecNum < $sloupcu; $sloupecNum++){

			if ((mt_rand(1, 15) == 1 || in_array($sloupecNum, $_SESSION["buildingCollums"])) && $radekNum > $radku/1.8){
				$_SESSION["field"][$radekNum][$sloupecNum] = new Cell($radekNum, $sloupecNum, "building");
				array_push($_SESSION["buildingCollums"], $sloupecNum);
			}
			else{
				$_SESSION["field"][$radekNum][$sloupecNum] = new Cell($radekNum, $sloupecNum, "air");
}}}}

if($_SESSION["inputNumber"] == $_POST["textbar"] && isset($_POST["textbar"])){
	$_SESSION["inputNumber"]++;

	$_SESSION["field"][$_SESSION["player"]["x"]][$_SESSION["player"]["y"]-$_SESSION["player"]["direction"]]->typ = "bomb";
}

if ($_SESSION["player"]["y"] == -1 && $_SESSION["player"]["direction"] == -1){
	$_SESSION["player"]["direction"] = 1;
	$_SESSION["player"]["x"]++;
}
elseif ($_SESSION["player"]["y"] == $sloupcu && $_SESSION["player"]["direction"] == 1){
	$_SESSION["player"]["direction"] = -1;
	$_SESSION["player"]["x"]++;
}

$_SESSION["field"] [$_SESSION["player"]["x"]] [$_SESSION["player"]["y"]]->typ = "player";

echo "<table>".PHP_EOL;
for ($radkuDrawn=0; $radkuDrawn < $radku; $radkuDrawn++){
	echo "<tr>";

	for ($sloupcuDrawn=0; $sloupcuDrawn < $sloupcu; $sloupcuDrawn++){

		switch($_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ){

			case "player":
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
				echo "<td class=\"player\" style=\"transform: scale(".$_SESSION["player"]["direction"].", 1);\">✈</td>";
				break;

			case "building":
				echo "<td class=\"building\"></td>";
				break;

			case "buildingDestroyed":
				echo "<td class=\"buildingDestroyed\"></td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "buildingExploded";
				break;

			case "buildingExploded":
				echo "<td class=\"buildingExploded\"></td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
				break;

			case "bomb":
				echo "<td class=\"bomb\">‍🌰</td>";
				$_SESSION["field"][$radkuDrawn][$sloupcuDrawn]->typ = "air";

				if ($_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ == "building"){
					$_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ = "buildingDestroyed";
				}
				else{
					$_SESSION["field"][$radkuDrawn+1][$sloupcuDrawn]->typ = "bombShadow";
				}
				break;

			case "bombShadow":
				echo "<td class=\"bombShadow\">🌰</td>";
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
 ||$_POST["textbar"][0] == "r" || $_POST["textbar"][0] == "R"){
	session_destroy();
}

$_SESSION["player"]["y"] += $_SESSION["player"]["direction"];
$_SESSION["cycle"]++;

echo "<p>Cyklus: ".$_SESSION["cycle"]."</p>";
echo "<p>Player_x: ".$_SESSION["player"]["x"]." | Player_y: ".$_SESSION["player"]["y"]." | Player_direction: ".$_SESSION["player"]["direction"]."</p>";
?>
<br>
<p><a href="index.php">Zpět na normální variantu</a></p>
</body>
</html>