<?php
if (! function_exists('StripArray')) {
	function StripArray($Array, $FieldName = array()) {
		$ArrayResult = array();
		foreach($Array as $Key => $Element) {
			if (in_array($Key, $FieldName) && in_array($Element, array('0', '0000-00-00', '0000-00-00 00:00:00'))) {
				$ArrayResult[$Key] = null;
			} else {
				$ArrayResult[$Key] = stripslashes($Element);
			}
		}
		return $ArrayResult;
	}
}

if (! function_exists('EscapeString')) {
	function EscapeString($Array) {
		$ArrayResult = array();
		foreach($Array as $Key => $Element) {
			$ArrayResult[$Key] = mysql_real_escape_string($Element);
		}
		return $ArrayResult;
	}
}

if (! function_exists('GetOption')) {
	function GetOption($OptAll, $ArrayOption, $Selected) {
		$temp = ($Selected == 0) ? 'selected' : '';
		$Content = ($OptAll) ? '<option value="0" '.$temp.'>All<option>' : '';
		foreach ($ArrayOption as $Value => $Title) {
			$temp = ($Selected == $Value) ? 'selected' : '';
			$Content .= '<option value="'.$Value.'" '.$temp.'>'.$Title.'</option>';
		}
		return $Content;
	}
}

if (! function_exists('ShowOption')) {
	// echo ShowOption(array( 'Array' => $array_kategori, 'ArrayID' => 'id', 'ArrayTitle' => 'nama' ));
    function ShowOption($Param) {
		$Param['OptAll'] = (isset($Param['OptAll'])) ? $Param['OptAll'] : false;
		$Param['ArrayID'] = (isset($Param['ArrayID'])) ? $Param['ArrayID'] : 'id';
		$Param['WithEmptySelect'] = (isset($Param['WithEmptySelect'])) ? $Param['WithEmptySelect'] : 1;
		
		$Param['ArrayTitle'] = (isset($Param['ArrayTitle'])) ? $Param['ArrayTitle'] : 'title';
		$Param['Selected'] = (isset($Param['Selected'])) ? $Param['Selected'] : '';
		
		if ($Param['WithEmptySelect'] == 1) {
			$Content = '<option value="">-</option>';
		} else {
			$Content = '';
		}
		
		$Selected = '';
		if ($Param['OptAll']) {
			$Selected = ($Param['Selected'] == '0') ? 'selected' : '';
			$Content .= '<option value="0" ' . $Selected . '>All</option>';
		}
		
		foreach ($Param['Array'] as $Array) {
			$Selected = ($Param['Selected'] == $Array[$Param['ArrayID']]) ? 'selected' : '';
			$Content .= '<option value="'.$Array[$Param['ArrayID']].'" '.$Selected.'>'.$Array[$Param['ArrayTitle']].'</option>';
		}
		
        return $Content;
    }
}

if (! function_exists('ArrayToJSON')) {
	function ArrayToJSON($Array) {
		$Result = '';
		foreach ($Array as $Key => $Element) {
			$Element = mysql_real_escape_string($Element);
			$Result .= (empty($Result)) ? "'$Key': '$Element'" : ",'$Key':'$Element'";
		}
		$Result = '{' . $Result . '}';
		return $Result;
	}
}

if (! function_exists('ConvertToUnixTime')) {
	function ConvertToUnixTime($String) {
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/i', $String, $Match);
		$UnixTime = mktime ($Match[4], $Match[5], $Match[6], $Match[2], $Match[3], $Match[1]);
		$UnixTime = 'new Date('.$UnixTime.')';
		return $UnixTime;
	}
}

if (! function_exists('ConvertDateToString')) {
	function ConvertDateToString($String) {
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/i', $String, $Match);
		return date("d F Y", mktime (0, 0, 0, $Match[2], $Match[3], $Match[1]));
	}
}

if (! function_exists('ConvertDateToQuery')) {
	function ConvertDateToQuery($String) {
		preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/i', $String, $Match);
		if (isset($Match[0]) && !empty($Match[0])) {
			$Result = $Match[0];
			return $Result;
		}
		
		$Array = explode('/', $String);
		$Result = $Array[2] . '-' . $Array[0] . '-' . $Array[1];
		
		return $Result;
	}
}

if (! function_exists('my_number_format')) {
	function my_number_format($Value) {
		return number_format($Value, 0, '.', ',');
	}
}

if (! function_exists('invoice_number_format')) {
	function invoice_number_format($Value) {
		return number_format($Value, 2, '.', ',');
	}
}

if (! function_exists('get_file_name')) {
	function get_file_name($value) {
		$array_value = explode('.', $value);
		if (count($array_value) > 1) {
			unset($array_value[count($array_value) - 1]);
		}
		
		// get result
		$result = implode('-', $array_value);
		
		return $result;
	}
}

