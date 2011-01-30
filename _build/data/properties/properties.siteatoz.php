<?php

/**
 * Default properties for the SiteAtoZ snippet
 * @author Bob Ray <http://bobsguides.com>
 * 1/15/11
 *
 * @package siteatoz
 * @subpackage build
 */

$properties = array(
    array(
        'name' => 'parents',
        'desc' => 'az_parents_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '0',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'tpl',
        'desc' => 'az_tpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'AzItemTpl',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'useNumbers',
        'desc' => 'az_useNumbers_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'combineNumbers',
        'desc' => 'az_combineNumbers_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'useAlphabet',
        'desc' => 'az_useAlphabet_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '1',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'headingSeparator',
        'desc' => 'az_headingSeparator_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '&nbsp;|&nbsp;',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'alphabetHeadingStart',
        'desc' => 'az_alphabetHeadingStart_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'A',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'alphabetHeadingEnd',
        'desc' => 'az_alphabetHeadingEnd_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'Z',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'title',
        'desc' => 'az_title_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'pagetitle',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'headingLinksTpl',
        'desc' => 'az_headingLinksTpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'noData',
        'desc' => 'az_noData_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'cssFile',
        'desc' => 'az_cssFile_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
        'lexicon' => 'siteatoz:properties',
    ),
    array(
        'name' => 'useJS',
        'desc' => 'az_useJS_desc',
        'type' => 'combo-boolean',
        'options' => '',
        'value' => '0',
        'lexicon' => 'siteatoz:properties',
    ),
);

return $properties;