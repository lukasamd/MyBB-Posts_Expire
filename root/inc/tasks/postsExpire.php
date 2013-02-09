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

function task_postsExpire($task)
{
    global $db, $lang, $mybb;

    // Check posts table for expire time column
    if (!$db->field_exists('postsexpire_expire', 'posts'))
    {
        return;
    }

    // Load language data
    $lang->load("postsExpire");
    $lang->load("postsExpire", true);

    require_once MYBB_ROOT . "inc/class_moderation.php";
    $moderation = new Moderation;

    // Grab expired posts
    $sql = "SELECT p.pid, p.tid, p.dateline AS post_time, t.dateline AS thread_time 
        FROM " . TABLE_PREFIX . "posts p
        INNER JOIN " . TABLE_PREFIX . "threads t ON p.tid = t.tid
        WHERE p.postsexpire_expire > 0 AND p.postsexpire_expire < " . TIME_NOW;
    $result = $db->query($sql);

    while ($row = $db->fetch_array($result))
    {
        if ($row['post_time'] == $row['thread_time'])
        {
            $moderation->delete_thread($row['tid']);
        }
        else
        {
            $moderation->delete_post($row['pid']);
        }
    }

    // Grab expired threads
    $pids = array();
    $sql = "SELECT p.pid, p.tid, p.dateline AS post_time, t.dateline AS thread_time 
        FROM " . TABLE_PREFIX . "posts p
        INNER JOIN " . TABLE_PREFIX . "threads t ON p.tid = t.tid
        WHERE p.postsexpire_close > 0 AND p.postsexpire_close < " . TIME_NOW;
    $result = $db->query($sql);

    while ($row = $db->fetch_array($result))
    {
        if ($row['post_time'] == $row['thread_time'])
        {
            $moderation->close_threads($row['tid']);
            $pids[] = $row['pid'];
        }
    }

    if (sizeof($pids))
    {
        $pids = implode(',', $pids);
        $update_sql = array('postsexpire_close' => 0);
        $db->update_query("posts", $update_sql, "pid IN ({$pids})");
    }

    add_task_log($task, $lang->postsExpireTaskDesc);
}
