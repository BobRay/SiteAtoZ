<?php
/**
 * SiteAtoZ snippet
 * @author Bob Ray <https://bobsguides.com>
 * @Copyright 2011-2017 Bob Ray
 * @version 1.0.3
 * 01/25/2011
 *
 * This snippet was inspired by the work of
 * garryn, patricksamshire, and OpenGeek
 * New features added by Jako
 */
/** This snippet makes use of getResources (or a similar element) to list records alphabetically, with an A to Z header of links to anchors in the text.
 *
 * By far the best practice to get it working is to call getResources directly with
 * a snippet tag in a resource. Once you get that working, and it shows every resource
 * you want to index, just change "getResources" to "SiteAtoZ" in the snippet tag.
 * Once that's working, you can add any of the optional parameters listed below.
 *
 * All of the parameters for getResources will be passed through. They can be used
 * to select the parents, sort field, tpl for each entry, etc.
 *
 * The &resources parameter can only be used to exclude resources (&resources=`-12,19`),
 * using it to include docs not work because getResources will include those resources
 * regardless of any other criteria and they will be included in every alphabet section.
 * The &where parameter will be ignored because it interferes with the selection by initial letter.
 *
 * Simple use:
 * [[!SiteAtoZ? &parents=`6` &tpl=`MyTpl`]]
 *
 * Required parameters:
 * ---------------
 * @property parents - (string) Comma-separated list of ID's of container documents you want included (`0` for all docs).
 * @property tpl - (string) Tpl chunk used to format each entry; Default 'AzItemTpl'.
 *
 * Optional parameters:
 * ---------------
 * @property useNumbers - (boolean) Put a number array in front of the alphabet; default '0'.
 * @property combineNumbers (boolean) Group 0-9 titles together; default '0'.
 * @property useAlphabet - (boolean) Use the Alphabet; default: '1'.
 * @property headingSeparator - (string) Separator to use between letters in heading; Default '&nbsp|&nbsp;'.
 * @property alphabetHeadingStart - (string) Letter to start with; Default: 'A'.
 * @property alphabetHeadingEnd - (string) Letter to end with; Default 'Z'.
 * @property title - (string) Field to used for search; Default: pagetitle.
 * @property headingLinksTpl - (string) A tpl containing the entire A-Z heading (useful if you'd like to use images).
 * @property noData - (string) String to show if search comes up empty.
 * @property cssFile - (string) Path to css file.
 * @property useJS - (boolean) - Use JS to hide entries until link is clicked.
 * @property hideUnsearchable - (boolean) - Hide unsearchable docs in list; default: true.
 * All other parameters are those of getResources. They should all work as they do for getResources with two exceptions:
 * @property resources can be used to exclude documents (e.g., &resources=`-2,24`), but not to include them .
 * @property where will be ignored (it conflicts with the selection by initial letter).
 */

/* JS script from: http://support.internetconnection.net/CODE_LIBRARY/Javascript_Show_Hide.shtml */

/* These two lines allow the snippet to run in development environments if the two system settings exist -- do not change or remove them */
$azAssetsUrl = $modx->getOption('az_base_url', null, $modx->getOption('assets_url') . 'components/siteatoz/');
$azAssetsPath = $modx->getOption('az_base_path', null, $modx->getOption('assets_path') . 'components/siteatoz/');

$output = '';
$header = array();

/* save some typing */
$sp =& $scriptProperties;

$documentId = $modx->resource->get('id');

/* Set CSS file path */
$cssFile = $modx->getOption('cssFile', $sp,
    $azAssetsUrl . 'css/siteatoz.css', true);
if (!empty($cssFile)) {
    $modx->regClientCSS($cssFile);
}

/* Set Tpl chunk to use for each item */
$sp['tpl'] = $modx->getOption('tpl', $sp, 'AzItemTpl', true);
$headingLinksTpl = $modx->getOption('headingLinksTpl', $sp, '', true);
/* Set other options */
$element = $modx->getOption('element', $sp, 'getResources');
$sp['parents'] = $modx->getOption('parents', $sp, '0', true);
$sp['noData'] = $modx->getOption('noData', $sp, 'Sorry, No Resources were Retrieved.');
$sp['sortby'] = $modx->getOption('sortby', $sp, '{"pagetitle":"ASC"}', true);
$where = json_decode($modx->getOption('where', $sp, '{}', true), true);
$where = ($where != null ? $where : array());

