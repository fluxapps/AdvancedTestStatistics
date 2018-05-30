<?php
/**
 * Class ilAdvancedTestStatisticsGUI
 *
 * @author  Silas Stulz <sst@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsGUI: ilUIPluginRouterGUI, ilObjTestGUI
 */
class ilAdvancedTestStatisticsGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;

	public function __construct() {
		global $ilCtrl,$tpl;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->ref_id = $_GET['ref_id'];

	}


	public function executeCommand(){
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass){
			case 'iladvancedteststatisticsavggui':
				$iladvancedteststatisticsavggui = new ilAdvancedTestStatisticsAvgGUI();
				$this->ctrl->forwardCommand($iladvancedteststatisticsavggui);
				break;
			case 'iladvancedteststatisticsagggui':
				$iladvancedteststatisticsagggui = new ilAdvancedTestStatisticsAggGUI();
				$this->ctrl->forwardCommand($iladvancedteststatisticsagggui);
				break;
			case 'iladvancedteststatisticssettingsgui':
				$iladvancedteststatisticssettingsgui = new ilAdvancedTestStatisticsSettingsGUI();
				$this->ctrl->forwardCommand($iladvancedteststatisticssettingsgui);
				break;
			default: $cmd = $this->ctrl->getCmd();
			$this->{$cmd}();
		}
	}
}