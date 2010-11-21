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

class   Erebot_Module_Countdown
extends Erebot_Module_Base
{
    static protected $_metadata = array(
        'requires'  =>  array(
            'Erebot_Module_TriggerRegistry',
            'Erebot_Module_Helper',
        ),
    );
    protected $_trigger;
    protected $_startHandler;
    protected $_rawHandler;
    protected $_game;

    const FORMULA_FILTER    = '@^[\\(\\)\\-\\+\\*/0-9 ]+$@';

    public function reload($flags)
    {
        if ($flags & self::RELOAD_HANDLERS) {
            $registry   = $this->_connection->getModule(
                'Erebot_Module_TriggerRegistry',
                Erebot_Connection::MODULE_BY_NAME
            );
            $matchAny  = Erebot_Utils::getVStatic($registry, 'MATCH_ANY');

            if (!($flags & self::RELOAD_INIT)) {
                $this->connection->removeEventHandler($this->startHandler);
                $registry->freeTriggers($this->_trigger, $matchAny);
            }

            $trigger        = $this->parseString('trigger', 'countdown');
            $this->_trigger = $registry->registerTriggers($trigger, $matchAny);
            if ($this->_trigger === NULL)
                throw new Exception($this->_translator->gettext(
                    'Could not register Countdown trigger'));

            $targets    = new Erebot_EventTarget(Erebot_EventTarget::ORDER_ALLOW_DENY);
            $targets->addRule(
                Erebot_EventTarget::TYPE_ALLOW,
                Erebot_EventTarget::MATCH_ALL,
                Erebot_EventTarget::MATCH_CHANNEL);

            $filter                 = new Erebot_TextFilter(
                                            $this->_mainCfg,
                                            Erebot_TextFilter::TYPE_STATIC,
                                            $trigger, TRUE);
            $this->_startHandler    = new Erebot_EventHandler(
                                                array($this, 'handleCountdown'),
                                                'Erebot_Event_ChanText',
                                                $targets, $filter);
            $this->_connection->addEventHandler($this->_startHandler);

            $targets            = new Erebot_EventTarget(Erebot_EventTarget::ORDER_ALLOW_DENY);
            $filter             = new Erebot_TextFilter(
                                        $this->_mainCfg,
                                        Erebot_TextFilter::TYPE_REGEXP,
                                        self::FORMULA_FILTER, FALSE);
            $this->_rawHandler  = new Erebot_EventHandler(
                                            array($this, 'handleRawText'),
                                            'Erebot_Event_ChanText',
                                            $targets, $filter);
            $this->_connection->addEventHandler($this->_rawHandler);
            $this->registerHelpMethod(array($this, 'getHelp'));
        }
    }

    public function getHelp(Erebot_Interface_Event_TextMessage &$event, $words)
    {
        if ($event instanceof Erebot_Interface_Event_Private) {
            $target = $event->getSource();
            $chan   = NULL;
        }
        else
            $target = $chan = $event->getChan();

        $translator = $this->getTranslator($chan);
        $trigger    = $this->parseString('trigger', 'countdown');

        $bot        =&  $this->_connection->getBot();
        $moduleName =   strtolower($bot->moduleClassToName($this));
        $nbArgs     =   count($words);

        if ($nbArgs == 1 && $words[0] == $moduleName) {
            $msg = $translator->gettext('
Provides the <b><var name="trigger"/></b> command which starts
a new Countdown game where contestants must propose a formula
to be as close as possible to a given number.
');
            $formatter = new Erebot_Styling($msg, $translator);
            $formatter->assign('trigger', $trigger);
            $this->sendMessage($target, $formatter->render());
            return TRUE;
        }

        if ($nbArgs < 2)
            return FALSE;

        if ($words[1] == $trigger) {
            $msg = $translator->gettext("
<b>Usage:</b> !<var name='trigger'/>.
Starts a new Countdown game. Given a set of numbers and a target result,
contestants must propose formulae to be as close as possible to the result.
The first one to get the target result or the closest result wins the game.
");
            $formatter = new Erebot_Styling($msg, $translator);
            $formatter->assign('trigger', $trigger);
            $this->sendMessage($target, $formatter->render());

            $msg = $translator->gettext("
Formulae must be given with the usual notation (eg. '(100+2) * 4 /2 - 7').
The four basic operators (+, -, *, /) and parenthesis are supported.
Non-integral divisions (eg. 5/2) are forbidden.
");
            $formatter = new Erebot_Styling($msg, $translator);
            $this->sendMessage($target, $formatter->render());

            return TRUE;
        }
    }

    public function handleCountdown(Erebot_Interface_Event_Generic &$event)
    {
        $chan       = $event->getChan();
        $translator = $this->getTranslator($chan);

        if (isset($this->_game[$chan])) {
            // Display current status.
            $game   =&  $this->_game[$chan]['game'];
            $msg    =   $translator->gettext('You must get <b><var name="target"/>'.
                            '</b> using the following numbers: '.
                            '<for from="numbers" item="number"><b><var '.
                            'name="number"/></b></for>.');
            $tpl    = new Erebot_Styling($msg, $translator);
            $tpl->assign('target',      $game->getTarget());
            $tpl->assign('numbers',     $game->getNumbers());
            $this->sendMessage($chan, $tpl->render());
            $best = $game->getBestProposal();
            if ($best === NULL)
                return;

            $msg    = $translator->gettext('So far, <b><var name="nick"/></b> has '.
                            'achieved <b><var name="result"/></b> using this '.
                            'formula: <b><var name="formula"/></b>');
            $tpl    = new Erebot_Styling($msg, $translator);
            $tpl->assign('nick',    $best->getOwner());
            $tpl->assign('result',  $best->getResult());
            $tpl->assign('formula', $best->getFormula());
            $this->sendMessage($chan, $tpl->render());
            return;
        }

        $minTarget  = $this->parseInt('minimum', 100);
        $maxTarget  = $this->parseInt('maximum', 999);
        $nbNumbers  = $this->parseInt('numbers', 7);
        $allowed    = $this->parseString('allowed', '1 2 3 4 5 6 7 8 9 10 25 50 75 100');
        $allowed    = array_map('intval', array_filter(explode(' ', $allowed)));

        $game   =   new Erebot_Module_Countdown_Game($minTarget, $maxTarget, $nbNumbers, $allowed);
        $delay  =   $this->parseInt('delay', 60);
        $msg    =   $translator->gettext('A new Countdown game has been started. '.
                        'You must get <b><var name="target"/></b> using the '.
                        'following numbers <for from="numbers" item="number">'.
                        '<b><var name="number"/></b></for>. You have <var '.
                        'name="delay"/> seconds to make suggestions.');
        $tpl    = new Erebot_Styling($msg, $translator);
        $tpl->assign('target',  $game->getTarget());
        $tpl->assign('numbers', $game->getNumbers());
        $tpl->assign('delay',   $delay);
        $this->sendMessage($chan, $tpl->render());

        $timer  = new Erebot_Timer(array($this, 'handleTimeOut'), $delay, FALSE);
        $this->_game[$chan] = array(
            'game'  => $game,
            'timer' => $timer,
        );
        $this->addTimer($timer);

        $targets =& $this->_rawHandler->getTargets();
        $targets->addRule(
            Erebot_EventTarget::TYPE_ALLOW,
            Erebot_EventTarget::MATCH_ALL,
            $chan
        );
    }

    public function handleRawText(Erebot_Interface_Event_Generic &$event)
    {
        $chan       = $event->getChan();
        $nick       = $event->getSource();
        $text       = (string) $event->getText();
        $translator = $this->getTranslator($chan);

        try {
            $formula = new Erebot_Module_Countdown_Formula($nick, $text);
        }
        catch (Erebot_Module_Countdown_FormulaMustBeAStringException $e) {
            throw new Exception($translator->gettext(
                'Expected the formula to be a string'));
        }
        catch (Erebot_Module_Countdown_DivisionByZeroException $e) {
            return $this->sendMessage($chan, $translator->gettext(
                'Division by zero'));
        }
        catch (Erebot_Module_Countdown_NonIntegralDivisionException $e) {
            return $this->sendMessage($chan, $translator->gettext(
                'Non integral division'));
        }
        catch (Erebot_Module_Countdown_SyntaxErrorException $e) {
            return $this->sendMessage($chan, $translator->gettext(
                'Syntax error'));
        }

        $game   =&  $this->_game[$chan]['game'];
        try {
            $best = $game->proposeFormula($formula);
        }
        catch (Erebot_Module_Countdown_UnavailableNumberException $e) {
            return $this->sendMessage($chan, $translator->gettext(
                'No such number or number already used'));
        }

        if ($best) {
            if ($formula->getResult() == $game->getTarget()) {
                $msg    =   $translator->gettext('<b>BINGO! <var name="nick"/></b> '.
                                'has achieved <b><var name="result"/></b> with '.
                                'this formula: <b><var name="formula"/></b>.');
                $tpl    = new Erebot_Styling($msg, $translator);
                $tpl->assign('nick',    $nick);
                $tpl->assign('result',  $formula->getResult());
                $tpl->assign('formula', $formula->getFormula());
                $this->sendMessage($chan, $tpl->render());

                $this->removeTimer($this->_game[$chan]['timer']);
                $targets =& $this->_rawHandler->getTargets();
                $targets->removeRule(
                    Erebot_EventTarget::TYPE_ALLOW,
                    Erebot_EventTarget::MATCH_ALL,
                    $chan
                );
                unset($this->_game[$chan]);
                return;
            }

            $msg    = $translator->gettext(
                'Congratulations <b><var name="nick"/></b>! You\'re '.
                'the closest with <b><var name="result"/></b>.');
            $tpl    = new Erebot_Styling($msg, $translator);
            $tpl->assign('nick',    $nick);
            $tpl->assign('result',  $formula->getResult());
            $this->sendMessage($chan, $tpl->render());
            return;
        }

        $msg    =   $translator->gettext('Not bad <b><var name="nick"/></b>, you '.
                        'actually got <b><var name="result"/></b>, but this '.
                        'is not the best formula... Try again ;)');
        $tpl    = new Erebot_Styling($msg, $translator);
        $tpl->assign('nick',    $nick);
        $tpl->assign('result',  $formula->getResult());
        $this->sendMessage($chan, $tpl->render());
    }

    public function handleTimeOut(Erebot_Interface_Timer &$timer)
    {
        $chan = $game = NULL;
        foreach ($this->_game as $key => &$data) {
            if ($data['timer'] === $timer) {
                $chan =     $key;
                $game =&    $data['game'];
                break;
            }
        }

        $this->removeTimer($timer);
        if ($chan === NULL)
            return;

        $translator = $this->getTranslator($chan);
        $targets =& $this->_rawHandler->getTargets();
        $targets->removeRule(
            Erebot_EventTarget::TYPE_ALLOW,
            Erebot_EventTarget::MATCH_ALL,
            $chan
        );
        unset($this->_game[$chan]);
        unset($key, $data);

        $best =& $game->getBestProposal();
        if ($best === NULL) {
            $msg = $translator->gettext("Time's up! Nobody has made any suggestion. :(");
            $this->sendMessage($chan, $msg);
            unset($chan, $game);
            return;
        }

        $msg    =   $translator->gettext('Congratulations to <b><var name="nick"/>'.
                        '</b> who wins this Countdown game. <b><var name="'.
                        'nick"/></b> has got <b><var name="result"/></b> with '.
                        'this formula: <b><var name="formula"/></b>.');
        $tpl    = new Erebot_Styling($msg, $translator);
        $tpl->assign('nick',    $best->getOwner());
        $tpl->assign('result',  $best->getResult());
        $tpl->assign('formula', $best->getFormula());
        $this->sendMessage($chan, $tpl->render());

        unset($chan, $game);
    }
}

