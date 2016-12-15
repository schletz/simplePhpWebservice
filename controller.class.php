<?php
abstract class Controller {
    private $logfile = "log.txt";
    private $dbms = "sqlite";             // mysql für MySQL, sqlite für SQLite
    private $dbHost = "sensor.db";       // Servername bei MySQL, Dateiname bei SQLite
    private $dbName = "personendb";      // Nur für MySQL
    private $dbUser = "root";            // Nur für MySQL
    private $dbPass = "mysql";           // Nur für MySQL 
    
    /* Folgende Felder sind für die Implementierung eigener Controllerklassen interessant */
    protected $dbConn = null;           // für $this->dbConn->lastInsertId()
    
    public $getParams;                   // alle übergebenen GET Parameter
    public $postParams;                  // alle übergebenen POST Parameter (x-www-form-urlencoded)
        
    /* Defaultmethode, muss im eigenen Controller implementiert werden, */
    abstract public function get();

    /*
     * connectToDb
     * Verbindet sich zur Datenbank.
     */
    protected function connectToDb() {
        if ($this->dbms == "mysql") {
            $this->dbConn = new PDO("mysql:host={$this->dbHost};dbname={$this->dbName};charset=utf8",
                                      $this->dbUser, $this->dbPass);
        }
        if ($this->dbms == "sqlite") {
            $this->dbConn = new PDO("sqlite:{$this->dbHost}");
        }    
    }
    /*
     * writeLog
     * Schreibt eine Meldung mit einem Zeitstempel in eine Datei. Der Dateiname wird in der 
     * Membervariable $logfile angegeben.
     * @param {string} daten: Der String, der geschrieben werden soll.
     */
    protected function writeLog($daten) {
        file_put_contents($this->logfile, "\r\n".date("c")."\r\n", FILE_APPEND);
        if (is_array($daten))
            file_put_contents($this->logfile, print_r($daten,true), FILE_APPEND);
        else
            file_put_contents($this->logfile, $daten, FILE_APPEND);
    }
    
    /*
     * getData
     * Führt eine Abfrage in der Datenbank durch und liefert das Ergebnis als JSON zurück. 
     * @param {string} query Die SQL Abfrage, die ausgeführt werden soll. Parameter können als ? 
     * angegeben werden. Diese werden dann aus dem Array befüllt.
     * @example
     * $ctrl->getData("SELECT * FROM Personen WHERE P_ID = ? AND P_Vorname = ?", array(12, 'Max'))
     * @param {array} parameter Ein Array mit den zu befüllenden Parametern.
     * @returns Ein JSON Objekt mit allen Daten. Dieses Objekt ist ein JSON Array, und jeder 
     * Datensatz wird als JSON Objekt mit dem Namen der Spalte zurückgegeben.
     * [{id:12, name:"Mustermann", vorname:"max"],{...},{...},...]
     * @throws {Exception} Meldung mit der errorInfo aus der Datenbank, falls die Abfrage misslingt.
     */
    protected function getData($query, $param=array()) {
        if ($this->dbConn === null) $this->connectToDb();
        
        if (!is_array($param)) $param = array($param);
        $stmt = $this->dbConn->prepare($query);
        if ($stmt === false) {
            $err = $this->dbConn->errorInfo();
            throw new Exception("Datenbankfehler bei Prepare: {$err[2]}");
        }
        if ($stmt->execute($param) === false) {
            $err = $stmt->errorInfo();
            throw new Exception("Datenbankfehler bei der Abfrage: {$err[2]}");
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($rows);
    }
}

?>