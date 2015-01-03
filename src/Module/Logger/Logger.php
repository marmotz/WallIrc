<?php

namespace Marmotz\WallIrc\Module\Logger;

use Hoa\Core\Event\Bucket;
use Marmotz\WallIrc\Module\Module;

class Logger extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'error'           => 'onError',
            'join'            => 'onJoin',
            'message'         => 'onMessage',
            'nick'            => 'onNick',
            'notice'          => 'onNotice',
            'open'            => 'onOpen',
            'other-message'   => 'onOtherMessage',
            'part'            => 'onPart',
            'private-message' => 'onPrivateMessage',
            'quit'            => 'onQuit',
        );
    }

    public function onError(Bucket $bucket) {
        $data = $bucket->getData();
        dumpd($data);
    }

    public function onJoin(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '%s joined %s',
                isset($data['nickname']) ? $data['nickname'] : $data['from']['nick'],
                $data['channel']
            )
        );
    }

    public function onMessage(Bucket $bucket) {
        $data = $bucket->getData();

        if ($data['isAction']) {
            $this->writeln(
                sprintf(
                    '* %s %s',
                    $data['from']['nick'],
                    $data['message']
                )
            );
        } else {
            $this->writeln(
                sprintf(
                    '<%s> %s',
                    $data['from']['nick'],
                    $data['message']
                )
            );
        }
    }

    public function onNick(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '%s is now known as %s',
                $data['from']['nick'],
                $data['nick']
            )
        );
    }

    public function onNotice(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '%s ->%s<- %s',
                $data['from']['nick'],
                $data['to'],
                $data['message']
            )
        );
    }

    public function onOpen(Bucket $bucket) {
        $this->writeln('Connection to ' . $bucket->getSource()->getConnection()->getStreamName() . ' opened.');
    }

    public function onOtherMessage(Bucket $bucket) {
        $this->writeln($bucket->getData()['line']);
    }

    public function onPart(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '%s left %s',
                $data['from']['nick'],
                $data['channel']
            )
        );
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

    public function onQuit(Bucket $bucket) {
        $data = $bucket->getData();

        $this->writeln(
            sprintf(
                '%s quit IRC (%s)',
                $data['from']['nick'],
                $data['message']
            )
        );
    }

    protected function writeln($txt) {
        printf(
            "[%s] %s\n",
            date('H:i:s'),
            $txt
        );
    }
}
