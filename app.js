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
let utenti = JSON.parse(localStorage.getItem("utenti")) || [];

function Utente(nomePersonaggio, specie, username, sesso, password) {
    this.nomePersonaggio = nomePersonaggio;
    this.specie = specie;
    this.username = username;
    this.sesso = sesso;
    this.password = password;
}

function creaUtente() {
    let nome = document.getElementById("nome").value;
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;
    let passwordConf = document.getElementById("passwordConf").value;
    let specie = document.getElementById("specie").value;
    let sesso = document.getElementById("sesso").value;
    username=username.toLowerCase();
    if (nome === "" || username === "" || password === "" || passwordConf === "") {
        alert("Compilare tutti i campi per completare la registrazione");
        return;
    }

    if (password !== passwordConf) {
        alert("Le password non coincidono");
        return;
    }

    let nuovoUtente = new Utente(nome, specie, username, sesso, password);
    utenti.push(nuovoUtente);
    localStorage.setItem("utenti", JSON.stringify(utenti));

    alert("Registrazione completata con successo");
    toSlide("login-page");
}

function accedi() {
    let usernameLogin = document.getElementById("usernameLogin").value;
    let passwordLogin = document.getElementById("passwordLogin").value;
    usernameLogin=usernameLogin.toLowerCase();
    if (usernameLogin === "" || passwordLogin === "") {
        alert("Compilare tutti i campi per tentare l'accesso");
        return;
    }
    let utentiSalvati = JSON.parse(localStorage.getItem("utenti")) || [];
    let utenteTrovato = utentiSalvati.find(utente => 
        utente.username === usernameLogin && utente.password === passwordLogin
    );

    if (utenteTrovato) {
        alert("Accesso eseguito con successo");
        toSlide("home-page");
    } else {
        alert("Username o password errati");
    }
}