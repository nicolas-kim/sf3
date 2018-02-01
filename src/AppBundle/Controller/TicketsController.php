<?php

namespace AppBundle\Controller;

use AppBundle\Forms\TicketSubmission;
use AppBundle\Forms\Types\TicketSubmissionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tiquette\Domain\TicketId;
use AppBundle\Forms\Offer as OfferDTO;
use AppBundle\Forms\Types\OfferType;

class TicketsController extends Controller
{
    public function latestSubmittedTicketsAction(Request $request): Response
    {
        $ticketSummaries = $this->get('repositories.ticket')->findLatestSubmittedTickets();
        
        return $this->render('@App/Tickets/latest_submitted_tickets.html.twig', ['ticketSummaries' => $ticketSummaries]);
    }

    public function hotTicketsAction(Request $request): Response
    {
        $ticketSummaries = $this->get('repositories.ticket')->findHotTickets();

        return $this->render('@App/Tickets/hot_tickets.html.twig', ['ticketSummaries' => $ticketSummaries]);
    }

    public function viewTicketDetailsAction(Request $request): Response
    {
        $ticketId = TicketId::fromString($request->get('ticketId'));

        $ticketDetails = $this->get('repositories.ticket')->getTicketDetails($ticketId);

        $offerDto = new OfferDTO();
        $offerDto->ticketId = $ticketId;
        $offerForm = $this->createForm(OfferType::class, $offerDto, [
            'action' => $this->generateUrl('make_an_offer', ['ticketId' => $ticketId])
        ]);

        return $this->render('@App/Tickets/ticket_details.html.twig', [
            'ticketDetails' => $ticketDetails,
            'offerForm' => $offerForm->createView()
        ]);
    }

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

        return $this->render('@App/Tickets/submit_ticket.html.twig', ['ticketSubmissionForm' => $ticketSubmissionForm->createView()]);
    }

    public function ticketSubmissionSuccessfulAction(Request $request): Response
    {
        return $this->render('@App/Tickets/ticket_submission_successful.html.twig');
    }
}
