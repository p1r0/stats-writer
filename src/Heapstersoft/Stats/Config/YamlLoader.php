<?php

namespace Heapstersoft\Stats\Config;

use Symfony\Component\Yaml\Parser;
/**
 * Description of YamlLoader
 *
 * @author Tabaré Caorsi <tabare@heapstersoft.com>
 */
class YamlLoader implements LoaderInterface
{
    public function load($configFile)
    {
        $yaml = new Parser();

        $value = $yaml->parse(file_get_contents($configFile));
        
        return $value;
    }
}

?>
