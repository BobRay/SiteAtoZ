<?php
/**
 * SiteAtoZ snippet
 * @author Bob Ray <https://bobsguides.com>
 * @Copyright 2011-2023 Bob Ray
 *
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
 * regardless of any other criteria, and they will be included in every alphabet section.
 * The &where parameter will be ignored because it interferes with the selection by initial letter.
 *
 * Simple use:
 * [[!SiteAtoZ? &parents=`6` &tpl=`MyTpl`]]
 *
 * Required parameters:
 * ---------------
 * @property $parents - (string) Comma-separated list of ID's of container documents you want included (`0` for all docs).
 * @property $tpl - (string) Tpl chunk used to format each entry; Default 'AzItemTpl'.
 *
 * Optional parameters:
 * ---------------
 * @property $useNumbers - (boolean) Put a number array in front of the alphabet; default '0'.
 * @property $combineNumbers (boolean) Group 0-9 titles together; default '0'.
 * @property $useAlphabet - (boolean) Use the Alphabet; default: '1'.
 * @property $headingSeparator - (string) Separator to use between letters in heading; Default '&nbsp|&nbsp;'.
 * @property $alphabetHeadingStart - (string) Letter to start with; Default: 'A'.
 * @property $alphabetHeadingEnd - (string) Letter to end with; Default 'Z'.
 * @property $title - (string) Field to used for search; Default: pagetitle.
 * @property $headingLinksTpl - (string) A tpl containing the entire A-Z heading (useful if you'd like to use images).
 * @property $noData - (string) String to show if search comes up empty.
 * @property $cssFile - (string) Path to css file.
 * @property $useJS - (boolean) - Use JS to hide entries until link is clicked.
 * @property $hideUnsearchable - (boolean) - Hide unsearchable docs in list; default: true.
 * All other parameters are those of getResources. They should all work as they do for getResources with two exceptions:
 * @property $resources -  can be used to exclude documents (e.g., &resources=`-2,24`), but not to include them .
 * @property $where - JSON string with additional criteria).
 * @property $context - Context to search; defaults to default_context System Setting
 * @property $alphabet - Alphabet to use; defaults to en ('A,B,C' ...,Z); see docs for using other alphabets
 */

/* JS script from: http://support.internetconnection.net/CODE_LIBRARY/Javascript_Show_Hide.shtml */

/** @var modX $modx */

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
$base_where = ($where != null ? $where : array());

$headingSeparator = $modx->getOption('headingSeparator', $sp, '');
$headingSeparator = empty($headingSeparator) ? '<span class="az-separator">&nbsp;|&nbsp;</span></div>' . "\n" : $sp['headingSeparator'] . "</div>\n";
$title = $modx->getOption('title', $sp, 'pagetitle', true);
$useNumbers = $modx->getOption('useNumbers', $sp, false, true);
$combineNumbers = $modx->getOption('combineNumbers', $sp, false, true);
$useAlphabet = $modx->getOption('useAlphabet', $sp, true);
$useJS = $modx->getOption('useJS', $sp, false, true);

$hideUnsearchable = $modx->getOption('hideUnsearchable', $sp, true, true);

$context = $modx->getOption('context', $sp, $modx->getOption('default_context', null, true));
$sp['context'] = $context;

$a = $modx->getOption('alphabet', $scriptProperties, 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,Z', true);

// Ł

if ($useNumbers) {
    $alph = $useAlphabet ? $a : '';
    if ($combineNumbers) {

        $a = $useAlphabet ? '[0-9],' . $alph : '[0-9]';
    } else {
        $a = '0,1,2,3,4,5,6,7,8,9,' . $alph;
    }
}

$a = explode(',', $a);

$alphabet = $a;

if ($useJS) {
    $output = '';
    $jsArray = array();
    foreach ($alphabet as $k => $v) {
        $jsArray[] .= "'a" . $v . '_' . $context . "'";
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

if ($hideUnsearchable) {
    $base_where['searchable'] = 1;
}

foreach ($alphabet as $k => $v) {
    $query = array();
    $local_where = $base_where;
    if ($combineNumbers && ($v == '[0-9]')) {
        $local_where[] = array(
            $title . ':REGEXP' => '^[0-9]',
        );
    } elseif (strpos($v, ':') !== false) {
        $temp = explode(':', $v);
        $query = array();
        foreach ($temp as $i => $q) {
            if ($i == 0) {
                $query[] = array($title . ':LIKE ' => $q . '%');
            } else {
                $query[] = array('OR:' . $title . ':LIKE ' => $q . '%');
            }
        }
    } else {
        $query[] = array($title . ':LIKE ' => $v . '%');
    }


    $local_where[] = $query;

    $sp['where'] = $modx->toJSON($local_where);

    // Uncomment next line to see JSON queries
    // echo "\n\n<br><br>" . $sp['where'];
    $ret = $modx->runSnippet($element, $sp);
    if (empty($ret)) {
        $header[] = '        <div class="az-no-results">' . $v;
    } else {
        $noData = false; /* found at least one */
        if (strpos($v, ':') !== false) {
            $displayCharacter = $v[0];
        } else {
            $displayCharacter = $v;
        }
        if ($useJS) {
            $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="[[~[[*id]]]]#" onclick="switchid(' . "'a" . $v . '_' . $context . "'" . ');">' . $displayCharacter . '</a>';
            $output .= '    <div class="az-section" style="display:none;" id="a' . $v . '_' . $context . '">' . "\n";
            $output .= ''; /* no anchors if using JS */
            $output .= '        <div class="az-items">' . $ret . '</div>';
        } else {
            if (strpos($v, ':') !== false) {
                $displayCharacter = $v[0];
            } else {
                $displayCharacter = $v;
            }
            $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="' . $modx->makeUrl($documentId, $context) . '#jump_to_' . $v . '_' . $context . '">' . $displayCharacter . '</a>';
            $output .= "\n" . '    <div class="az-section">' . "\n";
            $output .= '        <p class="az-anchor"><a id="jump_to_' . $v . '_' . $context . '"><span class="az-anchor-letter">' . $displayCharacter . "</span></a></p>\n";
            $output .= '        <div class="az-items">' . $ret . '</div>';
        }
        $output .= "\n</div>"; /* closing az-section */
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