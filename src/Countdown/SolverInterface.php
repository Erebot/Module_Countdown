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

namespace Erebot\Module\Countdown;

/**
 * \brief
 *      Interface for a solver meant to solve
 *      a Countdown game.
 */
interface SolverInterface
{
    /**
     * Creates a new instance of the solver.
     *
     * \param int $target
     *      Target number to reach.
     *
     * \param array $numbers
     *      A list of numbers that may be used
     *      to reach the target number.
     *
     * \throw Erebot::Module::Countdown::Exception
     *      Something was wrong with the parameters
     *      given to this method. See the exception's
     *      message for an explanation of what went
     *      wrong.
     */
    public function __construct($target, $numbers);

    /**
     * Solve the game (that is, find the closest result
     * to the target number using only a restricted set
     * of numbers and the four basic operators).
     *
     * \retval Erebot::Module::Countdown::Solver::Operation
     *      A formula that gives the best result for this
     *      game.
     */
    public function solve();
}
