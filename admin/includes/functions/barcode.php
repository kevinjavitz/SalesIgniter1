<?php

// encode itemId to I25 barcode standard. //////////////////////////////////////
function makeBarcode( $itemId, $directOutput = false )
{
	    global $savePath ;
		$new_string = "";
		$lbc = 0;
		$xi = 0;
		$k = 0;
		$bc_string = $itemId;
		$widthSlimBar		= 1;
		$widthFatBar		= 3;
		$BarCodeHeight = 20;
		$bc = array();

	// Barcode conversion /////////////////////////////////////
		$bc[0]  = "00110";         //0 digit
		$bc[1]  = "10001";         //1 digit
		$bc[2]  = "01001";         //2 digit
		$bc[3]  = "11000";         //3 digit
		$bc[4]  = "00101";         //4 digit
		$bc[5]  = "10100";         //5 digit
		$bc[6]  = "01100";         //6 digit
		$bc[7]  = "00011";         //7 digit
		$bc[8]  = "10010";         //8 digit
		$bc[9]  = "01010";         //9 digit
		$bc[10] = "0000";          //pre-amble
		$bc[11] = "100";           //post-amble
	//////////////////////////////////////////////////////////////////

		$bc_string = strtoupper($bc_string);

		$lbc = strlen($bc_string) - 1;

		for( $xi=0; $xi<= $lbc; $xi++ )
		{
			$k = (int) substr($bc_string,$xi,1);
			$new_string = $new_string . $bc[$k];
		}
		$bc_string = $new_string;

		// encode itemId to I25 barcode standard. //////////////////////////////////////
		$i = 0;
		$l = 0;
		$s = "";

		$l = strlen( $bc_string );
		for ( $i = 0; $i< $l; $i += 10 )
		{
			$s = $s . (isset($bc_string[$i]) ? $bc_string[$i] : '')   .  (isset($bc_string[$i+5]) ? $bc_string[$i+5] : '');
			$s = $s . (isset($bc_string[$i+1]) ? $bc_string[$i+1] : '') .  (isset($bc_string[$i+6]) ? $bc_string[$i+6] : '');
			$s = $s . (isset($bc_string[$i+2]) ? $bc_string[$i+2] : '') .  (isset($bc_string[$i+7]) ? $bc_string[$i+7] : '');
			$s = $s . (isset($bc_string[$i+3]) ? $bc_string[$i+3] : '') .  (isset($bc_string[$i+8]) ? $bc_string[$i+8] : '');
			$s = $s . (isset($bc_string[$i+4]) ? $bc_string[$i+4] : '') .  (isset($bc_string[$i+9]) ? $bc_string[$i+9] : '');
		}
		$bc_string = $s;

		///////////////////////////////////////////////////////////////////////////////////////////////
		$bc_string = $bc[10] . $bc_string .$bc[11];  //Adding Start and Stop Pattern

		$lbc = strlen($bc_string) - 1;
		$imWidth = ($l *3) + 20;

		//Header("Content-Type: image/png");
		$im = ImageCreate($imWidth, 50);
		$black = ImageColorAllocate($im, 0, 0, 0);
		$white = ImageColorAllocate($im, 255, 255, 255);
		ImageFill($im, 0, 0, $white);
		$Xposition = 10;

		for( $xi=0; $xi<= $lbc; $xi++ )
		{
			$imgBar = "";
			$imgWid = 0;

			$imgBar = ( $xi % 2 == 0 ) ? $black : $white;
			$imgWid = ( $bc_string[$xi]=="0" ) ? $widthSlimBar : $widthFatBar;

			$end_y = $BarCodeHeight;
			$end_y = $end_y + 5;
			for($qw=0;$qw< $imgWid;$qw++)
			{
				ImageLine($im, ($Xposition), 1, ($Xposition), $end_y, $imgBar);
				$Xposition++;
			}
		}
		$itemId = trim($itemId);
		ImageString($im,1, 20, 40, "*$itemId*", $black);

		$file = $itemId.".png";

		// output to browser
		if ($directOutput === true){
		    @ImagePNG($im);
		}else{
		    @ImagePNG($im, $savePath .$file );
		}

	return  $file;

}
?>