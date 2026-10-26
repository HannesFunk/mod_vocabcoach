<?php
// This file is part of Moodle - http://moodle.org
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_vocabcoach\table;

use core\context;
use core\output\html_writer;
use core_table\dynamic;
use core_table\local\filter\filterset;
use core_table\sql_table;
use moodle_url;

class lists_table extends sql_table implements dynamic {
    protected int $cmid;
    protected context $context;
    protected bool $caneditall;
    protected bool $candistribute;

    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        $columns = ['title', 'year', 'book', 'unit', 'number', 'creator', 'actions'];
        $headers = [
            get_string('name', 'mod_vocabcoach'),
            get_string('year_short', 'mod_vocabcoach'),
            get_string('book', 'mod_vocabcoach'),
            get_string('unit', 'mod_vocabcoach'),
            get_string('count', 'mod_vocabcoach'),
            get_string('creator', 'mod_vocabcoach'),
            get_string('actions', 'mod_vocabcoach'),
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);

        $this->define_header_column('title');
        $this->sortable(true, 'title');
        $this->no_sorting('actions');
        $this->no_sorting('creator');
        $this->collapsible(false);
        $this->set_default_per_page(20);
        $this->set_attribute('id', 'vocabcoach-lists');
    }

    public function get_context(): context {
        return $this->context;
    }

    public function has_capability(): bool {
        return has_capability('mod/vocabcoach:view', $this->context);
    }

    public function guess_base_url(): void {
        $this->baseurl = new \moodle_url('/mod/vocabcoach/lists.php', ['id' => $this->cmid]);
    }

    public function set_filterset(filterset $filterset): void {
        global $USER;

        $this->cmid = $filterset->get_filter('cmid')->current();
        $this->context = \core\context\module::instance($this->cmid);

        $this->caneditall = has_capability('mod/vocabcoach:delete_lists', $this->context);
        $this->candistribute = has_capability('mod/vocabcoach:distribute_lists', $this->context);


        $namesql = \core_user\fields::for_name()->get_sql('u');

        $fields = 'l.id, l.title, l.year, l.book, l.unit, l.private, l.createdby,
                   (SELECT COUNT(1) FROM {vocabcoach_list_vocabs} lv WHERE lv.listid = l.id) AS number'
            . $namesql->selects;

        $from = '{vocabcoach_lists} l
                 LEFT JOIN {user} u ON u.id = l.createdby' . $namesql->joins;

        $where = 'l.cmid = :cmid AND (l.private = 0 OR l.createdby = :userid)';
        $params = ['cmid' => $this->cmid, 'userid' => $USER->id] + $namesql->params;

        if ($filterset->has_filter('ownlists') && $filterset->get_filter('ownlists')->current()) {
            $where .= ' AND l.createdby = :ownerid';
            $params['ownerid'] = $USER->id;
        }

        $this->set_sql($fields, $from, $where, $params);

        parent::set_filterset($filterset);
    }

    public function col_title($row) {
        global $OUTPUT;

        $url = new \moodle_url('/mod/vocabcoach/viewlist.php', ['id' => $this->cmid, 'listid' => $row->id]);
        $link = html_writer::link($url, format_string($row->title, true, ['context' => $this->context]));

        if (!$row->private) {
            return $link;
        }

        $icon = $OUTPUT->pix_icon('t/locked', get_string('list_private', 'mod_vocabcoach'), 'core', ['class' => 'icon me-1']);
        return $icon . $link;
    }

    public function col_creator($row) {
        return fullname($row);
    }

    public function col_actions($row) {
        global $USER, $OUTPUT;
        $editable = $this->caneditall || (int) $row->createdby === (int)$USER->id;

        $cmidlistparams = ['id' => $this->cmid, 'listid' => $row->id];
        $menucontext = [
            'id' => $row->id,
            'editable' => $editable,
            'distributable' => $this->candistribute,
            'url_pdf_export' => new moodle_url('/mod/vocabcoach/export.php',  [...$cmidlistparams, 'dataformat' => 'pdf']),
            'url_csv_export' => new moodle_url('/mod/vocabcoach/export.php',  [...$cmidlistparams, 'dataformat' => 'csv']),
            'url_check' => new moodle_url('/mod/vocabcoach/check.php', $cmidlistparams),
            'url_edit' => new moodle_url('/mod/vocabcoach/check.php', $cmidlistparams),
        ];

        return $OUTPUT->render_from_template(
            'mod_vocabcoach/lists_action_menu',
            $menucontext
        );
    }
}