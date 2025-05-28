<?php function footer() { ?>
<footer class="text-bg-primary bg-gradient shadow-lg">
    <div class="container py-4">
        <div class="row">
            <div class="col-4 d-flex align-items-center justify-content-center">
                <div>
                    <h6 class="text-center mb-2 text-uppercase fw-bold"> Test&Tell</h6>
                    <p class="text-center">Prova. Condividi. Ispira.</p>
                </div>
            </div>
            <div class="col-4">
                <h6 class="text-center mb-3 text-uppercase fw-bold"> Contatti </h6>
                <div class="d-flex align-items-center justify-content-around">
                    <a href="mailto:info@testntell.com" class="text-white text-decoration-none">
                        <img src="/static/svg/at.svg" alt="chiocciola" class="icon invert">
                    </a>
                    <a href="phone:+11111111111" class="text-white text-decoration-none">
                        <img src="/static/svg/phone.svg" alt="telefono" class="icon invert">
                    </a>
                    <a href="https://instagram.com" class="text-white text-decoration-none">
                        <img src="/static/svg/instagram.svg" alt="instagram" class="icon invert">
                    </a>
                    <a href="https://web.telegram.org" class="text-white text-decoration-none">
                        <img src="/static/svg/telegram.svg" alt="telegram" class="icon invert">
                    </a>
                </div>
            </div>
            <div class="col-4">
                <h6 class="text-center mb-3 text-uppercase fw-bold"> Risorse legali </h6>
                <div class="d-flex align-items-start justify-content-between">
                    <a href="#" class="col-5 text-white text-center word-wrap">Informativa sulla privacy</a>
                    <a href="#" class="col-5 text-white text-center word-wrap">Termini di servizio</a>
                </div>
            </div>
        </div>

        <hr>

        <p class="text-center m-0">&copy; 2025, Giuseppe Pappalardo</p>
    </div>
</footer>
<?php } ?>