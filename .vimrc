set encoding=utf8
set ffs=unix,dos,mac
set viminfo='10,<1000,s100,%,n~/.viminfo

" color scheme
syntax on
set background=dark
let g:solarized_termcolors=256
set t_Co=16
colorscheme solarized

" make 1 tab = 4 spaces
nmap <F1> :set shiftwidth=4<CR>:set tabstop=4<CR>
" make 1 tab = 8 spaces
nmap <F2> :set shiftwidth=8<CR>:set tabstop=8<CR>
" fix spacings
nmap <F4> :%s/( /(/g<CR>:%s/ )/)/g<CR>:%s/if(/if (/g<CR>:%s/foreach(/foreach (/g<CR>:%s/for(/for (/g<CR>
" trim trailing whitespace
nmap <F5> :let _s=@/<Bar>:%s/\s\+$//e<Bar>:let @/=_s<Bar>:nohl<CR>
" convert to unix EOL
nmap <F6> :set fileformat=unix<CR>
" auto-indent
nmap <F7> mmHmt:%s/<C-V><cr>//ge<cr>'tzt'mmzgg=G`z<CR>
" all of the above
nmap <F8> mmHmt:%s/<C-V><cr>//ge<cr>'tzt'mmzgg=G`z<CR> :let _s=@/<Bar>:%s/\s\+$//e<Bar>:let @/=_s<Bar>:nohl<CR> :set fileformat=unix<CR>
" funky quotes and special chars
nmap <F9> :%s/“/"/g<CR>:%s/”/"/g<CR>:%s/’/'/g<CR>:%s/–/-/g<CR>

" go back to the same line we were just in when reopening a file
function! ResCur()
    if line("'\"") <= line("$")
        normal! g`"
        return 1
    endif
endfunction
if has("folding")
    function! UnfoldCur()
        if !&foldenable
            return
        endif
        let cl = line(".")
        if cl <= 1
            return
        endif
        let cf  = foldlevel(cl)
        let uf  = foldlevel(cl - 1)
        let min = (cf > uf ? uf : cf)
        if min
            execute "normal!" min . "zo"
            return 1
        endif
    endfunction
endif
augroup resCur
    autocmd!
    if has("folding")
        autocmd BufWinEnter * if ResCur() | call UnfoldCur() | endif
    else
        autocmd BufWinEnter * call ResCur()
    endif
augroup END
