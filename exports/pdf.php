<?php
//============================================================+
// File name   : example_011.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 011 for TCPDF class
//               Colored Table (very simple table)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Colored Table
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
//global $CFG;
require_once('../../../lib/tcpdf/tcpdf.php');

require(__DIR__ . '/../../../config.php');

class _pdf extends TCPDF {
       // Colored table
    public function ColoredTable($header, $data, $uses_third) {
        // Colors, line width and bold font
        $this->SetFillColor(15,108,191);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        if ($uses_third) {
            $w = array(50, 50, 0);
        } else {
            $w = array(80, 80);
        }
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], false, 0, 'L', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->setCellPadding(2);
        // Data
        $fill = true;
        foreach($data as $vocab) {
            $this->Cell($w[0], 5, $vocab->front, false, 0, 'L', $fill);
            $this->Cell($w[1], 5, $vocab->back, false, 0, 'L', $fill);

            if ($uses_third) {
                if ($vocab->third === null) {
                    $vocab->third = '';
                }
                $this->MultiCell($w[2], 9, $vocab->third, 0, 'L', $fill, 1,
                        null, null, true, 0, false, true , 0, 'M');
            } else {
                $this->Ln();
            }

            $fill = !$fill;
        }
        $this->Cell($uses_third ? 0 : array_sum($w), 0, '', 'T');
    }
}

// create new PDF document
$pdf = new _pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Moodle / Vocabcoach');
$pdf->SetTitle('Vokabelliste');
$pdf->SetSubject('Vokabelliste');

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

global $DB, $USER;

if (isset($_GET['listid'])) {
    $list = $DB->get_record('vocabcoach_lists', ['id'=>$_GET['listid']]);
    $header = $list->title.' ('.$list->book.', '.$list->unit.'). Erstellt für '
            .$USER->firstname.' '.$USER->lastname.' am '.date('d.m.Y');
    $pdf->SetHeaderData('', 0, 'Vokabelliste', $header);
} else if (isset($_GET['userid'])) {
    $header = 'Erstellt für '.$USER->firstname.' '.$USER->lastname.' am '.date('d.m.Y');
    $pdf->SetHeaderData('', 0, 'Vokabelliste (Box '.$_GET['stage'].')', $header);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// column titles
$cmid = required_param('cmid', PARAM_INT);
$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$instance_info = $DB->get_record('vocabcoach', ['id'=>$cm->instance], 'thirdactive');
$uses_third = $instance_info->thirdactive == 1;

if ($uses_third) {
    $table_header = array('Englisch', 'Deutsch', '');
} else {
    $table_header = array('Englisch', 'Deutsch');
}

// data loading
if (isset($_GET['listid'])) {
    $check_api = new \mod_vocabcoach\external\check_vocab_api();
    $data = $check_api->get_list_vocabs($_GET['listid']);
}

else if (isset($_GET['userid'])) {
    $check_api = new \mod_vocabcoach\external\check_vocab_api();
    $data = $check_api->get_user_vocabs($_GET['userid'], $_GET['cmid'], $_GET['stage'], true);
}

else {
    $data = null;
}

// print colored table
$pdf->ColoredTable($table_header, $data, $uses_third);

// close and output PDF document
$pdf->Output('vokabelliste.pdf');
