<?php

namespace AcmeVet\Scheduling\Domain\Appointment;

use Ramsey\Uuid\Uuid;

class AppointmentId
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        if (false === Uuid::isValid($id)) {
            throw new \DomainException(
                \sprintf("AppointmentId '%s' is not valid", $id)
            );
        }

        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
