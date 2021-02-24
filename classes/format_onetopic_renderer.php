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
 * @package   theme_forco
 * @copyright 2018 Samuel CALEGARI
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/onetopic/renderer.php');

class theme_forco_format_onetopic_renderer extends format_onetopic_renderer {

    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE, $OUTPUT;;

        $realcoursedisplay = $course->realcoursedisplay;
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();
        $course->realcoursedisplay = $realcoursedisplay;
        $sections = $modinfo->get_section_info_all();

        // Can we view the section in question?
        $context = context_course::instance($course->id);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);

        if (!isset($sections[$displaysection])) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', course_get_url($course),
                format_string($course->fullname));
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        $formatdata = new stdClass();
        $formatdata->mods = $mods;
        $formatdata->modinfo = $modinfo;
        $this->_course = $course;
        $this->_format_data = $formatdata;

        // General section if non-empty and course_display is multiple.
        if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $thissection = $sections[0];
            if ((($thissection->visible && $thissection->available) || $canviewhidden) &&
                ($thissection->summary || $thissection->sequence || $PAGE->user_is_editing() ||
                    (string)$thissection->name !== '')) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true);

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);

                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section onetopic'));

        // Move controls.
        $canmove = false;
        if ($PAGE->user_is_editing() && has_capability('moodle/course:movesections', $context) && $displaysection > 0) {
            $canmove = true;
        }
        $movelisthtml = '';

        // Init custom tabs.
        $section = 0;

        $sectionmenu = array();
        $tabs = array();
        $inactivetabs = array();

        $defaulttopic = -1;

        while ($section <= $this->numsections) {

            if ($course->realcoursedisplay == COURSE_DISPLAY_MULTIPAGE && $section == 0) {
                $section++;
                continue;
            }

            $thissection = $sections[$section];

            $showsection = true;
            if (!$thissection->visible || !$thissection->available) {
                $showsection = false;
            } else if ($section == 0 && !($thissection->summary || $thissection->sequence || $PAGE->user_is_editing())) {
                $showsection = false;
            }

            if (!$showsection) {
                $showsection = $canviewhidden || !$course->hiddensections;
            }

            if (isset($displaysection)) {
                if ($showsection) {

                    if ($defaulttopic < 0) {
                        $defaulttopic = $section;

                        if ($displaysection == 0) {
                            $displaysection = $defaulttopic;
                        }
                    }

                    $formatoptions = course_get_format($course)->get_format_options($thissection);

                    $sectionname = get_section_name($course, $thissection);

                    if ($displaysection != $section) {
                        $sectionmenu[$section] = $sectionname;
                    }

                    $customstyles = '';
                    $level = 0;
                    if (is_array($formatoptions)) {

                        if (!empty($formatoptions['fontcolor'])) {
                            $customstyles .= 'color: ' . $formatoptions['fontcolor'] . ';';
                        }

                        if (!empty($formatoptions['bgcolor'])) {
                            $customstyles .= 'background-color: ' . $formatoptions['bgcolor'] . ';';
                        }

                        if (!empty($formatoptions['cssstyles'])) {
                            $customstyles .= $formatoptions['cssstyles'] . ';';
                        }

                        if (isset($formatoptions['level'])) {
                            $level = $formatoptions['level'];
                        }
                    }

                    if ($section == 0) {
                        $url = new moodle_url('/course/view.php', array('id' => $course->id, 'section' => 0));
                    } else {
                        $url = course_get_url($course, $section);
                    }

                    $specialstyle = 'tab_position_' . $section . ' tab_level_' . $level;
                    if ($course->marker == $section) {
                        $specialstyle = ' marker ';
                    }

                    if (!$thissection->visible || !$thissection->available) {
                        $specialstyle .= ' dimmed ';

                        if (!$canviewhidden) {
                            $inactivetabs[] = "tab_topic_" . $section;
                        }
                    }

                    $newtab = new tabobject("tab_topic_" . $section, $url,
                        '<div style="' . $customstyles . '" class="tab_content ' . $specialstyle . '">' .
                        '<span>' . $sectionname . "</span></div>", $sectionname);

                    if (is_array($formatoptions) && isset($formatoptions['level'])) {

                        if ($formatoptions['level'] == 0 || count($tabs) == 0) {
                            $tabs[] = $newtab;
                            $newtab->level = 1;
                        } else {
                            $parentindex = count($tabs) - 1;
                            if (!is_array($tabs[$parentindex]->subtree)) {
                                $tabs[$parentindex]->subtree = array();
                            } else if (count($tabs[$parentindex]->subtree) == 0) {
                                $tabs[$parentindex]->subtree[0] = clone($tabs[$parentindex]);
                                $tabs[$parentindex]->subtree[0]->id .= '_index';
                                $parentsection = $sections[$section - 1];
                                $parentformatoptions = course_get_format($course)->get_format_options($parentsection);
                                if ($parentformatoptions['firsttabtext']) {
                                    $firsttabtext = $parentformatoptions['firsttabtext'];
                                } else {
                                    $firsttabtext = get_string('index', 'format_onetopic');
                                }
                                $tabs[$parentindex]->subtree[0]->text = '<div class="tab_content tab_initial">' .
                                    $firsttabtext . "</div>";
                                $tabs[$parentindex]->subtree[0]->level = 2;

                                if ($displaysection == $section - 1) {
                                    $tabs[$parentindex]->subtree[0]->selected = true;
                                }
                            }
                            $newtab->level = 2;
                            $tabs[$parentindex]->subtree[] = $newtab;
                        }
                    } else {
                        $tabs[] = $newtab;
                    }

                    // Init move section list.
                    if ($canmove) {
                        if ($section > 0) { // Move section.
                            $baseurl = course_get_url($course, $displaysection);
                            $baseurl->param('sesskey', sesskey());

                            $url = clone($baseurl);

                            $url->param('move', $section - $displaysection);

                            // Define class from sublevels in order to move a margen in the left.
                            // Not apply if it is the first element (condition !empty($movelisthtml))
                            // because the first element can't be a sublevel.
                            $liclass = '';
                            if (is_array($formatoptions) && isset($formatoptions['level']) && $formatoptions['level'] > 0 &&
                                !empty($movelisthtml)) {
                                $liclass = 'sublevel';
                            }

                            if ($displaysection != $section) {
                                $movelisthtml .= html_writer::tag('li', html_writer::link($url, $sectionname),
                                    array('class' => $liclass));
                            } else {
                                $movelisthtml .= html_writer::tag('li', $sectionname, array('class' => $liclass));
                            }
                        } else {
                            $movelisthtml .= html_writer::tag('li', $sectionname);
                        }
                    }
                    // End move section list.
                }
            }

            $section++;
        }

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $sections, $displaysection);
        $sectiontitle = '';

        if (!$course->hidetabsbar && count($tabs[0]) > 0) {

            if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {

                // Increase number of sections.
                $straddsection = get_string('increasesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                        'increase' => true,
                        'sesskey' => sesskey(),
                        'insertsection' => 0));
                $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
                $tabs[] = new tabobject("tab_topic_add", $url, $icon, s($straddsection));

            }

            $sectiontitle .= $OUTPUT->tabtree($tabs, "tab_topic_" . $displaysection, $inactivetabs);
        }

        echo $sectiontitle;

        if ($sections[$displaysection]->uservisible || $canviewhidden) {

            if ($course->realcoursedisplay != COURSE_DISPLAY_MULTIPAGE || $displaysection !== 0) {
                // Now the list of sections.
                echo $this->start_section_list();

                // The requested section page.
                $thissection = $sections[$displaysection];
                echo $this->section_header($thissection, $course, true);
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $completioninfo->display_help_icon();

                if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_NOT) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                } else if ($this->_course->templatetopic == format_onetopic::TEMPLATETOPIC_LIST) {
                    echo $this->custom_course_section_cm_list($course, $thissection, $displaysection);
                }

                echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }
        }

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $this->convertLinkButton($sectionnavlinks['previous']), array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $this->convertLinkButton($sectionnavlinks['next']), array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {

            echo '<br class="utilities-separator" />';
            print_collapsible_region_start('move-list-box clearfix collapsible mform', 'course_format_onetopic_config_movesection',
                get_string('utilities', 'format_onetopic'), '', true);

            // Move controls.
            if ($canmove && !empty($movelisthtml)) {
                echo html_writer::start_div("form-item clearfix");
                echo html_writer::start_div("form-label");
                echo html_writer::tag('label', get_string('movesectionto', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-setting");
                echo html_writer::tag('ul', $movelisthtml, array('class' => 'move-list'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-description");
                echo html_writer::tag('p', get_string('movesectionto_help', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::end_div();
            }

            $baseurl = course_get_url($course, $displaysection);
            $baseurl->param('sesskey', sesskey());

            $url = clone($baseurl);

            global $USER, $OUTPUT;
            if (isset($USER->onetopic_da[$course->id]) && $USER->onetopic_da[$course->id]) {
                $url->param('onetopic_da', 0);
                $textbuttondisableajax = get_string('enable', 'format_onetopic');
            } else {
                $url->param('onetopic_da', 1);
                $textbuttondisableajax = get_string('disable', 'format_onetopic');
            }

            echo html_writer::start_div("form-item clearfix");
            echo html_writer::start_div("form-label");
            echo html_writer::tag('label', get_string('disableajax', 'format_onetopic'));
            echo html_writer::end_div();
            echo html_writer::start_div("form-setting");
            echo html_writer::link($url, $textbuttondisableajax);
            echo html_writer::end_div();
            echo html_writer::start_div("form-description");
            echo html_writer::tag('p', get_string('disableajax_help', 'format_onetopic'));
            echo html_writer::end_div();
            echo html_writer::end_div();

            // Duplicate current section option.
            if (has_capability('moodle/course:manageactivities', $context)) {
                $urlduplicate = new moodle_url('/course/format/onetopic/duplicate.php',
                    array('courseid' => $course->id, 'section' => $displaysection, 'sesskey' => sesskey()));

                $link = new action_link($urlduplicate, get_string('duplicate', 'format_onetopic'));
                $link->add_action(new confirm_action(get_string('duplicate_confirm', 'format_onetopic'), null,
                    get_string('duplicate', 'format_onetopic')));

                echo html_writer::start_div("form-item clearfix");
                echo html_writer::start_div("form-label");
                echo html_writer::tag('label', get_string('duplicatesection', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::start_div("form-setting");
                echo $this->render($link);
                echo html_writer::end_div();
                echo html_writer::start_div("form-description");
                echo html_writer::tag('p', get_string('duplicatesection_help', 'format_onetopic'));
                echo html_writer::end_div();
                echo html_writer::end_div();
            }

            echo html_writer::start_div("form-item clearfix form-group row fitem");
            echo $this->change_number_sections($course, 0);
            echo html_writer::end_div();

            print_collapsible_region_end();
        }
    }

    protected function convertLinkButton($link) {
       return str_replace('>', ' class="btn btn-primary" >', $link);
    }
}

