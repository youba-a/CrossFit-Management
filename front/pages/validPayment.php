<section id="paymentValidation" class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white fw-bold text-center">
                        Validation du paiement
                    </div>

                    <div class="card-body text-center py-5">

                        <!-- Loader -->
                        <div class="my-4" id="loaderContainer">
                            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                            </div>
                        </div>

                        <!-- Message principal -->
                        <h5 id="msg" class="fw-bold mt-3">Validation du paiement...</h5>

                        <!-- Message d'erreur -->
                        <div id="error" class="alert alert-danger mt-4 d-none"></div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    const params = new URLSearchParams(window.location.search);
    const provider = params.get("method");
    const paymentId = params.get("pid");

    async function validate() {
        try {
            const res = await fetch("./back/API/member/payment/validatePayment.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "include",
                body: JSON.stringify({ provider, paymentId })
            });

            const data = await res.json();

            if (data.success) {
                document.getElementById("msg").innerText = "Paiement validé ✔";
                document.getElementById("loaderContainer").innerHTML =
                    '<div class="text-success" style="font-size:4rem;">✔</div>';

                setTimeout(() => window.location.href = "./index.php?p=memberSubscriptionView", 1500);
            } else {
                throw new Error(data.message);
            }

        } catch (e) {
            document.getElementById("error").innerText = e.message;
            document.getElementById("error").classList.remove("d-none");
            document.getElementById("loaderContainer").innerHTML =
                '<div class="text-danger" style="font-size:4rem;">✖</div>';
            document.getElementById("msg").innerText = "Erreur lors de la validation";
        }
    }

    validate();
</script>

<style>
    #paymentValidation .card {
        border-radius: 15px;
    }
</style>
