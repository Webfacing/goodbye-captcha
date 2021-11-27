<p>Hi {admin-full-name},</p>
<p>On {date-time}, a user with Administrator Capabilities signed in on your {current-site-link} website.</p>


<table width="100%"  cellpadding = "0" cellspacing="0" style="border:1px solid #ddd; background-color: transparent; border-spacing:0; border-collapse: collapse">
	<caption style = "text-align: left">User information</caption>
	<thead>
	<tr>

		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px; border-top: 0 none; ">Username</th>
		<th style = "padding:5px; text-align: center; border: 1px solid #ddd; border-bottom-width: 2px;">Ip Address</th>
	</tr>
	</thead>

	<tbody>
	<?php
	echo '<tr>';

	echo '<td style = "text-align: center; border: 1px solid #ddd; border-top: 0 none;">{username}</td>';
	echo '<td style = "text-align: center; border: 1px solid #ddd;">{client-ip}</td>';

	echo '</tr>';
	?>

	</tbody>

</table>


