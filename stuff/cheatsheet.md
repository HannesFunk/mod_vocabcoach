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
- addvocab - js: letzte node mit front[]
- add_vocab: cancel: falscher redirect

Features:
- Gamification
- Stalking-Tools
- Vokabeln mit Bildern
- Übersicht: how is my class doing?
- Listen bearbeiten als Ersteller oder Teacher
- Tippen/ etc. als Standardeinstellung


Improvements
- Zusammenfassung nach Abfrage
- globale Einstellungen
- globales Layout
- Code-Linting
- center Karteikästen auf Startseite
- Buch-Liste
- Jahrgangsstufe: automatisch wählen, Dropbown
- Aktion: Liste in meinen Kasten übernehmen

Meta:
- Elternbrief
- Modul für Unterricht