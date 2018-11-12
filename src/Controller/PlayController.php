<?php
// src/Controller/LuckyController.php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RandomService;

class PlayController extends AbstractController
{
    public $counter = 0;
    public static $number = 0;
    /**
     * @Route("play")
     *
     * @return void
     */
    public function index()
    {
        return new Response("play home page.");
    }
    /**
     * @Route("play/number")
     */
    public function number(RandomService $random) {
      $n = $random->random();
      return new Response("counter is  $n");
    }
    /**
     * @Route("play/count")
     */
    public function counter(RandomService $random) {
      $n = $random->getCounter();
      return new Response("counter is $n");
    }
    /**
     * @Route("/info")
     */
    public function info() {
      return new Response(phpinfo());
    }
}
