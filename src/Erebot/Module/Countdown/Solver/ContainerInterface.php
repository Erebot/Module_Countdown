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
 *      Interface for a generic container
 *      used by the solver.
 */
interface Erebot_Module_Countdown_Solver_ContainerInterface
{
    /**
     * Returns the value enclosed in this container.
     *
     * \retval mixed
     *      The value kept by this container.
     */
    public function getValue();

    /**
     * Returns the string representation of this
     * container's content.
     *
     * \retval string
     *      String representation of this container.
     */
    public function __toString();
}

