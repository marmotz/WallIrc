<?php

namespace Marmotz\WallIrc\Module;

use Hoa\Core\Event\Bucket;
use Hoa\Irc\Client as IrcClient;
use Marmotz\WallIrc\Configuration;

abstract class Module
{
    protected $configuration;

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getSubscribedEvents()
    {
        return array();
    }

    public function loadEvents(IrcClient $ircClient)
    {
        foreach ($this->getSubscribedEvents() as $name => $method) {
            $ircClient->on(
                $name,
                array($this, $method)
            );
        }
    }

    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
}
