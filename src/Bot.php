<?php

namespace Marmotz\WallIrc;

use Hoa\Irc\Client as IrcClient;
use Hoa\Socket\Client as SocketClient;
use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\ConfigurationLoader\File as ConfigurationFile;

class Bot
{
    protected $ircClient;
    protected $configuration;

    public function loadConfiguration($configFile)
    {
        $this->setConfiguration(
            (new Configuration)->load(
                ConfigurationFile::load(
                    $configFile
                )
            )
        );

        return $this;
    }

    public function start()
    {
        $this->getIrcClient()->run();
    }

    protected function getConfiguration()
    {
        return $this->configuration;
    }

    protected function getIrcClient()
    {
        return $this->ircClient;
    }

    protected function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        $this->loadIrcClient();
        $this->loadModules();

        return $this;
    }

    protected function loadModules()
    {
        $modules = array_merge(
            array(
                'Marmotz\WallIrc\Module\Logger\Logger',
                'Marmotz\WallIrc\Module\AutoJoin\AutoJoin',
            ),
            $this->getConfiguration()->get('modules', array())
        );

        foreach ($modules as $module) {
            $class = '\\' . ltrim($module, '\\');

            (new $class)
                ->setConfiguration($this->getConfiguration())
                ->loadEvents($this->getIrcClient())
            ;
        }
    }

    protected function loadIrcClient()
    {
        $this->setIrcClient(
            new IrcClient(
                new SocketClient(
                    sprintf(
                        'tcp://%s:%d',
                        $this->getConfiguration()->get('connection.server'),
                        $this->getConfiguration()->get('connection.port')
                    )
                )
            )
        );
    }

    protected function setIrcClient(IrcClient $ircClient)
    {
        $this->ircClient = $ircClient;

        return $this;
    }
}
