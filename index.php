<?php
session_start();
header("Refresh: 1");
?>
<!DOCTYPE html>
<html>
<head>
	<title>City bomber</title>
	<meta charset="UTF-8">

	<style type="text/css">
	td{
		border: solid grey 1px;
		width: 25px;
		height: 25px;
	}

	</style>
</head>
<body>

<form action=# method="post">
	<input style="opacity:100%" value=<?php echo "\"".$_SESSION["inputnumber"]."\"";?> type="text" id="textbar" name="textbar" autofocus>
	<input style="opacity:100%" type="submit" value="Submit">
</form>


<?php
class Bunka{
	public $x;
	public $y;
	public $typ;
	function __construct($x,$y,$typ){
		$this->x = $x;
		$this->y = $y;
		$this->typ = $typ;
	}
}

$radku = 15;
$sloupcu = 15;

if (!isset($_SESSION["inputnumber"])) {
	$_SESSION["inputnumber"] = 0;
	$_SESSION["justMovedDown"] = 0;

	$_SESSION["player"] = array("x" => 0,"y" => 0, "smer" => 1);

	$_SESSION["stavebniSloupce"] = array();
	$_SESSION["pole"] = array();

	for ($radekNum=0; $radekNum < $radku; $radekNum++) {
		array_push($_SESSION["pole"],array());

		for ($sloupecNum=0; $sloupecNum < $sloupcu; $sloupecNum++) {

			if ((mt_rand(1, 10) == 1 || in_array($sloupecNum, $_SESSION["stavebniSloupce"])) && $radekNum > $radku/2.5)
			{
				$_SESSION["pole"][$radekNum][$sloupecNum] = new Bunka($radekNum, $sloupecNum, "building");
				array_push($_SESSION["stavebniSloupce"], $sloupecNum);
			}
			else
			{
				$_SESSION["pole"][$radekNum][$sloupecNum] = new Bunka($radekNum, $sloupecNum, "air");
			}
		}
	}
}

if($_POST["textbar"][0] == "r")
{
	session_destroy();
}

if($_SESSION["inputnumber"] == $_POST["textbar"] && isset($_POST["textbar"]))
{
	$_SESSION["inputnumber"]++;

	$_SESSION["pole"][$_SESSION["player"]["x"]][$_SESSION["player"]["y"]-$_SESSION["player"]["smer"]]->typ = "bomba";
}
var_dump($_SESSION["inputnumber"]);

if ($_SESSION["player"]["y"] == -1 && $_SESSION["player"]["smer"] == -1)
{
	$_SESSION["player"]["smer"] = 1;
	$_SESSION["player"]["x"]++;
}
elseif ($_SESSION["player"]["y"] == $sloupcu && $_SESSION["player"]["smer"] == 1)
{
	$_SESSION["player"]["smer"] = -1;
	$_SESSION["player"]["x"]++;
}

$movedBomb = 0;

echo "<table>".PHP_EOL;
for ($radkuDrawn=0; $radkuDrawn < $radku; $radkuDrawn++)
{
	echo "<tr>";

	for ($sloupcuDrawn=0; $sloupcuDrawn < $sloupcu; $sloupcuDrawn++)
	{
		if ($_SESSION["player"]["x"] == $radkuDrawn && $_SESSION["player"]["y"] == $sloupcuDrawn)
		{
			echo "<td style=\"background-color: white; transform: scale(".$_SESSION["player"]["smer"].", 1);text-align:center\">✈</td>";
		}
		elseif($_SESSION["pole"][$radkuDrawn][$sloupcuDrawn]->typ == "building")
		{
			echo "<td style=\"background-color: #222; color: blue; text-align:center\">| |</td>";
		}
		elseif($_SESSION["pole"][$radkuDrawn][$sloupcuDrawn]->typ == "bomba")
		{
				echo "<td style=\"background-color: white;text-align:center\">💣</td>";
				if ($movedBomb < $_SESSION["inputnumber"])
				{
					$_SESSION["pole"][$radkuDrawn][$sloupcuDrawn]->typ = "air";
					$_SESSION["pole"][$radkuDrawn+1][$sloupcuDrawn]->typ = "bomba";
					$movedBomb += 1;
				}
		}
		elseif($_SESSION["pole"][$radkuDrawn][$sloupcuDrawn]->typ == "air")
		{
			echo "<td></td>";
		}
	}

	echo "</tr>".PHP_EOL;
}
echo "</table>";


echo "movedBomb: ".$movedBomb."<br>player y: ".$_SESSION["player"]["y"]."<br>sloupcu: ".$sloupcu."<br>smer: ".$_SESSION["player"]["smer"];



$_SESSION["player"]["y"] += $_SESSION["player"]["smer"];

?>

</body>
</html>