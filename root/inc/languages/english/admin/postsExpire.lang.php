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
 
$l['postsExpireName'] = 'Posts Expire / Closing Threads';
$l['postsExpireDesc'] = 'This plugin provides posts expire time and auto-close threads function.';

$l['postsExpireSettingGroupDesc'] = 'Settings for plugin "Posts Expire / Closing Threads".';

$l['postsExpireEnableExpire'] = 'Status of posts expire option';
$l['postsExpireEnableDescExpire'] = 'Specifies whether the posts expire option is active.';

$l['postsExpireOptionsExpire'] = 'Time options for posts expire';
$l['postsExpireOptionsDescExpire'] = 'All available posts expire time options.
<br />Syntax: "AMOUNTsign" | Available signs:<br />
s - second<br />
m - minute<br />
h - hour<br />
d - day<br />
w - week<br />
Ex. 45m - 45 minutes, 4d - four days dni etc.<br />';

$l['postsExpireDisallowExpire'] = 'Disallowed user groups (posts expire)';
$l['postsExpireDisallowDescExpire'] = 'ID of user groups, which can not be set posts expire time, comma separated.';

$l['postsExpireTimeFormatExpire'] = 'Date format for expire information';
$l['postsExpireTimeFormatDescExpire'] = 'The date format notation to indicate when a post will expire, consistent with the date() PHP function.';

$l['postsExpireEnableClose'] = 'Status of auto-close threads option';
$l['postsExpireEnableDescClose'] = 'Specifies whether the auto-close threads option is active.';

$l['postsExpireOptionsClose'] = 'Time options for auto-close threads';
$l['postsExpireOptionsDescClose'] = 'All available auto-close threads time options.
<br />Syntax: "AMOUNTsign" | Available signs:<br />
s - second<br />
m - minute<br />
h - hour<br />
d - day<br />
w - week<br />
Ex. 45m - 45 minutes, 4d - four days dni etc.<br />';

$l['postsExpireDisallowClose'] = 'Disallowed user groups (auto-close threads)';
$l['postsExpireDisallowDescClose'] = 'ID of user groups, which can not be set auto-close threads time, comma separated.';

$l['postsExpireTimeFormatClose'] = 'Date format for expire information';
$l['postsExpireTimeFormatDescClose'] = 'The date format notation to indicate when a topic will be closed, consistent with the date() PHP function.';

$l['postsExpireTask'] = 'Posts Expire / Closing threads';
$l['postsExpireTaskDesc'] = 'Task for plugin "Posts Expire / Closing threads".';
