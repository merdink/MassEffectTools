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
 * Iterable interface
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
abstract class MassEffect_Interface_Iterator extends MassEffect_Interface implements Iterator, Countable {

    /**
     * @var int
     */
    protected $pointer = 0;

    /**
     * @var	array
     */
    protected $records = array();

    /**
     * Convert object into string
     *
     * @return	string
     */
    public function __toString()
    {
        // Header
        $return = '(';

        // Iterate records
        foreach($this->records as $v)
        {
            if(is_bool($v))
            {
                $return .= ($v) ? 'TRUE' : 'FALSE';
            }
            else
            {
                $return .= $v;
            }

            // Delimiter
            $return .= '; ';
        }

        // Done
        return rtrim($return, '; ').')';
    }
	
    /**
     * Increase internal pointer
     *
     * @see Iterator::next()
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * Iterator rewind
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * Validate pointer position
     *
     * @see		Iterator::valid()
     * @return	bool
     */
    public function valid()
    {
        return ($this->pointer < count($this->records));
    }

    /**
     * Get current pointer position
     *
     * @see		Iterator::key()
     * @return	int
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * Get current value
     *
     * @see		Iterator::current()
     * @return	mixed
     */
    public function current()
    {
        return $this->records[$this->pointer];
    }

    /**
     * Handle save array element
     *
     * @param	int					$i
     * @param	MassEffect_Stream	$stream
     */
    abstract protected function handleItem($i, MassEffect_Stream $stream);

    /**
     * Write record
     *
     * @param	mixed				$record
     * @param	MassEffect_Stream	$stream
     */
    abstract protected function writeItem($record, MassEffect_Stream $stream);

    /**
     * Return as array
	 *
	 * @return	array
     */
    public function asArray()
    {
        return $this->records;
    }

    /**
     * Items count
     *
     * @return	int
     */
    public function count()
    {
        return count($this->records);
    }

    /**
     * Set elements from array
     *
     * @param	array	$array
     */
    public function fromArray(array $array)
    {
        $this->records = $array;
    }

    /**
     * Read Unreal array
     *
     * @throws	MassEffect_Exception
     * @see		MassEffect/MassEffect_Interface::read()
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
            // Execute item handler
            $this->handleItem($i, $stream);
        }
    }

    /**
     * Write Unreal array
     *
     * @see		MassEffect/MassEffect_Interface::write()
     */
    public function write(MassEffect_Stream $stream)
    {
        // Write items count
        $stream->writeU32(count($this->records));

        // Iterate
        foreach($this->records as $rec)
        {
            // Execute item handler
            $this->writeItem($rec, $stream);
        }
    }
}