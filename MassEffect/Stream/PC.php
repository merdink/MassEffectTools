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
 * PC version of stream
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	stream
 */
class MassEffect_Stream_PC extends MassEffect_Stream {

    /**
     * Read 16-bit unsigned int from stream (2 bytes)
     *
     * @return	int
     */
    public function readU16()
    {
        $stream = unpack('v', fread($this->file, 2));

        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read unsigned 16bit integer');
        }

        return $stream[1];
    }

    /**
     * Read 32-bit unsigned int from stream (4 bytes)
     *
     * @return	int
     */
    public function readU32()
    {
        $stream = unpack('V', fread($this->file, 4));

        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read unsigned 32bit integer');
        }

        return $stream[1];
    }

    /**
     * Read 16-bit signed int from stream (2 bytes)
     *
     * @return	int
     */
    public function readS16()
    {
        if(!$this->bigEndian)
        {
            $stream = unpack('s', fread($this->file, 2));
        }
        else
        {
            $stream = unpack('s', $this->convertEndian(fread($this->file, 2)));
        }


        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read signed 16bit integer');
        }

        return $stream[1];
    }

    /**
     * Read 32-bit signed int from stream (4 bytes)
     *
     * @return	int
     */
    public function readS32()
    {
        if(!$this->bigEndian)
        {
            $stream = unpack('l', fread($this->file, 4));
        }
        else
        {
            $stream = unpack('l', $this->convertEndian(fread($this->file, 4)));
        }

        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read signed 32bit integer');
        }

        return $stream[1];
    }

    /**
     * Read boolean from stream (4 bytes)
     *
     * @return	bool
     */
    public function readBool()
    {
        return (($this->readU32() & 1) == 1) ? TRUE : FALSE;
    }

    /**
     * Read float from stream (4 bytes)
     *
     * @return	float
     */
    public function readFloat()
    {
        if(!$this->bigEndian)
        {
            $stream = unpack('f', fread($this->file, 4));
        }
        else
        {
            $stream = unpack('f', $this->convertEndian(fread($this->file, 4)));
        }

        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read float');
        }

        return $stream[1];
    }

    /**
     * Read string from stream
     *
     * @return	string
     */
    public function readString()
    {
        // Get string length
        $length = $this->readS32();

        // Empty string?
        if($length == 0)
        {
            return '';
        }

        // Unicode support
        if($length < 0)
        {
            $length = abs($length) * 2;
			
			$string = iconv('UTF-16LE', 'UTF-8//IGNORE', fread($this->file, $length));
			
			return $string;
			
			/*if ($length < 2 || $string == "\0\0")
			{
				return '';
			}
			else
			{
				if (substr($string, -2) == "\0\0")
				{
					return substr($string, 0, ($length - 2));
				}
			}*/
        }
		
		return fread($this->file, $length);
    }

    /**
     * Read GUID from stream (16 bytes)
     *
     * @return	MassEffect_Guid
     */
    public function readGuid()
    {
        return new MassEffect_Guid(fread($this->file, 16));
    }

    /**
     * Read 1 byte from stream
     *
     * @return	int
     */
    public function readByte()
    {
        $stream = unpack('C', fread($this->file, 1));

        if(empty($stream) OR !isset($stream[1]))
        {
            throw new MassEffect_Exception('Unable to read byte');
        }

        return $stream[1];
    }

    /**
     * Write 16-bit unsigned int (2 bytes)
     *
     * @param	int	$int
     */
    public function writeU16($int)
    {
        fwrite($this->file, pack('v', $int), 2);
    }

    /**
     * Write 32-bit unsigned int (4 bytes)
     *
     * @param	int	$int
     */
    public function writeU32($int)
    {
        fwrite($this->file, pack('V', $int), 4);
    }

    /**
     * Write 16-bit signed int (2 bytes)
     *
     * @return	int	$int
     */
    public function writeS16($int)
    {
        if(!$this->bigEndian)
        {
            fwrite($this->file, pack('s', $int), 2);
        }
        else
        {
            fwrite($this->file, $this->convertEndian(pack('s', $int)), 2);
        }
    }

    /**
     * Write 32-bit signed int (4 bytes)
     *
     * @param	int	$int
     */
    public function writeS32($int)
    {
        if(!$this->bigEndian)
        {
            fwrite($this->file, pack('l', $int), 4);
        }
        else
        {
            fwrite($this->file, $this->convertEndian(pack('l', $int)), 4);
        }
    }

    /**
     * Write boolean to stream (4 bytes)
     *
     * @param	bool	$bool
     */
    public function writeBool($bool)
    {
        $this->writeU32($bool ? 1 : 0);
    }

    /**
     * Write float to stream (4 bytes)
     *
     * @param	float	$float
     */
    public function writeFloat($float)
    {
        fwrite($this->file, (!$this->bigEndian ? pack('f', $float) : $this->convertEndian(pack('f', $float))), 4);
    }

    /**
     * Write string
     *
     * @param	string	$string
     */
    public function writeString($string)
    {
		// ASCII?
/*		if (!preg_match('/[^\x00-\x7F]/', $string))
		{
			// Write string length
			$this->writeS32(strlen($string) + 1);
			
			// Write string
			fwrite($this->file, $string."\0");
		}
		else
		{
			// Convert string
			$string = iconv('UTF-8', 'UTF-16BE//IGNORE', $string);
			
			// Length
			$length = -(strlen($string)) / 2;
			
			// Write string length
			$this->writeS32($length - 1);
			
			// Write string
			fwrite($this->file, $string."\0\0");
		}*/
		// ASCII?
		if (!preg_match('/[^\x00-\x7F]/', $string))
		{
			// Write string length
			$this->writeS32(strlen($string));
			
			// Write string
			fwrite($this->file, $string);
		}
		else
		{
			// Convert string
			$string = iconv('UTF-8', 'UTF-16LE//IGNORE', $string);
			
			// Length
			$length = -(strlen($string)) / 2;
			
			// Write string length
			$this->writeS32($length);
			
			// Write string
			fwrite($this->file, $string);
		}
    }

    /**
     * Write 16 bytes GUID to stream
     *
     * @param	MassEffect_Guid	$guid
     */
    public function writeGuid(MassEffect_Guid $guid)
    {
        fwrite($this->file, $guid->getGuid(), 16);
    }

    /**
     * Write 1 byte to stream
     *
     * @param	mixed	$byte
     */
    public function writeByte($byte)
    {
        fwrite($this->file, pack('C', $byte), 1);
    }

    /**
     * Write file checksum
     */
    public function writeChecksum()
    {
        // Rewind file
        rewind($this->file);

        // Contents
        $contents = '';

        // Iterate
        while(!feof($this->file))
        {
            // Read
            $contents .= fread($this->file, 1024);
        }

        // Big endian?
        if($this->bigEndian)
        {
            fwrite($this->file, strrev(hash('CRC32', $contents, TRUE)));
        }
        else
        {
            fwrite($this->file, hash('CRC32', $contents, TRUE));
        }
    }
}