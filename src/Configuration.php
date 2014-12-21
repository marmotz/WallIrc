<?php

namespace Marmotz\WallIrc;

use Marmotz\WallIrc\ConfigurationLoader\ConfigurationLoaderInterface;

class Configuration {
    protected $data;

    public function load(ConfigurationLoaderInterface $configurationLoader)
    {
        $this->setData($configurationLoader->getData());

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function get($name = '', $default = null)
    {
        $data = &$this->data;

        if ($name) {
            $parts = explode('.', $name);

            foreach ($parts as $part) {
                if (isset($data[$part])) {
                    $data = &$data[$part];
                } else {
                    return $default;
                }
            }
        }

        return $data;
    }
}
