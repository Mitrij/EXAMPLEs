<?php
if(!defined('ETC_REPORTS'))
{
	exit;
}

session_start();

require_once (INCLUDES_PATH . "/" . "Classes/PHPExcel/IOFactory.php");
require_once (INCLUDES_PATH . "/" . "Classes/meekrodb.2.3.class.php");

/*
 * database functions ----------------------------
 */
DBinit();


function DBinit()
{
	global $settingsMySqlUsername, $settingsMySqlPassword, $settingsMySqlDatabase, $settingsMySqlHost;
	DB::$user = $settingsMySqlUsername;
	DB::$password = $settingsMySqlPassword;
	DB::$dbName = $settingsMySqlDatabase;
	DB::$host = $settingsMySqlHost;
	
	DB::$error_handler = 'DBerrorHandler';
}


function DBgetDatesArr()
{
	return DB::query("SELECT * FROM date_types WHERE 1=1");
}

function DBgetIdByDateId($dateId)
{
	$retVal = 0;
	$arr = DB::query("SELECT id FROM date_types WHERE date_id = %i", $dateId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}

	return $retVal;
}

function DBgetDateIdById($id)
{
	$retVal = 0;
	$arr = DB::query("SELECT date_id FROM date_types WHERE id = %i", $id);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["date_id"];
	}

	return $retVal;
}

function DBgetTimesArr()
{
	return DB::query("SELECT * FROM time_types WHERE 1=1");
}

function DBgetIdByTimeId($timeId)
{
	$retVal = 0;
	$arr = DB::query("SELECT id FROM time_types WHERE time_id = %i", $timeId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}

	return $retVal;
}

function DBgetTimeIdById($id)
{
	$retVal = 0;
	$arr = DB::query("SELECT time_id FROM time_types WHERE id = %i", $id);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["time_id"];
	}

	return $retVal;
}

//==========================================================================

function DBgetInvestorsArr($full = 1, $orderByCustomId = false)
{
	$orderBy = "";
	if($orderByCustomId)
	{
		$orderBy = " order by investor_id";
	}
	
	if($full) // full info
	{
		return DB::query("SELECT * FROM investors WHERE 1=1" . $orderBy);
	}
	else
	{
		return DB::query("SELECT id, investor_id, investor_name, date_added FROM investors WHERE 1=1" . $orderBy);
	}
}

function DBgetInvestorsIdsArr()
{
	return DB::query("SELECT id FROM investors");
}

function DBgetInvestorById($id)
{
	return DB::query("SELECT * FROM investors WHERE id = %i", $id);
}

function DBgetIdByInvestorId($investorId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM investors WHERE investor_id = %i", $investorId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}

	return $retVal;
}

function DBcheckInvestorByInvestorId($invetorId, $excludeId = 0, $id = 0)
{
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM investors WHERE investor_id = %i and id <> %i", $invetorId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM investors WHERE investor_id = %i", $invetorId);
	}

	return $count;
}


function DBcheckInvestorByInvestorName($invetorName)
{
	$count = DB::queryFirstField("SELECT count(*) FROM investors WHERE investor_name = %s", $invetorName);

	return $count;
}


function DBupdateInvestorById($id, $invetorId, $invetorName, $invetorNotes = "", $status = "", $dateTimeSheet = null, $dateTimePhase = "", $dateSearchRange_01 = "", $dateSearchRange_02 = "", $dateSearchRange_03 = "", $dateSearchRange_04 = "", $dateLocation_01 = "", $dateLocation_02 = "", $timeLocation_01 = "", $timeLocation_02 = "", $dateFormatId = -1, $timeFormatId = -1)
{
	DB::query
			(
				"UPDATE investors SET %? WHERE id = %i"
				,array
					(
						"investor_id" => $invetorId
						,"investor_name" => $invetorName
						,"notes" => $invetorNotes
						,"status" => $status
						,"date_time_sheet" => $dateTimeSheet
						,"date_time_phase" => $dateTimePhase
						,"date_time_r_start_row" => $dateSearchRange_02
						,"date_time_r_start_column" => $dateSearchRange_01
						,"date_time_r_end_row" => $dateSearchRange_04
						,"date_time_r_end_column" => $dateSearchRange_03
						,"date_location_row" => $dateLocation_02
						,"date_location_column" => $dateLocation_01
						,"time_location_row" => $timeLocation_02
						,"time_location_column" => $timeLocation_01
						,"date_format_id" => $dateFormatId
						,"time_format_id" => $timeFormatId
					)
				, $id
			);
					
	return DB::affectedRows();
}


function DBaddInvestor($invetorId, $invetorName, $invetorNotes = "", $status = "", $dateTimeSheet = null, $dateTimePhase = "", $dateSearchRange_01 = "", $dateSearchRange_02 = "", $dateSearchRange_03 = "", $dateSearchRange_04 = "", $dateLocation_01 = "", $dateLocation_02 = "", $timeLocation_01 = "", $timeLocation_02 = "", $dateFormatId = -1, $timeFormatId = -1)
{
	$date = date("Y-m-d");
	DB::insert
			(
				'investors',
				array
					(
						'id' => 0
						,"investor_id" => $invetorId
						,"investor_name" => $invetorName
						,"notes" => $invetorNotes
						,"status" => $status
						,"date_added" => $date
						,"date_time_sheet" => $dateTimeSheet
						,"date_time_phase" => $dateTimePhase
						,"date_time_r_start_row" => $dateSearchRange_02
						,"date_time_r_start_column" => $dateSearchRange_01
						,"date_time_r_end_row" => $dateSearchRange_04
						,"date_time_r_end_column" => $dateSearchRange_03
						,"date_location_row" => $dateLocation_02
						,"date_location_column" => $dateLocation_01
						,"time_location_row" => $timeLocation_02
						,"time_location_column" => $timeLocation_01
						,"date_format_id" => $dateFormatId
						,"time_format_id" => $timeFormatId
					)
			);
					
	return DB::insertId();
}

function DBdeleteInvestor($id)
{
	$retVal = false;
	DB::delete('investors', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
} 


//==========================================================================

function DBgetProductsArr($full = 1)
{
	if($full) // full info
	{
		return DB::query("SELECT * FROM products WHERE 1=1");
	}
	else
	{
		return DB::query("SELECT id, product_id, product_name, date_added FROM products WHERE 1=1");
	}
}

function DBgetProductsIdsArr()
{
	return DB::query("SELECT id FROM products");
}

function DBgetProductById($id)
{
	return DB::query("SELECT * FROM products WHERE id = %i", $id);
}

function DBgetIdByProductId($productId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM products WHERE product_id = %i", $productId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}
	return $retVal;
}

function DBcheckProductByProductId($productId, $excludeId = 0, $id = 0)
{
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM products WHERE product_id = %i and id <> %i", $productId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM products WHERE product_id = %i", $productId);
	}

	return $count;
}


function DBcheckProductByProductName($productName)
{
	$count = DB::queryFirstField("SELECT count(*) FROM products WHERE product_name = %s", $productName);

	return $count;
}


function DBupdateProductById($id, $productId, $productName, $productNotes = "")
{
	DB::query
			(
				"UPDATE products SET %? WHERE id = %i"
				,array
				(
					"product_id" => $productId
					,"product_name" => $productName
					,"notes" => $productNotes
				)
				, $id
			);
					
	return DB::affectedRows();
}


function DBaddProduct($productId, $productName, $productNotes = "")
{
	$date = date("Y-m-d");
	DB::insert
			(
				'products',
				array
					(
						'id' => 0
						,"product_id" => $productId
						,"product_name" => $productName
						,"notes" => $productNotes
						,"date_added" => $date
					)
			);
					
	return DB::insertId();
}

function DBdeleteProduct($id)
{
	$retVal = false;
	DB::delete('products', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
} 
//==========================================================================


function DBaddProductRateData($investorId, $productId, $lockDayId, $date, $time, $rate, $value)
{
	DB::insert
			(
				'products_rates_data',
				array
					(
						'id' => 0
						,"investor_id" => $investorId
						,"product_id" => $productId
						,"lock_day_id" => $lockDayId
						,"date" => $date
						,"time" => $time
						,"rate" => $rate
						,"value" => $value
					)
			);
					
	return DB::insertId();
}

function DBaddProductRateDataArr($prdArr)
{
	DB::insert
			(
				'products_rates_data'
				, $prdArr
			);
					
	return DB::insertId();
}

function DBreplaceProductRateDataArr($prdArr)
{
	DB::replace
			(
				'products_rates_data'
				, $prdArr
			);

	return DB::affectedRows();
}

function DBdeleteProductRateData($investorId, $productId, $lockDayId, $date, $time, $rate)
{
	DB::delete
			(
				'products_rates_data'
				, "investor_id=%i and product_id=%i and lock_day_id=%i and date=%s and time=%s and rate=%d"
					, $investorId
					, $productId
					, $lockDayId
					, $date
					, $time
					, $rate
			);
	
	return DB::affectedRows();
}

function DBdeleteProductRateDataByInvestorId($investorId)
{
	DB::delete
			(
				'products_rates_data'
				, "investor_id=%i"
					, $investorId
			);
	
	return DB::affectedRows();
}

function DBdeleteProductRateDataByProductId($productId)
{
	DB::delete
			(
				'products_rates_data'
				, "product_id=%i"
					, $productId
			);
	
	return DB::affectedRows();
}

function DBdeleteProductRateDataByLockdayId($lockDayId)
{
	DB::delete
			(
				'products_rates_data'
				, "lock_day_id=%i"
					, $lockDayId
			);
	
	return DB::affectedRows();
}

function DBgetProductRateData01($investorIds, $productIds, $lockdayIds, $date = "", $time = "")
{
	$retVal = false;
	$query = "SELECT * FROM products_rates_data WHERE ";

	if(empty($investorIds))
	{
		$investorIds = array(-1);
		$query .= "investor_id not in %li ";
	}
	else
	{
		$query .= "investor_id in %li ";
	}
	if(empty($productIds))
	{
		$productIds = array(-1);
		$query .= "and product_id not in %li ";
	}
	else
	{
		$query .= "and product_id in %li ";
	}
	if(empty($lockdayIds))
	{
		$lockdayIds = array(-1);
		$query .= "and lock_day_id not in %li ";
	}
	else
	{
		$query .= "and lock_day_id in %li ";
	}
	
	if(empty($date))
	{
		$query .= "/* and date=%s*/ ";
	}
	else
	{
		$query .= "and date=%s ";
	}
	
	if(empty($time))
	{
		$query .= "/* and time=%s*/ ";
	}
	else
	{
		$query .= "and time=%s ";
	}
	
	$arr = DB::query
				(
					$query
					, $investorIds
					, $productIds
					, $lockdayIds
					, $date
					, $time
				);

	if(count($arr) > 0)
	{
		$retVal = $arr;
	}
	return $retVal;
}

function DBgetLastTimeForProductRateData($investorId, $date, $maxTime = "")
{
	$retVal = false;
	$query = "SELECT time FROM products_rates_data WHERE investor_id=%i AND date=%s";
	if(!empty($maxTime))
	{
		$query .= " AND time <= %s";
	}
	$query .= " ORDER BY time DESC LIMIT 0,1";

	$arr = DB::query
					(
						$query
						, $investorId
						, $date
						, $maxTime
					);
					
	if(count($arr) > 0 && isset($arr[0]["time"]))
	{
		$retVal = $arr[0]["time"];
	}
	return $retVal;
}

//==========================================================================

function DBgetLockdaysArr()
{
	return DB::query("SELECT * FROM lock_days WHERE 1=1");
}

function DBgetLockdaysIdsArr()
{
	return DB::query("SELECT id FROM lock_days");
}

function DBgetLockdayById($id)
{
	return DB::query("SELECT * FROM lock_days WHERE id = %i", $id);
}

function DBcheckLockdayByLockdayId($lockdayId, $excludeId = 0, $id = 0)
{
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM lock_days WHERE lock_day_id = %i and id <> %i", $lockdayId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM lock_days WHERE lock_day_id = %i", $lockdayId);
	}

	return $count;
}

function DBupdateLockdayById($id, $lockdayId, $lockdayName)
{
	DB::query
			(
				"UPDATE lock_days SET %? WHERE id = %i"
				,array
				(
					"lock_day_id" => $lockdayId
					,"lock_day" => $lockdayName
				)
				, $id
			);
					
	return DB::affectedRows();
}


function DBaddLockday($lockdayId, $lockdayName)
{
	DB::insert
			(
				'lock_days',
				array
					(
						'id' => 0
						,"lock_day_id" => $lockdayId
						,"lock_day" => $lockdayName
					)
			);
					
	return DB::insertId();
}

function DBgetIdByLockdayId($lockdayId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM lock_days WHERE lock_day_id = %i", $lockdayId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}
	return $retVal;
}

function DBdeleteLockday($id)
{
	$retVal = false;
	DB::delete('lock_days', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
} 
//==============================================================================

function DBgetDateformatsArr()
{
	return DB::query("SELECT * FROM date_types WHERE 1=1");
}

function DBgetDateformatById($id)
{
	return DB::query("SELECT * FROM date_types WHERE id = %i", $id);
}

function DBcheckDateformatByDateformatId($dateformatId, $excludeId = 0, $id = 0)
{
print $dateformatId . "/" . $excludeId . "/" . $id;
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM date_types WHERE date_id = %i and id <> %i", $dateformatId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM date_types WHERE date_id = %i", $dateformatId);
	}

	return $count;
}

function DBupdateDateformatById($id, $dateformatId, $dateformat, $dateformatExample)
{
	DB::query
			(
				"UPDATE date_types SET %? WHERE id = %i"
				,array
				(
					"date_id" => $dateformatId
					,"date_format" => $dateformat
					,"example" => $dateformatExample
				)
				, $id
			);
					
	return DB::affectedRows();
}


