<?php

/**
 * Class ilAdvancedTestStatisticsSettingsGUI
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsSettingsGUI: ilUIPluginRouterGUI, ilAdvancedTestStatisticsGUI
 * @ilCtrl_Calls ilAdvancedTestStatisticsSettingsGUI: ilAdvancedTestStatisticsPlugin
 */
class ilAdvancedTestStatisticsSettingsGUI {


	const CMD_DISPLAY_FILTER = 'displayFilters';
	const CMD_DISPLAY_TRIGGERS = 'displayAlerts';
	const CMD_UPDATE_FILTER = 'updateFilter';
	const CMD_CANCEL= 'cancel';
	const CMD_CREATE_TRIGGER = 'createTrigger';

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var int
	 */
	protected $ref_id;

	public function __construct() {
		global $ilCtrl,$tpl,$ilTabs;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->ref_id = $_GET['ref_id'];
		$this->tabs = $ilTabs;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ctrl->saveParameterByClass($this,'ref_id');
		$this->test = ilObjectFactory::getInstanceByRefId($this->ref_id);
	}


	public function executeCommand() {
		$this->tpl->getStandardTemplate();
		$nextClass = $this->ctrl->getNextClass();

		switch ($nextClass) {
			default:
				$cmd = $this->ctrl->getCmd();
				$this->{$cmd}();
		}
	}


	public function displayAlerts(){
		$this->initHeader();

		$form = new ilAdvancedTestStatisticsAlertFormGUI($this, new xatsFilter());
		$this->tpl->setContent($form->getHTML());

		$this->tpl->show();

	}

	public function displayFilters(){
		$this->initHeader();

		$form = new ilAdvancedTestStatisticsFilterFormGUI($this);
		$this->tpl->setContent($form->getHTML());
		$this->tpl->show();
	}

	protected function initHeader() {
		$this->tpl->setTitle($this->test->getTitle());
		$this->tpl->setDescription($this->test->getDescription());
		$this->tpl->setTitleIcon(ilObject::_getIcon($this->test->getId()));

	//	$this->tpl->setTabs($this->tabs);

		$this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', (int)$_GET['ref_id']);
		$this->tabs->setBackTarget($this->pl->txt('btn_back'), $this->ctrl->getLinkTargetByClass(array( 'ilrepositorygui', 'ilObjTestGUI' )));
	}

	public function updateFilter(){
		$form = new ilAdvancedTestStatisticsFilterFormGUI($this);

		$form->setValuesByPost();

		if ($form->save()){
			ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'),true);
			$this->ctrl->redirect(new ilAdvancedTestStatisticsSettingsGUI, ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_FILTER);
		}
		$this->tpl->setContent($form->getHTML());

	}

	public function createTrigger(){


	}

}