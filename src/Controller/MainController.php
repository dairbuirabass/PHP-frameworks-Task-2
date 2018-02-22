<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DriverManager;

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
         return $this->render('maps/locations.html.twig');
     }

     /**
      * @Route("/maps/images")
      */
     public function images()
     {
       if (isset($_POST['submit'])) {
         if($_SERVER["REQUEST_METHOD"] == "POST") {
             // Check if file was uploaded without errors
             if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0){

                 $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
                 $filename = $_FILES["photo"]["name"];
                 $filetype = $_FILES["photo"]["type"];
                 $filesize = $_FILES["photo"]["size"];

                 // Verify file extension
                 $ext = pathinfo($filename, PATHINFO_EXTENSION);
                 if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

                 // Verify file size - 5MB maximum
                 $maxsize = 5 * 1024 * 1024;
                 if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

                 // Verify MYME type of the file
                 if(in_array($filetype, $allowed)){
                     // Check whether file exists before uploading it
                     if(file_exists("upload/" . $_FILES["photo"]["name"])){
                         $message =  $_FILES["photo"]["name"] . " already exists.";
                         // return $this->redirect('maps/images', 308, array('message' => '$_FILES["photo"]["name"] . " already exists."'));
                     } else{
                         move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $_FILES["photo"]["name"]);
                         $message =  "Your file was uploaded successfully.";
                         // return $this->render('maps/images.html.twig', array( 'message' => $message ));
                     }
                 } else {
                     $message = "Error: There was a problem uploading your file. Please try again.";
                     // return $this->redirect('maps/images', 308);
                     // return $this->render('maps/images.html.twig', array( 'message' => 'Error: There was a problem uploading your file. Please try again.' ));

                 }

             } else{
                 $message = "Error: " . $_FILES["photo"]["error"];
                 return $this->render('maps/images.html.twig', array( 'message' => "Error: " . $_FILES["photo"]["error"] ));
             }
             return $this->render('maps/images.html.twig', array( 'message' => $message ));

          }
       }
       else {
         return $this->render('maps/images.html.twig');
       }
     }

     /**
      * @Route("/saveimages")
      */
     public function saveImage()
     {
       if (isset($_GET['submit'])) {
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
               return $this->render('maps/images.html.twig', array(
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
         return $this->render('maps/images.html.twig');
       }

       if($_SERVER["REQUEST_METHOD"] == "POST") {
           // Check if file was uploaded without errors
           if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0){

               $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
               $filename = $_FILES["photo"]["name"];
               $filetype = $_FILES["photo"]["type"];
               $filesize = $_FILES["photo"]["size"];

               // Verify file extension
               $ext = pathinfo($filename, PATHINFO_EXTENSION);
               if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");

               // Verify file size - 5MB maximum
               $maxsize = 5 * 1024 * 1024;
               if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

               // Verify MYME type of the file
               if(in_array($filetype, $allowed)){
                   // Check whether file exists before uploading it
                   if(file_exists("upload/" . $_FILES["photo"]["name"])){
                       echo $_FILES["photo"]["name"] . " is already exists.";
                       return $this->redirect('maps/images', 308, array('message' => '$_FILES["photo"]["name"] . " is already exists."'));
                   } else{
                       // $destination_path = getcwd().DIRECTORY_SEPARATOR;
                       // echo $destination_path;
                       // $target_path = $destination_path . basename( $_FILES["photo"]["name"]);
                       // move_uploaded_file($_FILES['photo']['tmp_name'], $target_path);
                       move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $_FILES["photo"]["name"]);
                       echo "Your file was uploaded successfully.";
                       // return $this->redirect('maps/images', 308);

                       return $this->redirect($this->generateUrl('maps/images', array('message' => 'Your file was uploaded successfully.')));
                   }
               } else {
                   echo "Error: There was a problem uploading your file. Please try again.";
                   return $this->redirect('maps/images', 308);
               }
           } else{
               echo "Error: " . $_FILES["photo"]["error"];
           }
        }
      }
    }
