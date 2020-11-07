<?php

namespace AcmeVet\Scheduling\Domain\Appointment;

interface AppointmentRepository
{
    public function getAppointmentAtTime(\DateTimeImmutable $appointmentTime): ?Appointment;

    public function store(Appointment $appointment): void;

    /**
     * @return Appointment[]
     */
    public function getAll(): array;
}
