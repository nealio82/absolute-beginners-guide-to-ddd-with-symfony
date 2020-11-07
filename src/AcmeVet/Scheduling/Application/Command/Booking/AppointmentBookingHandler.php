<?php declare(strict_types=1);

namespace AcmeVet\Scheduling\Application\Command\Booking;

use AcmeVet\Scheduling\Application\Command\Command;
use AcmeVet\Scheduling\Application\Command\Handler;
use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentId;
use AcmeVet\Scheduling\Domain\Appointment\Exception\CouldNotConfirmSlotException;
use AcmeVet\Scheduling\Domain\Appointment\Pet;
use AcmeVet\Scheduling\Domain\Appointment\SlotConfirmationService;

/**
 * This handler is an application service. It doesn't contain any
 * business logic and its job is to provide an interface
 * to the Domain layer that the UI layer can communicate through.
 *
 * The UI layer calls Application services directly. In the case of
 * commands and handlers, this is wired using the Symfony message bus.
 *
 * The Application layer _is_ allowed to call Domain services directly.
 *
 * The handler _is_ allowed to ask simple questions of the domain
 * (eg: "does this record already exist in the repository?") in order
 * to provide simple validation, but it must not check invariants.
 *
 * It cannot contain Domain logic.
 */
class AppointmentBookingHandler implements Handler
{
    private SlotConfirmationService $slotConfirmationService;

    public function __construct(SlotConfirmationService $slotConfirmationService)
    {
        $this->slotConfirmationService = $slotConfirmationService;
    }

    public function __invoke(Command $command): void
    {
        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create(
                $command->getPetName(),
                $command->getOwnerName(),
                $command->getContactNumber()
            ),
            $command->getAppointmentTime(),
            $command->getAppointmentLengthInMinutes()
        );

        try {
            $this->slotConfirmationService->confirmSlot($appointment);
        } catch (CouldNotConfirmSlotException $couldNotConfirmSlotException) {
            throw new \RuntimeException("The slot could not be booked");
        }
    }
}
