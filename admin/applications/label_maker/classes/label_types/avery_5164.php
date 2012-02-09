<?php
	class labelMaker_avery_5164 {
		public function __construct(){
			$this->maxCol = 0;
			$this->pageCells = 6;
		}
		
		public function draw($data, $type){
			$this->outputType = $type;
			if ($type == 'pdf'){
				$this->pdf = new TCPDF('P', 'in', array('8.53', '11.03'), true);
				$this->pdf->SetCreator('osCommerce Rental Script');
				$this->pdf->SetAuthor('Kevin Javitz');
				$this->pdf->SetTitle('Rental Product Labels');
				$this->pdf->SetSubject('Rental Product Labels');
				$this->pdf->SetMargins(0.15, 0.49, 0.15);
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
				$leftPadding = 0.15;
				$rightPadding = 0.15;
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
			$labelContent = array();
			if (tep_not_null($labelInfo['products_name'])){
				$labelContent[] = '<b>' . $labelInfo['products_name'] . '</b>';
			}

			$Check = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select * from ' . TABLE_PRODUCTS_CUSTOM_FIELDS_TO_PRODUCTS . ' where product_id = "' . $labelInfo['products_id'] . '"');
			if (sizeof($Check) > 0){
				foreach($Check as $cInfo){
					$Field = Doctrine_Manager::getInstance()
						->getCurrentConnection()
						->fetchAssoc('select * from ' . TABLE_PRODUCTS_CUSTOM_FIELDS . ' f left join ' . TABLE_PRODUCTS_CUSTOM_FIELDS_DESCRIPTION . ' fd using(field_id) where fd.language_id = "' . Session::get('languages_id') . '" and f.field_id = "' . $cInfo['field_id'] . '"');
					if ($Field[0]['show_on_labels'] == '1'){
						$maxChars = ($field[0]['labels_max_chars'] > 0 ? $Field[0]['labels_max_chars'] : 150);
						if (strlen($cInfo['value']) > $maxChars){
							$labelContent[] = '<b>' . $Field[0]['field_name'] . ':</b> ' . substr($cInfo['value'], 0, $maxChars) . '...';
						}else{
							$labelContent[] = '<b>' . $Field[0]['field_name'] . ':</b> ' . $cInfo['value'];
						}
					}
				}
			}

			if (tep_not_null($labelInfo['products_description'])){
				$labelContent[] = '<b>Description:</b> ' . (strlen($labelInfo['products_description']) > 350 ? substr($labelInfo['products_description'], 0, 350) . '...' : $labelInfo['products_description']);
			}

			if (tep_not_null($labelInfo['barcode']) && $hideBarcode === false){
				$labelContent[] = '<b>Barcode:</b> ' . $labelInfo['barcode'];
				// define barcode style
				$style = array(
					'position' => '',
					'align' => 'L',
					'stretch' => false,
					'fitwidth' => false,
					'cellfitalign' => '',
					'border' => false,
					'hpadding' => '0',
					'vpadding' => '0',
					'fgcolor' => array(0,0,0),
					'bgcolor' => false, //array(255,255,255),
					'text' => true,
					'font' => 'helvetica',
					'fontsize' => 8,
					'stretchtext' => 4
				);

				$styleQR = array(
					'border' => 0,
					'vpadding' => '0',
					'hpadding' => '0',
					'fgcolor' => array(0,0,0),
					'bgcolor' => false, //array(255,255,255)
					'module_width' => 1, // width of a single module in points
					'module_height' => 1 // height of a single module in points
				);

				if (tep_not_null($labelInfo['barcode'])){
					switch(sysConfig::get('BARCODE_TYPE')){
						case 'Code 25':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'S25', '', '', '' ,1, 0.4, $style, 'N'));
							$labelContent[] = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
							break;
						case 'Code 25 Interleaved':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'I25', '', '', '' ,1, 0.4, $style, 'N'));
							$labelContent[] = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
							break;
						case 'Code 39':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'C39', '', '', '',1, 0.4, $style, 'N'));
							$labelContent[] = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
							break;
						case 'Code 39 Extended':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'C39E', '', '', '',1, 0.4, $style, 'N'));
							$labelContent[] = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
							break;
						case 'Code 128B':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'C128B', '', '', '',1, 0.4, $style, 'N'));
							$labelContent[] = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
							break;
						case 'QR':
							$params = $this->pdf->serializeTCPDFtagParameters(array($labelInfo['barcode'], 'QRCODE,H', '', '', 1, 1, $styleQR, 'N'));
							$labelContent[] = '<tcpdf method="write2DBarcode" params="'.$params.'" />';
							break;
					}
					//$labelContent[] =  '<img src="' . tep_href_link('showBarcode_' . $labelInfo['barcode_id'] . '.png', Session::getSessionName() . '=' . Session::getSessionId()) . '">';
				}else{
					$labelContent[] = 'Image Not Available';
				}
			}

			if ($this->outputType == 'pdf'){
				$this->pdf->MultiCell(4, 3.34, implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true);
				if ($newLine == 0){
					$this->pdf->Cell(.2, 3.34, '');
				}
			}else{
				$this->htmlOutput .= '<div style="width:4in;height:3.34in;position:relative;float:left;">' . "\n" .
				'<div style="padding:0.075in;">' . "\n" .
				implode('<br>', $labelContent) . "\n" .
				'</div>' . "\n" .
				'</div>' . "\n";
				if ($newLine == 1){
					$this->htmlOutput .= '<div style="clear:both;"></div>' . "\n";
				}else{
					$this->htmlOutput .= '<div style="width:0.2in;height:3.34in;position:relative;float:left;"></div>' . "\n";
				}
			}
		}
	}
?>