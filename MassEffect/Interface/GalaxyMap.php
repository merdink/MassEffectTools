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
 * Galaxy map interface
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_GalaxyMap extends MassEffect_Interface_Iterator {

    /**
     * Planet index (PlanetID => i)
     *
     * @var	array
     */
    protected $planetIndex = array();

    /**
     * Get planet by ID
     *
     * @param	int		$id
     * @return	mixed
     */
    public function getPlanet($id)
    {
        return isset($this->planetIndex[$id]) ? $this->records[$this->planetIndex[$id]] : NULL;
    }

    /**
     * Set planet by ID
     *
     * @param	int							$id
     * @param	MassEffect_Object_Planet	$planet
     */
    public function setPlanet($id, MassEffect_Object_Planet $planet)
    {
        $this->records[$this->planetIndex[$id]] = $planet;
    }

    /**
     * Handle save array element
     *
     * @see		MassEffect/Interface/MassEffect_Interface_Iterator::handleItem()
     * @param	int																	$i
     * @param	MassEffect_Stream													$stream
     */
    protected function handleItem($i, MassEffect_Stream $stream)
    {
        // Planet ID
        $planetId = $stream->readS32();

        // Set index
        $this->planetIndex[$planetId] = $i;

        // Add planet
        $this->records[$i] = new MassEffect_Object_Planet(array(
            'id' => $planetId,
            'visited' => $stream->readBool(),
            'probes' => new MassEffect_Interface_Object_Vector2D($stream)
        ));
    }

    /**
     * Write record
     *
     * @param	mixed				$record
     * @param	MassEffect_Stream	$stream
     */
    protected function writeItem($record, MassEffect_Stream $stream)
    {
        // Write ID, visited
        $stream->writeS32($record['id']);
        $stream->writeBool($record['visited']);

        // Probe positions
        $record['probes']->write($stream);
    }
}