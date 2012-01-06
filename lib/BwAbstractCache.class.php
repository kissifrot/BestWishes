<?php
/**
 * Cache mamagement class, borrowed from CakePHP 2.0 ;)
 */
abstract class BwAbstractCache
{
	public $settings = array();

	public function init($settings = array()) {
		$this->settings = array_merge(
			array('prefix' => 'bw_', 'duration' => 3600, 'probability' => 100),
			$this->settings,
			$settings
		);
		if (!is_numeric($this->settings['duration'])) {
			$this->settings['duration'] = strtotime($this->settings['duration']) - time();
		}
		return true;
	}

	public function gc() { }

	abstract public function write($key, $value, $duration);

	abstract public function read($key);


	abstract public function delete($key);

	abstract public function clear($check);

	public function settings() {
		return $this->settings;
	}

	public function key($key) {
		if (empty($key)) {
			return false;
		}
		$key = str_replace(array(DS, '/', '.'), '_', strval($key));
		return $key;
	}
}