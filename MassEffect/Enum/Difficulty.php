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
 * @subpackage	enum
 */

/**
 * Game difficulty level
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	enum
 */
class MassEffect_Enum_Difficulty extends MassEffect_Enum {

    /**
     * Casual level
     *
     * @var	int
     */
    const CASUAL = 0;

    /**
     * Normal level
     *
     * @var	int
     */
    const NORMAL = 1;

    /**
     * Veteran level
     *
     * @var	int
     */
    const VETERAN = 2;

    /**
     * Hardcore level
     *
     * @var	int
     */
    const HARDCORE = 3;

    /**
     * Insanity level
     *
     * @var	int
     */
    const INSANITY = 4;

    /**
     * Unknown (6) difficulty level
     *
     * @var	int
     */
    const UNKNOWN = 5;

    /**
     * Get allowed enum values
     *
     * @see		MassEffect/MassEffect_Enum::getValues()
     * @return	array
     */
    public function getValues()
    {
        return array(
            0 => 'Casual',
            1 => 'Normal',
            2 => 'Veteran',
            3 => 'Hardcore',
            4 => 'Insanity',
            5 => 'Unknown'
        );
    }
}