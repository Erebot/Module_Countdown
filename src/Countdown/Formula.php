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
 *      A class that stores information about a formula.
 */
class Formula
{
    /// Lexer that was used to analyze the formula.
    protected $lexer;

    /// Owner of this formula.
    protected $owner;

    /// The actual formula, as a string.
    protected $formula;


    /**
     * Constructs the representation for a new
     * formula.
     *
     * \param mixed $owner
     *      Some data that identifies the owner
     *      of this formula.
     *
     * \param string $formula
     *      The actual formula (eg. "5 * 4 + 1").
     *
     * \throw Erebot::Module::Countdown::FormulaMustBeAStringException
     *      The given "formula" is not a string
     *      or is an empty string.
     */
    public function __construct($owner, $formula)
    {
        if (!is_string($formula) || $formula == '') {
            throw new \Erebot\Module\Countdown\FormulaMustBeAStringException();
        }

        $this->owner    = $owner;
        $this->formula  = $formula;
        $formula        = str_replace(' ', '', $formula);
        $this->lexer    = new \Erebot\Module\Countdown\Lexer($formula);
    }

    /// Destructs the formula.
    public function __destruct()
    {
        unset($this->lexer);
    }

    /**
     * Returns the result of this formula.
     *
     * \retval int
     *      Result of this formula.
     */
    public function getResult()
    {
        return $this->lexer->getResult();
    }

    /**
     * Returns a list of the numbers used
     * in this formula.
     * A number may appear several times in this list
     * if it was used several times in the formula.
     *
     * \retval list
     *      A list with the numbers used in this
     *      formula.
     */
    public function getNumbers()
    {
        return $this->lexer->getNumbers();
    }

    /**
     * Returns the formula, as a string.
     *
     * \retval string
     *      The formula, exactly as it was
     *      given to this class' constructor.
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Returns the owner of this formula.
     *
     * \retval mixed
     *      Owner of this formula (as given
     *      to this class' constructor).
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
