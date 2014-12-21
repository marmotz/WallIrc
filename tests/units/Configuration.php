<?php

namespace Marmotz\WallIrc\tests\units;

use mock\Marmotz\WallIrc\ConfigurationLoader\ConfigurationLoaderInterface as mockConfigurationLoader;
use mock\Marmotz\WallIrc\ConfigurationLoader\File\Yaml as yamlConfigurationLoader;

class Configuration extends BaseTest
{
    public function testConstruct()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->testedInstance->getData())
                    ->isNull()
        ;
    }

    public function testLoad()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($mockLoader = new mockConfigurationLoader)
            ->and($data = $this->faker->randomElements())
            ->and($this->calling($mockLoader)->getData = $data)
            ->then
                ->object($this->testedInstance->load($mockLoader))
                    ->isIdenticalTo($this->testedInstance)
                ->array($this->testedInstance->getData())
                    ->isIdenticalTo($data)
        ;
    }

    public function testSetGetData()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($data = $this->faker->randomElements())
            ->then
                ->object($this->testedInstance->setData($data))
                    ->isIdenticalTo($this->testedInstance)
                ->array($this->testedInstance->getData())
                    ->isIdenticalTo($data)
        ;
    }

    public function testGet()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($loader = (new yamlConfigurationLoader)->loadFrom($this->getResourcePath('config/test1.yml')))
            ->and($this->testedInstance->load($loader))
            ->then
                ->array($this->testedInstance->get())
                    ->isIdenticalTo(
                        array(
                            'connection' => array(
                                'server'   => 'server.host',
                                'port'     => 6667,
                                'channels' => array('#channel'),
                                'nick'     => 'nick',
                            )
                        )
                    )
                ->array($this->testedInstance->get('connection'))
                    ->isIdenticalTo(
                        array(
                            'server'   => 'server.host',
                            'port'     => 6667,
                            'channels' => array('#channel'),
                            'nick'     => 'nick',
                        )
                    )
                ->string($this->testedInstance->get('connection.server'))
                    ->isIdenticalTo('server.host')
                ->integer($this->testedInstance->get('connection.port'))
                    ->isIdenticalTo(6667)
                ->array($this->testedInstance->get('connection.channels'))
                    ->isIdenticalTo(array('#channel'))
                ->string($this->testedInstance->get('connection.channels.0'))
                    ->isIdenticalTo('#channel')
        ;
    }
}
