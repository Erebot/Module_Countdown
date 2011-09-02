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

class Erebot_Module_Countdown_Game
{
    protected $_numbers;
    protected $_target;

    protected $_bestProposal;

    protected $_allowedNumbers = array(
        1, 2,  3,  4,  5,  6,   7,
        8, 9, 10, 25, 50, 75, 100,
    );

    protected $_minTarget;
    protected $_maxTarget;

    public function __construct(
        $minTarget          = 100,
        $maxTarget          = 999,
        $nbNumbers          = 7,
        $allowedNumbers     = NULL
    )
    {
        /// @TODO: refactor checks to avoid redundancy.
        if (!is_int($minTarget))
            throw new Erebot_Module_Countdown_InvalidValue(
                '$minTarget',
                'integer',
                typeof($minTarget)
            );
        if ($minTarget < 100)
            throw new Erebot_Module_Countdown_InvalidValue(
                '$minTarget',
                'number >= 100',
                $minTarget
            );
        $this->_minTarget = $minTarget;

        if (!is_int($maxTarget))
            throw new Erebot_Module_Countdown_InvalidValue(
                '$maxTarget',
                'integer',
                typeof($maxTarget)
            );
        if ($maxTarget <= $this->_minTarget)
            throw new Erebot_Module_Countdown_InvalidValue(
                '$maxTarget',
                'number > minTarget',
                $maxTarget
            );
        $this->_maxTarget = $maxTarget;

        if (!is_int($nbNumbers))
            throw new Erebot_Module_Countdown_InvalidValue(
                '$nbNumbers',
                'integer',
                typeof($nbNumbers)
            );
        if ($nbNumbers < 1)
            throw new Erebot_Module_Countdown_InvalidValue(
                '$nbNumbers',
                'number > 1',
                $nbNumbers
            );

        if ($allowedNumbers !== NULL) {
            if (!is_array($allowedNumbers))
                throw new Erebot_Module_Countdown_InvalidValue(
                    '$allowedNumbers',
                    'array',
                    typeof($allowedNumbers)
                );
            if (!count($allowedNumbers))
                throw new Erebot_Module_Countdown_InvalidValue(
                    '$allowedNumbers',
                    'non-empty array',
                    'empty array'
                );
            foreach ($allowedNumbers as $allowedNumber) {
                if (!is_int($allowedNumber))
                    throw new Erebot_Module_Countdown_InvalidValue(
                        '$allowedNumbers',
                        'array of int',
                        'array of '.typeof($allowedNumber)
                    );
                if ($allowedNumber < 1)
                    throw new Erebot_Module_Countdown_InvalidValue(
                        '$allowedNumbers',
                        'array of int >= 1',
                        $allowedNumber
                    );
            }
            $this->_allowedNumbers = $allowedNumbers;
        }
        $this->_bestProposal = NULL;
        $this->_chooseNumbers($nbNumbers);
    }

    protected function _chooseNumbers($nbNumbers)
    {
        $this->_numbers = array();
        for ($i = 0; $i < $nbNumbers; $i++) {
            $key = array_rand($this->_allowedNumbers);
            $this->_numbers[] = $this->_allowedNumbers[$key];
        }

        $this->_target = mt_rand($this->_minTarget, $this->_maxTarget);
    }

    public function __destruct()
    {
        unset($this->_bestProposal);
    }

    public function getNumbers()
    {
        return $this->_numbers;
    }

    public function getTarget()
    {
        return $this->_target;
    }

    public function & getBestProposal()
    {
        return $this->_bestProposal;
    }

    /// @TODO: write an interface for formulae and use it there.
    public function proposeFormula(Erebot_Module_Countdown_Formula &$formula)
    {
        $gameNumbers    = $this->_numbers;
        $formulaNumbers = $formula->getNumbers();

        foreach ($formulaNumbers as $number) {
            $key = array_search($number, $gameNumbers);
            if ($key === FALSE)
                throw new Erebot_Module_Countdown_UnavailableNumberException();
            unset($gameNumbers[$key]);
        }

        if ($this->_bestProposal === NULL) {
            $this->_bestProposal =&  $formula;
            return TRUE;
        }

        $oldDst = abs($this->_bestProposal->getResult() - $this->_target);
        $newDst = abs($formula->getResult() - $this->_target);
        if ($newDst < $oldDst) {
            $this->_bestProposal =&  $formula;
            return TRUE;
        }

        return FALSE;
    }
}

