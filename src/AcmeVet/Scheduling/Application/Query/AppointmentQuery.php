<?php

namespace AcmeVet\Scheduling\Application\Query;

use AcmeVet\Scheduling\Domain\Appointment\Appointment;
use AcmeVet\Scheduling\Domain\Appointment\AppointmentRepository;

class AppointmentQuery
{
    private AppointmentRepository $repository;

    public function __construct(AppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function fetchAll(): array
    {
        $results = $this->repository->getAll();

        return \array_map(function (Appointment $item) {
            return new AppointmentDTO(
                $item->getStartTime(),
                15 === $item->getLengthInMinutes() ? false : true,
                $item->getPet()->getName(),
                $item->getPet()->getOwnerName(),
                $item->getPet()->getContactNumber()
            );
        }, $results);
    }
}
