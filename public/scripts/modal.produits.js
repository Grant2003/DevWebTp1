    //-----------------------------------
    //   Fichier : modal.produit.js
    //   Par:      Anthony Grenier
    //   Date :    2025-2-22
    //----------------------------------- 
$(document).ready(() => {

    $(".produit-modal").click(async (event) => {

        //j'utilise axios car je suis plus familier avec 
        event.preventDefault();
        const href = event.currentTarget.href;

        const response = await axios.get(href);
        
        if(response.status === 200) {
            $("#produit-modal-content").html(response.data);

            const produitViewModal = new bootstrap.Modal(document.getElementById('produit-modal'), {});

            produitViewModal.show();
        }

    });

})