<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;

use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{


    #[Route('/', name: 'app_home', methods: ["GET"])]
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', compact('pins'));
    }


    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET","POST"})
     * @IsGranted("PIN_CREATE")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('sucess', 'Pin successfully created!');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
