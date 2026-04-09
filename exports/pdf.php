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
 * Create PDF files.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

// defined('MOODLE_INTERNAL') || die();

require_once('../../../lib/tcpdf/tcpdf.php');
require(__DIR__ . '/../../../config.php');
require_login();

/**
 * _pdf class. Creates a PDF
 */
class _pdf extends TCPDF {
    /**
     * Include a coloured table
     * @param array $headers
     * @param array $data
     * @return void
     */
    public function colored_table(array $headers, array $data) : void {
        // Colors, line width and bold font.
        $this->SetFillColor(15, 108, 191);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');

        $w = [80, 80];

        for ($i = 0; $i < count($headers); ++$i) {
            $this->Cell($w[$i], 7, $headers[$i], false, 0, 'L', 1);
        }
        $this->Ln();
        // Color and font restoration.
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor();
        $this->SetFont('');
        $this->setCellPadding(2);

        $fill = true;
        foreach ($data as $vocab) {
            // Calculate row height based on content.
            $frontheight = $this->getStringHeight($w[0], $vocab->front);
            $backheight = $this->getStringHeight($w[1], $vocab->back);
            $rowheight = max($frontheight, $backheight, 0, 5);

            // Check if we need a page break before this row.
            $pagebreak = $this->GetY() + $rowheight > ($this->getPageHeight() - $this->getBreakMargin());
            if ($pagebreak) {
                $this->AddPage();
            }

            $starty = $this->GetY();
            $startx = $this->GetX();

            // Front column.
            $this->MultiCell($w[0], $rowheight, $vocab->front, 0, 'L', $fill, 0,
                    $startx, $starty, true, 0, false, true, $rowheight, 'M');

            // Back column.
            $this->MultiCell($w[1], $rowheight, $vocab->back, 0, 'L', $fill, 0,
                    $startx + $w[0], $starty, true, 0, false, true, $rowheight, 'M');

            $this->SetY($starty + $rowheight);

            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf = new _pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Moodle / Vocabcoach');
$pdf->SetTitle('Vokabelliste');
$pdf->SetSubject('Vokabelliste');

$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

global $DB, $USER;

if (isset($_GET['listid'])) {
    $list = $DB->get_record('vocabcoach_lists', ['id' => $_GET['listid']]);
    $header = $list->title.' ('.$list->book.', '.$list->unit.'). Erstellt für '
            .$USER->firstname.' '.$USER->lastname.' am '.date('d.m.Y');
    $pdf->SetHeaderData('', 0, 'Vokabelliste', $header);
} else if (isset($_GET['userid'])) {
    $header = 'Erstellt für '.$USER->firstname.' '.$USER->lastname.' am '.date('d.m.Y');
    $pdf->SetHeaderData('', 0, 'Vokabelliste (Box '.$_GET['stage'].')', $header);
}

$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();

$cmid = required_param('cmid', PARAM_INT);
$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$instanceinfo = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*');
$desc_front = $instanceinfo->desc_front;
$desc_back = $instanceinfo->desc_back;

$tableheaders = [$desc_front, $desc_back];

if (isset($_GET['listid'])) {
    $checkapi = new \mod_vocabcoach\external\vocab_api();
    $data = $checkapi->get_list_vocabs($_GET['listid']);
} else if (isset($_GET['userid'])) {
    $checkapi = new \mod_vocabcoach\external\vocab_api();
    $data = $checkapi->get_user_vocabs($_GET['userid'], $_GET['cmid'], $_GET['stage'], true);
} else {
    $data = null;
}

$pdf->colored_table($tableheaders, $data);
$pdf->Output('vokabelliste.pdf');
