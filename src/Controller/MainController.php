<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;

class MainController extends Controller
{
    /**
     * @Route("/")
     */
    public function index()
    {
      return $this->render('index.html.twig');
    }
    /**
     * @Route("/maps/longlat")
     */
     public function longlat()
     {
       if (isset($_GET['submitForm'])) {
         if (!empty($_GET["address"])) {
           $address = urlencode($_GET["address"]);
           $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBt3uKBhBC3dEBbvgOGkXcKzB8fQilcJDA";
           $resp_json = file_get_contents($url);
           $resp = json_decode($resp_json, true);
         } else {
           $lat = urlencode($_GET["lat"]);
           $lng = urlencode($_GET["lng"]);
           $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key=AIzaSyBt3uKBhBC3dEBbvgOGkXcKzB8fQilcJDA";
           $resp_json = file_get_contents($url);
           $resp = json_decode($resp_json, true);
         }
         if ($resp['status']=='OK') {
             $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
             $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
             $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
             if ($lati && $longi && $formatted_address) {
               return $this->render('maps/longlat.html.twig', array(
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
       } else {
         return $this->render('maps/longlat.html.twig');
       }
     }

    /**
     * @Route("/maps/distance")
     */
     public function distance()
     {
       if (isset($_GET['submitForm'])) {
         $origin = urlencode($_GET["origin"]);
         $destination = urlencode($_GET["destination"]);
         $originGeo ;
         $destinationGeo ;
         for ($x = 0; $x <= 1; $x ++) {
           if ($x == 0) {
             $geolocation = $origin;
           }
           else {
             $geolocation = $destination;
           }

           $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$geolocation}&key=AIzaSyBt3uKBhBC3dEBbvgOGkXcKzB8fQilcJDA";
           $resp_json = file_get_contents($url);
           $resp = json_decode($resp_json, true);

           if ($resp['status']=='OK') {
             $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
             $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
             $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

             if ($lati && $longi && $formatted_address) {
               if ($x == 0) {
                 $originGeo = array($lati, $longi, $formatted_address);
               } else {
                 $destinationGeo = array($lati, $longi, $formatted_address);
               }
             } else {
               return false;
             }
           }
           else {
             echo "<strong>ERROR: {$resp['status']}</strong>";
             return false;
           }
         }
         $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origin}&destinations={$destination}&key=AIzaSyDEAsSxtHwgJdj083m2dYz3t4jxXOQ2Eng";
         $resp_json = file_get_contents($url);
         $resp = json_decode($resp_json, true);

         if ($resp['status']=='OK') {
           $distance = isset($resp['rows'][0]['elements'][0]['distance']['value']) ? $resp['rows'][0]['elements'][0]['distance']['value'] : "";


           if ($distance == null) {
             $distance = "This route does not exist";
           }

           return $this->render('maps/distance.html.twig', array(
                 'distance' => $distance,
                 'originLatitude' => $originGeo[0], 'originLongitude' => $originGeo[1], 'originFromattedAddress' => $originGeo[2],
                 'destinationLatitude' => $destinationGeo[0], 'destinationLongitude' => $destinationGeo[1], 'destinationFromattedAddress' => $destinationGeo[2]
           ));
         }
         else {
           echo "<strong>ERROR: {$resp['status']}</strong>";
           return false;
         }
       }
       else {
         return $this->render('maps/distance.html.twig');
       }
     }
     /**
      * @Route("/maps/navigation")
      */
     public function navigation()
     {
       if (isset($_GET['submitForm'])) {
         $origin = urlencode($_GET['origin']);
         $destination = urlencode($_GET['destination']);
         $mode = $_GET['mode'];

         for ($x = 0; $x <= 1; $x ++) {
           if ($x == 0) {
             $geolocation = $origin;
           }
           else {
             $geolocation = $destination;
           }

           $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$geolocation}&key=AIzaSyBt3uKBhBC3dEBbvgOGkXcKzB8fQilcJDA";
           $resp_json = file_get_contents($url);
           $resp = json_decode($resp_json, true);

           if ($resp['status']=='OK') {
             $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

             if ($formatted_address) {
               if ($x == 0) {
                 $originFormatted = $formatted_address;
               } else {
                 $destinationFormatted = $formatted_address;
               }
             } else {
               return false;
             }
           }
         }
         $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origin}&destinations={$destination}&key=AIzaSyDEAsSxtHwgJdj083m2dYz3t4jxXOQ2Eng";
         $resp_json = file_get_contents($url);
         $resp = json_decode($resp_json, true);

         if ($resp['status']=='OK') {
           $distance = isset($resp['rows'][0]['elements'][0]['distance']['value']) ? $resp['rows'][0]['elements'][0]['distance']['value'] : "";
         }

         return $this->render('maps/navigation.html.twig', array(
               'origin' => $originFormatted, 'destination' => $destinationFormatted, 'mode' => $mode, 'distance' => $distance
         ));
       }
       else {
         return $this->render('maps/navigation.html.twig');
       }
     }

     /**
      * @Route("/maps/locations")
      */
     public function locations()
     {
       if (isset($_GET['submitForm'])) {
         return $this->render('maps/locations.html.twig', array(
               'origin' => $originFormatted, 'destination' => $destinationFormatted, 'mode' => $mode, 'distance' => $distance
         ));
       }
       else {
         return $this->render('maps/locations.html.twig');
       }
     }

     /**
      * @Route("/addrow")
      */
     public function addrow(Connection $conn)
     {
       try
       {
        $dsn = "mysql:host=localhost;dbname=google";
        $db = new PDO ($dsn, "root", "root");
        //print ("Connected\n");
       }
       catch (PDOException $e)
       {
        print ("Cannot connect to server\n");
        print ("Error code: " . $e->getCode() . "\n");
        print ("Error message: " . $e->getMessage() . "\n");
       }

       $stmt=$db->prepare("INSERT INTO markers (name, address, lat, lng, type) VALUES (:name, :address, :lat, :lng, :type)");
       		$stmt->bindParam(':name', $name);
       		$stmt->bindParam(':address', $address);
           $stmt->bindParam(':lat', $lat);
           $stmt->bindParam(':lng', $lng);
           $stmt->bindParam(':type', $type);

           // Gets data from URL parameters.
           $name = $_GET['name'];
           $address = $_GET['address'];
           $lat = $_GET['lat'];
           $lng = $_GET['lng'];
           $type = $_GET['type'];

       	  $stmt->execute();
     }
}
