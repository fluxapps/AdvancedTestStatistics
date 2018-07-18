<?php

require_once 'class.ilCourseMember.php';

class ilCourseMembers {


	public static function getData ($crs_ref){
		global $ilDB;

		$result = $ilDB->query(self::getSQL($crs_ref));

		$users = array();

		while ($row = $ilDB->fetchAssoc($result)){
			$user = new ilCourseMember();
			$user->setUsrId($row['usr_id']);
			$user->setLogin($row['login']);
			$user->setEmail($row['email']);
			$user->setFirstname($row['firstname']);
			$user->setLastname($row['lastname']);


			$users[$row['usr_id']] = $user;
		}
		return $users;
	}


	protected static function getSQL ($crs_ref){
		global $ilDB;


		$sql = "select usr_data.login,usr_data.usr_id,email,firstname,lastname from obj_members
				inner join usr_data on obj_members.usr_id = usr_data.usr_id
				inner join object_data as crs_obj on crs_obj.obj_id = obj_members.obj_id and crs_obj.type = 'crs'
				inner join object_reference crs_ref on crs_ref.obj_id = obj_members.obj_id
				inner join crs_settings on crs_ref.obj_id = crs_settings.obj_id
				where crs_ref.ref_id=" . $ilDB->quote($crs_ref, "integer") . " and obj_members.member = 1
";
		return $sql;


	}

}