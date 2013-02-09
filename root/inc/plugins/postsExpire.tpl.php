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
 * Plugin Activator Class
 * 
 */
class postsExpireActivator
{

    private static $tpl = array();

    private static function getTpl()
    {
        global $db;


        self::$tpl[] = array(
            "tid" => NULL,
            "title" => 'postsExpire_expireBody',
            "template" => $db->escape_string('
                </span>
                </td>
                </tr>
                <tr>
                  <td class="trow1" valign="top">
                    <strong>{$lang->postsExpireTplTitleExpire}</strong>
                    <br />
                    <span class="smalltext">{$lang->postsExpireTplTitleExpireDesc}</span>
                  </td>
                  <td class="trow1">
                    <span class="smalltext">
                      <select name="postsexpire_expire">
                        {$postsExpireOptions}
                      </select>'),
            "sid" => "-1",
            "version" => "1.0",
            "dateline" => TIME_NOW,
        );

        self::$tpl[] = array(
            "tid" => NULL,
            "title" => 'postsExpire_closeBody',
            "template" => $db->escape_string('
                </span>
                </td>
                </tr>
                <tr>
                  <td class="trow1" valign="top">
                    <strong>{$lang->postsExpireTplTitleClose}</strong>
                    <br />
                    <span class="smalltext">{$lang->postsExpireTplTitleCloseDesc}</span>
                  </td>
                  <td class="trow1">
                    <span class="smalltext">
                      <select name="postsexpire_close">
                        {$postsExpireOptions}
                      </select>'),
            "sid" => "-1",
            "version" => "1.0",
            "dateline" => TIME_NOW,
        );
    }

    public static function activate()
    {
        global $db;
        self::deactivate();

        for ($i = 0; $i < sizeof(self::$tpl); $i++)
        {
            $db->insert_query('templates', self::$tpl[$i]);
        }

        find_replace_templatesets('newreply', '#' . preg_quote('{$disablesmilies}') . '#', '{$disablesmilies}{$postsExpire}');
        find_replace_templatesets('newthread', '#' . preg_quote('{$disablesmilies}') . '#', '{$disablesmilies}{$postsExpire}');
        find_replace_templatesets('editpost', '#' . preg_quote('{$disablesmilies}') . '#', '{$disablesmilies}{$postsExpire}');
        find_replace_templatesets('postbit_posturl', '#' . preg_quote('{$lang->postbit_post}') . '#', '<!-- EXPIRE_INFO_EXPIRE --> <!-- EXPIRE_INFO_CLOSE --> {$lang->postbit_post}');
    }

    public static function deactivate()
    {
        global $db;
        self::getTpl();

        for ($i = 0; $i < sizeof(self::$tpl); $i++)
        {
            $db->delete_query('templates', "title = '" . self::$tpl[$i]['title'] . "'");
        }

        include MYBB_ROOT . '/inc/adminfunctions_templates.php';
        find_replace_templatesets('newreply', '#' . preg_quote('{$postsExpire}') . '#', '');
        find_replace_templatesets('newthread', '#' . preg_quote('{$postsExpire}') . '#', '');
        find_replace_templatesets('editpost', '#' . preg_quote('{$postsExpire}') . '#', '');
        find_replace_templatesets('postbit_posturl', '#' . preg_quote('<!-- EXPIRE_INFO_EXPIRE --> <!-- EXPIRE_INFO_CLOSE --> ') . '#', '');
    }

}