<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style type="text/css">
        .square-photo {
            width: 100%;
            height: 0;
            padding-bottom: 100%;
            position: relative;
        }
        .square-photo img {
            position: absolute;
            width: 100%;
            height: 100%;
            inset: 0;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container py-3">
        <header>
            <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
                <a href="/" class="d-flex align-items-center text-dark text-decoration-none">
                    <img src="/public/img/logo.svg" style="max-width: 250px; width: 100%" class="me-4" />
                    <span class="fs-4">Fan Shop</span>
                </a>

                <nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">
                    <a class="py-2 text-dark text-decoration-none" href="/">Каталог</a>
                </nav>
            </div>

            <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                <h1 class="display-4 fw-normal">#Advance Fan Shop</h1>
                <p class="fs-5 text-muted">
                    Представяме ви нашият скромен каталог от мърчъндайзинг.
                </p>
            </div>
        </header>

        <main>
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                <?php foreach (($products ?? []) as $product): ?>
                    <div class="col">
                        <div class="card shadow-sm h-100">
                            <div class="square-photo">
                                <img src="<?=$product['image'] ?? ''?>" class="w-100 mb-2">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h2 class="card-title pricing-card-title"><?=$product['title'] ?? ''?></h2>
                                <p class="card-text"><?=$product['description'] ?? ''?></p>
                                <div class="d-flex justify-content-between align-items-end mt-auto">
                                    <h3 class="m-0 p-0"><span class="badge rounded-pill bg-success h-100">Цена: <?=$product['price'] ?? '0.00'?>€</span></h3>
                                    <div class="btn-group">
                                        <button type="button" class="w-100 btn btn-lg btn-primary py-1">Купи</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
