<?php

namespace AppBundle\Controller;

use AppBundle\Forms\TicketSubmission;
use AppBundle\Forms\OfferSubmission;
use AppBundle\Forms\Types\TicketSubmissionType;
use AppBundle\Forms\Types\OfferSubmissionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Tiquette\Domain\Ticket;

class SalesController extends Controller
{
    public function submitTicketAction(Request $request): Response
    {
        $ticketSubmission = new TicketSubmission();

        $ticketSubmissionForm = $this->createForm(TicketSubmissionType::class, $ticketSubmission);

        if ($request->isMethod('POST')) {
            $ticketSubmissionForm->handleRequest($request);
            if ($ticketSubmissionForm->isSubmitted() && $ticketSubmissionForm->isValid()) {

                $ticket = $this->get('ticket_factory')->fromTicketSubmission($ticketSubmission);
                $this->get('repositories.ticket')->save($ticket);

                return $this->redirectToRoute('ticket_submission_successful');
            }
        }

        return $this->render('@App/Sales/submit_ticket.html.twig', ['ticketSubmissionForm' => $ticketSubmissionForm->createView()]);
    }

    public function ticketSubmissionSuccessfulAction(Request $request): Response
    {
        return $this->render('@App/Sales/ticket_submission_successful.html.twig');
    }

    public function listBuyableTicketsAction(Request $request): Response
    {
        $tickets = $this->get('repositories.ticket')->findBuyableTickets();

        return $this->render('@App/Sales/list_buyable_tickets.html.twig', ['tickets' => $tickets]);
    }

    public function submitOfferAction(Request $request): Response
    {
        $ticketEventName = $request->attributes->get('eventName');
        $offerSubmission = new OfferSubmission();

        $offerSubmissionForm = $this->createForm(OfferSubmissionType::class, $offerSubmission);

        if ($request->isMethod('POST')) {
            $offerSubmissionForm->handleRequest($request);
            if ($offerSubmissionForm->isSubmitted() && $offerSubmissionForm->isValid()) {

                $ticket = $this->get('ticket_factory')->fromTicketSubmission($offerSubmission);
                $this->get('repositories.ticket')->save($ticket);

                return $this->redirectToRoute('ticket_submission_successful');
            }
        }

        $ticket = $this->get('repositories.ticket')->findTicketFromName($ticketEventName);

        return $this->render('@App/Sales/submit_offer.html.twig', ['offerSubmissionForm' => $offerSubmissionForm->createView(), 'ticket' => $ticket]);
    }
}
