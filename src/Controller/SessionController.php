<?php 
namespace App\Controller;

use App\Entity\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{

    #[Route('/listeSessions', name: 'liste_sessions')]
    public function listeSessions(Session $session): Response
    {


        return $this->render('session/listeSessions.html.twig');
    }

}
?>