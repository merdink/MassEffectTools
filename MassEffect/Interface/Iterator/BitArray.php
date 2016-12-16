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
 * @subpackage	interface
 */

/**
 * Bit array PHP support
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_Iterator_BitArray extends MassEffect_Interface {

    /**
     * @var	array
     */
    protected $bitArray = array();

    /**
     * Return info about bit array
     *
     * @return	string
     */
    public function __toString()
    {
        return 'Bit array with '.count($this->bitArray).' bits';
    }

    /**
     * Return as array
     *
     * @return	array
     */
    public function asArray()
    {
        return $this->bitArray;
    }

    /**
     * Set from array
     *
     * @param	array	$array
     */
    public function fromArray(array $array)
    {
        $this->bitArray = $array;
    }

    /**
     * Get bit
     *
     * @param	int		$bit
     * @return	bool
     */
    public function getBit($bit)
    {
        return isset($this->bitArray[$bit]) ? $this->bitArray[$bit] : NULL;
    }

    /**
     * Set bit
     *
     * @param	int		$bit
     * @param	bool	$value
     */
    public function setBit($bit, $value)
    {
        $this->bitArray[$bit] = $value;
    }

    /**
     * Read bit array
     *
     * @throws	MassEffect_Exception
     */
    public function read(MassEffect_Stream $stream)
    {
        // Read items count
        $count = $stream->readU32();

        // Validate
        if($count >= 0x7FFFFF)
        {
            throw new MassEffect_Exception('Invalid collection items count');
        }

        // Iterate
        for($i = 0; $i < $count; $i++)
        {
            // Get bit offset
            $offset = $i * 32;

            // Get 4 bytes
            $value = $stream->readS32();

            // S32 = 4 bytes; 1 byte = 8 bits; 4 * 8 = 32
            for($bit = 0; $bit < 32; $bit++)
            {
                $this->bitArray[($offset + $bit)] = (($value & (1 << $bit)) != 0);
            }
        }
    }

    /**
     * Write bit array
     *
     * @param	MassEffect_Stream	$stream
     */
    public function write(MassEffect_Stream $stream)
    {
        // Array count
        $count2 = count($this->bitArray);

        // Read items count
        $count = ($count2 + 31) / 32;

        // Write count
        $stream->writeU32($count);

        // Iterate
        for($i = 0; $i < ($count - 1); $i++)
        {
            // Get bit offset
            $offset = $i * 32;

            // Value
            $value = 0;

            // Get byte
            for($bit = 0; $bit < 32 AND ($offset + $bit < $count2); $bit++)
            {
                $value |= ($this->getBit($offset + $bit) ? 1 : 0) << $bit;
            }

            // Write byte
            $stream->writeS32($value);
        }
    }
}