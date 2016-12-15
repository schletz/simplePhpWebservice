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
    if (isset($_GET['controller']) && preg_match("/[A-Za-z][A-Za-z0-9_]+/",$_GET['controller'])) {
        $controllerName = $_GET['controller']."Controller";
    }
    else {
        throw new Exception("Der Parameter controller wurde nicht angegeben oder ist ungültig.");
    }
    /* Instanziert den Controller mit folgendem Namen: {controller}Controller, also z. B. 
     * SchuelerController, wenn controller=Schueler ist. */
    if (!class_exists($controllerName)) {
        throw new Exception("Die Klasse {$controllerName} existiert nicht.");
    }

    $ctrl = new $controllerName ();
    $ctrl->getParams = $_GET;
    $ctrl->postParams = $_POST;
    
    /* Wurde ein Methodenname übergeben? Wenn ja, wird diese Methode aufgerufen. Falls nicht, wird
     * die Methode get() aufgerufen. */
    if (isset($_GET['method']) && preg_match("/[A-Za-z][A-Za-z0-9_]+/",$_GET['method'])) {
        $methodName = $_GET['method'];
    }
    else {
        $methodName = "get";
    }

    if (!method_exists($ctrl, $methodName)) {
        throw new Exception("Die Methode {$controllerName}::{$methodName} existiert nicht.");
    }
    
    /* Methode aufrufen und die JSON Ausgabe 1:1 ausgeben */
    $data = $ctrl->$methodName();
    if ($data === null) {
        throw new Exception("Die Methode {$controllerName}::{$methodName} liefert keinen Wert zurück.");
    }
    echo $data;
}

catch (Exception $err) {
    file_put_contents("error.txt", $err->getMessage()."\r\n", FILE_APPEND);
    echo json_encode(array('error' => $err->getMessage()), JSON_UNESCAPED_UNICODE);
}

?>