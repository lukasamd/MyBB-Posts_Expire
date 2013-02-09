<?php
/**
 * This file is part of Posts Expire plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Disallow direct access to this file for security reasons
 * 
 */
if (!defined("IN_MYBB")) exit;

/**
 * Create plugin object
 * 
 */
$plugins->objects['postsExpire'] = new postsExpire();

/**
 * Standard MyBB info function
 * 
 */
function postsExpire_info()
{
    global $lang;

    $lang->load("postsExpire");
    
    $lang->postsExpireDesc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="3BTVZBUG6TMFQ">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->postsExpireDesc;

    return Array(
        "name" => $lang->postsExpireName,
        "description" => $lang->postsExpireDesc,
        "website" => "http://lukasztkacz.com",
        "author" => 'Lukasz Tkacz',
        "authorsite" => "http://lukasztkacz.com",
        "version" => "1.4",
        "guid" => "c2b470e3b4521fe3441a12dcd0d8d6af",
        "compatibility" => "16*"
    );
}

/**
 * Standard MyBB installation functions 
 * 
 */
function postsExpire_install()
{
    require_once('postsExpire.settings.php');
    postsExpireInstaller::install();

    rebuildsettings();
}

function postsExpire_is_installed()
{
    global $mybb;

    return (isset($mybb->settings['postsExpireOptionsExpire']));
}

function postsExpire_uninstall()
{
    require_once('postsExpire.settings.php');
    postsExpireInstaller::uninstall();

    rebuildsettings();
}

/**
 * Standard MyBB activation functions 
 * 
 */
function postsExpire_activate()
{
    require_once('postsExpire.tpl.php');
    postsExpireActivator::activate();
}

function postsExpire_deactivate()
{
    require_once('postsExpire.tpl.php');
    postsExpireActivator::deactivate();
}

/**
 * Plugin Class 
 * 
 */
class postsExpire
{

    // Array with all time options
    private $timeOptions = array();
    // Array with choosen option data
    private $chooseOption = array();
    // Array for expire type data
    private $typeData = array();
    