if (! function_exists('Upload')) {
	function Upload($InputName, $PathDir = 'User', $Param = array()) {
		$Param['max_size'] = (isset($Param['max_size'])) ? $Param['max_size'] : 1000000;
		$Param['AllowedExtention'] = (isset($Param['AllowedExtention'])) ? $Param['AllowedExtention'] : array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf');
		
		$ArrayResult = array('Result' => '0', 'FileDirName' => '');
		if (isset($_FILES[$InputName]) && is_array($_FILES[$InputName]) && is_array($_FILES[$InputName]['name'])) {
			$FileCount = count($_FILES[$InputName]['name']);
			for ($i = 0; $i < $FileCount; $i++) {
				if ($_FILES[$InputName]['error'][$i] == '0') {
					$Extention = F_GetExtention($_FILES[$InputName]['name'][$i]);
					$FileName = date("Ymd_His").'_'.rand(1000,9999).'.'.$Extention;
					
					@mkdir(IMGS_DIR.'/'.date("Y"));
					@mkdir(IMGS_DIR.'/'.date("Y").'/'.date("m"));
					@mkdir(IMGS_DIR.'/'.date("Y").'/'.date("m").'/'.date("d"));
					$FileLocation = IMGS_DIR.'/'.date("Y").'/'.date("m").'/'.date("d").'/'.basename($FileName);
					$FileRequest = date("Y").'/'.date("m").'/'.date("d").'/'.basename($FileName);
					
					if (move_uploaded_file($_FILES[$InputName]['tmp_name'][$i], $FileLocation)) {
						$ParamImage = array(
							'FileSource' => $FileLocation,
							'Width' => 456,
							'Height' => 320,
						);
						F_Resize($ParamImage);
						$ArrayResult['Result'] = '1';
						$ArrayResult['ArrayImage'][] = $FileRequest;
					}
				}
			}
		}
		else if (isset($_FILES[$InputName]) && !empty($_FILES[$InputName]) && !empty($_FILES[$InputName]['name'])) {
			$Extention = GetExtention($_FILES[$InputName]['name']);
			$ArrayResult['Message'] = 'There was an error uploading the file, please try again!';
			$ArrayResult['FileDirName'] = '';
			
			if ($_FILES[$InputName]['size'] > $Param['max_size']) {
				$ArrayResult['Message'] = 'Ukuran file yang diupload terlalu besar, ukuran file maks '.($Param['max_size'] / 1000).' KB';
			} else if (! in_array($Extention, $Param['AllowedExtention'])) {
				$ArrayResult['Message'] = 'Hanya file bertipe '.implode(', ', $Param['AllowedExtention']).' yang dapat di upload.';
			} else if ($_FILES[$InputName]['error'] == '0') {
				$DirYear = date("Y");
				$DirMonth = date("m");
				$DirDay = date("d");
				
				@mkdir($PathDir.'/'.$DirYear);
				@mkdir($PathDir.'/'.$DirYear.'/'.$DirMonth);
				@mkdir($PathDir.'/'.$DirYear.'/'.$DirMonth.'/'.$DirDay);
				
				// generate name
				if ($Param['file_rename']) {
					$FileName = date("Ymd_His").'_'.rand(1000,9999).'.'.$Extention;
				} else {
					$FileName = get_file_name($_FILES[$InputName]['name']).'.'.$Extention;
					$FileName = preg_replace('/ +/i', '_', $FileName);
					
					// check existing file
					$FileLocation = $PathDir.'/'.$DirYear.'/'.$DirMonth.'/'.$DirDay.'/'.basename($FileName);
					if (file_exists($FileLocation)) {
						$FileName = get_file_name($_FILES[$InputName]['name']).'_'.rand(1000,9999).'.'.$Extention;
						$FileName = preg_replace('/ +/i', '_', $FileName);
					}
				}
				
				$FileDirectory = $PathDir;
				$FileLocation = $FileDirectory.'/'.$DirYear.'/'.$DirMonth.'/'.$DirDay.'/'.basename($FileName);
				if (file_exists($FileLocation)) {
					unlink($FileLocation);
				}
				
				if (move_uploaded_file($_FILES[$InputName]['tmp_name'], $FileLocation)) {
					$ArrayResult['Result'] = '1';
					$ArrayResult['Message'] = 'Upload file berhasil.';
					$ArrayResult['file_only'] = $FileName;
					$ArrayResult['FileDirName'] = $DirYear.'/'.$DirMonth.'/'.$DirDay.'/'.$FileName;
				}
			}
		}
		
		return $ArrayResult;
	}
}

if (! function_exists('GetExtention')) {
	function GetExtention($FileName) {
		$FileName = strtolower(trim($FileName));
		if (empty($FileName)) {
			return '';
		}
		
		// string ext
		$array_string = explode('.', $FileName);
		$string_ext = $array_string[count($array_string) - 1];
		
		// get ext
		preg_match('/^([a-z]+)/i', $string_ext, $match);
		$ext = (!empty($match[1])) ? $match[1] : '';
		
		return $ext;
	}
}

