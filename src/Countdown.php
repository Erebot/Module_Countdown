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

namespace Erebot\Module;

/**
 * \brief
 *      A module for Erebot that provides
 *      an implementation of the Countdown
 *      TV gameshow.
 */
class Countdown extends \Erebot\Module\Base implements \Erebot\Interfaces\HelpEnabled
{
    /// Trigger registered by this module.
    protected $trigger;

    /// Handler used to create a new game.
    protected $startHandler;

    /// Handler used when formulae are proposed.
    protected $rawHandler;

    /// Keeps track of currently running games.
    protected $game;


    /// A pattern that attempts to match valid formulae.
    const FORMULA_FILTER    = '@^[\\(\\)\\-\\+\\*/0-9 ]+$@';


    /**
     * This method is called whenever the module is (re)loaded.
     *
     * \param int $flags
     *      A bitwise OR of the Erebot::Module::Base::RELOAD_*
     *      constants. Your method should take proper actions
     *      depending on the value of those flags.
     *
     * \note
     *      See the documentation on individual RELOAD_*
     *      constants for a list of possible values.
     */
    public function reload($flags)
    {
        if ($flags & self::RELOAD_HANDLERS) {
            $registry   = $this->connection->getModule(
                '\\Erebot\\Module\\TriggerRegistry'
            );

            if (!($flags & self::RELOAD_INIT)) {
                $this->connection->removeEventHandler($this->startHandler);
                $registry->freeTriggers($this->trigger, $registry::MATCH_ANY);
            }

            $trigger        = $this->parseString('trigger', 'countdown');
            $this->trigger = $registry->registerTriggers($trigger, $registry::MATCH_ANY);
            if ($this->trigger === null) {
                $fmt = $this->getFormatter(false);
                throw new \Exception(
                    $fmt->_('Could not register Countdown trigger')
                );
            }

            $this->startHandler    = new \Erebot\EventHandler(
                \Erebot\CallableWrapper::wrap(array($this, 'handleCountdown')),
                new \Erebot\Event\Match\All(
                    new \Erebot\Event\Match\Type('\\Erebot\\Event\\ChanText'),
                    new \Erebot\Event\Match\TextStatic($trigger, true)
                )
            );
            $this->connection->addEventHandler($this->startHandler);

            $this->rawHandler  = new \Erebot\EventHandler(
                \Erebot\CallableWrapper::wrap(array($this, 'handleRawText')),
                new \Erebot\Event\Match\All(
                    new \Erebot\Event\Match\Any(),
                    new \Erebot\Event\Match\Type('\\Erebot\\Event\\ChanText'),
                    new \Erebot\Event\Match\TextRegex(self::FORMULA_FILTER)
                )
            );
            $this->connection->addEventHandler($this->rawHandler);
        }

        if ($flags & self::RELOAD_MEMBERS) {
            $this->game = array();
        }
    }

    /**
     * Frees the resources associated with this module.
     */
    protected function unload()
    {
        foreach ($this->game as $entry) {
            if (isset($entry['timer'])) {
                $this->removeTimer($entry['timer']);
            }
        }
    }

    /**
     * Provides help about this module.
     *
     * \param Erebot::Interfaces::Event::Base::TextMessage $event
     *      Some help request.
     *
     * \param Erebot::Interfaces::TextWrapper $words
     *      Parameters passed with the request. This is the same
     *      as this module's name when help is requested on the
     *      module itself (in opposition with help on a specific
     *      command provided by the module).
     */
    public function getHelp(
        \Erebot\Interfaces\Event\Base\TextMessage   $event,
        \Erebot\Interfaces\TextWrapper              $words
    ) {
        if ($event instanceof \Erebot\Interfaces\Event\Base\PrivateMessage) {
            $target = $event->getSource();
            $chan   = null;
        } else {
            $target = $chan = $event->getChan();
        }

        $fmt        = $this->getFormatter($chan);
        $trigger    = $this->parseString('trigger', 'countdown');
        $nbArgs     = count($words);

        if ($nbArgs == 1 && $words[0] === get_called_class()) {
            $msg = $fmt->_(
                'Provides the <b><var name="trigger"/></b> command which '.
                'starts a new Countdown game where contestants must propose '.
                'a formula to be as close as possible to a given number.',
                array('trigger' => $trigger)
            );
            $this->sendMessage($target, $msg);
            return true;
        }

        if ($nbArgs < 2) {
            return false;
        }

        if ($words[1] == $trigger) {
            $msg = $fmt->_(
                "<b>Usage:</b> !<var name='trigger'/>. Starts a new Countdown ".
                "game. Given a set of numbers and a target result, ".
                "contestants must propose formulae to be as close as possible ".
                "to the result. The first one to get the target result or the ".
                "closest result wins the game.",
                array('trigger' => $trigger)
            );
            $this->sendMessage($target, $msg);

            $msg = $fmt->_(
                "Formulae must be given with the usual notation ".
                "(eg. '(100+2) * 4 /2 - 7'). The four basic operators ".
                "(+, -, *, /) and parenthesis are supported. Divisions ".
                "with a remainder (eg. 5/2) are forbidden."
            );
            $this->sendMessage($target, $msg);
            return true;
        }
    }

