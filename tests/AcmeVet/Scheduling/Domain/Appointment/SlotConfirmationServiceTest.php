<?php

namespace Tests\AcmeVet\Scheduling\Domain\Appointment;

use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentId;
use AcmeVet\Scheduling\Domain\Appointment\Exception\CouldNotConfirmSlotException;
use AcmeVet\Scheduling\Domain\Appointment\Pet;
use AcmeVet\Scheduling\Domain\Appointment\SlotConfirmationService;
use PHPUnit\Framework\TestCase;
use Tests\AcmeVet\Scheduling\Doubles\InMemoryAppointmentRepository;

class SlotConfirmationServiceTest extends TestCase
{
    public function test_a_slot_can_be_confirmed(): void
    {
        $repository = new InMemoryAppointmentRepository();
        $service = new SlotConfirmationService($repository);

        $dateTime = new \DateTimeImmutable();

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime,
            15
        );

        $service->confirmSlot($appointment);

        static::assertSame($appointment, $repository->getAppointmentAtTime($dateTime));
    }

    public function test_an_appointment_cannot_be_made_in_the_past(): void
    {
        $repository = new InMemoryAppointmentRepository();
        $service = new SlotConfirmationService($repository);

        $dateTime = new \DateTimeImmutable();

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime->modify('-1 minute'),
            15
        );

        static::expectException(CouldNotConfirmSlotException::class);
        static::expectExceptionMessage('The slot must not be in the past');
        $service->confirmSlot($appointment);
    }

    public function test_an_exception_is_thrown_if_the_slot_is_full(): void
    {
        $repository = new InMemoryAppointmentRepository();
        $service = new SlotConfirmationService($repository);

        $dateTime = new \DateTimeImmutable();

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime,
            15
        );

        $appointment2 = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime,
            15
        );

        $service->confirmSlot($appointment);

        static::expectException(CouldNotConfirmSlotException::class);
        static::expectExceptionMessage('The slot is already taken');
        $service->confirmSlot($appointment2);
    }

    public function test_a_single_appointment_cannot_overlap_an_existing_double_appointment(): void
    {
        $repository = new InMemoryAppointmentRepository();
        $service = new SlotConfirmationService($repository);

        $dateTime = new \DateTimeImmutable();

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime,
            30
        );

        $appointment2 = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime->modify('+15 minutes'),
            15
        );

        $service->confirmSlot($appointment);

        static::expectException(CouldNotConfirmSlotException::class);
        static::expectExceptionMessage('The slot overlaps with a double-length booking');
        $service->confirmSlot($appointment2);
    }

    public function test_a_double_appointment_cannot_overlap_an_existing_appointment(): void
    {
        $repository = new InMemoryAppointmentRepository();
        $service = new SlotConfirmationService($repository);

        $dateTime = new \DateTimeImmutable();

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime->modify('+15 minutes'),
            15
        );

        $appointment2 = Appointment::create(
            AppointmentId::generate(),
            Pet::create('Luna', 'David Smith', '07500777123'),
            $dateTime,
            30
        );

        $service->confirmSlot($appointment);

        static::expectException(CouldNotConfirmSlotException::class);
        static::expectExceptionMessage('The slot spans another booking');
        $service->confirmSlot($appointment2);
    }
}
