<?php

namespace Marmotz\WallIrc\Module\AutoJoin;

use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\Module\Module;

class AutoJoin extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'open' => 'onOpen',
        );
    }

    public function onOpen(Bucket $bucket)
    {
        $nick = $this->getConfiguration()->get('connection.nick');

        foreach ($this->getConfiguration()->get('connection.channels') as $channel) {
            $bucket->getSource()->join($nick, $channel);
        }
    }
}
