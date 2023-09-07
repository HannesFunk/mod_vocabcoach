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
- JS: relative paths

Features:
- Gamification
- Stalking-Tools
- Übersicht: how is my class doing?
- Listen bearbeiten als Ersteller oder Teacher
- Tippen/ etc. als Standardeinstellung

OUT OF SCOPE:
- Vokabeln mit Bildern

Improvements
- Zusammenfassung nach Abfrage
- globales Layout
- Code-Linting
- center Karteikästen auf Startseite
- Buch-Liste
- Aktion: Liste in meinen Kasten übernehmen
- Anleitung für add-vocab beim editten (z.B. löschen durch leer lassen)

Meta:
- Elternbrief
- Modul für Unterricht