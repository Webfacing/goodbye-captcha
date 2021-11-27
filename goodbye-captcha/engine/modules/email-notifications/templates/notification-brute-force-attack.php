<p>Hi {admin-full-name},</p>
<p>On {detection-date-time}, WPBruiser has detected a Brute Force Attack on your {current-site-link} website.</p>


<table width="100%"  cellpadding = "0" cellspacing="0" style="border:1px solid #ddd; background-color: transparent; border-spacing:0; border-collapse: collapse">
	<caption style = "text-align: left">Statistics at detection time</caption>
	<thead>
	<tr>

		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; border-top: 0 none; ">Total Hits</th>
		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">Total IPs</th>
		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">BlackListed IPs</th>
		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">Web Attackers IPs</th>
		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; ">Anonymizers IPs</th>
	</tr>
	</thead>

	<tbody>
<?php
	echo '<tr>';

	echo '<td style = "text-align: center; border: 1px solid #ddd; border-top: 0 none;">{total-hits}</td>';
	echo '<td style = "text-align: center; border: 1px solid #ddd;">{total-ips}</td>';
	echo '<td style = "text-align: center; border: 1px solid #ddd;">{total-black-listed}</td>';
	echo '<td style = "text-align: center; border: 1px solid #ddd;">{total-web-attackers}</td>';
	echo '<td style = "text-align: center; border: 1px solid #ddd;">{total-proxy-anonymizers}</td>';

	echo '</tr>';
?>

	</tbody>

</table>

<?php
if(!empty($arrSuggestions))
{
?>

	<table width="100%"  cellpadding = "0" cellspacing="0" style="margin-top: 30px; background-color: transparent; border-spacing:0; border-collapse: collapse">
		<caption style = "text-align: left">Suggestions:</caption>
		<tbody>

			<?php
				$suggestionsCounter = 0;
				foreach($arrSuggestions as $suggestion)
				{
					echo '<tr>';
					echo '<td style = "text-align: left; width: 20px;">' . ++$suggestionsCounter . '.</td>';
					echo '<td style = "text-align: left; ">' . $suggestion . '</td>';
					echo '</tr>';
				}
			?>


		</tbody>
	</table>
<?php
}
?>


<!--<table width="100%"  cellpadding = "0" cellspacing="0" style="border:1px solid #ddd; background-color: transparent; border-spacing:0; border-collapse: collapse">-->
<!---->
<!--	<thead>-->
<!--		<tr>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; border-top: 0 none;">No.</th>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">Client IP</th>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">Hits</th>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; ">BlackListed</th>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; ">Attacker</th>-->
<!--			<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; ">Anonymizer</th>-->
<!--		</tr>-->
<!--	</thead>-->
<!---->
<!--	<tbody>-->
<!---->
<?php

//$ipCounter = 0; $arrLoginAttempts = array();
//foreach($arrLoginAttempts as $loginAttempt)
//{
//	echo "<tr>";
//
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . (++$ipCounter) . '</td>';
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . $loginAttempt->ClientIp . '</td>';
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . $loginAttempt->Hits . '</td>';
//
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . ($loginAttempt->IsIpBlackListed ? 'Yes' : 'No') . '</td>';
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . ($loginAttempt->IsIpWebAttacker ? 'Yes' : 'No') . '</td>';
//	echo '<td style = "text-align: center; border: 1px solid #ddd;">' . ($loginAttempt->IsIpProxyAnonym ? 'Yes' : 'No') . '</td>';
//
//	echo "</tr>";
//}

?>
<!---->
<!---->
<!---->
<!--	</tbody>-->
<!--</table>-->
