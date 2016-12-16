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
 * @subpackage	stream
 */

/**
 * Save stream reader/writer
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	stream
 */
abstract class MassEffect_Stream {

    /**
     * @var	bool
     */
    protected $bigEndian = FALSE;

    /**
     * @var	resource
     */
    protected $file = NULL;

    /**
     * Stream constructor
     *
     * Assigns file handle
     *
     * @param	resource	$fileHandle
     */
    public function __construct($fileHandle)
    {
        // File handle
        $this->file = $fileHandle;

        // Determine machine endian
        $this->bigEndian = (pack('l*', "\x01\x00\x00\x00") == 1) ? TRUE : FALSE;
    }

    /**
     * Destructor
     *
     * Closes file handle
     */
    public function __destruct()
    {
        // Close file handle
        fclose($this->file);
    }

    /**
     * Little and big endians converter
     *
     * Used by Xbox 360 stream
     *
     * @param	string	$var
     */
    public function convertEndian($var)
    {
        return strrev($var);
    }

    /**
     * Stream factory
     *
     * @param	resource	$fileHandle
     * @param	string		$type		Stream type (right now only PC and X360 accepted)
     */
    public static function factory($fileHandle, $type = 'X360')
    {
        if($type == 'X360')
        {
            return new MassEffect_Stream_X360($fileHandle);
        }
        else
        {
            return new MassEffect_Stream_PC($fileHandle);
        }
    }

    /**
     * Get file handle
     *
     * @return	resource
     */
    public function getHandle()
    {
        return $this->file;
    }

    /**
     * Read 16-bit unsigned int from stream (2 bytes)
     *
     * @return	int
     */
    abstract public function readU16();

    /**
     * Read 32-bit unsigned int from stream (4 bytes)
     *
     * @return	int
     */
    abstract public function readU32();

    /**
     * Read 16-bit signed int from stream (2 bytes)
     *
     * @return	int
     */
    abstract public function readS16();

    /**
     * Read 32-bit signed int from stream (4 bytes)
     *
     * @return	int
     */
    abstract public function readS32();

    /**
     * Read boolean from stream (4 bytes)
     *
     * @return	bool
     */
    abstract public function readBool();

    /**
     * Read float from stream (4 bytes)
     *
     * @return	float
     */
    abstract public function readFloat();

    /**
     * Read string from stream
     *
     * @return	string
     */
    abstract public function readString();

    /**
     * Read GUID from stream (16 bytes)
     *
     * @return	MassEffect_Guid
     */
    abstract public function readGuid();

    /**
     * Read 1 byte from stream
     *
     * @return	int
     */
    abstract public function readByte();

    /**
     * Write 16-bit unsigned int (2 bytes)
     *
     * @param	int	$int
     */
    abstract public function writeU16($int);

    /**
     * Write 32-bit unsigned int (4 bytes)
     *
     * @param	int	$int
     */
    abstract public function writeU32($int);

    /**
     * Write 16-bit signed int (2 bytes)
     *
     * @return	int	$int
     */
    abstract public function writeS16($int);

    /**
     * Write 32-bit signed int (4 bytes)
     *
     * @param	int	$int
     */
    abstract public function writeS32($int);

    /**
     * Write boolean to stream (4 bytes)
     *
     * @param	bool	$bool
     */
    abstract public function writeBool($bool);

    /**
     * Write float to stream (4 bytes)
     *
     * @param	float	$float
     */
    abstract public function writeFloat($float);

    /**
     * Write string
     *
     * @param	string	$string
     */
    abstract public function writeString($string);

    /**
     * Write 16 bytes GUID to stream
     *
     * @param	MassEffect_Guid	$guid
     */
    abstract public function writeGuid(MassEffect_Guid $guid);

    /**
     * Write 1 byte to stream
     *
     * @param	mixed	$byte
     */
    abstract public function writeByte($byte);

    /**
     * Write file checksum
     */
    abstract public function writeChecksum();
}