if (! function_exists('Write')) {
	function Write($FileLocation, $FileContent) {
		$Handle = @fopen($FileLocation, 'wb+');
		if ($Handle) {
			fputs($Handle, $FileContent);
			fclose($Handle);
		}
	}
}

if (! function_exists('GetStringFilter')) {
	// $Param = array('filter' => '[{"type":"numeric","comparison":"eq","value":"' . $company_id . '","field":"company_id"},{"type":"numeric","comparison":"eq","value":1,"field":"menu_company_active"}]');
	
	/*
		// overwrite field name
		$param['field_replace']['id'] = 'Nota.id';
		$param['field_replace']['nota_currency_total'] = 'Nota.nota_total';
		$param['field_replace']['status_nota_name'] = 'StatusNota.name';
	/*	*/
	
	function GetStringFilter($Param, $ReplaceField = array(), $option = array()) {
		// default
		$StringFilter = '';
		$option['quote'] = (isset($option['quote'])) ? $option['quote'] : false;
		
		if (isset($Param['sSearch'])) {
			$field_replace = (isset($Param['field_replace'])) ? $Param['field_replace'] : array();
			
			$StringFilter = "";
			if ( $Param['sSearch'] != "" ) {
				$StringFilter = "AND (";
				$aWords = array( $Param['sSearch'] );
				for ($j = 0; $j < count($aWords); $j++) {
					if ( $aWords[$j] != "" ) {
						$StringFilter .= "(";
						for ($i = 0; $i < count($ReplaceField); $i++) {
							$field_name = (isset($field_replace[$ReplaceField[$i]])) ? $field_replace[$ReplaceField[$i]] : $ReplaceField[$i];
							if (empty($field_name)) {
								continue;
							}
							
							if ($option['quote']) {
								$StringFilter .= '`'.$field_name."` LIKE '%".mysql_real_escape_string( $aWords[$j] )."%' OR ";
							} else {
								$StringFilter .= $field_name." LIKE '%".mysql_real_escape_string( $aWords[$j] )."%' OR ";
							}
						}
						$StringFilter = substr_replace( $StringFilter, "", -3 );
						$StringFilter .= ") AND ";
					}
				}
				
				$StringFilter = substr_replace( $StringFilter, "", -5 );
				$StringFilter .= ")";
			}
			
			/* Individual column filtering */
			$sColumnWhere = "";
			for ( $i=0 ; $i<count($ReplaceField) ; $i++ ) {  
				if ( isset($Param['sSearch_'.$i]) && !empty($Param['sSearch_'.$i]) ) {
					$aWords = preg_split('/\s+/', $Param['sSearch_'.$i]);
					$sColumnWhere .= "(";
					for ( $j=0 ; $j<count($aWords) ; $j++ )
					{
						if ( $aWords[$j] != "" )
						{
							$sColumnWhere .= $ReplaceField[$i]." LIKE '%".mysql_real_escape_string( $aWords[$j] )."%' OR ";
						}
					}
					$sColumnWhere = substr_replace( $sColumnWhere, "", -3 );
					$sColumnWhere .= ") AND ";
				}
			}
			if ( $sColumnWhere != "" ) {
				$sColumnWhere = substr_replace( $sColumnWhere, "", -5 );
				$StringFilter .= " AND ".$sColumnWhere;
			}
		}
		else if (isset($Param['filter']) && !empty($Param['filter'])) {
			$Filter = json_decode($Param['filter']);
			
			foreach ($Filter as $Array) {
				if (isset($ReplaceField[$Array->field])) {
					$Field = $ReplaceField[$Array->field];
				} else if (isset($Param['field_replace']) && isset($Param['field_replace'][$Array->field])) {
					$Field = $Param['field_replace'][$Array->field];
				} else {
					$Field = $Array->field;
				}
				
				if (isset($Array->field) && isset($Array->type)) {
					if ($Array->type == 'numeric') {
						if ($Array->comparison == 'eq') {
							$StringFilter .= "AND " . $Field." = '".$Array->value."' ";
						} else if ($Array->comparison == 'lt') {
							$StringFilter .= "AND " . $Field." <= '".$Array->value."' ";
						} else if ($Array->comparison == 'gt') {
							$StringFilter .= "AND " . $Field." >= '".$Array->value."' ";
						} else if ($Array->comparison == 'not') {
							$StringFilter .= "AND " . $Field." != '".$Array->value."' ";
						} else if ($Array->comparison == 'eq_can_empty' && !empty($Array->value)) {
							$StringFilter .= "AND " . $Field." = '".$Array->value."' ";
						} else if ($Array->comparison == 'in') {
							$StringFilter .= "AND " . $Field." IN (".$Array->value.") ";
						}
					} else if ($Array->type == 'date') {
						if ($Array->comparison == 'eq') {
							$StringFilter .= "AND " . $Field." = '".ConvertDateToQuery($Array->value)."' ";
							} else if ($Array->comparison == 'lt') {
							$StringFilter .= "AND " . $Field." <= '".ConvertDateToQuery($Array->value)."' ";
							} else if ($Array->comparison == 'gt') {
							$StringFilter .= "AND " . $Field." >= '".ConvertDateToQuery($Array->value)."' ";
						}
					} else if ($Array->type == 'list') {
						$Array->field = $Field;
						$StringFilter .= GetStringFromList($Array);
					} else if ($Array->type == 'custom') {
						$StringFilter .= "AND " . $Array->field . ' ';
					} else {
						$StringFilter .= "AND " . $Field." LIKE '%".$Array->value."%' ";
					}
				}
			}
		}
		
		return $StringFilter;
	}
}

