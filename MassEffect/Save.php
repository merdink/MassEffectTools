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
 * @subpackage	core
 */

/**
 * Main save object
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	core
 */
class MassEffect_Save implements ArrayAccess {

    /**
     * @var	string
     */
    protected $filename = '';

    /**
     * @var	int|bool
     */
    protected $forceVersion = 29;

    /**
     * @var	bool
     */
    static protected $isInit = FALSE;

    /**
     * @var	array
     */
    protected $properties = array();

    /**
     * @var	MassEffect_Stream
     */
    protected $stream = NULL;

    /**
     * Save object constructor
     *
     * @param	string					$filename		Location of save file
     * @param	int|bool				$forceVersion	Force specified save version
     * @throws	MassEffect_Exception
     */
    public function __construct($filename, $forceVersion = 29)
    {
        // Class init
        if(!self::$isInit)
        {
            // Register our own classes autoloader
            spl_autoload_register(array('MassEffect_Save', 'autoload'));

            // Prevent double autoloader register
            self::$isInit = TRUE;
        }

        // Version check
        $this->forceVersion = $forceVersion;

        // Make sure save file exists
        if(!file_exists($filename))
        {
            throw new MassEffect_Exception('Save file not found');
        }

        // Set filename (in case we need it later)
        $this->filename = $filename;

        // Determine save type (PC saves have .pcsav extension, Xbox ones have .xbsav)
        if(strrchr($filename, '.') == '.pcsav')
        {
            $this->stream = MassEffect_Stream::factory(fopen($filename, 'rb'), 'PC');
        }
        else
        {
            $this->stream = MassEffect_Stream::factory(fopen($filename, 'rb'), 'X360');
        }

        // Start reading save file data
        $this->startReading();

        // Stats
        $stats = fstat($this->stream->getHandle());

        // Validate
        if(ftell($this->stream->getHandle()) != $stats['size'])
        {
            throw new MassEffect_Exception('Save file is broken, not consumed');
        }
    }

    /**
     * Class autoloader
     *
     * @param	string	$class	Class name
     */
    public static function autoload($class)
    {
        // Our class?
        if(substr($class, 0, 10) != 'MassEffect')
        {
            return;
        }

        // Transform class name into path
        $class = str_replace('_', '/', $class).'.php';

        // Load it
        require_once MASSEFFECT_PATH.$class;
    }

