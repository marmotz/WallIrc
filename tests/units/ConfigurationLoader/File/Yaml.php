<?php

namespace Marmotz\WallIrc\tests\units\ConfigurationLoader\File;

use Marmotz\WallIrc\tests\units\BaseTest;

class Yaml extends BaseTest {
    public function testLoadFrom()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(
                    function() use (&$path) {
                        $this->testedInstance->loadFrom($path = uniqid());
                    }
                )
                    ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage("$path does not exist.")

                ->exception(
                    function() use (&$path) {
                        $this->testedInstance->loadFrom($path = __DIR__);
                    }
                )
                    ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage("$path is not a valid file.")

            ->given(
                $path = $this->getResourcePath('config/test1.yml'),
                $filePermissions = fileperms($path),
                chmod($path, 0200)
            )
                ->exception(
                    function() use ($path) {
                        $this->testedInstance->loadFrom($path);
                    }
                )
                    ->isInstanceOf('InvalidArgumentException')
                    ->hasMessage("$path is not readable.")
            ->and(
                chmod($path, $filePermissions)
            )

                ->object($this->testedInstance->loadFrom($this->getResourcePath('config/test1.yml')))
                    ->isIdenticalTo($this->testedInstance)
                ->array($this->testedInstance->getData())
                    ->isIdenticalTo(
                        $data = array(
                            'connection' => array(
                                'server'   => 'server.host',
                                'port'     => 6667,
                                'channels' => array('#channel'),
                                'nick'     => 'nick',
                            )
                        )
                    )
        ;
    }

    public function testSetGetData()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->setData($data = $this->faker->randomElements()))
                    ->isIdenticalTo($this->testedInstance)
                ->array($this->testedInstance->getData())
                    ->isIdenticalTo($data)
        ;
    }
}
