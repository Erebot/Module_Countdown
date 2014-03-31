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

namespace Erebot\Module\Countdown\Solver;

/**
 * \brief
 *      An operation to be computed by the solver.
 */
class Operation implements \Erebot\Module\Countdown\Solver\ContainerInterface
{
    /// First operand for the operation.
    protected $first;

    /// Second operand for the operation.
    protected $second;

    /// Operator to use in the operation.
    protected $operator;

    /// Result of the operation.
    protected $value;


    /**
     * Computes a new operation.
     *
     * \param Erebot::Module::Countdown::Solver::ContainerInterface $first
     *      First operand for the operation.
     *
     * \param Erebot::Module::Countdown::Solver::ContainerInterface $second
     *      Second operand for the operation.
     *
     * \param string $operator
     *      Operator to be used in the operation.
     *
     * \throw Erebot::Module::Countdown::Solver::SkipException
     *      The operation is useless (overly simple).
     *
     * \throw Erebot::Module::Countdown::Exception
     *      An invalid operator was given.
     */
    public function __construct(
        \Erebot\Module\Countdown\Solver\ContainerInterface   $first,
        \Erebot\Module\Countdown\Solver\ContainerInterface   $second,
        $operator
    ) {
        $this->first    = $first;
        $this->second   = $second;

        switch ($operator) {
            case '+':
                $this->value = $first->getValue() + $second->getValue();
                break;
            case '-':
                $this->value = $first->getValue() - $second->getValue();
                break;
            case '*':
                if ($second->getValue() == 1) {
                    throw new \Erebot\Module\Countdown\Solver\SkipException(
                        'Skipped'
                    );
                }
                $this->value = $first->getValue() * $second->getValue();
                break;
            case '/':
                if ($second->getValue() == 1) {
                    throw new \Erebot\Module\Countdown\Solver\SkipException(
                        'Skipped'
                    );
                }
                $this->value = $first->getValue() / $second->getValue();
                break;
            default:
                throw new \Erebot\Module\Countdown\Exception('Invalid operator');
        }

        // Negative or non-integral results.
        if ($this->value <= 0 || !is_int($this->value)) {
            throw new \Erebot\Module\Countdown\Solver\SkipException('Skipped');
        }
        $this->operator = $operator;
    }

    /**
     * Returns the first operand for the operation.
     *
     * \retval Erebot::Module::Countdown::Solver::ContainerInterface
     *      First operand for the operation.
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * Returns the second operand for the operation.
     *
     * \retval Erebot::Module::Countdown::Solver::ContainerInterface
     *      Second operand for the operation.
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Returns the value for this operation.
     *
     * \retval int
     *      Result of this operation.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns a string representation for this operation.
     *
     * \retval string
     *      A representation of this operation.
     */
    public function __toString()
    {
        return '('.
            ((string) $this->first).
            $this->operator.
            ((string) $this->second).
        ')';
    }
}
