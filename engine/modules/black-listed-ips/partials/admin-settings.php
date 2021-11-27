<?php
/**
 * Copyright (C) 2015 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
?>
<style type="text/css">

	.gdbc-settings tr.even
	{
		background-color:#f5f5f5;
	}

	.gdbc-blacklisted-ips-holder input[type="text"]
	{
		width: 99%;
		padding:3px 5px;
	}
	.gdbc-blacklisted-ips-holder thead tbody > tr form > td:first-child
	{
		padding: 0 30px !important;
	}

	.gdbc-blacklisted-ips-holder td
	{
		vertical-align: middle !important;
		word-wrap: normal !important;
	}
	.gdbc-blacklisted-ips-holder th
	{
		font-weight: 700;
	}

	.gdbc-settings .tablenav-pages span.current {
		font-size: 18px;
		font-weight: bold;
		line-height: 30px;
		padding: 4px 6px;
		text-decoration: none;
	}

</style>

<?php

	$formAction = GoodByeCaptcha::isNetworkActivated() ? '' : 'options.php';

?>


<table class="wp-list-table widefat fixed gdbc-blacklisted-ips-holder">
	<thead>

	<tr>
		<th></th>
		<th></th>
		<th></th>
		<th></th>
	</tr>


	<tr class = "even">
		<td  colspan="4" style=" text-align: right">
			<form method="post" action="<?php echo $formAction ?>">
				<div class="clearfix" style="margin:0 auto; width: 700px;">
					<input style = "float: left;clear: left; width: 80%;" type="text" name="<?php echo $this->getBlackListedIpsInputName();?>" placeholder="<?php _e('IP/RANGE/CIDR', GoodByeCaptcha::PLUGIN_SLUG); ?>" required />
					<input style = "float: right;" type="submit" value="<?php _e('Add to BlackList', GoodByeCaptcha::PLUGIN_SLUG); ?>" class="button button-primary" />
				</div>

				<?php settings_fields( $this->getSettingGroupId($this->blackListedIpsGroupIndex) );?>

			</form>
		</td>
	</tr>
	<tr>
		<th width="5%"></th>
		<th width="30%"></th>
		<th width="50%"></th>
		<th width="15%"></th>
	</tr>

	<tr>
		<th width="5%"><?php  _e('Blocked Hosts', GoodByeCaptcha::PLUGIN_SLUG); ?></th>
		<th width="30%"><?php _e('IP Address/Range/CIDR', GoodByeCaptcha::PLUGIN_SLUG); ?></th>
		<th width="60%"><?php _e('Country', GoodByeCaptcha::PLUGIN_SLUG); ?></th>
		<th width="5%" style="text-align: right; padding-right: 22px"><?php _e('Actions', GoodByeCaptcha::PLUGIN_SLUG); ?></th>
	</tr>

	</thead>

	<tbody>
	<?php

	$arrAllIPs = GdbcIPUtils::getFormattedIpRangesForDisplay(GdbcBlackListedIpsAdminModule::getInstance()->getOption(GdbcBlackListedIpsAdminModule::OPTION_BLACK_LISTED_IPS));

	$blackListPageNumber = !empty( $_GET['blackListPageNumber'] ) ? absint( sanitize_text_field($_GET['blackListPageNumber']) ) : 1;

	$recordsPerPage = 10;

	$paginationCode = null;

	$arrRecords = array_chunk($arrAllIPs, $recordsPerPage, true);

	if(isset($arrRecords[1]))
	{
		$paginationCode = paginate_links(
			array(
			'base' =>  add_query_arg( 'blackListPageNumber', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', GoodByeCaptcha::PLUGIN_SLUG ),
			'next_text' => __( '&raquo;', GoodByeCaptcha::PLUGIN_SLUG ),
			'total' => ceil(count($arrAllIPs)/$recordsPerPage),
			'current' => $blackListPageNumber
			)
		);
	}

	if(!empty($arrRecords[$blackListPageNumber - 1]))
		$arrRecords = $arrRecords[$blackListPageNumber - 1];
	else
		$arrRecords = $arrAllIPs;

	foreach($arrRecords as $key => $formattedIp)
	{

		$arrFormattedIp = explode('|', $formattedIp);
		if(count($arrFormattedIp) != 2)
			continue;

		//$countryCode = sanitize_text_field(MchGdbcIPUtils::getCountryCodeByIpAddress($arrFormattedIp[0]));

		$countryName = GdbcIPUtils::getCountryNameByIpAddress($arrFormattedIp[0]);//GoodByeCaptchaUtils::getCountryNameById(GoodByeCaptchaUtils::getCountryIdByCode($countryCode));

		if(empty($countryName))
		{
			$countryName = __('Unavailable', GoodByeCaptcha::PLUGIN_SLUG);
		}

		$rowClass = (($key % 2) == 0) ? 'even' : '';

		echo '<tr class="' . $rowClass . '">';

		echo '<td width="5%">' . $arrFormattedIp[1] . '</td>';
		echo '<td width="30%">' . strtoupper($arrFormattedIp[0]) . '</td>';
		echo '<td width="60%">' . $countryName . '</td>';

		?>

	<td style="text-align: right;" width="5%">
		<form method="post" action="<?php echo $formAction ?>">
			<?php settings_fields( $this->getSettingGroupId($this->blackListedIpsGroupIndex) );?>
			<input type="hidden" name="<?php echo $this->getBlackListedIpsInputName();?>" value="<?php echo 'remove-' . $arrFormattedIp[0]?>" />
			<input type="submit" value="<?php echo __('Remove', GoodByeCaptcha::PLUGIN_SLUG); ?>" class="button" />
		</form>
	</td>



	<?php

	echo '</tr>';

	}


	?>


	</tbody>
</table>


<?php
	if(!empty($paginationCode))
	{
		echo '<div class="postbox-footer clearfix" style="">';
		echo '<div class="tablenav"><div class="tablenav-pages" style="margin:0">' . $paginationCode . '</div></div></div>';
	}
?>


<div class="postbox-footer clearfix" style="">

	<dl style="width: 48%; float: left; border-right: 1px solid #ddd; padding-right: 15px">
		<dt><h4 style="margin: 2px 0; border-bottom: 1px solid #ccc;">Accepted IPV4 Formats</h4></dt>
		<dd style="margin-left: 0">Standard IPV4 format <span style="float: right">123.123.1.1</span></dd>
		<dd style="margin-left: 0">Standard CIDR Block format <span style="float: right">123.123.1.1/32</span></dd>
		<dd style="margin-left: 0">Wildcard Range format <span style="float: right">123.123.1.*</span></dd>
		<dd style="margin-left: 0">Non-Standard Range format <span style="float: right">123.123.1.1 - 123.123.1.10</span></dd>
	</dl>

	<dl style="width: 50%; float: right;" >
		<dt><h4 style="margin: 2px 0; border-bottom: 1px solid #ccc;">Accepted IPV6 Formats</h4></dt>
		<dd style="margin-left: 0">Fully Uncompressed format  <span style="float: right">2002:4559:1FE2:0000:0000:0000:4559:1FE2</span></dd>
		<dd style="margin-left: 0">Standard CIDR Block format <span style="float: right">2002:4559:1FE2::4559:1FE2/128</span></dd>
		<dd style="margin-left: 0">Uncompressed format        <span style="float: right">2002:4559:1FE2:0:0:0:4559:1FE2</span></dd>
		<dd style="margin-left: 0">Compressed format          <span style="float: right">2002:4559:1FE2::4559:1FE2</span></dd>
	</dl>

</div>


