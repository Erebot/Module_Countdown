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

class   CountdownStub
extends Erebot_Module_Countdown_Game
{
    public function __construct()
    {
        parent::__construct(100, 110, 7, array(1));
        $this->_min = 7;
        $this->_max = 10;
    }
}

class   CountdownTest
extends PHPUnit_Framework_TestCase
{
    protected $_countdown = NULL;

    public function setUp()
    {
        $this->_countdown = new Erebot_Module_Countdown_Game();
    }

    public function tearDown()
    {
        unset($this->_countdown);
        $this->_countdown = NULL;
    }

    /**
     * GetNumbers may only generate numbers in the following set:
     * [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25, 50, 75, 100]
     * Any other number points to an implementation error.
     */
    public function testGetNumbers()
    {
        $numbers    = $this->_countdown->getNumbers();
        $allowed    = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 25, 50, 75, 100);
        foreach ($numbers as $number)
            $this->assertContains($number, $allowed,
                "$number is not allowed. Allowed numbers are: ".
                implode(', ', $allowed).'.');
    }

    /**
     * GetTarget must return an integer between 100 & 999 (inclusive).
     */
    public function testGetTarget()
    {
        $target         = $this->_countdown->getTarget();
        $expectedType   = PHPUnit_Framework_Constraint_IsType::TYPE_INT;
        $this->assertType($expectedType, $target);
        $this->assertGreaterThanOrEqual(100, $target);
        $this->assertLessThanOrEqual(   999, $target);
    }

    /**
     * @expectedException Erebot_Module_Countdown_UnavailableNumberException
     */
    public function testCannotReuseNumber()
    {
        $numbers    = $this->_countdown->getNumbers();
        $numbers[]  = $numbers[0];
        $formula    = implode(' + ', $numbers);
        $obj        = new Erebot_Module_Countdown_Formula('foo', $formula);
        $this->_countdown->proposeFormula($obj);
    }

    public function testReturnsBestProposedFormula()
    {
        unset($this->_countdown);
        $this->_countdown = new CountdownStub();

        $obj        = new Erebot_Module_Countdown_Formula('foo', '1+1');
        $this->_countdown->proposeFormula($obj);
        $this->assertSame($obj, $this->_countdown->getBestProposal());

        $numbers    = $this->_countdown->getNumbers();
        $formula    = implode(' + ', $numbers);
        $obj        = new Erebot_Module_Countdown_Formula('bar', $formula);
        $this->_countdown->proposeFormula($obj);
        $this->assertSame($obj, $this->_countdown->getBestProposal());
    }

    /**
     * @expectedException Erebot_Module_Countdown_SyntaxErrorException
     */
    public function testInvalidSyntax()
    {
        $obj        = new Erebot_Module_Countdown_Formula('foo', 'foo');
    }
}

?>
