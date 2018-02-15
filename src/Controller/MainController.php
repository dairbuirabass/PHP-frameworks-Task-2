<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @Route("/")
     */
    public function number()
    {
      $number = mt_rand(0, 100);

      return $this->render('index.html.twig', array(
          'number' => $number,
      ));
    }
    /**
     * @Route("/maps")
     */
     public function blabla()
     {
       $number = mt_rand(0, 100);

       return $this->render('maps/longlat.html.twig', array(
           'number' => $number,
       ));
     }

}
