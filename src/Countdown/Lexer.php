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
 *      A lexer (tokenizer) for formulae.
 */
class Lexer
{
    /// Formula to be tokenized.
    protected $formula;

    /// Length of the formula.
    protected $length;

    /// Current position in the formula.
    protected $position;

    /// Parser for the formula.
    protected $parser;

    /// List of number used in the formula.
    protected $numbers;

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
        $this->formula  = $formula;
        $this->length   = strlen($formula);
        $this->position = 0;
        $this->numbers  = array();
        $this->parser   = new \Erebot\Module\Countdown\Parser();
        $this->tokenize();
    }

    /**
     * Returns the result of the formula.
     *
     * \retval int
     *      Result of the formula.
     */
    public function getResult()
    {
        return $this->parser->getResult();
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
        return $this->numbers;
    }

    /// Does the actual work.
    protected function tokenize()
    {
        $operators = array(
            '(' =>  \Erebot\Module\Countdown\Parser::TK_PAR_OPEN,
            ')' =>  \Erebot\Module\Countdown\Parser::TK_PAR_CLOSE,
            '+' =>  \Erebot\Module\Countdown\Parser::TK_OP_ADD,
            '-' =>  \Erebot\Module\Countdown\Parser::TK_OP_SUB,
            '*' =>  \Erebot\Module\Countdown\Parser::TK_OP_MUL,
            '/' =>  \Erebot\Module\Countdown\Parser::TK_OP_DIV,

        );

        while ($this->position < $this->length) {
            $c          = $this->formula[$this->position];
            $subject    = substr($this->formula, $this->position);

            if (isset($operators[$c])) {
                $this->parser->doParse($operators[$c], $c);
                $this->position++;
                continue;
            }

            if (preg_match(self::PATT_INTEGER, $subject, $matches)) {
                $this->position += strlen($matches[0]);
                $integer = (int) $matches[0];
                $this->numbers[] = $integer;
                $this->parser->doParse(
                    \Erebot\Module\Countdown\Parser::TK_INTEGER,
                    $integer
                );
                continue;
            }

            // This will likely result in an exception
            // being thrown, which is actually good!
            $this->parser->doParse(0, 0);
        }

        // End of tokenization.
        $this->parser->doParse(0, 0);
    }
}
