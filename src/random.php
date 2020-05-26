<?php
include 'controller.php';
$bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
$query = "SELECT COUNT(*) as nb FROM memes";
$result = $bdd->query($query);
$nbMemes = $result->fetch()['nb'];
$randomInt = random_int(1,$nbMemes);
$meme = $bdd->prepare('SELECT * FROM memes WHERE id=?');
$meme->execute(array($randomInt));
echo $meme->fetch()['link'];
?>