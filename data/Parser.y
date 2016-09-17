%declare_class {class Parser}
%syntax_error { throw new \Erebot\Module\Countdown\SyntaxErrorException(); }
%token_prefix TK_
%include {
    // @codingStandardsIgnoreFile
    namespace Erebot\Module\Countdown;
}
%include_class {
    private $formulaResult = NULL;
    public function getResult() { return $this->formulaResult; }
}

%left OP_ADD OP_SUB.
%left OP_MUL OP_DIV.

formula ::= expr(e).                        { $this->formulaResult = e; }

expr(res) ::= PAR_OPEN expr(e) PAR_CLOSE.   { res = e; }
expr(res) ::= expr(opd1) OP_ADD expr(opd2). { res = opd1 + opd2; }
expr(res) ::= expr(opd1) OP_SUB expr(opd2). { res = opd1 - opd2; }
expr(res) ::= expr(opd1) OP_MUL expr(opd2). { res = opd1 * opd2; }
expr(res) ::= expr(opd1) OP_DIV expr(opd2). {
    if (!opd2) {
        throw new \Erebot\Module\Countdown\DivisionByZeroException();
    }

    if (opd1 % opd2) {
        throw new \Erebot\Module\Countdown\NonIntegralDivisionException();
    }

    res = opd1 / opd2;
}
expr(res) ::= INTEGER(i).                   { res = i; }

