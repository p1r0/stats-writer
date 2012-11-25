<?php
namespace Heapstersoft\Stats\Adapter;

/**
 * Description of StatsD
 *
 * @author Tabaré Caorsi <tabare@heapstersoft.com>
 */
class StatsD implements AdapterInterface
{
    protected $_host = '';
    protected $_port = '';
    protected $_keyTemplate = '';
    
    public function __construct($config)
    {
        $this->setHost($config['host']);
        $this->setPort($config['port']);
        $this->setKeyTemplate($config['key']);
    }
    
    public function getHost()
    {
        return $this->_host;
    }

    public function setHost($host)
    {
        $this->_host = $host;
    }

    public function getPort()
    {
        return $this->_port;
    }

    public function setPort($port)
    {
        $this->_port = $port;
    }

    public function getKeyTemplate()
    {
        return $this->_keyTemplate;
    }

    public function setKeyTemplate($keyTemplate)
    {
        $this->_keyTemplate = $keyTemplate;
    }

        
    public function decrement($key)
    {
        $this->_decrement($this->parseKey($key));
    }

    public function increment($key)
    {
        $this->_increment($this->parseKey($key));
    }

    protected function parseKey($key)
    {
        if($this->_keyTemplate == '')
        {
            return $key;
        }
        $keyTemplate = $this->_keyTemplate;
        if(strpos($keyTemplate, '#key#') === false)
        {
            //No key in template, append
            $keyTemplate .= '.#key#';
        }
        
        $placeHolders = array('#key#', '#host#');
        $replacers = array($key, $_SERVER['HTTP_HOST']);
        
        return str_replace($placeHolders, $replacers, $keyTemplate);
    }
    
    /**
     * Log timing information
     *
     * @param string $stats The metric to in log timing info for.
     * @param float $time The ellapsed time (ms) to log
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * */
    protected function timing($stat, $time, $sampleRate = 1)
    {
        $this->send(array($stat => "$time|ms"), $sampleRate);
    }

    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats The metric(s) to increment.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    protected function _increment($stats, $sampleRate = 1)
    {
        $this->updateStats($stats, 1, $sampleRate);
    }

    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats The metric(s) to decrement.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    protected function _decrement($stats, $sampleRate = 1)
    {
        $this->updateStats($stats, -1, $sampleRate);
    }

    /**
     * Updates one or more stats counters by arbitrary amounts.
     *
     * @param string|array $stats The metric(s) to update. Should be either a string or array of metrics.
     * @param int|1 $delta The amount to increment/decrement each metric by.
     * @param float|1 $sampleRate the rate (0-1) for sampling.
     * @return boolean
     * */
    protected function updateStats($stats, $delta = 1, $sampleRate = 1)
    {
        if (!is_array($stats))
        {
            $stats = array($stats);
        }
        $data = array();
        foreach ($stats as $stat)
        {
            $data[$stat] = "$delta|c";
        }

        $this->send($data, $sampleRate);
    }

    /*
     * Squirt the metrics over UDP
     * */

    protected function send($data, $sampleRate = 1)
    {
        // sampling
        $sampledData = array();

        if ($sampleRate < 1)
        {
            foreach ($data as $stat => $value)
            {
                if ((mt_rand() / mt_getrandmax()) <= $sampleRate)
                {
                    $sampledData[$stat] = "$value|@$sampleRate";
                }
            }
        } 
        else
        {
            $sampledData = $data;
        }

        if (empty($sampledData))
        {
            return;
        }

        // Wrap this in a try/catch - failures in any of this should be silently ignored
        try
        {
            $host = $this->_host;
            $port = $this->_port;
            $fp = fsockopen("udp://$host", $port, $errno, $errstr);
            if (!$fp)
            {
                return;
            }
            foreach ($sampledData as $stat => $value)
            {
                fwrite($fp, "$stat:$value");
            }
            fclose($fp);
        } 
        catch (Exception $e)
        {
        }
    }
}

?>
