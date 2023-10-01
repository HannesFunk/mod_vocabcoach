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
- activity logger: prüfen! --> müsste funktionieren
- Zusammenfassung nach Abfrage
- dritte Spalte! --> Done!

Features:
- Stalking-Tools

OUT OF SCOPE:
- Übersicht: how is my class doing?
- Vokabeln mit Bildern