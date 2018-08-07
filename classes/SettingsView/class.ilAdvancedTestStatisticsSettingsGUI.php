<?php

/**
 * Class ilAdvancedTestStatisticsSettingsGUI
 * @ilCtrl_isCalledBy ilAdvancedTestStatisticsSettingsGUI: ilUIPluginRouterGUI, ilAdvancedTestStatisticsGUI
 * @ilCtrl_Calls ilAdvancedTestStatisticsSettingsGUI: ilAdvancedTestStatisticsPlugin
 */
class ilAdvancedTestStatisticsSettingsGUI {


	const CMD_DISPLAY_FILTER = 'displayFilters';
	const CMD_UPDATE_FILTER = 'updateFilter';

	const CMD_TRIGGER_TRIGGER = 'executeTrigger';
	const CMD_DISPLAY_TRIGGERS = 'displayAlerts';
	const CMD_CREATE_TRIGGER = 'createTrigger';
	const CMD_UPDATE_TRIGGER = 'updateTrigger';
	const CMD_DELETE = 'delete';
	const IDENTIFIER_TRIGGER = 'trigger_id';
	const CMD_COPY_TRIGGER = 'copytrigger';
	const CMD_ADD_TRIGGER = 'add';
	const CMD_EDIT_TRIGGER = 'edit';
	const CMD_CANCEL= 'cancel';


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
		global $ilCtrl,$tpl,$ilTabs,$tree;

		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->ref_id = $_GET['ref_id'];
		$this->tabs = $ilTabs;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ctrl->saveParameterByClass($this,'ref_id');
		$this->ctrl->setParameterByClass(ilAdvancedTestStatisticsSettingsGUI::class,'ref_id',$this->ref_id);
		$this->test = ilObjectFactory::getInstanceByRefId($this->ref_id);

		$this->tree = $tree;
		$this->ref_id_course = $this->pl->getParentCourseId($this->ref_id);
		$this->usr_ids = ilCourseMembers::getData($this->ref_id_course);

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

