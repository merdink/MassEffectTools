<?php
/**
 * Mass Effect 2 save reading and writing support
 *
 * This library is not associated in any way with EA or Bioware.
 *
 * Copyright (c) 2010, EpicLegion
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 * 1) Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2) Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * 3) Neither the name of the ME2SaveLib nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	object
 */

/**
 * Single object
 *
 * Ways to retrieve/set property:
 * <code>
 * $value = $object['propKey'];
 * $value = $object->propKey;
 * $value = $object->getProperty('propKey', FALSE); // Second param is default value
 * $value = $object->propKey();
 * $value = $object->getPropKey();
 *
 * $object['propKey'] = 'Value';
 * $object->propKey = 'Value';
 * $object->setProperty('propKey', 'Value');
 * </code>
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	object
 */
class MassEffect_Object implements ArrayAccess {

    /**
     * @var	array
     */
    protected $properties = array();

    /**
     * Object constructor
     *
     * @param	array	$array
     */
    public function __construct(array $array = array())
    {
        $this->fromArray($array);
    }

    /**
     * Default toString() handler
     *
     * I recommend overriding it...
     *
     * Format: MassEffect_Object_Example (property: Value; property2: Value)
     *
     * @return	string
     */
    public function __toString()
    {
        // Header
        $return = get_class($this).' (';

        // Iterate object properties
        foreach($this->properties as $k => $v)
        {
            // Prop key
            $return .= $k.':';

            // Value is boolean? Then convert it to string
            if(is_bool($v))
            {
                $return .= ($v) ? 'TRUE' : 'FALSE';
            }
            else
            {
                $return .= $v;
            }

            // Property end
            $return .= '; ';
        }

        // Return with footer
        return rtrim($return, '; ').')';
    }

    /**
     * Get magic method
     *
     * @param	string	$name	Var name
     * @return	mixed
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * Set magic method
     *
     * @param	string	$name	Var name
     * @param	mixed	$value	New value
     */
    public function __set($name, $value)
    {
        $this->setProperty($name, $value);
    }

    /**
     * Call method magic PHP5 method
     *
     * Supports:
     * - variableName()
     * - getVariableName()
     *
     * @param	string	$name		Called method name
     * @param	array	$arguments	Passed params
     */
    public function __call($name, $arguments = array())
    {
        // Property with this name exists?
        if(isset($this->properties[$name]))
        {
            return $this->getProperty($name);
        }

        // getVariableNameGoesHere method implementation
        if(substr($name, 0, 3) == 'get' AND strlen($name) >= 4)
        {
            // Remove 'get' prefix
            $name = substr($name, 3);

            if(isset($this->properties[$name]))
            {
                return $this->getProperty($name);
            }
            else
            {
                // Gotta lower first char
                $name = strtolower(substr($name, 0, 1)).substr($name, 1);

                return $this->getProperty($name);
            }
        }

        return NULL;
    }

    /**
     * Offset isset() checking
     *
     * Implementation of ArrayAccess method
     *
     * @param	string	$offset	Offset name
     */
    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    /**
     * Offset fetching
     *
     * Implementation of ArrayAccess method
     *
     * @param	string	$offset	Offset name
     * @return	mixed
     */
    public function offsetGet($offset)
    {
        return $this->getProperty($offset);
    }

    /**
     * Offset setting
     *
     * Implementation of ArrayAccess method
     *
     * @param	string	$offset	Offset name
     * @param	mixed	$value	New value
     */
    public function offsetSet($offset, $value)
    {
        $this->setProperty($offset, $value);
    }

    /**
     * Offset removing
     *
     * Implementation of ArrayAccess method
     *
     * @param	string	$offset	Offset name
     */
    public function offsetUnset($offset)
    {
        $this->deleteProperty($offset);
    }

    /**
     * Remove property
     *
     * @param	string	$name
     */
    public function deleteProperty($name)
    {
        if(isset($this->properties[$name]))
        {
            unset($this->properties[$name]);
        }
    }

    /**
     * Get class property
     *
     * @param	string	$name		Property name
     * @param	mixed	$default	Returned in case property does not exists
     * @return	mixed
     */
    public function getProperty($name, $default = NULL)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * Class property setting
     *
     * @param	string	$name	Var name
     * @param	mixed	$value	New value
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Return properties as array
     *
     * @return	array
     */
    public function asArray()
    {
        return $this->properties;
    }

    /**
     * Import properties from array
     *
     * @param	array	$array
     */
    public function fromArray(array $array)
    {
        foreach($array as $k => $v)
        {
            $this->setProperty($k, $v);
        }
    }
}