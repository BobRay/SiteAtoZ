<?php
/**
 * SiteAtoZ Build Script
 *
 * Copyright 2011-2023 Bob Ray <https://bobsguides.com>
 *
 * SiteAtoZ is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * SiteAtoZ is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SiteAtoZ; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package siteatoz
 * @subpackage build
 */
/**
 * Build SiteAtoZ Package
 *
 * Description: Build script for SiteAtoZ package
 * @package siteatoz
 * @subpackage build
 */

/* Set package info */
define('PKG_NAME','siteatoz');
define('PKG_VERSION','1.3.3');
define('PKG_RELEASE','pl');
define('PKG_CATEGORY','SiteAtoZ');

/* Set package options - you can turn these on one-by-one
 * as you build the package
 *  */
$hasSnippets = true;
$hasChunks = true;
$hasPlugins = false;
$hasPluginEvents = false;
$hasTemplates = false;
$hasResources = false;
$hasValidator = false;
$hasResolver = false;
$hasSetupOptions = false; /* HTML/PHP script to interact with user */
$hasTemplateVariables = false;
$hasTemplates = false;
$hasMenu = false;
$hasSettings = false;


/* set start time */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Prevent error notices in build */
define('MODX_BASE_URL', 'http://localhost/addons/');
define('MODX_MANAGER_URL', 'http://localhost/addons/manager/');
define('MODX_ASSETS_URL', 'http://localhost/addons/assets/');
define('MODX_CONNECTORS_URL', 'http://localhost/addons/connectors/');

/* define sources */
$root = dirname(dirname(__FILE__)) . '/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    /* note that the next two must not have a trailing slash */
    'source_core' => $root . 'core/components/siteatoz',
    'source_assets' => $root . 'assets/components/siteatoz',
    'data' => $root . '_build/data/',
    'docs' => $root . 'core/components/siteatoz/docs/',
);
unset($root);

/* instantiate MODX -- if this require fails, check your
 *_build/build.config.php file
 */
require_once $sources['build'].'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

/* load builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace('siteatoz',false,true,'{core_path}components/siteatoz/');

/* create snippet objects */

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_CATEGORY);

/* add snippets */
if ($hasSnippets) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in snippets.');
    $snippets = include $sources['data'].'transport.snippets.php';
    /* note: Snippets' default properties are set in transport.snippets.php */
    if (is_array($snippets)) {
        $category->addMany($snippets);
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding snippets failed.'); }
}

if ($hasChunks) { /* add chunks  */
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in chunks.');
    /* note: Chunks' default properties are set in transport.chunks.php */    
    $chunks = include $sources['data'].'transport.chunks.php';
    if (is_array($chunks)) {
        $category->addMany($chunks);
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding chunks failed.'); }
}

if (false) {
    if ($hasTemplates) { /* add templates  */
        $modx->log(modX::LOG_LEVEL_INFO,'Adding in templates.');
        /* note: Templates' default properties are set in transport.templates.php */
        $templates = include $sources['data'].'transport.templates.php';
        if (is_array($templates)) {
            $category->addMany($templates);
        } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templates failed.'); }
    }

    if ($hasTemplateVariables) { /* add templatevariables  */
        $modx->log(modX::LOG_LEVEL_INFO,'Adding in Template Variables.');
        /* note: Template Variables' default properties are set in mytemplate1.templatevariables.php */
        $templatevariables = include $sources['data'].'mytemplate1.templatevariables.php';
        if (is_array($templatevariables)) {
            $category->addMany($templatevariables);
        } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templatevariables failed.'); }
    }
}

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Children' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'Snippets' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
                'Chunks' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
            ),
        ),
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    )
);
/* create a vehicle for the category and all the things
 * we've added to it.
 */
$vehicle = $builder->createVehicle($category,$attr);

/* This section transfers every file in the local
 assets/mycomponents/siteatoz/core/components/siteatoz/assets
 directory to the target site's assets/siteatoz directory on install.
 If the assets dir. has been renamed or moved, they will still
 go to the right place.
 */
$vehicle->resolve('file',array(
        'source' => $sources['source_core'],
        'target' => "return MODX_CORE_PATH . 'components/';",
    ));