if (! function_exists('GetStringFromList')) {
	function GetStringFromList($Param) {
		$ArrayFieldYesNo = array('supplier_active', 'agent_active', 'car_active', 'customer_active', 'driver_active', 'guide_active');
		$ArrayEmptyOrExist = array('driver_photo');
		
		$StringResult = '';
		if (in_array($Param->field, $ArrayFieldYesNo)) {
			foreach ($Param->value as $Value) {
				if ($Value == 'Yes') {
					$StringResult .= (empty($StringResult)) ? "'1'" : ", '1'";
				} else if ($Value == 'No') {
					$StringResult .= (empty($StringResult)) ? "'0'" : ", '0'";
				}
			}
			$StringResult = (empty($StringResult)) ? '' : "AND " . $Param->field . " IN (" . $StringResult . ") ";
		} else if (in_array($Param->field, $ArrayEmptyOrExist)) {
			foreach ($Param->value as $Value) {
				if ($Value == 'Yes') {
					$StringResult .= (empty($StringResult)) ? $Param->field . " != '' " : "OR " . $Param->field . " != '' ";
				} else if ($Value == 'No') {
					$StringResult .= (empty($StringResult)) ? $Param->field . "= '' " : "OR " . $Param->field . " = '' ";
				}
			}
			$StringResult = (empty($StringResult)) ? '' : "AND (" . $StringResult . ") ";
		} else {
			echo 'Please create new filter spesification';
			exit;
		}
		
		return $StringResult;
	}
}

if (! function_exists('GenerateInsertQuery')) {
	function GenerateInsertQuery($ArrayField, $ArrayParam, $Table, $Param = array()) {
        $Param['AllowSymbol'] = (isset($Param['AllowSymbol'])) ? $Param['AllowSymbol'] : 0;
        
		$StringField = $StringValue = '';
		foreach ($ArrayField as $Column) {
			$StringField .= (empty($StringField)) ? '`'.$Column.'`' : ', `' . $Column.'`';
			
			$Value = (isset($ArrayParam[$Column])) ? $ArrayParam[$Column] : '';
			$Value = mysql_real_escape_string($Value);
            
            if ($Param['AllowSymbol'] == 0) {
                $Value = preg_replace('/[^\x20-\x7E|\x0A]/i', '', $Value);
            }
            
			$StringValue .= (empty($StringValue)) ? "'" . $Value . "'" : ", '" . $Value . "'";
		}
		$Query = "INSERT INTO `$Table` ($StringField) VALUES ($StringValue)";
		
		return $Query;
	}
}

if (! function_exists('GenerateUpdateQuery')) {
	function GenerateUpdateQuery($ArrayField, $ArrayParam, $Table, $Param = array()) {
        $Param['AllowSymbol'] = (isset($Param['AllowSymbol'])) ? $Param['AllowSymbol'] : 0;
        
		$StringQuery = '';
		foreach ($ArrayField as $Key => $Column) {
			if ($Key != 0 && isset($ArrayParam[$Column])) {
                $Value = $ArrayParam[$Column];
                if ($Param['AllowSymbol'] == 0) {
                    $Value = preg_replace('/[^\x20-\x7E|\x0A]/i', '', $Value);
                }
                
				$StringQuery .= (empty($StringQuery)) ? '' : ', ';
				$StringQuery .= "`$Column` = '" . mysql_real_escape_string($Value) . "'";
			}
		}
		$Query = "UPDATE `$Table` SET $StringQuery WHERE " . $ArrayField[0] . " = '" . $ArrayParam[$ArrayField[0]] . "'";
		
		return $Query;
	}
}

if (! function_exists('GetNextAutoIncrement')) {
	function GetNextAutoIncrement($Table) {
		$NextAutoIncrement = 1;
		
		$SelectQuery = "SHOW TABLE STATUS LIKE '$Table'";
		$ResultQuery = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($ResultQuery)) {
			$NextAutoIncrement = $Row['Auto_increment'];
		}
		
		return $NextAutoIncrement;
	}
}

if (! function_exists('GetStringMonth')) {
	function GetStringMonth($Param) {
		if (empty($Param['value'])) {
			return  '';
		}
		
		$Param['Year'] = (isset($Param['Year'])) ? $Param['Year'] : date("Y");
		
		$StringMonth = "AND MONTH(" . $Param['field'] . ") = '" . $Param['value'] . "' AND YEAR(" . $Param['field'] . ") = '" . $Param['Year'] . "'";
		return $StringMonth;
	}
}

