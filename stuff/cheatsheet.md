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
- Check: reward after nn correct
- activity logger: prüfen! --> müsste funktionieren
- Tippen/ etc. als Standardeinstellung --> ist oben!
- Zusammenfassung nach Abfrage
- ListView zu breit für mobile Anzeige
- dritte Spalte!

Features:
- Stalking-Tools

OUT OF SCOPE:
- Übersicht: how is my class doing?
- Vokabeln mit Bildern