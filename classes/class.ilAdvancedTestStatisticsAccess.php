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

    /**
     * ilAdvancedTestStatisticsAccess constructor.
     * @param $ref_id
     */
	public function __construct($ref_id) {
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ref_id = $ref_id;
	}

    /**
     * @return bool
     */
	public function hasCurrentUserAlertAccess() {
		/** @var $ilAccess ilAccessHandler */
	    global $ilAccess;

		if ($ilAccess->checkAccess("tst_statistics", "", $this->ref_id)) {
			return true;
		}

		return false;
	}
}