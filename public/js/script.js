let popupStagiaire = document.getElementById("popupStagiaire");
let popupModule = document.getElementById("popupModule");
let popupCreateModule = document.getElementById("popupCreateModule")
let popupCreateSession = document.getElementById("popupCreateSession");
let popupSuprSession = document.getElementById("popupSuprSession");



// Initialisation du fullCalendar
window.onload = () => {
    var calendarEl = document.querySelector('#calendar');

    var divData = document.querySelector("#data")
    // On récupère la div qui stock les datas que l'on veut récupérer
    var data = divData.getAttribute("data-");
    // Puis dans cette div, on récupère les data grâce à l'attribut "data-" ou l'on stock les données


    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        // Vue initiale sur les jours de la grille du mois 
        locale: "fr",
        // On change la langue du calendrier
        timeZone: "Europe/Paris",
        // On change le fuseau horaire
        headerToolbar: {
        // On vient changer l'affichage des bouton du header
            start: "prev,next today",
            center: "title",
            end: "dayGridMonth,timeGridWeek"
        },
        events: JSON.parse(data),
    });
    calendar.render();
    // Sert à renvoyer la vue du calendrier
}

function openPopup(value)
{
    switch (value)
    {
        case "Stagiaire":
            popupStagiaire.classList.add("open-popup-stagiaire");break;
        case "Module":
            popupModule.classList.add("open-popup-module");break;
        case "CreateModule":
            popupCreateModule.classList.add("open-popup-createModule");break;
        case "CreateSession":
            popupCreateSession.classList.add("open-popup-createSession");break;
        case "SuprSession":
            popupSuprSession.classList.add("open-popup-suprSession");break;
    }
}

function closePopup(value)
{
    switch (value)
    {
        case "Stagiaire":
            popupStagiaire.classList.remove("open-popup-stagiaire");break;
        case "Module":
            popupModule.classList.remove("open-popup-module");break;
        case "CreateModule":
            popupCreateModule.classList.remove("open-popup-createModule");break;
        case "CreateSession":
            popupCreateSession.classList.remove("open-popup-createSession");break;
        case "SuprSession":
            popupSuprSession.classList.remove("open-popup-suprSession"); break; 
    }
}

