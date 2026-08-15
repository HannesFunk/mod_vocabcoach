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

$string['actions'] = 'Aktionen';
$string['actions_add_to_my_box'] = 'In meinen Kasten übernehmen';
$string['actions_delete'] = 'Löschen';
$string['actions_distribute_to_course'] = 'An Kurs verteilen';
$string['actions_edit'] = 'Bearbeiten';
$string['actions_export_csv'] = 'Als CSV exportieren';
$string['actions_export_pdf'] = 'Als PDF exportieren';
$string['actions_show_actions'] = 'Aktionen anzeigen';
$string['actions_start_check'] = 'Abfrage starten';
$string['add_to_own_box'] = 'Zum eigenen Vokabelkasten hinzufügen';
$string['add_vocab'] = 'Vokabeln hinzufügen';
$string['add_vocab_add_to_user_database'] = 'Sofort zum eigenen Karteikasten hinzufügen.';
$string['add_vocab_add_to_user_database_help'] = 'Wenn du diesen Haken nicht setzt, wird nur die Liste angelegt, die Vokabeln landen aber nicht direkt in deinem Kasten. Du kannst sie aber jederzeit später hinzufügen.';
$string['add_vocab_info_lines'] = 'Weitere Zeilen werden automatisch ergänzt.';
$string['add_vocab_list'] = 'Vokabelliste für den Kurs hinzufügen';
$string['add_vocab_successful'] = 'Vokabeln hinzugefügt.';
$string['add_vocab_title'] = 'Vokabeln hinzufügen';
$string['add_vocab_user'] = 'Vokabeln nur für mich hinzufügen';
$string['back'] = 'Rückseite';
$string['book'] = 'Buch';
$string['box'] = 'Box';
$string['boxtime'] = "Zeit für Box";
$string['boxtimes'] = 'Wiederholungszeiten';
$string['cancelled_form'] = 'Eintragen abgebrochen.';
$string['check'] = 'Abfrage';
$string['check_button_no'] = 'Nicht gewusst!';
$string['check_button_override'] = 'Das hatte ich!';
$string['check_button_reveal'] = 'Auflösen';
$string['check_button_unknown'] = 'Nicht gewusst. Nächste';
$string['check_button_verify'] = 'Prüfen';
$string['check_button_yes'] = 'Gewusst!';
$string['check_end_instruction'] = 'Klicke in das Feld, um die Abfrage zu beenden.';
$string['check_instructions'] = "Überlege dir die Übersetzung der Vokabel und überprüfe deine Lösung, indem du in das leere Feld klickst.";
$string['check_pagetitle'] = 'Vokabelcoach - Abfrage';
$string['check_remaining'] = 'Noch {$a} Vokabeln';
$string['check_remaining_one'] = 'Noch 1 Vokabel';
$string['check_result_excellent'] = 'Sehr gut gemacht!';
$string['check_result_good'] = 'Gute Arbeit';
$string['check_result_ok'] = 'Okay.';
$string['check_result_poor'] = 'Hm. Da ist Luft nach oben!';
$string['check_result_solid'] = 'Solide!';
$string['check_summary_result'] = '{$a->known} von {$a->total} gewusst!';
$string['checkmode'] = 'Abfragemodus';
$string['checkmode_back'] = 'Nach Rückseite fragen';
$string['checkmode_front'] = 'Nach Vorderseite fragen';
$string['checkmode_random'] = 'Zufällig';
$string['checkmode_type'] = 'Vorderseite eintippen';
$string['confirm_delete_vocab'] = 'Vokabel wirklich aus dem Kasten entfernen?';
$string['count'] = 'Anzahl Vokabeln';
$string['creator'] = 'Erstellt von';
$string['csv_filename'] = 'vokabeln.csv';
$string['desc_back_default'] = 'Deutsch';
$string['due_notification_body'] = 'Du hast {$a->count} fällige Vokabeln. Öffne die Aktivität: {$a->url}';
$string['due_notification_small'] = '{$a} Vokabeln fällig';
$string['due_notification_subject'] = '{$a} Vokabeln fällig';
$string['duetime_day'] = '{$a} Tag';
$string['duetime_days'] = '{$a} Tagen';
$string['duetime_hour'] = '{$a} Stunde';
$string['duetime_hours'] = '{$a} Stunden';
$string['duetime_now'] = 'Jetzt';
$string['edit_list_not_allowed'] = 'Du hast nicht die Berechtigung, diese Liste zu ändern.';
$string['edit_vocab_instructions'] = 'Um eine Vokabeln zu löschen, lass beide Seiten leer.';
$string['edit_vocab_successful'] = 'Liste geändert.';
$string['edit_vocab_title'] = 'Vokabel bearbeiten';
$string['email_notifications'] = 'E-Mail-Benachrichtigungen';
$string['email_notifications_help'] = 'Erhalte E-Mail-Benachrichtigungen, wenn Vokabeln zur Wiederholung fällig sind.';
$string['error_add_vocab_to_list'] = 'Fehler beim Eintragen der Vokabeln in die Liste.';
$string['error_add_vocab_to_user'] = 'Fehler beim Hinzufügen der Vokabeln zu deinem Kasten.';
$string['error_create_list'] = 'Fehler beim Anlegen der Liste.';
$string['error_csv_output'] = 'CSV-Ausgabe kann nicht geöffnet werden.';
$string['error_edit_vocab'] = 'Fehler beim Bearbeiten der Vokabel. Bitte versuche es später erneut.';
$string['error_wrong_parameters'] = 'Falsche Parameter.';
$string['front'] = 'Vorderseite';
$string['info_boxtimes'] = 'Setze hier die Zeiten, nach denen Vokabeln in den verschiedenen Fächern wiederholt werden sollen und wieder als "fällig" erscheinen.';
$string['instructions'] = 'Hinweise zum Eintippen von Vokabeln';
$string['instructions_default'] = '<div class="pl-5 pr-3"><p>Beachte folgende Hinweise, wenn du neue Vokabeln eintippst, damit alle ähnliche Form haben.</p>
    <ul>
        <li><b>Verben:</b> im Englischen mit <i>to</i> einleiten (ohne Klammern etc.): <i>to go - gehen.</i></li>
        <li><b>Abkürzungen:</b> Normalerweise wie im Schulbuch verwenden,
        z. B. nicht <s>somebody</s> oder <s>sbd</s>, sondern <i>sb.</i> (mit Punkt). Hier eine Liste gängiger Abkürzungen: <br />
        Englisch: <i>sb. - sth. </i><br />
        Deutsch: <i>etw. - jmd.</i> (für jemandem, jemanden, jemand)
        </li>
        <li><b>Klammern vermeiden:</b> Präpositionen etc. einfach ohne Klammern übernehmen,
        im Deutschen wie im Englischen: <i>fear of - Angst vor</i>.</li>
    </ul>
    </div>
    ';
