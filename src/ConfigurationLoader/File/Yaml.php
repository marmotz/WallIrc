<?php

namespace Marmotz\WallIrc\ConfigurationLoader\File;

use Marmotz\WallIrc\ConfigurationLoader\ConfigurationLoaderInterface;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml implements ConfigurationLoaderInterface {
    protected $data;

    public function loadFrom($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("$filepath does not exist.");
        }

        if (!is_file($filepath)) {
            throw new \InvalidArgumentException("$filepath is not a valid file.");
        }

        if (!is_readable($filepath)) {
            throw new \InvalidArgumentException("$filepath is not readable.");
        }

        $this->setData(YamlParser::parse(file_get_contents($filepath)));

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
}
