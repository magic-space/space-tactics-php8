<?php

namespace App\Controller;

use App\Entity\Planet;
use App\Entity\Support;
use App\Entity\User;
use App\Form\SupportType;
use App\Repository\BuildingsQueueRepository;
use App\Repository\PlanetBuildingRepository;
use App\Repository\PlanetRepository;
use App\Repository\SupportRepository;
use App\Service\BuildingCalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainController extends CustomAbstractController
{

    use Traits\MessagesTrait;
    use Traits\PlanetsTrait;

    #[Route('/main/{slug?}', name: 'main')]
    public function index(
        ManagerRegistry            $managerRegistry,
        Security                   $security,
        PlanetRepository           $p,
        BuildingCalculationService $bcs,
        BuildingsQueueRepository   $bqr,
        Request                    $request,
                                   $slug,
        EntityManagerInterface     $em,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $planets = $this->getPlanetsByPlayer($managerRegistry, $this->user_uuid, $slug);

        if($slug === NULL) {
            $slug = $planets[1]->getSlug();
        }

        $res        = $p->findOneBy(['user_uuid' => $this->user_uuid, 'slug' => $slug]);
        $prodActual = $bcs->calculateActualBuildingProduction($res->getMetalBuilding(), $res->getCrystalBuilding(), $res->getDeuteriumBuilding(), $managerRegistry);
        $now        = new \DateTime();
        $nowString  = $now->format('Y-m-d H:i:s');

        //ToDo
        // 1. get all buildings in queue


        return $this->render(
            'main/index.html.twig', [
            'planets'        => $planets[0],
            'selectedPlanet' => $planets[1],
            'planetData'     => $planets[2],
            'user'           => $this->getUser(),
            'messages'       => $this->getMessages($security, $managerRegistry),
            'slug'           => $slug,
            'production'     => $prodActual,
        ],
        );
    }

    #[Route('/app_logout', name: 'app_logout')]
    public function logoutAction(
        AuthorizationCheckerInterface $authorizationChecker,
        SessionInterface              $session,
        EntityManagerInterface        $entityManager,
        RequestStack                  $requestStack,
        Security                      $security,
    ): Response
    {

        if($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Custom logout logic, if needed
            /** @var User $user */
            $user = $this->getUser();
            $user->setLogoutOn(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $requestStack->getSession()->invalidate();

        }
        $response = $security->logout();
        $session->invalidate();

        return $this->render('logout.html.twig');

    }

    #[Route('/statistics/{slug?}', name: 'statistics')]
    public function statistics(
        ManagerRegistry            $managerRegistry,
        Security                   $security,
        PlanetRepository           $p,
        PlanetBuildingRepository   $pbr,
        BuildingCalculationService $bcs,
        BuildingsQueueRepository   $bqr,
        Request                    $request,
                                   $slug,
        EntityManagerInterface     $em,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');


        $planets            = $this->getPlanetsByPlayer($managerRegistry, $this->user_uuid, $slug);
        $slug               = $slug ?? $planets[0]->getSlug();
        $repository         = $managerRegistry->getRepository(Planet::class);
        $planet             = $repository->findOneBy(['user_uuid' => $this->user_uuid, 'slug' => $slug]);
        $metalBuilding      = $planet->getMetalBuilding();
        $crystalBuilding    = $planet->getCrystalBuilding();
        $deuteriumBuilding  = $planet->getDeuteriumBuilding();
        $prodActual         = $bcs->calculateActualBuildingProduction($metalBuilding, $crystalBuilding, $deuteriumBuilding, $managerRegistry);
        $planetForBuildings = $repository->findBy(['user_uuid' => $this->user_uuid]);
        $buildings          = [];

        foreach($planetForBuildings as $pl) {
            $buildings[$pl->getSlug()] = $pbr->getPlanetBuildingsByPlanetId($em, $pl->getId());
        }

        return $this->render(
            'main/statistics.html.twig', [
            'planets'        => $planets[0],
            'selectedPlanet' => $planets[1],
            'planetData'     => $planets[2],
            'user'           => $this->getUser(),
            'messages'       => $this->getMessages($security, $managerRegistry),
            'slug'           => $slug,
            'production'     => $prodActual,
            'buildings'      => $buildings,
        ],
        );
    }

    #[Route('/support/{slug?}', name: 'support')]
    public function support(
        Request                $request,
        ManagerRegistry        $managerRegistry,
        PlanetRepository       $p,
        EntityManagerInterface $em,
        Security               $security,
        Session                $session,
        SupportRepository      $supportRepository,
                               $slug = NULL,
    ): Response
    {
        $user_uuid = $security->getUser()->getUuid();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $planets = $this->getPlanetsByPlayer($managerRegistry, $user_uuid, $slug);

        $tickets = $supportRepository->findBy(['uuid' => $user_uuid, 'closed' => 0]);

        $form = $this->createForm(SupportType::class, new Support());
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $form->getData()->setUuid($security->getUser()->getUuid());
            $form->getData()->setUsername($security->getUser()->getUsername());
            $form->getData()->setDatum(new \DateTime());
            $form->getData()->setAnswered(FALSE);
            $form->getData()->setClosed(FALSE);

            $em->persist($form->getData());
            $em->flush();
            $session->getFlashBag()->add('success', 'Dein Ticket wurde erstellt. Vielen Dank.');
        }
        return $this->render(
            'main/support.html.twig', [
            'planets'        => $planets[0],
            'selectedPlanet' => $planets[1],
            'user'           => $this->getUser(),
            'messages'       => $this->getMessages($security, $managerRegistry),
            'form'           => $form->createView(),
            'tickets'        => $tickets,
            'slug'           => $slug,
        ],
        );
    }

    /*#[Route('/rules/{slug?}', name: 'rules')]
    public function rules(
        Request $request, ManagerRegistry $managerRegistry, PlanetRepository $p, EntityManagerInterface $em, Security $security, $slug = NULL,
    ): Response
    {
        $planets = $this->getPlanetsByPlayer($managerRegistry, $this->user_uuid, $slug);

        return $this->render(
            'main/rules.html.twig', [
            'planets'        => $planets[0],
            'selectedPlanet' => $planets[1],
            'user'           => $this->getUser(),
            'messages'       => $this->getMessages($security, $managerRegistry),
            'slug'           => $slug,
        ],
        );
    }*/

    #[Route('/notices/{slug?}', name: 'notices')]
    public function playerNotices(
        Request $request, ManagerRegistry $managerRegistry, PlanetRepository $p, EntityManagerInterface $em, Security $security, $slug = NULL,
    ): Response
    {
        $user_uuid = $security->getUser()->getUuid();
        $this->denyAccessUnlessGranted('ROLE_USER');
        $planets = $this->getPlanetsByPlayer($managerRegistry, $user_uuid, $slug);

        return $this->render(
            'main/notices.html.twig', [
            'planets'        => $planets[0],
            'selectedPlanet' => $planets[1],
            'user'           => $this->getUser(),
            'messages'       => $this->getMessages($security, $managerRegistry),
            'slug'           => $slug,
        ],
        );
    }

    #[Route('/support/ticket_close/{ticketId}/{slug?}', name: 'ticket_close')]
    public function closeTicket(
        int                    $ticketId,
        SupportRepository      $supportRepository,
        EntityManagerInterface $em,
                               $slug = NULL,
    ): Response
    {
        $ticket = $supportRepository->find($ticketId);
        $ticket->setClosed(1);
        $em->persist($ticket);
        $em->flush();

        return $this->redirectToRoute(
            'support',
            [
                'slug' => $slug,
            ],
        );
    }


}