    /**
     * Get save file stream
     *
     * @return	MassEffect_Stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Save changes
     *
     * @param	string	$filename
     */
    public function save($filename = NULL)
    {
        // Overwrite
        if(!$filename)
        {
            $filename = $this->filename;
        }

        // Determine save type (PC saves have .pcsav extension, Xbox ones have .xbsav)
        if(strrchr($filename, '.') == '.pcsav')
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
     * @throws	MassEffect_Exception
     */
    protected function startReading()
    {
        // Save file version, for ME2 it's 29 (or it should be...)
        $this->properties['version'] = $this->stream->readU32();

        // Version check
        if($this->properties['version'] != $this->forceVersion AND $this->forceVersion != FALSE)
        {
            throw new MassEffect_Exception('Invalid save version');
        }

        // No idea what this variable is for (empty in my saves)
        $this->properties['debugName'] = $this->stream->readString();

        // Seconds played by player (no-life level meter)
        $this->properties['secondsPlayed'] = $this->stream->readFloat();

        // Anyone knows what this var is for?
        $this->properties['disc'] = $this->stream->readS32();

        // Current map
        $this->properties['baseLevelName'] = $this->stream->readString();

        // Difficulty level
        $this->properties['difficulty'] = new MassEffect_Enum_Difficulty($this->stream->readByte());

        // End game state (game not finished, shepard dead or shepard alive)
        $this->properties['endGameState'] = new MassEffect_Enum_EndGameState($this->stream->readS32());

        // Save file timestamp (year, month, day and time in seconds)
        $this->properties['timestamp'] = new MassEffect_Interface_Timestamp($this->stream);

        // Shepard location
        $this->properties['location'] = new MassEffect_Object_Vector($this->stream->readFloat(), $this->stream->readFloat(), $this->stream->readFloat()); // vector of save location

        // Shepard rotation
        $this->properties['rotation'] = new MassEffect_Object_Rotation($this->stream->readS32(), $this->stream->readS32(), $this->stream->readS32()); // shepard rotation

        // Number of current loading tip
        $this->properties['currentLoadingTip'] = $this->stream->readS32();

        // Level records (name, visible, loaded)
        $this->properties['levelRecords'] = new MassEffect_Interface_LevelRecords($this->stream);

        // Streaming records
        $this->properties['streamingRecords'] = new MassEffect_Interface_StreamingRecords($this->stream);

        // Kismet records
        $this->properties['kismetRecords'] = new MassEffect_Interface_KismetRecords($this->stream);

        // Door records
        $this->properties['doorRecords'] = new MassEffect_Interface_DoorRecords($this->stream);

        // Pawn records
        $this->properties['pawnRecords'] = new MassEffect_Interface_Iterator_Guid($this->stream);

        // Player data object (most of interesting data is inside it)
        $this->properties['player'] = new MassEffect_Interface_Player($this->stream);

        // Henchman records (Shepard squadmates)
        $this->properties['henchmanRecords'] = new MassEffect_Interface_Object_Henchman($this->stream);

        // Main Mass Effect plot table
        $this->properties['plotTable'] = new MassEffect_Interface_PlotTable($this->stream);

        // Original Mass Effect plot table
        $this->properties['plotTableLegacy'] = new MassEffect_Interface_PlotTableLegacy($this->stream);

        // Galaxy map
        $this->properties['galaxyMap'] = new MassEffect_Interface_GalaxyMap($this->stream);

        // Dependent DLCs
        $this->properties['dependentDLC'] = new MassEffect_Interface_Object_DependentDLC($this->stream);

        // Checksum
        $this->properties['checksum'] = $this->stream->readU32();
    }

    /**
     * Save file writing process
     *
     * @throws	MassEffect_Exception
     */
    protected function startWriting()
    {
        // Save file version, for ME2 it's 29 (or it should be...)
        $this->stream->writeU32($this->properties['version']);

        // No idea what this variable is for (empty in my saves)
        $this->stream->writeString($this->properties['debugName']);

        // Seconds played by player (no-life level meter)
        $this->stream->writeFloat($this->properties['secondsPlayed']);

        // Anyone knows what this var is for?
        $this->stream->writeS32($this->properties['disc']);

        // Current map
        $this->stream->writeString($this->properties['baseLevelName']);

        // Difficulty level
        $this->stream->writeByte($this->properties['difficulty']->getValue());

        // End game state (game not finished, shepard dead or shepard alive)
        $this->stream->writeS32($this->properties['endGameState']->getValue());

        // Save file timestamp (year, month, day and time in seconds)
        $this->properties['timestamp']->write($this->stream);

        // Shepard location
        $this->stream->writeFloat($this->properties['location']->x);
        $this->stream->writeFloat($this->properties['location']->y);
        $this->stream->writeFloat($this->properties['location']->z);

        // Shepard rotation
        $this->stream->writeS32($this->properties['rotation']->pitch);
        $this->stream->writeS32($this->properties['rotation']->yaw);
        $this->stream->writeS32($this->properties['rotation']->roll);

        // Number of current loading tip
        $this->stream->writeS32($this->properties['currentLoadingTip']);

        // Level records (name, visible, loaded)
        $this->properties['levelRecords']->write($this->stream);

        // Streaming records
        $this->properties['streamingRecords']->write($this->stream);

        // Kismet records
        $this->properties['kismetRecords']->write($this->stream);

        // Door records
        $this->properties['doorRecords']->write($this->stream);

        // Pawn records
        $this->properties['pawnRecords']->write($this->stream);

        // Player data object (most of interesting data is inside it)
        $this->properties['player']->write($this->stream);

        // Henchman records (Shepard squadmates)
        $this->properties['henchmanRecords']->write($this->stream);

        // Main Mass Effect plot table
        $this->properties['plotTable']->write($this->stream);

        // Original Mass Effect plot table
        $this->properties['plotTableLegacy']->write($this->stream);

        // Galaxy map
        $this->properties['galaxyMap']->write($this->stream);

        // Dependent DLCs
        $this->properties['dependentDLC']->write($this->stream);

        // Write checksum
        $this->stream->writeChecksum();
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
     * Not supported by this object
     *
     * @param	string	$offset	Offset name
     */
    public function offsetUnset($offset)
    {

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