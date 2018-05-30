<?php

/**
 * Class ilAdvancedTestStatisticsAggGUI
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsAggGUI: ilUIPluginRouterGUI, ilAdvancedTestStatisticsGUI
 */

class ilAdvancedTestStatisticsAggGUI {

	const CMD_DISPLAY = 'display';

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;

	public function __construct() {
		global $ilCtrl;

		$this->ctrl = $ilCtrl;

	}


	public function executeCommand() {
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd($this::CMD_DISPLAY);
				$this->{$cmd}();
		}
	}


	public function display(){


	}
}