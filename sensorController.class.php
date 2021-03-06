<?php
require_once("controller.class.php");

/*
 * Musterimplementierung einer Controllerklasse. Diese Klasse reagiert auf Requests der Form 
 * ?controller=Sensor: dies ruft die Methode get auf
 * ?controller=Sensor&method=getValues&id=2: dies ruft getValues auf und schreibt die übergebene
 * id in $getParams.
 */
class SensorController extends Controller {    
    /* Wird der Controller ohne Methodennamen aufgerufen, geben wir alle Sensor-IDs zurück, die in 
     * der Datenbank sind. 
     */
    public function get()
    {
        $data = $this->getData("SELECT DISTINCT V_Sensor FROM tValues");
        return $data;   // Wichtig, sonst wird nichts ausgegeben!
    }
    
    /* Liefert alle Daten des Sensors, der mit dem GET Parameter id übergeben wurde.
     * Requestbeispiel: ?controller=Sensor&method=getValues&id=2 */    
    public function getValues() {
        $data = $this->getData("SELECT V_Timestamp, V_Value FROM tValues WHERE V_Sensor = ? ORDER BY V_Timestamp", $this->getParams["id"]);
        return $data;   // Wichtig, sonst wird nichts ausgegeben!
    }

    /*
     * Trägt die Daten eines Sensors ein. Beispiel für den Request:
     * GET Request: ?controller=Sensor&method=setValues&id=2
     * POST Daten: timestamp, value */
    public function setValues() 
    {
         $this->getData("INSERT INTO tValues (V_Sensor, V_Timestamp, V_Value) VALUES (?, ?, ?)",
            array($this->getParams["id"],$this->postParams["timestamp"],$this->postParams["value"]));
         $data = $this->getData("SELECT * FROM tValues WHERE V_ID = ?", $this->dbConn->lastInsertId());
            
         return $data;   // Wichtig, sonst wird nichts ausgegeben!

    }
}
