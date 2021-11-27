<?php
class MchGdbcUnTrustedIPRanges
{
	public static function isAttachingHostIP($ipAddress, $ipVersion = -1)
	{
		(-1 === $ipVersion) ? $ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress) : null;

		if( $ipVersion !== MchGdbcIPUtils::IP_VERSION_4 ) return false;

		$ipNumber = (float)MchGdbcIPUtils::ipAddressToNumber($ipAddress, $ipVersion, true);

		if( $ipNumber < 16815956 )
			return false;

		if( (16815956 <= $ipNumber) && ($ipNumber <= 84516421) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-0.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 84519908 )
			return false;

		if( (84519908 <= $ipNumber) && ($ipNumber <= 96755415) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-1.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 96755416 )
			return false;

		if( (96755416 <= $ipNumber) && ($ipNumber <= 237872199) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-2.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 237872327 )
			return false;

		if( (237872327 <= $ipNumber) && ($ipNumber <= 249516006) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-3.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 249523604 )
			return false;

		if( (249523604 <= $ipNumber) && ($ipNumber <= 406241728) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-4.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 406251751 )
			return false;

		if( (406251751 <= $ipNumber) && ($ipNumber <= 528564490) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-5.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 528565671 )
			return false;

		if( (528565671 <= $ipNumber) && ($ipNumber <= 623923820) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-6.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 623924387 )
			return false;

		if( (623924387 <= $ipNumber) && ($ipNumber <= 675951538) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-7.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 675970053 )
			return false;

		if( (675970053 <= $ipNumber) && ($ipNumber <= 703283687) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-8.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 703298807 )
			return false;

		if( (703298807 <= $ipNumber) && ($ipNumber <= 757305029) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-9.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 757305031 )
			return false;

		if( (757305031 <= $ipNumber) && ($ipNumber <= 779517128) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-10.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 779520649 )
			return false;

		if( (779520649 <= $ipNumber) && ($ipNumber <= 787243941) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-11.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 787248588 )
			return false;

		if( (787248588 <= $ipNumber) && ($ipNumber <= 842933174) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-12.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 842934402 )
			return false;

		if( (842934402 <= $ipNumber) && ($ipNumber <= 977225134) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-13.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 977225138 )
			return false;

		if( (977225138 <= $ipNumber) && ($ipNumber <= 989241583) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-14.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 989248114 )
			return false;

		if( (989248114 <= $ipNumber) && ($ipNumber <= 993618685) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-15.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 993624631 )
			return false;

		if( (993624631 <= $ipNumber) && ($ipNumber <= 1010235438) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-16.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1010235506 )
			return false;

		if( (1010235506 <= $ipNumber) && ($ipNumber <= 1023935986) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-17.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1023943560 )
			return false;

		if( (1023943560 <= $ipNumber) && ($ipNumber <= 1032500079) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-18.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1032500127 )
			return false;

		if( (1032500127 <= $ipNumber) && ($ipNumber <= 1034745206) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-19.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1034747736 )
			return false;

		if( (1034747736 <= $ipNumber) && ($ipNumber <= 1045165259) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-20.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1045165272 )
			return false;

		if( (1045165272 <= $ipNumber) && ($ipNumber <= 1054179060) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-21.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1054188546 )
			return false;

		if( (1054188546 <= $ipNumber) && ($ipNumber <= 1078733845) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-22.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1078733867 )
			return false;

		if( (1078733867 <= $ipNumber) && ($ipNumber <= 1114926791) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-23.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1114965847 )
			return false;

		if( (1114965847 <= $ipNumber) && ($ipNumber <= 1145130860) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-24.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1145131514 )
			return false;

		if( (1145131514 <= $ipNumber) && ($ipNumber <= 1170355661) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-25.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1170355687 )
			return false;

		if( (1170355687 <= $ipNumber) && ($ipNumber <= 1218940292) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-26.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1218940300 )
			return false;

