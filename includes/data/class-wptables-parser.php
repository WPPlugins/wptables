<?php 
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
abstract class WPTables_Parser {

	protected function create_fields($header) {
		$fields = array();
		foreach ($header as $field) {
			$fields[] = $this->create_field($field, 'text');
		}
		return $fields;
	}

	protected function create_field($name, $type, $title = null) {
		$output = array();
		$output['name'] = $name;
		$output['title'] = $title ? $title : $name;
		$output['type'] = $type;
		$output['visible'] = true;
		return $output;
	}
}

?>