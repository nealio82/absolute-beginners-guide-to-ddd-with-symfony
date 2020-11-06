<?php

namespace Tests\AcmeVet\Scheduling\Domain\Appointment;

use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentId;
use AcmeVet\Scheduling\Domain\Appointment\Exception\AppointmentLengthInvalid;
use AcmeVet\Scheduling\Domain\Appointment\Pet;
use PHPUnit\Framework\TestCase;

class AppointmentTest extends TestCase
{
    public function test_an_appointment_can_be_created(): void
    {
        $appointmentId = AppointmentId::generate();
        $pet = Pet::create(
            'Luna',
            'David Smith',
            '07007771234'
        );
        $startTime = new \DateTimeImmutable();
        $appointmentLength = 15;

        $appointment = Appointment::create(
            $appointmentId,
            $pet,
            $startTime,
            $appointmentLength
        );

        static::assertSame($appointmentId, $appointment->getId());
        static::assertSame($pet, $appointment->getPet());
        static::assertSame($startTime, $appointment->getStartTime());
        static::assertSame($appointmentLength, $appointment->getLengthInMinutes());
    }

    public function test_a_double_appointment_can_be_created(): void
    {
        $appointmentLength = 30;

        $appointment = Appointment::create(
            AppointmentId::generate(),
            Pet::create(
                'Luna',
                'David Smith',
                '07007771234'
            ),
            new \DateTimeImmutable(),
            $appointmentLength
        );

        static::assertSame($appointmentLength, $appointment->getLengthInMinutes());
    }

    public function test_an_appointment_cannot_be_longer_than_a_double_slot(): void
    {
        $this->expectException(AppointmentLengthInvalid::class);

        Appointment::create(
            AppointmentId::generate(),
            Pet::create(
                'Luna',
                'David Smith',
                '07007771234'
            ),
            new \DateTimeImmutable(),
            31
        );
    }

    public function test_an_appointment_must_be_longer_than_zero_minutes_long(): void
    {
        $this->expectException(AppointmentLengthInvalid::class);

        Appointment::create(
            AppointmentId::generate(),
            Pet::create(
                'Luna',
                'David Smith',
                '07007771234'
            ),
            new \DateTimeImmutable(),
            0
        );
    }

    public function test_an_appointment_must_be_a_multiple_of_fifteen_minutes(): void
    {
        $this->expectException(AppointmentLengthInvalid::class);

        Appointment::create(
            AppointmentId::generate(),
            Pet::create(
                'Luna',
                'David Smith',
                '07007771234'
            ),
            new \DateTimeImmutable(),
            20
        );
    }
}