if (! function_exists('GetStringBettween')) {
	function GetStringBettween($Param, $Field = array()) {
		$StringResult = '';
		
		if (isset($Param['StartDate']) && !empty($Param['StartDate']) && isset($Param['EndDate']) && !empty($Param['EndDate'])) {
			foreach ($Field as $Value) {
				$StringResult .= (empty($StringResult)) ? '' : 'OR ';
				$StringResult .= "$Value between '".$Param['StartDate']."' and '".$Param['EndDate']."' ";
			}
			
			$StringResult = "AND (" . $StringResult . ") ";
		}
		
		return $StringResult;
	}
}

if (! function_exists('GetStringSorting')) {
	// sorting sample
	// $Param = array('sort' => '[{"property":"tanggal","direction":"DESC"}]');
	
	// overwrite field name
	// $param['field_replace']['nama'] = 'Seeker.first_name';
	function GetStringSorting($param, $Field = array(), $string_default = '', $option = array()) {
		// default
		$Result = '';
		$option['quote'] = (isset($option['quote'])) ? $option['quote'] : false;
		
		if (isset($param['iSortCol_0'])) {
			for ($i = 0; $i < intval($param['iSortingCols']); $i++) {
				if ( $param[ 'bSortable_'.intval($param['iSortCol_'.$i]) ] == "true" ) {
					$field_name = $Field[ intval( $param['iSortCol_'.$i] ) ];
					$field_value = $param['sSortDir_'.$i];
					if (isset($param['field_replace']) && isset($param['field_replace'][$field_name])) {
						$field_name = $param['field_replace'][$field_name];
					}
					
					if ($option['quote']) {
						$Result .= '`'.$field_name."` ".mysql_real_escape_string( $field_value ) .", ";
					} else {
						$Result .= $field_name." ".mysql_real_escape_string( $field_value ) .", ";
					}
				}
			}
			
			$Result = substr_replace( $Result, "", -2 );
		}
		else if (isset($param['sort'])) {
			$ArrayString = json_decode($param['sort']);
			if (is_array($ArrayString)) {
				foreach ($ArrayString as $Array) {
					$FieldName = (isset($Field[$Array->property])) ? $Field[$Array->property] : $Array->property;
					$Query = $FieldName . ' ' . $Array->direction;
					
					$Result .= (empty($Result)) ? '' : ', ';
					$Result .= $Query;
				}
			} else if (is_object($ArrayString)) {
				if (!empty($ArrayString->is_custom)) {
					$Result = $ArrayString->query;
				}
			}
		}
		else {
			$Result = $string_default;
		}
		
		return $Result;
	}
}

if (! function_exists('json_response')) {
	function json_response($json, $status=200) {
		if ($status != 200) header('HTTP/1.1 ' . $status);
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode( $json );
		exit;
	}
}

if (! function_exists('GetArrayFromFileUpload')) {
	function GetArrayFromFileUpload($FileUploadPath) {
		$ArrayFile = file($FileUploadPath);
		
		$ArrayRaw = array();
		foreach ($ArrayFile as $StringTemp) {
			$StringCheck = preg_replace('/\,/i', '', trim($StringTemp));
			if (empty($StringCheck)) {
				continue;
			}
			
			$ArrayTemp = explode(',', $StringTemp);
			foreach ($ArrayTemp as $Key => $Value) {
				$Value = preg_replace('/^\"|\"$/i', '', trim($Value));
				$ArrayTemp[$Key] = $Value;
			}
			
			$ArrayRaw[] = $ArrayTemp;
		}
		return $ArrayRaw;
	}
}

if (! function_exists('EncriptPassword')) {
	function EncriptPassword($Value) {
		return md5(sha1(SHA_SECRET . ':' . $Value));
	}
}

if (! function_exists('GetResource')) {
	function GetResource($Source) {
		$Buffer = '';
		$Handle = fopen($Source, "rb+");
		if ($Handle) {
			while (!feof($Handle)) {
				$Buffer .= fgets($Handle, 8192);
			}
			fclose($Handle);
		}
		return $Buffer;
	}
}

