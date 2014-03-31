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
 *      A very basic solver for the countdown game.
 *
 * This solver does not try to be smart and just does
 * a plain old brute-force on the combination space.
 */
class Solver implements \Erebot\Module\Countdown\SolverInterface
{
    /// Target to reach.
    protected $target;

    /// Numbers that may be used to reach the target.
    protected $numbers;


    public function __construct($target, $numbers)
    {
        if (!is_int($target) || $target <= 0) {
            throw new \Erebot\Module\Countdown\Exception(
                'Invalid target number'
            );
        }
        if (!is_array($numbers)) {
            throw new \Erebot\Module\Countdown\Exception(
                'An array of numbers was expected'
            );
        }

        $this->target   = $target;
        $this->numbers  = array();
        rsort($numbers, SORT_NUMERIC);
        foreach ($numbers as $number) {
            $this->numbers[] = new \Erebot\Module\Countdown\Solver\Number($number);
        }
    }

    public function solve()
    {
        $best           = null;
        $bestDistance   = null;
        $numbersBefore  = array($this->numbers);
        $operators      = array('+', '-', '*', '/');
        $opCls          = "\\Erebot\\Module\\Countdown\\Solver\\Operation";

        while (count($numbersBefore)) {
            $numbersAfter   = array();
            foreach ($numbersBefore as $set) {
                $nbNumbers = count($set);

                for ($i = 1; $i < $nbNumbers; $i++) {
                    for ($j = 0; $j < $i; $j++) {
                        foreach ($operators as $operator) {
                            try {
                                $result =
                                    new $opCls($set[$j], $set[$i], $operator);
                                $distance = abs(
                                    $result->getValue() -
                                    $this->target
                                );

                                if (!$distance) {
                                    $best = $result;
                                    break 5;
                                }

                                if ($best === null ||
                                    $distance < $bestDistance) {
                                    $best = $result;
                                    $bestDistance = abs($best->getValue() - $this->target);
                                }

                                if ($nbNumbers == 2) {
                                    continue;
                                }

                                $newSet = array_merge(
                                    array_slice($set, 0, $j),
                                    array($result),
                                    array_slice($set, $j + 1, $i - $j - 1),
                                    array_slice($set, $i + 1)
                                );
                                usort(
                                    $newSet,
                                    function ($a, $b) {
                                        return ($b->getValue() - $a->getValue());
                                    }
                                );
                                $numbersAfter[] = $newSet;
                            } catch (\Erebot\Module\Countdown\Solver\SkipException $e) {
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
