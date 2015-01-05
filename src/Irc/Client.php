<?php

namespace Marmotz\WallIrc\Irc;

use Hoa\Core;
use Hoa\Irc\Client as BaseClient;
use Hoa\Socket;

class Client extends BaseClient
{
    /**
     * Constructor.
     *
     * @access  public
     * @param   \Hoa\Socket\Client  $client    Client.
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function __construct (Socket\Client $client)
    {
        parent::__construct($client);

        $this->_on->addIds(
            [
                'mode',
                'nick',
                'notice',
                'part',
                'quit',
            ]
        );

        return;
    }

    /**
     * Run a node.
     *
     * @access  protected
     * @param   \Hoa\Socket\Node  $node    Node.
     * @return  void
     */
    protected function _run (Socket\Node $node)
    {
        if(false === $node->hasJoined()) {
            $node->setJoined(true);
            $this->_on->fire('open', new Core\Event\Bucket());

            return;
        }

        try {
            $line = trim($node->getConnection()->readLine());

            preg_match(
                '#^(?::(?<prefix>[^\s]+)\s+)?(?<command>[^\s]+)\s+(?<middle>[^:]+)?(:\s*(?<trailing>.+))?$#',
                $line,
                $matches
            );

            if(!isset($matches['command'])) {
                $matches['command'] = null;
            }

            if (isset($matches['middle'])) {
                $matches['middle'] = trim($matches['middle']);
            }

            if (isset($matches['trailing'])) {
                $matches['trailing'] = trim($matches['trailing']);
            }

            $listener = null;

            switch($matches['command']) {
                case 366: // RPL_ENDOFNAMES
                    list($nickname, $channel) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'join';
                    $bucket   = [
                        'nickname' => $nickname,
                        'channel'  => $channel,
                    ];
                break;

                case 'INVITE':
                    list($channel, ) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'invite';
                    $bucket   = [
                        'from'               => $this->parseNick($matches['prefix']),
                        'channel'            => $channel,
                        'invitation_channel' => $matches['trailing'],
                    ];
                break;

                case 'JOIN':
                    $listener = 'join';
                    $bucket = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'channel' => isset($matches['trailing']) ? $matches['trailing'] : $matches['middle'],
                    ];
                break;

                case 'KICK':
                    list($channel, ) = explode(' ', $matches['middle'], 2);
                    $node->setChannel($channel);

                    $listener = 'kick';
                    $bucket   = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'channel' => $channel,
                    ];
                break;

                case 'MODE':
                    $modeParts = explode(' ', $matches['middle']);

                    if (count($modeParts) === 3) {
                        $listener = 'mode';

                        list($channel, $mode, $nick) = explode(' ', $matches['middle']);

                        $bucket = [
                            'from'    => $this->parseNick($matches['prefix']),
                            'channel' => $channel,
                            'mode'    => $mode,
                            'nick'    => $nick,
                        ];
                    }
                break;

                case 'NICK':
                    $listener = 'nick';
                    $bucket = [
                        'from' => $this->parseNick($matches['prefix']),
                        'nick' => $matches['trailing'],
                    ];
                break;

                case 'NOTICE':
                    $listener = 'notice';
                    $bucket = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'to'      => $matches['middle'],
                        'nick'    => substr($matches['middle'], 0, 1) !== '#' ? $matches['middle'] : null,
                        'channel' => substr($matches['middle'], 0, 1) === '#' ? $matches['middle'] : null,
                        'message' => $matches['trailing'],
                    ];
                break;

                case 'PART':
                    $listener = 'part';
                    $bucket = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'channel' => $matches['middle'],
                    ];
                break;

                case 'PING':
                    $daemons  = explode(' ', $matches['trailing']);
                    $listener = 'ping';
                    $bucket   = [
                        'daemons' => $daemons,
                    ];

                    if(isset($daemons[1])) {
                        $this->pong($daemons[0], $daemons[1]);
                    } else {
                        $this->pong($daemons[0]);
                    }
                break;

                case 'PRIVMSG':
                    $middle   = $matches['middle'];
                    $message  = $matches['trailing'];
                    $username = $node->getUsername();

                    if (preg_match('/^\x01ACTION (?<message>.*)\x01$/', $message, $match)) {
                        $message = $match['message'];
                        $isAction  = true;
                    } else {
                        $isAction  = false;
                    }

                    $isMention = strpos($message, $username) !== false;

                    $bucket = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'message' => $message,
                        'isAction'  => $isAction,
                        'isMention' => $isMention,
                    ];

                    if($username === $middle) {
                        $listener = 'private-message';
                    } else {
                        $node->setChannel($middle);
                        $listener = 'message';
                        $bucket['channel'] = $middle;
                    }

                    if ($isMention) {
                        $this->_on->fire('mention', new Core\Event\Bucket($bucket));
                    }
                break;

                case 'QUIT':
                    $listener = 'quit';
                    $bucket = [
                        'from'    => $this->parseNick($matches['prefix']),
                        'message' => $matches['trailing'],
                    ];
                break;
            }

            if ($listener === null) {
                $listener = 'other-message';
                $bucket   = [
                    'line'        => $line,
                    'parsed_line' => $matches,
                ];
            }

            $this->_on->fire($listener, new Core\Event\Bucket($bucket));
        } catch (Core\Exception\Idle $e) {
            $this->_on->fire(
                'error',
                new Core\Event\Bucket(
                    [ 'exception' => $e ]
                )
            );
        }

        return;
    }
}
