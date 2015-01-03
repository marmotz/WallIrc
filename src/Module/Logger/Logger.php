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

        $this->log(
            sprintf(
                '%s joined the channel',
                isset($data['nickname']) ? $data['nickname'] : $data['from']['nick']
            ),
            $data['channel']
        );
    }

    public function onMessage(Bucket $bucket) {
        $data = $bucket->getData();

        if ($data['isAction']) {
            $format = '* %s %s';
        } else {
            $format = '<%s> %s';
        }

        $this->log(
            sprintf(
                $format,
                $data['from']['nick'],
                $data['message']
            ),
            $data['channel']
        );
    }

    public function onNick(Bucket $bucket) {
        $data = $bucket->getData();

        $this->log(
            sprintf(
                '%s is now known as %s',
                $data['from']['nick'],
                $data['nick']
            )
        );
    }

    public function onNotice(Bucket $bucket) {
        $data = $bucket->getData();

        $this->log(
            sprintf(
                '%s ->%s<- %s',
                $data['from']['nick'],
                $data['to'],
                $data['message']
            ),
            $data['channel']
        );
    }

    public function onOpen(Bucket $bucket) {
        $this->log(
            sprintf(
                'Connection to %s opened.',
                $bucket->getSource()->getConnection()->getStreamName()
            )
        );
    }

    public function onOtherMessage(Bucket $bucket) {
        $this->log($bucket->getData()['line']);
    }

    public function onPart(Bucket $bucket) {
        $data = $bucket->getData();

        $this->log(
            sprintf(
                '%s left the channel',
                $data['from']['nick']
            ),
            $data['channel']
        );
    }

    public function onPrivateMessage(Bucket $bucket) {
        $data = $bucket->getData();

        $this->log(
            sprintf(
                '%s : %s',
                $data['from']['nick'],
                $data['message']
            )
        );
    }

    public function onQuit(Bucket $bucket) {
        $data = $bucket->getData();

        $this->log(
            sprintf(
                '%s quit IRC (%s)',
                $data['from']['nick'],
                $data['message']
            )
        );
    }

    protected function log($txt, $channel = null) {
        printf(
            "[%s] %s%s\n",
            date('H:i:s'),
            $channel === null ? '' : "- $channel - ",
            $txt
        );
    }
}
