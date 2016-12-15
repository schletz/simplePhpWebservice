<?php
/* 
 * Webseite für AJAX Requests
 * Eine allgemeine Vorlage für Requests der Form
 * http://localhost/ajax.php?controller=controllername&method=methodenname
 */

error_reporting(E_ALL ^ E_NOTICE);
/* Durch den eigenen Controller zu ersetzen */
require_once("sensorController.class.php");

header('Content-type: application/json; charset=utf-8');
  
try {
    if (isset($_GET['controller']) && preg_match("/[A-Za-z][A-Za-z0-9_]+/",$_GET['controller']))
        $action = $_GET['controller'];
    else
        throw new Exception("Der Parameter controller wurde nicht angegeben oder ist ungültig.");

    /* Instanziert den Controller mit folgendem Namen: {controller}Controller, also z. B. 
     * SchuelerController, wenn controller=Schueler ist. */
    $controllerName = $action."Controller";
    $ctrl = new $controllerName ();
    $ctrl->getParams = $_GET;
    $ctrl->postParams = $_POST;
    
    /* Wurde ein Methodenname übergeben? Wenn ja, wird diese Methode aufgerufen. Falls nicht, wird
     * die Methode get() aufgerufen. */
    if (isset($_GET['method']) && preg_match("/[A-Za-z][A-Za-z0-9]+/",$_GET['method'])) {
        $methodName = $_GET['method'];
    }
    else {
        $methodName = "get";
    }
    
    echo $ctrl->$methodName();
}

catch (Exception $err) {
    file_put_contents("error.txt", $err->getMessage()."\r\n", FILE_APPEND);
    echo json_encode(array('error' => $err->getMessage()), JSON_UNESCAPED_UNICODE);
}

?>