function DBaddDateformat($dateformatId, $dateformat, $dateformatExample)
{
	DB::insert
			(
				'date_types',
				array
					(
						'id' => 0
						,"date_id" => $dateformatId
						,"date_format" => $dateformat
						,"example" => $dateformatExample
					)
			);
					
	return DB::insertId();
}

function DBgetIdByDateformatId($dateformatId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM date_types WHERE date_id = %i", $dateformatId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}
	return $retVal;
}

function DBdeleteDateformat($id)
{
	$retVal = false;
	DB::delete('date_types', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
} 

//==============================================================================


function DBgetTimeformatsArr()
{
	return DB::query("SELECT * FROM time_types WHERE 1=1");
}

function DBgetTimeformatById($id)
{
	return DB::query("SELECT * FROM time_types WHERE id = %i", $id);
}

function DBcheckTimeformatByTimeformatId($timeformatId, $excludeId = 0, $id = 0)
{
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM time_types WHERE time_id = %i and id <> %i", $timeformatId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM time_types WHERE time_id = %i", $timeformatId);
	}

	return $count;
}

function DBupdateTimeformatById($id, $timeformatId, $timeformat, $timeformatExample)
{
	DB::query
			(
				"UPDATE time_types SET %? WHERE id = %i"
				,array
				(
					"time_id" => $timeformatId
					,"time_format" => $timeformat
					,"example" => $timeformatExample
				)
				, $id
			);
					
	return DB::affectedRows();
}


function DBaddTimeformat($timeformatId, $timeformat, $timeformatExample)
{
	DB::insert
			(
				'time_types',
				array
					(
						'id' => 0
						,"time_id" => $timeformatId
						,"time_format" => $timeformat
						,"example" => $timeformatExample
					)
			);
					
	return DB::insertId();
}

function DBgetIdByTimeformatId($timeformatId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM time_types WHERE time_id = %i", $timeformatId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}
	return $retVal;
}

function DBdeleteTimeformat($id)
{
	$retVal = false;
	DB::delete('time_types', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
} 

//==============================================================================

function DBcheckSettingByInvestorAndProductId($invetorId, $productId, $excludeId = 0, $id = 0)
{
	if($excludeId)
	{
		$count = DB::queryFirstField("SELECT count(*) FROM irf_settings WHERE investor_id = %i and product_id = %i and id <> %i", $invetorId, $productId, $id);
	}
	else
	{
		$count = DB::queryFirstField("SELECT count(*) FROM irf_settings WHERE investor_id = %i and product_id = %i", $invetorId,  $productId);
	}

	return $count;
}

function DBgetSettingByInvestorAndProductId($investorId, $productId)
{
		return DB::query("SELECT * FROM irf_settings WHERE investor_id = %i and product_id = %i", $investorId,  $productId);
}

function DBgetSettingsByInvestorId($investorId)
{
		return DB::query("SELECT * FROM irf_settings WHERE investor_id = %i", $investorId);
}

function DBgetSettingIdByInvestorAndProductId($invetorId, $productId)
{
	$retVal = false;
	$arr = DB::query("SELECT id FROM irf_settings WHERE investor_id = %i and product_id = %i", $invetorId,  $productId);
	
	if(count($arr) > 0)
	{
		$retVal = $arr[0]["id"];
	}
	return $retVal;
}

function DBaddSetting($investorId, $productId, $useitEnabled, $searchPhase, $searchRangeStartColumn, $searchRangeStartRow, $searchRangeEndColumn, $searchRangeEndRow, $searchSheet, $startLocationColumn, $startLocationRow, $readingMap, $calMap, $lockDays, $formulaA, $formulaB, $sheetStyle = 1)
{
	$date = date("Y-m-d");
	DB::insert
			(
				'irf_settings',
				array
					(
						'id' => 0
						,'investor_id' => $investorId
						,'product_id' => $productId
						,'useit' => $useitEnabled
						,"sheet_style" => $sheetStyle
						,'search_phase' => $searchPhase
						,'search_r_start_row' => $searchRangeStartRow
						,'search_r_start_column' => $searchRangeStartColumn
						,'search_r_end_row' => $searchRangeEndRow
						,'search_r_end_column' => $searchRangeEndColumn
						,'search_sheet' => $searchSheet
						,'start_location_row' => $startLocationRow
						,'start_location_column' => $startLocationColumn
						,'reading_map' => $readingMap
						,'cal_map' => $calMap
						,'lock_days' => $lockDays
						,'formula_a' => $formulaA
						,'formula_b' => $formulaB
						,"date_added" => $date
					)
			);
					
	return DB::insertId();
}

function DBgetSettingById($id)
{
	return DB::query("SELECT * FROM irf_settings WHERE id = %i", $id);
}

function DBupdateSettingById($id, $investorId, $productId, $useitEnabled, $searchPhase, $searchRangeStartColumn, $searchRangeStartRow, $searchRangeEndColumn, $searchRangeEndRow, $searchSheet, $startLocationColumn, $startLocationRow, $readingMap, $calMap, $lockDays, $formulaA, $formulaB, $sheetStyle = 1)
{
	
	DB::query
			(
				"UPDATE irf_settings SET %? WHERE id = %i"
				,array
				(
					'investor_id' => $investorId
					,'product_id' => $productId
					,'useit' => $useitEnabled
					,"sheet_style" => $sheetStyle
					,'search_phase' => $searchPhase
					,'search_r_start_row' => $searchRangeStartRow
					,'search_r_start_column' => $searchRangeStartColumn
					,'search_r_end_row' => $searchRangeEndRow
					,'search_r_end_column' => $searchRangeEndColumn
					,'search_sheet' => $searchSheet
					,'start_location_row' => $startLocationRow
					,'start_location_column' => $startLocationColumn
					,'reading_map' => $readingMap
					,'cal_map' => $calMap
					,'lock_days' => $lockDays
					,'formula_a' => $formulaA
					,'formula_b' => $formulaB
				)
				, $id
			);
			
	return DB::affectedRows();
}

function DBdeleteSetting($id)
{
	$retVal = false;
	DB::delete('irf_settings', "id=%i", $id);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}

	return $retVal;
}

function DBdeleteSettingByInvestorId($investorId)
{
	$retVal = false;
	DB::delete('irf_settings', "investor_id=%i", $investorId);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}

	return $retVal;
}


function DBdeleteSettingByProductId($productId)
{
	$retVal = false;
	DB::delete('irf_settings', "product_id=%i", $productId);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}

	return $retVal;
}


function DBdeleteSettingByLockdayId($lockdayId)
{
	$retVal = true;
	$query = "";
	/*
	DB::delete('irf_settings', "product_id=%i", $productId);
	
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	*/
	$query = "SELECT * from irf_settings WHERE lock_days LIKE '%i;%' OR lock_days LIKE '%;%i' OR lock_days LIKE '%;%i;%'";
	//SELECT * from irf_settings WHERE lock_days LIKE '3;%' OR lock_days LIKE '%;3' OR lock_days LIKE '%;3;%'
	$rArr = DB::query($query, $lockdayId, $lockdayId, $lockdayId);
	if(0 < count($rArr))
	{
		foreach($rArr as $k => $v)
		{
			$nLD = $v["lock_days"];
			$nLD = preg_replace('/^' . $lockdayId . ';/', '', $nLD);
			$nLD = preg_replace('/;' . $lockdayId . '$/', '', $nLD);
			$nLD = preg_replace('/;' . $lockdayId . ';/', ';', $nLD);
//print $v["lock_days"] . " : " . $nLD ."<br>\n";
			
			if(!DBupdateSettingById(
									$v["id"]
									, $v["investor_id"]
									, $v["product_id"]
									, $v["useit"]
									, $v["search_phase"]
									, $v["search_r_start_column"]
									, $v["search_r_start_row"]
									, $v["search_r_end_column"]
									, $v["search_r_end_row"]
									, $v["search_sheet"]
									, $v["start_location_column"]
									, $v["start_location_row"]
									, $v["reading_map"]
									, $v["cal_map"]
									, $nLD
									, $v["formula_a"]
									, $v["formula_b"]
									, $v["sheet_style"]
								))
				{
					$retVal = false;
				}
		}
	}
	
	return $retVal;
}


function DBgetSettingsArr($investorId = -1)
{
	if(0 >= $investorId) // full info
	{
		return DB::query("SELECT * FROM irf_settings WHERE 1=1");
	}
	else
	{
		return DB::query("SELECT * FROM irf_settings WHERE investor_id=%i", $investorId);
	}
}


//==============================================================================




function DBgetUserDataByLogin($login)
{
	$rArr = DB::query("SELECT * FROM users WHERE login=%s", $login);

	if(0 >= count($rArr))
	{
		return false;
	}
	else
	{
		return $rArr;
	}
}

function DBgetUserDataById($id)
{
	$rArr = DB::query("SELECT * FROM users WHERE id=%i", $id);

	if(0 >= count($rArr))
	{
		return false;
	}
	else
	{
		return $rArr;
	}
}


function DBgetGroupsIdsListByName($nameArr)
{
	$rArr = array();
	$tArr = DB::query("SELECT g_id FROM u_groups WHERE g_name in %?", array('admin', 'user'));

	if(0 >= count($tArr))
	{
		return false;
	}
	else
	{
		foreach($tArr as $k => $v)
		{
			if(isset($v["g_id"]))
			{
				array_push($rArr, $v["g_id"]);
			}
		}
		return $rArr;
	}
}


function DBgetLoginAttemptInfoByIp($ip)
{
	$rArr = DB::query("SELECT * FROM login_attempts WHERE ip=%s", $ip);

	if(0 >= count($rArr))
	{
		return false;
	}
	else
	{
		return $rArr;
	}
}


function DBaddBadLoginAttemptByIp($ip, $login = "")
{
	$rArr = DB::query("SELECT * FROM login_attempts WHERE ip=%s", $ip);
	$dt = date("Y-m-d H:i:s");
	$blCnt = 1;
	
	if(strlen($login) > 20)
	{
		substr($login, 0, 20);
	}
	
	if(0 >= count($rArr))
	{
		DB::insert
				(
					'login_attempts', 
					array
					(
						"ip" => $ip
						,"login" => $login
						,"last_attempt" => $dt
						,"b_attempt_cnt" => $blCnt
					)
				);
	}
	else
	{
		$blCnt = $rArr[0]["b_attempt_cnt"];
		$blCnt++;
		DB::query
				(
					"UPDATE login_attempts SET %? WHERE ip = %s"
					,array
					(
						"login" => $login
						,"last_attempt" => $dt
						,"b_attempt_cnt" => $blCnt
					)
					, $ip
				);
	}
					
	return $blCnt;
}


function DBresetBadLoginAttemptInfoByIp($ip, $login = "")
{
	$count = DB::queryFirstField("SELECT COUNT(*) FROM login_attempts WHERE ip = %s", $ip);
	$dt = date("Y-m-d H:i:s");
	
	if(strlen($login) > 20)
	{
		substr($login, 0, 20);
	}
	
	if($count > 0)
	{
		DB::query
				(
					"UPDATE login_attempts SET %? WHERE ip = %s"
					,array
					(
						"login" => $login
						,"last_attempt" => $dt
						,"b_attempt_cnt" => 0
					)
					, $ip
				);
	}
	else
	{
		DB::insert
				(
					'login_attempts', 
					array
					(
						"ip" => $ip
						,"login" => $login
						,"last_attempt" => $dt
						,"b_attempt_cnt" => 0
					)
				);
	}
					
	return;
}

function DBgetUsersList()
{
	$rArr = DB::query("SELECT * FROM users WHERE 1=1");

	if(0 >= count($rArr))
	{
		return false;
	}
	else
	{
		return $rArr;
	}
}

function DBgetGroupsList()
{
	$rArr = DB::query("SELECT * FROM u_groups WHERE 1=1");

	if(0 >= count($rArr))
	{
		return false;
	}
	else
	{
		return $rArr;
	}
}

function DBaddUser($login, $groupId, $enabled, $password, $salt, $name, $email)
{
	$retVal = false;
	$date = date("Y-m-d");

	DB::insert
			(
				'users',
				array
					(
						"login" => $login
						,"password" => $password
						,"p_salt" => $salt
						,"email" => $email
						,"name" => $name
						,"enabled" => $enabled
						,"u_group" => $groupId
						,"date_added" => $date
					)
			);
			
	if(DB::insertId() > 0)
	{
		$retVal = true;
	}
	return $retVal;
}

function DBupdateUser($id, $groupId, $enabled, $password, $salt, $name, $email)
{
	$retVal = false;
	DB::query
			(
				"UPDATE users SET %? WHERE id = %i"
				,array
				(
					"password" => $password
					,"p_salt" => $salt
					,"email" => $email
					,"name" => $name
					,"enabled" => $enabled
					,"u_group" => $groupId
				)
				, $id
			);

	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	
	return $retVal;
}

function DBdeleteUser($id)
{
	$retVal = false;
	DB::delete('users', "id=%i", $id);
	if(DB::affectedRows() > 0)
	{
		$retVal = true;
	}
	return $retVal;
}


function DBerrorHandler($params)
{
	echo "Error: " . $params['error'] . "<br>\n";
	
	$dMsg = "Error: " . $params['error'] . "<br>\n";
	$dMsg .= "Query: " . $params['query'] . "<br>\n";
	
	debugMsg($dMsg);
	//die; // don't want to keep going if a query broke
}

/*
 * ----------------------------------------------------------------
 */

 
 
/*
* user manage functions ----------------------------
*/

