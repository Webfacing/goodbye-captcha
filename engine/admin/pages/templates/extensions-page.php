<div class="wp-filter">
    <ul class="filter-links">
        <li><a href="<?php echo $premiumExtensionsAdminUrl ?>" class="current"><?php echo $premiumExtensionsText ?></a></li>
    </ul>
</div>

<div class="wp-list-table widefat plugin-install">
    <div id="the-list">


        <?php

            $extensionsListCode = '';

            if(!empty($arrPremiumExtensions))
            {
                foreach ($arrPremiumExtensions as $arrExtensionInfo)
                {
                    $extensionsListCode .= '<div class="plugin-card">';
                    $extensionsListCode .= '<div class="plugin-card-top">';
                    $extensionsListCode .= '<div class="name column-name">';
                    $extensionsListCode .= "<h3><a href=\"{$arrExtensionInfo['url']}\">{$arrExtensionInfo['name']}<img class=\"plugin-icon\" src=\"{$arrExtensionInfo['img-src']}\"></a></h3>";
                    $extensionsListCode .= '</div>';
                    $extensionsListCode .= '<div class="desc column-description">';
                    $extensionsListCode .= "<p>{$arrExtensionInfo['descr']}</p>";
                    $extensionsListCode .= "<p class=\"authors\"><cite>Category: <a href=\"{$arrExtensionInfo['category-url']}\">{$arrExtensionInfo['category-name']}</a></cite></p>";
                    $extensionsListCode .= '</div>';
                    $extensionsListCode .= "<div class=\"action-links\"><ul class=\"plugin-action-buttons\"><li><a href=\"{$arrExtensionInfo['url']}\" class=\"install-now button\">Get this Extension</a></li></ul></div>";
                    $extensionsListCode .= '</div>';
                    $extensionsListCode .= '</div>';
                }
            }

            echo $extensionsListCode;
        ?>

    </div>
</div>
