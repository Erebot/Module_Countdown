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
 *      A lexer (tokenizer) for formulae.
 */
class Erebot_Module_Countdown_Lexer
{
    /// Formula to be tokenized.
    protected $_formula;

    /// Length of the formula.
    protected $_length;

    /// Current position in the formula.
    protected $_position;

    /// Parser for the formula.
    protected $_parser;

    /// List of number used in the formula.
    protected $_numbers;

    /// A pattern used to recognize integers.
    const PATT_INTEGER  = '/^[0-9]+/';


    /**
     * Constructs a new lexer for some formula.
     *
     * \param string $formula
     *      Some formula to tokenize.
     */
    public function __construct($formula)
    {
        $this->_formula     = $formula;
        $this->_length      = strlen($formula);
        $this->_position    = 0;
        $this->_numbers     = array();
        $this->_parser      = new Erebot_Module_Countdown_Parser();
        $this->_tokenize();
    }

    /**
     * Returns the result of the formula.
     *
     * \retval int
     *      Result of the formula.
     */
    public function getResult()
    {
        return $this->_parser->getResult();
    }

    /**
     * Returns a list with all numbers used
     * in the formula.
     * A number may appear twice (or more) if it
     * was used twice (or more) in the formula.
     *
     * \retval list
     *      A list with the numbers used in
     *      the formula.
     */
    public function getNumbers()
    {
        return $this->_numbers;
    }

    /// Does the actual work.
    protected function _tokenize()
    {
        $operators = array(
            '(' =>  Erebot_Module_Countdown_Parser::TK_PAR_OPEN,
            ')' =>  Erebot_Module_Countdown_Parser::TK_PAR_CLOSE,
            '+' =>  Erebot_Module_Countdown_Parser::TK_OP_ADD,
            '-' =>  Erebot_Module_Countdown_Parser::TK_OP_SUB,
            '*' =>  Erebot_Module_Countdown_Parser::TK_OP_MUL,
            '/' =>  Erebot_Module_Countdown_Parser::TK_OP_DIV,

        );

        while ($this->_position < $this->_length) {
            $c          = $this->_formula[$this->_position];
            $subject    = substr($this->_formula, $this->_position);

            if (isset($operators[$c])) {
                $this->_parser->doParse($operators[$c], $c);
                $this->_position++;
                continue;
            }

            if (preg_match(self::PATT_INTEGER, $subject, $matches)) {
                $this->_position += strlen($matches[0]);
                $integer = (int) $matches[0];
                $this->_numbers[] = $integer;
                $this->_parser->doParse(
                    Erebot_Module_Countdown_Parser::TK_INTEGER,
                    $integer
                );
                continue;
            }

            // This will likely result in an exception
            // being thrown, which is actually good!
            $this->_parser->doParse(0, 0);
        }

        // End of tokenization.
        $this->_parser->doParse(0, 0);
    }
}

