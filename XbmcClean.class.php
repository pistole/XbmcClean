<?php
require_once(dirname(dirname(__FILE__)) . '/DbWrapper/DbWrapper.class.php');
class XbmcClean
{
	const SMB_PATH = 'smb://veda/vids/';
	const BASE_PATH = '/Volumes/vids/';
	
	private $dbConfig = array(
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'db' => 'xbmc_video',
		);
	
	public function __construct()
	{
		DbWrapper::setConfig($this->dbConfig);
	}
	
	protected function mysqlDate($date)
	{
		return date('Y-m-d H:i:s', $date);
	}	
	
	public function getViewedFiles()
	{
		$db = DbWrapper::getInstance();
		$sql = '
			SELECT 
				strFilename, 
				strPath, 
				playCount, 
				lastPlayed  
			FROM 
				files 
				INNER JOIN path 
					ON files.idPath = path.idPath 
			WHERE 
				playcount IS NOT NULL
				AND lastPlayed < DATE_SUB(NOW(), INTERVAL 1 DAY)		
		';
		
		$filenames = array();
		$result = $db->query($sql);
		while ($row = $result->getRow())
		{
			if (strpos($row['strPath'], 'C:') === 0)
			{
				continue;
			}
			$stackedNames = explode(',',$row['strFilename']);
			
			foreach ($stackedNames as $stackName)
			{
				$stackName = str_replace('stack://', '', $stackName);
				if (strpos($stackName, 'smb://') === FALSE)
				{
					$stackName = $row['strPath'] . $stackName;
				}
				$stackName = str_replace(self::SMB_PATH, self::BASE_PATH, $stackName);
				$filenames[] = $stackName;
			}
		}
		return $filenames;
	}
	
	
}