<?php
/**
 *
 */
require_once($GLOBALS['bwVendorDir'] . DS . 'tfpdf' . DS . 'tfpdf.php');
class BwPdf extends tFPDF
{
	public function __construct()
	{
		parent::__construct();
		$this->configure();
	}

	private function configure()
	{
		$this->AliasNbPages();
		$this->AddPage();
		// We add an unicode font
		$this->AddFont('DejaVu','','DejaVuSans.ttf',true);
		$this->AddFont('DejaVu','B','DejaVuSans-Bold.ttf',true);
		$this->AddFont('DejaVu','BI','DejaVuSans-BoldOblique.ttf',true);
		$this->AddFont('DejaVu','I','DejaVuSans-Oblique.ttf',true);
	}

	public function Footer()
	{
		// Go 1.5cm from the bottom
		$this->SetY(-15);

		// Arial italic 8
		$this->SetFont('Arial','I',8);
		$this->SetTextColor(0, 0, 0);
		// Page number
		$this->Cell(0, 10, sprintf(_('Page %d/{nb}'), $this->PageNo()), 0, 0, 'C');
	}
}