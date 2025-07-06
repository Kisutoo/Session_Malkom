let popupStagiaire = document.getElementById("popupStagiaire");
let popupModule = document.getElementById("popupModule");
let popupCreateModule = document.getElementById("popupCreateModule")




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
    popupCreateModule.classList.add("open-popup-createModule")
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