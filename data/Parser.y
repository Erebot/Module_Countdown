%name Erebot_Module_Countdown_Parser_
%declare_class {class Erebot_Module_Countdown_Parser}
%syntax_error { throw new Erebot_Module_Countdown_SyntaxErrorException(); }
%token_prefix TK_
%include_class {
    private $_formulaResult = NULL;
    public function getResult() { return $this->_formulaResult; }
}

%left OP_ADD OP_SUB.
%left OP_MUL OP_DIV.

formula ::= expr(e).                        { $this->_formulaResult = e; }

expr(res) ::= PAR_OPEN expr(e) PAR_CLOSE.   { res = e; }
expr(res) ::= expr(opd1) OP_ADD expr(opd2). { res = opd1 + opd2; }
expr(res) ::= expr(opd1) OP_SUB expr(opd2). { res = opd1 - opd2; }
expr(res) ::= expr(opd1) OP_MUL expr(opd2). { res = opd1 * opd2; }
expr(res) ::= expr(opd1) OP_DIV expr(opd2). {
    if (!opd2)
        throw new Erebot_Module_Countdown_DivisionByZeroException();

    if (opd1 % opd2)
        throw new Erebot_Module_Countdown_NonIntegralDivisionException();

    res = opd1 / opd2;
}
expr(res) ::= INTEGER(i).                   { res = i; }

