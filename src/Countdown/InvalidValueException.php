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
 *      An exception thrown whenever an
 *      unexpected value was given.
 */
class InvalidValueException extends \Erebot\Module\Countdown\Exception
{
    /// Location where some value expected.
    protected $location;

    /// The value that was expected.
    protected $expectedData;

    /// THe actual value received.
    protected $givenData;


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
        $this->location       = $location;
        $this->expectedData   = $expected;
        $this->givenData      = $given;

        parent::__construct(
            sprintf(
                "Invalid value, expected %s, got %s for %s",
                $this->expectedData,
                $this->givenData,
                $this->location
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
        return $this->location;
    }

    /**
     * Returns the value that was expected.
     *
     * \retval mixed
     *      Expected value.
     */
    public function getExpectedData()
    {
        return $this->expectedData;
    }

    /**
     * Returns the value that was received.
     *
     * \retval mixed
     *      Received value.
     */
    public function getGivenData()
    {
        return $this->givenData;
    }
}
