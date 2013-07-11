<?php

require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');
global $DB;
$courses = get_courses(7);
foreach ($courses as $course) {
     print("Modification du cours $course->fullname $course->shortname...");
     $course->fullname = '[2012-2013]'.$course->fullname;
     $course->shortname = '[2012-2013]'.$course->shortname;
     $course->idnumber = '[2012-2013]'.$course->idnumber;
     if($DB->update_record('course', $course))
        print("Cours modifié!<br>");
     
///     else die();
//     
	}

if(coursecat::get(7)->delete_move(9)) echo 'cours déplacés dans les archives';
else echo 'problème...';
//echo 'a';
//print_object(coursecat::get(2)->can_move_content_to(5));
