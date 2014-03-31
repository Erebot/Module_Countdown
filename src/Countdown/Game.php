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
 *      An implementation of the famous
 *      Countdown TV game show.
 */
class Game
{
    /// A list of numbers that may be used to reach the target number.
    protected $numbers;

    /// Target number to reach.
    protected $target;

    /// Best formula proposed so far.
    protected $bestProposal;

    /// Default set of allowed numbers.
    protected $allowedNumbers = array(
        1, 2,  3,  4,  5,  6,   7,
        8, 9, 10, 25, 50, 75, 100,
    );

    /// The target number may not be less than this.
    protected $minTarget;

    /// The target number may not be greater than this.
    protected $maxTarget;


    /**
     * Constructs a new instance of the Countdown game.
     *
     * A random (target) number will be selected between
     * $minTarget and $maxTarget. Contestants will be
     * presented with $nbNumbers random numbers to help
     * them reach the target number.
     *
     * \param int $minTarget
     *      (optional) The target number may not be
     *      less than this. Defaults to 100.
     *
     * \param int $maxTarget
     *      (optional) The target number may not be
     *      greater than this. Defaults to 999.
     *
     * \param int $nbNumbers
     *      (optional) How many random numbers will
     *      be picked (from $allowedNumbers) to help
     *      contestants. Defaults to 7.
     *
     * \param list $allowedNumbers
     *      (optional) A list with the numbers that may
     *      be selected to help contestants.
     *      Defaults to: 1-10, 25, 50, 75 & 100.
     */
    public function __construct($minTarget = 100, $maxTarget = 999, $nbNumbers = 7, $allowedNumbers = null)
    {
        /// @TODO: refactor checks to avoid redundancy.
        if (!is_int($minTarget)) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$minTarget',
                'integer',
                typeof($minTarget)
            );
        }
        if ($minTarget < 100) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$minTarget',
                'number >= 100',
                $minTarget
            );
        }
        $this->minTarget = $minTarget;

        if (!is_int($maxTarget)) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$maxTarget',
                'integer',
                typeof($maxTarget)
            );
        }
        if ($maxTarget <= $this->minTarget) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$maxTarget',
                'number > minTarget',
                $maxTarget
            );
        }
        $this->maxTarget = $maxTarget;

        if (!is_int($nbNumbers)) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$nbNumbers',
                'integer',
                typeof($nbNumbers)
            );
        }
        if ($nbNumbers < 1) {
            throw new \Erebot\Module\Countdown\InvalidValue(
                '$nbNumbers',
                'number > 1',
                $nbNumbers
            );
        }

        if ($allowedNumbers !== null) {
            if (!is_array($allowedNumbers)) {
                throw new \Erebot\Module\Countdown\InvalidValue(
                    '$allowedNumbers',
                    'array',
                    typeof($allowedNumbers)
                );
            }
            if (!count($allowedNumbers)) {
                throw new \Erebot\Module\Countdown\InvalidValue(
                    '$allowedNumbers',
                    'non-empty array',
                    'empty array'
                );
            }
            foreach ($allowedNumbers as $allowedNumber) {
                if (!is_int($allowedNumber)) {
                    throw new \Erebot\Module\Countdown\InvalidValue(
                        '$allowedNumbers',
                        'array of int',
                        'array of '.typeof($allowedNumber)
                    );
                }
                if ($allowedNumber < 1) {
                    throw new \Erebot\Module\Countdown\InvalidValue(
                        '$allowedNumbers',
                        'array of int >= 1',
                        $allowedNumber
                    );
                }
            }
            $this->allowedNumbers = $allowedNumbers;
        }
        $this->bestProposal = null;
        $this->chooseNumbers($nbNumbers);
    }

    /**
     * Chooses the random numbers used by the game.
     * This randomly selected a target number and
     * also picks the numbers that will be made
     * available to help contestants.
     *
     * \param int $nbNumbers
     *      How many numbers will be selected.
     */
    protected function chooseNumbers($nbNumbers)
    {
        $this->numbers = array();
        for ($i = 0; $i < $nbNumbers; $i++) {
            $key = array_rand($this->allowedNumbers);
            $this->numbers[] = $this->allowedNumbers[$key];
        }

        $this->target = mt_rand($this->minTarget, $this->maxTarget);
    }

    /// Destructs the game.
    public function __destruct()
    {
        unset($this->bestProposal);
    }

    /**
     * Returns a list with the numbers that may
     * be used to reach the target number.
     * The same number may appear multiple times.
     *
     * \retval list
     *      List of numbers that may be used in
     *      formulae.
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    /**
     * Returns the target number contestants must
     * reach.
     *
     * \retval int
     *      Target number for the game.
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns the best proposal made so far.
     *
     * \retval Erebot_Module_Countdown_Formula
     *      Best proposal received so far.
     */
    public function getBestProposal()
    {
        return $this->bestProposal;
    }

    /**
     * Formula proposal.
     *
     * \param Erebot::Module::Countdown::Formula $formula
     *      The proposed formula.
     *
     * \TODO: write an interface for formulae and use it there.
     */
    public function proposeFormula(\Erebot\Module\Countdown\Formula $formula)
    {
        $gameNumbers    = $this->numbers;
        $formulaNumbers = $formula->getNumbers();

        foreach ($formulaNumbers as $number) {
            $key = array_search($number, $gameNumbers);
            if ($key === false) {
                throw new \Erebot\Module\Countdown\UnavailableNumberException();
            }
            unset($gameNumbers[$key]);
        }

        if ($this->bestProposal === null) {
            $this->bestProposal = $formula;
            return true;
        }

        $oldDst = abs($this->bestProposal->getResult() - $this->target);
        $newDst = abs($formula->getResult() - $this->target);
        if ($newDst < $oldDst) {
            $this->bestProposal = $formula;
            return true;
        }

        return false;
    }
}
