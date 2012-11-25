<?php

namespace Heapstersoft\Stats\Adapter;
 
/**
 * Description of Interface
 *
 * @author Tabaré Caorsi <tabare@heapstersoft.com>
 */
interface AdapterInterface
{
    public function increment($key);
    public function decrement($key);
}
?>
