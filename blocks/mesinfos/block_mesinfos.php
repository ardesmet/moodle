<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * mesinfos block caps.
 *
 * @package    block_mesinfos
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_mesinfos extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_mesinfos');
    }
    /**
     * Gets Javascript that may be required for navigation
     */
     function get_required_javascript() {
        global $CFG;
        $arguments = array('id' => $this->instance->id, 'instance' => $this->instance->id, 'candock' => $this->instance_can_be_docked());
        $this->page->requires->yui_module(array('core_dock', 'moodle-block_navigation-navigation'), 'M.block_navigation.init_add_tree', array($arguments));
        user_preference_allow_ajax_update('docked_block_instance_'.$this->instance->id, PARAM_INT);
    }
    function get_content() {
        global $CFG, $OUTPUT, $PAGE;
        require_once($CFG->libdir . '/coursecatlib.php');
        if ($this->content != null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content->text = '';
            return $this->content;
}
        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        
        //$this->content->text = coursecat::get($this->config->category)->name;
       
        $list = coursecat::get($this->config->category)->get_courses();
        $this->courses = $list;
        $sec = $this->get_sections();
        //print_r($sec);
       
        
//       
//        foreach($list as $cours)
//        {
//           $this->content->text .= get_course
//        }

       //print_r(coursecat::get($this->config->category));
//        $renderer = $this->page->get_renderer('block_mesinfos');
//        $courses = get_courses($this->config->category);
//        $this->courses = $courses;
//        $sectionslist = $this->get_sections();
//        foreach($this->courses as $course)
//        {
//            $modinfo = get_fast_modinfo($course);
//            $sections = $modinfo->get_section_info_all();
//            //var_dump($sections);
//            foreach($sections as $section)
//            {
//                if($section->visible)
//                    $render .= html_writer::tag('li', get_section_name($course, $section), array('class' => "type_system depth_2 contains_branch"));
//
//                //$this->content->text .= html_writer::label(get_section_name($course, $section),null);
//                
//            }
//            $output = '<div class="block_navigation">';
//		
//		//$sectionslist = $this->get_sections();
//		//$render = $renderer->render_courses($this->courses,$sectionslist);
//		$output .= html_writer::tag('ul', $render, array('class' => 'block_tree list'));
//            $output .= '</div>';
//            //print_r($sections);
//            $this->content->text = $output;
//            
//        }
//        //get_all_sections($courses->id);
        //$sectionslist = $this->get_sections();
        //$render = $renderer->render_courses($this->courses,$sectionslist);
        //print_r($courses);
        $output = '<div class="block_navigation">';
        $renderer = $this->page->get_renderer('block_mesinfos');
        $render = $renderer->render_courses($list,$sec);
        $output .= html_writer::tag('ul', $render, array('class' => 'block_tree list'));
        
        $output .= '</div>';
        $this->content->text = $output;
        $this->contentgenerated = true;
        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('all' => false,
                     'site' => true,
                     'site-index' => true,
                     'course-view' => true, 
                     'course-view-social' => false,
                     'mod' => true, 
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
          return true;
    }

    function has_config() {return true;}

    public function cron() {
            mtrace( "Hey, my cron script is running" );
             
                 // do something
                  
                      return true;
    }
    function get_sections()
    {
        global $CFG, $USER, $DB, $OUTPUT;
        $sectionslist = array();
        foreach($this->courses as $this->course)
	{
            $context = get_context_instance(CONTEXT_COURSE, $this->course->id);
            $coursecontext = $DB->get_record('course',array('id'=> $this->course->id));
            
            if(!can_access_course($coursecontext))
                continue;
            $modinfo = get_fast_modinfo($this->course->id);
//                foreach($modinfo->sections[0] as $cmid) {
//                    $cm = $modinfo->cms[$cmid];
//                   echo $cm->name;
//                }
           
            
            //get_all_mods($courseid, $sectionslist, $modnames, $modnamesplural, $modnamesused);
           
            //print_object($context);
            
            
            //$guest = get_role_users(5, $context);
            //print_object($guest);
            $format = course_get_format($this->course);
            
            $allSections = $format->get_sections();
            $sections = array();
            //print_r($sections);
            foreach ($allSections as $section) {
                
                                          //$modinfo = unserialize($this->course->modinfo);
              
               //print_r($modinfo->sections[$section->section]);
                //print_r($section->sequence);
                //print_r($sectionmods);
                    
                $newSec = array();
                $newSec['visible'] = $section->visible;
                $newSec['num'] = $section->section;
                //$newSec['number'] = $section->number;
                $newSec['name'] = $format->get_section_name($section);
                $newSec['url'] = $format->get_view_url($section);
                $newSec['resources'] = array();
                
                 if (!empty($modinfo->sections[$section->section])){
                     
                 foreach($modinfo->sections[$section->section] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                   if($cm->visible){ 
                   $resource = array();
                   $resource['name']=$cm->name;
                   //print_object($cm);
                   
                   $resource['url'] = "$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id";
                        $icon = $OUTPUT->pix_url("icon", $cm->modname);
                        if (is_object($icon)) {
                            $resource['icon'] = $icon->__toString();
                        } else {
                            $resource['icon'] = '';
                        }
                   if($cm->modname== 'url')
                   {
                       $info = url_get_coursemodule_info($cm);
                                if (isset($info->onclick)) {
                                    $tmp = $info->onclick;
                                    $onclick = str_replace("amp;", "", $tmp);
                                    $resource['onclick'] = $onclick;
                                }
                                else
                                    $resource['onclick'] = '';
                            }
                       
                        $newSec['resources'][] = $resource;
                    }
                 }
                }
                
               
                $sections[] = $newSec;
                
            }
            
            $sectionslist[] = $sections;  
                    
            
        }
        //print_object($sectionslist);
        return $sectionslist;
    }
//    function get_sections() {
//    	global $CFG, $USER, $DB, $OUTPUT;
//        
//    	//if (!empty($this->instance) && $this->page->course->id != SITEID) {
//            
//            require_once($CFG->dirroot."/course/lib.php");
//			$sectionslist = array();
//			foreach($this->courses as $this->course)
//			{
//            //get_all_mods($this->course->id, $mods, $modnames, $modnamesplural, $modnamesused);
//            
//        	$context = get_context_instance(CONTEXT_COURSE, $this->course->id);
//            $isteacher = has_capability('moodle/course:update', $context);
//            
//        	$courseFormat = $this->course->format == 'topics' ? 'topic' : 'week';
//
//            // displaysection - current section
//    		$week = optional_param($courseFormat, -1, PARAM_INT);
//    		/**if ($week != -1) {
//    		    $displaysection = course_set_display($this->course->id, $week);
//    		} else {
//    		    if (isset($USER->display[$this->course->id])) {
//    		        $displaysection = $USER->display[$this->course->id];
//    		    } else {
//    		        $displaysection = course_set_display($this->course->id, 0);
//    		    }
//    		}**/
//
//        	//$genericName = get_string("name" . $this->course->format, $this->blockname);
//            $allSections  = get_fast_modinfo($this->course->id)->get_section_info_all();
//            print_r($allSections);
//    		$sections = array();
//			
//            if ($this->course->format != 'social' && $this->course->format != 'scorm') {
//                foreach ($allSections as $k => $section) {
//                       //print_r($section);
//                       
//                     // get_all_sections() may return sections that are in the db but not displayed because the number of the sections for this course was lowered - bug [CM-B10]
//                        if (!empty($section)) {
//                            $newSec = array();
//                            $newSec['visible'] = $section->visible;
//
//                            
//
//                            //$strsummary = $this->trim($strsummary);
//                            //$strsummary = trim($this->clearEnters($strsummary));
//                            $newSec['name'] = '$strsummary';
//                            
//                            // url
//                            /**if ($displaysection != 0) {
//                                $newSec['url'] = "{$CFG->wwwroot}/course/view.php?id={$this->course->id}&$courseFormat=$k";
//                            } else {*/
//                                $url = (string)$this->page->url;
//                                // if (!preg_match("/\/course\/view.php/", $url)) {
//								//if($this->course->coursedisplay==0)
//                                    $newSec['url'] = "{$CFG->wwwroot}/course/view.php?id={$this->course->id}#section-$k";
//								//else if($this->course->coursedisplay==1)
//									//$newSec['url'] = "{$CFG->wwwroot}/course/view.php?id={$this->course->id}&section=$k";
//									 //$newSec['url'] = "{$CFG->wwwroot}/course/view.php?id={$this->course->id}&$courseFormat=$k";
//                                // } else {
//                                    // $newSec['url'] = "#section-$k";
//                                // }
//                            //}
//
//                            // resources
//                            $modinfo = unserialize($this->course->modinfo);
//							//echo '------------------------------------<br><br><br>';print_r($modinfo);echo '------------------------------------<br><br><br>';
//                            $newSec['resources'] = array();
//                            $sectionmods = explode(",", $section->sequence);
//                            foreach ($sectionmods as $modnumber) {
//                                if (empty($mods[$modnumber])) {
//                                    continue;
//                                }
//                                $mod = $mods[$modnumber];
//								
//                                if ($mod->visible or $isteacher) {
//                                    $instancename = urldecode($modinfo[$modnumber]->name);
//                                    if (!empty($CFG->filterall)) {
//                                        $instancename = filter_text($instancename, $this->course->id);
//                                    }
//
//                                    if (!empty($modinfo[$modnumber]->extra)) {
//                                        $extra = urldecode($modinfo[$modnumber]->extra);
//                                    }
//                                    else {
//                                        $extra = "";
//                                    }
//
//                                    // don't do anything for labels
//                                    if ($mod->modname != 'label') {
//										
//                                        // Normal activity
//                                        if ($mod->visible) {
//                                            if (!strlen(trim($instancename))) {
//                                                $instancename = $mod->modfullname;
//                                            }
//                                            $instancename = $this->truncate_description($instancename,2000);
//
//                                            $resource = array();
//											
//                                            if ($mod->modname != 'resource' & $mod->modname != 'url') {
//											
//                                                $resource['name'] = $this->truncate_description($instancename, 2000);
//                                                $resource['url']  = "$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id";
//												
//												//echo $modinfo[18]->onclick.'<br>';
//												//echo $modnumber;
//												//print_r($modinfo);
//												//echo($modnumber.'|||||||||||||||||||');print_r($modinfo);echo '----------';
//                                                $icon = $OUTPUT->pix_url("icon", $mod->modname);
//                                                if (is_object($icon)) {
//                                                    $resource['icon'] = $icon->__toString();
//                                                } else {
//                                                    $resource['icon'] = '';
//                                                }
//                                            }
//											else if($mod->modname == 'url')
//											{
//												$info = url_get_coursemodule_info($mod);
//												$resource['name'] = $this->truncate_description($instancename, 2000);
//                                                $resource['url']  = "$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id";
//												
//												//print_r($modinfo[$modnumber]).'<br>';
//												if(isset($info->onclick))
//												{
//													$tmp = $info->onclick;
//													$onclick = str_replace("amp;","",$tmp);
//													$resource['onclick'] = $onclick;
//												}	
//													else $resource['onclick'] = '';
//												$icon = $OUTPUT->pix_url("icon", $mod->modname);
//                                                if (is_object($icon)) {
//                                                    $resource['icon'] = $icon->__toString();
//                                                } else {
//                                                    $resource['icon'] = '';
//                                                }
//											
//											
//											}
//											else {
//                                                require_once($CFG->dirroot.'/mod/resource/lib.php');
//                                                $info = resource_get_coursemodule_info($mod);
//												
//                                                if (isset($info->icon)) {
//														
//                                                    $resource['name'] = $this->truncate_description($info->name, 2000);
//                                                    $resource['url']  = "$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id";
//													//echo $modinfo[$modnumber]->onclick; 
//													if(isset($modinfo[$modnumber]->onclick)) $resource['onclick'] = $modinfo[$modnumber]->onclick;  
//													else $resource['onclick'] = '';
//													
//													
//                                                    $icon = $OUTPUT->pix_url($mod->icon);
//                                                    if (is_object($icon)) {
//                                                        
//														$resource['icon'] = $icon->__toString();
//                                                    } else {
//                                                        $resource['icon'] = '';
//                                                    }
//                                                } else if(!isset($info->icon)) {
//                                                    $resource['name'] = $this->truncate_description($info->name, 2000);
//                                                    $resource['url']  = "$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id";
//                                                    $icon = $OUTPUT->pix_url("icon", $mod->modname);
//                                                    if (is_object($icon)) {
//                                                        $resource['icon'] = $icon->__toString();
//                                                    } else {
//                                                        $resource['icon'] = $OUTPUT->pix_url("icon", $mod->modname);
//                                                    }
//                                                }
//                                            }
//											//print_r($resource);echo '<br><br><br><br>';
//                                            $newSec['resources'][] = $resource;
//                                        }
//                                    }
//
//                                }
//                            }
//                            //hide hidden sections from students if the course settings say that - bug #212
//                            $coursecontext = get_context_instance(CONTEXT_COURSE, $this->course->id);
//                            if (!($section->visible == 0 && !has_capability('moodle/course:viewhiddensections', $coursecontext))) {
//                                $sections[] = $newSec;
//                            }
//                        }
//                    
//                }
//				$sectionslist[] = $sections;
//				//print_r($sections);
//                // get rid of the first one
//                
//				
//            }
//			
//			}
//			//print_r($sectionslist);
//    	    return $sectionslist;
//    	
//    	//return array();
//    }
}
