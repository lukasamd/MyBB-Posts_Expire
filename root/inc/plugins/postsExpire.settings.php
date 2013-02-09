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
 * Plugin Installator Class
 * 
 */
class postsExpireInstaller
{

    public static function install()
    {
        global $db, $lang, $mybb;
        self::uninstall();

        $result = $db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder');
        $max_disporder = $db->fetch_field($result, 'max_disporder');
        $disporter = 1;

        $settings_group = array(
            'gid' => 'NULL',
            'name' => 'postsExpire',
            'title' => $lang->postsExpireName,
            'description' => $lang->postsExpireSettingGroupDesc,
            'disporder' => $max_disporder + 1,
            'isdefault' => '0'
        );
        $db->insert_query('settinggroups', $settings_group);
        $gid = (int) $db->insert_id();

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireEnableExpire',
            'title' => $lang->postsExpireEnableExpire,
            'description' => $lang->postsExpireEnableDescExpire,
            'optionscode' => 'onoff',
            'value' => "1",
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireOptionsExpire',
            'title' => $lang->postsExpireOptionsExpire,
            'description' => $lang->postsExpireOptionsDescExpire,
            'optionscode' => 'textarea',
            'value' => "30m\n1h\n6h\n1d\n3d\n1w",
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireDisallowExpire',
            'title' => $lang->postsExpireDisallowExpire,
            'description' => $lang->postsExpireDisallowDescExpire,
            'optionscode' => 'text',
            'value' => '',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireTimeFormatExpire',
            'title' => $lang->postsExpireTimeFormatExpire,
            'description' => $lang->postsExpireTimeFormatDescExpire,
            'optionscode' => 'text',
            'value' => 'd.m.Y H:i',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireEnableClose',
            'title' => $lang->postsExpireEnableClose,
            'description' => $lang->postsExpireEnableDescClose,
            'optionscode' => 'onoff',
            'value' => "1",
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireOptionsClose',
            'title' => $lang->postsExpireOptionsClose,
            'description' => $lang->postsExpireOptionsDescClose,
            'optionscode' => 'textarea',
            'value' => "30m\n1h\n6h\n1d\n3d\n1w",
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireDisallowClose',
            'title' => $lang->postsExpireDisallowClose,
            'description' => $lang->postsExpireDisallowDescClose,
            'optionscode' => 'text',
            'value' => '',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        $setting = array(
            'sid' => 'NULL',
            'name' => 'postsExpireTimeFormatClose',
            'title' => $lang->postsExpireTimeFormatClose,
            'description' => $lang->postsExpireTimeFormatDescClose,
            'optionscode' => 'text',
            'value' => 'd.m.Y H:i',
            'disporder' => $disporter++,
            'gid' => $gid
        );
        $db->insert_query('settings', $setting);

        // Add task to auto-delete expired posts
        $task = array(
            'title' => $db->escape_string($lang->postsExpireTask),
            'description' => $db->escape_string($lang->postsExpireTaskDesc),
            'file' => 'postsExpire',
            'minute' => '*',
            'hour' => '*',
            'day' => '*',
            'month' => '*',
            'weekday' => '*',
            'nextrun' => (time() + 300),
            'lastrun' => '0',
            'enabled' => '1',
            'logging' => '1',
            'locked' => '0'
        );
        $db->insert_query('tasks', $task);

        // Add expire time column to posts table
        $db->add_column('posts', 'postsexpire_expire', "INT(10) NOT NULL DEFAULT '0'");
        $sql = "ALTER TABLE " . TABLE_PREFIX . "posts ADD INDEX postsexpire_expire (postsexpire_expire)";
        $db->query($sql);

        // Add close time column to posts table
        $db->add_column('posts', 'postsexpire_close', "INT(10) NOT NULL DEFAULT '0'");
        $sql = "ALTER TABLE " . TABLE_PREFIX . "posts ADD INDEX postsexpire_close (postsexpire_close)";
        $db->query($sql);
    }

    public static function uninstall()
    {
        global $db;

        $result = $db->simple_select('settinggroups', 'gid', "name = 'postsExpire'");
        $gid = (int) $db->fetch_field($result, "gid");
        
        if ($gid > 0)
        {
            $db->delete_query('settings', "gid = '{$gid}'");
        }
        $db->delete_query('settinggroups', "gid = '{$gid}'");

        $db->delete_query('tasks', "file = 'postsExpire'");
        
        if ($db->index_exists('posts', 'postsexpire_expire'))
        {
            $db->drop_index('posts', 'postsexpire_expire');
        }
        if ($db->field_exists('postsexpire_expire', 'posts'))
        {
            $db->drop_column('posts', 'postsexpire_expire');
        }
        if ($db->index_exists('posts', 'postsexpire_close'))
        {
            $db->drop_index('posts', 'postsexpire_close');
        }
        if ($db->field_exists('postsexpire_close', 'posts'))
        {
            $db->drop_column('posts', 'postsexpire_close');
        }
    }
    
}
