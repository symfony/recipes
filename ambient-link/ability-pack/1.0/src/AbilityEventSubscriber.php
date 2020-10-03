<?php

declare(strict_types=1);

namespace AmbientLink\Contrib\Ability;

use AmbientLink\SDK\Ability;
use AmbientLink\SDK\Event\EnterRoomEvent;
use Psr\Log\LoggerInterface;

final class AbilityEventSubscriber implements Ability
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // can be found in AmbientLink\SDK\Event\
            EnterRoomEvent::NAME => 'onEnterRoom',
        ];
    }

    public function onEnterRoom(EnterRoomEvent $event): void
    {
        $roomName = $event->roomName();

        $this->logger->debug(sprintf('Some person enters room %s', $roomName));

        // @todo implement logic here
    }
}
