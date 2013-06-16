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

$l['postsExpireName'] = 'Wygasanie postów / zamykanie tematów';
$l['postsExpireDesc'] = 'Ten Plugin pozwala na ustawienie czasu, po którym posty wygasają, a tematy są zamykane.';

$l['postsExpireSettingGroupDesc'] = 'Ustawienia pluginu, "Wygasanie postów / zamykanie tematów".';

$l['postsExpireEnableExpire'] = 'Status opcji wygasania postów';
$l['postsExpireEnableDescExpire'] = 'Określa, czy opcja wygasania postów ma być aktywna.';

$l['postsExpireOptionsExpire'] = 'Dostępne opcje czasowe wygasania';
$l['postsExpireOptionsDescExpire'] = 'Lista wszystkich dostępnych opcji wygasania.
<br />Składnia: "ILOSCoznaczenie" | Dostępne oznaczenia:<br />
s - sekunda<br />
m - minuta<br />
h - godzina<br />
d - dzień<br />
w - tydzień<br />
Np. 45m - 45 minut, 4d - cztery dni itd.<br />';

$l['postsExpireDisallowExpire'] = 'Wykluczone grupy użytkowników (wygasanie)';
$l['postsExpireDisallowDescExpire'] = 'ID grup użytkowników, które nie będą mogły ustawiać czasu wygaśnięcia postów, oddzielone przecinkami.';

$l['postsExpireTimeFormatExpire'] = 'Format daty informacji o wygasaniu';
$l['postsExpireTimeFormatDescExpire'] = 'Format daty zapisu informującego kiedy wygasa post, zgodny z date().';

$l['postsExpireEnableClose'] = 'Status opcji zamykania tematów';
$l['postsExpireEnableDescClose'] = 'Określa, czy opcja zamykania tematów ma być aktywna.';

$l['postsExpireOptionsClose'] = 'Dostępne opcje czasowe zamykania';
$l['postsExpireOptionsDescClose'] = 'Lista wszystkich dostępnych opcji zamykania.
<br />Składnia: "ILOSCoznaczenie" | Dostępne oznaczenia:<br />
s - sekunda<br />
m - minuta<br />
h - godzina<br />
d - dzień<br />
w - tydzień<br />
Np. 45m - 45 minut, 4d - cztery dni itd.<br />';

$l['postsExpireDisallowClose'] = 'Wykluczone grupy użytkowników (zamykanie)';
$l['postsExpireDisallowDescClose'] = 'ID grup użytkowników, które nie będą mogły ustawić czasu zamknięcia wątków, oddzielone przecinkami.';

$l['postsExpireTimeFormatClose'] = 'Format daty informacji o zamknięciu';
$l['postsExpireTimeFormatDescClose'] = 'Format daty zapisu informującego kiedy temat zostanie zamknięty, zgodny z date().';

$l['postsExpireTask'] = 'Wygasanie i zamykanie postów';
$l['postsExpireTaskDesc'] = 'Usuwanie postów i zamykanie tematów, dla których minął czas wygaśnięcia.';
