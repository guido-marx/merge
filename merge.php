#!/usr/bin/php
<?php

// Dateiname für Ergebnis setzen
if (isset($argv[1]))
  $fileOutput = $argv[1];
else
  $fileOutput = 'output.json';

// Dateiname für Eingabe setzen
if (isset($argv[2]))
  $fileInput = $argv[2];
else
  $fileInput = 'input.json';

// Vergleichsoperator für usort definieren, da
// der Standartvergleich in dieser Datenstruktur
// nicht funktioniert
// Vergleich der beiden unteren Grenzen der Intervalle
function cmp ($a, $b)
{
  if ($a[0] < $b[0]) return -1;
  if ($a[0] > $b[0]) return 1;
  return 0;
}

printf("Merge Daten aus %s und schreibe Ergebnis nach %s\n", $fileInput, $fileOutput);

// Lese Eingabedaten aus Datei
$em = error_reporting(E_ERROR | E_PARSE);
if (($input = file_get_contents($fileInput)) === false)
{
  printf("Fehler: Datei %s konnte nicht gelesen werden.\n", $fileInput);
  exit(1);
}
error_reporting($em);

$input = json_decode($input);
$input_num = count($input);

// gehe durch alle Intervalle:
// wenn obere Grenze kleiner als untere Grenze: vertausche die Werte
foreach ($input as $i => &$interval)
{
  if ($interval[1] < $interval[0])
  {
    $help = $interval[0];
    $interval[0] = $interval[1];
    $interval[1] = $help;
  }
}

// Sortiere Eingabe entsprechend dem oben definierten
// Vergleichsoperator "cmp" (Vergleich der unteren Grenzen der Intervalle)
usort($input, "cmp");

// Ausgabe zum Debuggen
//foreach($input as $n => $i) printf("%3d: %5d - %5d\n", $n, $i[0], $i[1]);

// Ausgabe initialieren: leeres Array
$output = array();
// erstes Intervall aus Eingabe entnehmen
$last = array_shift($input);
// Schleife, solange ein Intervall aus der Eingabe entnommen werden kann
while (($current = array_shift($input)) !== null)
{
  // Beide Intervalle sind disjunkt,
  // das erste (last) kann damit in die Ausgabe,
  // mit dem zweiten (current) wird ab jetzt verglichen
  if ($current[0] > $last[1])
  {
    $output[] = $last;
    $last = $current;
  }
  // beide Interfalle überschneiden sich,
  // es wird die Vereinigungsmenge von beiden genommen
  elseif ($current[1] > $last[1])
  {
    $last[1] = $current[1];
  }
  // else
  // sonst ist das zweite Intervall eine Teilmenge des ersten
  // dann ist es bereits im ersten Intervall (last) enthalten
}
// das letzte Vergleichsintervall gehört noch zur Ausgabemenge
$output[] = $last;
$output_num = count($output);

// Schreibe Ausgabedaten in Datei
file_put_contents($fileOutput, json_encode($output));

// Ausgabe zum Debuggen:
/*
echo "output:\n";
foreach($output as $n => $i) printf("%3d: %5d - %5d\n", $n, $i[0], $i[1]);
*/

printf("input: %d Intervalle, output: %d Intervalle\n", $input_num, $output_num);
?>

