<?php

require_once($CFG->libdir.'/coursecatlib.php');
class block_mesinfos_edit_form extends block_edit_form {

    protected function specific_definition($mform) {

global $CFG;        
// Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // A sample string variable with a default value.
        $coursecatt = coursecat::make_categories_list();
        $select = $mform->addElement('select', 'config_category', get_string('selectcategory', 'block_mesinfos'),$coursecatt);
        //$mform->addElement('checkbox', 'ratingtime', get_string('protectedcourse', 'block_mesinfos'));
        //$mform->addElement('advcheckbox', 'ratingtime', get_string('ratingtime', 'forum'), 'Label displayed after checkbox', array('group' => 1), array(0, 1));
                 //$select->setSelected('3');
       
    }
}
