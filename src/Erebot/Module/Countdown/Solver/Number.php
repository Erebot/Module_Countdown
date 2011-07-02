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

class       Erebot_Module_Countdown_Solver_Number
implements  Erebot_Module_Countdown_Solver_ContainerInterface
{
    protected $_value;

    public function __construct($value)
    {
        if (!is_int($value) || $value <= 0)
            throw new Erebot_Module_Countdown_Exception('Not a strictly positive integer');
        $this->_value = $value;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function __toString()
    {
        return (string) $this->_value;
    }
}

