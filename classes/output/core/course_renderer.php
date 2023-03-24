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
 * Course renderer.
 *
 * @package   theme_forco
 * @copyright 2023 Samuel CALEGARI
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_forco\output\core;

use moodle_url;
use lang_string;
use coursecat_helper;
//use coursecat;
use core_course_category;
use stdClass;
//use course_in_list;
use core_course_list_element;
use pix_url;
use html_writer;
use heading;
use image_url;
use single_select;


/**
 * @package    theme_forco
 * @copyright 2023 Samuel CALEGARI
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {

    public function frontpage_available_courses($id=0) {

        global $CFG, $OUTPUT, $PAGE;
        #require_once ($CFG->libdir . '/coursecatlib.php');

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(array(
            'recursive' => true,
            'limit' => $CFG->frontpagecourselimit,
            'viewmoreurl' => new moodle_url('/course/index.php') ,
            'viewmoretext' => new lang_string('fulllistofcourses')
        ));

        $chelper->set_attributes(array(
            'class' => 'frontpage-course-list-all'
        ));
        $courses = core_course_category::get($id)->get_courses($chelper->get_courses_display_options());
        $rcourseids = array_keys($courses);
        $acourseids = array_chunk($rcourseids, 4);

        $html ='';
        if (count($rcourseids) > 0) {
            foreach ($acourseids as $courseids) {
                $html .= '<div class="row">';
                foreach ($courseids as $courseid) {
                    $course = get_course($courseid);
                    $coursetitle = format_text($course->fullname);
                    $coursesummary = format_text($course->summary);
                    $courseurl = new moodle_url('/course/view.php', array(
                        'id' => $courseid
                    ));
                    $imgurl = $OUTPUT->image_url('course-default', 'theme');

                    if ($course instanceof stdClass) {
                        #require_once ($CFG->libdir . '/coursecatlib.php');
                        $course = new core_course_list_element($course);
                    }

                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        if ($isimage) {
                            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                        }
                    }

                    $html .= $this->display_course_card( $coursetitle, $coursesummary, $imgurl, $courseurl);
                }
                $html .= '</div>';
            }
        }
        return $html;

        //return parent::frontpage_available_courses();
    }

    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {

        global $CFG;

        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            }
            else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage, $paginationurl->out(false, array(
                    'perpage' => $perpage
                )));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                        'perpage' => 'all'
                    )) , get_string('showall', '', $totalcount)) , array(
                        'class' => 'paging paging-showall'
                    ));
                }
            }
            else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::tag('a', html_writer::start_tag('i', array(
                        'class' => 'fa-graduation-cap' . ' fa fa-fw'
                    )) . html_writer::end_tag('i') . $viewmoretext, array(
                    'href' => $viewmoreurl,
                    'class' => 'btn btn-primary coursesmorelink'
                )) , array(
                    'class' => 'paging paging-morelink'
                ));

            }
        }
        else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array(
                'perpage' => $CFG->coursesperpage
            )) , get_string('showperpage', '', $CFG->coursesperpage)) , array(
                'class' => 'paging paging-showperpage'
            ));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        $categoryid = optional_param('categoryid', 0, PARAM_INT);
        $coursecount = 0;

        $content .= $this->view_available_courses($categoryid, $courses, $totalcount);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // .courses


        $content .= '<div class="clearfix"></div>';

        return $content;
    }

    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        global $OUTPUT;

        $title = $coursecat->get_formatted_name();
        $url = new moodle_url('/course/index.php', array(
            'categoryid' => $coursecat->id
        ));

        $chelper = new coursecat_helper();
        $imgurl = $OUTPUT->image_url('coursecat-default', 'theme');

        if($description = $chelper->get_category_formatted_description($coursecat)) {
            $pattern = '/src="([^"]*)"/';
            preg_match($pattern, $description, $matches);
            $imgurl = $matches[1];
            unset($matches);
        }

        return $this->display_coursecat_card($title,$imgurl,$url) ;
    }

    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call coursecat::get_children_count() AFTER coursecat::get_children() to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                    $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                        get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                    array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));
        $content .= html_writer::start_tag('div', array('class' => 'row'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');
        return $content;
    }

    protected function display_course_card($title,$summary,$img,$url,$class='col-lg-3 col-md-6') {
        return '<div class="' . $class . '">
                    <div class="card card-courses">
                        <img class="card-img-top img-responsive" src="' . $img. '" alt="">
                        <div class="card-body">
                            <span class="badge-box"><i class="fa fa-check"></i></span>
                            <h3 class="card-title">' . $title . '</h3>
                            <p class="card-text">' . $summary . '</p>
                            <a href = "' . $url . '" class="btn btn-default text-uppercase" >' . get_string('seecourse', 'theme_forco') . '</a >
                        </div >
                    </div >
                </div >';
    }

    protected function display_coursecat_card($title,$img,$url,$class='col-lg-3 col-md-6') {
        return '<div class="' . $class . '">
                    <div class="card card-courses-catv2">
                        <img class="card-img-top img-responsive" src="' . $img. '" alt="">
                        <div class="card-body">
                            <a href = "' . $url . '"><h3 class="card-title">' . $title . '</h3></a>
                        </div >
                    </div >
                </div >';
    }

    protected function view_available_courses($id = 0, $courses = NULL) {
        global $CFG, $OUTPUT;
        $rcourseids = array_keys($courses);
        $acourseids = array_chunk($rcourseids, 4);

        $html ='';
        if (count($rcourseids) > 0) {
            foreach ($acourseids as $courseids) {
                $html .= '<div class="row">';
                foreach ($courseids as $courseid) {
                    $course = get_course($courseid);
                    $coursetitle = format_text($course->fullname);
                    $coursesummary = format_text($course->summary);
                    $courseurl = new moodle_url('/course/view.php', array(
                        'id' => $courseid
                    ));
                    $imgurl = $OUTPUT->image_url('course-default', 'theme');

                    if ($course instanceof stdClass) {
                        #require_once ($CFG->libdir . '/coursecatlib.php');
                        $course = new core_course_list_element($course);
                    }

                    foreach ($course->get_course_overviewfiles() as $file) {
                        $isimage = $file->is_valid_image();
                        if ($isimage) {
                            $imgurl = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                        }
                    }

                    $html .= $this->display_course_card( $coursetitle, $coursesummary, $imgurl, $courseurl);
                }
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function course_category($category) {
        global $CFG;
        #require_once($CFG->libdir. '/coursecatlib.php');
        $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';
        $categorieslist = core_course_category::make_categories_list();

        if (can_edit_in_category($coursecat->id)) {
            // Add 'Manage' button if user has permissions to edit this category.
            $managebutton = $this->single_button(new moodle_url('/course/management.php',
                array('categoryid' => $coursecat->id)), get_string('managecourses'), 'get');
            $this->page->set_button($managebutton);
        }
        if (!$coursecat->id) {
            if (count($categorieslist) == 1) {
                // There exists only one category in the system, do not display link to it
                $coursecat = core_course_category::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        } else {
            $title = $site->shortname;

            if (count($categorieslist) > 1) {
                $title .= ": ". $coursecat->get_formatted_name();
            }
            $this->page->set_title($title);

            // Print the category selector
            if (count($categorieslist) > 1) {
                $output .= html_writer::start_tag('div', array('class' => 'categorypicker'));
                $select = new single_select(new moodle_url('/course/index.php'), 'categoryid',
                    core_course_category::make_categories_list(), $coursecat->id, null, 'switchcategory');
                $select->set_label(get_string('categories').':');
                $output .= $this->render($select);
                $output .= html_writer::end_tag('div'); // .categorypicker
            }
        }

        // Print current category description
        $chelper = new coursecat_helper();

        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
            ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);
        // Add course search form.
        $output .= $this->course_search_form();

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add action buttons
        $output .= $this->container_start('buttons');
        $context = get_category_or_system_context($coursecat->id);
        if (has_capability('moodle/course:create', $context)) {
            // Print link to create a new course, for the 1st available category.
            if ($coursecat->id) {
                $url = new moodle_url('/course/edit.php', array('category' => $coursecat->id, 'returnto' => 'category'));
            } else {
                $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
            }
            $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
        }
        ob_start();
        if (count($categorieslist) == 1) {
            print_course_request_buttons(context_system::instance());
        } else {
            print_course_request_buttons($context);
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->container_end();

        return $output;
    }
}
