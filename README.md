# simplePhpWebservice
Implementierung eines einfachen "Webservices" in PHP. Eigentlich ist es eine Webseite, die JSON Daten liefert. Wer ein "echtes" REST Webservice mit PUT/DELETE Requests und/oder weiterführenden HTTP Funktionen erstellen möchte, soll auf besser geeignete Technologien wie Tomcat (Java), Microsoft IIS (C#) oder Node JS setzen!

##Beispielaufruf
In diesem Werbservice wurde ein Beispielcontroller - der Controller Sensor - implementiert. Somit können als Beispiel mit
<code>?controller=Sensor&method=getValues&id=1</code>
alle Daten des Sensors 1 als JSON ausgegeben werden. Die Datenbank sensor.db wird als Muster mitgeliefert.

##Erweiterung
Ein Controller wird von Controller geerbt und muss mindestens die Methode <code>get()</code> implementieren:
```php
class SensorController extends Controller {    
    public function get()
    {
        /* Liefert alle übergebenen GET Parameter zum Testen */
        return json_encode($this->getParams);
        /* ODER */
        /* Liefert alle übergebenen POST Parameter zum Testen */
        return json_encode($this->postParams);        
    }
}
```
Die Methode get wird aufgerufen, wenn kein GET Parameter method übergeben wurde. Um auf den Request
<code>?controller=Sensor&method=getValues&id=1</code>
reagieren zu können, muss die methode <code>getValues()</code> implementiert werden. Den Zugriff auf die anderen Parameter erhält man mit $this->getParams bzw. $this->postParams.

Danach muss in der index.php das require_once entsprechend angepasst werden und in der controller.class.php die Datenbankverbindung entsprechend gesetzt werden.
