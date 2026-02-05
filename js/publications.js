$(document).ready(function () {
    // 🔁 Fonction pour charger les publications existantes
    function loadPosts() {
        $.get("fetch_posts.php", function (data) {
            $("#postsContainer").html(data);
        }).fail(function () {
            $("#postsContainer").html("<p style='color: red;'>Erreur de chargement des publications.</p>");
        });
    }

    // Charger les publications au démarrage
    loadPosts();

    // Afficher ou masquer le formulaire
    $("#newPostButton").click(function () {
        $("#postFormContainer").slideToggle();
    });

    // Gestion de la soumission du formulaire
    $("#postForm").submit(function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const $submitBtn = $("#postForm button[type='submit']");
        const $message = $("#postMessage");

        // Désactiver le bouton pour éviter le spam
        $submitBtn.prop("disabled", true).text("Publication en cours...");
        $message.hide().text("");

        $.ajax({
            url: "add_posts.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json", // 👈 On attend un JSON, donc pas besoin de JSON.parse
            success: function (response) {
                const isSuccess = response.status === "success";

                $message
                    .css("color", isSuccess ? "green" : "red")
                    .hide()
                    .text(response.message)
                    .slideDown();

                if (isSuccess) {
                    $("#postForm")[0].reset();
                    $("#postFormContainer").slideUp();
                    loadPosts();
                }
            },
            error: function () {
                $message
                    .css("color", "red")
                    .hide()
                    .text("Erreur lors de la connexion au serveur.")
                    .slideDown();
            },
            complete: function () {
                $submitBtn.prop("disabled", false).text("Publier");
            }
        });
    });
});
