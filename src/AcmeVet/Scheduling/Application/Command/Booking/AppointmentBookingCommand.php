<?php declare(strict_types=1);

namespace AcmeVet\Scheduling\Application\Command\Booking;

use AcmeVet\Scheduling\Application\Command\Command;

/**
 * This is a command forming part of the CQRS
 * (Command / Query Responsibility Segregation) pattern, where
 * we send information to the Domain layer.
 *
 * The UI layer is allowed to know about the command.
 *
 * It is a POPO (Plain Old PHP Object), which means it doesn't
 * contain any logic and doesn't depend on anything from any other layers.
 *
 * Its job is to carry data to a command handler.
 *
 * We do not return anything to the UI layer in a command; that
 * is the responsibility of the Query classes.
 *
 * There is a matching handler for this command.
 */
class AppointmentBookingCommand implements Command
{
    private \DateTimeImmutable $appointmentTime;
    private string $petName;
    private string $ownerName;
    private string $contactNumber;
    private int $appointmentLengthInMinutes;

    public function __construct(
        \DateTimeImmutable $appointmentTime,
        string $petName,
        string $ownerName,
        string $contactNumber,
        int $appointmentLengthInMinutes
    )
    {
        $this->appointmentTime = $appointmentTime;
        $this->petName = $petName;
        $this->ownerName = $ownerName;
        $this->contactNumber = $contactNumber;
        $this->appointmentLengthInMinutes = $appointmentLengthInMinutes;
    }

    public function getAppointmentTime(): \DateTimeImmutable
    {
        return $this->appointmentTime;
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

    public function getAppointmentLengthInMinutes(): int
    {
        return $this->appointmentLengthInMinutes;
    }
}