function u_getPassHash($password, $salt)
{
	$realSalt = "";
	if(strlen($salt) > 3)
	{
		$realSalt = substr($salt, 3);
	}

	return sha1($password . $realSalt);
}

function u_checkIfIpBlocked($ip)
{
	global $settingsMaxBadLoginsAttempts;
	$retVal = false;
	$badLoginAttempts = u_badLoginAttemptCount($ip);
	
	if($badLoginAttempts >= $settingsMaxBadLoginsAttempts)
	{
		$retVal = true;
	}

	return $retVal;
}

function u_processBadLoginAttempt($ip, $login = "")
{
	DBaddBadLoginAttemptByIp($ip, $login = "");
}

function u_badLoginAttemptCount($ip, $login = "")
{
	global $settingsHoursToBlockBadLogins;
	$retVal = 0;

	$rArr = DBgetLoginAttemptInfoByIp($ip);
	if(count($rArr) > 0)
	{
		$dt1 = $rArr[0]["last_attempt"];
		$dt2 = date("Y-m-d H:i:s");
		$difference = strtotime($dt2 - $dt1);
		$hours = $difference / 3600; // 3600 seconds in an hour
		if($hours > $settingsHoursToBlockBadLogins)
		{
			DBresetBadLoginAttemptInfoByIp($ip, $login);
			$retVal = 0;
		}
		else
		{
			$retVal = $rArr[0]["b_attempt_cnt"];
		}
	}
	else
	{
		$retVal = 0;
	}
	
	return $retVal;
}

function u_resetBadLoginAttemptCount($ip, $login = "")
{
	DBresetBadLoginAttemptInfoByIp($ip, $login);
}

function u_do_login($login, $password)
{

	$retVal = false;
	$uArr = DBgetUserDataByLogin($login);

	if(false !== $uArr)
	{
		if(u_getPassHash($password, $uArr[0]['p_salt']) == $uArr[0]['password'] && $uArr[0]['enabled'])
		{
			$_SESSION['loged_in'] = true;
			$_SESSION['user_id'] = $uArr[0]['id'];
			$_SESSION['group_id'] = $uArr[0]['u_group'];
			$retVal = true;
		}
	}
	else
	{
		$_SESSION['loged_in'] = false;
		$_SESSION['user_id'] = -1;
		$_SESSION['group_id'] = -1;
		$retVal = false;
	}
	return $retVal;
}

function u_do_logout()
{
	$_SESSION['loged_in'] = false;
	$_SESSION['user_id'] = -1;
	$_SESSION['group_id'] = -1;
}

function u_isLogedIn()
{
	$retVal = false;

	if(isset($_SESSION['loged_in']) && isset($_SESSION['user_id']))
	{
		if(true == $_SESSION['loged_in'] && $_SESSION['user_id'] > 0)
		{
			$retVal = true;
		}
	}

	return $retVal;
}

function u_getUserId()
{
	$retVal = false;
	
	if(true == $_SESSION['loged_in'] && $_SESSION['user_id'] > 0)
	{
		$retVal = $_SESSION['user_id'];
	}
	return $retVal;
}

function u_getUserGroupId()
{
	$retVal = false;
	
	if(true == $_SESSION['loged_in'] && $_SESSION['group_id'] > 0)
	{
		$retVal = $_SESSION['group_id'];
	}
	return $retVal;
}

function u_isUserAllowed($gAllowedArr)
{
	$retVal = false;

	if(true == $_SESSION['loged_in'] && $_SESSION['group_id'] > 0 && count($gAllowedArr) > 0)
	{
		$gIdArr = DBgetGroupsIdsListByName($gAllowedArr);
		if(count($gIdArr) > 0 && in_array($_SESSION['group_id'], $gIdArr))
		{
			$retVal = true;
		}
	}
	return $retVal;
}

function u_getUsersList()
{
	$retVal = false;

	$retVal = DBgetUsersList();
	
	return $retVal;
}

function u_getGroupsList()
{
	$retVal = false;

	$retVal = DBgetGroupsList();
	
	return $retVal;
}

function u_editUser($id, $groupId, $enabled = true, $password = "", $name = "", $email="")
{
	$retVal = false;
	$salt = "";
	$uArr = DBgetUserDataById($id);

	if(false !== $uArr)
	{
		if("" == $password)
		{
			$salt = $uArr[0]["p_salt"];
			$password = $uArr[0]["password"];
		}

		else
		{
			$salt = generateRandomString(10);
			$password = u_getPassHash($password, $salt);
		}

		if(DBupdateUser($id, $groupId, $enabled, $password, $salt, $name, $email))
		{
			$retVal = true;
		}
	}
	
	return $retVal;
}

function u_addUser($login, $password, $groupId, $enabled = true, $name = "", $email = "")
{
	$retVal = false;
	$salt = "";
	$uArr = DBgetUserDataByLogin($login);

	if(false === $uArr)
	{
		$salt = generateRandomString(10);
		$password = u_getPassHash($password, $salt);
		if(DBaddUser($login, $groupId, $enabled, $password, $salt, $name, $email))
		{
			$retVal = true;
		}
	}
	return $retVal;
}

function u_deleteUser($id)
{
	$retVal = false;
	if(DBdeleteUser($id))
	{
		$retVal = true;
	}
	return $retVal;
}


/*
* ----------------------------------------------------------------
*/
 
 
function getInvestorsArrWithIdAsKey($full = 1, $sortByUId = false)
{
	$invArr = array();
	$tmpInvArr = DBgetInvestorsArr();
	
	if($sortByUId)
	{
		for($i = 0; $i < count($tmpInvArr); $i++)
		{
			for($j = $i + 1; $j < count($tmpInvArr); $j++)
			{
				if($tmpInvArr[$j]["investor_id"] < $tmpInvArr[$i]["investor_id"])
				{
					$tmp = $tmpInvArr[$j];
					for($n = $j; $n > $i; $n--)
					{
						$tmpInvArr[$n] = $tmpInvArr[$n - 1];
					}
					$tmpInvArr[$n] = $tmp;
				}
			}
		}
	}
	
	foreach($tmpInvArr as $k => $v)
	{
		if($full)
		{
			$invArr[$v["id"]] = $v;
		}
		else
		{
			$invArr[$v["id"]] = array
									(
										"id" => $v["id"]
										, "investor_id" => $v["investor_id"]
										, "investor_name" => $v["investor_name"]
										, "notes" => $v["notes"]
										, "status" => $v["status"]
										, "date_added" => $v["date_added"]
									);
		}
	}

	return $invArr;
}

function getProductsArrWithIdAsKey($sortByUId = false)
{
	$prodArr = array();
	$tmpProdArr = DBgetProductsArr();
	
	if($sortByUId)
	{
		for($i = 0; $i < count($tmpProdArr); $i++)
		{
			for($j = $i + 1; $j < count($tmpProdArr); $j++)
			{
				if($tmpProdArr[$j]["product_id"] < $tmpProdArr[$i]["product_id"])
				{
					$tmp = $tmpProdArr[$j];
					for($n = $j; $n > $i; $n--)
					{
						$tmpProdArr[$n] = $tmpProdArr[$n - 1];
					}
					$tmpProdArr[$n] = $tmp;
				}
			}
		}
	}
	
	foreach($tmpProdArr as $k => $v)
	{
		$prodArr[$v["id"]] = $v;
	}

	return $prodArr;
}

function getLockdaysArrWithIdAsKey($sortByUId = false)
{
	$ldArr = array();
	$tmpLockdayArr = DBgetLockdaysArr();
	
	if($sortByUId)
	{
		for($i = 0; $i < count($tmpLockdayArr); $i++)
		{
			for($j = $i + 1; $j < count($tmpLockdayArr); $j++)
			{
				if($tmpLockdayArr[$j]["lock_day_id"] < $tmpLockdayArr[$i]["lock_day_id"])
				{
					$tmp = $tmpLockdayArr[$j];
					for($n = $j; $n > $i; $n--)
					{
						$tmpLockdayArr[$n] = $tmpLockdayArr[$n - 1];
					}
					$tmpLockdayArr[$n] = $tmp;
				}
			}
		}
	}
	
	foreach($tmpLockdayArr as $k => $v)
	{
		$ldArr[$v["id"]] = $v;
	}

	return $ldArr;
}

/*
* ----------------------------------------------------------------
*/

function getValOfRequest($_rArr, &$var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/)
{
	$retVal = false;
	if(isset($_rArr[$vName]))
	{
		$retVal = true;
		if(0 == $vType)
		{
			$var = $_rArr[$vName];
			if(strlen($var) > $maxLen)
			{
				$var = substr($var, 0, $maxLen);
			}
		}
		else if(1 == $vType)
		{
			$var = intval($_rArr[$vName]);
		}
		else if(2 == $vType)
		{
			 $var = true;
		}
		else if(3 == $vType)
		{
			$var = array();
			if(is_array($_rArr[$vName]))
			{
				foreach($_rArr[$vName] as $k => $v)
				{
					$var[$k] = $v; 
				}
			}
		}
	}

	return $retVal;
}

function getRequestVal(&$var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/)
{
	return getValOfRequest($_REQUEST, $var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/);
}

function getGetVal(&$var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/)
{
	return getValOfRequest($_GET, $var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/);
}

function getPostVal(&$var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/)
{
	return getValOfRequest($_POST, $var, $vName, $vType /* 0 - str, 1 - int, 2 - bool, 3 - array */, $maxLen /*for $vType == 0*/);
}

function checktime($hour, $min, $sec)
{
     if ($hour < 0 || $hour > 23 || !is_numeric($hour))
	 {
         return false;
     }
     if ($min < 0 || $min > 59 || !is_numeric($min))
	 {
         return false;
     }
     if ($sec < 0 || $sec > 59 || !is_numeric($sec))
	 {
         return false;
     }
     return true;
}
 
function sanitizeFileName($filename)
{
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[\s-]+/', '-', $filename);
	$filename = trim($filename, '.-_');
	return $filename;
}

function generateRandomString($length = 10)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++)
	{
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function getDirList($path, $recursive = true, $dirsOnly = false)
{
	$retVal = array();
	//$retVal subGetDirList(new DirectoryIterator($path));
	if(is_dir($path))
	{
		$dit = new DirectoryIterator($path);
		
		foreach ($dit as $key => $child)
		{
			$isDir = false;
			$arrTmp = array
			(
				"type" => ""
				,"name" => ""
				,"dirList" => array()
				,"path" => ""
			);
			
			if ($child->isDot())
			{
					continue;
			}
			$arrTmp["name"] = /*utf8_encode(*/$child->getBasename()/*)*/;
			$arrTmp["path"] = /*utf8_encode(*/$child->getPathname()/*)*/;
			$arrTmp["path"] = str_replace("\\", "/", $arrTmp["path"]); //!?
			if ($child->isDir())
			{
				$isDir = true;
				$arrTmp["type"] = "dir";
				if($recursive)
				{
					$arrTmp["dirList"] = getDirList($child->getPathname());
				}
			}
			else
			{
				$arrTmp["type"] = "file";
			}
			
			if($dirsOnly && !$isDir)
			{
				
			}
			else
			{
				$retVal[] = $arrTmp;
			}
		}
	}

	for($i = 0; $i < count($retVal); $i++)
	{
		for($j = $i + 1; $j < count($retVal); $j++)
		{

			$re = false;
			if("file" == $retVal[$i]["type"] && "dir" == $retVal[$j]["type"])
			{
				$re = true;
			}
			elseif(strcmp($retVal[$i]["name"], $retVal[$j]["name"]) > 0 )
			{
				$re = true;
			}
			
			if($re)
			{
				$el = $retVal[$j];
				for($n = $j; $n > $i; $n--)
				{
					$retVal[$n] = $retVal[$n - 1];
				}
				$retVal[$i] = $el;
			}
		}
	}
		
	return $retVal;
}


function pickUpNotExistentPName($path)
{
	$retVal = $path;
	if(file_exists($path))
	{
		$i = 1;
		$name = pathinfo($path, PATHINFO_FILENAME);
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		$path = pathinfo($path, PATHINFO_DIRNAME);
		$newPathName = $path . "/" . $name . "-" . str_pad($i, 3, "0", STR_PAD_LEFT) . "." . $ext;
		while(file_exists($newPathName) && $i < 1000)
		{
			$i++;
			$newPathName = $path . "/" . $name . "-" . str_pad($i, 3, "0", STR_PAD_LEFT) . "." . $ext;
		}
		$retVal = $newPathName;
	}
	return $retVal;
}

function prepFolderECF($path)
{
	$retVal = false;
	$error = false;
	$mExcelCsvFpath = $path . "/" . date("Ymd");
	
	if(!is_dir($path))
	{
		if(!mkdir($path))
		{
			$error = true;
			debugMsg("Can't create folder: " . $path);
		}
	}
	
	if(!$error)
	{
		if(is_dir($mExcelCsvFpath))
		{
			$retVal = $mExcelCsvFpath;
		}
		else
		{
			if(mkdir($mExcelCsvFpath))
			{
				$retVal = $mExcelCsvFpath;
			}
			else
			{
				debugMsg("Can't create folder: " . $mExcelCsvFpath);
			}
		}
	}
	return $retVal;
}

function prepFolder($path)
{
	$retVal = false;
	$error = false;
	
	if(!is_dir($path))
	{
		if(!mkdir($path))
		{
			$error = true;
			debugMsg("Can't create folder: " . $path);
		}
		else
		{
			$retVal = $path;
		}
	}
	else
	{
		$retVal = $path;
	}

	return $retVal;
}

function createCvsFrom2DArr($dataArray, $path)
{
	$retVal = false;
	$error = false;
	
	try
	{
		$fileType = 'CSV';
		$objReader = PHPExcel_IOFactory::createReader($fileType);
		$objPHPExcel = new PHPExcel();
		
		for($i = 0; $i < count($dataArray); $i++)
		{
			for($j = 0; $j < count($dataArray[$i]); $j++)
			{
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j, $i + 1, $dataArray[$i][$j]);
			}
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $fileType);
		$objWriter->setDelimiter(',');
		$objWriter->setEnclosure('"');
		$objWriter->setLineEnding("\r\n");
		$objWriter->save($path);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
	}


	if(!$error)
	{
		$retVal = $path;
	}
	return $retVal;
}


