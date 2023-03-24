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

if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingforco', get_string('configtitle', 'theme_forco'));

    // Each page is a tab - the first is the "General" tab.
    $page = new admin_settingpage('theme_forco_general', get_string('generalsettings', 'theme_forco'));

    //Slide 1
    $page->add(new admin_setting_heading('theme_forco_slider_slide1', get_string('slideshow_slide1', 'theme_forco'),NULL));

    $name = 'theme_forco/slide1title';
    $title = get_string('slidetitle', 'theme_forco');
    $description = get_string('slidetitledesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide1content';
    $title = get_string('slidecontent', 'theme_forco');
    $description = get_string('slidecontentdesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide1image';
    $title = get_string('slideimage', 'theme_forco');
    $description = get_string('slideimagedesc', 'theme_forco');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide1image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    //Slide 2
    $page->add(new admin_setting_heading('theme_forco_slider_slide2', get_string('slideshow_slide2', 'theme_forco'),NULL));

    $name = 'theme_forco/slide2title';
    $title = get_string('slidetitle', 'theme_forco');
    $description = get_string('slidetitledesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide2content';
    $title = get_string('slidecontent', 'theme_forco');
    $description = get_string('slidecontentdesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide2image';
    $title = get_string('slideimage', 'theme_forco');
    $description = get_string('slideimagedesc', 'theme_forco');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide2image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    //Slide 3
    $page->add(new admin_setting_heading('theme_forco_slider_slide3', get_string('slideshow_slide3', 'theme_forco'),NULL));

    $name = 'theme_forco/slide3title';
    $title = get_string('slidetitle', 'theme_forco');
    $description = get_string('slidetitledesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide3content';
    $title = get_string('slidecontent', 'theme_forco');
    $description = get_string('slidecontentdesc', 'theme_forco');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_forco/slide3image';
    $title = get_string('slideimage', 'theme_forco');
    $description = get_string('slideimagedesc', 'theme_forco');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide3image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after defining all the settings!
    $settings->add($page);
}
