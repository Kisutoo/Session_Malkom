<?php 
namespace App\Controller;

use DateTime;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\CategorieRepository;
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
    // Fonction qui servira à afficher la liste des sessions disponibles
    {
        $sessions = $sessionRepositoy->findBy([], ["nomSession" => "ASC"]);
        // On récupère toutes les sessions de la basse de donnée et on les afficheras par ordre croissant via le nom de session

        return $this->render('session/listeSessions.html.twig', [
            'sessions' => $sessions
        ]);
        // On passe en paramètre la liste des session pour la récupérer dans la vue "listeSessions"
    }

    #[Route('detailSessions/{id}', name: 'detail_session')]
    public function detailSession(int $id, Session $session, ProgrammeRepository $programmeRepository, StagiaireRepository $stagiaireRepository, ModuleRepository $moduleRepository, CategorieRepository $categorieRepository): Response
    // Fonction qui servira a afficher le détail d'une session
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
        $categories = $categorieRepository->findAll([], ["nomCategorie" => "ASC"]);
        
        
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
            'categories' => $categories,
            'stagiairesInscrit' => $stagiairesInscrit,
            'dureeModules' => $dureeModules,
            'modules' => $modules,
            'days' => $days,
            'maxDays' => $maxDays
        ]);
        // On passe toutes ces variables en paramètres pour les récupérer dans la vue détailSession
    }

    #[Route('detailSession/{id}', name: 'add_stagiaire_to_session')]
    public function addStagiaireToSession(int $id, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository): Response
    {
        if(isset($_POST["submit"]))
        // Si le formulaire a bien été validé via l'input submit
        {
            $stagiaireId = filter_input(INPUT_POST, "stagiaire", FILTER_VALIDATE_INT);
            // On sanitize l'id du stagiaire (On vérifie qu'il s'agit bien d'un int)

            if($stagiaireId && $id)
            // Si on arrive récupérer deux variables suite à ça
            {
                $stagiaireArray = $stagiaireRepository->findOneById($stagiaireId);
                // On va chercher l'objet Stagiaire en base de donnée grace à son id
                                
                $session = $session->addStagiaire($stagiaireArray);
                // On ajoute l'objet Stagiaire à la session 

                $entityManager->persist($session);
                $entityManager->flush();
                // Persist & flush sont équivalent à prepare et execute en PDO, on les utilise pour éviter les injections SQL
                
                $this->addFlash("success", "Le stagiaire a bien été ajouté à la session");
                return $this->redirectToRoute('detail_session', ['id' => $id]);
                // On retourne l'utilisateur vers le détail de la session avec un message 
            }
            else
            // Si on arrive pas à récupérer de variables valides
            {
                $this->addFlash("error", "Veuillez rentrer un champ valide");
                return $this->redirectToRoute('detail_session', ['id' => $id]);
                // On retourne l'utilisateur vers le détail de la session avec un message 
            }
        }
        else
        // Si on accède à cette route manuellement sans passer par l'input submit
        {
            return $this->redirectToRoute('detail_session', ['id' => $id]);
            // On retourne l'utilisateur vers le détail de la session
        }
    }



    #[Route('deleteStagiaire/{idSession}/{idStagiaire}', name: 'remove_stagiaire_from_session')]
    public function removeStagiaireFromSession(int $idSession, int $idStagiaire, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository, SessionRepository $sessionRepositoy): Response
    // Cette fontion servira comme son nom l'indique à retirer un stagiaire d'une session
    {
        $stagiaire = $stagiaireRepository->findOneBy(["id" => $idStagiaire]);
        // On récupère l'objet Stagiaire souhaité grace à son id passé en paramètres
        
        $session = $sessionRepositoy->findOneBy(["id" => $idSession]);
        // On récupère également l'objet Session grace à son id passé en paramètres

        $session = $session->removeStagiaire($stagiaire);
        // On retire le stagiaire de la session grace à la méthode removeStagiaire de l'entité Session

        $entityManager->persist($session);
        $entityManager->flush();
        // Puis on renvoie la session en base de donnée comme elle vient d'être modifiée, la table associative qui sert de pont entre Stagiaire et Session sera modifiée en conséquence

        $this->addFlash("success", "Le stagiaire a bien été retiré de la session");
        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
        // On renvoie l'utilisateur sur le détail de la session avec un message lui affirmant que la supression du stagiaire s'est bien déroulée
    }



    #[Route('addModuleToSession/{idSession}/{maxDays}', name: 'add_module_to_session')]
    public function addModuleToSession(int $idSession, int $maxDays, EntityManagerInterface $entityManager, Session $session, ProgrammeRepository $programmeRepository, ModuleRepository $moduleRepository, SessionRepository $sessionRepositoy, Module $module): Response
    // Cette fonction servira à ajouter un module prédéfini à la session, donc à créer un nouveau programme pour la session
    {
        if(isset($_POST["submit"]))
        {
            $moduleId = filter_input(INPUT_POST, "module", FILTER_VALIDATE_INT);
            $duree = filter_input(INPUT_POST, "duree", FILTER_VALIDATE_INT);
            // On sanitize les champs rentrés, en l'occurence, on vérifie qu'il s'agit bien là de deux nombres (un ID pour récupérer l'objet module et la durée souhaitée (en jours) du module)
            
            if($moduleId && $duree && $idSession)
            // Si on arrive bien à récupérer 3 variables avec que les champs aient été assainis 
            {
                $moduleObject = $moduleRepository->findOneBy(["id" => $moduleId], []);
                // On récupère l'object Module souhaité grace à son ID
                $sessionObject = $sessionRepositoy->findOneBy(["id" => $idSession], []);
                // On récupère l'object Session souhaité grace à son ID

                $programmeObj = new Programme();
                // On crée un nouvel object Programme

                $programmeObj->setSession($sessionObject);
                $programmeObj->setModule($moduleObject);
                // On ajoute les deux objets "Module" et "Session" au Programme
                
                if($duree <= $maxDays && $duree > 0)
                // On ne veut pouvoir ajouter de nouveau modules si la durée de celui ci dépasse le nombre de jours que la session possèdera, on vérifie donc ça ici
                {
                    $programmeObj->setDuree($duree);
                    // On ajoute la durée du module souhaitée à l'objet Programme 


                    $entityManager->persist($programmeObj);
                    $entityManager->flush();
                    // Puis on envoie le Programme en base de donnée

                    $this->addFlash('success', "Le module a bien été ajouté au programme.");
                    return $this->redirectToRoute('detail_session', ['id' => $idSession]);
                    // Finalement on redirige l'utilisateur sur le détail de la session avec un message de succès
                }
                else
                // Si la durée n'est pas valide (elle dépasse le nombre de jours de la session ou est inférieure à 0)
                {
                    $this->addFlash('error', "La durée totale des modules ne peut exéder le nombre de jours de la session.");
                    return $this->redirectToRoute('detail_session', ['id' => $idSession]);
                    // On redirige l'utilisateur vers le détail de la session avec un message d'erreur
                }
            }
            else
            // Si les champs saisis dans le formulaire ne sont pas corrects
            {
                $this->addFlash('error', "Veuillez saisir des valeurs correctes.");
                return $this->redirectToRoute('detail_session', ['id' => $idSession]);
                // On redirige l'utilisateur vers le détail de la session avec un message d'erreur
            }
        }
        else
        // Si une personne accède à cette fonction sans valider le formulaire via l'input submit
        {
            return $this->redirectToRoute('detail_session', ['id' => $idSession]);
            // On redirige la personne mal intentionnée vers le détail de la session
        }
    }



    #[Route('deleteModule/{idSession}/{idProgramme}', name: 'remove_module_from_session')]
    public function removeModuleFromSession(int $idSession, int $idProgramme, EntityManagerInterface $entityManager, ProgrammeRepository $programmeRepository): Response
    // Cette fonction servira à retirer un module d'une session, donc à retirer un programme
    {   
        $programme = $programmeRepository->findOneBy(["id" => $idProgramme], []);
        // On récupère l'objet programme que l'on veut retirer via son id reçu en paramètres

        $entityManager->remove($programme);
        // On utilise la méthode remove de l'entity manager
        $entityManager->flush();

        $this->addFlash("success", "Le module a bien été retiré de la session");
        return $this->redirectToRoute('detail_session', ['id' => $idSession]);
        // Puis on redirige l'utilisateur vers le détail de la session avec un message de succès
    }


    #[Route('addNewSession', name: 'add_new_session')]
    public function addNewSession(EntityManagerInterface $entityManager, Session $session): Response
    // Fonction qui servira à créer une nouvelle session et l'ajouter en base de donnée
    {

        if(isset($_POST["submit"]))
        // Si on a validé le formulaire avec le bouton submit
        {
            $nomSession = filter_input(INPUT_POST, "nomSession", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $dateDebut = filter_input(INPUT_POST, "dateDebut");
            $dateFin = filter_input(INPUT_POST, "dateFin");
            $pTheoriques = filter_input(INPUT_POST, "pTheoriques", FILTER_VALIDATE_INT);
            $pReservees = filter_input(INPUT_POST, "pReservees", FILTER_VALIDATE_INT);
            // On récupère les inputs dans des variables puis on les assainis pour éviter les failles XSS

            if($nomSession && $dateDebut && $dateFin && $pTheoriques && $pReservees)
            // Si on arrive à récupérer toutes les variables
            {
                $session->setNomSession($nomSession);
                // On vient modifier l'objet Session avec seulement le nom de la session pour l'instant car il n'y a plus de vérification à faire avec celui ci 

                // ------------------------------------------
                $dateF = explode('-', $dateFin);
                $dateD = explode('-', $dateDebut);

                $anneeFin = $dateF[0];
                $moisFin = $dateF[1];

                $anneeDebut = $dateD[0];
                $moisDebut = $dateD[1];

                $timeF = explode('T', $dateF[2]);
                $timeD = explode('T', $dateD[2]);
                                                                // Tout ce code sert simplement à récupérer les jours/mois/année/heure/minutes des chaines de caractères
                                                                // dateDebut et dateFin des input, dans le but de faire des vérification juste après
                $joursFin = $timeF[0];
                $joursDebut = $timeD[0];

                $hourMinDebut = explode(':', $timeF[1]);
                $hourMinFin = explode(':', $timeD[1]);

                $hourDebut = $hourMinDebut[0];
                $minDebut = $hourMinDebut[1];

                $hourFin = $hourMinFin[0];
                $minFin = $hourMinFin[1];
                // ---------------------------------------------

                // -----------------------------------------------------------------------------------------------
                // On crée ensuite deux nouveaux objets dateTime dans le but de les comparer avec dateDiff ensuite
                // -----------------------------------------------------------------------------------------------

                $dateDebutObj = new DateTime(); 
                $dateDebutObj->setDate($anneeDebut, $moisDebut, $joursDebut);
                $dateDebutObj->setTime($hourDebut, $minDebut);
                // Modifie le nouvel objet date pour le faire correspondre à la chaine de caractères de l'input dateDebut

                $dateFinObj = new DateTime(); 
                $dateFinObj->setDate($anneeFin, $moisFin, $joursFin);
                $dateFinObj->setTime($hourFin, $minFin);
                // Modifie le nouvel objet date pour le faire correspondre à la chaine de caractères de l'input dateFin

                if(checkdate($moisFin, $joursFin, $anneeFin) && checkdate($moisDebut, $joursDebut, $anneeDebut))
                // Ici, on vient vérifier si les dates passées en paramètres sont valides, il n'existe pas de Filter_Validate_Date ou autre à ma connaissance donc j'ai fait comme celà
                {
                    $dateDiff = date_diff($dateDebutObj, $dateFinObj);
                    // On vient comparer les deux dates de début et de fin dans le but de vérifier si la date de début n'est pas supérieure à celle de fin, ou inversement
                    $invert = $dateDiff->invert;
                    // invert, dans l'objet dateDiff, renvoie 0 si la comparaison de date_diff est positive, ou -1 si elle est négative
                    
                    if($invert == 0)
                    // Si la comparaison de date_diff est positive, donc si la date de début est bien inférieure à la date de fin
                    {
                        $session->setDateDebut($dateDebutObj);
                        $session->setDateFin($dateFinObj);
                        // On ajoute ces deux dates début et fin à l'objet session créé au début

                        if($pTheoriques > $pReservees)
                        // On procède ici à une nouvelle vérification, on veut s'assurer que le nombre de place réservé, ne soit pas supérieur au nombre de place disponible en session
                        {
                            $session->setPlaceTheorique($pTheoriques);
                            $session->setPlaceReserve($pReservees);
                            // Si tout va bien, on ajouter également ces deux nouvelles variables à l'objet session

                            $entityManager->persist($session);
                            $entityManager->flush();
                            // Puis on envoie cet objet session en base de donnée car il n'a plus besoin d'être modifié et toutes les vérification ont été faites

                            $this->addFlash("success", "Une nouvelle session a été créée avec succès.");
                            return $this->redirectToRoute('liste_sessions');
                            // Pour finir on redirige l'utilisateur sur la liste des sessions avec un message lui spécifiant qu'il a bien crée une nouvelle session
                        }
                        else
                        // Si le nombre de places théoriques est inférieur au nombre de places réservées (ce qui n'est pas possible), on annule le processus de création d'une nouvelle session
                        {
                            $this->addFlash("error", "Le nombre de places théoriques ne peut être inférieur au nombre de places réservées.");
                            return $this->redirectToRoute('liste_sessions'); 
                            // On redirige l'utilisateur vers la liste de sessions avec un message d'erreur correspondant 
                        }
                    }
                    else
                    // Si invert de l'objet dateDiff est négatif, ce qui signifie que la date de fin est inférieure à la date de début
                    {
                        $this->addFlash("error", "La date de début ne peut être inférieure à la date de fin.");
                        return $this->redirectToRoute('liste_sessions'); 
                        // On redirige l'utilisateur vers la liste des sessions avec un message d'erreur
                    }
                }
                else
                // Si les dates saisies dans l'input date ne correspondent pas à des dates valides
                {
                    $this->addFlash("error", "Veuillez saisir une date valide.");
                    return $this->redirectToRoute('liste_sessions');
                    // On redirige également l'utilisateur vers la liste des sessions avec un message d'erreur
                }
            }
            else
            // Si n'importe quel autre champ du formulaire n'est pas valide, on arrive donc pas à récupérer les 5 variables souhaitées
            {
                $this->addFlash("error", "Veuillez saisir des données valides.");
                return $this->redirectToRoute('liste_sessions');
            }
        }
        else
        // Si on accède à cette fonction sans appuyer sur l'input button du formulaire
        {
            return $this->redirectToRoute('liste_sessions');
        }
    }

    #[Route('deleteSession/{idSession}', name: 'delete_session')]
    public function deleteSession(int $idSession, EntityManagerInterface $entityManager, SessionRepository $sessionRepositoy): Response
    // Cette fonction servira à supprimer une fonction de la base de donnée et de la liste des sessions
    {
        $sessionObj = $sessionRepositoy->findOneBy(["id" => $idSession], []);
        // On récupère un objet session grace à son id que l'on a reçu en paramètres
        
        $entityManager->remove($sessionObj);
        $entityManager->flush();
        // Retire la session de la base de donnée

        return $this->redirectToRoute('liste_sessions');
        // Redirige l'utilisateur sur la liste des sessions
    }
}
?>
