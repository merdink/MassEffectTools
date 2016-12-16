<?php
/**
 * Mass Effect 2 save reading and writing support
 *
 * This library is not associated in any way with EA or Bioware.
 *
 * Copyright (c) 2010, EpicLegion
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provIDed that the following conditions are met:
 *
 * 1) Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * 2) Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provIDed with the distribution.
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
 * Player interface
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_Player extends MassEffect_Interface {

    /**
     * @var	MassEffect_Object_Player
     */
    protected $player = NULL;

    /**
     * Convert object into string
     *
     * @return	string
     */
    public function __toString()
    {
        return ($this->player) ? $this->player->__toString() : '';
    }

    /**
     * Get player object
     *
     * @return	MassEffect_Object_Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set player object
     *
     * @param	MassEffect_Object_Player	$player
     */
    public function setPlayer(MassEffect_Object_Player $player)
    {
        $this->player = $player;
    }

    /**
     * Read complex player object from unreal stream
     *
     * @see		MassEffect/MassEffect_Interface::read()
     * @param	MassEffect_Stream						$stream
     */
    public function read(MassEffect_Stream $stream)
    {
        // Create new player object
        $this->player = new MassEffect_Object_Player;

        // Gender
        $this->player->isFemale = $stream->readBool();

        // Class
        $this->player->class = $stream->readString();

        // Level and experience points
        $this->player->level = $stream->readS32();
        $this->player->currentXP = $stream->readFloat();

        // Name and last name (not used)
        $this->player->name = $stream->readString();
        $this->player->lastName = $stream->readS32();

        // Preservice history and profile
        $this->player->preservice = new MassEffect_Enum_Preservice($stream->readByte());
        $this->player->profile = new MassEffect_Enum_Profile($stream->readByte());

        // Unused talent points
        $this->player->talentPoints = $stream->readS32();

        // Mapped powers (propably only X360 version)
        $this->player->mappedPower1 = $stream->readString();
        $this->player->mappedPower2 = $stream->readString();
        $this->player->mappedPower3 = $stream->readString();

        // Read player appearance
        $this->readAppearance($stream);

        // Player powers
        $this->player->powers = new MassEffect_Interface_Object_Power($stream);

        // Player weapons
        $this->player->weapons = new MassEffect_Interface_Object_Weapon($stream);

        // Loadout (equipped stuff)
        $this->player->loadout = new MassEffect_Object_Loadout(array(
            'assaultRifle' => $stream->readString(),
            'shotgun' => $stream->readString(),
            'sniperRifle' => $stream->readString(),
            'smg' => $stream->readString(),
            'pistol' => $stream->readString(),
            'heavyWeapon' => $stream->readString()
        ));

        // Hotkeys (PC only)
        $this->player->hotkeys = new MassEffect_Interface_Object_Hotkey($stream);

        // Credits, medigel, resources, probes and fuel
        $this->player->credits = $stream->readS32();
        $this->player->medigel = $stream->readS32();
        $this->player->elementZero = $stream->readS32();
        $this->player->iridium = $stream->readS32();
        $this->player->palladium = $stream->readS32();
        $this->player->platinum = $stream->readS32();
        $this->player->probes = $stream->readS32();
        $this->player->currentFuel = $stream->readFloat();

        // Face code
        $this->player->faceCode = $stream->readString();

        // Another class name
        $this->player->classFriendlyName = $stream->readS32();
    }

    /**
     * Write complex player object
     *
     * @see		MassEffect/MassEffect_Interface::write()
     * @param	MassEffect_Stream							$stream
     */
    public function write(MassEffect_Stream $stream)
    {
        // Gender
        $stream->writeBool($this->player->isFemale);

        // Class
        $stream->writeString($this->player->class);

        // Level and experience points
        $stream->writeS32($this->player->level);
        $stream->writeFloat($this->player->currentXP);

        // Name and last name (not used)
        $stream->writeString($this->player->name);
        $stream->writeS32($this->player->lastName);

        // Preservice history and profile
        $stream->writeByte($this->player->preservice->getValue());
        $stream->writeByte($this->player->profile->getValue());

        // Unused talent points
        $stream->writeS32($this->player->talentPoints);

        // Mapped powers (propably only X360 version)
        $stream->writeString($this->player->mappedPower1);
        $stream->writeString($this->player->mappedPower2);
        $stream->writeString($this->player->mappedPower3);

        // Read player appearance
        $this->writeAppearance($stream);

        // Player powers
        $this->player->powers->write($stream);

        // Player weapons
        $this->player->weapons->write($stream);

        // Loadout (equipped stuff)
        $stream->writeString($this->player->loadout->assaultRifle);
        $stream->writeString($this->player->loadout->shotgun);
        $stream->writeString($this->player->loadout->sniperRifle);
        $stream->writeString($this->player->loadout->smg);
        $stream->writeString($this->player->loadout->pistol);
        $stream->writeString($this->player->loadout->heavyWeapon);

        // Hotkeys (PC only)
        $this->player->hotkeys->write($stream);

        // Credits, medigel, resources, probes and fuel
        $stream->writeS32($this->player->credits);
        $stream->writeS32($this->player->medigel);
        $stream->writeS32($this->player->elementZero);
        $stream->writeS32($this->player->iridium);
        $stream->writeS32($this->player->palladium);
        $stream->writeS32($this->player->platinum);
        $stream->writeS32($this->player->probes);
        $stream->writeFloat($this->player->currentFuel);

        // Face code
        $stream->writeString($this->player->faceCode);

        // Another class name
        $stream->writeS32($this->player->classFriendlyName);
    }

    /**
     * Write player appearance
     *
     * @param	MassEffect_Stream	$stream
     */
    protected function writeAppearance(MassEffect_Stream $stream)
    {
        // Combat appearance type
        $stream->writeByte($this->player->appearance->combatAppearance->getValue());

        // Non-combat appearance ID
        $stream->writeS32($this->player->appearance->casualID);

        // Full armor ID
        $stream->writeS32($this->player->appearance->fullBodyID);

        // Armor parts IDs
        $stream->writeS32($this->player->appearance->torsoID);
        $stream->writeS32($this->player->appearance->shoulderID);
        $stream->writeS32($this->player->appearance->armID);
        $stream->writeS32($this->player->appearance->legID);
        $stream->writeS32($this->player->appearance->specID);
        $stream->writeS32($this->player->appearance->tintOneID);
        $stream->writeS32($this->player->appearance->tintTwoID);
        $stream->writeS32($this->player->appearance->tintThreeID);
        $stream->writeS32($this->player->appearance->patternID);
        $stream->writeS32($this->player->appearance->patternColorID);
        $stream->writeS32($this->player->appearance->helmetID);

        // Has customized head?
        $stream->writeBool($this->player->appearance->hasMorphHead);

        // Morph head data needs to be written
        if($this->player->appearance->hasMorphHead)
        {
            $this->writeMorphHead($stream);
        }
    }

    /**
     * Write morphed head data
     *
     * @param	MassEffect_Stream	$stream
     */
    protected function writeMorphHead(MassEffect_Stream $stream)
    {
        // Hair
        $stream->writeString($this->player->appearance->morphHead->hairMesh);

        // Accesories
        $this->player->appearance->morphHead->accessoryMeshes->write($stream);

        // Some stuff i do not understand
        $this->player->appearance->morphHead->morphFeatures->write($stream);
        $this->player->appearance->morphHead->offsetBones->write($stream);
        $this->player->appearance->morphHead->lod0Verticles->write($stream);
        $this->player->appearance->morphHead->lod1Verticles->write($stream);
        $this->player->appearance->morphHead->lod2Verticles->write($stream);
        $this->player->appearance->morphHead->lod3Verticles->write($stream);
        $this->player->appearance->morphHead->scalarParameters->write($stream);
        $this->player->appearance->morphHead->vectorParameters->write($stream);
        $this->player->appearance->morphHead->textureParameters->write($stream);
    }

    /**
     * Read player appearance
     *
     * @param	MassEffect_Stream	$stream
     */
    protected function readAppearance(MassEffect_Stream $stream)
    {
        // Appearance object
        $this->player->appearance = new MassEffect_Object_Player_Appearance;

        // Combat appearance type
        $this->player->appearance->combatAppearance = new MassEffect_Enum_AppearanceType($stream->readByte());

        // Non-combat appearance ID
        $this->player->appearance->casualID = $stream->readS32();

        // Full armor ID
        $this->player->appearance->fullBodyID = $stream->readS32();

        // Armor parts IDs
        $this->player->appearance->torsoID = $stream->readS32();
        $this->player->appearance->shoulderID = $stream->readS32();
        $this->player->appearance->armID = $stream->readS32();
        $this->player->appearance->legID = $stream->readS32();
        $this->player->appearance->specID = $stream->readS32();
        $this->player->appearance->tintOneID = $stream->readS32();
        $this->player->appearance->tintTwoID = $stream->readS32();
        $this->player->appearance->tintThreeID = $stream->readS32();
        $this->player->appearance->patternID = $stream->readS32();
        $this->player->appearance->patternColorID = $stream->readS32();
        $this->player->appearance->helmetID = $stream->readS32();

        // Has customized head?
        $this->player->appearance->hasMorphHead = $stream->readBool();

        // Morph head data needs to be parsed?
        if($this->player->appearance->hasMorphHead)
        {
            $this->readMorphHead($stream);
        }
    }

    /**
     * Read morphed head data
     *
     * @param	MassEffect_Stream	$stream
     */
    protected function readMorphHead(MassEffect_Stream $stream)
    {
        // Morph head object
        $this->player->appearance->morphHead = new MassEffect_Object_Player_MorphHead;

        // Hair
        $this->player->appearance->morphHead->hairMesh = $stream->readString();

        // Accesories
        $this->player->appearance->morphHead->accessoryMeshes = new MassEffect_Interface_Iterator_String($stream);

        // Some stuff i do not understand
        $this->player->appearance->morphHead->morphFeatures = new MassEffect_Interface_Object_MorphFeature($stream);
        $this->player->appearance->morphHead->offsetBones = new MassEffect_Interface_Object_OffsetBone($stream);
        $this->player->appearance->morphHead->lod0Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->player->appearance->morphHead->lod1Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->player->appearance->morphHead->lod2Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->player->appearance->morphHead->lod3Verticles = new MassEffect_Interface_Object_Vector($stream);
        $this->player->appearance->morphHead->scalarParameters = new MassEffect_Interface_Object_ScalarParameter($stream);
        $this->player->appearance->morphHead->vectorParameters = new MassEffect_Interface_Object_VectorParameter($stream);
        $this->player->appearance->morphHead->textureParameters = new MassEffect_Interface_Object_TextureParameter($stream);
    }
}