function getIndexForInvestor($dir, $id)
{
	$retVal = false;
	if($handle = opendir($dir))
	{
		$retVal = 0;
		while (false !== ($entry = readdir($handle)))
		{
			if ($entry != "." && $entry != "..")
			{
				if(preg_match ("/" . $id . "-(\d+)-\d+\.csv/i", $entry, $matches))
				{
					if($matches[1] >= $retVal)
					{
						$retVal = $matches[1] + 1;
					}
				}
			}
		}
		closedir($handle);
	}
	else
	{
		debugMsg("Can't open dir: " . $dir);
	}
	return $retVal;
}

function processInvestorsFile($path)
{
	$retVal = false;
	$arrTmp = array();
	$error = false;
	$ext = pathinfo($path, PATHINFO_EXTENSION);
	if(!in_array($ext, array("xls", "xlsx", "csv")))
	{
		return $retVal;
	}
	
	
	try
	{
		//$m_InputFileType = PHPExcel_IOFactory::identify($path);
		//$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
		$objReader = PHPExcel_IOFactory::createReaderForFile($path);
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($path);
		$sheetCount = $objPHPExcel->getSheetCount();
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
	}

	if(!$error)
	{
		try
		{
			for($i = 0; $i < $sheetCount; $i++)
			{
				$objPHPExcel->setActiveSheetIndex($i);
				$fCellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, 1)->getValue();
				
				if(trim(strtolower($fCellVal)) == "investors")
				{
					$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestColumn());
					$hr = $objPHPExcel->getActiveSheet()->getHighestRow();
					if($hc > 0 && $hr > 2)
					{
						for($j = 1; $j <= $hr; $j++)
						{
							$fCellValId = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $j)->getValue();
							$fCellValName = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $j)->getValue();
							$arrTmp[] = array("investor_id" => $fCellValId, "investor_name" => $fCellValName);
							//print $fCellValId.":".$fCellValName."<br>";
						}
					}
					//PHPExcel_Cell::columnIndexFromString($column);
					//PHPExcel_Cell::stringFromColumnIndex();
				}
			}
		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg($e->getMessage());
		}
	}
	
	if(!$error && count($arrTmp) > 0)
	{
		$retVal = $arrTmp;
	}
	return $retVal;
}


/*
// filter for PHPExcel_IOFactory reader
class m_PHPExcelReadFilter implements PHPExcel_Reader_IReadFilter
{
	protected $rowMin;
	protected $rowMax;
	protected $columnMin;
	protected $columnMax;
	
	function __construct()
	{
		$this->rowMin = 0;
		$this->rowMax = 0;
		$this->columnMin = 0;
		$this->columnMax = 0;
	}
   
	public function setRange($rowMin = 0, $rowMax = 0, $columnMin = 0, $columnMax = 0)
	{
		$this->rowMin = $rowMin;
		$this->rowMax = $rowMax;
		$this->columnMin = $columnMin;
		$this->columnMax = $columnMax;
	}
	
    public function readCell($column, $row, $worksheetName = '') {

        if ($row >= $this->rowMin && $row <= $this->rowMax && $column >= $this->columnMin && $column <= $this->columnMax)
		{
//print "readCell " . $column . ":" . $row . "<br>";
            return true;
        }

        return false;
    }
}
*/


function processUploadedECF($path, $investor_id)
{
	$retVal = false;
	$fileInd = 0;
	$error = false;
	$dir = dirname($path);
	$arrFiles = array();
	$fName = "";
	$sheetNames = array();
	
	$fileInd = getIndexForInvestor($dir, $investor_id);
	if(false === $fileInd)
	{
		$error = true;
	}
	
	if(!$error)
	{
		try
		{
			
			//$objReader = PHPExcel_IOFactory::createReaderForFile($path);
			$m_InputFileType = PHPExcel_IOFactory::identify($path);
			$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
			//$objReader->setReadDataOnly(true);
			//$sheetNames = $objReader->listWorksheetNames($path);
			
			//$filter = new m_PHPExcelReadFilter();
			//$filter->setRange(0, $hr, 0, $hc);
			//$objReader->setReadFilter($filter);
			//$objReader->setLoadSheetsOnly($sheetName)
			
			$objPHPExcel = $objReader->load($path);
			//$objPHPExcel->enableMemoryOptimization();

			
			
					
		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
		}
	}

	if(!$error)
	{

		if(strtolower($m_InputFileType) == "csv")
		{
			$fName = $investor_id . "-" . $fileInd . "-0.csv";
			$arrFiles[] = $fName;
			copy($path, $dir . "/" . $fName);
		}
		else
		{
			try
			{
				$sheetCount = $objPHPExcel->getSheetCount();
				for($i = 0; $i < $sheetCount; $i++)
				{
					
					$objPHPExcel->setActiveSheetIndex($i);
					//$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestColumn());
					$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestDataColumn());
					//$hr = $objPHPExcel->getActiveSheet()->getHighestRow();
					$hr = $objPHPExcel->getActiveSheet()->getHighestDataRow();
					
					
					for($j = 1; $j <= $hr; $j++)
					{
						for($n = 0;$n < $hc; $n++ )
						{
							$isCellDateTime = PHPExcel_Shared_Date::isDateTime($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($n, $j));
							

							$fCellValId = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($n, $j)->getValue();
							if($isCellDateTime)
							{
								$fCellValId = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($n, $j)->getFormattedValue();
							}
							elseif(substr($fCellValId, 0, 1) == "=")
							{
								$fCellValId = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($n, $j)->getOldCalculatedValue();
							}
							
							
							if(strtolower($fCellValId) == "#n/a" || strtolower($fCellValId) == "#í/ä")
							{
								$fCellValId = "";
							}
							
							$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($n, $j, $fCellValId);

						}
					}
						
				
					
				}
				
				
				$sheetCount = $objPHPExcel->getSheetCount();
				for($i = 0; $i < $sheetCount; $i++)
				{
					$objPHPExcel->setActiveSheetIndex($i);
					
					$fName = $investor_id  . "-" . $fileInd . "-" . $i . ".csv";
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
					
					$objWriter->setDelimiter(',');
					$objWriter->setEnclosure('"');
					$objWriter->setLineEnding("\r\n");
					$objWriter->setSheetIndex($i);
					$objWriter->save($dir . "/" . $investor_id  . "-" . $fileInd . "-" . $i . ".csv");
					
					$arrFiles[] = $fName;
					unset($objWriter);
				}
					
				$objPHPExcel->disconnectWorksheets(); 
				unset($objPHPExcel); 

			}
			catch(Exception $e)
			{
				$error = true;
				debugMsg($e->getMessage());
			}
		}

		$retVal = $arrFiles;
	}
	
	//debugMsg();
	return $retVal;
}


function processSettingsFile($path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array();

	try
	{
		
		$m_InputFileType = PHPExcel_IOFactory::identify($path);
		$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
		$objPHPExcel = $objReader->load($path);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	
	if(!$error)
	{
		try
		{
			
			$objPHPExcel->setActiveSheetIndex(0);
			$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestDataColumn());
			$hr = $objPHPExcel->getActiveSheet()->getHighestDataRow();

			$currKey = "";
			$prevKey = "";
			for($i = 1; $i <= $hr; $i++)
			{
				$rowArr = array();
				$isRowEmpty = true;
				$isCommentStarted = false;
				for($j = 0; $j < $hc; $j++)
				{
					$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getValue();
					$tCellVal = trim($cellVal);
					//$tCellVal = strtolower(trim($cellVal));
					if("" != $tCellVal)
					{
						$isRowEmpty = false;
					}
					if("//" == substr($tCellVal, 0, 2))
					{
						$isCommentStarted = true;
					}
					if($isCommentStarted)
					{
						$rowArr[$j] = "";
					}
					else
					{
						$rowArr[$j] = $tCellVal;
					}
					//break;
				}
				if($isRowEmpty)
				{
					continue;
				}
				
				$fCellVal = $rowArr[0];
				if("" != $fCellVal)
				{
					$currKey = $fCellVal;
				}
				
				$rowArr[0] = $currKey;
				array_push($arrTmp, $rowArr);
			}
		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg($e->getMessage());
		}
	}
	
	if(!$error && count($arrTmp) > 0)
	{
		$retVal = $arrTmp;
	}
	return $retVal;
}

function processDataSettingsFile($path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array();
	
	
	if($settArr = processSettingsFile($path))
	{
		$searchPhase = array();
		$searchRange = array();
		$searchSheet = array();
		$startLocation = array();
		$readingMap = array();
		$callMap = array();
		$lockDays = array();
		$formulaA = array();
		$formulaB = array();
		$sheetType = array();
		
		for($i = 0; $i < count($settArr); $i++)
		{
			if("search phase" == $settArr[$i][0])
			{
				$searchPhase = array($settArr[$i][1], $settArr[$i][2]);
			}
			if("search range" == $settArr[$i][0])
			{
				$searchRange = array($settArr[$i][1], $settArr[$i][2], $settArr[$i][3], $settArr[$i][4]);
			}
			if("search sheet" == $settArr[$i][0])
			{
				$searchSheet = array($settArr[$i][1]);
			}
			if("start location" == $settArr[$i][0])
			{
				$startLocation = array($settArr[$i][1], $settArr[$i][2]);
			}
			if("reading map" == $settArr[$i][0])
			{
				$readingMap = array_slice($settArr[$i], 1);
			}
			if("cal map" == $settArr[$i][0])
			{
				$callMap = array_slice($settArr[$i], 1);
			}
			if("lock days" == $settArr[$i][0])
			{
				$lockDays = array_slice($settArr[$i], 1);
			}
			if("a =" == $settArr[$i][0])
			{
				$formulaA = array_slice($settArr[$i], 1);
			}
			if("b =" == $settArr[$i][0])
			{
				$formulaB = array_slice($settArr[$i], 1);
			}
			if("sheet type" == $settArr[$i][0])
			{
				$sheetType = array($settArr[$i][1]);
			}
		}
		
		$arrTmp = array
		(
			"searchPhase" => $searchPhase
			,"searchRange" => $searchRange
			,"searchSheet" => $searchSheet
			,"startLocation" => $startLocation
			,"readingMap" => $readingMap
			,"callMap" => $callMap
			,"lockDays" => $lockDays
			,"formulaA" => $formulaA
			,"formulaB" => $formulaB
			,"sheetType" => $sheetType
		);
	}
//print_r($arrTmp);
	if(!$error && count($arrTmp) > 0)
	{
		$retVal = $arrTmp;
	}
	return $retVal;
}


function processGeneralSettingsFile($path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array
					(
						"investors" => array()
						,"loan_products" => array()
						,"lock_days" => array()
						,"date_format" => array()
						,"time_format" => array()
					);
	
	if($settArr = processSettingsFile($path))
	{
		for($i = 0; $i < count($settArr); $i++)
		{
			$lowerFCellVall = trim(strtolower($settArr[$i][0]));
			if("investors" == $lowerFCellVall)
			{
				array_push($arrTmp["investors"], $settArr[$i]);
			}
			if("loan product list" === $lowerFCellVall)
			{
				array_push($arrTmp["loan_products"], $settArr[$i]);
			}
			if("lock days list" === $lowerFCellVall)
			{
				array_push($arrTmp["lock_days"], $settArr[$i]);
			}
			if("date formet" === $lowerFCellVall || "date format" === $lowerFCellVall)
			{
				array_push($arrTmp["date_format"], $settArr[$i]);
			}
			if("time format" === $lowerFCellVall)
			{
				array_push($arrTmp["time_format"], $settArr[$i]);
			}
		}
	}
	else
	{
		$error = true;
	}
	
	if(!$error)
	{
		$retVal = $arrTmp;
	}
	
	return $retVal;
}

