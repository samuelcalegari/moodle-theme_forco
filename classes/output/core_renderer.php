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
 * @copyright 2023 Samuel CALEGARI
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_forco\output;

use moodle_url;
use html_writer;
use get_string;

defined('MOODLE_INTERNAL') || die;

class core_renderer extends \theme_boost\output\core_renderer {

    public function frontpage_header() {

        global $PAGE;
        $html = "";

        $hasslide1title = (!empty($PAGE->theme->settings->slide1title));
        $hasslide2title = (!empty($PAGE->theme->settings->slide2title));
        $hasslide3title = (!empty($PAGE->theme->settings->slide3title));

        $hasslide1content = (!empty($PAGE->theme->settings->slide1content));
        $hasslide2content = (!empty($PAGE->theme->settings->slide2content));
        $hasslide3content = (!empty($PAGE->theme->settings->slide3content));

        $hasslide1image = (!empty($PAGE->theme->settings->slide1image));
        $hasslide2image = (!empty($PAGE->theme->settings->slide2image));
        $hasslide3image = (!empty($PAGE->theme->settings->slide3image));

        if(!$hasslide1image && !$hasslide2image && !$hasslide3image)
            return $this->full_header();

        $html .= html_writer::start_div('carousel slide mb-3', array('id' => 'front-page-carousel', 'data-ride' => 'carousel'));

        $html .= html_writer::start_tag('ol', array('class' => 'carousel-indicators'));
        if($hasslide1image) {
            $html .= html_writer::start_tag('li', array('data-target' => '#front-page-carousel', 'data-slide-to' => '0', 'class' => 'active'));
            $html .= html_writer::end_tag('li');
        }
        if($hasslide2image) {
            $html .= html_writer::start_tag('li', array('data-target' => '#front-page-carousel', 'data-slide-to' => '1'));
            $html .= html_writer::end_tag('li');
        }
        if($hasslide3image) {
            $html .= html_writer::start_tag('li', array('data-target' => '#front-page-carousel', 'data-slide-to' => '2'));
            $html .= html_writer::end_tag('li');
        }
        $html .= html_writer::end_tag('ol');

        $html .= html_writer::start_div('carousel-inner', array('role' => 'listbox'));
        if($hasslide1image) {
            $html .= html_writer::start_div('carousel-item active', array('style' => 'background: url(' . $PAGE->theme->setting_file_url('slide1image', 'slide1image') . ')'));
            if($hasslide1title || $hasslide1content) {
                $html .= html_writer::start_div('carousel-caption');
                if ($hasslide1title) {
                    $html .= html_writer::start_tag('h3');
                    $html .= $PAGE->theme->settings->slide1title;
                    $html .= html_writer::end_tag('h3');
                }
                if ($hasslide1content) {
                    $html .= html_writer::start_tag('span', array('class' => 'hidden-md-down'));
                    $html .= $PAGE->theme->settings->slide1content;
                    $html .= html_writer::end_tag('span');
                }
                $html .= html_writer::end_div();
            }
            $html .= html_writer::end_div();
        }
        if($hasslide2image) {
            $html .= html_writer::start_div('carousel-item', array('style' => 'background: url(' . $PAGE->theme->setting_file_url('slide2image', 'slide2image') . ')'));
            if($hasslide2title || $hasslide2content) {
                $html .= html_writer::start_div('carousel-caption');
                if ($hasslide2title) {
                    $html .= html_writer::start_tag('h3');
                    $html .= $PAGE->theme->settings->slide2title;
                    $html .= html_writer::end_tag('h3');
                }
                if ($hasslide2content) {
                    $html .= html_writer::start_tag('span', array('class' => 'hidden-md-down'));
                    $html .= $PAGE->theme->settings->slide2content;
                    $html .= html_writer::end_tag('span');
                }
                $html .= html_writer::end_div();
            }
            $html .= html_writer::end_div();
        }
        if($hasslide3image) {
            $html .= html_writer::start_div('carousel-item', array('style' => 'background: url(' . $PAGE->theme->setting_file_url('slide3image', 'slide3image') . ')'));
            if($hasslide3title || $hasslide3content) {
                $html .= html_writer::start_div('carousel-caption');
                if ($hasslide3title) {
                    $html .= html_writer::start_tag('h3');
                    $html .= $PAGE->theme->settings->slide3title;
                    $html .= html_writer::end_tag('h3');
                }
                if ($hasslide3content) {
                    $html .= html_writer::start_tag('span', array('class' => 'hidden-md-down'));
                    $html .= $PAGE->theme->settings->slide3content;
                    $html .= html_writer::end_tag('span');
                }
                $html .= html_writer::end_div();
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();

        $html .= html_writer::end_div();


        return $html;
    }

    // Zone de Recherche
    public function search_box($id = false) {

        $data = [
            'action' => new moodle_url('/course/search.php'),
            'hiddenfields' => (object) ['name' => 'context', 'value' => $this->page->context->id],
            'inputname' => 'q',
            'searchstring' => get_string('search'),
        ];
        return $this->render_from_template('core/search_input_navbar', $data);
    }

    // Logo
    public function logo() {
        global $OUTPUT;
        return $OUTPUT->image_url('logo', 'theme');
    }

    // Logo SFCA
    public function logosfca() {
        global $OUTPUT;
        return $OUTPUT->image_url('logo-sfca', 'theme');
    }

    // date
    public function current_year(){
        return date("Y");
    }
}
