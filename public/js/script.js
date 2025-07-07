let popupStagiaire = document.getElementById("popupStagiaire");
let popupModule = document.getElementById("popupModule");
let popupCreateModule = document.getElementById("popupCreateModule")
let popupCreateSession = document.getElementById("popupCreateSession");
let popupSuprSession = document.getElementById("popupSuprSession");




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