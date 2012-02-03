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
 *      An exception thrown whenever an
 *      unexpected value was given.
 */
class   Erebot_Module_Countdown_InvalidValueException
extends Erebot_Module_Countdown_Exception
{
    /// Location where some value expected.
    protected $_location;

    /// The value that was expected.
    protected $_expectedData;

    /// THe actual value received.
    protected $_givenData;


    /**
     * Constructs the exception.
     *
     * \param string $location
     *      Where the value was expected.
     *
     * \param mixed $expected
     *      The value that was expected.
     *
     * \param mixed $given
     *      The value that was actually received.
     */
    public function __construct($location, $expected, $given)
    {
        $this->_location        = $location;
        $this->_expectedData    = $expected;
        $this->_givenData       = $given;

        parent::__construct(
            sprintf(
                "Invalid value, expected %s, got %s for %s",
                $this->_expectedData,
                $this->_givenData,
                $this->_location
            )
        );
    }

    /**
     * Returns the location where the value
     * was expected.
     *
     * \retval string
     *      Location where the value was expected.
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * Returns the value that was expected.
     *
     * \retval mixed
     *      Expected value.
     */
    public function getExpectedData()
    {
        return $this->_expectedData;
    }

    /**
     * Returns the value that was received.
     *
     * \retval mixed
     *      Received value.
     */
    public function getGivenData()
    {
        return $this->_givenData;
    }
}

