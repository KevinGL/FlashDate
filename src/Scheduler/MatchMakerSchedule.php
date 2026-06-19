<?php

namespace App\Scheduler;

use App\Message\MatchMakerMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('matchmaker')]
final class MatchMakerSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    public function getSchedule(): Schedule
    {
        $timezone = new \DateTimeZone('Europe/Paris');
    
        return (new Schedule())
            ->add(
                // @TODO - Modify the frequency to suite your needs
                RecurringMessage::cron('0 21 * * *', new MatchMakerMessage(), $timezone)
            )
            ->stateful($this->cache)
        ;
    }
}
