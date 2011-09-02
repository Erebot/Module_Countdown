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

class Erebot_Module_Countdown_Formula
{
    protected $_lexer;
    protected $_owner;
    protected $_formula;

    public function __construct($owner, $formula)
    {
        if (!is_string($formula) || $formula == '')
            throw new Erebot_Module_Countdown_FormulaMustBeAStringException();

        $this->_owner   = $owner;
        $this->_formula = $formula;
        $formula        = str_replace(' ', '', $formula);
        $this->_lexer   = new Erebot_Module_Countdown_Lexer($formula);
    }

    public function __destruct()
    {
        unset($this->_lexer);
    }

    public function getResult()
    {
        return $this->_lexer->getResult();
    }

    public function getNumbers()
    {
        return $this->_lexer->getNumbers();
    }

    public function getFormula()
    {
        return $this->_formula;
    }

    public function getOwner()
    {
        return $this->_owner;
    }
}

