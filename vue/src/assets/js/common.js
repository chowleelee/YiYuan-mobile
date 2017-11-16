function selfAdaption () {
  var html = document.documentElement
  var hWidth = html.getBoundingClientRect().width
  html.style.fontSize = Math.floor(hWidth / 20) + 'px'
}
function menu () {
  window.scroll(0, 0)
}
export {
  selfAdaption,
  menu
}
