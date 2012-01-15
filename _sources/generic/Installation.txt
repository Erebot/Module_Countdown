Installation
============

This pages contains instructions on how to install this module on your machine.
There are several ways to achieve that. Each method is described below.

..  warning::
    You cannot mix the different methods. Especially, **you must use the same
    method to install this module as the one you selected for Erebot itself**.

..  contents::

..  note::
    We recommend that you install this module using `Erebot's PEAR channel`_.
    This will result in a system-wide installation which can be upgraded
    very easily later.
    If this is not feasible for you or if you prefer to keep the installation
    local (for a single user), we recommend that you go the PHAR way.
    Installation from sources is reserved for advanced installations
    (developers).


Installation from Erebot's PEAR channel
---------------------------------------

This is by far the simplest way to install the module.
Hence, it's the recommended way for beginners.
Just use whatever tool your distribution provides to manage PEAR packages:

* Either `pear`_ (traditionnal tool)
* or `pyrus`_ (new experimental tool meant to replace pear someday)

..  warning::
    Pyrus currently has issues with some PEAR packages. It is thus recommended
    that you use the regular pear tool to install Erebot.
    See https://github.com/pyrus/Pyrus/issues/26 for more information.

You can install (**as a privileged user**) either the latest stable release
using a command such as:

..  parsed-code:: bash

        $ pear channel-discover pear.erebot.net
        $ pear install erebot/|project|

... or you can install the latest unstable version instead, using:

..  parsed-code:: bash

        $ pear channel-discover pear.erebot.net
        $ pear install erebot/|project|-alpha

Please note that the ``channel-discover`` command needs to be run only once
(pear and pyrus will refuse to discover a PEAR channel more than once anyway).
To use Pyrus to manage PEAR packages instead of the regular Pear tool,
just replace ``pear`` with ``pyrus`` in the commands above.


Installation using PHAR archives
--------------------------------

Installing |project| from a PHAR archive is very easy.
However, please note that Erebot must have been installed as a PHAR archive
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


Downloading the achive
~~~~~~~~~~~~~~~~~~~~~~

First, select the version you want to install. Available versions are listed
on `Erebot's PEAR channel`_.

The PHAR archive for a certain version can be downloaded by using a URL
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

Once you've selected and downloaded a release. Just drop the PHAR archive
in the ``modules/`` directory we `previously created`_.

Therefore, you're installation should look somewhat like that:

    * Erebot/
        * Erebot-X.Y.Z.phar
        * modules/
            * |project|-|release|.phar

That's all folks! You can now add `configuration options`_ for this module
in Erebot's configuration file.


Installation from source
------------------------

First, make sure a git client is installed on your machine.
Under Linux, **from a root shell**, run the command that most closely matches
the tools provided by your distribution:

..  parsed-code:: bash

    # For apt-based distributions such as Debian or Ubuntu
    $ apt-get install git

    # For yum-based distributions such as Fedora / RHEL (RedHat)
    $ yum install git

    # For urpmi-based distributions such as SLES (SuSE) or MES (Mandriva)
    $ urpmi git

..  note::
    Windows users may be interested in installing `Git for Windows`_ to get
    an equivalent git client. Also, make sure that ``git.exe`` is present
    on your account's ``PATH``. If not, you'll have to replace ``git`` by
    the full path to ``git.exe`` on every invocation
    (eg. ``"C:\Program Files\Git\bin\git.exe" clone ...``)

Now, clone the module's repository:

..  parsed-code:: bash

    $ cd /path/to/Erebot/vendor/
    $ git clone git://github.com/fpoirotte/|project|.git

..  note::
    Linux users (especially Erebot developers) may prefer to create a separate
    checkout for each component and then use symbolic links to join them
    together, like this:

    ..  parsed-code:: bash

        $ git clone git://github.com/fpoirotte/|project|.git
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
..  _`Git for Windows`:
    http://code.google.com/p/msysgit/downloads/list
..  _`Erebot's prerequisites`:
    /Erebot/Prerequisites.html
..  _`prerequisites`:
    ../Prerequisites.html
..  _`configuration options`:
    ../Configuration.html

.. vim: ts=4 et