$headingSeparator = $modx->getOption('headingSeparator', $sp, '');
$headingSeparator = empty($headingSeparator) ? '<span class="az-separator">&nbsp;|&nbsp;</span></div>' . "\n" : $sp['headingSeparator'] . "</div>\n";
$title = $modx->getOption('title', $sp, 'pagetitle', true);
$alphabetHeadingStart = $modx->getOption('alphabetHeadingStart', $sp, 'A', true);
$alphabetHeadingEnd = $modx->getOption('alphabetHeadingEnd', $sp, 'Z', true);
$useNumbers = $modx->getOption('useNumbers', $sp, false, true);
$combineNumbers = $modx->getOption('combineNumbers', $sp, false, true);
$useAlphabet = $modx->getOption('useAlphabet', $sp, true);
$useJS = $modx->getOption('useJS', $sp, false, true);

$hideUnsearchable = $modx->getOption('hideUnsearchable', $sp, true, true);

if ($combineNumbers) {
    $n = array('[0-9]');
} else {
    $n = range('0', '9');
}
// $a = range($alphabetHeadingStart, $alphabetHeadingEnd);
$a = $modx->getOption('alphabet', $scriptProperties, 'A,B,C,D,E,F,G,H,I,J,K,L:Ł,M,N,O,P,Q,R,S,T,U,V,W,Z', true);
$a = explode(',', $a);
$alphabet = array();

if ($useNumbers) {
    $alphabet = $n;
}
if ($useAlphabet) {
    $alphabet = array_merge($alphabet, $a);
}
// unset($n,$a);

if ($useJS) {
    $output = '';
    $jsArray = array();
    foreach ($alphabet as $k => $v) {
        $jsArray[] .= "'a" . $v . "'";
    }
    $jsString = implode(',', $jsArray);
    $startupBlock =
        "<script type=\"text/javascript\">
//<![CDATA[
        var ids= new Array([[+jstring]]);
        ";
    $startupBlock = str_replace('[[+jstring]]', $jsString, $startupBlock);
    $startupBlock .= file_get_contents($azAssetsPath . 'js/siteatoz.js');
    $startupBlock .= "
//]]>
</script>";
    $modx->regClientStartupHTMLBlock($startupBlock);
}

$noData = true;


foreach ($alphabet as $k => $v) {
    $local_where = array();
    $query = array();

    if ($hideUnsearchable) {
        $local_where[] = array('searchable' => 1);
    }

    if ($combineNumbers && ($v == '[0-9]')) {
        $local_where = array(
            $title . ':REGEXP' => '^[0-9]',
        );
    } else {
        $temp = explode(':', $v);

        $query = array();
        foreach ($temp as $i => $q) {
            if ($i == 0) {
                $query[] = array($title . ':LIKE ' => $q . '%');
            } else {
                $query[] = array('OR:' . $title . ':LIKE ' => $q . '%');
            }
        }


        $local_where[] = $query;

//        $local_where += $query;
        // echo "\nLOCAL: " . print_r($local_where, true);
        $sp['where'] = $modx->toJSON($local_where);

        // echo "\n WHERE: " . $sp['where'];
        $ret = $modx->runSnippet($element, $sp);
        if (empty($ret)) {
            $header[] = '        <div class="az-no-results">' . $v;
        } else {
            $noData = false; /* found at least one */
            if ($useJS) {
                $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="[[~[[*id]]]]#" onclick="switchid(' . "'a" . $v . "'" . ');">' . $v . '</a>';
                $output .= '    <div class="az-section" style="display:none;" id="a' . $v . '">' . "\n";
                $output .= ''; /* no anchors if using JS */
                $output .= '        <div class="az-items">' . $ret . '</div>';
            } else {
                $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="' . $modx->makeUrl($documentId) . '#jump_to_' . $v . '">' . $v . '</a>';
                $output .= "\n" . '    <div class="az-section">' . "\n";
                $output .= '        <p class="az-anchor"><a id="jump_to_' . $v . '"><span class="az-anchor-letter">' . $v . "</span></a></p>\n";
                $output .= '        <div class="az-items">' . $ret . '</div>';
            }
            $output .= "\n</div>"; /* closing az-section */
        }
    }
}
if ($noData === true) {
    $modx->setPlaceholder('noData', $sp['noData']);
}
$headingLinks = (empty($headingLinksTpl)) ? implode($headingSeparator, $header) : $modx->getChunk($headingLinksTpl);

if (!empty($headingLinks)) {
    $output = '    <div class="az-header">' . "\n" . $headingLinks . "        </div>\n" . "    </div>\n" . $output;
}
if ($noData) {
    $output = '    <p class="az-noData">' . $sp['noData'] . '</p>' . $output;
}
$output = "\n" . '<div class="az-outer">' . $output . '</div>';
return $output;