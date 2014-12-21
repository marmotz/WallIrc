<?php

namespace Marmotz\WallIrc\ConfigurationLoader;

use Symfony\Component\Yaml\Yaml;

class File {
    static public function load($filepath)
    {
        switch (strtolower(substr($filepath, strrpos($filepath, '.') + 1))) {
            case 'yml':
            case 'yaml':
                return (new File\Yaml)->loadFrom($filepath);
            break;
        }
    }
}
