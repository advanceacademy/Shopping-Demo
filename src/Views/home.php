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

                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center" id="menu">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <div class="position-relative d-inline-block me-3">
                                    <i class="fa-solid fa-cart-shopping fa-fw fa-2x"></i>
                                    <span class="badge badge-sm rounded-pill bg-primary position-absolute px-2 py-1" id="cartQuantity" style="bottom:-5px; right:-5px"></span>
                                </div>
                                Количка
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <div class="px-3 pt-2">
                                    <div id="cart"></div>
                                    <div id="paypal-button-container" class="d-none"></div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item ms-3 guest">
                            <a href="#" class="nav-link d-flex align-items-center p-0" data-bs-toggle="modal" data-bs-target="#modalLogin">
                                <div class="position-relative d-inline-block me-2 ">
                                    <i class="fa-solid fa-user fa-fw fa-2x"></i>
                                </div>
                                Потребител
                            </a>
                        </li>

                        <li class="nav-item ms-3 dropdown d-none profile">
                            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <div class="position-relative d-inline-block me-1">
                                    <i class="fa-solid fa-user fa-fw fa-2x"></i>
                                </div>

                                <div class="d-inline-block" id="userName">

                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#" id="logoutUser">Изход</a></li>
                            </ul>
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
                    <div class="card shadow-sm h-100"
                        data-id="<?=htmlspecialchars($product['id'])?>"
                        data-title="<?=htmlspecialchars($product['title'])?>"
                        data-quantity="<?=((int) $product['quantity'])?>"
                        data-price="<?=number_format($product['price'] ?? 0.00, 2, '.', '') ?>"
                    >
                        <div class="square-photo">
                            <img src="<?=$product['image'] ?? ''?>" class="w-100 mb-2" />
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h2 class="card-title pricing-card-title"><?=$product['title'] ?? ''?></h2>
                            <p class="card-text"><?=$product['description'] ?? ''?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div class="price-tag">
                                    Цена: <?=$product['price'] ?? '0.00'?> лв.
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="w-100 btn btn-md btn-warning py-1 add-to-cart">
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

    <div class="modal fade" id="modalLogin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-body">
                        <div class="alert d-none"></div>

                        <div class="row mb-3">
                            <label for="inputEmail" class="col-sm-2 col-form-label">E-mail</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputEmail">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputPassword" class="col-sm-2 col-form-label">Парола</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalRegister">Регистрация?</button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отказ</button>
                        <button type="submit" class="btn btn-primary">Вход</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalRegister" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-body">
                        <div class="alert d-none"></div>

                        <div class="row mb-3">
                            <label for="inputRegisterEmail" class="col-sm-2 col-form-label">E-mail</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputRegisterEmail">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputRegisterPassword" class="col-sm-2 col-form-label">Парола</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputRegisterPassword">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputRegisterPhone" class="col-sm-2 col-form-label">Телефон</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputRegisterPhone">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputRegisterName" class="col-sm-2 col-form-label">Име</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputRegisterName">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputRegisterSurname" class="col-sm-2 col-form-label">Фамилия</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputRegisterSurname">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отказ</button>
                        <button type="submit" class="btn btn-primary">Регистрация</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalClient ?? ''?>&components=buttons&intent=capture&commit=false&currency=EUR"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script src="/public/js/app.js"></script>
</body>
</html>
