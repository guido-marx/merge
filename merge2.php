#!/usr/bin/php
<?php

// Dateiname für Ergebnis setzen
if (isset($argv[1]))
  $fileOutput = $argv[1];
else
  $fileOutput = 'output.json';

// Dateiname für erste Eingabe setzen
if (isset($argv[2]))
  $fileInput1 = $argv[2];
else
  $fileInput1 = 'input1.json';

// Dateiname für zweite Eingabe setzen
if (isset($argv[3]))
  $fileInput2 = $argv[3];
else
  $fileInput2 = 'input2.json';

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

printf("Merge Daten aus %s und %s, schreibe Ergebnis nach %s\n", $fileInput1, $fileInput2, $fileOutput);

// Lese Eingabedaten aus beiden Datein
$em = error_reporting(E_ERROR | E_PARSE);
if (($input1 = file_get_contents($fileInput1)) === false)
{
  printf("Fehler: Datei %s konnte nicht gelesen werden.\n", $fileInput1);
  exit(1);
}
if (($input2 = file_get_contents($fileInput2)) === false)
{
  printf("Fehler: Datei %s konnte nicht gelesen werden.\n", $fileInput2);
  exit(1);
}
error_reporting($em);

$input1 = json_decode($input1);
$input2 = json_decode($input2);
$input1_num = count($input1);
$input2_num = count($input2);

function sortList(&$input)
{
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
}

sortList($input1);
sortList($input2);

// Ausgabe zum Debuggen
/*
echo "Input 1:\n";
foreach($input1 as $n => $i) printf("%3d: %5d - %5d\n", $n, $i[0], $i[1]);
echo "Input 2:\n";
foreach($input2 as $n => $i) printf("%3d: %5d - %5d\n", $n, $i[0], $i[1]);
*/

// Ausgabe initialisieren: leeres Array
$output = array();
// erstes Intervall aus Eingabe entnehmen
if ($input1[0][0] < $input2[0][0])
  $last = array_shift($input1);
else
  $last = array_shift($input2);
// Variablen initialisieren
$current1 = null;
$current2 = null;

do {
  // Hole den nächsten Wert aus beiden Quellen, wenn noch nicht vorhanden
  if ($current1 === null) { $current1 = array_shift($input1); }
  if ($current2 === null) { $current2 = array_shift($input2); }

  // Alle Daten aus Datei eins sind bereits verarbeitet
  if ($current1 === null)
  {
    // Alle Daten aus Datei zwei auch: wir sind fertig
    if ($current2 === null)
    { break; }
    // Aus Datei zwei kommt jetzt der kleinste Wert
    else
    { $current = $current2; $current2 = null; }
  }
  // Alle Daten aus Datei zwei sind beretis verarbeitet,
  // aus Datei eins kommt jetzt der kleinste Wert
  elseif ($current2 === null)
  { $current = $current1; $current1 = null; }
  // Es kommen noch Intervalle aus beiden Dateien
  else
  {
    // Verwende das Intervall mit dem kleineren Wert
    if ($current1[0] < $current2[0])
    { $current = $current1; $current1 = null; }
    else
    { $current = $current2; $current2 = null; }
  }

  // Vergleich
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

// Schleife läuft, bis alle Daten aus beiden Dateien verarbeitet sind
} while ($current !== null);
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

printf("input1: %d Intervalle, input2: %d Intervalle, output: %d Intervalle\n", $input1_num, $input2_num, $output_num);
?>

