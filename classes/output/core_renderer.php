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
namespace theme_forco\output;
defined('MOODLE_INTERNAL') || die;

use html_writer;
use custom_menu;
use moodle_url;
use stdClass;
use user_picture;

class core_renderer extends \theme_boost\output\core_renderer {

    // Header
    public function full_header() {
        global $PAGE;

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        return $this->render_from_template('theme_boost/head', $header);
    }

    // Header Front-Page (SlideShow)
    public function frontpage_header() {

        global $PAGE;

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

        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'row'));
        $html .= html_writer::start_div('col-xs-12 p-a-1 w-100');

        $html .= html_writer::start_div('carousel slide', array('id' => 'front-page-carousel', 'data-ride' => 'carousel'));

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

        $html .= html_writer::start_tag('a', array('class' => 'carousel-control-prev', 'href'=>'#front-page-carousel', 'role'=>'button', 'data-slide'=>'prev'));
        $html .= html_writer::start_tag('i', array('class' => 'carousel-control-prev-icon', 'aria-hidden'=>'true'));
        $html .= html_writer::end_tag('i');
        $html .= html_writer::start_tag('span', array('class' => 'sr-only'));
        $html .= get_string('previous', 'theme_forco');
        $html .= html_writer::end_tag('span');
        $html .= html_writer::end_tag('a');

        $html .= html_writer::start_tag('a', array('class' => 'carousel-control-next', 'href'=>'#front-page-carousel', 'role'=>'button', 'data-slide'=>'next'));
        $html .= html_writer::start_tag('i', array('class' => 'carousel-control-next-icon', 'aria-hidden'=>'true'));
        $html .= html_writer::end_tag('i');
        $html .= html_writer::start_tag('span', array('class' => 'sr-only'));
        $html .= get_string('next', 'theme_forco');
        $html .= html_writer::end_tag('span');
        $html .= html_writer::end_tag('a');

        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('header');

        return $html;
    }

    // Lien retour haut de page
    public function top_link() {
        return '<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button" data-toggle="tooltip" data-placement="left"><i class="fa fa-chevron-up" aria-hidden="true"></i><span class="sr-only">' . get_string('top_link', 'theme_forco') . '</span></a>';
    }

    // ChatBot
    public function bot() {
        $url = new moodle_url('/blocks/miro_web_bot/bot/index.php');
        return '<a id="bot" href="' . $url . '" class="btn btn-primary btn-lg bot" role="button" data-toggle="tooltip" data-placement="left" target="_blank"><i class="fa fa-comments-o" aria-hidden="true"></i><span class="sr-only">' . get_string('bot', 'theme_forco') . '</span></a>';
    }

    // Zone de Recherche
    public function search_box($id = false) {

        global $CFG;

        // Si la recherche globale est activé => fonctionement normal / appel de la fonction du parent
        if ($CFG->enableglobalsearch) {
            return parent::search_box($id);
        }

        // Sinon recherche des les cours
        if ($id == false) {
            $id = uniqid();
        } else {
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }

        $this->page->requires->js_call_amd('core/search-input', 'init', array($id));

        $searchicon = html_writer::tag('div', $this->pix_icon('a/search', get_string('search', 'search'), 'moodle'),
            array('role' => 'button', 'tabindex' => 0));

        $formattrs = array('class' => 'search-input-form', 'action' => $CFG->wwwroot . '/course/search.php');

        $inputattrs = array('type' => 'text', 'name' => 'search', 'placeholder' => get_string('search', 'search'),
            'size' => 13, 'tabindex' => -1, 'id' => 'coursesearchbox' . $id, 'class' => 'form-control');

        $contents = html_writer::tag('label', get_string('enteryoursearchquery', 'search'),
                array('for' => 'coursesearchbox' . $id, 'class' => 'sr-only')) . html_writer::tag('input', '', $inputattrs);

        $searchinput = html_writer::tag('form', $contents, $formattrs);

        return html_writer::tag('div', $searchicon . $searchinput, array('class' => 'search-input-wrapper nav-link', 'id' => $id));
    }

    // Infos Front Page
    public function frontpage_infos() {

        $html = '';
        $html .= html_writer::link('#skipavailablecourses','Sauter la présentation',array('class' => 'skip skip-block'));

        $html .= html_writer::start_div('',array('id' => 'frontpage-infos'));
        $html .= html_writer::start_div('row');

        $html .= html_writer::start_div('col-lg-12');
        $html .= html_writer::start_tag('h2'). 'La Formation tout au long de la vie' . html_writer::end_tag('h2');
        $html .= html_writer::end_div();

        $html .= html_writer::start_div('col-lg-6');
        $html .= 'La Formation Tout au Long de la Vie est une mission prioritaire de l’Université de Perpignan Via Domitia.Le Service de Formation Continue de l’Université de Perpignan aide tous les publics - salariés, non-salariés (travailleurs indépendants), demandeurs d’emploi - à mettre en place leur projet de formation ou de reconversion. Il a aussi pour mission de vous accompagner dans votre démarche de validation de vos acquis professionnels (VAP) et des acquis de l’expérience (VAE).';
        $html .= html_writer::start_div('',array('style' => 'text-align: right'));
        $html .= html_writer::link('https://sfc.univ-perp.fr','En savoir +',array('class' => 'btn btn-secondary'));
        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $html .= html_writer::start_div('col-lg-6');
        $html .= 'L’ensemble des formations diplômantes et qualifiantes de l’UPVD est accessible en formation continue. Le SFC s’appuie ainsi sur toutes les composantes et instituts de l’UPVD pour mener à bien ses missions.Le SFC met également à votre disposition son savoir-faire en ingénierie de formation en s’appuyant sur le potentiel de recherche et les capacités d’innovation de l’Université pour vos demandes de formations dédiées.';
        $html .= html_writer::end_div();

        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        $html .= html_writer::start_tag('span', array('class' => 'skip-block-to', 'id' => 'skipfrontpageinfos'));
        $html .= html_writer::end_tag('span');
        return $html;
    }

    // Logo
    public function logo() {
        global $OUTPUT;
        return $OUTPUT->image_url('logo', 'theme');
    }

    public function logomiro() {
        global $OUTPUT;
        return $OUTPUT->image_url('logo-miro', 'theme');
    }

    // date
    public function current_year(){
        return date("Y");
    }

    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    public function render_custom_menu(custom_menu $menu) {
        global $CFG;

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            preg_match( '!\(([^\)]+)\)!', $currentlang , $match );
            $currentlang = strtoupper($match[1]);
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

	public function activity_navigation() {
        return '';
    }
}
