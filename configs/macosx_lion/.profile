#!/bin/bash

# ~/.profile: executed by the command interpreter for login shells.
# This file is not read by bash(1), if ~/.bash_profile or ~/.bash_login
# exists.
# see /usr/share/doc/bash/examples/startup-files for examples.
# the files are located in the bash-doc package.

# the default umask is set in /etc/profile; for setting the umask
# for ssh logins, install and configure the libpam-umask package.
#umask 022

# if running bash
if [ -n "$BASH_VERSION" ]; then
    # include .bashrc if it exists
    if [ -f "$HOME/.bashrc" ]; then
  . "$HOME/.bashrc"
    fi
fi

# set PATH so it includes user's private bin if it exists
if [ -d "$HOME/bin" ] ; then
    PATH="$HOME/bin:$PATH"
fi
# MacPorts Installer addition on 2011-07-13_at_21:16:02: adding an appropriate PATH variable for use with MacPorts.
export PATH=/opt/local/lib/postgresql83/bin:/opt/local/share/postgresql83/contrib:/opt/local/sbin:/Users/triton/dev/enova/db/bin:/opt/local/bin:/opt/local/sbin:$PATH
export VISUAL=vim
export EDITOR=vim
export P4CONFIG=.p4config
export LD_LIBRARY_PATH=$LD_LBRIARY_PATH:/Developer/usr/lib
export PGDATA=/opt/local/var/db/postgresql83/defaultdb