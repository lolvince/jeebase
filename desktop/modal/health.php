<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogics = jeebase::byType('jeebase');
?>

<table class="table table-condensed tablesorter" id="table_healthNetatmoWeather">
	<thead>
		<tr>
			<th>{{Module}}</th>
			<th>{{ID}}</th>
            <th>{{Frequence}}</th>
			<th>{{Batterie}}</th>
			<th>{{Level}}</th>
			<th>{{Dernière communication}}</th>
		<!--	<th>{{Wifi}}</th>
			<th>{{RF}}</th>-->
			<th>{{Date création}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
foreach ($eqLogics as $eqLogic) {
	echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getId() . '</span></td>';
	echo '<td>' . $eqLogic->getConfiguration('frequence') . '</td>';
	echo '<td>' . $eqLogic->getConfiguration('bat') . '</td>';
	echo '<td>' . $eqLogic->getConfiguration('level') . '</td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getConfiguration('last_seen') . '</span></td>';
//	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getConfiguration('wifi_status') . '</span></td>';
//	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getConfiguration('rf_status') . '</span></td>';
	echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getConfiguration('createtime') . '</span></td></tr>';
}
?>
	</tbody>
</table>
