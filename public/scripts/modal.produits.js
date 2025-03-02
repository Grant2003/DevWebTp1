$(document).ready(() => {

    $(".produit-modal").click(async (event) => {

        event.preventDefault();

        const href = event.currentTarget.href;

        const response = await axios.get(href);
        console.log(response.data)
        if(response.status === 200) {
            $("#produit-modal-content").html(response.data);
            const produitViewModal = new bootstrap.Modal(document.getElementById('produit-modal'), {});
            produitViewModal.show();
        }

    });

})