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
 * @subpackage	enum
 */

/**
 * Enumeration object
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	enum
 */
abstract class MassEffect_Enum {

    /**
     * @var	mixed
     */
    protected $value = NULL;

    /**
     * Enum constructor
     *
     * Sets value
     *
     * @param	mixed	$value	Enum value
     */
    public function __construct($value = 0)
    {
        $this->setValue($value);
    }

    /**
     * Support for echo'ing enum
     *
     * @return	string
     */
    public function __toString()
    {
        return $this->getString();
    }

    /**
     * Get enum string representation
     *
     * @return	string
     */
    public function getString()
    {
        // Get available enum values
        $enumValues = $this->getValues();

        // Get array val
        return $enumValues[$this->value];
    }

    /**
     * Get enum
     *
     * @return	mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set current enum value
     *
     * @param	mixed					$value
     * @throws	MassEffect_Exception
     */
    public function setValue($value)
    {
        // Get available values
        $enumValues = $this->getValues();

        // Valid value?
        if(!isset($enumValues[$value]))
        {
            throw new MassEffect_Exception('Invalid enum value');
        }

        $this->value = $value;
    }

    /**
     * Get allowed enum values
     *
     * @return	array
     */
    abstract public function getValues();
}