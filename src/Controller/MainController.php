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
     * @Route("/maps/longlat")
     */
     public function longlat()
     {
       return $this->render('maps/longlat.html.twig');
     }
    /**
     * @Route("/maps/longlat/results")
    */
    public function longlat_result()
    {
      // url encode the address
      $address = urlencode($_GET["address"]);

      // google map geocode api url
      $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBt3uKBhBC3dEBbvgOGkXcKzB8fQilcJDA";

      // get the json response
      $resp_json = file_get_contents($url);

      // decode the json
      $resp = json_decode($resp_json, true);

      // response status will be 'OK', if able to geocode given address
      if ($resp['status']=='OK') {

        // get the important data
        $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

        // verify if data is complete
        if ($lati && $longi && $formatted_address) {

          return $this->render('maps/longlat_result.html.twig', array(
                'longitude' => $longi, 'latitude' => $lati, 'formatted_address'=> $formatted_address
          ));

        } else {
          return false;
        }
      }

      else {
        echo "<strong>ERROR: {$resp['status']}</strong>";
        return false;
      }
    }
}
