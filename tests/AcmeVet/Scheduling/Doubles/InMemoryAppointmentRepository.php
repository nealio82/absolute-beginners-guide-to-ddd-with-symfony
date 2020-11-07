<?php

namespace Tests\AcmeVet\Scheduling\Doubles;

use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentRepository;

class InMemoryAppointmentRepository implements AppointmentRepository
{
    private array $appointments = [];

    public function getAppointmentAtTime(\DateTimeImmutable $appointmentTime): ?Appointment
    {
        $results = array_filter($this->appointments, function (Appointment $item) use ($appointmentTime) {

            $dateDiff = $appointmentTime->diff($item->getStartTime());

            return (int)$dateDiff->format('%y%m%d%h%i') === 0;
        });

        return $results[0] ?? null;
    }

    public function store(Appointment $appointment): void
    {
        $this->appointments[] = $appointment;
    }

    public function getAll(): array
    {
        return $this->appointments;
    }
}
