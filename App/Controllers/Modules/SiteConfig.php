<?php

namespace App\Modules;

use Core\Modules\Module;
use App\Models\ConfigModel;

class SiteConfig extends Module
{
    /**
     * Gets the entire site configuration and arranges it into a displayable list
     * @return array the config ordered and ready to display
     * @throws \ReflectionException
     */
    public function getSiteConfig()
    {

        $configs = new ConfigModel($this->container);
        $siteConfig = $configs->getAllConfig();
        $data = [];
        foreach ($siteConfig as $config) {
            $data[$config->configs_name] = $config->configs_value;
        }
        return $data;
    }
}