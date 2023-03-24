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

// Empèche l'accès direct par l'URL
defined('MOODLE_INTERNAL') || die();

// $THEME est défini avant que cette page ne soit incluse et nous pouvons définir des paramètres en ajoutant des propriétés à cet objet global.

// Nom du thème
$THEME->name = 'forco';

// Ce paramètre liste les feuilles de style que nous voulons inclure dans notre thème. Parce que nous voulons utiliser SCSS au lieu de CSS. -
// nous ne listerons aucune feuille de style. Si c'était le cas, nous listerions le nom d'un fichier dans le dossier / style / pour notre thème
// sans extension de fichier css
$THEME->sheets = [];

// C'est un paramètre qui peut être utilisé pour donner du style au contenu de l'éditeur de texte TinyMCE.
// Ce n'est plus l'éditeur de texte par défaut et "Atto" n'a pas besoin de ce paramètre, donc nous ne fournirons rien.
// Si nous l'avions fait, il fonctionnerait de la même manière que le paramètre précédent - en listant un fichier dans le dossier /styles/.
$THEME->editor_sheets = [];

// Définition du thème parent
$THEME->parents = ['boost'];
$THEME_P = theme_config::load('boost');

// Le dock est un moyen de retirer des blocs de la page et de les placer dans une zone flottante persistante sur le côté de la page.
// Boost ne supporte pas de dock alors nous ne le ferons pas non plus
$THEME->enable_dock = false;

// Ceci est un ancien paramètre utilisé pour charger du CSS pour YUI JS.
// Il n'est plus recommandé d'utiliser ce paramètre.
$THEME->yuicssmodules = array();

// La plupart des thèmes utiliseront ce rendererfactory car ce qui permet au thème de surcharger n'importe quel autre rendu.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Ceci est une liste de blocs qui doivent exister sur toutes les pages pour que ce thème fonctionne correctement.
// Boost ne nécessite pas ces blocs, car il fournit d'autres moyens de navigation intégrés dans le thème.
$THEME->requiredblocks = '';

// Définit où placer les contrôles "Ajouter un bloc" lorsque l'édition est activée.
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// Fait apparaitre le thème dans le sélecteur
$THEME->hidefromselector = false;

$THEME->scss = function($theme) {
    return theme_forco_get_main_scss_content($theme);
};

// Javascript
$THEME->javascripts_footer = array('main');

// Layouts
$layouts = $THEME_P->layouts;
$layouts['frontpage'] = array(
    'file' => 'frontpage.php',
    'regions' => array('side-pre'),
    'defaultregion' => 'side-pre',
    'options' => array('nonavbar' => true),
);
$THEME->layouts = $layouts;

$THEME->iconsystem = '\\theme_forco\\output\\icon_system_fontawesome';

$THEME->haseditswitch = true;

$THEME->activityheaderconfig = [
    'notitle' => true
];
