#!/usr/bin/php
<?php

if (isset($argv[1]))
  $outputFile = $argv[1];
else
  $outputFile ='input.json';

if (isset($argv[2]) and is_numeric($argv[2]))
  $number = (int) $argv[2];
else
  $number = 30;

printf("Generiere %d Datensätze und schreibe sie in die Datei %s\n", $number, $outputFile);

$output = array();

//$number = random_int(1000,1500);
$max = 1000;
$prim = 719;
$first = random_int(0,500);

for ($i = 0; $i < $number; $i++)
{
  //$first = random_int(0,99);
  $first = ( $first + $prim ) % $max;
  $diff  = random_int(-50,50);
  $output[] = array($first, max($first + $diff, 0));
}

file_put_contents($outputFile, json_encode($output));

?>
