<?php

namespace Marmotz\WallIrc\tests\units;

use Marmotz\WallIrc\Configuration;
use mock\Hoa\Irc\Client as mockIrcClient;
use mock\Hoa\Socket\Client as mockSocketClient;

class Bot extends BaseTest {
    public function testConstruct()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->variable($this->invoke($this->testedInstance)->getConfiguration())
                    ->isNull()
                ->variable($this->invoke($this->testedInstance)->getIrcClient())
                    ->isNull()
        ;
    }

    public function testLoadConfiguration()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->loadConfiguration($this->getResourcePath('config/test1.yml')))
                    ->isIdenticalTo($this->testedInstance)
                ->object($this->invoke($this->testedInstance)->getIrcClient())
                    ->isInstanceOf('Hoa\Irc\Client')
        ;
    }

    public function testSetGetConfiguration()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($configuration = new Configuration)
            ->then
                ->object($this->invoke($this->testedInstance)->setConfiguration($configuration))
                    ->isIdenticalTo($this->testedInstance)
                ->object($this->invoke($this->testedInstance)->getConfiguration())
                    ->isIdenticalTo($configuration)
        ;
    }

    public function testSetGetIrcClient()
    {
        $this
            ->given($this->newTestedInstance)
            ->and($this->mockGenerator->shuntParentClassCalls())
            ->and($mockSocketClient = new mockSocketClient(uniqid()))
            ->and($mockIrcClient = new mockIrcClient($mockSocketClient))
            ->then
                ->object($this->invoke($this->testedInstance)->setIrcClient($mockIrcClient))
                    ->isIdenticalTo($this->testedInstance)
                ->object($this->invoke($this->testedInstance)->getIrcClient())
                    ->isIdenticalTo($mockIrcClient)
        ;
    }
}
