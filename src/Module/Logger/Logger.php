<?php

namespace Marmotz\WallIrc\Module\Logger;

use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\Module\Module;

class Logger extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'open'            => 'onOpen',
            'private-message' => 'onPrivateMessage',
            'message'         => 'onMessage',
            'other-message'   => 'onOtherMessage',
            'error'           => 'onError',
        );
    }

    protected function writeln($txt) {
        echo "$txt\n";
    }

    public function onOpen(Bucket $bucket) {
        $this->writeln('Connection to ' . $bucket->getSource()->getConnection()->getStreamName() . ' opened.');
    }

    public function onPrivateMessage(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '>> %s > %s',
                $data['from']['nick'],
                $data['message']
            )
        );
    }

    public function onMessage(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '<%s> %s',
                $data['from']['nick'],
                $data['message']
            )
        );
    }

    public function onOtherMessage(Bucket $bucket) {
        $this->writeln($bucket->getData()['line']);
    }

    public function onError(Bucket $bucket) {
        $data = $bucket->getData();
        dumpd($data);
    }
}
