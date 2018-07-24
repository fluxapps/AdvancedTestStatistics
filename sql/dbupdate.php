<#1>
	<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsFilter.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsFilteredUsers.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsTriggers.php';

xatsFilter::updateDB();
xatsFilteredUsers::updateDB();
xatsTriggers::updateDB();
?>
<#2>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsExtendedStatisticsFields.php';
xatsExtendedStatisticsFields::updateDB();
?>
<#3>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsTriggers.php';
xatsTriggers::updateDB();
?>
<#4>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsTriggers.php';
xatsTriggers::updateDB();
?>
