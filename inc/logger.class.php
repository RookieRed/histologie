<?php
class Logger
{
    const LOG_DIR = '/logs/';

    private $_fileName;
    private $_logLevel;
    private $_levels = ['DEBUG' => 0, 'INFO' => 1, 'WARNING' => 2, 'ERROR' => 3];

   /**
    * Instancie un nouveau logger dans un répertoire et avec un niveau de log précis
    *
    * @param string $logLevel
    */
    public function __construct($logLevel)
    {
        $directory = $_SERVER['DOCUMENT_ROOT']. self::LOG_DIR;
        if (!is_dir($directory))
        {
            mkdir($directory, 0755, true);
        }
        $this->_fileName = $directory . "log_" . date("Y-m-d") . ".log";
        $this->_logLevel = $logLevel;
    }

   /**
    * Enregistre un évènement dans un fichier de log
    *
    * @param string $module Module depuis lequel l'évènement est enregistré
    * @param string $level Niveau de l'évènement, parmis DEBUG, INFO, WARNING et ERROR
    * @param string $message Message descriptif de l'évènement
    *
    * @return null
    */
    public function log($module, $level, $message)
    {
        if($this->_levels[$level] >= $this->_levels[$this->_logLevel])
        {
            $file = fopen($this->_fileName, "a");
            if($file === false)
                die("Failed to create or open file " . $this->_fileName);
            fwrite($file, date("H:i:s") . " " . $module . " " . $level . " : " . $message . "\n");
            fclose($file);
        }
    }
}
