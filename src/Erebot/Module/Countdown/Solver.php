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

/**
 * \brief
 *      A very basic solver for the countdown game.
 *
 * This solver does not try to be smart and just does
 * a plain old brute-force on the combination space.
 */
class       Erebot_Module_Countdown_Solver
implements  Erebot_Module_Countdown_Solver_Interface
{
    /// Target to reach.
    protected $_target;

    /// Numbers that may be used to reach the target.
    protected $_numbers;


    /// \copydoc Erebot_Module_Countdown_Solver_Interface::__construct()
    public function __construct($target, $numbers)
    {
        if (!is_int($target) || $target <= 0) {
            throw new Erebot_Module_Countdown_Exception(
                'Invalid target number'
            );
        }
        if (!is_array($numbers)) {
            throw new Erebot_Module_Countdown_Exception(
                'An array of numbers was expected'
            );
        }

        $this->_target  = $target;
        $this->_numbers = array();
        rsort($numbers, SORT_NUMERIC);
        foreach ($numbers as $number)
            $this->_numbers[] =
                new Erebot_Module_Countdown_Solver_Number($number);
    }

    /**
     * Returns an integer that indicates how the two
     * arguments compare to each other.
     *
     * \param int $a
     *      First integer to use in the comparison.
     *
     * \param int $b
     *      Second operand for the comparison.
     *
     * \retval int
     *      Returns an integer that is < 0, equal to 0
     *      or > 0 when the first argument is respectively,
     *      less than, equal to or greater than the second.
     */
    protected function _sortSet($a, $b)
    {
        return ($b->getValue() - $a->getValue());
    }

    /// \copydoc Erebot_Module_Countdown_Solver_Interface::solve()
    public function solve()
    {
        $best           = NULL;
        $bestDistance   = NULL;
        $numbersBefore  = array($this->_numbers);
        $operators      = array('+', '-', '*', '/');

        while (count($numbersBefore)) {
            $numbersAfter   = array();
            foreach ($numbersBefore as $set) {
                $nbNumbers = count($set);

                for ($i = 1; $i < $nbNumbers; $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        foreach ($operators as $operator) {
                            try {
                                $result =
                                    new Erebot_Module_Countdown_Solver_Operation(
                                        $set[$j], $set[$i], $operator
                                    );
                                $distance = abs(
                                    $result->getValue() -
                                    $this->_target
                                );

                                if (!$distance) {
                                    $best = $result;
                                    break 5;
                                }

                                if ($best === NULL ||
                                    $distance < $bestDistance) {
                                    $best = $result;
                                    $bestDistance =
                                        abs($best->getValue() - $this->_target);
                                }

                                if ($nbNumbers == 2)
                                    continue;

                                $newSet = array_merge(
                                    array_slice($set, 0, $j),
                                    array($result),
                                    array_slice($set, $j + 1, $i - $j - 1),
                                    array_slice($set, $i + 1)
                                );
                                usort($newSet, array($this, '_sortSet'));
                                $numbersAfter[] = $newSet;
                            }
                            catch (Erebot_Module_Countdown_Solver_SkipException $e) {
                                // Do nothing.
                            }
                        }
                    }
                }
            }
            $numbersBefore = $numbersAfter;
        }
        return $best;
    }
}

