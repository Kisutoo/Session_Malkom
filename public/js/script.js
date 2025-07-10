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
        editable: true,
        eventResizableFromStart: true
    });
    calendar.render();
    // Sert à renvoyer la vue du calendrier
}
 

function openPopupStagiaire()
{
    popupStagiaire.classList.add("open-popup-stagiaire");
}

function openPopupModule()
{
    popupModule.classList.add("open-popup-module");
}

function openPopupCreateModule()
{
    popupCreateModule.classList.add("open-popup-createModule");
}

function openPopupCreateSession()
{
    popupCreateSession.classList.add("open-popup-createSession");
}

function openPopupSuprSession()
{
    popupSuprSession.classList.add("open-popup-suprSession");
}




function closePopupStagiaire()
{
    popupStagiaire.classList.remove("open-popup-stagiaire");
}

function closePopupModule()
{
    popupModule.classList.remove("open-popup-module");
}

function closePopupCreateModule()
{
    popupCreateModule.classList.remove("open-popup-createModule")
}

function closePopupCreateSession()
{
    popupCreateSession.classList.remove("open-popup-createSession")
}

function closePopupSuprSession()
{
    popupSuprSession.classList.remove("open-popup-suprSession")
}