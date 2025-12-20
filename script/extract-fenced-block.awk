#!/usr/bin/awk -f
BEGIN {
  if(open_fence == "") {
    open_fence = "BEGIN_GRACEFULDEV_CUSTOM";
  }
  if(close_fence == "") {
    close_fence = "END_GRACEFULDEV_CUSTOM";
  }
}
$0 ~ open_fence, $0 ~ close_fence {
    print
}