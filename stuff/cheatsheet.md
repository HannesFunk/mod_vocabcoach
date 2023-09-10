### Uninstalling a plugin
php admin/cli/uninstall_plugins.php --plugins=mod_vocabcoach --run

### Running cron
php admin/cli/cron.php

## Requires
$PAGE->requires->js_call_amd('mod_vocabcoach/check', 'init');

$PAGE->requires->css('/mod/vocabcoach/styles/boxes.css');

## Errors nobody understands
'cannot find file with external function implementation'. Check db/services.php!


TODO:
Programming:
- vocabRows ordentlich!
- check-type-Bug?!
- check: Anzeige, falls letztes getippt!


Mobile Stuff:
- Main Activity Buttons: Spacing
- Center Vokabelboxes (display: flex?)
- ListView zu breit
- Don't capitalize Englisch
- 

Features:
- Gamification
- Stalking-Tools
- Übersicht: how is my class doing?
- nach Modusänderung Vokabeln randomisieren

OUT OF SCOPE:
- Vokabeln mit Bildern
- Tippen/ etc. als Standardeinstellung

Improvements
- Zusammenfassung nach Abfrage
- Code-Linting


Erstmal ned
- Buch-Liste

Meta:
- Elternbrief
- Modul für Unterricht