<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @author  Silas Stulz <sst@studer-raimann.ch>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilAdvancedTestStatisticsUIHookGUI extends ilUIHookPluginGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;


	public function __construct() {
		global $ilCtrl;

		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$this->ref_id = $_GET['ref_id'];
		$this->access = new ilAdvancedTestStatisticsAccess($this->ref_id);
	}


	function getHTML($a_comp, $a_part, $a_par = array()) {

	}


	/**
	 * Modify GUI objects, before they generate ouput
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par  array of parameters (depend on $a_comp and $a_part)
	 */
	function modifyGUI($a_comp, $a_part, $a_par = array()) {
		/**
		 * @var ilTabsGUI $tabs
		 */
		if ($a_part == 'sub_tabs') {
			if ($this->checkTest()) {
				$tabs = $a_par['tabs'];
				$this->ctrl->saveParameterByClass('ilAdvancedTestStatisticsGUI', 'ref_id');
				//	$tabs->removeSubTab('tst_results_aggregated');
				$link = $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilAdvancedTestStatisticsGUI',
					'ilAdvancedTestStatisticsAggGUI'
				));
				$tabs->addSubTab('aggTestResults', 'Aggregated Test Results', $link);
				$link = $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilAdvancedTestStatisticsGUI',
					'ilAdvancedTestStatisticsAvgGUI'
				));
				$tabs->addSubTab('avg_points', 'Average Points', $link);
				if ($this->access->hasCurrentUserAlertAccess()) {
					$link = $this->ctrl->getLinkTargetByClass(array(
						'ilUIPluginRouterGUI',
						'ilAdvancedTestStatisticsGUI',
						'ilAdvancedTestStatisticsSettingsGUI'
					), ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_FILTER);
					$tabs->addSubTab("filter", "Filters", $link);
					$link = $this->ctrl->getLinkTargetByClass(array(
						'ilUIPluginRouterGUI',
						'ilAdvancedTestStatisticsGUI',
						'ilAdvancedTestStatisticsSettingsGUI'
					), ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_TRIGGERS);
					$tabs->addSubTab("alerts", "Alerts", $link);
				}
			}
		}
	}


	function checkTest() {
		foreach ($this->ctrl->getCallHistory() as $GUIClassesArray) {
			if ($GUIClassesArray['class'] == 'ilTestEvaluationGUI') {
				return true;
			}
		}

		return false;
	}
}

?>