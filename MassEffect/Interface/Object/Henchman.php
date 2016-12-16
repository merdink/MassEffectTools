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
 * Henchman iterator
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_Object_Henchman extends MassEffect_Interface_Iterator {

    /**
     * Handle save array element
     *
     * @see		MassEffect/Interface/MassEffect_Interface_Iterator::handleItem()
     * @param	int																	$i
     * @param	MassEffect_Stream													$stream
     */
    protected function handleItem($i, MassEffect_Stream $stream)
    {
        $this->records[] = new MassEffect_Object_Henchman(array(
        'tag' => $stream->readString(), // Squadmate name
        'powers' => new MassEffect_Interface_Object_Power($stream), // Power collection
        'level' => $stream->readS32(), // Level
        'talentPoints' => $stream->readS32(), // Free talent points
        'loadout' => new MassEffect_Object_Loadout(array( // Current loadout
            'assaultRifle' => $stream->readString(),
            'shotgun' => $stream->readString(),
            'sniperRifle' => $stream->readString(),
            'smg' => $stream->readString(),
            'pistol' => $stream->readString(),
            'heavyWeapon' => $stream->readString()
        )),
        'mappedPower' => $stream->readString() // Mapped power (X360 only?)
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
        // Write name
        $stream->writeString($record['tag']);

        // Write powers
        $record->powers->write($stream);

        // Level and TP
        $stream->writeS32($record['level']);
        $stream->writeS32($record['talentPoints']);

        // Loadout
        $stream->writeString($record->loadout['assaultRifle']);
        $stream->writeString($record->loadout['shotgun']);
        $stream->writeString($record->loadout['sniperRifle']);
        $stream->writeString($record->loadout['smg']);
        $stream->writeString($record->loadout['pistol']);
        $stream->writeString($record->loadout['heavyWeapon']);

        // Write power
        $stream->writeString($record['mappedPower']);
    }
}