$(document).ready(() => {

    $(".produit-modal").click(async (event) => {

        event.preventDefault();

        const href = event.currentTarget.href;

        const response = await axios.get(href);
        if(response.status === 200) {
            $("#produit-modal-content").html(response.data);
            const championViewModal = new bootstrap.Modal(document.getElementById('produit-modal'), {});
            championViewModal.show();
        }

    });

})