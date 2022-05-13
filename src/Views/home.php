<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Shopping Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="/public/css/app.css" rel="stylesheet" />
</head>
<body>
    <header>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="/public/img/logo.svg" style="max-width: 250px; width: 100%" class="me-4" />
                    <span class="fs-4">Fan Shop</span>
                </a>
                <button class="navbar-toggler"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-cart-shopping fa-fw"></i>
                                Количка
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <div class="px-3 py-2">
                                    <div id="cart">

                                    </div>

                                    <div id="paypal-button-container"></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container py-3">
            <div class="row">
                <div class="col-12">
                    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                        <h1 class="display-4 fw-normal">#Advance Fan Shop</h1>
                        <p class="fs-5 text-muted">
                            Представяме ви нашият скромен каталог от мърчъндайзинг.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <?php foreach (($products ?? []) as $product): ?>

                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="square-photo">
                            <img src="<?=$product['image'] ?? ''?>" class="w-100 mb-2" />
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h2 class="card-title pricing-card-title"><?=$product['title'] ?? ''?></h2>
                            <p class="card-text"><?=$product['description'] ?? ''?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="price-tag">
                                    Цена: <?=$product['price'] ?? '0.00'?>€
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="w-100 btn btn-md btn-warning py-1 add-to-cart" data-price="<?=$product['price'] ?? 0.00 ?>">
                                        <i class="fa-solid fa-cart-plus fa-fw"></i>
                                        Добави
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

            </div>
        </div>
    </main>

    <footer class="text-muted py-5">
        <div class="container border-top pt-4">
            <div class="row">
                <div class="col">
                    <h3 class="mb-3">Контакти</h3>

                    <ul class="list-unstyled">
                        <li class="mb-1">
                            <a href="tel:0899990030">
                                <i class="fa-fw fa-solid fa-phone-flip"></i>
                                <span>0899 990 030</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="mailto:office@advanceacademy.bg">
                                <i class="fa-fw fa-solid fa-at"></i>
                                <span>office@advanceacademy.bg</span>
                            </a>
                        </li>
                        <li class="mb-1">
                            <a target="_blank" href="https://goo.gl/maps/Ca6RxaoHb6oT4iR68">
                                <i class="fa-fw fa-solid fa-house-circle-exclamation"></i>
                                <span>гр. Варна, ул. „Димитър Икономов“ 19 ет.3</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="#">Нагоре</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClient ?? ''?>&components=buttons&intent=capture&commit=false&currency=EUR"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="/public/js/app.js"></script>
</body>
</html>