		$table = new ilAdvancedTestStatisticsAlertTableGUI($this);
		$this->tpl->setContent($table->getHTML());

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
		$this->tabs->setBackTarget($this->pl->txt('btn_back'), $this->ctrl->getLinkTargetByClass(array( 'ilrepositorygui', 'ilObjTestGUI', 'ilTestEvaluationGUI' ), 'outEvaluation'));
	}

	protected function cancel() {
        $this->ctrl->setParameterByClass('ilrepositorygui', 'ref_id', (int)$_GET['ref_id']);
        $this->ctrl->redirectByClass(array( 'ilrepositorygui', 'ilObjTestGUI', 'ilTestEvaluationGUI' ), 'outEvaluation');
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


	public function delete() {
		$trigger = xatsTriggers::find($_GET[self::IDENTIFIER_TRIGGER]);
		$trigger->delete();
		$this->ctrl->redirect($this,self::CMD_DISPLAY_TRIGGERS);
	}


	/**
	 * Form for adding new trigger
	 */
	public function add(){
		$this->initHeader();
		$form = new ilAdvancedTestStatisticsAlertFormGUI($this, new xatsTriggers());
		$html = $form->getHTML();
		$this->tpl->setContent($html);
		$this->tpl->show();

	}


	/**
	 * Form for editing existing trigger
	 */
	public function edit(){
		$this->initHeader();
		$form = new ilAdvancedTestStatisticsAlertFormGUI($this, xatsTriggers::find($_GET[self::IDENTIFIER_TRIGGER]));
		$form->fillForm();
		$html = $form->getHTML();
		$this->tpl->setContent($html);
		$this->tpl->show();
	}


	/**
	 * create new Trigger
	 */
	public function createTrigger(){
        $form = new ilAdvancedTestStatisticsAlertFormGUI($this,new xatsTriggers());
        $form->setValuesByPost();

        if($form->save()){
            ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'),true);
            $this->ctrl->redirect(new ilAdvancedTestStatisticsSettingsGUI, ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_TRIGGERS);
        }

        $this->tpl->setContent($form->getHTML());

	}

	/**
	 * update Trigger
	 */
	public function updateTrigger(){
        $form = new ilAdvancedTestStatisticsAlertFormGUI($this, xatsTriggers::find($_POST[self::IDENTIFIER_TRIGGER]));
        $form->setValuesByPost();

        if($form->save()){
            ilUtil::sendSuccess($this->pl->txt('system_account_msg_success'),true);
            $this->ctrl->redirect(new ilAdvancedTestStatisticsSettingsGUI, ilAdvancedTestStatisticsSettingsGUI::CMD_DISPLAY_TRIGGERS);
        }

        $this->tpl->setContent($form->getHTML());
	}


	/**
	 * copy the trigger
	 */
	public function copyTrigger(){
		$trigger = xatsTriggers::find($_GET[self::IDENTIFIER_TRIGGER]);

		$xat = new xatsTriggers();
		$xat->setRefId($this->ref_id);
		$xat->setTriggerName($trigger->getTriggerName());
		$xat->setOperator($trigger->getOperator());
		$xat->setValue($trigger->getValue());
		$xat->setUserId($trigger->getUserId());
		$xat->setUserPercentage($trigger->getUserPercentage());
		$xat->setDatesender($trigger->getDatesender());
		$xat->setIntervalls($trigger->getIntervalls());

		$xat->create();
		$this->ctrl->redirect($this,self::CMD_DISPLAY_TRIGGERS);
	}


	public function executeTrigger(){
		if(!$this->trigger()){
			ilUtil::sendFailure($this->pl->txt('trigger_not_executed'),true);
			$this->ctrl->redirect($this,self::CMD_DISPLAY_TRIGGERS);
		}
	}


	/*
	 * Activate trigger
	 */
	public function trigger(){
	    /** @var xatsTriggers $trigger */
		$trigger = xatsTriggers::find($_GET[self::IDENTIFIER_TRIGGER]);

		/*
		 * Check Preconditions First
		 * First Check Date then check how many user finished the test
		 */
		if ($trigger->getDatesender() > date('U')) {
			return false;
		}

		$class = new ilAdvancedTestStatisticsAggResults();
		$finishedtests = $class->getTotalFinishedTests($this->ref_id);
		$course_members = count($this->usr_ids);

		// Check if enough people finished the test
		if((100/$course_members) * $finishedtests < $trigger->getUserPercentage()){
			return false;
		}

		$triggername = $trigger->getTriggerName();
		$trigger_value = $trigger->getValue();
        $operator = $trigger->getOperatorFormatted();

        $values_reached = ilAdvancedTestStatisticsConstantTranslator::getValues($triggername,$this->ref_id);
        $trigger_values = '';

        switch ($triggername) {
            case 'qst_percentage':
                $trigger_values .= "\n";
                foreach ($values_reached as $qst_id => $value_reached) {
                    if (!eval('return ' . $value_reached . ' ' . $operator . ' ' . $trigger_value . ';')) {
                        unset($values_reached[$qst_id]);
                    } else {
                        $trigger_values .= '"' . assQuestion::_instanciateQuestion($qst_id)->getTitle() . '"' . ': ';
                        $trigger_values .= $value_reached . "\n";
                    }
                }

                if (empty($values_reached)) {
                    return false;
                }
                break;
            default:
                if (!eval('return ' . $values_reached . ' ' . $operator . ' ' . $trigger_value . ';')) {
                    return false;
                }
                $trigger_values = $values_reached;
                break;
        }

        $sender = new ilAdvancedTestStatisticsSender();
		try {
		    $sender->createNotification($this->ref_id_course, $trigger, $trigger_values);
		    ilUtil::sendSuccess($this->pl->txt('system_account_msg_success_trigger'),true);
		} catch (Exception $exception){
            ilUtil::sendFailure('Error: ' . $exception->getMessage(), true);
		}

		$this->ctrl->redirect($this,self::CMD_DISPLAY_TRIGGERS);
	}




}