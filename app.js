const VERSION="v20241209a"

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js')
}
if(location.protocol=="http:"){
    location.href="https"+location.href.substring(4)
}
if (window.matchMedia('(display-mode: standalone)').matches) { //PWAs in standalone mode don't have the are you sure you want to leave the page dialog, so we prevent accidental back button presses on android from killing the app
    history.pushState({},null,document.URL)
    window.addEventListener('popstate', () => {
        history.pushState({},null,document.URL)
    })
}

function toSlide(id){
    document.querySelectorAll("div.slide").forEach(function(e){
        e.classList.remove("visible")
        e.classList.add("hidden")
        e.querySelectorAll("*").forEach(function(l){
            l.tabIndex=-1
        })
    })
    let e=document.getElementById(id)
    e.classList.add("visible")
    e.classList.remove("hidden")
    e.querySelectorAll("*").forEach(function(l){
        l.tabIndex=""
    })
}