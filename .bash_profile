#  don't put duplicate lines or lines starting with space in the history
HISTCONTROL=ignoreboth

# go into the db
alias db="mysql -uroot -pblink183"
alias refresh="source ~/.bash_profile"
alias gitdiff="git diff -w --ignore-blank-lines --ignore-space-change --ignore-space-at-eol"
alias snippets="cd /var/www/staging && rm -rf snippets && git checkout snippets && chmod 777 -R snippets && chown www-data.www-data snippets"
alias bigfiles="find . -type f -print0 | xargs -0 du -h | sort -hr | head -20"
alias bigdirs="find . -type d -print0 | xargs -0 du -h | sort -hr | head -20"
alias cache="rm -rf /lando_cache_old && mv /lando_cache /lando_cache_old && mkdir /lando_cache && chmod 777 /lando_cache && chown www-data.www-data /lando_cache && mv /lando_cache_old/bundles* /lando_cache/. && rm -rf /lando_cache_old"

#alias lsa="ls -lhsa --color=auto"
#alias grep="grep --color=auto"
#alias grepr="grep -r --color=auto"
alias lsa="php ~/lsa.php"
alias lsar="php ~/lsar.php"
alias lsam="php ~/lsam.php"
alias lsas="php ~/lsas.php"
alias grepr="php ~/grepr.php"

function sql()
{
	su postgres
	psql
}

# git status
function gitstatus()
{
	RESULT=`git diff --name-only --ignore-blank-lines --ignore-space-change --ignore-space-at-eol | xargs -L1 echo "	modified:   "`
	echo -e "\e[31m$RESULT\e[39m"
}

# php -l multiple files (phpl file1 file2 dir/*)
function phpl()
{
    for i in "$@";
    do
        php -l -d error_reporting=E_ALL $i
    done
}

# php -l recursive
function phplr
{
    BAD=()
    for file in `find .`
    do
        EXTENSION="${file##*.}"
        if [ "$EXTENSION" == "php" ] || [ "$EXTENSION" == "phtml" ]
        then
            RESULTS=`php -l -d error_reporting=E_ALL $file`
            if [ "$RESULTS" != "No syntax errors detected in $file" ]
            then
                echo "ERRORS IN $file:"
                echo $RESULTS
		BAD+=($file)
            else
                echo "$file ok"
            fi
        fi
    done
    echo ""
    if [${BAD[@]} -eq 0]; then
        echo "all files ok"
    else
        echo ""
	echo "BAD FILES:"
        echo ""
	    for file in "${BAD[@]}"
	    do
                echo $file
		RESULTS=`php -l -d error_reporting=E_ALL $file`
                echo $RESULTS
                echo ""
            done
    fi
}

# copy a file into multiple files
function cpm()
{
    for i in "${@:2}";
    do
        cp $1 $i
    done
}
