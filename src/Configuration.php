<?php

namespace Marmotz\WallIrc;

use Marmotz\WallIrc\ConfigurationLoader\ConfigurationLoaderInterface;

class Configuration {
    protected $data;

    public function get($name = '', $default = null, $noneAsException = false)
    {
        $data = &$this->data;

        if ($name) {
            $parts = explode('.', $name);

            foreach ($parts as $part) {
                if (isset($data[$part])) {
                    $data = &$data[$part];
                } else {
                    if ($noneAsException) {
                        throw new \OutOfBoundsException(
                            sprintf(
                                '"%s" key does not exist in current configuration.',
                                $name
                            )
                        );
                    }

                    return $default;
                }
            }
        }

        return $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function has($name)
    {
        try {
            $this->get($name, null, true);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function load(ConfigurationLoaderInterface $configurationLoader)
    {
        $this->setData($configurationLoader->getData());

        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
