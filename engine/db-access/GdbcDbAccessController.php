<?php
/**
 * Copyright (C) 2015 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

final class GdbcDbAccessController
{
	public static $LAST_INSERTED_ATTEMPT_ID = 0;

	private static function sanitizeEntityNotes($entityNote)
	{
		if(is_string($entityNote))
		{
			return wp_filter_kses(wp_check_invalid_utf8($entityNote));
		}

		if(is_array($entityNote))
		{
			return array_map( array( __CLASS__, 'sanitizeEntityNotes' ), $entityNote );
		}

		return $entityNote;
	}

	public static function registerAttempt(GdbcAttemptEntity $attemptEntity)
	{
		self::$LAST_INSERTED_ATTEMPT_ID = 0;

		if(empty($attemptEntity->ModuleId) || empty($attemptEntity->SiteId) || empty($attemptEntity->CreatedDate) || empty($attemptEntity->ReasonId))
			return;

		unset($attemptEntity->Id);

		if(is_array($attemptEntity->Notes))
		{
			$attemptEntity->Notes = array_filter($attemptEntity->Notes);

			$tokenFieldName   = GdbcSettingsPublicModule::getInstance()->getOption(GdbcSettingsAdminModule::OPTION_HIDDEN_INPUT_NAME);
			$browserInputName = GdbcRequestController::getPostedBrowserInfoInputName();

			$arrKeysToUnset = array($tokenFieldName, $browserInputName, strtolower($tokenFieldName), strtolower($browserInputName), strtoupper($tokenFieldName), strtoupper($browserInputName), '_wpnonce');
			foreach($arrKeysToUnset as $keyName)
			{
				unset($attemptEntity->Notes[$keyName]);
			}
		}

		$attemptEntity->Notes = !empty($attemptEntity->Notes) ? maybe_serialize(self::sanitizeEntityNotes($attemptEntity->Notes)) : null;
		global $wpdb;

		return self::$LAST_INSERTED_ATTEMPT_ID = (false === $wpdb->insert(self::getAttemptsTableName(), array_filter((array)$attemptEntity))) ? 0 : $wpdb->insert_id;
	}


	public static function getCombinedAttemptsPerDay($numberOfDays)
	{
		$createdDateTime = self::getDaysAgoTimeStamp($numberOfDays);

		global $wpdb;

		$sqlQuery  = "SELECT COUNT(1) AS AttemptsNumber, FROM_UNIXTIME(CreatedDate, '%%Y-%%m-%%d') as CreatedDate FROM " . self::getAttemptsTableName() . " WHERE CreatedDate >= %d ";
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? '' : ' AND SiteId = %d ';
		$sqlQuery .= " GROUP BY FROM_UNIXTIME(CreatedDate, '%%Y-%%m-%%d') ORDER BY CreatedDate ";

		$arrParams = array($createdDateTime);
		GoodByeCaptcha::isNetworkActivated() ? null : $arrParams[] = get_current_blog_id();

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

	}

	public static function getLatestAttempts($numberOfAttempts)
	{
		global $wpdb;

		$sqlQuery  = 'SELECT * FROM ' . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? '' : ' WHERE SiteId = %d';
		$sqlQuery .= ' ORDER BY CreatedDate DESC LIMIT 0, %d';

		$arrParams = array();
		GoodByeCaptcha::isNetworkActivated() ? null : $arrParams[] = get_current_blog_id();

		$arrParams[] = (int)$numberOfAttempts;

		$latestAttemptsList = (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

		return $latestAttemptsList;
	}

	public static function getAttemptsByClientIp($pageNumber, $recordsPerPage = 10)
	{
		global $wpdb;

		$pageNumber = abs((int)$pageNumber);
		$pageNumber < 1 ? $pageNumber = 1 : null;

		$sqlQuery  = "SELECT COUNT(1) AS Attempts, ClientIp FROM " . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d ' : ' WHERE SiteId = %d ';
		$sqlQuery .= 'GROUP BY ClientIp ORDER BY Attempts DESC LIMIT %d, %d';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();
		$arrParams[] = ($pageNumber - 1) * $recordsPerPage;
		$arrParams[] = $recordsPerPage;

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

	}

	public static function getTotalAttemptsPerModule()
	{
		global $wpdb;
		$sqlQuery = 'SELECT ModuleId, COUNT(1) as Total FROM ' . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d' : ' WHERE SiteId = %d';
		$sqlQuery .= ' GROUP BY ModuleId';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));
	}

	public static function getAttemptsPerModuleAndDay($numberOfDays)
	{
		global $wpdb;
		$createdDateTime = self::getDaysAgoTimeStamp($numberOfDays) + 1;

		$sqlQuery = "SELECT ModuleId, FROM_UNIXTIME(CreatedDate, '%%Y-%%m-%%d') AS AttemptDate, COUNT(1) AS AttemptsNumber FROM " . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d ' : ' WHERE SiteId = %d ';
		$sqlQuery .= " AND CreatedDate > $createdDateTime GROUP BY ModuleId, AttemptDate ORDER BY ModuleId ASC, AttemptDate DESC";

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));
	}

	public static function getNumberOfAttemptsByModuleId($moduleId)
	{
		global $wpdb;
		$sqlQuery = 'SELECT COUNT(1) AS Total FROM ' . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d ' : ' WHERE SiteId = %d ';
		$sqlQuery .= ' AND ModuleId = %d ';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();
		$arrParams[] = $moduleId;

		$arrTotalAttempts = self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

		return isset($arrTotalAttempts[0]) ? (int)$arrTotalAttempts[0]->Total : 0;

	}

	public static function getAttemptsPerModule($moduleId, $pageNumber, $recordsPerPage)
	{
		global $wpdb;

		$pageNumber = abs((int)$pageNumber);
		$pageNumber < 1 ? $pageNumber = 1 : null;

		$sqlQuery  = "SELECT * FROM " . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d ' : ' WHERE SiteId = %d ';
		$sqlQuery .= ' AND ModuleId = %d ORDER BY CreatedDate DESC LIMIT %d, %d';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();
		$arrParams[] = $moduleId;

		$arrParams[] = ($pageNumber - 1) * $recordsPerPage;
		$arrParams[] = $recordsPerPage;

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));
	}


	public static function getAttemptsPerModuleOlderThan($moduleId, $numberOfDaysAgo, $pageNumber, $recordsPerPage)
	{
		global $wpdb;

		$pageNumber = abs((int)$pageNumber);
		$pageNumber < 1 ? $pageNumber = 1 : null;

		$createdDateTime = self::getDaysAgoTimeStamp($numberOfDaysAgo);

		$sqlQuery  = "SELECT * FROM " . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d ' : ' WHERE SiteId = %d ';
		$sqlQuery .= ' AND CreatedDate < %d ';
		$sqlQuery .= ' AND ModuleId = %d ORDER BY CreatedDate DESC LIMIT %d, %d';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		$arrParams[] = $createdDateTime;
		$arrParams[] = $moduleId;

		$arrParams[] = ($pageNumber - 1) * $recordsPerPage;
		$arrParams[] = $recordsPerPage;

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));
	}



	public static function getAttemptById($attemptId)
	{
		global $wpdb;
		$sqlQuery = 'SELECT * FROM ' . self::getAttemptsTableName() . '  WHERE Id = %d';
		$arrGdbcAttempts = self::executePreparedQuery($wpdb->prepare($sqlQuery, $attemptId));
		return isset($arrGdbcAttempts[0]) ? $arrGdbcAttempts[0] : 0;
	}

	public static function getAttemptsPerModuleAndSection()
	{
		global $wpdb;
		$sqlQuery = 'SELECT ModuleId, SectionId, Count(ModuleId) AS Attempts FROM ' . self::getAttemptsTableName();
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' WHERE Id > %d' : ' WHERE SiteId = %d';
		$sqlQuery .= ' GROUP BY ModuleId, SectionId';

		$arrParams = array();
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		return (array)self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));


	}

	private static function getDaysAgoTimeStamp($numberOfDays)
	{
		return MchGdbcHttpRequest::getServerRequestTime() - ( absint($numberOfDays) * 3600 * 24 );
	}

	private static function executePreparedQuery($sqlQuery)
	{
		global $wpdb;
		return null !== ($queryResult = $wpdb->get_results($sqlQuery)) ? $queryResult : array();
	}

	public static function createAttemptsTable()
	{
		global $wpdb;
		if(self::attemptsTableExists())
			return false;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$createTableStatement = "CREATE TABLE " . self::getAttemptsTableName() . " (
								  Id int unsigned NOT NULL auto_increment,
								  CreatedDate int unsigned NOT NULL,
								  SiteId smallint unsigned NOT NULL,
								  ClientIp varchar(45) DEFAULT NULL,
								  ModuleId tinyint unsigned NOT NULL,
								  SectionId tinyint unsigned default 0,
								  ReasonId tinyint unsigned NOT NULL,
								  Notes longtext NOT NULL DEFAULT '',
								  PRIMARY KEY  (Id),
								  KEY idx_gdbc_CreatedDateSiteId (CreatedDate, SiteId)
								)"; //KEY index_gdbc_ClientIp (ClientIp)

		$createTableStatement .= !empty($wpdb->charset) ? " DEFAULT CHARACTER SET {$wpdb->charset}" : '';
		$createTableStatement .= !empty($wpdb->collate) ? " COLLATE {$wpdb->collate}"               : '';

		$result = dbDelta($createTableStatement);

		return !empty($result) ? true : false;

	}



	public static function deleteAttemptsOlderThan($numberOfDays)
	{
		if(empty($numberOfDays))
			return;

		$createdDateTime = self::getDaysAgoTimeStamp($numberOfDays);

		global $wpdb;
		$sqlQuery  = 'DELETE FROM ' . self::getAttemptsTableName() . ' WHERE CreatedDate < %d ';
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' AND Id > %d ' : ' AND SiteId = %d ';
		$sqlQuery .= 'LIMIT 10000';

		$arrParams = array($createdDateTime);
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

	}

	public static function clearAttemptsNotesOlderThan($numberOfDays)
	{
		if(empty($numberOfDays))
			return;

		$createdDateTime = self::getDaysAgoTimeStamp($numberOfDays);

		global $wpdb;
		$sqlQuery  = 'UPDATE ' . self::getAttemptsTableName() . " SET Notes = '' WHERE CreatedDate < %d AND (Notes <> '' OR Notes IS NOT NULL )";
		$sqlQuery .= GoodByeCaptcha::isNetworkActivated() ? ' AND Id > %d ' : ' AND SiteId = %d ';
		$sqlQuery .= 'LIMIT 1000';

		$arrParams = array($createdDateTime);
		$arrParams[] = GoodByeCaptcha::isNetworkActivated() ? 0 : get_current_blog_id();

		self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

	}


	public static function getLatestLoginAttempts($numberOfSecondsAgo, $calculateAverage = false)
	{
		global $wpdb;

		$averageStatement = ($calculateAverage) ? 'FORMAT( (MAX(CreatedDate) - MIN(CreatedDate)) / (Count(1) - 1) , 2) As TimeAverage,' : '';

		$sqlQuery  = "SELECT Count(1) AS Hits, $averageStatement ClientIp FROM " .  self::getAttemptsTableName();
		$sqlQuery .= ' WHERE CreatedDate > %d ' . ( GoodByeCaptcha::isNetworkActivated() ? ' AND SiteId = %d' : '');
		$sqlQuery .= ' AND ( ';

		foreach(GoodByeCaptchaUtils::getAllPossibleLoginAttemptEntities() as $gdbcAttempt){
			$sqlQuery .= '(ModuleId = ' . $gdbcAttempt->ModuleId . ' AND SectionId = ' . $gdbcAttempt->SectionId . ') OR ';
		}

		$sqlQuery = substr($sqlQuery, 0, -4)  . ' ) GROUP BY ClientIp';

		$arrParams = array(
			MchGdbcHttpRequest::getServerRequestTime() - $numberOfSecondsAgo - 2,
		);

		if(GoodByeCaptcha::isNetworkActivated()){
			$arrParams[] = get_current_blog_id();
		}

		return self::executePreparedQuery($wpdb->prepare($sqlQuery, $arrParams));

	}


	public static function attemptsTableExistsAndIsEmpty()
	{
		if(!self::attemptsTableExists())
			return false;

		global $wpdb;
		$arrFirstId = $wpdb->get_col('SELECT Id FROM ' . self::getAttemptsTableName() . ' LIMIT 1');
		return empty($arrFirstId);
	}

	public static function attemptsTableExists()
	{
		global $wpdb;
		return ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", self::getAttemptsTableName())) === self::getAttemptsTableName());
	}


	public static function getAttemptsTableName()
	{
		global $wpdb;
		return $wpdb->base_prefix . 'gdbc_attempts';
	}


	public static function getLoginAttempts($lastNumberOfSeconds)
	{
//		SELECT Count(1) AS Hits ,   ((MAX(CreatedDate) - MIN(CreatedDate)) / (Count(1) - 1))  As Average, ClientIp
//		from wp_gdbc_attempts qa where  qa.CreatedDate > (1443473534 - (1 * 60)) GROUP BY ClientIp;
	}

	private function __construct()
	{}
}