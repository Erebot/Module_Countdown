<?php
/*
    This file is part of Erebot.

    Erebot is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Erebot is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Erebot.  If not, see <http://www.gnu.org/licenses/>.
*/

class   CountdownParserTokenTest
extends \PHPUnit\Framework\TestCase
{
    public function testParserTokenCreation()
    {
        // Preload Erebot_Module_Countdown_Parser so that underlying
        // classes like Erebot_Module_Countdown_Parser_yyToken become available.
        class_exists('\\Erebot\\Module\\Countdown\\Parser');
        $obj = new \Erebot\Module\Countdown\ParseyyToken('foo');
        $this->assertEquals('foo', (string) $obj);

        $obj[] = array('token');
        $this->assertEquals(TRUE, isset($obj[0]));
        $this->assertEquals("token", $obj[0]);

        unset($obj[0]);
        $this->assertEquals(FALSE, isset($obj[0]));

        $obj2 = new \Erebot\Module\Countdown\ParseyyToken($obj);
        $obj2 = new \Erebot\Module\Countdown\ParseyyToken('foo', $obj2);
        $obj2[] = NULL;
        $obj2[0] = NULL;
        $obj2[42] = 'foo';

        $obj[42] = $obj2;
    }
}

