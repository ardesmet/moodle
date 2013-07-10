<?php
/*
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * This file is part of the Course Menu block for Moodle
 *
 * The Course Menu block for Moodle software package is Copyright � 2008 onwards NetSapiensis AB and is provided under
 * the terms of the GNU GENERAL PUBLIC LICENSE Version 3 (GPL). This program is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version. This program is distributed in the hope that
 * it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.
 *
 * See the GNU General Public License for more details. You should have received a copy of the GNU General Public
 * License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------------------------------------------------------
 */


class block_mesinfos_renderer extends plugin_renderer_base {
	
	private $topic_depth = 1;
	private $chapter_depth = 2;
	private $subchater_depth = 3;
	public $session;
	private $displaysection = 1000;
	public function render_courses($courses,$coursescontent)
    	
	{
            global $DB;
            $html='';
                $i=0;
                
                foreach($courses as $course)
                {
                    $context = get_context_instance(CONTEXT_COURSE, $course->id);
                     $coursecontext = $DB->get_record('course',array('id'=> $course->id));
                    if(!can_access_course($coursecontext) || !$course->visible)
                        continue;
                    
                    $temp = '';
                    $content = $coursescontent[$i];
                    
                    foreach($content as $section)
                    {
                        if($section['visible'] == 1)
                        {
                        //$leaf = html_writer::tag('li', 'test', array ('class' => "type_custom item_with_icon current_branch"));
                       
                            $leaf2= '';
                        foreach($section['resources'] as $mod)
                        {
                            $attributes = NULL;
                            if(isset($mod['onclick'])) $attributes = array('onclick'=> $mod['onclick']);
                            $icon = $this->icon($mod['icon'], $mod['name']);
                            //$leaf2 .= html_writer::tag('ul', $this->render_leaf($mod['name'],$mod['url'],$icon));
                            $leaf2 .= $this->render_leaf($mod['name'],$mod['url'],$icon,$attributes);
                        }
                        
                        //$leaf2 = html_writer::tag('ul', $this->render_leaf($section['name'],$section['url']));
                        if($section['num'] !== 0)
                        {
                        $leaf2 = html_writer::tag('ul',$leaf2);
                        $plink = html_writer::link($section['url'],$section['name']);
                        $pname = html_writer::tag('p', $plink, array('class' => 'tree_item branch root_node'));
                        $temp .= html_writer::tag('li', $pname.$leaf2, array ('class' => "type_system collapsed contains_branch depth_3",'aria-expanded' => 'false'));
                        }
                        else
                        {
                            $temp = $leaf2;
                        }
                        }
                    }
                    $i++;
                    $title = html_writer::tag('span',$course->fullname, array('class' => 'item_name'));
                    $p = html_writer::tag('p', $title, array('class' => 'tree_item branch root_node'));
                    $u = html_writer::tag('ul', $temp);
                    $html .= html_writer::tag('li', $p.$u, array ('class' => "type_system collapsed contains_branch depth_2",'aria-expanded' => 'false'));
                    
                }
		return $html;
		
	}
        public function rend_section($section,$head=false)
        {
            global $OUTPUT;
            $html = '';
            foreach($section as $sec)
            {
                
            }
                
        }
        public function render_leaf($title,$link,$icon,$attributes)
        {
            $html = html_writer::link($link,$icon . $title,$attributes);
            $html = html_writer::tag('p', $html, array ('class' => 'tree_item'));
            //$html .= html_writer::empty_tag('hr') . $html;
            $html = html_writer::tag('li', $html, array ('class' => "item_with_icon"));
            return $html;
        }
        public function navigation_tree(global_navigation $navigation) {
        $navigation->add_class('navigation_node');
        $content = $this->navigation_node(array($navigation), array('class'=>'block_tree list'));
        if (isset($navigation->id) && !is_numeric($navigation->id) && !empty($content)) {
            $content = $this->output->box($content, 'block_tree_box', $navigation->id);
        }
        return $content;
    }
    
    
    protected function navigation_node(navigation_node $node, $attrs=array()) {
        $items = $node->children;

        // exit if empty, we don't want an empty ul element
        if ($items->count()==0) {
            return '';
        }

        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            if (!$item->display) {
                continue;
            }
            print_object($item);
            $isbranch = ($item->children->count()>0  || $item->nodetype==navigation_node::NODETYPE_BRANCH);
            $hasicon = (!$isbranch && $item->icon instanceof renderable);

            if ($isbranch) {
                $item->hideicon = true;
            }
            $content = $this->output->render($item);

            // this applies to the li item which contains all child lists too
            $liclasses = array($item->get_css_type());
            $liexpandable = array();
            if (!$item->forceopen || (!$item->forceopen && $item->collapse) || ($item->children->count()==0  && $item->nodetype==navigation_node::NODETYPE_BRANCH)) {
                $liclasses[] = 'collapsed';
            }
            if ($isbranch) {
                $liclasses[] = 'contains_branch';
                $liexpandable = array('aria-expanded' => in_array('collapsed', $liclasses) ? "false" : "true");
            } else if ($hasicon) {
                $liclasses[] = 'item_with_icon';
            }
            if ($item->isactive === true) {
                $liclasses[] = 'current_branch';
            }
            $liattr = array('class' => join(' ',$liclasses)) + $liexpandable;
            // class attribute on the div item which only contains the item content
            $divclasses = array('tree_item');
            if ($isbranch) {
                $divclasses[] = 'branch';
            } else {
                $divclasses[] = 'leaf';
            }
            if (!empty($item->classes) && count($item->classes)>0) {
                $divclasses[] = join(' ', $item->classes);
            }
            $divattr = array('class'=>join(' ', $divclasses));
            if (!empty($item->id)) {
                $divattr['id'] = $item->id;
            }
            $content = html_writer::tag('p', $content, $divattr) . $this->navigation_node($item);
            if (!empty($item->preceedwithhr) && $item->preceedwithhr===true) {
                $content = html_writer::empty_tag('hr') . $content;
            }
            $content = html_writer::tag('li', $content, $liattr);
            $lis[] = $content;
        }

        if (count($lis)) {
            return html_writer::tag('ul', implode("\n", $lis), $attrs);
        } else {
            return '';
        }
    }
    public function icon($src, $title)
	{
		//print_r($src);
		
		return '<img src="' . $src . '" 
				class="smallicon navicon" title="' . $title . '"
				alt="' . $title . '"/>';
	}
	
}