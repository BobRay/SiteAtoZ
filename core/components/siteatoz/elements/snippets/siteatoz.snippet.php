<?php
/**
 * SiteAtoZ snippet
 * @author Bob Ray <http://bobsguides.com>
 * @version 1.0.3
 * 01/25/2011
 *
 * This snippet was inspired by the work of
 * garryn, patricksamshire, and OpenGeek
*/
/** This snippet makes use of getResources to list records alphabetically, with an A to Z header of links to anchors in the text.
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
 *
 * All other parameters are those of getResources. They should all work as they do for getResources with two exceptions:
 * @property resources can be used to exclude documents (e.g., &resources=`-2,24`), but not to include them .
 * @property where will be ignored (it conflicts with the selection by initial letter).
 */

/* JS script from: http://support.internetconnection.net/CODE_LIBRARY/Javascript_Show_Hide.shtml */

/* These two lines allow the snippet to run in development environments if the two system settings exist -- do not change or remove them */
$azAssetsUrl =  $modx->getOption('az_base_url', null, $modx->getOption('assets_url') . 'components/siteatoz/');
$azAssetsPath =  $modx->getOption('az_base_path', null, $modx->getOption('assets_path') . 'components/siteatoz/');

$output = '';
$header = array();

/* save some typing */
$sp =& $scriptProperties;

$documentId = $modx->resource->get('id');

/* Set CSS file path */
$cssFile = $modx->getOption('cssFile', $sp, $azAssetsUrl . 'css/siteatoz.css');
if (empty($cssFile)) {
    $cssFile = $azAssetsUrl . 'css/siteatoz.css';
}
$modx->regClientCSS($cssFile);

/* Set Tpl chunk to use for each item */
$sp['tpl'] = empty($sp['tpl'])? 'AzItemTpl' : $sp['tpl'] ;

/* Set other options */
$sp['parents'] = empty($sp['parents'])? '0' : $sp['parents'];
$sp['noData'] = empty($sp['noData'])? 'Sorry, No Resources were Retrieved.' : $sp['noData'];
$headingSeparator = empty($sp['headingSeparator'])? '<span class="az-separator">&nbsp;|&nbsp;</span></div>'. "\n" : $sp['headingSeparator'] . "</span></div>\n";
$title = empty($sp['title'])? 'pagetitle' : $sp['title'];
$alphabetHeadingStart = (empty($sp['alphabetHeadingStart']))? 'A' : $sp['alphabetHeadingStart'];
$alphabetHeadingEnd = (empty($sp['alphabetHeadingEnd']))? 'Z' : $sp['alphabetHeadingEnd'];
$useNumbers = $sp['useNumbers'] === '1'? true : false;
$combineNumbers = $sp['combineNumbers'] === '1'? true : false;
$useAlphabet = $sp['useAlphabet'] === '0'? false: true;
$useJS = $sp['useJS'] == '1'? true : false;

if ($combineNumbers) {
    $n = array('[0-9]');
} else {
    $n = range('0','9');
}
$a = range($alphabetHeadingStart, $alphabetHeadingEnd);
$alphabet = array();

if ($useNumbers) {
    $alphabet=$n;
}
if ($useAlphabet) {
    $alphabet = array_merge($alphabet,$a);
}
unset($n,$a);

if ($useJS) {
    $output = '';
    $jsArray = array();
    foreach ($alphabet as $k=>$v) {
        $jsArray[] .= "'a" . $v . "'";
    }
    $jsString = implode(',',$jsArray);
    $startupBlock =
        "<script type=\"text/javascript\">
//<![CDATA[
        var ids= new Array([[+jstring]]);
        ";
    $startupBlock = str_replace('[[+jstring]]',$jsString, $startupBlock);
    $startupBlock .= file_get_contents($azAssetsPath . 'js/siteatoz.js');
    $startupBlock .= "
//]]>
</script>";
    $modx->regClientStartupHTMLBlock($startupBlock);
}

$whereProperty = !empty($sp['where'])? $sp['where'] : false;

$noData = true;
foreach ($alphabet as $k=>$v) {
    if ($combineNumbers && ($v == '[0-9]') ) {
        $local_where = array(
            $title . ':REGEXP' => '^[0-9]',
        );
    } else {
        $local_where = array(
            $title . ':LIKE' => $v . '%',
        );
    }
    /* ToDo: Some day this may work */
    /*if ($whereProperty !== false) {
        $w = $modx->fromJSON($sp['where']);
        $local_where = array_merge($local_where,$w);
        unset($w);
    }*/
    $sp['where'] = $modx->toJSON($local_where);

    $ret = $modx->runSnippet('getResources',$sp);
    if (empty($ret)) {
        $header[] = '        <div class="az-no-results">' . $v;
    } else {
        $noData = false; /* found at least one */
        if ($useJS) {
           $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="[[~[[*id]]]]#" onclick="switchid(' . "'a" . $v . "'" .');">' . $v . '</a>';
            $output .= '    <div class="az-section" style="display:none;" id="a' . $v . '">' . "\n";
            $output .= ''; /* no anchors if using JS */
            $output .= $ret;
        } else {
            $header[] = '        <div class="az-headeritem"><a class="az-headeritem" href="'.$modx->makeUrl($documentId).'#jump_to_' . $v . '">' . $v . '</a>';
            $output .= '    <div class="az-section">' . "\n";
            $output .= '        <p class="az-anchor"><a id="jump_to_' . $v . '"><span class="az-anchor-letter">' . $v . "</span></a></p>\n";
            $output .= $ret;
        }
        $output .= "\n</div>"; /* closing az-section */
    }
}
if ($noData === true) {
    $modx->setPlaceholder('noData',$sp['noData']);
}
$headingLinks = (empty($sp['headingLinksTpl']))? implode($headingSeparator,$header) : $modx->getChunk($sp['headingLinksTpl']);

return '    <div class="az-outer"><div class="az-header">' . "\n" . $headingLinks . "</div>\n        </div>\n" .  '        <p class="az-noData">[[+noData]]</p>' . $output . "\n    </div>";

