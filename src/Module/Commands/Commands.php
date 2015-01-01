<?php

namespace Marmotz\WallIrc\Module\Commands;

use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\Module\Module;

class Commands extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'private-message' => 'onPrivateMessage',
        );
    }

    public function onPrivateMessage(Bucket $bucket)
    {
        $data = $bucket->getData();

        $owner = $this->getConfiguration()->get('configuration.commands.owner');

        if ($owner && $data['from']['nick'] !== $owner) {
            return;
        }

        switch (trim($data['message'])) {
            case 'quit':
                die;
            break;
        }
    }
}
