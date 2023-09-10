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
- check-type-Bug?!

Mobile Stuff:
- ListView zu breit
- Don't capitalize Englisch --> done, check
- input-type-area: mit Enter bestätigen
- Zufalls-Lob

Features:
- Gamification
- Stalking-Tools
- Übersicht: how is my class doing?

OUT OF SCOPE:
- Vokabeln mit Bildern
- Tippen/ etc. als Standardeinstellung

Improvements
- Zusammenfassung nach Abfrage

Erstmal ned
- Buch-Liste

Meta:
- Elternbrief
- Modul für Unterricht