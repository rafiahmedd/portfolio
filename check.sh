#!/bin/sh
# Smoke check for the SEO layer: ./check.sh http://localhost:9400 [/a-post-slug/]
B=${1:?usage: check.sh BASE_URL [POST_PATH]}; fail=0
has() { curl -sL "$B$1" | grep -q "$2" || { echo "FAIL $1 missing $2"; fail=1; }; }
has / '"@type":"ProfilePage"'
has / 'name="description"'
has /blog/ '"@type":"Blog"'
has /blog/ 'rel="canonical"'
has /llms.txt '## Posts'
if [ -n "$2" ]; then
  has "$2" '"@type":"BlogPosting"'
  has "$2" '"@type":"BreadcrumbList"'
  has "$2" 'article:published_time'
fi
[ $fail = 0 ] && echo OK
exit $fail
