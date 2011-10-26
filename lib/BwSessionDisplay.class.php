<?php
/**
 * Display management class
 */
class BwSessionDisplay extends BwDisplay
{
	private $user;

	public function __construct($theme = null, $user = null)
	{
		parent::__construct($theme);
		
		$this->assign('sessionOk', true);
		$this->assign('user', $user);
	}
}