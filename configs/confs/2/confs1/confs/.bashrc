# .bashrc

# exit if we're in a script
[ -z "$PS1" ] && return

# source some files
[ -f ~/.bash_functions ] && source $HOME/.bash_functions
[ -f ~/.bash_exports ] && source $HOME/.bash_exports
[ -f ~/.bash_alias ] && source $HOME/.bash_alias

# shell completiong with sudo 
complete -cf sudo

# colorize dir and ls
[ -f ~/.dircolors ] && eval `/bin/dircolors -b ~/.dircolors`

# advanced bash-completion
[ -f /etc/bash_completion ] && source /etc/bash_completion

# set the titlebar in xterm/urxvt
case "$TERM" in
  xterm*|rxvt*)
    PROMPT_COMMAND='echo -ne "\0033]0;${HOSTNAME} ${PWD/$HOME/~}\007"'
    ;;
  *)
    ;;
esac

# colored prompt
if [ "`tput colors`" = "256" ]; then
  B="\e[0;38;5;67m"
  G="\e[0;38;5;114m"
  Y="\e[0;38;5;214m"
else
  B="\e[0;34m"
  G="\e[0;32m"
  Y="\e[0;33m"
fi

W="\e[0m"

PS1="\[$B\]┌─\[$W\][ \[$Y\]\A \[$W\]][ \[$G\]\h:\w \[$W\]]\n\[$B\]└─\[$Y\]> \[$W\]"
PS2="  \[$Y\]> \[$W\]"

# fallback prompt
#PS1='[ \A ][ \w ]\n> '
#PS2='>'

# auto startx and logout, security ! 
if [[ -z "$DISPLAY" ]] && [[ $(tty) = /dev/vc/1 ]]; then
  startx
  logout
fi

# check mail upon login
#$HOME/.bin/checkmail
#sleep 2 && clear

