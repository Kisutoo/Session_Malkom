let popupStagiaire = document.getElementById("popupStagiaire");
let popupModule = document.getElementById("popupModule");

function openPopupStagiaire()
{
    popupStagiaire.classList.add("open-popup-stagiaire");
}

function openPopupModule()
{
    popupModule.classList.add("open-popup-module");
}

function closePopupStagiaire()
{
    popupStagiaire.classList.remove("open-popup-stagiaire");
}

function closePopupModule()
{
    popupModule.classList.remove("open-popup-module");
}