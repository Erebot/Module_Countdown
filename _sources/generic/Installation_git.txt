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

..  _`Git for Windows`:
    http://code.google.com/p/msysgit/downloads/list
