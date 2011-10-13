<?php
class BwDebug
{
	const NO_DEBUG = 0;
	const LOG_PERF = 1;
	const LOG_ALL  = 2;

	private $mode;

	private $sqlLogs;

	public function __construct($mode = self::NO_DEBUG)
	{
		$this->mode = $mode;
	}
}