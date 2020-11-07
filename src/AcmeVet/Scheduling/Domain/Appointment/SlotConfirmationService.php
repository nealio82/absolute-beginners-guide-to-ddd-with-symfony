<?php

namespace AcmeVet\Scheduling\Domain\Appointment;

use AcmeVet\Scheduling\Domain\Appointment\Exception\CouldNotConfirmSlotException;

/**
 * This is a Domain service. It contains some business logic,
 * and is used to perform some sort of action that should not
 * be leaked to the outside world. The UI layer is not allowed
 * to call Domain services directly (but Domain services _can_
 * be called from the Application layer)
 *
 * Domain services are nice because you can send / receive
 * complex domain types (such as Appointment here), as opposed
 * to Application services which need need to work with
 * primitive types or DTOs (Data Transfer Objects)
 */
class SlotConfirmationService
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function confirmSlot(Appointment $appointment): void
    {
        $this->checkStartTimeIsNotInThePast($appointment);
        $this->checkSlotStartTimeIsNotTaken($appointment);
        $this->checkSlotIsNotTakenByADoubleLengthBooking($appointment);
        $this->checkSlotDoesNotSpanAnotherBooking($appointment);

        $this->appointmentRepository->store($appointment);
    }

    private function checkStartTimeIsNotInThePast(Appointment $appointment): void
    {
        $dateDiff = $appointment->getStartTime()->diff(new \DateTimeImmutable());

        if (
            $appointment->getStartTime() < new \DateTimeImmutable()
            && 0 !== (int) $dateDiff->format('%y%m%d%h%i')
        ) {
            throw new CouldNotConfirmSlotException("The slot must not be in the past");
        }
    }

    private function checkSlotStartTimeIsNotTaken(Appointment $appointment): void
    {
        $existingAppointment = $this->appointmentRepository->getAppointmentAtTime(
            $appointment->getStartTime()
        );

        if (null !== $existingAppointment) {
            throw new CouldNotConfirmSlotException("The slot is already taken");
        }
    }

    private function checkSlotIsNotTakenByADoubleLengthBooking(Appointment $appointment): void
    {
        $previousAppointment = $this->appointmentRepository->getAppointmentAtTime(
            $appointment->getStartTime()->modify('-15 minutes')
        );

        if ($previousAppointment && 30 === $previousAppointment->getLengthInMinutes()) {
            throw new CouldNotConfirmSlotException("The slot overlaps with a double-length booking");
        }
    }

    private function checkSlotDoesNotSpanAnotherBooking(Appointment $appointment): void
    {
        $nextAppointment = $this->appointmentRepository->getAppointmentAtTime(
            $appointment->getStartTime()->modify('+15 minutes')
        );

        if ($nextAppointment && 30 === $appointment->getLengthInMinutes()) {
            throw new CouldNotConfirmSlotException("The slot spans another booking");
        }
    }
}
