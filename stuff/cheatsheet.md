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

- Vokabeln korrigieren (oder löschen!)
- CSV-Export

- Oscar: Zugangsdaten!

Features:
- Stalking-Tools

OUT OF SCOPE:
- Übersicht: how is my class doing?
- Vokabeln mit Bildern
- rules for implementation (JS) --> verworfen!
- erstes Fach mit Vokabel-tinder


CODING:
- refactor add_vocab.php

