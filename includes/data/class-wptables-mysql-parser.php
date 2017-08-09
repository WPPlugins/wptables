<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_MySqlParser extends WPTables_Parser {
	public function parse_table($table_name) {
		global $wpdb;
		$header = $wpdb->get_col( "DESC " . $table_name, 0);
		return array(
			"fields"=> $this->create_fields($header),
			"data"	=> $table_name
		);
	}

	public function parse_query($query) {
		global $wpdb;
		$results = $wpdb->get_results( $query, ARRAY_A );
		$header = array();
		$row = array_shift($results);
		foreach ($row as $name => $value) {
			$header[] = $name;
		}
		return array(
			"fields"=> $this->create_fields($header),
			"data"	=> $query
		);
	}
}
?>
