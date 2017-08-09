<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_CsvParser extends WPTables_Parser {
	public function parse_text($text) {
		$lines = explode(PHP_EOL, $text);
		$header = $this->parse_header(array_shift($lines));
		$data = array();
		foreach ($lines as $line) {
			$data[] = $this->parse_row($line, $header);
		}		
		return array(
			"fields"=> $this->create_fields($header),
			"data"	=> $data
		);
	}

	public function parse_file($file) {
		$handle = fopen($file, "r");
		if ($handle) {
			$header = $this->parse_header(fgets($handle));
			$data = array();
		    while (($line = fgets($handle)) !== false) {
		        $data[] = $this->parse_row($line, $header);
		    }
		    fclose($handle);
		    return array(
				"fields"=> $this->create_fields($header),
				"data"	=> $data
			);
		} else {
		    return false;
		} 
	}

	private function parse_header($line) {
		return str_getcsv($line);
	}

	private function parse_row($line, $header) {
		$row = str_getcsv($line);
		return array_combine($header, $row);
	}
}
?>
