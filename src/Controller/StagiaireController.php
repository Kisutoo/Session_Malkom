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

class StagiaireController extends AbstractController
{
    #[Route('newStagiaireForm', name: 'new_stagiaire_form')]
    public function newStagiaireForm(): Response
    // Cette fonction servira a rediriger l'utilisateur vers le formulaire de création d'un nouveau stagiaire
    {

        return $this->render('stagiaire/newStagiaireForm.html.twig', [
            
        ]);
    }
}