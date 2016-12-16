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
 * Mass Effect 1 plot table
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_PlotTableLegacy extends MassEffect_Interface {

    /**
     * @var	MassEffect_Interface_Iterator_BitArray
     */
    protected $boolVariables = NULL;

    /**
     * @var	MassEffect_Interface_Iterator_Int
     */
    protected $intVariables = NULL;

    /**
     * @var	MassEffect_Interface_Iterator_Float
     */
    protected $floatVariables = NULL;

    /**
     * Set/get boolean table var
     *
     * If second param is present, this method behaves like setter
     *
     * @param	int		$index
     * @param	bool	$newValue
     * @return	mixed
     */
    public function bool($index, $newValue = NULL)
    {
        // Getter
        if($newValue === NULL)
        {
            return $this->boolVariables->getBit($index);
        }

        // Setter
        $this->boolVariables->setBit($index, $newValue);
    }

    /**
     * Set/get float table var
     *
     * If second param is present, this method behaves like setter
     *
     * @param	int		$index
     * @param	float	$newValue
     * @return	float
     */
    public function float($index, $newValue = NULL)
    {
        // Getter
        if($newValue === NULL)
        {
            return isset($this->floatVariables[$index]) ? $this->floatVariables[$index] : FALSE;
        }

        // Setter
        $this->floatVariables[$index] = (float) $newValue;
    }

    /**
     * Set/get int table var
     *
     * If second param is present, this method behaves like setter
     *
     * @param	int		$index
     * @param	int		$newValue
     * @return	int
     */
    public function int($index, $newValue = NULL)
    {
        // Getter
        if($newValue === NULL)
        {
            return isset($this->intVariables[$index]) ? $this->intVariables[$index] : 0;
        }

        // Setter
        $this->intVariables[$index] = (int) $newValue;
    }

    /**
     * Read ME1 table
     *
     * @see		MassEffect/MassEffect_Interface::read()
     * @param	MassEffect_Stream						$stream
     */
    public function read(MassEffect_Stream $stream)
    {
        $this->boolVariables = new MassEffect_Interface_Iterator_BitArray($stream);
        $this->intVariables = new MassEffect_Interface_Iterator_Int($stream);
        $this->floatVariables = new MassEffect_Interface_Iterator_Float($stream);
    }

    /**
     * Write to stream
     *
     * @param	MassEffect_Stream	$stream
     */
    public function write(MassEffect_Stream $stream)
    {
        // Bool/int/float
        $this->boolVariables->write($stream);
        $this->intVariables->write($stream);
        $this->floatVariables->write($stream);
    }
}