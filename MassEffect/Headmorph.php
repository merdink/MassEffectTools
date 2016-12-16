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
 * @author      EpicLegion                                          <me2.legion@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @copyright   EpicLegion (c) 2010
 * @package     me2save
 * @subpackage  core
 */

/**
 * Main save object
 *
 * @author      EpicLegion                                          <me2.legion@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @copyright   EpicLegion (c) 2010
 * @package     me2save
 * @subpackage  core
 */
class MassEffect_Headmorph implements ArrayAccess {

    /**
     * @var string
     */
    protected $filename = '';

    /**
     * @var int|bool
     */
    protected $forceVersion = 29;

    /**
     * @var string
     */
    const GIBBED_MAGIC = 'GIBBEDMASSEFFECT2HEADMORPH';

    /**
     * @var array
     */
    protected $properties = array();

    /**
     * @var MassEffect_Stream
     */
    protected $stream = NULL;

    /**
     * Save object constructor
     *
     * @param   string                  $filename       Location of save file
     * @param   int|bool                $forceVersion   Force specified save version
     * @throws  MassEffect_Exception
     */
    public function __construct($filename = '', $forceVersion = 29)
    {
        // Version check
        $this->forceVersion = $forceVersion;

        if(!$filename) return;

        // Make sure save file exists
        if(!file_exists($filename))
        {
            throw new MassEffect_Exception('Save file not found');
        }

        // Set filename (in case we need it later)
        $this->filename = $filename;

        // Open file
        $file = fopen($filename, 'rb');

        // Read magic
        if(fread($file, strlen(self::GIBBED_MAGIC)) != self::GIBBED_MAGIC)
        {
            throw new MassEffect_Exception('Invalid file format (no header)');
        }

        // Zero?
        if(fread($file, 1) != "\x00")
        {
            throw new MassEffect_Exception('Invalid file format (no zero after header)');
        }

        // Version
        $version = unpack('V', fread($file, 4));

        // Check
        if(empty($version) OR !isset($version[1]))
        {
            throw new MassEffect_Exception('Invalid file format (corrupted version)');
        }

        // Set version
        $version = $version[1];

        // Determine type
        if($version == 29)
        {
            $this->stream = MassEffect_Stream::factory($file, 'PC');
        }
        elseif($version == 486539264)
        {
            $this->stream = MassEffect_Stream::factory($file, 'X360');
        }
        else
        {
            throw new MassEffect_Exception('Invalid file format (unknown version, '.$version.')');
        }

        // Start reading save file data
        $this->startReading();

        // Stats
        $stats = fstat($this->stream->getHandle());

        // Validate
        if(ftell($this->stream->getHandle()) != $stats['size'])
        {
            throw new MassEffect_Exception('File is broken, not consumed');
        }
    }

    /**
     * Get save file stream
     *
     * @return  MassEffect_Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Save changes
     *
     * @param   string  $filename
     * @param   string  format
     */
    public function save($filename = NULL, $format = 'pc')
    {
        // Overwrite
        if(!$filename)
        {
            $filename = $this->filename;
        }

        // Determine save type (PC saves have .pcsav extension, Xbox ones have .xbsav)
        if($format == 'pc')
        {
            $this->stream = MassEffect_Stream::factory(fopen($filename, 'w+b'), 'PC');
        }
        else
        {
            $this->stream = MassEffect_Stream::factory(fopen($filename, 'w+b'), 'X360');
        }

        // Start writing
        $this->startWriting();
    }

    /**
     * Save file reading process
     *
     * @throws  MassEffect_Exception
     */
    protected function startReading()
    {
        // Shortcut
        $stream = $this->stream;

        // Morph head object
        $this->morphHead = new MassEffect_Object_Player_MorphHead;

        // Hair
        $this->morphHead->hairMesh = $stream->readString();

        // Accesories
        $this->morphHead->accessoryMeshes = new MassEffect_Interface_Iterator_String($stream);

        // Some stuff i do not understand
        $this->morphHead->morphFeatures = new MassEffect_Interface_Object_MorphFeature($stream);
        $this->morphHead->offsetBones = new MassEffect_Interface_Object_OffsetBone($stream);
        $this->morphHead->lod0Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->morphHead->lod1Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->morphHead->lod2Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->morphHead->lod3Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->morphHead->scalarParameters = new MassEffect_Interface_Object_ScalarParameter($stream);
        $this->morphHead->vectorParameters = new MassEffect_Interface_Object_VectorParameter($stream);
        $this->morphHead->textureParameters = new MassEffect_Interface_Object_TextureParameter($stream);
    }

    /**
     * Save file writing process
     *
     * @throws  MassEffect_Exception
     */
    protected function startWriting()
    {
        // Write magic
        fwrite($this->stream->getHandle(), self::GIBBED_MAGIC);

        // Write zero
        fwrite($this->stream->getHandle(), "\x00");

        // Write version
        $this->stream->writeU32(29);

        // Shortcut
        $stream = $this->stream;

        // Hair
        $stream->writeString($this->morphHead->hairMesh);

        // Accesories
        $this->morphHead->accessoryMeshes->write($stream);

        // Some stuff i do not understand
        $this->morphHead->morphFeatures->write($stream);
        $this->morphHead->offsetBones->write($stream);
        $this->morphHead->lod0Verticles->write($stream);
        $this->morphHead->lod1Verticles->write($stream);
        $this->morphHead->lod2Verticles->write($stream);
        $this->morphHead->lod3Verticles->write($stream);
        $this->morphHead->scalarParameters->write($stream);
        $this->morphHead->vectorParameters->write($stream);
        $this->morphHead->textureParameters->write($stream);
    }

    /**
     * Get magic method
     *
     * @param   string  $name   Var name
     * @return  mixed
     */
    public function __get($name)
    {
        return $this->getProperty($name);
    }

    /**
     * Set magic method
     *
     * @param   string  $name   Var name
     * @param   mixed   $value  New value
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
     * @param   string  $name       Called method name
     * @param   array   $arguments  Passed params
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
     * @param   string  $offset Offset name
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
     * @param   string  $offset Offset name
     * @return  mixed
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
     * @param   string  $offset Offset name
     * @param   mixed   $value  New value
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
     * Not supported by this object
     *
     * @param   string  $offset Offset name
     */
    public function offsetUnset($offset)
    {

    }

    /**
     * Get class property
     *
     * @param   string  $name       Property name
     * @param   mixed   $default    Returned in case property does not exists
     * @return  mixed
     */
    public function getProperty($name, $default = NULL)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : $default;
    }

    /**
     * Class property setting
     *
     * @param   string  $name   Var name
     * @param   mixed   $value  New value
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Return properties as array
     *
     * @return  array
     */
    public function asArray()
    {
        return $this->properties;
    }

    /**
     * Import properties from array
     *
     * @param   array   $array
     */
    public function fromArray(array $array)
    {
        foreach($array as $k => $v)
        {
            $this->setProperty($k, $v);
        }
    }
}