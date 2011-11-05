
" Debian system-wide default configuration Vim

set runtimepath=~/.vim,/var/lib/vim/addons,/usr/share/vim/vimfiles,/usr/share/vim/vim71,/usr/share/vim/vimfiles/after,/var/lib/vim/addons/after,~/.vim/after

" Normally we use vim-extensions. If you want true vi-compatibility
" remove change the following statements
set nocompatible	" Use Vim defaults instead of 100% vi compatibility
set backspace=indent,eol,start	" more powerful backspacing

" Now we set some defaults for the editor
" set autoindent		" always set autoindenting on
" set linebreak		" Don't wrap words by default
set history=50		" keep 50 lines of command line history
set ruler		" show the cursor position all the time

set viminfo='20,\"50    " read/write a .viminfo file, don't store more than
            " 50 lines of registers
syntax on
set expandtab
set history=50
set autoindent
set tabstop=4
set sw=4
"set number
set cindent
set encoding=utf8
set tenc=utf8
set fileencoding=utf8
filetype plugin indent on
set comments=sl:/*,mb:*,elx:*/
set showmatch
set background=dark

" modelines have historically been a source of security/resource
" vulnerabilities -- disable by default, even when 'nocompatible' is set
set nomodeline

" Suffixes that get lower priority when doing tab completion for filenames.
" These are files we are not likely to want to edit or read.
set suffixes=.bak,~,.swp,.o,.info,.aux,.log,.dvi,.bbl,.blg,.brf,.cb,.ind,.idx,.ilg,.inx,.out,.toc

" We know xterm-debian is a color terminal
if &term =~ "xterm-debian" || &term =~ "xterm-xfree86"
  set t_Co=16
  set t_Sf=[3%dm
  set t_Sb=[4%dm
endif

if has("autocmd")
 " Enabled file type detection
 " Use the default filetype settings. If you also want to load indent files
 " to automatically do language-dependent indenting add 'indent' as well.
 filetype plugin on

endif " has ("autocmd")

" Some Debian-specific things
if has("autocmd")
  " set mail filetype for reportbug's temp files
  augroup debian
    au BufRead reportbug.*		set ft=mail
    au BufRead reportbug-*		set ft=mail
  augroup END
endif

" Set paper size from /etc/papersize if available (Debian-specific)
if filereadable("/etc/papersize")
  try
    let s:shellbak = &shell
    let &shell="/bin/sh"
    let s:papersize = matchstr(system("cat /etc/papersize"), "\\p*")
    let &shell=s:shellbak
    if strlen(s:papersize)
      let &printoptions = "paper:" . s:papersize
    endif
  catch /^Vim\%((\a\+)\)\=:E145/
  endtry
endif

if has('gui_running')
  " Make shift-insert work like in Xterm
  map <S-Insert> <MiddleMouse>
  map! <S-Insert> <MiddleMouse>
endif
