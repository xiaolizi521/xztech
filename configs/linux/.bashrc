# ~/.bashrc: executed by bash(1) for non-login shells.
# see /usr/share/doc/bash/examples/startup-files (in the package bash-doc)
# for examples

# If not running interactively, don't do anything
[ -z "$PS1" ] && return

# don't put duplicate lines in the history. See bash(1) for more options
# ... or force ignoredups and ignorespace
HISTCONTROL=ignoredups:ignorespace

# append to the history file, don't overwrite it
shopt -s histappend

# for setting history length see HISTSIZE and HISTFILESIZE in bash(1)
HISTSIZE=1000
HISTFILESIZE=2000

# check the window size after each command and, if necessary,
# update the values of LINES and COLUMNS.
shopt -s checkwinsize

# make less more friendly for non-text input files, see lesspipe(1)
[ -x /usr/bin/lesspipe ] && eval "$(SHELL=/bin/sh lesspipe)"

# set variable identifying the chroot you work in (used in the prompt below)
if [ -z "$debian_chroot" ] && [ -r /etc/debian_chroot ]; then
    debian_chroot=$(cat /etc/debian_chroot)
fi

# set a fancy prompt (non-color, unless we know we "want" color)
case "$TERM" in
    xterm-color) color_prompt=yes;;
    rxvt-unicode) color_prompt=yes;;
esac

# uncomment for a colored prompt, if the terminal has the capability; turned
# off by default to not distract the user: the focus in a terminal window
# should be on the output of commands, not on the prompt
#force_color_prompt=yes

if [ -n "$force_color_prompt" ]; then
    if [ -x /usr/bin/tput ] && tput setaf 1 >&/dev/null; then
	# We have color support; assume it's compliant with Ecma-48
	# (ISO/IEC-6429). (Lack of such support is extremely rare, and such
	# a case would tend to support setf rather than setaf.)
	color_prompt=yes
    else
	color_prompt=
    fi
fi

#if [ "$color_prompt" = yes ]; then
#    PS1='${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '
#else
#    PS1='${debian_chroot:+($debian_chroot)}\u@\h:\w\$ '
#fi
unset color_prompt force_color_prompt

# If this is an xterm set the title to user@host:dir
#case "$TERM" in
#xterm*|rxvt*)
#    PS1="\[\e]0;${debian_chroot:+($debian_chroot)}\u@\h: \w\a\]$PS1"
#    ;;
#*)
#    ;;
#esac
# c00kiemon5ter (ivan.kanak@gmail.com) ~ under c00kie License

# Shell is non-interactive.  Be done now!
[ -z "$PS1" ] && return

# view current command as title
#if [ "$SHELL" = '/bin/bash' ]; then
#   case $TERM in
#       URxvt*|rxvt*|*term)
#           set -o functrace
#           trap 'echo -ne "\e]0;$BASH_COMMAND\007"' DEBUG
#           export PS1="\e]0;$TERM\007$PS1" ;;
#   esac
#fi

# set tty color theme
if [ "$TERM" = "linux" ]; then
    ## set the theme name
    local THEME="console_c00kiez"
    ## read the theme, remove comments
    local colors=($(cat $HOME/.color_schemes/$THEME | sed "s/#.*//"))
    ## apply the colors
    for index in ${!colors[@]}
    do
        printf "\e]P%x%s" $index "${colors[$index]}"
    done
    clear #for background artifacting
fi

test -r .dircolors && eval "export $(dircolors -b .dircolors)"
source "$HOME"/.alias
source "$HOME"/.funcs
source "$HOME"/.completion
source "$HOME"/.git.alias

# set color variables
fgblk="$(tput setaf 0)"     # Black - Regular
fgred="$(tput setaf 1)"     # Red
fggrn="$(tput setaf 2)"     # Green
fgylw="$(tput setaf 3)"     # Yellow
fgblu="$(tput setaf 4)"     # Blue
fgpur="$(tput setaf 5)"     # Purple
fgcyn="$(tput setaf 6)"     # Cyan
fgwht="$(tput setaf 7)"     # White
bfgblk="$(tput setaf 8)"    # Black - Intense
bfgred="$(tput setaf 9)"    # Red
bfggrn="$(tput setaf 10)"   # Green
bfgylw="$(tput setaf 11)"   # Yellow
bfgblu="$(tput setaf 12)"   # Blue
bfgpur="$(tput setaf 13)"   # Purple
bfgcyn="$(tput setaf 14)"   # Cyan
bfgwht="$(tput setaf 15)"   # White
bgblk="$(tput setab 0)"     # Black - Background
bgred="$(tput setab 1)"     # Red
bggrn="$(tput setab 2)"     # Green
bgylw="$(tput setab 3)"     # Yellow
bgblu="$(tput setab 4)"     # Blue
bgpur="$(tput setab 5)"     # Purple
bgcyn="$(tput setab 6)"     # Cyan
bgwht="$(tput setab 7)"     # White
bbgblk="$(tput setab 8)"    # Black - Background - Intense
bbgred="$(tput setab 9)"    # Red
bbggrn="$(tput setab 10)"   # Green
bbgylw="$(tput setab 11)"   # Yellow
bbgblu="$(tput setab 12)"   # Blue
bbgpur="$(tput setab 13)"   # Purple
bbgcyn="$(tput setab 14)"   # Cyan
bbgwht="$(tput setab 15)"   # White
normal="$(tput sgr0)"   # text reset
undrln="$(tput smul)"   # underline
noundr="$(tput rmul)"   # remove underline
mkbold="$(tput bold)"   # make bold
mkblnk="$(tput blink)"  # make blink
revers="$(tput rev)"    # reverse
PROMPT_COMMAND=prompt

# enable color support of ls and also add handy aliases
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    #alias dir='dir --color=auto'
    #alias vdir='vdir --color=auto'

    alias grep='grep --color=auto'
    alias fgrep='fgrep --color=auto'
    alias egrep='egrep --color=auto'
fi

# some more ls aliases
alias ll='ls -alF'
alias la='ls -A'
alias l='ls -CF'

# Alias definitions.
# You may want to put all your additions into a separate file like
# ~/.bash_aliases, instead of adding them here directly.
# See /usr/share/doc/bash-doc/examples in the bash-doc package.

if [ -f ~/.bash_aliases ]; then
    . ~/.bash_aliases
fi

# enable programmable completion features (you don't need to enable
# this, if it's already enabled in /etc/bash.bashrc and /etc/profile
# sources /etc/bash.bashrc).
if [ -f /etc/bash_completion ] && ! shopt -oq posix; then
    . /etc/bash_completion
fi

export VISUAL=vim
