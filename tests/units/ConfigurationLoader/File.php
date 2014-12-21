<?php

namespace Marmotz\WallIrc\tests\units\ConfigurationLoader;

use Marmotz\WallIrc\ConfigurationLoader\File as ConfigurationFile;
use Marmotz\WallIrc\tests\units\BaseTest;

class File extends BaseTest {
    public function testLoad()
    {
        $this
            ->object(ConfigurationFile::load($this->getResourcePath('config/test1.yml')))
                ->isInstanceOf('Marmotz\WallIrc\ConfigurationLoader\File\Yaml')
        ;
    }
}
