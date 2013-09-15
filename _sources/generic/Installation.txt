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
    :ref:`PHAR package <install_phar>` or through
    :ref:`composer <install_composer>`.
    Installation from sources is reserved for advanced installations
    (eg. Erebot developers).


..  _`install_phar`:

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
on `Erebot's package repository`_.

The PHAR package for a certain version can be downloaded by using a URL
such as |project_version| (replace `version` with the actual version you
selected).

As a special shortcut, the following link always points to the latest snapshot
of |project|: |project_latest|.

..  warning::

    Using the latest snapshot available means that you may benefit from
    very recent developments, but it also means that the code may be in
    an unstable state. Use at your own risk.

The PHAR package must be downloaded to your installation's :file:`modules/`
directory.

Downloading the package's signature
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

All the packages delivered by Erebot's developers are cryptographically signed
using the "OpenSSL" algorithm in PHP's Phar extension.
This signature is used to detect corrupted packages and packages that have been
tampered with.

You must retrieve the signature corresponding to the version of the PHAR
package you downloaded and put it alongside the package.
The signature can be downloaded by appending ``.pubkey`` at the end of the link
to the package itself. Therefore, the signature for the latest version can be
downloaded from |project_latest_pubkey|.

..  note::

    PHP automatically checks the integrity of signed PHAR packages when they
    are loaded. Neither the name of the PHAR package nor the name of the
    signature file should be altered, as the integrity check would then fail.

..  warning::

    Although PHP automatically checks the integrity of cryptographically
    signed phar archives when they are loaded using the signature file, 
    you may also check an archive manually by using the :command:`phar`
    command provided with the phar extension.

    For example, the following session shows a passing result.

    ..  parsed-code:: bash

        $ phar info -f |project|-dev-master.phar
        # Alias:              |project|
        # Hash-type:          OpenSSL
        # ... (other fields removed for clarity) ...

    Note how the "Hash-type" field indicates that the "OpenSSL" algorithm
    has been used to sign the archive. **Any other value should be considered
    as if the check had failed**, unless the package was downloaded
    from Erebot's website over a secure (SSL/TLS) connection.

    On the other hand, the following example shows a session where
    the verification failed.

    ..  parsed-code:: bash

        $ phar info -f |project|-dev-master.phar
        # Exception while opening phar '|project|-dev-master.phar':
        # phar "|project|-dev-master.phar" openssl signature could not be verified: openssl public key could not be read

Conclusion
~~~~~~~~~~

Once the PHAR package and its signature have been downloaded,
your installation should look somewhat like that:

..  parsed-code:: text

    Erebot/
        Erebot-X.Y.Z.phar
        modules/
            |project|-|release|.phar
            |project|-|release|.phar.pubkey

That's all folks! You may now add `configuration options`_ for this module
in Erebot's configuration file.


..  _`install_composer`:

Installation through Composer
-----------------------------

Installation through `Composer <http://getcomposer.org/>`_ is very easy.
However, please note that Erebot itself must have been installed using Composer
for this method to work properly.

To install the new module:

*   Go to the directory where you installed Erebot.
*   Add this module to your installation's dependencies with:

    ..  parsed-code:: bash

        $ # Replace |version| with whatever version you want to install.
        $ php composer.phar install |composer_name|\=\ |version|

*   You may now add `configuration options`_ for this module in Erebot's
    configuration file.


Installation from source
------------------------

Please note that Erebot itself must have been installed from source
for this method to work.

..  warning::

    This method exists only for the sake of running Erebot on the now deprecated
    PHP 5.2.x. Also, please note that depending on your environment, other actions
    than the ones described here may be required to make this module work properly.

First, make sure the git client is installed on your machine.

..  include:: Installation_git.inc

Now, clone the module's repository:

..  parsed-code:: bash

    $ cd /path/to/Erebot/vendor/
    $ mkdir -p erebot
    $ git clone git://github.com/Erebot/|project|.git |composer_name|

Last but not least, install the rest of this module's `prerequisites`_
and then run:

..  parsed-code:: bash

    $ cd /path/to/Erebot/vendor/|composer_name|
    $ /path/to/phing

You may now add `configuration options`_ for this module in Erebot's
configuration file.

..  _`Erebot's package repository`:
    https://packages.erebot.net/
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

