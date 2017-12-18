//给不同大小窗口下的html设置不同大小的font-size
$(function() {
  var width = document.body.clientWidth;
  // width = (width <= 1024) ? width : 1024;
  var font  = 16 * ( width/320 ) +'px';
  document.querySelector('html').style.fontSize = font;
})

window.onresize = function() {
  var width = document.body.clientWidth;
  // width = (width <= 1024) ? width : 1024;
  var font  = 16 * ( width/320 ) + 'px';
  document.querySelector('html').style.fontSize = font;
}
