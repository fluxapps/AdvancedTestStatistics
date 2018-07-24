<?php
include_once('./Services/Form/classes/class.ilCustomInputGUI.php');
require_once('./Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php');

/**
 * Class MultiLineInputGUI
 */
class MultiLineInputGUI extends ilCustomInputGUI {

    /**
     * @var ilAdvancedTestStatisticsPlugin
     */
	protected $pl;

	/**
	 * @var array
	 */
	protected $values;
	/**
	 * @var string
	 */
	protected $field_name;
	/**
	 * @var array
	 */
	protected $operators = array('>', '<', '>=', '<=', '!=', '==');
	/**
	 * @var array
	 */
	protected $extendedFields = array(
		"avg_points_finished",
		"avg_result_passed",
		"avg_result_finished",
		"avg_result_finished_run_one",
		"avg_result_passed_run_one",
		"avg_result_passed_run_two",
		"avg_result_finished_run_two",
	);
	/**
	 * @var int
	 */
	protected $default_value = 0;
	/**
	 * @var string
	 */
	protected $description = "";


	/**
	 * @param string $title
	 * @param string $post_var
	 * @param        $field_name
	 */
	public function __construct($title, $post_var, $field_name) {
		global $ilCtrl;

		$this->ctrl = $ilCtrl;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();

		parent::__construct($title, $post_var);
		$this->setFieldName($field_name);
	}


	/**
	 * @return string
	 */
	public function getHtml() {
		$this->tpl = new ilTemplate("tpl.alert_template.html", true ,true, './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics');

		foreach ($this->extendedFields as $extendedField){
			$this->tpl->setCurrentBlock('OPTIONS');
			//$this->tpl->setVariable('SELECT_NAME','Select one');
			$this->tpl->setVariable('OPTION_VALUE', $this->getFieldName() . $this->pl->txt($extendedField));
			$this->tpl->setVariable('OPTION', $this->pl->txt($extendedField));
			$this->tpl->parseCurrentBlock();
		}

		foreach ($this->operators as $operator){
			$this->tpl->setCurrentBlock('OPTIONS1');
			//	$this->tpl->setVariable('SELECT_NAME1','Select one');
			$this->tpl->setVariable('OPTION_VALUE1', $this->getFieldName() . '_new[option_two][]');
			$this->tpl->setVariable('OPTION1', $operator);
			$this->tpl->parseCurrentBlock();
		}

		$this->tpl->setVariable('VALUE', 'Value');



		$this->tpl->parseCurrentBlock();
		$user = new ilTextInputGUI("user", "login");
		$user->setDataSource($this->ctrl->getLinkTargetByClass(array(
			ilUIPluginRouterGUI::class,
			ilAdvancedTestStatisticsPlugin::class
		), ilAdvancedTestStatisticsPlugin::CMD_ADD_USER_AUTO_COMPLETE, "", true));
		$user->setInfo("User");


		$html = $user->getToolbarHTML();
		$this->tpl->setVariable("SEARCHFIELD",$html);

		$link = $this->ctrl->getLinkTargetByClass(ilAdvancedTestStatisticsSettingsGUI::class, ilAdvancedTestStatisticsSettingsGUI::CMD_CREATE_TRIGGER);
		$this->tpl->setVariable("HREF",$link);

		return $this->tpl->get();
	}


	/**
	 * @param $value array form $value[$postvar] = array(id, array(name, value))
	 */
	public function setValueByArray($value) {
		parent::setValueByArray($value);
		$this->setValues(is_array($value[$this->getPostVar()]) ? $value[$this->getPostVar()] : array());
	}


	/**
	 * @param array $values
	 */
	public function setValues($values) {
		$this->values = $values;
	}


	/**
	 * @return array
	 */
	public function getValues() {
		return $this->values;
	}


	/**
	 * @param string $field_name
	 */
	public function setFieldName($field_name) {
		$this->field_name = $field_name;
	}


	/**
	 * @return string
	 */
	public function getFieldName() {
		return $this->field_name;
	}


	/**
	 * @param string $placeholder_title
	 */
	public function setPlaceholderTitle($placeholder_title) {
		$this->placeholder_title = $placeholder_title;
	}


	/**
	 * @return string
	 */
	public function getPlaceholderTitle() {
		return $this->placeholder_title;
	}


	/**
	 * @param string $placeholder_value
	 */
	public function setPlaceholderValue($placeholder_value) {
		$this->placeholder_value = $placeholder_value;
	}


	/**
	 * @return string
	 */
	public function getPlaceholderValue() {
		return $this->placeholder_value;
	}


	/**
	 * @param string $default_value
	 */
	public function setDefaultValue($default_value) {
		$this->default_value = $default_value;
	}


	/**
	 * @return string
	 */
	public function getDefaultValue() {
		return $this->default_value;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
}

?>
