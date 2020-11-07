<?php

namespace AcmeVet\Scheduling\Infrastructure;

use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentId;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentRepository;
use AcmeVet\Scheduling\Domain\Appointment\Pet;
use Doctrine\DBAL\Connection;

class DbalAppointmentRepository implements AppointmentRepository
{
    private const DATE_FORMAT = "Y-m-d\TH:i";

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAppointmentAtTime(\DateTimeImmutable $appointmentTime): ?Appointment
    {
        $stmt = $this->connection->prepare('
            SELECT * FROM appointments WHERE start_time = :start_time 
        ');

        $stmt->bindValue('start_time', $appointmentTime->format(self::DATE_FORMAT));
        $stmt->execute();

        $result = $stmt->fetchAssociative();

        if (!$result) {
            return null;
        }

        return Appointment::create(
            AppointmentId::fromString($result['id']),
            Pet::create(
                $result['pet_name'],
                $result['owner_name'],
                $result['contact_number']
            ),
            \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $result['start_time']),
            $result['length']
        );
    }

    public function store(Appointment $appointment): void
    {
        $stmt = $this->connection->prepare('
            INSERT INTO appointments (id, start_time, length, pet_name, owner_name, contact_number)
            VALUES (:id, :start_time, :length, :pet_name, :owner_name, :contact_number) 
        ');

        $stmt->bindValue('id', $appointment->getId()->toString());
        $stmt->bindValue('start_time', $appointment->getStartTime()->format(self::DATE_FORMAT));
        $stmt->bindValue('length', $appointment->getLengthInMinutes());
        $stmt->bindValue('pet_name', $appointment->getPet()->getName());
        $stmt->bindValue('owner_name', $appointment->getPet()->getOwnerName());
        $stmt->bindValue('contact_number', $appointment->getPet()->getContactNumber());

        $stmt->execute();
    }

    public function getAll(): array
    {
        $stmt = $this->connection->prepare('
            SELECT * FROM appointments
        ');

        $stmt->execute();

        $result = $stmt->fetchAllAssociative();

        return \array_map(function (array $row) {
            return Appointment::create(
                AppointmentId::fromString($row['id']),
                Pet::create(
                    $row['pet_name'],
                    $row['owner_name'],
                    $row['contact_number']
                ),
                \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $row['start_time']),
                $row['length']
            );
        }, $result);
    }
}
