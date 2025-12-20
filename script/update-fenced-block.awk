#!/usr/bin/awk -f

BEGIN {
  if(open_fence == "") {
    open_fence = "BEGIN_GRACEFULDEV_CUSTOM"
  }
  if(close_fence == "") {
    close_fence = "END_GRACEFULDEV_CUSTOM"
  }
  if(anchor == "") {
    anchor = "That's all, stop editing! Happy blogging"
  }
  if(insert_block == "") {
    print "Error: You must supply an `insert_block` to insert/update." > "/dev/stderr"
    exit 1
  }
}

# drop existing fenced block
$0 ~ open_fence, $0 ~ close_fence {
  next
}

# insert block before anchor line
$0 ~ anchor {
    print insert_block
    print $0
    next
}

# pass any other lines through
{ print } 