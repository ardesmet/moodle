<?php
	
	$id = required_param('id',PARAM_INT);
	global $CFG, $SESSION, $DB;
	//suppression de la variable de session toexcel
	unset($SESSION->toexcel);
	//Création de la variable de session toexcel
	if (empty($SESSION->toexcel)) {
    
	$SESSION->toexcel = array();
							}
	
	$count=0;
	//ajout des infos utilisateurs dans la variable de session toexcel (utilisateurs sélectionnés)
	foreach ($_POST as $k => $v) {
    if (preg_match('/^(user|teacher)(\d+)$/',$k,$m)) {
        
            if ($user = $DB->get_record_select('user', "id = ?", array($m[2]), 'id,firstname,lastname,idnumber,email,mailformat,lastaccess, lang')) {
                $SESSION->toexcel[$m[2]] = $user;
				//$SESSION->toexcel[0] = 
                $count++;
            }
        
    }
}
	//récupération du cours
	$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
	//champs qui doivent apparaitre dans le résultat
	$fields = array('lastname'  => 'nom',
					'firstname' => 'prenom',                                     
                    
					'email'     => 'email');
	//création du résultat
	user_download_xls($fields);
	//suppression de la variable de session toexcel
	//print_r($course);
	unset($SESSION->toexcel);
	
		
	function user_download_xls($fields) {
    global $CFG, $SESSION, $DB;

    require_once("$CFG->libdir/excellib.class.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
	
    $filename = clean_filename('participants.xls');
	
	
	$workbook = new MoodleExcelWorkbook('-');
	$workbook->send($filename);

    $worksheet = array();

    $worksheet[0] =& $workbook->add_worksheet('');
    $col = 0;
    foreach ($fields as $fieldname) {
		$worksheet[0]->write(0, $col, $fieldname);
     $col++;
     }
	 $row = 1;
	
     foreach ($SESSION->toexcel as $userid) 
	 {
		
		$user = $DB->get_record('user', array('id'=>$userid->id)); 
			
         $col = 0;
         profile_load_data($user);
		
         foreach ($fields as $field=>$unused) {
             $worksheet[0]->write($row, $col, $user->$field);
             $col++;
        }
         $row++;
    }
	 $workbook->close();
     
	 die;
}
function user_download_csv($fields) {
    global $CFG, $SESSION, $DB;

    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.csv');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $delimiter = get_string('listsep', 'langconfig');
    $encdelim  = '&#'.ord($delimiter);

    $row = array();
    foreach ($fields as $fieldname) {
        $row[] = str_replace($delimiter, $encdelim, $fieldname);
    }
    echo implode($delimiter, $row)."\n";

    
    die;
}
?>
	