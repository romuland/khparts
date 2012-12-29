<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');


class plgSystemGoogleAjaxLib extends JPlugin
{
    var $libraries = array(
        'Mootools' => array('Replace' => 1,
            'Version' => '1.4.1',
            'Name' => 'mootools-core.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/mootools/',
            'Suffix' => '/mootools-yui-compressed.js'),

        'JQuery' => array(
            'Replace' => 0,
            'Version' => '1.8.2',
            'Name' => 'jquery.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/jquery/',
            'Suffix' => '/jquery.min.js'),

        'JQueryUI' => array(
            'Replace' => 0,
            'Version' => '1.8.16',
            'Name' => 'jquery-ui.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/',
            'Suffix' => '/jquery-ui.min.js'),

        'Prototype' => array('Replace' => 0,
            'Version' => '1.7.0.0',
            'Name' => 'prototype.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/prototype/',
            'Suffix' => '/prototype.js'),

        'ScriptAculoUs' => array('Replace' => 0,
            'Version' => '1.9.0',
            'Name' => 'scriptaculous.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/scriptaculous/',
            'Suffix' => '/scriptaculous.js'),

        'SWFObject' => array('Replace' => 0,
            'Version' => '2.2',
            'Name' => 'swfobject.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/swfobject/',
            'Suffix' => '/swfobject.js'),

        'ChromeFrame' => array('Replace' => 0,
            'Version' => '1.0.2',
            'Name' => 'CFInstall.min.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/chrome-frame/',
            'Suffix' => '/CFInstall.min.js'),

        'WebFontLoader' => array('Replace' => 0,
            'Version' => '1.0.24',
            'Name' => 'webfont.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/webfont/',
            'Suffix' => '/webfont.js'),

        'ExtCore' => array('Replace' => 0,
            'Version' => '3.1.0',
            'Name' => 'ext-core.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/ext-core/',
            'Suffix' => '/ext-core.js'),

        'Dojo' => array('Replace' => 0,
            'Version' => '1.5.0',
            'Name' => 'dojo.xd.js',
            'Prefix' => 'https://ajax.googleapis.com/ajax/libs/dojo/',
            'Suffix' => '/dojo/dojo.xd.js'),
    );

    function onAfterRender()
    {
        $app = JFactory::getApplication();

        $noAdmin = $this->params->get("NoAdmin", 1);

        //ignore admin
        if ($noAdmin && $app->isAdmin()) {
            return '';
        }

        $done = JRequest :: getVar('GoogleAjaxLib');

        if ($done) {
            return '';
        }
        $body = JResponse::getBody();
        $scriptRegex = "/<script [^>]+(\/>|><\/script>)/i";
        $body = preg_replace_callback($scriptRegex, array(&$this, '_replaceJs'), $body);
        JResponse::setBody($body);

        JRequest :: setVar('GoogleAjaxLib', '1');
        return '';
    }

    function _replaceJs($matches)
    {

        $scriptStart = '<script type="text/javascript" src="';
        $scriptEnd = '"></script>';
        //print_r($matches);

        foreach ($this->libraries as $library => $config) {
            $replace = $this->param('Replace' . $library, $config['Replace']);
            if ($replace) {
                $version = $this->param($library . 'Version', $config['Version']);
                $name = $this->param($library . 'Name', $config['Name']);
                $googlePrefix = $config['Prefix'];
                $googleSuffix = $config['Suffix'];
                if (strpos($matches[0], $name) !== false) {
                    $google = $googlePrefix . $version . $googleSuffix;
                    return $scriptStart . $google . $scriptEnd;
                }
            }
        }


        return $matches[0];

    }

    function param($name, $default = NULL)
    {
        return $this->params->get($name, $default);
    }
}
