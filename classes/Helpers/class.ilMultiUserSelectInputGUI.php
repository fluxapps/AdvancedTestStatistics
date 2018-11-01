<?php
require_once("class.ilMultiSelectSearchInputGUI.php");

/**
 * Class ilMultiUserSelectInputGUI
 * This class allows you to easily make an input gui where you can add multiple users. There is no default security check, as by default the link for the ajax request is
 * made via the ilUIPluginRouterGUI. If you want a permission check, which is highly recommended, set the Ajax Link via your own GUI using setAjaxLink and route to this class's searchUser method.
 *
 * TT: Adjusted for AdvancedTestStatistics-Plugin - only finds users enlisted in the course
 *
 * @author: Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy ilMultiUserSelectInputGUI: ilUIPluginRouterGUI
 * @ilCtrl_Calls ilMultiUserSelectInputGUI: ilUIPluginRouterGUI
 */
class ilMultiUserSelectInputGUI extends ilMultiSelectSearchInputGUI{

    /**
     * @var ilCtrl|mixed
     */
    protected $ctrl;
    /**
     * @var ilDB|ilDBInterface
     */
    protected $db;
    /**
     * @var ilAdvancedTestStatisticsPlugin
     */
    protected $pl;

	public function __construct($title = "", $post_var = ""){
		global $ilCtrl, $ilDB;
		parent::__construct($title, $post_var);

		/**
		 * @var $ilCtrl ilCtrl
		 * @var $ilDB ilDB
		 */
		$this->ctrl = $ilCtrl;
		$this->db = $ilDB;
		$this->pl = ilAdvancedTestStatisticsPlugin::getInstance();
		$this->ctrl->setParameter($this, 'ref_id', $_GET['ref_id']);
		$this->setAjaxLink($this->ctrl->getLinkTargetByClass(array("ilUIPluginRouterGUI","ilMultiUserSelectInputGUI"), "searchUsers"));
		$this->setInputTemplate(new ilTemplate("tpl.multiple_user_select.html", true, true,"Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics"));
		$this->setMinimumInputLength(3);
	}

	public function render(){
		$tpl = $this->getInputTemplate();
		$values = $this->getValueAsJson();

		$tpl->setVariable("POST_VAR", substr($this->getPostVar(), 0, -2));
		$tpl->setVariable("ID", str_replace(']','',str_replace('[','_',substr($this->getPostVar(), 0, -2))));
		$tpl->setVariable("WIDTH", $this->getWidth());
		$tpl->setVariable("HEIGHT", $this->getHeight());
		$tpl->setVariable("PLACEHOLDER", "");
		$tpl->setVariable("MINIMUM_INPUT_LENGTH", $this->getMinimumInputLength());
		$tpl->setVariable("Class", $this->getCssClass());
		$tpl->setVariable("PRELOAD", $values);

		if($this->getDisabled()) {
			$tpl->setVariable("ALL_DISABLED", "disabled=\"disabled\"");
		}

		if(isset($this->ajax_link)){
			$tpl->setVariable("AJAX_LINK", $this->getAjaxLink());
		}

		return $tpl->get();
	}

	public function getValueAsJson(){
		$query = "SELECT usr_id, firstname, lastname, login FROM usr_data WHERE ".$this->db->in("usr_id", $this->getValue(), false, "integer");
		$res = $this->db->query($query);
		$result = array();
		while($user = $this->db->fetchAssoc($res)){
			$result[] = array("id" => $user["usr_id"], "text" => $user["firstname"]." ".$user["lastname"]." [".$user["login"]."]");
		}
		return json_encode($result);
	}

	public function executeCommand() {
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			case '':
				$cmd = $this->ctrl->getCmd();
				switch ($cmd) {
					default:
						$this->$cmd();
						break;
				}
				break;
			default:
				require_once($this->ctrl->lookupClassPath($next_class));
				$gui = new $next_class();
				$this->ctrl->forwardCommand($gui);
				break;
		}

		return true;
	}

	/**
	 * For the ajax request to be understood by the javascript, the json_encode must be of an array with an "id" and a "text" entry.
	 */
	public function searchUsers(){
		$page_limit = $_GET["page_limit"];
		$term = str_replace("%", "x", $_GET["term"]);
		$users = ilObjUser::searchUsers($term, 1, false);
		$result = array();
		$i = 0;
		foreach($users as $user){
		    if (!in_array($user['usr_id'], $this->pl->getCourseMembers($_GET['ref_id']))) {
		        continue;
            }
			$i++;
			$result[] = $this->parseUserRow($user);
			if($i > $page_limit)
				break;
		}
		echo json_encode($result);
		exit;
	}

	/**
	 * Override this method if you want the display text to be different or the id to be handled differently. Make sure there is still an "id" and a "text" association in the return.
	 * @param $user
	 * @return array
	 */
	protected function parseUserRow($user){
		return array("id" => $user["usr_id"], "text" => $user["firstname"]." ".$user["lastname"]." [".$user["login"]."]");
	}

	public function getValue(){
		$val = parent::getValue();
		if(is_array($val))
			return $val;
		elseif(!$val)
			return array();
		else
			return explode(",", $val);
	}

}
