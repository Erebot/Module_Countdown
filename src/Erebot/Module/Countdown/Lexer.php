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

class Erebot_Module_Countdown_Lexer
{
    protected $_formula;
    protected $_length;
    protected $_position;
    protected $_skip;
    protected $_parser;
    protected $_numbers;

    // Allow stuff such as "1234".
    const PATT_INTEGER  = '/^[0-9]+/';

    public function __construct($formula)
    {
        $this->_formula     = $formula;
        $this->_length      = strlen($formula);
        $this->_position    = 0;
        $this->_numbers     = array();
        $this->_parser      = new Erebot_Module_Countdown_Parser();
        $this->_tokenize();
    }

    public function getResult()
    {
        return $this->_parser->getResult();
    }

    public function getNumbers()
    {
        return $this->_numbers;
    }

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

