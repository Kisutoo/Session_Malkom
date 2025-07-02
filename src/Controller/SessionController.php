<?php 
namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{

    #[Route('/listeSessions', name: 'liste_sessions')]
    public function listeSessions(SessionRepository $sessionRepositoy): Response
    {
        $sessions = $sessionRepositoy->findBy([], ["nomSession" => "ASC"]);

        return $this->render('session/listeSessions.html.twig', [
            'sessions' => $sessions
        ]);
    }

    #[Route('detailSessions/{id}', name: 'detail_session')]
    public function detailSession(int $id, Session $session, ProgrammeRepository $programmeRepository): Response
    {
        $programmes = $programmeRepository->findBy(["session" => $id], []);

        return $this->render('session/detailSession.html.twig', [
            'session' => $session,
            'programmes' => $programmes
        ]);
    }

}
?>