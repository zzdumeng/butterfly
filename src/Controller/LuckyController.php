<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{

    /**
     * @Route("lucky")
     *
     * @return void
     */
    public function index()
    {
        return new Response("lucky home page.");
    }
    /**
     * @Route("lucky/number")
     */
    public function number()
    {
        //
        $req = Request::createFromGlobals();
        $token = $req->cookies->get('token');
        if (!isset($token)) {
            // has not login
            return new Response("<h1>you need to login in to access this resource.</h1>" .
                "<b><a href='/login'>loign</a></b>");
        }
        // check if the token in database
        $userEntity = $this->getDoctrine()->getRepository(User::class);
        $userEntity->findOneBy(['token' => $token]);
        if (!$userEntity) {

            return new Response("<h1>you need to login in to access this resource.</h1>" .
                "<b><a href='/login'>loign</a></b>");
        }
        $number = random_int(0, 100);

        return $this->render("lucky/number.html.twig", [
            "number" => $number,
        ]);
        // return new Response(
        //     '<html><body>Lucky number: ' . $number . '</body></html>'
        // );
    }
}