if (! function_exists('ImageResize')) {
	function ImageResize($ImageSource, $ImageOutput, $MinWidth, $MinHeight, $IsCrop = 0) {
		$info = @getimagesize($ImageSource);
		if (!empty($info)) {
			$Image = imagecreatefromstring(GetResource($ImageSource));
			$ImageWidth = imagesx($Image);
			$ImageHeight = imagesy($Image);
			
			// Enlarge for Small Image
			if ($ImageWidth < $MinWidth || $ImageHeight < $MinHeight) {
				$FactorWidth = $FactorHeight = 0;
				if ($ImageWidth < $MinWidth) {
					$FactorWidth = $MinWidth / $ImageWidth;
				}
				if ($ImageHeight < $MinHeight) {
					$FactorHeight = $MinHeight / $ImageHeight;
				}
				
				$FactorMultiply = ($FactorWidth > $FactorHeight) ? $FactorWidth : $FactorHeight;
				$ResultWidth = intval($FactorMultiply * $ImageWidth);
				$ResultHeight = intval($FactorMultiply * $ImageHeight);
			
			// Resize for Large Image
			} else {
				$FactorWidth = $ImageWidth / $MinWidth;
				$FactorHeight = $ImageHeight / $MinHeight;
				
				$FactorMultiply = ($FactorWidth < $FactorHeight) ? $FactorWidth : $FactorHeight;
				$ResultWidth = intval($ImageWidth / $FactorMultiply);
				$ResultHeight = intval($ImageHeight / $FactorMultiply);
			}
			
			$Result = imagecreatetruecolor($ResultWidth, $ResultHeight);
			imagecopyresampled($Result, $Image, 0, 0, 0, 0, $ResultWidth, $ResultHeight, $ImageWidth, $ImageHeight);
			imagejpeg($Result, $ImageOutput);
			imagedestroy($Image);
			imagedestroy($Result);
			
			if ($IsCrop == 1) {
				ImageCrop($ImageOutput, $ImageOutput, $MinWidth, $MinHeight);
			}
		}
	}
}

if (! function_exists('ImageCrop')) {
	function ImageCrop($source, $output, $out_x, $out_y) {
		$info = @getimagesize($source);
		if (!empty($info)){
			$img = imagecreatefromstring(GetResource($source));
			$img_x = imagesx($img);
			$img_y = imagesy($img);
			$img_top = 0;
			$img_left = 0;
			
			if ($img_x <= $out_x && $img_y <= $out_y){
				copy($source, $output);
				return;
			}
			
			$diff = round($img_y/2) - round($out_y/2);
			$img_top = 0;
			$img_y = $out_y;
			
			
			
			$out = imagecreatetruecolor($out_x, $out_y);
			imagecopyresampled($out, $img, 0, 0, $img_left, $img_top, $out_x, $out_y, $img_x, $img_y);
			imagejpeg($out, $output);
			imagedestroy($img);
			imagedestroy($out);
		}
	}
}

if (! function_exists('GetLengthChar')) {
	function GetLengthChar($String, $LengthMax, $Follower = '') {
		$String = strip_tags($String);
		if (strlen($String) > $LengthMax) {
			$String = substr($String, 0, $LengthMax);
			$Stringpos = strrpos($String, ' ');
			if (false !== $Stringpos) $String = substr($String, 0, $Stringpos);
			if (!empty($Follower)) {
				$String .= $Follower;
			}
		}
		return $String;
	}
}

if (! function_exists('save_tinymce')) {
	function save_tinymce($value) {
		$result = $value;
		$result = str_replace("\"", "'", $result);
		$result = htmlentities($result, ENT_QUOTES);

		return $result;
	}
}

if (! function_exists('GetStringLimit')) {
	function GetStringLimit($Param) {
		$StringLimit = "0, 25";
		
		if ( isset( $Param['iDisplayStart'] ) && $Param['iDisplayLength'] != '-1' ) {
			$StringLimit = mysql_real_escape_string( $Param['iDisplayStart'] ).", ". mysql_real_escape_string( $Param['iDisplayLength'] );
		} else if (isset($Param['start']) || isset($Param['limit'])) {
			$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
			$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
			$StringLimit = "$PageOffset, $PageLimit";
		}
		
		return $StringLimit;
	}
}

if (! function_exists('dt_view_set')) {
	function dt_view_set($row, $param) {
		$param['is_edit'] = (isset($param['is_edit'])) ? $param['is_edit'] : 0;
		$param['is_delete'] = (isset($param['is_delete'])) ? $param['is_delete'] : 0;
		$param['is_detail'] = (isset($param['is_detail'])) ? $param['is_detail'] : 0;
		
		$temp_column = '';
		if ($param['is_edit'] == 1) {
			$temp_column .= '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span> ';
			$temp_column .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span> ';
		}
		if (isset($param['is_edit_only']) && $param['is_edit_only'] == 1) {
			$temp_column .= '<span class="cursor-font-awesome icon-pencil btn-edit" title="Edit"></span> ';
		}
		if ($param['is_detail'] == 1) {
			$temp_column .= '<span class="cursor-font-awesome icon-reorder btn-detail" title="Detail"></span> ';
		}
		if ($param['is_delete'] == 1) {
			$temp_column .= '<span class="cursor-font-awesome icon-trash btn-delete" title="Delete"></span> ';
		}
		if (!empty($param['is_custom'])) {
			$temp_column = $param['is_custom'];
		}
		
		// populate required data
		$record = array();
		foreach ($param['column'] as $key) {
			$record[] = (isset($row[$key])) ? $row[$key] : '';
		}
		
		// remove unused data
		// $param['clean_column'] = 1;
		if (!empty($param['clean_column'])) {
			foreach ($row as $key => $value) {
				if (!in_array($key, $param['column'])) {
					unset($row[$key]);
				}
			}
		}
		
		if (!empty($temp_column)) {
			$temp_column .= '<span class="hide">'.json_encode($row).'</span>';
			$record[] = $temp_column;
		}
		
		return $record;
	}
}

