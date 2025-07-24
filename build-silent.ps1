vite build 2>&1 | Where-Object {
    $_ -notmatch 'Deprecation Warning' -and
    $_ -notmatch 'More info' -and
    $_ -notmatch 'Use color\.mix instead\.' -and
    $_ -notmatch 'Use math\.unit instead\.' -and
    $_ -notmatch 'color\.channel\(' -and
    $_ -notmatch 'https://sass-lang.com/d/import' -and
    $_ -notmatch 'warning:'
}