		if( (1218940300 <= $ipNumber) && ($ipNumber <= 1264785034) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-27.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1264810485 )
			return false;

		if( (1264810485 <= $ipNumber) && ($ipNumber <= 1306750979) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-28.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1306990769 )
			return false;

		if( (1306990769 <= $ipNumber) && ($ipNumber <= 1320857191) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-29.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1320857290 )
			return false;

		if( (1320857290 <= $ipNumber) && ($ipNumber <= 1343856820) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-30.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1343868312 )
			return false;

		if( (1343868312 <= $ipNumber) && ($ipNumber <= 1360662274) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-31.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1360715417 )
			return false;

		if( (1360715417 <= $ipNumber) && ($ipNumber <= 1384776054) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-32.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1384780886 )
			return false;

		if( (1384780886 <= $ipNumber) && ($ipNumber <= 1403671947) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-33.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1403671988 )
			return false;

		if( (1403671988 <= $ipNumber) && ($ipNumber <= 1427721836) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-34.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1427725226 )
			return false;

		if( (1427725226 <= $ipNumber) && ($ipNumber <= 1440097139) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-35.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1440097398 )
			return false;

		if( (1440097398 <= $ipNumber) && ($ipNumber <= 1466579533) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-36.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1466579556 )
			return false;

		if( (1466579556 <= $ipNumber) && ($ipNumber <= 1484089732) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-37.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1484090651 )
			return false;

		if( (1484090651 <= $ipNumber) && ($ipNumber <= 1492929568) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-38.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1492930765 )
			return false;

		if( (1492930765 <= $ipNumber) && ($ipNumber <= 1509466763) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-39.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1509466790 )
			return false;

		if( (1509466790 <= $ipNumber) && ($ipNumber <= 1539358102) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-40.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1539359432 )
			return false;

		if( (1539359432 <= $ipNumber) && ($ipNumber <= 1546680005) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-41.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1546680132 )
			return false;

		if( (1546680132 <= $ipNumber) && ($ipNumber <= 1570696786) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-42.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1570698404 )
			return false;

		if( (1570698404 <= $ipNumber) && ($ipNumber <= 1583756695) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-43.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1583756725 )
			return false;

		if( (1583756725 <= $ipNumber) && ($ipNumber <= 1598347056) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-44.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1598353493 )
			return false;

		if( (1598353493 <= $ipNumber) && ($ipNumber <= 1608749058) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-45.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1608754755 )
			return false;

		if( (1608754755 <= $ipNumber) && ($ipNumber <= 1707651878) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-46.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1707652446 )
			return false;

		if( (1707652446 <= $ipNumber) && ($ipNumber <= 1743893391) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-47.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1743893397 )
			return false;

		if( (1743893397 <= $ipNumber) && ($ipNumber <= 1760322495) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-48.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1760328738 )
			return false;

		if( (1760328738 <= $ipNumber) && ($ipNumber <= 1806537569) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-49.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1806537605 )
			return false;

		if( (1806537605 <= $ipNumber) && ($ipNumber <= 1832870789) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-50.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1832870810 )
			return false;

		if( (1832870810 <= $ipNumber) && ($ipNumber <= 1839322799) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-51.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1839322821 )
			return false;

		if( (1839322821 <= $ipNumber) && ($ipNumber <= 1850484620) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-52.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1850486739 )
			return false;

		if( (1850486739 <= $ipNumber) && ($ipNumber <= 1873172073) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-53.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1873187626 )
			return false;

		if( (1873187626 <= $ipNumber) && ($ipNumber <= 1890399768) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-54.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1890403077 )
			return false;

		if( (1890403077 <= $ipNumber) && ($ipNumber <= 1902934298) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-55.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1902937862 )
			return false;

		if( (1902937862 <= $ipNumber) && ($ipNumber <= 1915483543) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-56.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1915518715 )
			return false;

		if( (1915518715 <= $ipNumber) && ($ipNumber <= 1934491730) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-57.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1934491738 )
			return false;

