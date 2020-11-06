<?php

namespace AcmeVet\Scheduling\Domain\Appointment;

/**
 * This class is a 'Domain Entity'. It is a representation of
 * some sort of object in the 'Domain Model', but it is secondary
 * to the Aggregate Root (the Appointment class in this particular
 * Bounded Context).
 *
 * It _can_ have methods and functionality on it, but these methods
 * can only be called by the Aggregate Root (the Appointment class),
 * and can never be called directly by any Domain or Application services.
 */
class Pet
{
    private string $name;
    private string $ownerName;
    private string $contactNumber;

    private function __construct(string $name, string $ownerName, string $contactNumber)
    {
        $this->name = $name;
        $this->ownerName = $ownerName;
        $this->contactNumber = $contactNumber;
    }

    public static function create(string $name, string $ownerName, string $contactNumber)
    {
        return new self($name, $ownerName, $contactNumber);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }
}
