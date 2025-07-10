<?php 
namespace App\Controller;

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

class ModuleController extends AbstractController
{

    #[Route('admin/addNewModule/{idSession}', name: 'add_new_module')]
    public function addNewModule(int $idSession, EntityManagerInterface $entityManager, Session $session, StagiaireRepository $stagiaireRepository, Module $module, CategorieRepository $categorieRepository): Response
    // Cette fonction servira à créer et ajouter un nouveau module en base de donnée
    {
        if(isset($_POST["submit"]))
        // Si le formulaire a été validé via le bouton submit
        {
            $nomModule = filter_input(INPUT_POST, "nomModule", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idCategorie = filter_input(INPUT_POST, "idCategorie", FILTER_VALIDATE_INT);
            // On sanitize les champs données saisies dans le formulaire

            if($nomModule && $idCategorie)
            // Si on arrive à récupérer deux variables après que les champs aient été assainis
            {
                $categorieObj = $categorieRepository->findOneBy(["id" => $idCategorie], []);
                // On récupère l'objet catégorie souhaité grace à son id reçu en paramètres du formulaire

                $module->setNomModule($nomModule);
                $module->setCategorie($categorieObj);
                // On modifie le nouvel objet module avec les données du formulaire
                
                $entityManager->persist($module);
                $entityManager->flush();
                // Puis on l'envoie en base de donnée

                $this->addFlash("success", "Le module a bien été créé.");
                return $this->redirectToRoute('detail_session', ['id' => $idSession]);
                // On redirige ensuite l'utilisateur sur la page du détail de session avec un message de succès 
            }
            else
            // Si les données saisies dans le formulaire sont incorrectes 
            {
                $this->addFlash("error", "Veuillez saisir un nom de module et choisissez une catégorie à celui-ci.");
                return $this->redirectToRoute('detail_session', ['id' => $idSession]);
                // On redirige l'utilisateur sur la page du détail de session avec un message d'erreur
            }
        }
        else
        {
            return $this->redirectToRoute('detail_session', ['id' => $idSession]);
        }
    }
}