function processInvestorSettingsFile($path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array();
	$isDateSettingsSection = false;
	$dateTimeSettingsArr = array("date/time sheet", "date/time phase", "date/time range", "date location", "time location","date format", "time format");

	if($settArr = processSettingsFile($path))
	{
		for($i = 0; $i < count($settArr); $i++)
		{
			$lowerFCellVall = strtolower($settArr[$i][0]);
			
			if("investor name" == $lowerFCellVall)
			{
				$arrTmp["investor_name"] = $settArr[$i][1];
			}
			if("investor index" == $lowerFCellVall)
			{
				$arrTmp["investor_index"] = $settArr[$i][1];
			}
			
			if(in_array($lowerFCellVall, $dateTimeSettingsArr))
			{
				/*if("date/setting" == $lowerFCellVall)
				{
					$arrTmp["date_setting"] = $settArr[$i][1];
				}*/
				if("date/time sheet" == $lowerFCellVall)
				{
					$arrTmp["date_time_sheet"] = $settArr[$i][1];
				}
				if("date/time phase" == $lowerFCellVall)
				{
					$arrTmp["date_time_phase"] = $settArr[$i][1];
				}
				if("date/time range" == $lowerFCellVall)
				{
					$arrTmp["date_time_range"] = array($settArr[$i][1], $settArr[$i][2], $settArr[$i][3], $settArr[$i][4]);
				}
				if("date location" == $lowerFCellVall)
				{
					$arrTmp["date_location"] = array($settArr[$i][1], $settArr[$i][2]);
				}
				if("time location" == $lowerFCellVall)
				{
					$arrTmp["time_location"] = array($settArr[$i][1], $settArr[$i][2]);
				}
				if("date format" == $lowerFCellVall)
				{
					$arrTmp["date_format"] = $settArr[$i][1];
				}
				if("time format" == $lowerFCellVall)
				{
					$arrTmp["time_format"] = $settArr[$i][1];
				}
			}
			
			if("product setting" == $lowerFCellVall)
			{
				$product_id = $settArr[$i][1];
				
				$goOn = true;
				
				$product_use_it = 0;
				$product_sheet_style = "";
				$product_search_phase = "";
				$product_search_range = array();
				$product_search_sheet = "";
				$product_start_location = array();
				$product_reading_map = array();
				$product_cal_map = array();
				$product_lock_days = array();
				$product_formula_A = array();
				$product_formula_B = array();
					
				while($goOn)
				{
					$i++;
					if($i >= count($settArr))
					{
						$goOn = false;
						break;
					}
					$lowerFCellVall = trim(strtolower($settArr[$i][0]));
					if("product setting" == $lowerFCellVall)
					{
						$i--;
						$goOn = false;
						break;
					}
					
					if("use it" == $lowerFCellVall && isset($settArr[$i][1]) && ("1" === $settArr[$i][1] || 1 === $settArr[$i][1]))
					{
						$product_use_it = 1;
					}
					if("sheet style" == $lowerFCellVall)
					{
						$product_sheet_style = $settArr[$i][1];
					}
					if("search phase" == $lowerFCellVall)
					{
						$product_search_phase = $settArr[$i][1];
					}
					if("search range" == $lowerFCellVall)
					{
						$product_search_range = array
													(
														"start_row" => $settArr[$i][2]
														,"start_col" => $settArr[$i][1]
														,"end_row" => $settArr[$i][4]
														,"end_col" => $settArr[$i][3]
													);
					}
					if("search sheet" == $lowerFCellVall)
					{
						$product_search_sheet = $settArr[$i][1];
					}
					if("start location" == $lowerFCellVall)
					{
						$product_start_location = array("col" => $settArr[$i][1], "row" => $settArr[$i][2]);
					}
					if("reading map" == $lowerFCellVall)
					{
						for($j = 1; $j < count($settArr[$i]); $j++)
						{
							if("" === trim($settArr[$i][$j]))
							{
								break;
							}
							else
							{
								array_push($product_reading_map, $settArr[$i][$j]);
							}
						}
					}
					if("cal map" == $lowerFCellVall)
					{
						for($j = 1; $j < count($settArr[$i]); $j++)
						{
							if("" === trim($settArr[$i][$j]))
							{
								break;
							}
							else
							{
								array_push($product_cal_map, $settArr[$i][$j]);
							}
						}
					}
					if("lock days" == $lowerFCellVall)
					{
						for($j = 1; $j < count($settArr[$i]); $j++)
						{
							if("" === trim($settArr[$i][$j]))
							{
								break;
							}
							else
							{
								array_push($product_lock_days, $settArr[$i][$j]);
							}
						}
					}
					if("a =" == $lowerFCellVall)
					{
						for($j = 1; $j < count($settArr[$i]); $j++)
						{
							if("" === trim($settArr[$i][$j]))
							{
								break;
							}
							else
							{
								array_push($product_formula_A, $settArr[$i][$j]);
							}
						}
					}
					if("b =" == $lowerFCellVall)
					{
						for($j = 1; $j < count($settArr[$i]); $j++)
						{
							if("" === trim($settArr[$i][$j]))
							{
								break;
							}
							else
							{
								array_push($product_formula_B, $settArr[$i][$j]);
							}
						}
					}
				}
				
				if(!isset($arrTmp["products_settings"]))
				{
					$arrTmp["products_settings"] = array();
				}
				
				$product_set_info = array
					(
						"product_id" => $product_id
						,"use_it" => $product_use_it
						,"sheet_style" => $product_sheet_style
						,"search_phase" => $product_search_phase
						,"search_range" => $product_search_range
						,"search_sheet" => $product_search_sheet
						,"start_location" => $product_start_location
						,"reading_map" => $product_reading_map
						,"cal_map" => $product_cal_map
						,"lock_days" => $product_lock_days
						,"formula_A" => $product_formula_A
						,"formula_B" => $product_formula_B
					);
				array_push($arrTmp["products_settings"], $product_set_info);
			}
		}
	}
	if(isset($arrTmp["investor_index"]))
	{
		$retVal = $arrTmp;
	}
	else
	{
		$retVal = false;
	}

	return $retVal;
}

function readData01FromFile($readSettings, $path, &$errMsgVar = null)
{
	$retVal = false;
	$error = false;
	$errorMessage = "";
	$arrTmp = array();

	try
	{
		
		$m_InputFileType = PHPExcel_IOFactory::identify($path);
		$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
		$objPHPExcel = $objReader->load($path);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
		$errorMessage .= "error during creating PHPExcel object<br>\n";
	}
	
	if(!$error)
	{
		try
		{
			$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestDataColumn());
			$hr = $objPHPExcel->getActiveSheet()->getHighestDataRow();
			
			$startingPointColumn = -1;
			$startingPointRow = -1;
			$startingDataColumn = -1;
			$startingDataRow = -1;
			
			$rangeRowStart = $readSettings["searchRange"][1];
			$rangeRowEnd = $readSettings["searchRange"][3];

			$rangeClumnStart = PHPExcel_Cell::columnIndexFromString($readSettings["searchRange"][0]) - 1;
			$rangeClumnEnd = PHPExcel_Cell::columnIndexFromString($readSettings["searchRange"][2]) - 1;
			
			if($rangeClumnStart < 0)
			{
				$rangeClumnStart = 0;
			}
			if($rangeClumnEnd < 0)
			{
				$rangeClumnEnd = 0;
			}
			
			$dateTimePhaseLen = strlen($readSettings["searchPhase"][0]);
			$doBreak = false;
			for($i = $rangeRowStart; $i <= $rangeRowEnd; $i++)
			{
				for($j = $rangeClumnStart; $j <= $rangeClumnEnd; $j++)
				{
					$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getValue();
					$cellVal = trim($cellVal);
					if(substr($cellVal, 0, $dateTimePhaseLen) == $readSettings["searchPhase"][0])
					{
						$startingPointColumn = $j;
						$startingPointRow = $i;
						$doBreak = true;
						break;
					}
				}
				
				if($doBreak)
				{
					break;
				}
			}

			if(0 > $startingPointColumn && 0 > $startingPointRow)
			{
				$error = true;
				$errorMessage .= "'search phase' was not found<br>\n";
			}

			if(!$error)
			{
				$startingDataColumn = $startingPointColumn + intval($readSettings["startLocation"][0]);
				$startingDataRow = $startingPointRow + intval($readSettings["startLocation"][1]);
				$sheetType = $readSettings["sheetType"][0];
				
				$columnCount = 0;
				for($i = 0; $i < count($readSettings["readingMap"]); $i++)
				{
					if("" != trim($readSettings["readingMap"][$i]))
					{
						$columnCount++;
					}
					else
					{
						break;
					}
				}

				// read data
				$tArr = array();
				if(1 == $sheetType)
				{
					for($i = $startingDataRow; $i < $hr; $i++)
					{
						$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($startingDataColumn, $i)->getValue();
						$cellVal = trim($cellVal);
						$cellVal = rtrim($cellVal, '%');
						
						$rowFCellIsNumeric = is_numeric($cellVal);
						if(!$rowFCellIsNumeric)
						{
							break;
						}
						$rowArr = array();
						for($j = 0; $j < $columnCount; $j++)
						{
							$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($startingDataColumn + $j, $i)->getValue();
							$cellVal = trim($cellVal);
							$mn = 1;

							if(preg_match("/^\((\d+(?:\.?\d)*)\)$/i", $cellVal, $matches))
							{
								$mn = -1;
								$cellVal = $matches[1];
							}
							
							$floatVal = floatval(trim($cellVal));
							$floatVal *= $mn;
							if("1" == trim($readSettings["readingMap"][$j]))
							{
								array_push($rowArr, $floatVal);
							}
						}
						array_push($tArr, $rowArr);
					}
				}
				else if(2 == $sheetType)
				{
					$rowReadLimit = min($hr, $rangeRowEnd);
					for($i = $startingPointRow; $i < $rowReadLimit; $i++)
					{
						$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($startingPointColumn, $i)->getValue();
						if(substr($cellVal, 0, $dateTimePhaseLen) == $readSettings["searchPhase"][0])
						{
							$rowArr = array();
							for($j = 0; $j < $columnCount; $j++)
							{
								$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($startingDataColumn + $j, $i)->getValue();
								$cellVal = trim($cellVal);
								$mn = 1;
								if(preg_match("/^\((\d+(?:\.?\d)*)\)$/i", $cellVal, $matches))
								{
									$mn = -1;
									$cellVal = $matches[1];
								}
								$floatVal = floatval(trim($cellVal));
								$floatVal *= $mn;
								if("1" == trim($readSettings["readingMap"][$j]))
								{
									array_push($rowArr, $floatVal);
								}
							}
							array_push($tArr, $rowArr);
						}
					}
				}
				else
				{
					$error = true;
					$errorMessage .= "unknown type of sheet<br>\n";
				}
			}

			if(!$error)
			{
				
				// sort arr
				for($i = 0; $i < count($tArr); $i++)
				{
					for($j = $i; $j < count($tArr); $j++)
					{
						if($tArr[$j][0] < $tArr[$i][0])
						{
							$tmpArr = $tArr[$j];
							for($n = $j; $n > $i; $n--)
							{
								$tArr[$n] = $tArr[$n - 1];
							}
							$tArr[$i] = $tmpArr;
						}
					}
				}
				
				$headerArr = array();
				for($i = 0; $i < count($readSettings["lockDays"]); $i++)
				{
					$tmpVal = $readSettings["lockDays"][$i];
					if("" != $tmpVal)
					{
						array_push($headerArr, $tmpVal);
					}
					else
					{
						break;
					}
				}
				
				array_push($arrTmp, $headerArr);
				for($i = 0; $i < count($tArr); $i++)
				{
					$rowArr = array();
					for($j = 0; $j < count($readSettings["callMap"]); $j++)
					{
						$tmpVal = $readSettings["callMap"][$j];
						if("" != $tmpVal && isset($tArr[$i][$tmpVal - 1]))
						{
							$a = 1;
							$b = 0;
							if(isset($readSettings["formulaA"][$j]))
							{
								$a = $readSettings["formulaA"][$j];
							}
							if(isset($readSettings["formulaB"][$j]))
							{
								$b = $readSettings["formulaB"][$j];
							}

							$tmpVar = round($a * $tArr[$i][$tmpVal - 1] + $b, 10);
							array_push($rowArr, $tmpVar);
						}
					}
					array_push($arrTmp, $rowArr);
				}
			}
		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg($e->getMessage());
			$errorMessage .= "error during working with PHPExcel object<br>\n";
		}
	}
	
	if(null != $errMsgVar)
	{
		$errMsgVar = $errorMessage;
	}
			
	if(!$error && count($arrTmp) > 0)
	{
		$retVal = $arrTmp;
	}
	return $retVal;
}


function processDateTimeSettingsFile($path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array();

	try
	{
		
		$m_InputFileType = PHPExcel_IOFactory::identify($path);
		$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
		$objPHPExcel = $objReader->load($path);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	
	if(!$error)
	{
		try
		{
			$objPHPExcel->setActiveSheetIndex(0);
			$hr = $objPHPExcel->getActiveSheet()->getHighestDataRow();
			
			$defDateR = false;
			$defTimeR = false;
			$dateFormats = array();
			$timeFormats = array();
			$dateTimePhase = array();
			$dateTimeRange = array();
			$dateLocation = array();
			$timeLocation = array();
			$dateFormat = array();
			$timeFormat = array();
			for($i = 1; $i <= $hr; $i++)
			{
				$fCellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $i)->getValue();
				$fCellVal = strtolower(trim($fCellVal));
				
				$column1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue();
				$column2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $i)->getValue();
				$column3 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $i)->getValue();
				$column4 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $i)->getValue();
				$column1 = trim($column1);
				$column2 = trim($column2);
				$column3 = trim($column3);
				$column4 = trim($column4);
					
				if($fCellVal == "date formet" || $fCellVal == "date format")
				{
					$defDateR = true;
				}
				if($fCellVal == "time format")
				{
					$defTimeR = true;
				}
				if($fCellVal == "date/time phase")
				{
					$dateTimePhase = array($column1, $column2);
				}
				if($fCellVal == "date/time range")
				{
					$dateTimeRange = array($column1, $column2, $column3, $column4);
				}
				if($fCellVal == "date location")
				{
					$dateLocation = array($column1, $column2);
				}
				if($fCellVal == "time location")
				{
					$timeLocation = array($column1, $column2);
				}
				if($fCellVal == "date format")
				{
					$dateFormat = array($column1, $column2);
				}
				if($fCellVal == "time format")
				{
					$timeFormat = array($column1, $column2);
				}
				
				if($defDateR)
				{
					
					if($column1 != "" && $column2 != "")
					{
						array_push($dateFormats, array($column1, $column2, $column3));
					}
					else
					{
						$defDateR = false;
					}
				}
				if($defTimeR)
				{
					
					if($column1 != "" && $column2 != "")
					{
						array_push($timeFormats, array($column1, $column2, $column3));
					}
					else
					{
						$defTimeR = false;
					}
				}
			}
			
			$arrTmp = array
			(
				"dateFormats" => $dateFormats
				,"timeFormats" => $timeFormats
				,"dateTimePhase" => $dateTimePhase
				,"dateTimeRange" => $dateTimeRange
				,"dateLocation" => $dateLocation
				,"timeLocation" => $timeLocation
				,"dateFormat" => $dateFormat
				,"timeFormat" => $timeFormat
			);

		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg($e->getMessage());
		}
	}
	
	if(!$error && count($arrTmp) > 0)
	{
		$retVal = $arrTmp;
	}
	return $retVal;
}

