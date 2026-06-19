<?php

namespace App\MessageHandler;

use App\Message\MatchMakerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class MatchMakerMessageHandler
{
    public function __invoke(MatchMakerMessage $message): void
    {
        //echo date('Y-m-d H:i:s') . PHP_EOL;
    }
}
