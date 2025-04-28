    //-----------------------------------
    //   Fichier : TimerAnnulation.produit.js
    //   Par:      Anthony Grenier
    //   Date :    2025-4-22
    //----------------------------------- 
function formatTemps(secondes) {
    if (secondes > 86400) {
        let jours = Math.floor(secondes / 86400);
        let heures = Math.floor((secondes % 86400) / 3600);
        return `${jours} jour${jours > 1 ? 's' : ''} ${heures}h restantes`;
    } else if (secondes > 3600) {
        let heures = Math.floor(secondes / 3600);
        let minutes = Math.floor((secondes % 3600) / 60);
        return `${heures}h ${minutes}m restantes`;
    } else {
        let minutes = Math.floor(secondes / 60);
        let secs = secondes % 60;
        return `${minutes}m ${secs}s restantes`;
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////
////
////
//////////////////////////////////////////////////////////////////////////////////////////////
function lancerCompteARebours(idElement, secondesRestantes) {
    const el = document.getElementById(idElement);

    const interval = setInterval(() => {
        if (secondesRestantes <= 0) {
            el.innerHTML = "Commande non annulable";
            clearInterval(interval);
            const btn = el.nextElementSibling;
            if (btn) btn.remove();
        } else {
            el.innerHTML = formatTemps(secondesRestantes);
            secondesRestantes -= 5;
        }
    }, 5000);

    // Mise à jour immédiate à l'affichage sans refresh
    el.innerHTML = formatTemps(secondesRestantes);
}

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[id^='delai-']").forEach(el => {
        const secondes = parseInt(el.getAttribute("data-secondes"));
        lancerCompteARebours(el.id, secondes);
    });
});