function readDateTimeFromFile($readSettings, $path)
{
	$retVal = false;
	$error = false;
	$arrTmp = array(0, 0);
	

	try
	{
		
		$m_InputFileType = PHPExcel_IOFactory::identify($path);
		$objReader = PHPExcel_IOFactory::createReader($m_InputFileType);
		$objPHPExcel = $objReader->load($path);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg('Error loading file "'.pathinfo($path, PATHINFO_BASENAME).'": '.$e->getMessage());
	}
	
	if(!$error)
	{
		try
		{
			$hc = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getHighestDataColumn());
			$hr = $objPHPExcel->getActiveSheet()->getHighestDataRow();
			
			$startingPointColumn = -1;
			$startingPointRow = -1;
			
			$rangeRowStart = $readSettings["dateTimeRange"][1];
			$rangeRowEnd = $readSettings["dateTimeRange"][3];
			$rangeClumnStart = PHPExcel_Cell::columnIndexFromString($readSettings["dateTimeRange"][0]) - 1;
			$rangeClumnEnd = PHPExcel_Cell::columnIndexFromString($readSettings["dateTimeRange"][2]) - 1;
			$dateFormat = "";
			$timeFormat = "";
			$dateCoordRow = 0;
			$dateCoordColmn = 0;
			$timeCoordRow = 0;
			$timeCoordColmn = 0;
			$dateString = "";
			$timeString = "";
			
			if($rangeClumnStart < 0)
			{
				$rangeClumnStart = 0;
			}
			if($rangeClumnEnd < 0)
			{
				$rangeClumnEnd = 0;
			}

			$dateTimePhaseLen = strlen($readSettings["dateTimePhase"][0]);
			$doBreak = false;
			for($i = $rangeRowStart; $i <= $rangeRowEnd; $i++)
			{
				for($j = $rangeClumnStart; $j <= $rangeClumnEnd; $j++)
				{
					$cellVal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getValue();
					$cellVal = trim($cellVal);

					if(substr($cellVal, 0, $dateTimePhaseLen) == $readSettings["dateTimePhase"][0])
					{
						$startingPointColumn = $j;
						$startingPointRow = $i;
						$doBreak = true;
						break;

					}

				}
				
				if($doBreak)
				{
					break;
				}
			}
			

			if(0 > $startingPointColumn && 0 > $startingPointRow)
			{
				$error = true;
			}

			if(!$error)
			{
				for($n = 0; $n < count($readSettings["dateFormats"]); $n++)
				{
					if($readSettings["dateFormats"][$n][0] == $readSettings["dateFormat"][0])
					{
						$dateFormat = $readSettings["dateFormats"][$n][1];
					}
				}
				for($n = 0; $n < count($readSettings["timeFormats"]); $n++)
				{
					if($readSettings["timeFormats"][$n][0] == $readSettings["timeFormat"][0])
					{
						$timeFormat = $readSettings["timeFormats"][$n][1];
					}
				}
				
				if("" == $dateFormat && "" == $timeFormat)
				{
					$error = true;
				}
			}

			if(!$error)
			{
				$dateCoordRow = $startingPointRow + $readSettings["dateLocation"][1];
				$dateCoordColmn = $startingPointColumn + $readSettings["dateLocation"][0];
				$timeCoordRow = $startingPointRow + $readSettings["timeLocation"][1];
				$timeCoordColmn = $startingPointColumn + $readSettings["timeLocation"][0];

				$tmp = strtolower($dateFormat);
				if($tmp != "no date" && $tmp != "no date:" && $dateCoordRow <= $hr && $dateCoordColmn <= $hc)
				{
					$dateString = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($dateCoordColmn, $dateCoordRow)->getValue();

					$monthType = "MM";
					$yearType = "YY";
					$dayType = "DD";
					$monthPos = 0;
					$monthPosI = 0;
					$dayPos = 0;
					$dayPosI = 0;
					$yearPos = 0;
					$yearPosI = 0;
					$year = "";
					$month = "";
					$day = "";

					if(stripos($dateFormat, "month"))
					{
						$monthType = "month";
					}

					if(stripos($dateFormat, "YYYY"))
					{
						$yearType = "YYYY";
					}

					$monthPos = stripos($dateFormat, $monthType);
					$dayPos = stripos($dateFormat, $dayType);
					$yearPos = stripos($dateFormat, $yearType);
					if(false !== $monthPos && false !== $dayPos && false !== $yearPos)
					{
						if($monthPos < $dayPos && $monthPos < $yearPos)
						{
							$monthPosI = 1;
							if($dayPos < $yearPos)
							{
								$dayPosI = 2;
								$yearPosI = 3;
							}
							else
							{
								$dayPosI = 3;
								$yearPosI = 2;
							}
						}
						if($dayPos < $monthPos && $dayPos < $yearPos)
						{
							$dayPosI = 1;
							if($monthPos < $yearPos)
							{
								$monthPosI = 2;
								$yearPosI = 3;
							}
							else
							{
								$monthPosI = 3;
								$yearPosI = 2;
							}
						}
						if($yearPos < $monthPos && $yearPos < $dayPos)
						{
							$yearPosI = 1;
							if($monthPos < $dayPos)
							{
								$monthPosI = 2;
								$dayPosI = 3;
							}
							else
							{
								$monthPosI = 3;
								$dayPosI = 2;
							}
						}
						
						$regEx = "";
						for($i = 1; $i <= 3; $i++)
						{
							if($regEx != "")
							{
								$regEx .= '[-\/ ,]';
							}
							if($i == $dayPosI)
							{
								$regEx .= '(';
								$regEx .= '\d{1,2}';
								$regEx .= ')';
							}
							if($i == $monthPosI)
							{
								$regEx .= '(';
								if("month" == $monthType)
								{
									print $monthType;
									$regEx .= '[a-zA-Z]{3,9}';
								}
								else
								{
									$regEx .= '\d{1,2}|[a-zA-Z]{3}';
								}
								$regEx .= ')';
							}
							if($i == $yearPosI)
							{
								$regEx .= '(';
								if($yearType == "YYYY")
								{
									$regEx .= '\d{4}';
								}
								else
								{
									$regEx .= '\d{2}';
								}
								$regEx .= ')';
							}
						}

						$regEx = '/' . $regEx . '/i';
						if(preg_match($regEx, $dateString, $matches))
						{
							$year = $matches[$yearPosI];
							$month = $matches[$monthPosI];
							$day = $matches[$dayPosI];

							if("month" == $monthType)
							{
								$month = date_parse($month);
							}
							else
							{
								if(!is_numeric($monthType))
								{
									for($j = 1; $j <= 12; $j++)
									{
										$monthName = date('M', mktime(0, 0, 0, $j, 10));
										if(strtolower($monthName) == strtolower($month))
										{
											$month = $j;
											break;
										}
									}
								}
							}
							if($year < 2000)
							{
								$year += 2000;
							}

							$date = date_create();
							date_date_set($date, $year, $month, $day);
							$arrTmp[0] = $date;
							$retVal = $arrTmp;
						}
					}
				}
				$tmp = strtolower($timeFormat);
				if($tmp != "no time" && $tmp != "no time:" && $timeCoordRow <= $hr && $timeCoordColmn <= $hc)
				{
					$timeString = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($timeCoordColmn, $timeCoordRow)->getValue();
					$timeErr = false;
					$hoursType = "H";
					$minutesType = "MM";
					$secondsType = "SS";
					$hoursPos = 0;
					$hoursPosI = 0;
					$minutesPos = 0;
					$minutesPosI = 0;
					$secondsPos = 0;
					$secondsPosI = -1;
					$hours = "";
					$minutes = "";
					$seconds = "";

					if(stripos($timeFormat, "HH"))
					{
						$hoursType = "HH";
					}
					$hoursPos = stripos($timeFormat, $hoursType);
					$minutesPos = stripos($timeFormat, $minutesType);
					$secondsPos = stripos($timeFormat, $secondsType);
					if(false === $secondsPos && false !== $hoursPos && false !== $minutesPos)
					{
						$secondsPosI = -1;
						if($hoursPos < $minutesPos)
						{
							$hoursPosI = 1;
							$minutesPosI = 2;
						}
						else
						{
							$hoursPosI = 2;
							$minutesPosI = 1;
						}
					}
					else if(false !== $hoursPos && false !== $minutesPos)
					{
						if($hoursPos < $minutesPos && $hoursPos < $secondsPos)
						{
							$hoursPosI = 1;
							if($minutesPos < $secondsPos)
							{
								$minutesPosI = 2;
								$secondsPosI = 3;
							}
							else
							{
								$minutesPosI = 3;
								$secondsPosI = 2;
							}
						}
						if($minutesPos < $hoursPos && $minutesPos < $secondsPos)
						{
							$minutesPosI = 1;
							if($hoursPos < $secondsPos)
							{
								$hoursPosI = 2;
								$secondsPosI = 3;
							}
							else
							{
								$hoursPosI = 3;
								$secondsPosI = 2;
							}
						}
						if($secondsPos < $hoursPos && $secondsPos < $minutesPos)
						{
							$secondsPosI = 1;
							if($hoursPos < $minutesPos)
							{
								$hoursPosI = 2;
								$minutesPosI = 3;
							}
							else
							{
								$hoursPosI = 3;
								$minutesPosI = 2;
							}
						}
					}
					else
					{
						$timeErr = true;
					}

					if(!$timeErr)
					{
						$regEx = "";
						$regExCnt = 3;
						if(-1 == $secondsPosI)
						{
							$regExCnt = 2;
						}
						for($i = 1; $i <= $regExCnt; $i++)
						{
							if($regEx != "")
							{
								$regEx .= ':';
							}
							if($i == $hoursPosI)
							{
								$regEx .= '(';
								$regEx .= '\d{1,2}';
								$regEx .= ')';
							}
							if($i == $minutesPosI)
							{
								$regEx .= '(';
								$regEx .= '\d{1,2}';
								$regEx .= ')';
							}
							if($i == $secondsPosI)
							{
								$regEx .= '(';
								$regEx .= '\d{1,2}';
								$regEx .= ')';
							}
						}
						$regEx .= '\s*(AM|PM)?';
						$regEx = '/' . $regEx . '/i';
						if(preg_match($regEx, $timeString, $matches))
						{
							$hours = $matches[$hoursPosI];
							$minutes = $matches[$minutesPosI];
							if(-1 < $secondsPosI)
							{
								$seconds = $matches[$secondsPosI];
							}
							else
							{
								$seconds = 0;
							}

							if(isset($matches[$regExCnt + 1]) && strtolower($matches[$regExCnt + 1]) == "pm")
							{
								$hours += 12;
							}

							$date = date_create();
							date_time_set($date, $hours, $minutes, $seconds);
							$arrTmp[1] = $date;
							$retVal = $arrTmp;
						}
					}
				}
			}
		}
		catch(Exception $e)
		{
			$error = true;
			debugMsg($e->getMessage());
		}
	}
	return $retVal;
}


