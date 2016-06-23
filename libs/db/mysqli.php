<?php
/**
 * This file contains the Backup_Database class wich performs
 * a partial or complete backup of any given MySQL database
 * @author Daniel Lиоpez Aza?a <http://www.azanweb.com-->
 * @version 1.0
 */
 
/**
 * The backup_database class
 */
class backup_database {
	private $db;
	
	function __construct($db) {
		$this->db = $db;
	}
	
    /**
     * Backup the whole database or just some tables
     * Use '*' for whole database or 'table1,table2,table3...'
     * @param string $tables
     */
    public function backup_tables($tables = '*', $outputDir = '.')
	{
		try {
			if($tables == '*')
			{
				$tables = array();
				$stmt = $this->db->prepare("SHOW TABLES");
				$stmt->execute();
				
				while ($result = $stmt->fetch(PDO::FETCH_NUM)) {
					$tables[] = $result[0];
				}
			}
			else
			{
				$tables = is_array($tables) ? $tables : explode(',',$tables);
			}
			
			$stmt = $this->db->prepare("SELECT DATABASE()");
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_NUM);
			
			$sql = 'CREATE DATABASE IF NOT EXISTS '.$result[0].";\n\n";
			$sql .= 'USE '.$result[0].";\n\n";
			
			foreach($tables as $table)
			{
				$sql .= 'DROP TABLE IF EXISTS '.$table.';';
				$stmt = $this->db->prepare("SHOW CREATE TABLE " . $table);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_NUM);
				$sql.= "\n\n".$result[1].";\n\n";
				
				$stmt = $this->db->prepare("SELECT * FROM " . $table);
				$stmt->execute();
								
				$result = array();
				while ($row = $stmt->fetch()) {
					$sql .= 'INSERT INTO '.$table.' VALUES(';
					foreach($row as $v) {
						$value = preg_replace("/\n/",'\n', addslashes($v));
						$sql .= isset($value) ? '"'.$value.'",' : '"",';
					}
					
					$sql = rtrim($sql, ",");
					$sql .= ");\n";
				}

				$sql.="\n\n\n";
			}
		}catch (PDOException $e) {
			error_log($e->getMessage());
			return false;
		}
		
        return $this->save_file($sql, $outputDir);
    }

    /**
     * Save SQL to file
     * @param string $sql
     */
    private function save_file(&$sql, $outputDir = '.')
    {
        if (!$sql) return false;

        try
        {
			$gzdata = gzencode($sql, 9);
            $handle = fopen($outputDir.'/db-backup-'.date("Ymd-His", time()).'.sql.gz','w+');
            fwrite($handle, $gzdata);
            fclose($handle);
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }
}
?>