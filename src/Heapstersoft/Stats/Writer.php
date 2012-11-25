<?php
namespace Heapstersoft\Stats;
use Heapstersoft\Stats\Config\LoaderInterface;
/**
 * Description of Writer
 *
 * @author Tabaré Caorsi <tabare@heapstersoft.com>
 */
class Writer
{
    /**
     *
     * @var \Heapstersoft\Stats\Connector\AdapterInterface
     */
    protected $adapter = null;
    
    /**
     *
     * @var \Heapstersoft\Stats\Config\LoaderInterface
     */
    protected $configLoader = null;
    
    public function __construct($configFile, 
               \Heapstersoft\Stats\Config\LoaderInterface $configLoader = null)
    {
        if($configLoader == null)
        {
            $this->setConfigLoader(new \Heapstersoft\Stats\Config\YamlLoader());
        }
        
        $conf = $this->configLoader->load($configFile);
        
        $this->adapter = new $conf['Adapter']['class']($conf['Adapter']);
    }
    
    public function increment($key)
    {
        $this->adapter->increment($key);
    }
    
    public function decrement($key)
    {
        $this->adapter->decrement($key);
    }
    
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(\Heapstersoft\Stats\Connector\AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getConfigLoader()
    {
        return $this->configLoader;
    }

    public function setConfigLoader(\Heapstersoft\Stats\Config\LoaderInterface $confiLoader)
    {
        $this->configLoader = $confiLoader;
    }


}

?>
