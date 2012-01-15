Configuration
=============

.. _`configuration options`:

Options
-------

This module provides several configuration options.

..  table:: Options for |project|

    +---------------+-----------+-------------------+-------------------------------+
    | Name          | Type      | Default value     | Description                   |
    +===============+===========+===================+===============================+
    | allowed       | string    | "1 2 3 4 5 6 7 8  | A space-separated list of     |
    |               |           | 9 10 25 50 75     | numbers from which the bot    |
    |               |           | 100"              | will randomly select values   |
    |               |           |                   | meant to help contestants.    |
    +---------------+-----------+-------------------+-------------------------------+
    | delay         | integer   | 60                | How many seconds contestants  |
    |               |           |                   | have before the game ends.    |
    +---------------+-----------+-------------------+-------------------------------+
    | maximum       | integer   | 999               | The target number may not     |
    |               |           |                   | exceed this value.            |
    +---------------+-----------+-------------------+-------------------------------+
    | minimum       | integer   | 100               | The target number may not be  |
    |               |           |                   | less than this value.         |
    +---------------+-----------+-------------------+-------------------------------+
    | numbers       | integer   | 7                 | How many numbers will be      |
    |               |           |                   | given to help contestants.    |
    +---------------+-----------+-------------------+-------------------------------+
    | solver        | boolean   | FALSE             | Whether the bot should try to |
    |               |           |                   | solve the game or not.        |
    |               |           |                   | Enabling this option has a    |
    |               |           |                   | great impact on the bot's     |
    |               |           |                   | responsiveness. Only use it   |
    |               |           |                   | if you understand the         |
    |               |           |                   | consequences.                 |
    +---------------+-----------+-------------------+-------------------------------+
    | solver_class  | string    | "|solver_class|"  | The class to use to solve the |
    |               |           |                   | game (useless unless the      |
    |               |           |                   | ``solver`` option is set to   |
    |               |           |                   | TRUE).                        |
    +---------------+-----------+-------------------+-------------------------------+
    | trigger       | string    | "countdown"       | The command to use (without   |
    |               |           |                   | any prefix) to start a new    |
    |               |           |                   | Countdown game. This text     |
    |               |           |                   | should only contain           |
    |               |           |                   | alpha-numeric characters.     |
    +---------------+-----------+-------------------+-------------------------------+


Example
-------

In this example, we enable the |project| module at the general configuration
level. Therefore, the game will be available on all networks/servers/channels.
Of course, you can use a more restrictive configuration file if it suits your
needs better.

..  parsed-code:: xml

    <?xml version="1.0"?>
    <configuration
      xmlns="http://localhost/Erebot/"
      version="..."
      language="fr-FR"
      timezone="Europe/Paris"
      commands-prefix="!">

      <modules>
        <!-- Other modules ignored for clarity. -->

        <!--
          Configure the module:
          - the game will be started using the "!count" command.
          - contestants will have 2 minutes to make suggestions.
        -->
        <module name="Erebot_Module_Countdown">
          <param name="trigger" value="count" />
          <param name="delay"   value="120" />
        </module>
      </modules>
    </configuration>


..  |solver_class| replace:: Erebot_Module_Countdown_Solver

.. vim: ts=4 et
