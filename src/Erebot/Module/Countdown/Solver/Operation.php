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
 *      An operation to be computed by the solver.
 */
class       Erebot_Module_Countdown_Solver_Operation
implements  Erebot_Module_Countdown_Solver_ContainerInterface
{
    /// First operand for the operation.
    protected $_operand1;

    /// Second operand for the operation.
    protected $_operand2;

    /// Operator to use in the operation.
    protected $_operator;

    /// Result of the operation.
    protected $_value;


    /**
     * Computes a new operation.
     *
     * \param Erebot_Module_Countdown_Solver_ContainerInterface $operand1
     *      First operand for the operation.
     *
     * \param Erebot_Module_Countdown_Solver_ContainerInterface $operand2
     *      Second operand for the operation.
     *
     * \param string $operator
     *      Operator to be used in the operation.
     *
     * \thrown Erebot_Module_Countdown_Solver_SkipException
     *      The operation is useless (overly simple).
     *
     * \thrown Erebot_Module_Countdown_Exception
     *      An invalid operator was given.
     */
    public function __construct(
        Erebot_Module_Countdown_Solver_ContainerInterface   $operand1,
        Erebot_Module_Countdown_Solver_ContainerInterface   $operand2,
                                                            $operator
    )
    {
        $this->_operand1 = $operand1;
        $this->_operand2 = $operand2;

        switch ($operator) {
            case '+':
                $this->_value = $operand1->getValue() + $operand2->getValue();
                break;
            case '-':
                $this->_value = $operand1->getValue() - $operand2->getValue();
                break;
            case '*':
                if ($operand2->getValue() == 1)
                    throw new Erebot_Module_Countdown_Solver_SkipException(
                        'Skipped'
                    );
                $this->_value = $operand1->getValue() * $operand2->getValue();
                break;
            case '/':
                if ($operand2->getValue() == 1)
                    throw new Erebot_Module_Countdown_Solver_SkipException(
                        'Skipped'
                    );
                $this->_value = $operand1->getValue() / $operand2->getValue();
                break;
            default:
                throw new Erebot_Module_Countdown_Exception('Invalid operator');
        }

        // Negative or non-integral results.
        if ($this->_value <= 0 || !is_int($this->_value))
            throw new Erebot_Module_Countdown_Solver_SkipException('Skipped');
        $this->_operator = $operator;
    }

    /**
     * Returns the first operand for the operation.
     *
     * \retval Erebot_Module_Countdown_Solver_ContainerInterface
     *      First operand for the operation.
     */
    public function getOperand1()
    {
        return $this->_operand1;
    }

    /**
     * Returns the second operand for the operation.
     *
     * \retval Erebot_Module_Countdown_Solver_ContainerInterface
     *      Second operand for the operation.
     */
    public function getOperand2()
    {
        return $this->_operand2;
    }

    /**
     * Returns the value for this operation.
     *
     * \retval int
     *      Result of this operation.
     */
    public function getValue()
    {
        return $this->_value;
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
            ((string) $this->_operand1).
            $this->_operator.
            ((string) $this->_operand2).
        ')';
    }
}