$string['instructions_help'] = 'Hinweise für Schülerinnen und Schüler, die sie beim Eingeben von Vokabeln sehen (beispielsweise einheitliche Abkürzungen).';
$string['instructions_short'] = 'Hinweise';
$string['intro_lists'] = 'Du siehst hier alle öffentlichen Vokabellisten, die andere Schüler in diesem Kurs angelegt haben. Du kannst entweder direkt von diesen Listen lernen oder die ganze Liste in deinen Vokabelkasten übernehmen.';
$string['leaderboard'] = 'Leaderboard';
$string['leaderboard_due'] = 'Fällige Vokabeln';
$string['leaderboard_rank'] = 'Rang';
$string['list'] = 'Liste';
$string['list_book_default'] = 'Access';
$string['list_distribute_now'] = 'An alle verteilen';
$string['list_distribute_now_help'] = 'Wenn du diesen Haken setzt, werden die Vokabeln direkt bei allen anderen Schüler:innen in diesem Kurs hinzugefügt. Für vocab duties (in-class oder homework) bitte setzen.';
$string['list_pagetitle'] = 'Vokabelcoach - Liste';
$string['list_private'] = 'Private Liste';
$string['list_private_help'] = 'Wenn du diesen Haken setzt, kannst nur du selbst diese Liste sehen. Andernfalls können die anderen Teilnehmer aus dem Kurs die Liste sehen, aber nicht bearbeiten.';
$string['list_title'] = 'Name der Liste';
$string['listplural'] = 'Listen';
$string['listprops'] = 'Listeneigenschaften';
$string['lists'] = 'Vokabellisten';
$string['lists_added_to_box'] = 'Neue Vokabeln von dieser Liste wurden deinem Kasten hinzugefügt.';
$string['lists_confirm_delete'] = 'Soll diese Liste wirklich gelöscht werden?';
$string['lists_confirm_distribute'] = 'Soll diese Liste wirklich an alle Teilnehmer in diesem Kurs verteilt werden?';
$string['lists_deleted'] = 'Liste gelöscht.';
$string['lists_distributed'] = 'Liste an alle Teilnehmer in diesem Kurs verteilt.';
$string['lists_empty'] = 'Keine Vokabelliste gefunden.';
$string['lists_onlyown'] = 'Nur eigene anzeigen';
$string['lists_pagetitle'] = 'Vokabelcoach - Vokabellisten';
$string['loading'] = "Lädt...";
$string['modulename'] = 'Vokabelkasten';
$string['modulenameplural'] = 'Vokabelkästen';
$string['move_undue'] = 'Vokabeln, die vor Ablauf der Wiederholungszeit abgefragt werden, ins nächste Fach bewegen.';
$string['move_undue_help'] = 'Vokabeln können jederzeit wiederholt werden. Ist dieses Feld aktiviert, werden auch Vokabeln, die noch vor Ablauf der Wiederholungszeit abgefragt werden, in die nächste Box verschoben. Andernfalls verbleiben diese in der aktuellen Box.';
$string['name'] = 'Name';
$string['no_vocabs_to_check'] = 'Hier sind momentan keine Vokabeln enthalten.';
$string['notification_userprefs_updated'] = 'Einstellungen erfolgreich aktualisiert.';
$string['notifications_enabled'] = 'Benachrichtigungen über fällige Vokabeln senden';
$string['notifications_enabled_help'] = 'Wenn aktiviert, sendet der Scheduler E‑Mail‑Benachrichtigungen an Schüler, wenn sie fällige Vokabeln in dieser Aktivität haben.';
$string['notifications_optout'] = 'Benachrichtungen standardmäßig aktiviert (Teilnehmer können sie deaktivieren)';
$string['notifications_optout_help'] = 'Andernfalls müssen Terilnehmer zunächst die Benachrichtigungen selbst aktivieren.';
$string['pdf_created_for'] = 'Erstellt für {$a->name} am {$a->date}';
$string['pdf_filename'] = 'vokabelliste.pdf';
$string['pdf_title_box'] = 'Vokabelliste (Box {$a})';
$string['plugin_name'] = 'Vokabelcoach';
$string['pluginadministration'] = 'Einstellungen';
$string['pluginname'] = 'Vokabelcoach';
$string['remove_from_box'] = "Aus meinem Kasten entfernen";
$string['settings'] = 'Einstellungen';
$string['show_lists'] = 'Vokabellisten anzeigen';
$string['task_sendduenotifications'] = 'Sende Benachrichtigungen für fällige Vokabeln';
$string['unit'] = 'Kapitel';
$string['update_vocab_success'] = 'Aktualisierung erfolgreich.';
$string['view_actions'] = 'Aktionen';
$string['view_activity'] = 'Aktivität';
$string['view_box_all_done'] = 'In dieser Box hast du bereits alle Vokabeln gelernt. Die nächsten Vokabeln sind in {$a} fällig.';
$string['view_box_due_first'] = 'Wiederhole zuerst die aktuell fälligen Vokabeln in dieser Box. Erst danach kannst du auch die anderen abfragen.';
$string['view_box_empty'] = 'In dieser Box sind zur Zeit keine Vokabeln enthalten.';
$string['view_box_title'] = 'Box {$a}';
$string['view_check_undue'] = 'Nicht fällige abfragen';
$string['view_checkall_streak'] = 'Durchgehend alle Vokabeln wiederholt seit {$a} Tagen.';
$string['view_class_total'] = 'Fällige Vokabeln in der ganzen Klasse';
$string['view_export_pdf'] = 'Als PDF exportieren';
$string['view_live_update'] = 'Live updates.';
$string['view_logged_in_streak'] = 'Durchgehend eingeloggt seit {$a} Tagen.';
$string['view_remove_incorrect'] = 'Fehlerhafte entfernen';
$string['view_show_actions'] = 'Aktionen anzeigen';
$string['view_your_vocab_box'] = 'Dein Vokabelkasten';
$string['viewlist_heading'] = 'Vokabelliste {$a}';
$string['vocab'] = 'Vokabel';
$string['vocabcoach:addinstance'] = 'Neue Vokabelcoach-Aktivität hinzufügen';
$string['vocabcoach:delete_lists'] = 'Vokabellisten löschen';
$string['vocabcoach:distribute_lists'] = 'Vokabellisten an den Kurs verteilen';
$string['vocabcoach:mod/vocabcoach:show_class_total_live'] = 'Live updates der fälligen Vokabeln anzeigen.';
$string['vocabcoach:show_class_total'] = 'Gesamtzahl nicht wiederholter Vokabeln in der Klasse anzeigen';
$string['vocabcoach:show_leaderboard'] = 'Leaderboard anzeigen';
$string['vocabcoach:view'] = 'Vokabelcoach-Aktivität anzeigen';
$string['vocabcoachname'] = 'Vokabelkasten';
$string['vocabcoachname_help'] = 'Hilfe';
$string['vocabcoachnameplural'] = 'Vokabelkasten';
$string['vocabcoachsettings'] = 'Einstellungen';
$string['vocablist'] = 'Vokabelliste';
$string['vocabplural'] = 'Vokabeln';
$string['year'] = 'Jahrgangsstufe';
$string['year_short'] = 'Jgst.';
