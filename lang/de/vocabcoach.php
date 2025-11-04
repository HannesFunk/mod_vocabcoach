<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_vocabcoach
 * @category    string
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Vokabelcoach';
$string['plugin_name'] = 'Vokabelcoach';
$string['modulename'] = 'Vokabelkasten';
$string['modulenameplural'] = 'Vokabelkästen';
$string['pluginadministration'] = 'Einstellungen';


$string['vocabcoachsettings'] = 'Einstellungen';
$string['vocabcoachname'] = 'Vokabelkasten';
$string['vocabcoachname_help'] = 'Hilfe';
$string['vocabcoachnameplural'] = 'Vokabelkasten';

$string['boxtimes'] = 'Wiederholungszeiten';
$string['info_boxtimes'] = 'Setze hier die Zeiten, nach denen Vokabeln in den verschiedenen Fächern wiederholt werden sollen und wieder als "fällig" erscheinen.';
$string['boxtime'] = "Zeit für Box";
$string['move_undue'] = 'Vokabeln, die vor Ablauf der Wiederholungszeit abgefragt werden, ins nächste Fach bewegen.';
$string['move_undue_help'] = 'Vokabeln können jederzeit wiederholt werden. Ist dieses Feld aktiviert, werden auch Vokabeln, die noch vor Ablauf der Wiederholungszeit abgefragt werden, in die nächste Box verschoben. Andernfalls verbleiben diese in der aktuellen Box.';
$string['third_active'] = 'Dritte Spalte für zusätzliche Informationen aktivieren.';

$string['add_vocab_title'] = 'Vokabeln hinzufügen';
$string['front'] = 'Vorderseite';
$string['back'] = 'Rückseite';
$string['cancelled_form'] = 'Eintragen abgebrochen.';
$string['add_vocab_successful'] = 'Vokabeln hinzugefügt.';
$string['edit_vocab_successful'] = 'Liste geändert.';
$string['add_vocab_info_lines'] = 'Weitere Zeilen werden automatisch ergänzt.';
$string['add_vocab_add_to_user_database'] = 'Sofort zum eigenen Karteikasten hinzufügen.';
$string['edit_vocab_instructions'] = 'Um eine Vokabeln zu löschen, lass beide Seiten leer.';
$string['edit_list_not_allowed'] = 'Du hast nicht die Berechtigung, diese Liste zu ändern.';
$string['add_vocab_add_to_user_database_help'] = 'Wenn du diesen Haken nicht setzt, wird nur die Liste angelegt, die Vokabeln landen aber nicht direkt in deinem Kasten. Du kannst sie aber jederzeit später hinzufügen.';

$string['vocab'] = 'Vokabel';
$string['vocabplural'] = 'Vokabeln';
$string['list'] = 'Liste';
$string['listplural'] = 'Listen';
$string['listprops'] = 'Listeneigenschaften';
$string['instructions'] = 'Hinweise zum Eintippen von Vokabeln';
$string['list_private'] = 'Private Liste';
$string['list_private_help'] = 'Wenn du diesen Haken setzt, kannst nur du selbst diese Liste sehen. Andernfalls können die anderen Teilnehmer aus dem Kurs die Liste sehen, aber nicht bearbeiten.';
$string['list_distribute_now'] = 'An alle verteilen';
$string['list_distribute_now_help'] = 'Wenn du diesen Haken setzt, werden die Vokabeln direkt bei allen anderen Schüler:innen in diesem Kurs hinzugefügt. Für vocab duties (in-class oder homework) bitte setzen.';

$string['type_vocab_label'] = 'Vokabeln tippen';
$string['mode'] = 'Abfragemodus';

// Lists page.
$string['lists'] = 'Vokabellisten';
$string['intro_lists'] = 'Du siehst hier alle öffentlichen Vokabellisten, die andere Schüler in diesem Kurs angelegt haben. Du kannst entweder direkt von diesen Listen lernen oder die ganze Liste in deinen Vokabelkasten übernehmen.';
$string['lists_onlyown'] = 'Nur eigene anzeigen';

$string['name'] = 'Name';
$string['year_short'] = 'Jgst.';
$string['year'] = 'Jahrgangsstufe';
$string['book'] = 'Buch';
$string['unit'] = 'Kapitel';
$string['count'] = 'Anzahl Vokabeln';
$string['creator'] = 'Erstellt von';
$string['actions'] = 'Aktionen';

// Action menu (lists_action_menu.mustache).
$string['actions_show_actions'] = 'Aktionen anzeigen';
$string['actions_start_check'] = 'Abfrage starten';
$string['actions_add_to_my_box'] = 'In meinen Kasten übernehmen';
$string['actions_export_pdf'] = 'Als PDF exportieren';
$string['actions_export_csv'] = 'Als CSV exportieren';
$string['actions_edit'] = 'Bearbeiten';
$string['actions_delete'] = 'Löschen';
$string['actions_distribute_to_course'] = 'An Kurs verteilen';

// Mobile app strings.
$string['check_vocab'] = 'Vokabeln abfragen';
$string['box'] = 'Box';
$string['due'] = 'Fällig';
$string['total'] = 'Gesamt';
$string['start_check'] = 'Abfrage starten';
$string['no_vocab_due'] = 'Keine Vokabeln in dieser Box fällig';
$string['check_complete'] = 'Abfrage beendet';
$string['correct'] = 'Richtig';
$string['incorrect'] = 'Falsch';
$string['known'] = 'Gewusst';
$string['unknown'] = 'Nicht gewusst';
$string['next_vocab'] = 'Nächste Vokabel';
$string['finish_check'] = 'Abfrage beenden';
$string['next_due'] = 'Nächste fällig';
$string['no_vocab_available'] = 'Keine Vokabeln verfügbar';
$string['days_logged_in'] = 'Tage eingeloggt';
$string['days_checked_all'] = 'Tage alle abgefragt';
$string['check_mode_buttons'] = 'Button-Modus';
$string['check_mode_type'] = 'Eingabe-Modus';
$string['type_answer'] = 'Antwort eingeben';
$string['check_answer'] = 'Antwort prüfen';
$string['show_answer'] = 'Antwort anzeigen';
$string['back_to_boxes'] = 'Zurück zu den Boxen';