function readInvProdDataFromFile($investorId, $dirPath, $date = "", $time = "", &$errMsgVar = null)
{
	$retVal = true;
	$error = false;
	$errorMessage = "";
	$settingsList = DBgetSettingsByInvestorId($investorId);
	$dirList = getDirList($dirPath);
	$invFilesList = array();
	$curDate = "";
	$curTime = "";
	
	if("" == $date)
	{
		$curDate = date("Y-m-d");
	}
	else
	{
		$curDate = $date;
	}
	if("" == $time)
	{
		$curTime = date("H:i:s");
	}
	else
	{
		$curTime = $time;
	}
	
	$iArr = DBgetInvestorById($investorId);
	$uInvestorId = $iArr[0]["investor_id"];
	foreach($dirList as $dk => $dv)
	{
		if(preg_match("/(\d{1,5})-(\d{1,3})-(\d{1,2})\.csv/i", $dv["name"], $matches))
		{
			if(intval($matches[1]) == intval($uInvestorId))
			{
				array_push($invFilesList, $dv["name"]);
			}
		}
	}

	if(count($settingsList) > 0 && count($invFilesList) > 0)
	{
		$invInf = DBgetInvestorById($investorId);
		if(count($invInf) <= 0)
		{
			$error = true;
			$errorMessage .= "couldn't retrieve information about investor<br>\n";
		}
		
		
		
		if(!$error)
		{
			$invInf = $invInf[0];
			
			$versionsList = array();
			foreach($invFilesList as $fv)
			{
				if(preg_match("/(\d{1,5})-(\d{1,3})-(\d{1,2})\.csv/i", $fv, $matches))
				{
					$addNew = true;
					foreach($versionsList as $k => $v)
					{
						if($v == $matches[2])
						{
							$addNew = false;
							break;
						}
					}
					if($addNew)
					{
						array_push($versionsList, $matches[2]);
					}
				}
			}

			$dateFormats = array();
			$timeFormats = array();
			$arrDates = DBgetDatesArr();
			$arrTimes = DBgetTimesArr();
			foreach($arrDates as $k => $v)
			{
				array_push($dateFormats, array($v["id"], $v["date_format"], ""));
			}
			foreach($arrTimes as $k => $v)
			{
				array_push($timeFormats, array($v["id"], $v["time_format"], ""));
			}
			
			$dtSettings = array
								(
									"dateFormats" => $dateFormats
									,"timeFormats" => $timeFormats
									,"dateTimePhase" => array($invInf["date_time_phase"])
									,"dateTimeRange" => array($invInf["date_time_r_start_column"], $invInf["date_time_r_start_row"], $invInf["date_time_r_end_column"], $invInf["date_time_r_end_row"])
									,"dateLocation" => array($invInf["date_location_column"], $invInf["date_location_row"])
									,"timeLocation" => array($invInf["time_location_column"], $invInf["time_location_row"])
									,"dateFormat" => $invInf["date_format_id"]
									,"timeFormat" => $invInf["time_format_id"]
								);
			
			
			$recordsToAdd = array();
			$recordsToReplace = array();
				
			foreach($versionsList as $version)
			{
				$fName = "";
				foreach($invFilesList as $fv)
				{
					if(preg_match("/(\d{1,5})-(\d{1,3})-(\d{1,2})\.csv/i", $fv, $matches))
					{
						if($matches[3] == $invInf["date_time_sheet"] && $matches[2] == $version)
						{
							$fName = $fv;
							break;
						}
					}
				}
				$fPath = $dirPath . "/" . $fName;
				if(is_file($fPath))
				{
					$dateAndtime = readDateTimeFromFile($dtSettings, $fPath);
					if(false !== $dateAndtime)
					{
						$curDate = date_format($dateAndtime[0], 'Y-m-d');
						$curTime = date_format($dateAndtime[1], 'H:i:s');
					}
					else
					{
						$error = true;
						$errorMessage .= "couldn't read date and time from investor file<br>\n";
					}
				}
				else
				{
					$error = true;
					$errorMessage .= "couldn't read date and time from investor file. File does not exist or setting for date is wrong<br>\n";
				}

				foreach($settingsList as $sk => $sv)
				{
					if($sv["useit"])
					{
						$sheetType = array(1);
						if(isset($sv["sheet_style"]) && in_array($sv["sheet_style"], array(1, 2)))
						{
							$sheetType = array($sv["sheet_style"]);
						}
						$settArr = array
						(
							"searchPhase" => array($sv["search_phase"])
							,"searchRange" => array($sv["search_r_start_column"], $sv["search_r_start_row"], $sv["search_r_end_column"], $sv["search_r_end_row"])
							,"searchSheet" => $sv["search_sheet"]
							,"startLocation" => array($sv["start_location_column"], $sv["start_location_row"])
							,"readingMap" => explode(";", $sv["reading_map"])
							,"callMap" => explode(";", $sv["cal_map"])
							,"lockDays" => explode(";", $sv["lock_days"])
							,"formulaA" => explode(";", $sv["formula_a"])
							,"formulaB" => explode(";", $sv["formula_b"])
							,"sheetType" => $sheetType
						);
						if(ctype_digit($settArr["searchRange"][0]))
						{
							$settArr["searchRange"][0] = PHPExcel_Cell::stringFromColumnIndex($settArr["searchRange"][0]);
						}
						if(ctype_digit($settArr["searchRange"][2]))
						{
							$settArr["searchRange"][2] = PHPExcel_Cell::stringFromColumnIndex($settArr["searchRange"][2]);
						}
						if(ctype_alpha($settArr["startLocation"][0]))
						{
							$settArr["startLocation"][0] = PHPExcel_Cell::columnIndexFromString($settArr["startLocation"][0]);
						}
						
						$fName = "";
						foreach($invFilesList as $fv)
						{
							if(preg_match("/(\d{1,5})-(\d{1,3})-(\d{1,2})\.csv/i", $fv, $matches))
							{
								if($matches[3] == $settArr["searchSheet"] && $matches[2] == $version)
								{
									$fName = $fv;
									break;
								}
							}
						}
						
						$fPath = $dirPath . "/" . $fName;

						//print $fPath . "\n";
						$rd = false;
						$tmpErrMsg = "";
						$rd = readData01FromFile($settArr, $fPath, $tmpErrMsg);

						if(false !== $fPath)
						{
							$lockDaysIds = array("rate");
							for($i = 1; $i < count($rd[0]); $i++)
							{
								$lockDaysIds[$i] = DBgetIdByLockdayId($rd[0][$i]);
							}
							
							$rdDB = DBgetProductRateData01(array($investorId), array($sv["product_id"]), $lockDaysIds, $curDate, $curTime);
							
							for($i = 1; $i < count($rd); $i++)
							{
								
								for($j = 1; $j < count($rd[$i]); $j++)
								{
									
									$dbId = -1;
									$repalceRecord = false;
									if(false !== $rdDB)
									{
										foreach($rdDB as $dbk => $dbv)
										{
											if	(
													$dbv["investor_id"] == $investorId
													&& $dbv["product_id"] == $sv["product_id"]
													&& $dbv["lock_day_id"] == $lockDaysIds[$j]
													&& $dbv["date"] == $curDate
													&& $dbv["time"] == $curTime
													&& $dbv["rate"] == $rd[$i][0]
												)
											{
												$dbId = $dbv["id"];
												$repalceRecord = true;
												break;
											}
										}
									}
									
									if($repalceRecord)
									{
										$recordsToReplace[] = array
																(
																	'id' => $dbId
																	,"investor_id" => $investorId
																	,"product_id" => $sv["product_id"]
																	,"lock_day_id" => $lockDaysIds[$j]
																	,"date" => $curDate
																	,"time" => $curTime
																	,"rate" => $rd[$i][0]
																	,"value" => $rd[$i][$j]
																);
									}
									else
									{
										$recordsToAdd[] = array
																(
																	'id' => 0
																	,"investor_id" => $investorId
																	,"product_id" => $sv["product_id"]
																	,"lock_day_id" => $lockDaysIds[$j]
																	,"date" => $curDate
																	,"time" => $curTime
																	,"rate" => $rd[$i][0]
																	,"value" => $rd[$i][$j]
																);
									}

								}
								
							}
						}
						else
						{
							$error = true;
							$errorMessage .= "error during reading data from file: " . $fName . ":" . $tmpErrMsg . "<br>\n";
						}
					}
				}
			}

			if(count($recordsToAdd) > 0 || count($recordsToReplace) > 0)
			{
				if(count($recordsToAdd) > 0)
				{
					if(!DBaddProductRateDataArr($recordsToAdd))
					{
						$error = true;
						$errorMessage .= "error during adding data to database <br>\n";
					}
				}
				if(count($recordsToReplace) > 0)
				{
					if(!DBreplaceProductRateDataArr($recordsToReplace))
					{
						$error = true;
						$errorMessage .= "error during adding data to database <br>\n";
					}
				}
			}
			else
			{
				$error = true;
				$errorMessage .= "no records to add to database <br>\n";
			}
			//!!!!!!!!!!!!!!!!!!!!!!!!!!!11
		}
	}
	else
	{
		$error = true;
		$errorMessage .= "there is no setting or files for investor<br>\n";
	}
	
	if(null !== $errMsgVar)
	{
		$errMsgVar = $errorMessage;
	}
	
	if($error)
	{
		$retVal = false;
	}
	
	return $retVal;
}


function getReportData($investorIds = array(), $productIds = array(), $lockdayIds = array(), $date = "", $type = 2 /*1-whole date, 2-only for specified time*/, $time = ""/*, $order = 1 /*1-investors,2-products,3-*/)
{
	$retVal = false;
	
	if(empty($investorIds))
	{
		$tmpArr = DBgetInvestorsIdsArr();
		foreach($tmpArr as $tv)
		{
			array_push($investorIds, $tv["id"]);
		}
	}
	
	if(empty($productIds))
	{
		$tmpArr = DBgetProductsIdsArr();
		foreach($tmpArr as $tv)
		{
			array_push($productIds, $tv["id"]);
		}
	}
	
	if(empty($lockdayIds))
	{
		$tmpArr = DBgetLockdaysIdsArr();
		foreach($tmpArr as $tv)
		{
			array_push($lockdayIds, $tv["id"]);
		}
	}
	
	if(empty($date))
	{
		$date = date("Y-m-d");
	}


	$repArr = DBgetProductRateData01($investorIds, $productIds, $lockdayIds, $date);

	if(2 == $type && false !== $repArr)
	{
		$invLastTimesArr = array();
		foreach($repArr as $k => $v)
		{
			if(!isset($invLastTimesArr[$v["investor_id"]]))
			{
				$invLastTimesArr[$v["investor_id"]] = DBgetLastTimeForProductRateData($v["investor_id"], $date, $time);
				if(false === $invLastTimesArr[$v["investor_id"]])
				{
					$invLastTimesArr[$v["investor_id"]] = "";
				}
			}

			if("" !== trim($v["time"]) && strtotime($v["time"]) !== strtotime($invLastTimesArr[$v["investor_id"]]))
			{
				unset($repArr[$k]);
			}
		}
		$repArr = array_values($repArr);
	}

	if(count($repArr) > 0)
	{
		$retVal = $repArr;
	}
	
	return $retVal;
}

function ipldtUsortF($a, $b)
{
	$retVal = 0;
	$key = "";
	if(isset($a["investor_id"]) && isset($b["investor_id"]))
	{
		$key = "investor_id";
	}
	else if(isset($a["product_id"]) && isset($b["product_id"]))
	{
		$key = "product_id";
	}
	else if(isset($a["lock_day_id"]) && isset($b["lock_day_id"]))
	{
		$key = "lock_day_id";
	}
	else if(isset($a["date_id"]) && isset($b["date_id"]))
	{
		$key = "date_id";
	}
	else if(isset($a["time_id"]) && isset($b["time_id"]))
	{
		$key = "time_id";
	}
	
	if(!empty($key))
	{
		$retVal = ($a[$key] < $b[$key]) ? -1 : 1;
	}
	
	return $retVal;
}

function generateGeneralSettingFile($filePath)
{
	$retVal = false;
	$error = false;
	
	try
	{
		$curRow = 1;
		$objPHPExcel = new PHPExcel();
		$objWorkSheet = $objPHPExcel->getActiveSheet();
		//$objWorkSheet->setTitle($invName);
		
		$invArr = DBgetInvestorsArr(false);
		$prodArr = DBgetProductsArr(false);
		$ldArr = DBgetLockdaysArr();
		$datesArr = DBgetDatesArr();
		$timesArr = DBgetTimesArr();
		
		usort($invArr, "ipldtUsortF");
		usort($prodArr, "ipldtUsortF");
		usort($ldArr, "ipldtUsortF");
		usort($datesArr, "ipldtUsortF");
		usort($timesArr, "ipldtUsortF");
		
		
		$objWorkSheet->SetCellValue('A' . $curRow, "Investors");
		foreach($invArr as $k => $v)
		{
			$objWorkSheet->SetCellValue('B' . $curRow, $v["investor_id"]);
			$objWorkSheet->SetCellValue('C' . $curRow, $v["investor_name"]);
			$curRow++;
		}
		
		$curRow += 2;
		$objWorkSheet->SetCellValue('A' . $curRow, "loan product list");
		foreach($prodArr as $k => $v)
		{
			$objWorkSheet->SetCellValue('B' . $curRow, $v["product_id"]);
			$objWorkSheet->SetCellValue('C' . $curRow, $v["product_name"]);
			$curRow++;
		}
		
		$curRow += 2;
		$objWorkSheet->SetCellValue('A' . $curRow, "lock days list");
		foreach($ldArr as $k => $v)
		{
			$objWorkSheet->SetCellValue('B' . $curRow, $v["lock_day_id"]);
			$objWorkSheet->SetCellValue('C' . $curRow, $v["lock_day"]);
			$curRow++;
		}
		
		$curRow += 2;
		$objWorkSheet->SetCellValue('A' . $curRow, "date format");
		foreach($datesArr as $k => $v)
		{
			$objWorkSheet->SetCellValue('B' . $curRow, $v["date_id"]);
			$objWorkSheet->SetCellValue('C' . $curRow, $v["date_format"]);
			$objWorkSheet->SetCellValue('D' . $curRow, $v["example"]);
			$curRow++;
		}
		
		$curRow += 2;
		$objWorkSheet->SetCellValue('A' . $curRow, "time format");
		foreach($timesArr as $k => $v)
		{
			$objWorkSheet->SetCellValue('B' . $curRow, $v["time_id"]);
			$objWorkSheet->SetCellValue('C' . $curRow, $v["time_format"]);
			$objWorkSheet->SetCellValue('D' . $curRow, $v["example"]);
			$curRow++;
		}
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($filePath);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg($e->getMessage());
	}
	
	if(!$error)
	{
		$retVal = true;
	}

	return $retVal;
}

