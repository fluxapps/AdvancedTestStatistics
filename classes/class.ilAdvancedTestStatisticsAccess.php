<?php

/**
 * Class ilAdvancedTestStatisticsAccess
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 */
class ilAdvancedTestStatisticsAccess {

	/**
	 * @var int
	 */
	protected $ref_id;


	public function __construct($ref_id) {
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $ref_id;
	}


	public function hasCurrentUserAlertAccess() {
		global $ilAccess;

		if ($ilAccess->checkAccess("statistics", "", $this->ref_id)) {
			return true;
		}

		return false;
	}
}