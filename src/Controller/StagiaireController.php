<?php 
namespace App\Controller;

use DateTime;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Repository\UserRepository;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('newStagiaireForm', name: 'new_stagiaire_form')]
    public function newStagiaireForm(): Response
    // Cette fonction servira a rediriger l'utilisateur vers le formulaire de création d'un nouveau stagiaire
    {

        return $this->render('stagiaire/newStagiaireForm.html.twig');
    }

    #[Route('admin/addNewStagiaire', name: 'add_new_stagiaire')]
    #[Route('editStagiaire/{idStagiaire}', name: 'edit_stagiaire')]
    public function addNewEditStagiaire(EntityManagerInterface $entityManager, Stagiaire $stagiaire): Response
    // Cette fonction servira à créer et ajouter un nouveau stagiaire en base de donnée
    {

        if(isset($_POST["submit"]))
        // Si on accède à cette fonction en validant le formulaire avec le bouton submit
        {
            $nom = filter_input(INPUT_POST, "nom", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, "prenom", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sexe = filter_input(INPUT_POST, "sexe", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $adresse = filter_input(INPUT_POST, "adresse", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $ville = filter_input(INPUT_POST, "ville", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cp = filter_input(INPUT_POST, "cp", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $dateNaissance = filter_input(INPUT_POST, "dateNaissance", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $telephone = filter_input(INPUT_POST, "telephone", FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/[0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2} [0-9]{2}/")));
            $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
            // On vient assainir tous les champs reçu via le formulaire et on les mets dans des variables 

            if($nom && $prenom && $sexe && $adresse && $ville && $cp && $dateNaissance && $telephone && $email)
            // Si toutes les variables ont été correctement remplies
            {
                $stagiaire->setNom($nom);
                $stagiaire->setPrenom($prenom);
                $stagiaire->setSexe($sexe);
                $stagiaire->setVille($ville);
                $stagiaire->setCp($cp);
                $stagiaire->setAdresse($adresse);
                $stagiaire->setTelephone($telephone);
                $stagiaire->setEmail($email);
                // On les mets toutes dans l'objet stagiaire crée à l'appel de la fonction SAUF la date de naissance qui subira une vérification en plus car il n'existe pas de filtre pour assainir une date

                
                $date = explode('-', $dateNaissance);
                $time = explode('T', $date[2]);
                                                // Ce qu'on récupère de l'input date est sous cette forme : 10-12-2003T12:15, on explode donc dans une variable et on récupères les information qui nous intéressent si elles existent
                $annee = $date[0];
                $mois = $date[1];
                $jours = $time[0];

                if(checkdate($mois, $jours, $annee))
                // checkdate retourne true si la date est valide et false si elle ne l'est pas, donc si elle est valide, on ajoute la date dans l'objet stagiaire
                {
                    $newDateNaissance = new DateTime();
                    $newDateNaissance->setDate($annee, $mois, $jours);
                    
                    $stagiaire->setDateNaissance($newDateNaissance);

                    $entityManager->persist($stagiaire);
                    $entityManager->flush();
                    // On ajoute ensuite l'objet stagiaire en base de donnée

                    $this->addFlash("success", "Le nouveau stagiaire a bien été ajouté");
                    return $this->redirectToRoute('liste_stagiaires');
                    // Puis on redirige l'utilisateur vers la liste des sessions avec un message lui spécifiant que le stagiaire a bien été créé
                }
                else
                // Si la date n'est pas valide
                {
                    $this->addFlash("error", "Veuillez saisir une date valide");
                    return $this->redirectToRoute('new_stagiaire_form');
                    // On redirige l'utilisateur vers le formulaire avec un message d'erreur correspondant
                }
            }
            else
            // Si on arrive pas a récupérer les variables des différents inputs
            {
                $this->addFlash("error", "Veuillez saisir des informations valides");
                return $this->redirectToRoute('new_stagiaire_form');
                // On redirige l'utilisateur vers le formulaire avec un message d'erreur
            }
        }
        else
        // Si on accède a cette fonction sans valider le formulaire
        {
            return $this->redirectToRoute('new_stagiaire_form');
        }
    }

    #[Route('listeStagiaire', name: 'liste_stagiaires')]
    public function listeStagiaires(EntityManagerInterface $entityManager, StagiaireRepository $stagiaireRepository): Response
    // Cette fonction sert à afficher la liste des stagiaire dans une vue dédiée
    {
        $stagiaires = $stagiaireRepository->findAll([], ["nom" => "ASC"]);
        // On récupère tous les stagiaires

        return $this->render("stagiaire/listeStagiaires.html.twig", [
        // Puis on renvoie l'utilisateur vers une vue avec une variable qui contiendra tous les stagiaires présent en base de donnée
            "stagiaires" => $stagiaires
        ]);
    }

    #[Route('admin/deleteStagiaire/{idStagiaire}', name: 'delete_stagiaire')]
    public function deleteStagiaire(int $idStagiaire, EntityManagerInterface $entityManager, StagiaireRepository $stagiaireRepository): Response
    // Cette fonction sert à effacer un stagiaire de la base de donnée
    {
        $stagiaire = $stagiaireRepository->findOneBy(["id" => $idStagiaire], []);
        // On récupère l'objet stagiaire que l'on veut supprimer grâce à son id passé en paramètres

        $entityManager->remove($stagiaire);
        $entityManager->flush();
        // Puis on le supprime de la base de donnée

        return $this->redirectToRoute('liste_stagiaires');
        // On retourne ensuite l'utilisateur vers la vue qui affiche la liste des stagiaires
    }

    #[Route('detailStagiaire/{idStagiaire}', name: 'detail_stagiaire')]
    public function detailStagiaire(int $idStagiaire, EntityManagerInterface $entityManager, StagiaireRepository $stagiaireRepository, SessionRepository $sessionRepository, Stagiaire $stagiaire): Response
    // Cette fonction permet d'afficher le détail d'un stagiaire et les session auxquelles il est inscrit
    {
        $stagiaireObj = $stagiaireRepository->findOneBy(["id" => $idStagiaire], []);
        // On récupère le stagiaire dont on veut le détail grâce à son identifiant passé en paramètres
        $sessions = $sessionRepository->findSessionsByStagiaire($idStagiaire);
        // On récupère également les sessions auxquelles il est inscrit grâce à son id

        return $this->render("stagiaire/detailStagiaire.html.twig", [
        // On renvoie ensuite l'utilisateur vers la vue correspondante avec 2 variables
            "stagiaire" => $stagiaireObj,
            "sessions" => $sessions
        ]);
    }

    #[Route('membre/profile/{idUser}', name: 'see_profile')]
    public function seeProfile(int $idUser, UserRepository $userRepository, SessionRepository $sessionRepository): Response
    // Cette fonction servira à afficher le profil d'un Utilisateur (formateur), avec les session auxquelles il est associé
    {
        $user = $userRepository->findOneBy(["id" => $idUser], []);
        // On récupère l'utilisateur grâce à son id passé en paramètres
        $sessions = $sessionRepository->findSessionsByFormateur($idUser);
        // On vient récupérer toutes les session auxquelles il participe en tant que formateur référent

        return $this->render("stagiaire/profile.html.twig", [
        // On le redirige ensuite vers son profil avec la liste de ses sessions
            "user" => $user,
            "sessions" => $sessions
        ]);
    }

}