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

class   CountdownSolverTest
extends PHPUnit_Framework_TestCase
{
    public function problems()
    {
        return array(
            # Exact solution.
            array(
                array(10, 1, 25, 1),
                286,
                "((25+1)*(10+1))",
                286
            ),

            # Close solution.
            array(
                array(100, 25, 4, 3),
                599,
                "((100+(25*4))*3)",
                600
            ),
        );
    }

    /**
     * @dataProvider problems
     */
    public function testSolver($numbers, $target, $formula, $result)
    {
        $solver = new \Erebot\Module\Countdown\Solver($target, $numbers);
        $best   = $solver->solve();
        $this->assertEquals($formula, (string) $best);
        $this->assertEquals($result, $best->getValue());
    }
}

