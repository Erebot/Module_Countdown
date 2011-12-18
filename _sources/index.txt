Welcome to the documentation for Erebot_Module_Countdown!
=========================================================

Erebot_Module_Countdown is a module for `Erebot`_ that provides a game inspired
by the TV show with the same name.
Given a set of numbers and a target number, contestants have 60 seconds
to propose formulae that produce the target number using only number
from the given set and the four basic operators (+, -, *, /).

The winner is the one that managed to get the target number exactly or who
got the closest result.

The listing below shows a game played in french.

..  sourcecode:: irc

    17:29:20 < Erebot> Une nouvelle partie des Chiffres et des Lettres commence. Vous devez obtenir 965 grâce aux nombres suivants : 4, 2, 75, 25, 10, 7 & 8. Vous avez 60 secondes pour faire des propositions.
    17:29:31 < foobar> (75+25-4)*10
    17:29:31 < Erebot> Félicitations foobar ! Vous êtes le plus proche avec 960.
    17:29:37 < foobar> (75+25-4)*10+7-2
    17:29:37 < Erebot> BINGO ! foobar a obtenu 965 avec cette formule : (75+25-4)*10+7-2.


Contents:

..  toctree::
    :maxdepth: 2

    generic/Installation


..  _`Erebot`:
    https://www.erebot.net/
..  _`configuration`:
    Configuration.html

.. vim: ts=4 et