if (! function_exists('get_page')) {
	function get_page() {
		preg_match('/page_(\d+)/i', $_SERVER['REQUEST_URI'], $match);
		$page = (empty($match[1])) ? 1 : $match[1];
		$page = (!empty($_POST['page_no'])) ? $_POST['page_no'] : $page;
		
		return $page;
	}
}

if (! function_exists('sent_mail')) {
	function sent_mail($param) {
		if (isset($param['headers'])) {
			$headers  = $param['headers'];
		} else {
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <no-reply@'.SITE_DOMAIN.'>' . "\r\n";
		}
		
		if (SENT_MAIL) {
			if (!empty($param['to'])) {
				@mail($param['to'], $param['title'], $param['message'], $headers);
			}
		}
	}
}

if (! function_exists('sent_grid')) {
	/*
		// sample param
		$param['from'] = 'hello@sendgrid.com';
		$param['fromname'] = 'Hello Send Grid';
		$param['to'] = array(
			array( 'name' => 'Herry Yahoo', 'email' => 'her0satr@yahoo.com' ),
			array( 'name' => 'Herry Gmail', 'email' => 'her0satr@gmail.com' )
		);
		$param['sub'] = array(									// replace -name- with 'Name 1' and 'Name 2' for 2 email
			'-name-' => array( 'Name 1', 'Name 2' )
		);
		$param['subject'] = 'Timer - Multiple Email - '.time();
		$param['html'] = 'testing body - 12345';
		$param['text'] = 'testing body - 12345';
	/*	*/
	function sent_grid($param) {
		$ci = get_instance();
		
		// send grid
		$sendgrid = $ci->config_model->get_row(array( 'config_key' => 'sendgrid' ));
		
		// url
		$url_request = 'https://api.sendgrid.com/api/mail.send.json';
		
		// smtp api
		$json_string = array(
			'to' => array( ),
			'category' => 'Jafaria Application'
		);
		foreach ($param['to'] as $row) {
			if (empty($row['email'])) {
				continue;
			}
			
			if (empty($row['name'])) {
				$string_email = $row['email'];
			} else {
				$string_email = $row['name'].' <'.$row['email'].'>';
			}
			$json_string['to'][] = $string_email;
		}
		if (isset($param['category'])) {
			if (!empty($param['category']['title'])) {
				$json_string['category'] = $param['category']['title'];
			}
			/*
			Disable asm group id
			if (!empty($param['category']['asm_group_id'])) {
				$json_string['asm_group_id'] = $param['category']['asm_group_id'];
			}
			/*	*/
		}
		if (isset($param['sub'])) {
			$json_string['sub'] = $param['sub'];
		}
		
		// send grid param
		if (SENT_MAIL) {
			$params = array(
				'api_user'  => $sendgrid['user'],
				'api_key'   => $sendgrid['passwd'],
				'x-smtpapi' => json_encode($json_string),
				'to'        => @$param['to'][0]['email'],
				'subject'   => $param['subject'],
				'html'      => $param['html'],
				'text'      => $param['text'],
				'from'      => $param['from'],
				'fromname'      => $param['fromname']
			);
			
			$session = curl_init($url_request);
			curl_setopt ($session, CURLOPT_POST, true);
			curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($session);
			curl_close($session);
			
			$result = false;
		
			$result_curl = json_decode($response);
			if (isset($result_curl->message) && $result_curl->message == 'success') {
				$result = true;
			}
		} else {
			$result = true;
		}
		
		return $result;
	}
}

if (! function_exists('set_flash_message')) {
	function set_flash_message($value) {
		$_SESSION['flash_message'] = $value;
	}
}

if (! function_exists('get_flash_message')) {
	function get_flash_message() {
		$value = '';
		if (isset($_SESSION['flash_message'])) {
			$value = $_SESSION['flash_message'];
		}
		
		$_SESSION['flash_message'] = '';
		unset($_SESSION['flash_message']);
		
		return $value;
	}
}

if (! function_exists('get_name')) {
	function get_name($value) {
		$result = preg_replace('/[^0-9a-z]+/i', '-', strtolower($value));
		$result = preg_replace('/^-/i', '', $result);
		$result = preg_replace('/-$/i', '', $result);
		
		return $result;
	}
}

if (! function_exists('object_to_array')) {
	function object_to_array($data) {
		if (is_array($data) || is_object($data)) {
			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = object_to_array($value);
			}
			return $result;
		}
		
		return $data;
	}
}

