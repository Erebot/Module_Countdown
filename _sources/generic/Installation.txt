Installation
============

This pages contains instructions on how to install this module on your machine.
There are several ways to achieve that. Each method is described below.

..  warning::

    You cannot mix the different methods. Especially, **you must use the same
    method to install this module as the one you selected for Erebot itself**.

..  contents::

..  note::

    We recommend that you install this module using either its
    :ref:`PHAR package <Installation using PHAR packages>`
    or through :ref:`composer <Installation through composer>`_.
    Installation from sources is reserved for advanced installations
    (eg. Erebot developers).


Installation using PHAR packages
--------------------------------

Installing |project| from a PHAR package is very easy.
However, please note that Erebot must have been installed as a PHAR package
for this method to work properly.

..  _`previously created`:

Preparations
~~~~~~~~~~~~

If you haven't done so already, create a directory in Erebot's folder
named ``modules``.

Hence, your tree should look like this:

    * Erebot/
        * Erebot-X.Y.Z.phar
        * modules/

Also, make sure your installation fulfills all of the `prerequisites`_
for this module.


Downloading the package
~~~~~~~~~~~~~~~~~~~~~~~

First, select the version you want to install. Available versions are listed
on `Erebot's PEAR channel`_.

The PHAR package for a certain version can be downloaded by using a URL
such as |project_version| (replace `version` with the actual version you
selected).

As a special shortcut, the following link always points to the latest snapshot
of |project|: |project_latest|.

..  warning::

    Using the latest snapshot available means that you may benefit from
    very recent developments, but it also means that the code may be in
    an unstable state. Use at your own risk.


Installation
~~~~~~~~~~~~

Once you've selected and downloaded a release. Just drop the PHAR package
in the ``modules/`` directory we `previously created`_.

Therefore, your installation should look somewhat like that:

    * Erebot/
        * Erebot-X.Y.Z.phar
        * modules/
            * |project|-|release|.phar

That's all folks! You can now add `configuration options`_ for this module
in Erebot's configuration file.


Installation through composer
-----------------------------

Installation through `composer <http://getcomposer.org/>` is very easy.

First, make sure the git client is installed on your machine.

..  include: Installation_git

Now that git has been installed, we can proceed with the module's installation:

*   Go to the directory where you installed Erebot.
*   Edit :file:`composer.json` and add |project| to the list of
    required dependencies.
*   Update your installation with:

    ..  parsed-code:: bash

        $ php composer.phar install

*   Enjoy!

You can now add `configuration options`_ for this module in Erebot's
configuration file.


Installation from source
------------------------

First, make sure the git client is installed on your machine.

..  include: Installation_git

Now, clone the module's repository:

..  parsed-code:: bash

    $ cd /path/to/Erebot/vendor/
    $ git clone git://github.com/Erebot/|project|.git

..  note::

    Linux users (especially Erebot developers) may prefer to create a separate
    checkout for each component and then use symbolic links to join them
    together, like this:

    ..  parsed-code:: bash

        $ git clone git://github.com/Erebot/|project|.git
        $ cd Erebot/vendor/
        $ ln -s ../../|project|

Optionally, you can compile the translation files for each component.
However, this requires that `gettext`_ and `phing`_ be installed on your machine
as well. See the documentation on `Erebot's prerequisites`_ for additional
information on how to install these tools depending on your system.

Depending on the module, other additional tools may be required.
Check out this module's `prerequisites`_ for more information.

Once you got those two up and running, the translation files can be compiled
using these commands:

..  parsed-code:: bash

    $ cd /path/to/Erebot/vendor/|project|
    $ phing


..  _`pear`:
    http://pear.php.net/package/PEAR
..  _`Pyrus`:
    http://pyrus.net/
..  _`Erebot's PEAR channel`:
    https://pear.erebot.net/
..  _`gettext`:
    http://www.gnu.org/s/gettext/
..  _`Phing`:
    http://www.phing.info/
..  _`Erebot's prerequisites`:
    /Erebot/Prerequisites.html
..  _`prerequisites`:
    ../Prerequisites.html
..  _`configuration options`:
    ../Configuration.html

.. vim: ts=4 et

