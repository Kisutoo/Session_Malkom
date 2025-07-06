<?php 
namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Repository\ModuleRepository;
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
    public function detailSession(int $id, Session $session, ProgrammeRepository $programmeRepository, StagiaireRepository $stagiaireRepository, ModuleRepository $moduleRepository): Response
    {
        $programmes = $programmeRepository->findBy(["session" => $id], []);
        // Servira à afficher le programme d'une session
        $dureeModules = $programmeRepository->findBy(["session" => $id], []);
        // Servira à compter le nombre de jours des modules cumulés
        $modules = $moduleRepository->findByNoModules($id, []);
        // Servira à afficher les modules qui ne sont pas encore dans la session
        $stagiaires = $stagiaireRepository->findByNoSession($id, []);
        // Serivra à afficher les stagiaires qui ne sont pas encore dans la session
        $stagiairesInscrit = $stagiaireRepository->findBySession($id, []);
        // Servira à afficher les stagiaires inscrits dans la session

        $dateDebut = $session->getDateDebut();
        $dateFin = $session->getDateFin();
        // On récupère les dates de début et de fin pour calculer la différence et récupérer le nombre de jours total de la session

        $dateDiff = date_diff($dateDebut, $dateFin);
        // Différence entre les deux dates
        $days = $dateDiff->days;
        // On récupère le nombre de jours de l'objet dateTime, Servira à afficher le nombre de jours total d'une session

        $maxDays = 0;
        // Servira à empêcher l'utilisateur de rentrer une durée qui excedera le nombre de jours maximum de la session

        foreach($dureeModules as $module)
        // On récupère chaque modules associés à la session
        {
            $maxDays += $module->getDuree();
            // On incrémente maxDays en additionnant les durées des modules
        }
        $maxDays = $days - $maxDays; 
        

        return $this->render('session/detailSession.html.twig', [
            'session' => $session,
            'programmes' => $programmes,
            'stagiaires' => $stagiaires,
            'stagiairesInscrit' => $stagiairesInscrit,
            'dureeModules' => $dureeModules,
            'modules' => $modules,
            'days' => $days,
            'maxDays' => $maxDays
        ]);
        // On passe toutes ces variables en paramètres pour les récupérer dans la vue détailSession
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



    #[Route('deleteStagiaire/{idSession}/{idStagiaire}', name: 'remove_stagiaire_from_session')]
    public function removeStagiaireFromSession(int $idSession, int $idStagiaire, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository, SessionRepository $sessionRepositoy): Response
    {
        $stagiaire = $stagiaireRepository->findOneBy(["id" => $idStagiaire]);
        
        $session = $sessionRepositoy->findOneBy(["id" => $idSession]);

        $session = $session->removeStagiaire($stagiaire);

        $entityManager->persist($session);
        $entityManager->flush();

        $this->addFlash("success", "Le stagiaire a bien été retiré de la session");
        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
    }



    #[Route('addModuleToSession/{idSession}/{maxDays}', name: 'add_module_to_session')]
    public function addModuleToSession(int $idSession, int $maxDays, EntityManagerInterface $entityManager, Session $session, ProgrammeRepository $programmeRepository, ModuleRepository $moduleRepository, SessionRepository $sessionRepositoy, Module $module): Response
    {
        if(isset($_POST["submit"]))
        {
            $moduleId = filter_input(INPUT_POST, "module", FILTER_VALIDATE_INT);
            $duree = filter_input(INPUT_POST, "duree", FILTER_VALIDATE_INT);
            
            if($moduleId && $duree && $idSession)
            {
                $moduleObject = $moduleRepository->findOneBy(["id" => $moduleId], []);
                $sessionObject = $sessionRepositoy->findOneBy(["id" => $idSession], []);

                $programmeObj = new Programme();

                $programmeObj->setSession($sessionObject);
                $programmeObj->setModule($moduleObject);
                
                if($duree <= $maxDays && $duree > 0)
                {
                    $programmeObj->setDuree($duree);

                    $entityManager->persist($programmeObj);
                    $entityManager->flush();

                    $this->addFlash('success', "Le module a bien été ajouté au programme.");
                    $this->redirectToRoute('detail_session', ['id' => $idSession]);
                }
                else
                {
                    $this->addFlash('error', "La durée totale des modules ne peut exéder le nombre de jours de la session.");
                    $this->redirectToRoute('detail_session', ['id' => $idSession]);
                }
            }
            else
            {
                $this->addFlash('error', "Veuillez saisir des valeurs correctes.");
                $this->redirectToRoute('detail_session', ['id' => $idSession]);
            }
        }
        else
        {
            $this->redirectToRoute('detail_session', ['id' => $idSession]);
        }

        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
    }



    #[Route('deleteModule/{idSession}/{idProgramme}', name: 'remove_module_from_session')]
    public function removeModuleFromSession(int $idSession, int $idProgramme, EntityManagerInterface $entityManager, Session $session, ProgrammeRepository $programmeRepository, SessionRepository $sessionRepositoy): Response
    {   
        $programme = $programmeRepository->findOneBy(["id" => $idProgramme], []);

        $entityManager->remove($programme);
        $entityManager->flush();

        $this->addFlash("success", "Le module a bien été retiré de la session");
        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
    }
    
}
?>
