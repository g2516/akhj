<?php
/**
 * Auton kulujen seurantajärjestelmä
 *
 */

namespace Akhj\Util;

use Akhj\Exception\BaseException;
use Akhj\Service\Storage;
use RuntimeException;

/**
 * Logitustyökalu
 *
 * @package Akhj\Util
 * @author Mikko Rainio
 */
class Logger {

    /**
     * Set the maximum size that the output file is allowed to reach
     * before being rolled over to backup files.
     *
     * <p>You can specify the value with the suffixes "KB", "MB" or "GB"
     * so that the integer is interpreted being expressed respectively
     * in kilobytes, megabytes or gigabytes. For example, the value "10KB"
     * will be interpreted as 10240.</p>
     *
     * <p>The default maximum file size is 5MB.</p>
     *
     * <p>Note that <var>LOG_MAX_FILE_SIZE</var> cannot exceed <i>2 GB</i>.</p>
     *
     * @var String
     */
    const LOG_MAX_FILE_SIZE = "5MB";

    /**
     * Date format
     */
    const LOG_DATE_FORMAT = "Y-m-d H:i:s";

    /**
     * Jo alustetut loggerit taulukossa
     * @var array
     */
    private static $instances = array();

    /**
     * Lokitetaanko debug-viestit
     * @var boolean
     */
    private $debugEnabled = false;

    /**
     * Lokitiedoston nimi
     * @var String
     */
    private $logfile;

    /**
     * Lokitiedoston sijainti
     * @var String
     */
    private $logfilepath;

    /**
     * Tiedostonimi, joka tulostetaan joka lokirivin alkuun
     * @var String
     */
    private $tiedostonimi;

    /**
     * Muodostaa uuden Util_Logger-olion.
     *
     * <p>Käyttää asetuksia <keyb>['logger']['filename']</keyb> ja <keyb>['logger']['path']</keyb>.
     * Suoritus pysähtyy mikäli lokitiedoston kansiota ei ole olemassa tai se on 
     * kirjoitussuojattu</p>
     *
     * @throws RuntimeException Mikäli loki-hakemistoon tai -tiedostoon ei voi kirjoittaa tai lukea
     * @param String $tiedostonimi Tiedostonimi, joka tulostetaan joka lokirivin alkuun
     */
    private function __construct($tiedostonimi) {
        $this->tiedostonimi = $tiedostonimi;
        $this->debugEnabled = $this->isDebugEnabled();
        $conf = Storage::get(Storage::KEY_APP_CONFIG);
        $this->logfile = $conf['logger']['filename'];
        $this->logfilepath = $conf['logger']['path'];
        if (StringHelper::isBlank($this->logfilepath)) {
            throw new RuntimeException("Lokikansiota '". $this->logfilepath . "' ei ole olemassa!", BaseException::DISABLE_LOGGING);
        }
        if (!is_dir($this->logfilepath)) {
            throw new RuntimeException("Lokikansiota '". $this->logfilepath . "' ei ole olemassa!", BaseException::DISABLE_LOGGING);
        }
        if (!is_writable($this->logfilepath)) {
            throw new RuntimeException("Lokikansioon '". $this->logfilepath . "' ei voi kirjoittaa!", BaseException::DISABLE_LOGGING);
        }
        if (!file_exists($this->logfilepath . $this->logfile)) {
            @touch($this->logfilepath . $this->logfile);
            @chmod($this->logfilepath . $this->logfile, 0766);
        }
    }

    /**
     * Palauttaa tiedon, onko debuggaus päällä
     * 
     * @return boolean
     */
    public function isDebugEnabled() {
        $conf = Storage::get(Storage::KEY_APP_CONFIG);
        return isset($conf['logger']['debug']) && $conf['logger']['debug'] === true;
    }

    /**
     * Palauttaa halutun tiedoston loggerin.
     * @param String $tiedostonimi
     * @return \Akhj\Util\Logger
     */
    public static function getInstance($tiedostonimi) {
        if (!isset(self::$instances[$tiedostonimi])) {
            $logger = new Logger($tiedostonimi);
            self::$instances[$tiedostonimi] = $logger;
        }
        return self::$instances[$tiedostonimi];
    }

    /**
     * Writes DEBUG-level message to log file if debug is enabled.
     *
     * @param mixed $logMsg Log row
     * @param String $line [Optional] Line number
     */
    public function debug($logMsg,$line='') {
        if ($this->isDebugEnabled()) {
            $this->logRow($logMsg,'DEBUG',$line);
        }
    }

    /**
     * Writes WARNING-level message to log file.
     *
     * @param mixed $logMsg    Log row
     * @param String $line      [Optional]. Line number
     */
    public function warn($logMsg,$line='') {
        $this->logRow($logMsg,'WARNING',$line);
    }