    /**
     * Constructor - add plugin hooks
     */
    public function __construct()
    {
        global $plugins;

        // Add all hooks
        $plugins->hooks["newreply_start"][10]["postsExpire_injectTemplate"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'postsExpire\']->injectTemplate();'));
        $plugins->hooks["newthread_start"][10]["postsExpire_injectTemplate"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'postsExpire\']->injectTemplate();'));
        $plugins->hooks["editpost_action_start"][10]["postsExpire_injectTemplate"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'postsExpire\']->injectTemplate();'));
        
        $plugins->hooks["showthread_start"][10]["postsExpire_loadLanguage"] = array("function" => create_function('', 'global $plugins; $plugins->objects[\'postsExpire\']->loadLanguage();'));
        $plugins->hooks["postbit"][10]["postsExpire_showExpireInfo"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'postsExpire\']->showExpireInfo($arg);'));
        
        $plugins->hooks["datahandler_post_insert_post"][10]["postsExpire_setPostTimeData"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'postsExpire\']->setPostTimeData($arg);'));
        $plugins->hooks["datahandler_post_insert_thread_post"][10]["postsExpire_setPostTimeData"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'postsExpire\']->setPostTimeData($arg);'));
        $plugins->hooks["datahandler_post_update"][10]["postsExpire_setPostTimeData"] = array("function" => create_function('&$arg', 'global $plugins; $plugins->objects[\'postsExpire\']->setPostTimeData($arg);'));
    }    
    
    /**
     * Prepare data to inject
     *  
     */
    public function injectTemplate()
    {
        // Load plugin lang data
        $this->loadLanguage();

        // Inject template for expire time
        $this->setType('expire');
        if (!$this->isDisallowed())
        {
            $this->executeInjectTemplate();
        }

        $this->setType('close');
        if (THIS_SCRIPT != 'newreply.php' && !$this->isDisallowed())
        {
            if (THIS_SCRIPT == 'editpost.php')
            {
                global $post;

                $thread = get_thread($post['tid']);

                if ($thread['dateline'] != $post['dateline'])
                {
                    return false;
                }
            }

            $this->executeInjectTemplate();
        }
    }

    /**
     * Add expire options to template
     * 
     */
    private function executeInjectTemplate()
    {
        global $mybb, $templates, $lang, $postsExpire;

        if (!$this->typeData['status'])
        {
            return;
        }
        
        // Get time options
        $this->grabTimeOptions();

        // Is there any good options?
        if (!sizeof($this->timeOptions))
        {
            return;
        }

        $this->completeTimeOptions();

        $postsExpireOptions = '';
        foreach ($this->timeOptions as $option)
        {
            $selected = '';
            if (isset($option['sel']) && $option['sel'] == 1)
            {
                $selected = ' selected="selected"';
            }
            $postsExpireOptions .= "<option value=\"{$option['val']}\"{$selected}>{$option['name']}</option>";
        }

        $tpl = $this->typeData['template'];
        eval("\$postsExpire .= \"" . $templates->get($tpl) . "\";");
    }

    /**
     * Show information when post expires
     * 
     * @param array $post Reference to post data
     */
    public function showExpireInfo(&$post)
    {
        global $lang, $mybb;

        $post['expireinfo'] = '';
        $post['closeinfo'] = '';
        if ($this->getConfig('EnableExpire') && $post['postsexpire_expire'] > 0)
        {
            $post['expireinfo'] = $lang->postsExpireTplPostInfoExpire;
            $post['expireinfo'] .= my_date($this->getConfig('TimeFormatExpire'), $post['postsexpire_expire']);

            $post['posturl'] = str_replace('<!-- EXPIRE_INFO_EXPIRE -->', $post['expireinfo'], $post['posturl']);
        }

        if ($this->getConfig('EnableClose') && $post['postsexpire_close'] > 0)
        {
            $post['closeinfo'] = $lang->postsExpireTplPostInfoClose;
            $post['closeinfo'] .= my_date($this->getConfig('TimeFormatClose'), $post['postsexpire_close']);

            $post['posturl'] = str_replace('<!-- EXPIRE_INFO_CLOSE -->', $post['closeinfo'], $post['posturl']);
        }
    }

    /**
     * Load plugin language in showthread
     * 
     */
    public function loadLanguage()
    {
        global $lang;

        // Load lang array
        $lang->load("postsExpire");
    }

    /**
     * Set expire time action in post datahandler
     * 
     * @param array $post Reference to post data
     */
    public function setPostTimeData(&$post)
    {
        // Load plugin lang data
        $this->loadLanguage();

        $this->setType('expire');
        $this->setExpireTime($post);

        $this->setType('close');
        $this->setExpireTime($post);
    }

    /**
     * Helper for set expire time action in post datahandler
     * 
     * @param array $post Reference to post data
     */
    private function setExpireTime(&$post)
    {
        if (!$this->typeData['status'] || $this->isDisallowed())
        {
            return;
        }
        
        $this->grabTimeOptions();
        $this->completeTimeOptions();

        if (!$this->getChooseOption())
        {
            return;
        }

        if ($this->chooseOption['val'] > 0)
        {
            $this->chooseOption['val'] += TIME_NOW;
        }

        // Update post expire time!
        $type = $this->typeData['column_name'];

        if (!empty($post->post_update_data))
        {
            $post->post_update_data[$type] = $this->chooseOption['val'];
        }
        elseif (!empty($post->post_insert_data))
        {
            $post->post_insert_data[$type] = $this->chooseOption['val'];
        }
    }

    /**
     * Helper function to check if user is from disallowed group
     * 
     * @return bool True when is disallowed
     */
    private function isDisallowed()
    {
        global $mybb;

        $disallowed_list = explode(",", $this->typeData['option_disallow']);
        $disallowed_list = array_map('intval', $disallowed_list);

        if (in_array($mybb->user['usergroup'], $disallowed_list))
        {
            return true;
        }

        $user_groups = explode(',', $mybb->user['additionalgroups']);
        $disallowed_count = array_intersect($disallowed_list, $user_groups);

        // Is there any good options?
        if (sizeof($disallowed_count))
        {
            return true;
        }

        return false;
    }

    /**
     * Get option for input - return false when no change, or true + set timeOptions 
     * 
     * @return bool True when user choose option from list
     */
    private function getChooseOption()
    {
        global $mybb;

        // Get data type
        $field_name = $this->typeData['column_name'];
        $choosedOption = (int) $mybb->input[$field_name];

        // No change time
        if ($choosedOption == -1)
        {
            return false;
        }

        // Check all time options for choosen
        foreach ($this->timeOptions as $option)
        {
            if ($option['val'] == $choosedOption)
            {
                $this->chooseOption = $option;
                return true;
            }
        }

        return false;
    }

    /**
     * Build time options array from settings 
     * 
     */
    private function grabTimeOptions()
    {
        global $mybb, $lang;

        // Get data type
        $type = $this->typeData['option_time'];

        $expire_options = explode("\n", $mybb->settings[$type]);
        $expire_options = array_map('trim', $expire_options);
        $count_options = sizeof($expire_options);

        $this->timeOptions = array();

        for ($i = 0; $i < $count_options; $i++)
        {
            $preg_results = array();
            $option = array();

            if (!preg_match("#([0-9]+)([smhdw]?)#i", $expire_options[$i], $preg_results))
            {
                continue;
            }
            
            if (!isset($preg_results[1]))
            {
                continue;   
            }
                    
            $preg_results[1] = (int) $preg_results[1];
            $option['val'] = 3600 * $preg_results[1];
            $option['name'] = $lang->postsExpireTimeH;

            // Is there time sign? 
            if (isset($preg_results[2]))
            {
                $preg_results[2] = strtolower($preg_results[2]);

                switch ($preg_results[2])
                {
                    case 's':
                        $option['val'] /= 3600;
                        $option['name'] = $lang->postsExpireTimeS;
                        break;

                    case 'm':
                        $option['val'] /= 60;
                        $option['name'] = $lang->postsExpireTimeM;
                        break;

                    case 'h':
                        break;

                    case 'd':
                        $option['val'] *= 24;
                        $option['name'] = $lang->postsExpireTimeD;
                        break;

                    case 'w':
                        $option['val'] *= 168;
                        $option['name'] = $lang->postsExpireTimeW;
                        break;

                    default:
                        $option['val'] = 0;
                        break;
                }

                $option['name'] = $preg_results[1] . ' ' . $option['name'];
            }
            // Good option, add to options array!
            if ($option['val'] > 0)
            {
                $this->timeOptions[] = $option;
            } 
        }
    }

    /**
     * Add additional, standard options for options array
     * 
     */
    private function completeTimeOptions()
    {
        global $lang;

        // If edit post, add no-change option
        if (THIS_SCRIPT === 'editpost.php')
        {
            $this->timeOptions[] = array(
                'val' => -1,
                'name' => $lang->postsExpireTimeNoChange,
                'sel' => 1,
            );
        }

        $this->timeOptions[] = array(
            'val' => 0,
            'name' => $lang->postsExpireTimeNone,
            'sel' => (THIS_SCRIPT !== 'editpost.php') ? 1 : 0,
        );
    }

    /**
     * Helper Helper function to set expire type
     * 
     * @param string $type Name of config to set
     */
    private function setType($type = '')
    {
        global $mybb;

        switch ($type)
        {
            case 'expire':
            default:
                $this->typeData = array(
                    'column_name' => 'postsexpire_expire',
                    'option_disallow' => $this->getConfig('DisallowExpire'),
                    'option_time' => 'postsExpireOptionsExpire',
                    'template' => 'postsExpire_expireBody',
                    'status' => $this->getConfig('EnableExpire'),
                );
                break;

            case 'close':
                $this->typeData = array(
                    'column_name' => 'postsexpire_close',
                    'option_disallow' => $this->getConfig('DisallowClose'),
                    'option_time' => 'postsExpireOptionsClose',
                    'template' => 'postsExpire_closeBody',
                    'status' => $this->getConfig('EnableClose'),
                );
                break;
        }
    }
    
    /**
     * Helper function to get variable from config
     * 
     * @param string $name Name of config to get
     * @return string Data config from MyBB Settings
     */
    private function getConfig($name)
    {
        global $mybb;

        return $mybb->settings["postsExpire{$name}"];
    }

}
