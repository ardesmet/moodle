<?php

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/course/lib.php');
global $DB;
//$courses = get_courses(2);
// foreach ($courses as $course) {
////     print("Modification du cours $course->fullname $course->shortname...");
////     $course->fullname = '[2012-2013] '.$course->fullname;
////     $course->shortname = '[2012-2013] '.$course->shortname;
////     if($DB->update_record('course', $course))
////        print("Cours modifié!\n");
////     else die();
//     
//	}
echo coursecat::get(2)->delete_move(5);