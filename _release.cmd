cd /d "%~dp0"

robocopy "%~dp0." "\\eti-sync\c$\Sync\Lanweb" /mir /XD ".sync" ".svn" /XF *.cmd desktop.ini