		if( (1934491738 <= $ipNumber) && ($ipNumber <= 1945102180) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-58.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1945102204 )
			return false;

		if( (1945102204 <= $ipNumber) && ($ipNumber <= 1962909599) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-59.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1962909623 )
			return false;

		if( (1962909623 <= $ipNumber) && ($ipNumber <= 1975756348) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-60.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1975758201 )
			return false;

		if( (1975758201 <= $ipNumber) && ($ipNumber <= 1982070497) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-61.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1982076098 )
			return false;

		if( (1982076098 <= $ipNumber) && ($ipNumber <= 1992481365) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-62.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 1992484101 )
			return false;

		if( (1992484101 <= $ipNumber) && ($ipNumber <= 2008811424) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-63.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2008811425 )
			return false;

		if( (2008811425 <= $ipNumber) && ($ipNumber <= 2020780580) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-64.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2020960713 )
			return false;

		if( (2020960713 <= $ipNumber) && ($ipNumber <= 2038571618) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-65.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2038573246 )
			return false;

		if( (2038573246 <= $ipNumber) && ($ipNumber <= 2041580605) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-66.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 2041584305 )
			return false;

		if( (2041584305 <= $ipNumber) && ($ipNumber <= 2054758530) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-67.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 2054760035 )
			return false;

		if( (2054760035 <= $ipNumber) && ($ipNumber <= 2062363530) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-68.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2062363676 )
			return false;

		if( (2062363676 <= $ipNumber) && ($ipNumber <= 2078812996) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-69.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2078827085 )
			return false;

		if( (2078827085 <= $ipNumber) && ($ipNumber <= 2093951485) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-70.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2093952188 )
			return false;

		if( (2093952188 <= $ipNumber) && ($ipNumber <= 2106267565) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-71.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2106268344 )
			return false;

		if( (2106268344 <= $ipNumber) && ($ipNumber <= 2224750591) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-72.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2225093733 )
			return false;

		if( (2225093733 <= $ipNumber) && ($ipNumber <= 2356634406) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-73.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2356650775 )
			return false;

		if( (2356650775 <= $ipNumber) && ($ipNumber <= 2472961268) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-74.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2474049536 )
			return false;

		if( (2474049536 <= $ipNumber) && ($ipNumber <= 2617152121) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-75.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2617166633 )
			return false;

		if( (2617166633 <= $ipNumber) && ($ipNumber <= 2732747209) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-76.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2732793874 )
			return false;

		if( (2732793874 <= $ipNumber) && ($ipNumber <= 2822752917) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-77.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2822753348 )
			return false;

		if( (2822753348 <= $ipNumber) && ($ipNumber <= 2913097697) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-78.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2913117814 )
			return false;

		if( (2913117814 <= $ipNumber) && ($ipNumber <= 2927981098) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-79.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2927985218 )
			return false;

		if( (2927985218 <= $ipNumber) && ($ipNumber <= 2953428323) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-80.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2953428330 )
			return false;

		if( (2953428330 <= $ipNumber) && ($ipNumber <= 2971120485) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-81.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2971133079 )
			return false;

		if( (2971133079 <= $ipNumber) && ($ipNumber <= 2987529079) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-82.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2987529085 )
			return false;

		if( (2987529085 <= $ipNumber) && ($ipNumber <= 2997173255) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-83.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 2997188432 )
			return false;

		if( (2997188432 <= $ipNumber) && ($ipNumber <= 3025970710) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-84.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3025971424 )
			return false;

		if( (3025971424 <= $ipNumber) && ($ipNumber <= 3041436302) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-85.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3041438924 )
			return false;

		if( (3041438924 <= $ipNumber) && ($ipNumber <= 3063289307) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-86.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3063291110 )
			return false;

		if( (3063291110 <= $ipNumber) && ($ipNumber <= 3076941775) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-87.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3076945382 )
			return false;

		if( (3076945382 <= $ipNumber) && ($ipNumber <= 3098038841) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-88.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3098039053 )
			return false;