function generateReport01($repData, $repDate, $repTime, $filePath)
{
	$retVal = false;
	$error = false;
/**/
	try
	{
		$date = date("Y-m-d", strtotime($repDate));
		$newSheet = false;
		$newLockday = false;
		$sheetCount = 0;
		$curInvId = -1;
		$curProdId = -1;
		$curRate = -1;
		$curLockdayId = -1;
		$curValue = -1;
		$curTime = "";
		$prevInvId = -1;
		$prevProdId = -1;
		$prevRate = -1;
		$prevLockdayId = -1;
		$lockdayName = "";
		$invName = "";
		$invIndex = "";
		$prodName = "";
		$curProdInvestorsArr = array();
		$curRow = 1;
		$objPHPExcel = new PHPExcel();
			
		$invArr = getInvestorsArrWithIdAsKey(true);
		$prodArr = getProductsArrWithIdAsKey(true);
		$ldArr = getLockdaysArrWithIdAsKey(true);

		for($i = 0; $i < count($repData); $i++)
		{
			for($j = $i + 1; $j < count($repData); $j++)
			{
				if
					(
						$repData[$j]["product_id"] < $repData[$i]["product_id"]
						|| $repData[$j]["product_id"] == $repData[$i]["product_id"] && $repData[$j]["lock_day_id"] < $repData[$i]["lock_day_id"]
						|| $repData[$j]["product_id"] == $repData[$i]["product_id"] && $repData[$j]["lock_day_id"] == $repData[$i]["lock_day_id"] && $repData[$j]["rate"] < $repData[$i]["rate"]
						|| $repData[$j]["lock_day_id"] == $repData[$i]["lock_day_id"] && $repData[$j]["product_id"] == $repData[$i]["product_id"] && $repData[$j]["rate"] == $repData[$i]["rate"] && $repData[$j]["investor_id"] < $repData[$i]["investor_id"]
					)
				{
					$tmp = $repData[$j];
					for($n = $j; $n > $i; $n--)
					{
						$repData[$n] = $repData[$n - 1];
					}
					$repData[$n] = $tmp;
				}
			}
				
			if(!isset($prodArr[$repData[$i]["product_id"]]["investor_count"]))
			{
				$prodArr[$repData[$i]["product_id"]]["investor_count"] = array();
			}
			if(!isset($prodArr[$repData[$i]["product_id"]]["investor_count"][$repData[$i]["investor_id"]]))
			{
				$prodArr[$repData[$i]["product_id"]]["investor_count"][$repData[$i]["investor_id"]] = 0;
			}
			if(!isset($invArr[$repData[$i]["investor_id"]][$repData[$i]["product_id"]]))
			{
				$invArr[$repData[$i]["investor_id"]][$repData[$i]["product_id"]] = array();
			}
			if(!isset($invArr[$repData[$i]["investor_id"]][$repData[$i]["product_id"]]["data_time"]))
			{
				$invArr[$repData[$i]["investor_id"]][$repData[$i]["product_id"]]["data_time"] = $repData[$i]["time"];
			}
			$prodArr[$repData[$i]["product_id"]]["investor_count"][$repData[$i]["investor_id"]]++;
		}

		for($i = 0; $i < count($repData); $i++)
		{
			$curInvId = $repData[$i]["investor_id"];
			$curProdId = $repData[$i]["product_id"];
			$curRate = $repData[$i]["rate"];
			$curLockdayId = $repData[$i]["lock_day_id"];
			$curValue = $repData[$i]["value"];
			$curTime = date("h:i A", strtotime($repData[$i]["time"]));

			
			
			if($prevProdId != $curProdId)
			{
				$prodName = "";
				if(isset($prodArr[$curProdId]["product_name"]))
				{
					$prodName = $prodArr[$curProdId]["product_name"];
				}
				$newSheet = true;
			}
			if($prevProdId != $curProdId || $prevLockdayId != $curLockdayId)
			{
				$lockdayName = "";
				if(isset($ldArr[$curLockdayId]["lock_day"]))
				{
					$lockdayName = $ldArr[$curLockdayId]["lock_day"];
				}
				$newLockday = true;
			}
			if($prevProdId != $curProdId || $prevLockdayId != $curLockdayId || $prevRate != $curRate)
			{
				$newRate = true;
			}
			
			if($newSheet)
			{
				if($sheetCount > 0)
				{
					$objWorkSheet = $objPHPExcel->createSheet($sheetCount);
				}
				$objPHPExcel->setActiveSheetIndex($sheetCount);
				$objWorkSheet = $objPHPExcel->getActiveSheet();
				$objWorkSheet->setTitle(sanitizeFileName($prodName));
				$sheetCount++;
				
				$objWorkSheet->SetCellValue('A1', "product");
				$objWorkSheet->SetCellValue('B1', $prodName);
				$objWorkSheet->getStyle("A1:B1")->getFont()->setBold(true);
				
				$curRow = 2;
				$curProdInvestorsArr = array();
				$investorCount = 0;
				foreach($invArr as $k => $v)
				{
					if(isset($prodArr[$curProdId]["investor_count"][$v["id"]]) && $prodArr[$curProdId]["investor_count"][$v["id"]] > 0)
					{
						$curProdInvestorsArr[$v["id"]] = $investorCount;
						$investorCount++;
					}
				}
				$newSheet = false;
			}
			
			if($newLockday)
			{
				$curRow += 2;
				$objWorkSheet->SetCellValue("A" . $curRow, $date . ", " . date("H:i:s") . ", " . $prodName . ", " . $lockdayName);
				$curRow++;
				$objWorkSheet->SetCellValue("A" . ($curRow + 1), "time");
				foreach($curProdInvestorsArr as $k => $v)
				{
					$column = PHPExcel_Cell::stringFromColumnIndex($v + 1);
					$objWorkSheet->SetCellValue($column . $curRow, $invArr[$k]["investor_name"]);
					$ciTime = "";
					if(isset($invArr[$k][$curProdId]["data_time"]))
					{
						$ciTime = $invArr[$k][$curProdId]["data_time"];
					}
					$objWorkSheet->SetCellValue($column . ($curRow + 1), $ciTime);
				}
				
				
				$BStyle = array
							(
								'borders' => array(
								'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
								)
							);
				$column = PHPExcel_Cell::stringFromColumnIndex(count($curProdInvestorsArr));
				$objWorkSheet->getStyle('A' . ($curRow - 1) . ':' . $column . ($curRow + 1))->applyFromArray($BStyle);
				
				$curRow++;
				//$objWorkSheet->SetCellValue("B" . $curRow, $prodName);
				$newLockday = false;
			}
			if($newRate)
			{
				$curRow++;
				$objWorkSheet->SetCellValue("A" . $curRow, $curRate);
				$BStyle = array
							(
								'borders' => array(
								'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
								)
							);
				$column = PHPExcel_Cell::stringFromColumnIndex(count($curProdInvestorsArr));
				$objWorkSheet->getStyle('A' . ($curRow) . ':' . $column . ($curRow))->applyFromArray($BStyle);
				$newRate = false;
			}
			
			$column = PHPExcel_Cell::stringFromColumnIndex($curProdInvestorsArr[$curInvId] + 1);
			$objWorkSheet->SetCellValue($column . $curRow, $curValue);

			$prevInvId = $curInvId;
			$prevProdId = $curProdId;
			$prevRate = $curRate;
			$prevLockdayId = $curLockdayId;
			
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($filePath);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg($e->getMessage());
	}
	
	if(!$error)
	{
		$retVal = true;
	}
/**/
	return $retVal;
}	

function generateReport02($repData, $repDate, $repTime, $filePath, $order = 1)
{
	$retVal = false;
	$error = false;

	try
	{
		$newSheet = false;
		$newProd = false;
		$newRate = false;
		$sheetCount = 0;
		$curInvId = -1;
		$curProdId = -1;
		$curRate = -1;
		$curLockdayId = -1;
		$curValue = -1;
		$curTime = "";
		$prevInvId = -1;
		$prevProdId = -1;
		$prevRate = -1;
		$invName = "";
		$invIndex = "";
		$prodName = "";
		$curInvLockdaysArr = array();
		$curRow = 1;
		$objPHPExcel = new PHPExcel();
			
		$invArr = getInvestorsArrWithIdAsKey(true);
		$prodArr = getProductsArrWithIdAsKey(true);
		$ldArr = getLockdaysArrWithIdAsKey(true);
		if(1 == $order)
		{
			for($i = 0; $i < count($repData); $i++)
			{
				for($j = $i + 1; $j < count($repData); $j++)
				{
					if
						(
							$repData[$j]["investor_id"] < $repData[$i]["investor_id"]
							|| ($repData[$j]["investor_id"] == $repData[$i]["investor_id"] && $repData[$j]["product_id"] < $repData[$i]["product_id"])
							|| ($repData[$j]["investor_id"] == $repData[$i]["investor_id"] && $repData[$j]["product_id"] == $repData[$i]["product_id"] && $repData[$j]["rate"] < $repData[$i]["rate"])
							|| ($repData[$j]["investor_id"] == $repData[$i]["investor_id"] && $repData[$j]["product_id"] == $repData[$i]["product_id"] && $repData[$j]["rate"] == $repData[$i]["rate"] && $repData[$j]["lock_day_id"] < $repData[$i]["lock_day_id"])
						)
					{
						$tmp = $repData[$j];
						for($n = $j; $n > $i; $n--)
						{
							$repData[$n] = $repData[$n - 1];
						}
						$repData[$n] = $tmp;
					}
				}
				
				if(!isset($invArr[$repData[$i]["investor_id"]]["lockday_count"]))
				{
					$invArr[$repData[$i]["investor_id"]]["lockday_count"] = array();
				}
				if(!isset($invArr[$repData[$i]["investor_id"]]["lockday_count"][$repData[$i]["lock_day_id"]]))
				{
					$invArr[$repData[$i]["investor_id"]]["lockday_count"][$repData[$i]["lock_day_id"]] = 0;
				}
				$invArr[$repData[$i]["investor_id"]]["lockday_count"][$repData[$i]["lock_day_id"]]++;
			}

			for($i = 0; $i < count($repData); $i++)
			{
				$curInvId = $repData[$i]["investor_id"];
				$curProdId = $repData[$i]["product_id"];
				$curRate = $repData[$i]["rate"];
				$curLockdayId = $repData[$i]["lock_day_id"];
				$curValue = $repData[$i]["value"];
				//$curTime = $repData[$i]["time"];
				$curTime = date("h:i A", strtotime($repData[$i]["time"]));
				
				if($prevInvId != $curInvId)
				{
					$invName = "";
					$invIndex = "";
					if(isset($invArr[$curInvId]["investor_name"]))
					{
						$invName = $invArr[$curInvId]["investor_name"];
					}
					if(isset($invArr[$curInvId]["investor_id"]))
					{
						$invIndex = $invArr[$curInvId]["investor_id"];
					}
					$newSheet = true;
				}
				if($prevProdId != $curProdId || $prevInvId != $curInvId)
				{
					$prodName = "";
					if(isset($prodArr[$curProdId]["product_name"]))
					{
						$prodName = $prodArr[$curProdId]["product_name"];
					}
					$newProd = true;
				}
				if($prevRate != $curRate || $prevProdId != $curProdId || $prevInvId != $curInvId)
				{
					$newRate = true;
				}
				
				if($newSheet)
				{
					if($sheetCount > 0)
					{
						$objWorkSheet = $objPHPExcel->createSheet($sheetCount);
					}
					$objPHPExcel->setActiveSheetIndex($sheetCount);
					$objWorkSheet = $objPHPExcel->getActiveSheet();
					$objWorkSheet->setTitle(sanitizeFileName($invName));
					$sheetCount++;
					
					$objWorkSheet->SetCellValue('A1', "investor name");
					$objWorkSheet->SetCellValue('B1', $invName);
					$objWorkSheet->SetCellValue('A2', "investor index");
					$objWorkSheet->SetCellValue('B2', $invIndex);
					$objWorkSheet->SetCellValue('A3', "calculated");
					$objWorkSheet->SetCellValue('B3', "1");
					$objWorkSheet->SetCellValue('A5', "Date");
					$objWorkSheet->SetCellValue('B5', $repDate);
					$objWorkSheet->SetCellValue('A6', "Time");
					$objWorkSheet->SetCellValue('B6', $curTime);
					$objWorkSheet->SetCellValue('A8', "price data");
					$objWorkSheet->SetCellValue('A9', "rate");
					$curRow = 9;
					$curInvLockdaysArr = array();
					$lockdayCount = 0;
					foreach($ldArr as $k => $v)
					{
						if(isset($invArr[$curInvId]["lockday_count"][$v["lock_day_id"]]) && $invArr[$curInvId]["lockday_count"][$v["lock_day_id"]] > 0)
						{
							$curInvLockdaysArr[$v["lock_day_id"]] = $lockdayCount;
							$column = PHPExcel_Cell::stringFromColumnIndex($lockdayCount + 1);
							$lockdayName = "";
							if(isset($ldArr[$v["lock_day_id"]]["lock_day"]))
							{
								$lockdayName = $ldArr[$v["lock_day_id"]]["lock_day"];
							}
							$objWorkSheet->SetCellValue($column . "9", $lockdayName);
							$lockdayCount++;
						}
					}
					$newSheet = false;
				}
				
				if($newProd)
				{
					$curRow += 2;
					$objWorkSheet->SetCellValue("A" . $curRow, $prodArr[$curProdId]["product_id"]);
					$objWorkSheet->SetCellValue("B" . $curRow, $prodName);
					$newProd = false;
				}
				
				if($newRate)
				{
					$curRow++;
					$objWorkSheet->SetCellValue("A" . $curRow, $curRate);
					$newRate = false;
				}
				
				$column = PHPExcel_Cell::stringFromColumnIndex($curInvLockdaysArr[$curLockdayId] + 1);
				$objWorkSheet->SetCellValue($column . $curRow, $curValue);

				$prevInvId = $curInvId;
				$prevProdId = $curProdId;
				$prevRate = $curRate;
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($filePath);
	}
	catch(Exception $e)
	{
		$error = true;
		debugMsg($e->getMessage());
	}
	
	if(!$error)
	{
		$retVal = true;
	}

	return $retVal;
}



function debugMsg($msg = "")
{
	global $settingsDebug_m;
	
	if($settingsDebug_m)
	{
		print "<br>" . $msg . "<br>\r\n";
		print "<pre>";
		debug_print_backtrace();
		print "</pre>";
	}
}

?>