<?php

namespace AppBundle\Controller;

use AppBundle\Forms\Types\OfferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Forms\Offer as OfferDTO;
use Tiquette\Domain\Offer;
use Tiquette\Domain\Price;
use Tiquette\Domain\TicketId;

class OffersController extends Controller
{
    public function viewAllMemberOffersAction(Request $request): Response
    {
        // todo
    }

    public function makeAnOfferAction(Request $request): Response
    {
        $offerDto = new OfferDTO();
        $offerDto->ticketId = $request->get('ticketId');
        $offerForm = $this->createForm(OfferType::class, $offerDto);

        if ($request->isMethod('POST')) {
            $offerForm->handleRequest($request);
            if ($offerForm->isSubmitted() && $offerForm->isValid()) {

                $offer = Offer::for(
                    TicketId::fromString($offerDto->ticketId),
                    $this->getUser()->getId(),
                    Price::inLowestSubunit($offerDto->proposedPrice, 'EUR'),
                    $offerDto->buyerMessage
                );

                $this->get('repositories.offer')->save($offer);

                return $this->redirectToRoute('offer_successfully_made');
            }
        }

        return $this->render('@App/Offers/make_an_offer.html.twig', ['offerForm' => $offerForm->createView()]);
    }

    public function offerSuccessfullyMadeAction(Request $request): Response
    {
        return $this->render('@App/Offers/offer_successfully_made.html.twig');
    }
}
