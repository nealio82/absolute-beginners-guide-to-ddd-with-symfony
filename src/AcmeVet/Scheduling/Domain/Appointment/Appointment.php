<?php

namespace AcmeVet\Scheduling\Domain\Appointment;

use AcmeVet\Scheduling\Domain\Appointment\Exception\AppointmentLengthInvalid;

/**
 * This is the 'Aggregate Root' of the Appointment Bounded Context.
 *
 * Each Bounded Context will have one Entity class which naturally
 * describes what the bounded context is trying to achieve. In this
 * case the Bounded Context is focused on booking appointments, so
 * the Appointment class is the natural Aggregate Root.
 *
 * The Aggregate Root is the entry-point for all functionality in the
 * Domain model. It can also be comprised in-part of other
 * Entity classes (eg here we have Pet as a property of Appointment).
 *
 * The Aggregate Root is the only class in the Bounded Context which is
 * allowed to create other Entity objects or to call methods on those entities.
 *
 * All services in the Bounded Context call methods directly on the
 * Aggregate Root; they _do not_ call any methods on the other Entity classes,
 * nor do they instantiate any other class than the Aggregate Root.
 *
 * If you cannot think of an appropriate Aggregate Root in your own context,
 * you might need to either consider splitting your model into more
 * Bounded Contexts, or go back to the 'Domain Expert' and gain clarification.
 */
class Appointment
{
    private const STANDARD_APPOINTMENT_LENGTH_IN_MINUTES = 15;

    private AppointmentId $appointmentId;
    private Pet $pet;
    private \DateTimeImmutable $appointmentTime;
    private int $appointmentLengthInMinutes;

    private function __construct(
        AppointmentId $appointmentId,
        Pet $pet,
        \DateTimeImmutable $appointmentTime,
        int $appointmentLengthInMinutes
    )
    {
        $this->appointmentId = $appointmentId;
        $this->pet = $pet;
        $this->appointmentTime = $appointmentTime;
        $this->appointmentLengthInMinutes = $appointmentLengthInMinutes;
    }

    public static function create(
        AppointmentId $appointmentId,
        Pet $pet,
        \DateTimeImmutable $appointmentTime,
        int $appointmentLengthInMinutes
    ): self
    {
        if (self::STANDARD_APPOINTMENT_LENGTH_IN_MINUTES > $appointmentLengthInMinutes) {
            throw new AppointmentLengthInvalid(
                sprintf("The minumum appointment length is %s minutes", self::STANDARD_APPOINTMENT_LENGTH_IN_MINUTES)
            );
        }

        $maxAppointmentLength = self::STANDARD_APPOINTMENT_LENGTH_IN_MINUTES * 2;

        if ($maxAppointmentLength < $appointmentLengthInMinutes) {
            throw new AppointmentLengthInvalid(
                sprintf("The maximum appointment length is %s minutes", $maxAppointmentLength)
            );
        }

        if (0 !== $appointmentLengthInMinutes % self::STANDARD_APPOINTMENT_LENGTH_IN_MINUTES) {
            throw new AppointmentLengthInvalid(
                sprintf("The appointment length must be a multiple of %s minutes", self::STANDARD_APPOINTMENT_LENGTH_IN_MINUTES)
            );
        }

        return new self($appointmentId, $pet, $appointmentTime, $appointmentLengthInMinutes);
    }

    public function getId(): AppointmentId
    {
        return $this->appointmentId;
    }

    public function getPet(): Pet
    {
        return $this->pet;
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->appointmentTime;
    }

    public function getLengthInMinutes(): int
    {
        return $this->appointmentLengthInMinutes;
    }
}