/* This section transfers every file in the local 
 assets/mycomponents/siteatoz/core/components/siteatoz/core
 directory to the target site's core/siteatoz directory on install.
 If the core has been renamed or moved, they will still
 go to the right place.
 */

    $vehicle->resolve('file',array(
        'source' => $sources['source_assets'],
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ));
/* Put the category vehicle (with all the stuff we added to the
 * category) into the package 
 */
$builder->putVehicle($vehicle);

/* Load Plugins */

 /* Because plugins have their own related events, it doesn't
 * work to add them to the category. We'll add them here
 * and set the plugin category in the resolver script */
if ($hasPlugins) {
    $attributes = array (
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
    if ($hasPluginEvents) {
        $pe = array(
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'PluginEvents' => array(
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
            ),
        ));
        $attributes = array_merge($attributes, $pe);
    }


        $plugins = include $sources['data'] . 'transport.plugins.php';

        foreach ($plugins as $plugin) {
            $name = strtolower($plugin->get('name'));
            if ($hasPluginEvents) {
                $events = include $sources['data'] . 'events/' . $name . '.events.php';
                if (is_array($events) && !empty($events)) {
                    $modx->log(modX::LOG_LEVEL_INFO,'Added '.count($events).' events to ' . $name);
                    $plugin->addMany($events);
                    unset($events);
                }
            }
            $vehicle = $builder->createVehicle($plugin, $attributes);
            $builder->putVehicle($vehicle);

        }
        unset($vehicle,$attributes,$plugins);
    }

/* Load Templates */
/* Because templates have their own related TVs, it doesn't
 * work to add them to the category. We'll add them here
 * and ToDo: set the plugin category in the resolver script */
if ($hasTemplates) {
    $attributes = array (
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'templatename',
    );
    if ($hasTemplateVariables) {
        $tvt = array(
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'TemplateVarTemplates' => array(
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::UNIQUE_KEY => array('templateid','tmplvarid'),
            ),
        ));
        $attributes = array_merge($attributes, $tvt);
    }


    $templates = include $sources['data'] . 'transport.templates.php';
    if (!is_array($templates)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in templates.');
    } else {
        foreach ($templates as $template) {
            $name = strtolower($template->get('templatename'));
                if ($hasTemplateVariables) {
                    $tvs = include $sources['data'] . 'tvs/' . $name . '.templatevariables.php';
                    if (is_array($tvs) && !empty($tvs)) {
                        $modx->log(modX::LOG_LEVEL_INFO,'Added '.count($tvs).' TVs to ' . $name);
                        $template->addMany($tvs);
                        unset($tvs);
                    } else {
                        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in template variables.');
                    }
                }
                $vehicle = $builder->createVehicle($template, $attributes);
                $builder->putVehicle($vehicle);

                }

    }
    unset($vehicle,$attributes,$templates);
}

/* Transport Resources */

if ($hasResources) {
    $resources = include $sources['data'].'transport.resources.php';
    if (!is_array($resources)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in resources.');
    } else {
        $attributes= array(
            xPDOTransport::UNIQUE_KEY => 'pagetitle',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($resources as $resource) {
            $vehicle = $builder->createVehicle($resource,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($resources).' resources.');
    }
    unset($resources,$resource,$attributes);
}

/* Transport Menus */
if ($hasMenu) {
    /* load menu */
    $modx->log(modX::LOG_LEVEL_INFO,'Packaging in menu...');
    $menu = include $sources['data'].'transport.menu.php';
    if (empty($menu)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in menu.');
    } else {
        $vehicle= $builder->createVehicle($menu,array (
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'text',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'Action' => array (
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
                ),
            ),
        ));
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($menu).' menu items.');
        unset($menu);
    }
}

/* load system settings */
if ($hasSettings) {
    $settings = include $sources['data'].'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
    } else {
        $attributes= array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' System Settings.');
        unset($settings,$setting,$attributes);
    }
}



/* $vehicle = $builder->createVehicle($settings,$attr); */

/* Next-to-last step - pack in the license file, readme.txt, changelog,
 * and setup options 
 */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['source_core'] . '/docs/license.txt'),
    'readme' => file_get_contents($sources['source_core'] . '/docs/readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));

/* Last step - zip up the package */
$builder->pack();

/* report how long it took */
$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(xPDO::LOG_LEVEL_INFO, "Package Built.");
$modx->log(xPDO::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();