		if( (3098039053 <= $ipNumber) && ($ipNumber <= 3110803565) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-89.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3110803568 )
			return false;

		if( (3110803568 <= $ipNumber) && ($ipNumber <= 3126013222) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-90.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3126037445 )
			return false;

		if( (3126037445 <= $ipNumber) && ($ipNumber <= 3135988195) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-91.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3135993918 )
			return false;

		if( (3135993918 <= $ipNumber) && ($ipNumber <= 3147122207) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-92.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3147123670 )
			return false;

		if( (3147123670 <= $ipNumber) && ($ipNumber <= 3161854433) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-93.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3161855011 )
			return false;

		if( (3161855011 <= $ipNumber) && ($ipNumber <= 3170363135) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-94.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3170363392 )
			return false;

		if( (3170363392 <= $ipNumber) && ($ipNumber <= 3181590756) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-95.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3181615838 )
			return false;

		if( (3181615838 <= $ipNumber) && ($ipNumber <= 3189864027) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-96.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3189867019 )
			return false;

		if( (3189867019 <= $ipNumber) && ($ipNumber <= 3196806262) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-97.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3196806746 )
			return false;

		if( (3196806746 <= $ipNumber) && ($ipNumber <= 3201995107) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-98.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3201996225 )
			return false;

		if( (3201996225 <= $ipNumber) && ($ipNumber <= 3225798692) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-99.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3225799734 )
			return false;

		if( (3225799734 <= $ipNumber) && ($ipNumber <= 3239278512) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-100.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3239295010 )
			return false;

		if( (3239295010 <= $ipNumber) && ($ipNumber <= 3261479587) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-101.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3261479620 )
			return false;

		if( (3261479620 <= $ipNumber) && ($ipNumber <= 3283273814) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-102.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3283277868 )
			return false;

		if( (3283277868 <= $ipNumber) && ($ipNumber <= 3322767546) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-103.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3322767547 )
			return false;

		if( (3322767547 <= $ipNumber) && ($ipNumber <= 3341852745) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-104.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3341853726 )
			return false;

		if( (3341853726 <= $ipNumber) && ($ipNumber <= 3359011411) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-105.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3359011475 )
			return false;

		if( (3359011475 <= $ipNumber) && ($ipNumber <= 3368711778) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-106.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3368734562 )
			return false;

		if( (3368734562 <= $ipNumber) && ($ipNumber <= 3382940143) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-107.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3382944917 )
			return false;

		if( (3382944917 <= $ipNumber) && ($ipNumber <= 3392466293) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-108.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3392467060 )
			return false;

		if( (3392467060 <= $ipNumber) && ($ipNumber <= 3397614465) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-109.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3397614602 )
			return false;

		if( (3397614602 <= $ipNumber) && ($ipNumber <= 3413180726) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-110.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3413181801 )
			return false;

		if( (3413181801 <= $ipNumber) && ($ipNumber <= 3420586004) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-111.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3420590346 )
			return false;

		if( (3420590346 <= $ipNumber) && ($ipNumber <= 3486889724) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-112.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3487048348 )
			return false;

		if( (3487048348 <= $ipNumber) && ($ipNumber <= 3517718621) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-113.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3517718741 )
			return false;

		if( (3517718741 <= $ipNumber) && ($ipNumber <= 3535877233) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-114.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3535878845 )
			return false;

		if( (3535878845 <= $ipNumber) && ($ipNumber <= 3549446470) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-115.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3549447242 )
			return false;

		if( (3549447242 <= $ipNumber) && ($ipNumber <= 3561483534) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-116.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3561483583 )
			return false;

		if( (3561483583 <= $ipNumber) && ($ipNumber <= 3572198043) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-117.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3572199392 )
			return false;

		if( (3572199392 <= $ipNumber) && ($ipNumber <= 3623908136) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-118.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3623919116 )
			return false;

		if( (3623919116 <= $ipNumber) && ($ipNumber <= 3645247525) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-119.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3645250715 )
			return false;

