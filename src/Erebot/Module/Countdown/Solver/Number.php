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
 *      A container for a number that may be used
 *      by the solver.
 */
class       Erebot_Module_Countdown_Solver_Number
implements  Erebot_Module_Countdown_Solver_ContainerInterface
{
    /// The number in this container.
    protected $_value;


    /**
     * Constructs a new container for a number.
     *
     * \param int $value
     *      Value to be stored in this container.
     */
    public function __construct($value)
    {
        if (!is_int($value) || $value <= 0)
            throw new Erebot_Module_Countdown_Exception(
                'Not a strictly positive integer'
            );
        $this->_value = $value;
    }

    /**
     * Returns the value in this container.
     *
     * \retval int
     *      The value stored in this container.
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * String representation of this number.
     *
     * \retval string
     *      Representation of this container
     *      as a string.
     */
    public function __toString()
    {
        return (string) $this->_value;
    }
}

