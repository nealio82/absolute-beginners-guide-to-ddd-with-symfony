<?php

namespace AcmeVet\Scheduling\Application\Query;

/**
 * This is a DTO; a 'Data Transfer Object'. It is a 'value object', which means
 * its only purpose is to hold data so you can send it across layer boundaries.
 *
 * Its role is to provide a simple data structure to the UI layer from the
 * application service. It cannot contain functionality or logic.
 *
 * It provides an agreed interface between the Application layer and the UI layer.
 */
class AppointmentDTO
{
    private \DateTimeImmutable $startTime;
    private bool $isDoubleAppointment;
    private string $petName;
    private string $ownerName;
    private string $contactNumber;

    public function __construct(
        \DateTimeImmutable $startTime,
        bool $isDoubleAppointment,
        string $petName,
        string $ownerName,
        string $contactNumber
    )
    {
        $this->startTime = $startTime;
        $this->isDoubleAppointment = $isDoubleAppointment;
        $this->petName = $petName;
        $this->ownerName = $ownerName;
        $this->contactNumber = $contactNumber;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getPetName(): string
    {
        return $this->petName;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function getContactNumber(): string
    {
        return $this->contactNumber;
    }

    public function isDoubleAppointment(): bool
    {
        return $this->isDoubleAppointment;
    }
}
