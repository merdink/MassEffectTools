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
 * Save timestamp
 *
 * @author		EpicLegion											<me2.legion@gmail.com>
 * @license 	http://www.opensource.org/licenses/bsd-license.php	BSD License
 * @copyright	EpicLegion (c) 2010
 * @package		me2save
 * @subpackage	interface
 */
class MassEffect_Interface_Timestamp extends MassEffect_Interface {

    /**
     * @var	int
     */
    public $timestamp = 0;

    /**
     * Read timestamp
     *
     * @see		MassEffect/MassEffect_Interface::read()
     * @param	MassEffect_Stream						$stream
     */
    public function read(MassEffect_Stream $stream)
    {
        // Read time
        $time = $stream->readS32();

        // Day, month and year
        $day = $stream->readS32();
        $month = $stream->readS32();
        $year = $stream->readS32();

        // Get hours
        $hours = floor($time / 3600);
        $time -= ($hours * 3600);

        // Get minutes
        $minutes = floor($time / 60);
        $time -= ($minutes * 60);

        // Create timestamp ($time is seconds)
        $this->timestamp = mktime($hours, $minutes, $time, $month, $day, $year);
    }

    /**
     * Write timestamp
     *
     * @see		MassEffect/MassEffect_Interface::write()
     * @param	MassEffect_Stream							$stream
     */
    public function write(MassEffect_Stream $stream)
    {
        // Time
        $stream->writeS32($this->timestamp - (strtotime(date('Y-m-d', $this->timestamp))));

        // Day
        $stream->writeS32((int) date('d', $this->timestamp));

        // Month
        $stream->writeS32((int) date('m', $this->timestamp));

        // Year
        $stream->writeS32((int) date('Y', $this->timestamp));
    }

    /**
     * Convert timestamp to localized date string
     *
     * @return	string
     */
    public function __toString()
    {
        return date('Y-m-d H:i:s', $this->timestamp);
    }
}