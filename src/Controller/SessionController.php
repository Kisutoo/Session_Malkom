<?php 
namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function detailSession(int $id, Session $session, ProgrammeRepository $programmeRepository, StagiaireRepository $stagiaireRepository): Response
    {
        $programmes = $programmeRepository->findBy(["session" => $id], []);
        $stagiaires = $stagiaireRepository->findByNoSession($id, []);
        $stagiairesInscrit = $stagiaireRepository->findBySession($id, []);

        return $this->render('session/detailSession.html.twig', [
            'session' => $session,
            'programmes' => $programmes,
            'stagiaires' => $stagiaires,
            'stagiairesInscrit' => $stagiairesInscrit
        ]);
    }

    #[Route('detailSession/{id}', name: 'add_stagiaire_to_session')]
    public function AddStagiaireToSession(int $id, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository): Response
    {
        if(isset($_POST["submit"]))
        {
            $stagiaireId = filter_input(INPUT_POST, "stagiaire", FILTER_VALIDATE_INT);

            if($stagiaireId && $id)
            {
                $stagiaireArray = $stagiaireRepository->findOneById($stagiaireId);
                                
                $session = $session->addStagiaire($stagiaireArray);

                $entityManager->persist($session);
                $entityManager->flush();
                
                $this->addFlash("success", "Le stagiaire a bien été ajouté à la session");
                return $this->redirectToRoute('detail_session', ['id' => $id]);
            }
            else
            {
                $this->addFlash("error", "Veuillez rentrer un champ valide");
                return $this->redirectToRoute('detail_session', ['id' => $id]);
            }
        }
        else
        {
            return $this->redirectToRoute('detail_session', ['id' => $id]);
        }

    }

    #[Route('detailSession/{idSession}/{idStagiaire}', name: 'remove_stagiaire_from_session')]
    public function removeStagiaireFromSession(int $idSession, int $idStagiaire, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository): Response
    {
        $stagiaireArray = $stagiaireRepository->findOneById($idStagiaire);
        
        $listSession = $session->getStagiaires();
        $session = $session->removeStagiaire($stagiaireArray);

        

        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash("success", "Le stagiaire a bien été retiré de la session");
        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
    }
    
}
?>