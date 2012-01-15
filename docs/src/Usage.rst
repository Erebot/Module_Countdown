Usage
=====

This section assumes default values are used for all triggers.
Please refer to :ref:`configuration options <configuration options>`
for more information on how to customize triggers.


Provided commands
-----------------

This module provides the following commands:

..  table:: Commands provided by |project|

    +-------------------+---------------------------------------------------+
    | Command           | Description                                       |
    +===================+===================================================+
    | ``!countdown``    | Starts a new game. If a game is already running,  |
    |                   | displays the target number, usable numbers and    |
    |                   | current leader of the game.                       |
    +-------------------+---------------------------------------------------+

Once a new game has been created, contestants can make propositions directly
by sending a new formula in the channel the game was started in.

The four basic operators (+ - / \*) and parenthesis may be used in the formula.


Examples
--------

The listing below shows a game played in french.

..  sourcecode:: irc

    17:29:20 < foobar> !countdown
    17:29:20 < Erebot> Une nouvelle partie des Chiffres et des Lettres commence. Vous devez obtenir 965 grâce aux nombres
                       suivants : 4, 2, 75, 25, 10, 7 & 8. Vous avez 60 secondes pour faire des propositions.
    17:29:31 < foobar> (75+25-4)*10
    17:29:31 < Erebot> Félicitations foobar ! Vous êtes le plus proche avec 960.
    17:29:37 < foobar> (75+25-4)*10+7-2
    17:29:37 < Erebot> BINGO ! foobar a obtenu 965 avec cette formule : (75+25-4)*10+7-2.

..  vim: ts=4 et
