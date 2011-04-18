<?php
	class labelMaker_avery_5160 {
		public function __construct(){
			$this->maxCol = 1;
			$this->pageCells = 30;
		}
		
		public function draw($data, $type){
			$this->outputType = $type;
			if ($type == 'pdf'){
				$this->pdf = new TCPDF('P', 'in', array('8.53', '11.03'), true);
				$this->pdf->SetCreator('osCommerce Rental Script');
				$this->pdf->SetAuthor('Kevin Javitz');
				$this->pdf->SetTitle('Rental Product Labels');
				$this->pdf->SetSubject('Rental Product Labels');
				$this->pdf->SetMargins(0.18, 0.49, 0.2);
				$this->pdf->SetCellPadding(.075);
				$this->pdf->setPrintHeader(false);
				$this->pdf->setPrintFooter(false);
				$this->pdf->SetAutoPageBreak(TRUE, .51);
				$this->pdf->setImageScale(1);
				$this->pdf->AliasNbPages();
				$this->pdf->AddPage();
				$this->pdf->SetFont("helvetica", "", 11);
			}else{
				$topPadding = 0.49;
				$bottomPadding = 0.51;
				$leftPadding = 0.18;
				$rightPadding = 0.18;
				$this->htmlOutput = '<html>' .
				'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>' .
				'<body topmargin="0" leftmargin="0" style="font-family:halvetica;font-size:11pt;">' .
				'<div style="padding-top:' . $topPadding . 'in;padding-left:' . $leftPadding . 'in;padding-right:' . $rightPadding . 'in;padding-bottom:' . $bottomPadding . 'in;width:8.53in;height:11.03in;">' . "\n";
			}
			
			$col = 0;
			if (isset($this->labelLocation) && tep_not_null($this->labelLocation)){
				$n = $this->pageCells;
			}else{
				$n = sizeof($data);
			}
			for($i=0; $i<$n; $i++){
				if ($col > $this->maxCol){
					$col = 0;
					$newLine = 1;
				}else{
					$col++;
					$newLine = 0;
				}
				$lInfo = $data[$i];
				if (isset($this->labelLocation) && tep_not_null($this->labelLocation)){
					if ($i != $this->labelLocation){
						$lInfo = array();
					}else{
						$lInfo = $data[0];
					}
				}
				$this->buildLabel($lInfo, $newLine);
			}

			if ($type == 'pdf'){
				$this->pdf->lastPage();
				$this->pdf->Output("example_017.pdf", "I");
			}else{
				$this->htmlOutput .= '</div>' . "\n" .
				'</body>' . "\n" .
				'</html>' . "\n";
				return $this->htmlOutput;
			}
		}
		
		private function buildLabel($labelInfo, $newLine, $hideBarcode = false){
			$labelContent = array(
				substr($labelInfo['products_name'], 0, 13) . ' - ' . $labelInfo['barcode']
			);

			if ($labelInfo['customers_address'] !== false){
				$labelContent[] = $labelInfo['customers_address']['name'];
				$labelContent[] = $labelInfo['customers_address']['street_address'];
				$labelContent[] = $labelInfo['customers_address']['city'] . ', ' . $labelInfo['customers_address']['state'] . ' ' . $labelInfo['customers_address']['postcode'];
			}

			if ($this->outputType == 'pdf'){
				$this->pdf->MultiCell(2.63, 1, implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true);
				if ($newLine == 0){
					$this->pdf->Cell(0.13, 1, '');
				}
			}else{
				$this->htmlOutput .= '<div style="width:2.63in;height:1in;position:relative;float:left;">' . "\n" .
					'<div style="padding:0.075in;">' . "\n" .
						implode('<br>', $labelContent) . "\n" .
					'</div>' . "\n" .
				'</div>' . "\n";
				if ($newLine == 1){
					$this->htmlOutput .= '<div style="clear:both;"></div>' . "\n";
				}else{
					$this->htmlOutput .= '<div style="width:0.13in;height:1in;position:relative;float:left;"></div>' . "\n";
				}
			}
		}
	}
?>