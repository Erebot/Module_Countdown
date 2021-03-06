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

class   CountdownFormulaTest
extends \PHPUnit\Framework\TestCase
{
    protected $formula = NULL;

    /**
     * The formula parser expects a string as its input.
     * @expectedException \Erebot\Module\Countdown\FormulaMustBeAStringException
     */
    public function testFormulaParsing()
    {
        new \Erebot\Module\Countdown\Formula('foo', 42);
    }

    /**
     * Using an empty string should throw an error.
     * @expectedException \Erebot\Module\Countdown\FormulaMustBeAStringException
     */
    public function testFormulaParsing2()
    {
        new \Erebot\Module\Countdown\Formula('foo', '');
    }

    /**
     * Parsing on a single number represented as a string
     * should return than number as an integer.
     */
    public function testFormulaParsing3()
    {
        $formula    = '42';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(42, $obj->getResult());
    }

    /**
     * Must be able to parse additions.
     */
    public function testFormulaParsing4()
    {
        $formula    = '40 + 2';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(42, $obj->getResult());
    }

    /**
     * Must be able to parse multiplications.
     */
    public function testFormulaParsing5()
    {
        $formula    = '6 * 7';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(42, $obj->getResult());
    }

    /**
     * Must be able to parse subtractions.
     */
    public function testFormulaParsing6()
    {
        $formula    = '45 - 3';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(42, $obj->getResult());
    }

    /**
     * Must be able to parse divisions.
     */
    public function testFormulaParsing7()
    {
        $formula    = '42 / 6';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(7, $obj->getResult());
    }

    /**
     * Test operator priorities.
     */
    public function testFormulaParsing8()
    {
        $formula    = '2 + 2 * 20';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(42, $obj->getResult());

        $formula    = '(2 + 2) * 20';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertSame(80, $obj->getResult());
    }

    public function testGettingNumbersUsedInFormula()
    {
        $formula    = '1 + 2 * 42 / 7 + 1';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $numbers    = $obj->getNumbers();
        $used       = array(1, 2, 42, 7, 1);
        sort($used);
        sort($numbers);
        $this->assertEquals($used, $numbers,
            "Failed retrieving numbers used in formula.");
    }

    public function testGetFormula()
    {
        $formula    = '1 + 2 * 42 / 7 + 1';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertEquals($formula, $obj->getFormula());
    }

    public function testGetOwner()
    {
        $formula    = '1 + 2 * 42 / 7 + 1';
        $obj        = new \Erebot\Module\Countdown\Formula('foo', $formula);
        $this->assertEquals('foo', $obj->getOwner());
    }
}