    /**
     * Writes ERROR-level message to log file.
     *
     * @param mixed $logMsg    Log row
     * @param String $line      [Optional]. Line number
     */
    public function error($logMsg,$line='') {
        $this->logRow($logMsg,'ERROR',$line);
    }

    /**
     * Writes INFO-level message to log file.
     *
     * @param mixed $logMsg    Log row
     * @param String $line      [Optional]. Line number
     */
    public function info($logMsg,$line='') {
        $this->logRow($logMsg, 'INFO', $line);
    }


    /**
     * Return formatted actual date.
     *
     * @param   String $pattern [Optional]. Date pattern. Default is 'Y-m-d H:i:s'
     * @return  String          Actual date.
     */
    private function get_formatted_date($pattern = ''){
        if($pattern == '') {
            $pattern = Logger::LOG_DATE_FORMAT;
        }
        return date($pattern);
    }

    /**
     * Constructs the log row.
     *
     * @param String $logMsg    Log row
     * @param String $level     Message log-level ('INFO','DEBUG','WARNING','ERROR')
     * @param String $line      [Optional]. Line number
     */
    private function logRow($logMsg,$level,$line=''){
        $logRow = $this->get_formatted_date() . ' ' . $level . ' ' . $this->tiedostonimi;
        if(!StringHelper::isBlank($line)) {
            $logRow .= '('.$line.')';
        }
        $logRow .= ': ' . $this->msgToString($logMsg) . "\n";
        $this->writeLog($logRow);

    }

    /**
     * Converts $arg to string. Can handle objects, arrays, multidimensional
     * arrays etc...
     *
     * @param mixed     $arg    Convertable input
     * @param int $level
     * @return String           Converted string
     */
    private function msgToString($arg, $level=0) {
        $retMsg = '';
        if(is_array($arg)) {
            $retMsg .= str_repeat("\t", $level) . "Array (\n";
            foreach($arg as $key => $value) {
                $retMsg .= str_repeat("\t", $level) . "\t[".$key.'] => ' . trim($this->msgToString($value, $level+1)) . "\n";
            }
            $retMsg .= str_repeat("\t", $level) . ")";
        }
        elseif( is_object($arg) ) {
            if ($arg instanceof \Exception) {
                $retMsg .= str_repeat("\t", $level) . $arg->getMessage() . "\n";
                $retMsg .= "\n" . $arg->getTraceAsString() . "\n";
            } else {
                $retMsg .= print_r($arg, true);
                $retMsg .= str_repeat("\t", $level) . ")";
            }
        }
        elseif( is_bool($arg) ) {
            $retMsg .= str_repeat("\t", $level) . ($arg ? 'True' : 'False');
        }
        elseif( $arg == NULL ) {
            $retMsg .= str_repeat("\t", $level) . 'NULL';
        }
        else {
            $retMsg .= str_repeat("\t", $level) . "$arg";
        }
        return $retMsg . ($level == 0 ? "" : "\n");
    }

    /**
     * Writes String in to log file.
     *
     * @param String $logString Log row
     */
    private function writeLog($logString){
        $logFile = $this->logfilepath . $this->logfile;

        //write to log file
        $fp = @fopen($logFile, "a");
        if(@fwrite($fp,$logString)===FALSE){
            die("Could not write to LOG file: " . $logFile);
        }
        @fclose($fp);

        if(filesize($logFile) >= $this->getMaxFileSizeInBytes()) {
            $newFile = $this->logfilepath . date("YmdHis") . '_' . $this->logfile;
            @rename( $logFile, $newFile );
        }
    }

    /**
     * Converts property <var>{@link Logger::LOG_MAX_FILE_SIZE}</var> to bytes.
     *
     * @return int {@link Logger::LOG_MAX_FILE_SIZE} in bytes
     */
    private function getMaxFileSizeInBytes() {
        $value = Logger::LOG_MAX_FILE_SIZE;
        $maxFileSize = NULL;
        $numpart = substr($value, 0, strlen($value)-2);
        $suffix  = strtoupper(substr($value, -2));

        switch ($suffix) {
            case 'KB': $maxFileSize = (int)((int)$numpart * 1024); break;
            case 'MB': $maxFileSize = (int)((int)$numpart * 1024 * 1024); break;
            case 'GB': $maxFileSize = (int)((int)$numpart * 1024 * 1024 * 1024); break;
            default:
                if (is_numeric($value)) {
                    $maxFileSize = (int)$value;
                }
        }

        if ($maxFileSize === null) {
            // if something fails, return default value (5242880 = 5MB)
            return 5242880;
        } else {
            return abs($maxFileSize);
        }
    }
}

