<#1>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsFilter.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsFilteredUsers.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsTriggers.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsTriggers.php';
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/AdvancedTestStatistics/ActiveRecords/xatsExtendedStatisticsFields.php';
xatsExtendedStatisticsFields::updateDB();
xatsTriggers::updateDB();
xatsFilter::updateDB();
xatsFilteredUsers::updateDB();
xatsTriggers::updateDB();
?>
