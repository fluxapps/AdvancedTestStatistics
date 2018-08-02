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
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilAdvancedTestStatisticsPlugin
	 */
	protected $pl;


	public function __construct() {
		global $ilCtrl, $tpl;

		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
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
			    $subtabs = array('subtab_aggTestResults', 'subtab_avg_points');

				$tabs = $a_par['tabs'];
//                $tabs->removeSubTab('tst_results_aggregated');

                // aggTestResults
                $this->ctrl->saveParameterByClass('ilAdvancedTestStatisticsGUI', 'ref_id');
				$link = $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilAdvancedTestStatisticsGUI',
					'ilAdvancedTestStatisticsAggGUI'
				));
				$tabs->addSubTab('aggTestResults', $this->pl->txt('subtab_agg_test_results'), $link);

				// avg_points
				$link = $this->ctrl->getLinkTargetByClass(array(
					'ilUIPluginRouterGUI',
					'ilAdvancedTestStatisticsGUI',
					'ilAdvancedTestStatisticsAvgGUI'
				));
				$tabs->addSubTab('avg_points', $this->pl->txt('subtab_avg_points'), $link);


				if ($this->access->hasCurrentUserAlertAccess()) {
				    $subtabs = array_merge($subtabs, array('subtab_filter', 'subtab_alerts'));

				    // filter
					$link = $this->ctrl->getLinkTargetByClass(array(
						'ilUIPluginRouterGUI',
						'ilAdvancedTestStatisticsGUI',
						'ilAdvancedTestStatisticsSettingsGUI'
					), ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_FILTER);
					$tabs->addSubTab("filter", $this->pl->txt('subtab_filters'), $link);

					// alerts
					$link = $this->ctrl->getLinkTargetByClass(array(
						'ilUIPluginRouterGUI',
						'ilAdvancedTestStatisticsGUI',
						'ilAdvancedTestStatisticsSettingsGUI'
					), ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_TRIGGERS);
					$tabs->addSubTab("alerts", $this->pl->txt('subtab_alerts'), $link);
				}

                // deactivate tabs via js
                $code = '';
				foreach ($subtabs as $subtab) {
				    $code .= "$('#$subtab').removeClass('active');";
                }
                $this->tpl->addOnLoadCode($code);
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