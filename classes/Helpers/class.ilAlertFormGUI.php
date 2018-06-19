<?php
/**
 * Class ilAlertFormGUI
 */
class ilAlertFormGUI extends ilPropertyFormGUI {

	const FIELD_NAME_SCALE = 'scale';
	const FIELD_NAME_QUEST = 'quest';
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs_gui;
	/**
	 * @var
	 */
	protected $obj;


	/**
	 * @param int  $parent_id
	 */
	public function __construct() {
		global $tpl, $ilCtrl;
		/**
		 * @var $tpl    ilTemplate
		 * @var $ilCtrl ilCtrl
		 */
		$this->tpl = $tpl;
		$this->ctrl = $ilCtrl;
		$this->pl = new ilAdvancedTestStatisticsPlugin();
		$this->initForm();
		//$this->tpl->addJavaScript('./Customizing/global/plugins/Services/Repository/RepositoryObject/LearningSuccessExamination/templates/js/sortable.js');
	}


	protected function initForm() {
		// Header
		$te = new ilFormSectionHeaderGUI();
		$te->setTitle($this->pl->txt('Alerts'));
		$this->addItem($te);
		$te = new MultiLineInputGUI($this->pl->txt('Trigger'), 'triggers','triggers');
		$te->setPlaceholderValue($this->pl->txt('multinput_value'));
		$te->setPlaceholderTitle($this->pl->txt('multinput_title'));
		$te->setDescription($this->pl->txt('multinput_description'));
		$te->setDisabled($this->locked);
		$this->addItem($te);

		// FillForm
		//$this->fillForm();
	}


	/**
	 * @return array
	 **/
	public function fillForm() {
		$array1 = array();
		$questions = xlseQuestions::where(array('checklist_id' => $this->check_id))->get();

		foreach ($questions as $question) {
			$array1[$question->getId()] = array("value" => $question->getText());
		}

		$array = array();
		$scales = ilScales::getDatabyCheck_id($this->check_id);

		foreach ($scales as $scale) {
			$array[$scale->getId()] = array('title' => $scale->getText(), 'value' => $scale->getPoints());
		}

		$array = array(
			'scale' => $array,
			'quest' => $array1,
		);
		$this->setValuesByArray($array);

		return $array;
	}


	/**
	 * @param ilPropertyFormGUI $form_gui
	 *
	 * @return ilPropertyFormGUI
	 */
	public function appendToForm(ilPropertyFormGUI $form_gui) {
		foreach ($this->getItems() as $item) {
			$form_gui->addItem($item);
		}

		return $form_gui;
	}
}

?>