		if( (3645250715 <= $ipNumber) && ($ipNumber <= 3659124859) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-120.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3659124860 )
			return false;

		if( (3659124860 <= $ipNumber) && ($ipNumber <= 3668323368) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-121.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3668331897 )
			return false;

		if( (3668331897 <= $ipNumber) && ($ipNumber <= 3685125051) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-122.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3685136816 )
			return false;

		if( (3685136816 <= $ipNumber) && ($ipNumber <= 3703428386) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-123.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3703429634 )
			return false;

		if( (3703429634 <= $ipNumber) && ($ipNumber <= 3719599365) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-124.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3719599480 )
			return false;

		if( (3719599480 <= $ipNumber) && ($ipNumber <= 3730859556) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-125.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3730860678 )
			return false;

		if( (3730860678 <= $ipNumber) && ($ipNumber <= 3736739410) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-126.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3736739420 )
			return false;

		if( (3736739420 <= $ipNumber) && ($ipNumber <= 3740309698) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-127.php'; return (isset($a[$ipNumber]) );
		}

		if( $ipNumber < 3740309702 )
			return false;

		if( (3740309702 <= $ipNumber) && ($ipNumber <= 3758080110) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/attackers-ips-128.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		return false;
	}
	public static function isAnonymizerProxyIP($ipAddress, $ipVersion = -1)
	{
		(-1 === $ipVersion) ? $ipVersion = MchGdbcIPUtils::getIpAddressVersion($ipAddress) : null;

		if( $ipVersion !== MchGdbcIPUtils::IP_VERSION_4 ) return false;

		$ipNumber = (float)MchGdbcIPUtils::ipAddressToNumber($ipAddress, $ipVersion, true);

		if( $ipNumber < 16821645 )
			return false;

		if( (16821645 <= $ipNumber) && ($ipNumber <= 391644090) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-0.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 391658042 )
			return false;

		if( (391658042 <= $ipNumber) && ($ipNumber <= 609659443) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-1.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 609798131 )
			return false;

		if( (609798131 <= $ipNumber) && ($ipNumber <= 692932675) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-2.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 692947122 )
			return false;

		if( (692947122 <= $ipNumber) && ($ipNumber <= 774163015) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-3.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 774163016 )
			return false;

		if( (774163016 <= $ipNumber) && ($ipNumber <= 973638951) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-4.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 973638959 )
			return false;

		if( (973638959 <= $ipNumber) && ($ipNumber <= 973801464) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-5.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 973801477 )
			return false;

		if( (973801477 <= $ipNumber) && ($ipNumber <= 973830160) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-6.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 973830226 )
			return false;

		if( (973830226 <= $ipNumber) && ($ipNumber <= 1029309857) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-7.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1029309882 )
			return false;

		if( (1029309882 <= $ipNumber) && ($ipNumber <= 1029323077) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-8.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1029323087 )
			return false;

		if( (1029323087 <= $ipNumber) && ($ipNumber <= 1054003161) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-9.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1054003245 )
			return false;

		if( (1054003245 <= $ipNumber) && ($ipNumber <= 1156237854) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-10.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1156237900 )
			return false;

		if( (1156237900 <= $ipNumber) && ($ipNumber <= 1266079398) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-11.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1266080583 )
			return false;

		if( (1266080583 <= $ipNumber) && ($ipNumber <= 1339019273) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-12.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1339094839 )
			return false;

		if( (1339094839 <= $ipNumber) && ($ipNumber <= 1401665733) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-13.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1401750068 )
			return false;

		if( (1401750068 <= $ipNumber) && ($ipNumber <= 1474259503) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-14.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1474351859 )
			return false;

		if( (1474351859 <= $ipNumber) && ($ipNumber <= 1541444897) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-15.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1541444909 )
			return false;

		if( (1541444909 <= $ipNumber) && ($ipNumber <= 1598501176) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-16.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1598503202 )
			return false;

		if( (1598503202 <= $ipNumber) && ($ipNumber <= 1731740860) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-17.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1731740865 )
			return false;

		if( (1731740865 <= $ipNumber) && ($ipNumber <= 1808274746) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-18.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1808478795 )
			return false;

		if( (1808478795 <= $ipNumber) && ($ipNumber <= 1896580555) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-19.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1896580556 )
			return false;

		if( (1896580556 <= $ipNumber) && ($ipNumber <= 1945683185) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-20.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 1945695093 )
			return false;

		if( (1945695093 <= $ipNumber) && ($ipNumber <= 2017143738) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-21.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2017143792 )
			return false;

		if( (2017143792 <= $ipNumber) && ($ipNumber <= 2088239595) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-22.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2088239597 )
			return false;

		if( (2088239597 <= $ipNumber) && ($ipNumber <= 2088261577) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-23.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2088261578 )
			return false;

		if( (2088261578 <= $ipNumber) && ($ipNumber <= 2088287947) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-24.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2088287956 )
			return false;

		if( (2088287956 <= $ipNumber) && ($ipNumber <= 2088379650) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-25.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2088379661 )
			return false;

		if( (2088379661 <= $ipNumber) && ($ipNumber <= 2088421908) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-26.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2088421910 )
			return false;

		if( (2088421910 <= $ipNumber) && ($ipNumber <= 2160578478) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-27.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2160578909 )
			return false;

		if( (2160578909 <= $ipNumber) && ($ipNumber <= 2513623593) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-28.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2513624368 )
			return false;

		if( (2513624368 <= $ipNumber) && ($ipNumber <= 2875223805) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-29.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2875223815 )
			return false;

		if( (2875223815 <= $ipNumber) && ($ipNumber <= 2875306665) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-30.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2875306681 )
			return false;

		if( (2875306681 <= $ipNumber) && ($ipNumber <= 2946062914) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-31.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2946062975 )
			return false;

		if( (2946062975 <= $ipNumber) && ($ipNumber <= 2982983537) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-32.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 2982983749 )
			return false;

		if( (2982983749 <= $ipNumber) && ($ipNumber <= 3036321097) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-33.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3036324642 )
			return false;

		if( (3036324642 <= $ipNumber) && ($ipNumber <= 3108079587) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-34.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3108096093 )
			return false;

		if( (3108096093 <= $ipNumber) && ($ipNumber <= 3156610265) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-35.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3156610273 )
			return false;

		if( (3156610273 <= $ipNumber) && ($ipNumber <= 3194779555) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-36.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3194804486 )
			return false;

		if( (3194804486 <= $ipNumber) && ($ipNumber <= 3281651149) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-37.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3281651320 )
			return false;

		if( (3281651320 <= $ipNumber) && ($ipNumber <= 3376303403) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-38.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3376509651 )
			return false;

		if( (3376509651 <= $ipNumber) && ($ipNumber <= 3494127283) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-39.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3494132447 )
			return false;

		if( (3494132447 <= $ipNumber) && ($ipNumber <= 3631449609) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-40.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3631453025 )
			return false;

		if( (3631453025 <= $ipNumber) && ($ipNumber <= 3736624901) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-41.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		if( $ipNumber < 3736703545 )
			return false;

		if( (3736703545 <= $ipNumber) && ($ipNumber <= 3758070700) ){
			$a=include  dirname(__FILE__) . '/bad-ip-lists/anonymizers-ips-42.php'; return (isset($a[$ipNumber]) || self::isIPInRange($ipNumber, $a));
		}

		return false;
	}

private static function isIPInRange($ipNumber, $arrIPs)
{
				foreach($arrIPs as $minIpValue => $maxIpValue){
								if( 1 === $maxIpValue ) break;
								$minIpValue < 0 ? $minIpValue += 4294967296 : null;
								if( ($minIpValue < $ipNumber) && ($ipNumber <= $maxIpValue) )
									return true;
							}
							return false;
			
		}
}