    /**
     * Handles a request to create a new game.
     *
     * \param Erebot::Interfaces::EventHandler $handler
     *      Handler that triggered this event.
     *
     * \param Erebot::Interfaces::Event::ChanText $event
     *      Request for a new game.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleCountdown(
        \Erebot\Interfaces\EventHandler     $handler,
        \Erebot\Interfaces\Event\ChanText   $event
    ) {
        $chan   = $event->getChan();
        $fmt    = $this->getFormatter($chan);

        if (isset($this->game[$chan])) {
            // Display current status.
            $game   =&  $this->game[$chan]['game'];
            $msg    =   $fmt->_(
                'You must get <b><var name="target"/>'.
                '</b> using the following numbers: '.
                '<for from="numbers" item="number"><b><var '.
                'name="number"/></b></for>.',
                array(
                    'target' => $game->getTarget(),
                    'numbers' => $game->getNumbers(),
                )
            );
            $this->sendMessage($chan, $msg);
            $best = $game->getBestProposal();
            if ($best === null) {
                return;
            }

            $msg = $fmt->_(
                'So far, <b><var name="nick"/></b> has '.
                'achieved <b><var name="result"/></b> using this '.
                'formula: <b><var name="formula"/></b>',
                array(
                    'nick' => $best->getOwner(),
                    'result' => $best->getResult(),
                    'formula' => $best->getFormula(),
                )
            );
            $this->sendMessage($chan, $msg);
            return;
        }

        $minTarget  = $this->parseInt('minimum', 100);
        $maxTarget  = $this->parseInt('maximum', 999);
        $nbNumbers  = $this->parseInt('numbers', 7);
        $allowed    = $this->parseString(
            'allowed',
            '1 2 3 4 5 6 7 8 9 10 25 50 75 100'
        );
        $allowed    = array_map('intval', array_filter(explode(' ', $allowed)));

        $game   =   new \Erebot\Module\Countdown\Game(
            $minTarget,
            $maxTarget,
            $nbNumbers,
            $allowed
        );

        $delay  =   $this->parseInt('delay', 60);
        $msg    =   $fmt->_(
            'A new Countdown game has been started. '.
            'You must get <b><var name="target"/></b> using the '.
            'following numbers <for from="numbers" item="number">'.
            '<b><var name="number"/></b></for>. You have <var '.
            'name="delay"/> seconds to make suggestions.',
            array(
                'target' => $game->getTarget(),
                'numbers' => $game->getNumbers(),
                'delay' => $delay,
            )
        );
        $this->sendMessage($chan, $msg);

        $timerCls = $this->getFactory('!Timer');
        $timer  = new $timerCls(
            \Erebot\CallableWrapper::wrap(array($this, 'handleTimeOut')),
            $delay,
            false,
            array($chan)
        );
        $this->game[$chan] = array(
            'game'      => $game,
            'timer'     => $timer,
            'filter'    => new \Erebot\Event\Match\Chan($chan)
        );
        $this->addTimer($timer);

        $filter = $this->rawHandler->getFilter();
        $filter[0]->add($this->game[$chan]['filter']);
        unset($filter);
    }

    /**
     * Handles a formula proposition.
     *
     * \param Erebot::Interfaces::EventHandler $handler
     *      Handler that triggered this event.
     *
     * \param Erebot::Interfaces::Event::Base::ChanText $event
     *      A message containing the formula being proposed.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function handleRawText(
        \Erebot\Interfaces\EventHandler     $handler,
        \Erebot\Interfaces\Event\ChanText   $event
    ) {
        $chan   = $event->getChan();
        $nick   = $event->getSource();
        $text   = (string) $event->getText();
        $fmt    = $this->getFormatter($chan);

        try {
            $formula = new \Erebot\Module\Countdown\Formula($nick, $text);
        } catch (\Erebot\Module\Countdown\FormulaMustBeAStringException $e) {
            return $this->sendMessage(
                $chan,
                $fmt->_('Expected the formula to be a string')
            );
        } catch (\Erebot\Module\Countdown\DivisionByZeroException $e) {
            return $this->sendMessage(
                $chan,
                $fmt->_('Division by zero')
            );
        } catch (\Erebot\Module\Countdown\NonIntegralDivisionException $e) {
            return $this->sendMessage(
                $chan,
                $fmt->_('Non integral division')
            );
        } catch (\Erebot\Module\Countdown\SyntaxErrorException $e) {
            return $this->sendMessage(
                $chan,
                $fmt->_('Syntax error')
            );
        }

        $game   =&  $this->game[$chan]['game'];
        try {
            $best = $game->proposeFormula($formula);
        } catch (\Erebot\Module\Countdown\UnavailableNumberException $e) {
            return $this->sendMessage(
                $chan,
                $fmt->_('No such number or number already used')
            );
        }

        if ($best) {
            if ($formula->getResult() == $game->getTarget()) {
                $msg = $fmt->_(
                    '<b>BINGO! <var name="nick"/></b> '.
                    'has achieved <b><var name="result"/></b> with '.
                    'this formula: <b><var name="formula"/></b>.',
                    array(
                        'nick' => $nick,
                        'result' => $formula->getResult(),
                        'formula' => $formula->getFormula(),
                    )
                );
                $this->sendMessage($chan, $msg);

                $this->removeTimer($this->game[$chan]['timer']);
                $filter = $this->rawHandler->getFilter();
                $filter[0]->remove($this->game[$chan]['filter']);
                unset($this->game[$chan], $filter);
                return;
            }

            $msg = $fmt->_(
                'Congratulations <b><var name="nick"/></b>! '.
                'You\'re the closest with <b><var name="result"/></b>.',
                array(
                    'nick' => $nick,
                    'result' => $formula->getResult(),
                )
            );
            $this->sendMessage($chan, $msg);
            return;
        }

        $msg = $fmt->_(
            'Not bad <b><var name="nick"/></b>, you '.
            'actually got <b><var name="result"/></b>, but this '.
            'is not the best formula... Try again ;)',
            array(
                'nick' => $nick,
                'result' => $formula->getResult(),
            )
        );
        $this->sendMessage($chan, $msg);
    }

    /**
     * Handles the timer that marks the end of the game.
     *
     * \param Erebot::TimerInterface $timer
     *      Timer that marks the end of the game.
     *
     * \param string $chan
     *      Name of the IRC channel where the game
     *      was taking place.
     */
    public function handleTimeOut(\Erebot\TimerInterface $timer, $chan)
    {
        $this->removeTimer($timer);
        if (!isset($this->game[$chan])) {
            return;
        }
        $game =& $this->game[$chan]['game'];

        $fmt    = $this->getFormatter($chan);
        $filter = $this->rawHandler->getFilter();
        $filter[0]->remove($this->game[$chan]['filter']);
        unset($filter, $this->game[$chan]);
        unset($key, $data);

        $best = $game->getBestProposal();
        if ($best === null) {
            $msg = $fmt->_("Time's up! Nobody has made any suggestion. :(");
            $this->sendMessage($chan, $msg);
            unset($chan, $game);
            return;
        }

        $msg =   $fmt->_(
            'Congratulations to <b><var name="nick"/>'.
            '</b> who wins this Countdown game. <b><var name="'.
            'nick"/></b> has got <b><var name="result"/></b> with '.
            'this formula: <b><var name="formula"/></b>.',
            array(
                'nick' => $best->getOwner(),
                'result' => $best->getResult(),
                'formula' => $best->getFormula(),
            )
        );
        $this->sendMessage($chan, $msg);

        $target = $game->getTarget();
        if ($this->parseBool('solver', false) && $best->getResult() != $target) {
            $solverCls = $this->parseString(
                'solver_class',
                '\\Erebot\\Module\\Countdown\\Solver'
            );

            if (!class_exists($solverCls)) {
                unset($chan, $game);
                return;
            }

            $solver = new $solverCls($target, $game->getNumbers());
            if (!($solver instanceof \Erebot\Module\Countdown\SolverInterface)) {
                unset($chan, $game);
                return;
            }

            $solved = $solver->solve();
            // Make sure the solver actually found a better result
            // before nagging the players.
            if (abs($solved->getValue() - $target) <
                abs($best->getResult() - $target)) {
                $msg = $fmt->_(
                    'However, a better result could be achieved '.
                    '(<var name="result"/>) by using this formula: '.
                    '<b><var name="formula"/></b>.',
                    array(
                        'result' => $solved->getValue(),
                        'formula' => (string) $solved,
                    )
                );
                $this->sendMessage($chan, $msg);
            }
        }

        unset($chan, $game);
    }
}
