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

// Empèche l'accès direct par l'URL
defined('MOODLE_INTERNAL') || die();

// version du plugin.
$plugin->version = 2021070800;

// version de Moodle que ce plugin nécessite
$plugin->requires = 2020061511;;

// nom du composant du plugin - il commence toujours par 'theme_'
// pour les thèmes et devrait être le même que le nom du dossier.
$plugin->component = 'theme_forco';

// liste de plugins, ce plugin dépend de (et de leurs versions).
$plugin->dependencies = [
    'theme_boost' => 2020061500,
    'block_accessplus' => ANY_VERSION
];

// numéro de version.
$plugin->release = 0.2;
