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

class vocablist_pdf extends TCPDF {

    function set_header($id) {
        global $DB, $USER;
        $list = $DB->get_record('mod_vocabcoach_lists', ['id'=>$id]);
        $header = $list->title.' ('.$list->book.', '.$list->unit.'). Erstellt für '
        .$USER->firstname.' '.$USER->lastname.' am '.date('d.m.Y');
        $this->SetHeaderData('', 0, 'Vokabelliste', $header);

        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Moodle / Vocabcoach');
        $this->SetTitle('Vokabelliste');
        $this->SetSubject('Vokabelliste');


// set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    }

   // Colored table
    public function ColoredTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(15,108,191);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(60, 60);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], false, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = true;
        foreach($data as $vocab) {
            $this->Cell($w[0], 6, $vocab->front, false, 0, 'L', $fill);
            $this->Cell($w[1], 6, $vocab->back, false, 0, 'L', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// create new PDF document
$pdf = new vocablist_pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->set_header($_GET['listid']);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// column titles
$header = array('Englisch', 'Deutsch');

// data loading
if (isset($_GET['listid'])) {
    global $DB;
    $listid = $_GET['listid'];

    $query = "SELECT DISTINCT vocab.ID AS dataid, front, back FROM {mod_vocabcoach_vocab} vocab 
            INNER JOIN {mod_vocabcoach_list_contains} list_contains ON  list_contains.vocabID = vocab.ID
            WHERE list_contains.listID = $listid;";
    try {
        $output =  $DB->get_records_sql($query);
        $data = array_values($output);
    } catch(\dml_exception $e) {
        echo "Error: ".$e->getMessage();
    }
}

// print colored table
$pdf->ColoredTable($header, $data);

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('example_011.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+