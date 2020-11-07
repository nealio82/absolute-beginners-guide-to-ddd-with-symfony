<?php

namespace App\Controller;

use AcmeVet\Scheduling\Application\Command\Booking\AppointmentBookingCommand;
use App\Form\Type\AppointmentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route as Route;

class AppointmentController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/appointments", name="appointments");
     */
    public function list(Request $request): Response
    {
        $form = $this->createForm(AppointmentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new AppointmentBookingCommand(
                $form->get('appointmentTime')->getData(),
                $form->get('petName')->getData(),
                $form->get('ownerName')->getData(),
                $form->get('contactNumber')->getData(),
                true === $form->get('appointmentLength')->getData() ? 30 : 15
            );
            $this->messageBus->dispatch($command);
        }

        return $this->render('appointment/list.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