if (! function_exists('get_array_mail_info')) {
	function get_array_mail_info($param = array()) {
		$ci = get_instance();
		$add_teacher_classroom = false;
		$user = $ci->user_model->get_session();
		
		if ($user['user_type_id'] == USER_TYPE_ADMINISTRATOR) {
			$add_teacher_classroom = true;
			$array = array(
				array( 'value' => 'Principal', 'title' => 'Principal' ),
				array( 'value' => 'All Teachers', 'title' => 'All Teachers' ),
				array( 'value' => 'Specific Teachers', 'title' => 'Specific Teachers' ),
				array( 'value' => 'All Parents', 'title' => 'All Parents' ),
				array( 'value' => 'Specific Parents', 'title' => 'Specific Parents' )
			);
		} else if ($user['user_type_id'] == USER_TYPE_PRINCIPAL) {
			$add_teacher_classroom = true;
			$array = array(
				array( 'value' => 'Administrator', 'title' => 'Administrator' ),
				array( 'value' => 'All Teachers', 'title' => 'All Teachers' ),
				array( 'value' => 'Specific Teachers', 'title' => 'Specific Teachers' ),
				array( 'value' => 'All Parents', 'title' => 'All Parents' ),
				array( 'value' => 'Specific Parents', 'title' => 'Specific Parents' )
			);
		} else if ($user['user_type_id'] == USER_TYPE_TEACHER) {
			$add_teacher_classroom = true;
			$array = array(
				array( 'value' => 'Principal', 'title' => 'Principal' ),
				array( 'value' => 'Administrator', 'title' => 'Administrator' ),
				array( 'value' => 'All Teachers', 'title' => 'All Teachers' ),
				array( 'value' => 'Specific Teachers', 'title' => 'Specific Teachers' ),
				array( 'value' => 'Specific Parents', 'title' => 'Specific Parents' )
			);
			
			// get additional quran
			$param_quran = array(
				'user_id' => $user['user_id'],
				'class_level_id' => 0,
				'sort' => '[{"property":"quran_level.no_order","direction":"ASC"}]'
			);
			$array_quran = $ci->teacher_class_model->get_array($param_quran);
			
			// add to raw option
			$array_quran_level_id = array();
			foreach ($array_quran as $row) {
				if (! in_array($row['quran_level_id'], $array_quran_level_id)) {
					$array_quran_level_id[] = $row['quran_level_id'];
					$array[] = array( 'value' => 'All Parents of Quran ('.$row['quran_level_id'].')', 'title' => 'All Parents of Quran '.$row['quran_level_name'] );
				}
			}
			
			// get additional class
			$param_class = array(
				'user_id' => $user['user_id'],
				'quran_level_id' => 0,
				'sort' => '[{"property":"class_level.no_order","direction":"ASC"}]'
			);
			$array_class = $ci->teacher_class_model->get_array($param_class);
			
			// add to raw option
			$array_class_level_id = array();
			foreach ($array_class as $row) {
				if (! in_array($row['class_level_id'], $array_class_level_id)) {
					$array_class_level_id[] = $row['class_level_id'];
					$array[] = array( 'value' => 'All Parents of Class ('.$row['class_level_id'].')', 'title' => 'All Parents of '.$row['class_level_name'] );
				}
			}
		} else {
			// student
			$student = $ci->student_model->get_by_id(array( 's_id' => $user['student_id'] ));
			
			// default option
			$array = array(
				array( 'value' => 'Principal', 'title' => 'Principal' ),
				array( 'value' => 'Administrator', 'title' => 'Administrator' )
			);
			
			// get additional teacher
			$param_teacher = array(
				'user_type_id' => USER_TYPE_TEACHER,
				'with_subject' => 1,
				'class_level_id' => $student['class_level_id'],
				'quran_level_id' => $student['quran_level_id']
			);
			$array_teacher = $ci->user_model->get_array($param_teacher);
			foreach ($array_teacher as $row) {
				$array[] = array( 'value' => 'Custom Teacher ('.$row['user_id'].')', 'title' => $row['user_display'] );
			}
		}
		
		// add teacher in classroom
		if ($add_teacher_classroom) {
			$array[] = array( 'value' => 'Teacher Classroom', 'title' => 'All Teachers of a certain subject' );
		}
		
		return $array;
	}
}

if (! function_exists('get_array_class_type')) {
	function get_array_class_type($param = array()) {
		$ci = get_instance();
		$array = $ci->class_type_model->get_array(array( 'sort' => '[{"property":"id","direction":"ASC"}]' ));
		
		/*
		$array = array(
			array( 'id' => '1', 'title' => 'Quran' ),
			array( 'id' => '2', 'title' => 'Fiqh' ),
			array( 'id' => '3', 'title' => 'Akhlaq' ),
			array( 'id' => '4', 'title' => 'Tareekh' ),
			array( 'id' => '5', 'title' => 'Aqaid' )
		);
		/*	*/
		
		return $array;
	}
}

if (! function_exists('get_score_grade')) {
	function get_score_grade($value) {
		if ($value >= 90) {
			$result = 'A';
		} else if ($value >= 80) {
			$result = 'B';
		} else if ($value >= 70) {
			$result = 'C';
		} else if ($value >= 60) {
			$result = 'S';
		} else {
			$result = 'N';
		}
		
		return $result;
	}
}
