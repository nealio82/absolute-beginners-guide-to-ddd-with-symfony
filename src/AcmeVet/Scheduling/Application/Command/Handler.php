<?php declare(strict_types=1);

namespace AcmeVet\Scheduling\Application\Command;

interface Handler
{
    public function __invoke(Command $command): void;
}
