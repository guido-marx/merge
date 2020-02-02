# merge
Merged eine Liste von Intervallen in eine neue Liste disjunkter Intervalle

## Input:
* Das Programm liest eine Liste von Intervallen aus einer Datei im JSON-Format.
* Es wird weder vorausgesetzt, dass die Liste sortiert ist, noch dass die erste Zahl eines Intervalls kleiner als die zweite ist.

## Merge:
1. Es werden in allen Intervallen, in denen der zweite Wert kleiner als der erste ist, die beiden Werte getauscht.
2. Alle Intervalle werden aufsteigend sortiert, Ordnungskriterium ist der erste Wert in jedem Intervall.
3. In einer linearen Suche werden alle Intervalle, die sich überlappen zu einem neuen Intervall zusammengefasst.

## Output:
Die neuerstellte Liste der verbliebenen Intervalle wird in eine Datei im JSON-Format geschrieben.

Das Programm ist in PHP geschrieben. Aufruf:
```
php -f merge.php [Output-File] [Input-File]
```
Beide Parameter sind optional, Default-Werte sind output.json und input.json

## Verschiedene Datenquellen
Kommen (viele) Daten aus mehreren Quellen, kann die Performance verbessert werden, indem alle Quellen erst einzeln sortiert und gemerged werden. Anschließend werden nur noch die Ergebnisse zusammen gefasst und gemergt. Dieser Vorgang kann in einer Kaskade beliebig häufig wiederholt werden.

Dafür gibt es eine Variante des Programms. Aufruf:
```
php -f merge2.php [Output-File] [Input-File1] [Input-File2]
```
Alle Parameter sind optional, Default-Werte sind output.json, input1.json, input2.json

Es werden die Listen beider Dateien separat sortiert (das kann weggelassen werden, wenn die aufsteigende Sortierung immer vorausgesetzt werden kann). Anschließend werden alle Intervalle beider Dateien in aufsteigender Ordnung durchlaufen und zu einem Output gemerged.

## Testdaten:
Das folgende Tool erzeugt per Zufallsgenerator Testdaten. Aufruf:
```
php -f gen-input.php [Output-File] [Anzahl der zu erzeugenden Intervalle]
```
Beide Parameter sind optional, Default-Werte sind input.json und 30
