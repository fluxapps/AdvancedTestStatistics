<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook class
 *
 * @author Silas Stulz <sst@studer-raimann.ch>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilAdvancedTestStatisticsUIHookGUI extends ilUIHookPluginGUI
{


	function getHTML($a_comp, $a_part, $a_par = array())
	{

	}

	/**
	 * Modify GUI objects, before they generate ouput
	 *
	 * @param string $a_comp component
	 * @param string $a_part string that identifies the part of the UI that is handled
	 * @param string $a_par array of parameters (depend on $a_comp and $a_part)
	 */
	function modifyGUI($a_comp, $a_part, $a_par = array())
	{
		echo "hello";
		/**
		 * @var ilTabsGUI $tabs
		 */
		if($a_part == 'sub_tabs'){
			$tabs = $a_par['tabs'];
			$tabs->addSubTab("settings","Settings","");
		}

	}

}
?>