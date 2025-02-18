<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <style>
    @media (max-width: 444px) {

        main .Advantages_h {
            height: 65px;
        }
    }

    @media (max-width: 1360px) {
        main .Advantages_h {
            height: 65px;
        }

        header .nav_left {
            padding-right: 15rem;
        }

    }

    @media (max-width: 1220px) {
        body main {
            height: 800px;
        }

        main .Advantages {
            gap: 2rem;
        }

        main .Advantages_h {
            height: 65px;
        }

        main .Advantages_resolved_issues {
            right: 1rem;
        }

        body header {
            height: 1000px;
        }

        header .nav_left {
            padding-right: 5rem;
        }

        header .nav_center {
            padding-right: 5rem;
        }

        header .container {
            display: grid;
        }

        header .content_h1 {
            left: 50px;
        }

        header .content_h1_2 {
            height: 100px;
        }

        header .content_p1 {
            left: 50px;
        }

        header .div_border1 {
            left: 50px;
        }
    }

    @media (max-width: 1041px) {
        header .content_h1_2 {
            font-size: 70px;
        }

        header .content_h1_3 {
            font-size: 70px;
        }
    }

    @media (max-width: 814px) {
        header .nav_left {
            padding-right: 2rem;
        }

        header .nav_center {
            padding-right: 2rem;
        }

        header .content_h1_2 {
            font-size: 60px;
        }

        header .content_h1_3 {
            font-size: 60px;
        }
    }

    @media (max-width: 730px) {
        header .nav_left {
            padding-right: 1rem;
        }

        header .nav_center {
            padding-right: 1rem;
        }

        header .content_h1_2 {
            font-size: 50px;
        }

        header .content_h1_3 {
            font-size: 50px;
        }

        body main {
            height: 2400px;
        }

        main .Advantages {
            display: grid;
            text-align: center;
            justify-content: center;
        }

        main .Advantages_p {
            text-align: center;
            padding-left: 2rem;
        }
    }

    @media (max-width: 580px) {
        header .nav_menu2 {
            display: grid;
        }

        header .div_border1 {
            left: 0px;
            width: 400px;
        }

        header .div_border2 {
            width: 200px;
        }

        header .nav_center {
            padding-right: 1rem;
        }

        header .content_h1_2 {
            font-size: 50px;
        }

        header .content_h1_3 {
            font-size: 50px;
        }

        main .Advantages {
            display: grid;
            text-align: center;
            justify-content: center;
        }

        main .Advantages_p {
            text-align: center;
            padding-left: 2rem;
        }
    }

    @media (max-width: 460px) {
        header .img_1 {
            width: 180px;
            height: 100px;
        }

        header .content_h1 {
            left: 30px;
            height: 180px;
            width: 0px;
        }

        header .nav_menu2 {
            display: grid;
        }

        header .div_border1 {
            left: 0px;
            width: 300px;
        }

        header .div_border2 {
            width: 120px;
        }

        header .nav_center {
            padding-right: 1rem;
        }

        header .content_h1_2 {
            font-size: 40px;
        }

        header .content_h1_3 {
            font-size: 40px;
        }

    }

    * {
        box-sizing: border-box;
        text-decoration: none;
    }

    body {
        display: block;
    }

    /*Header*/
    header {
        height: 543px;
        top: -10px;

        background-color: #E4DCD6;
    }

    li {
        display: block;
        position: relative;
        font-family: 'Open Sans', sans-serif;
    }

    a {
        color: #000000;
    }

    .img_1 {
        position: relative;
        width: 360px;
        height: 216px;
        top: 49px;
        left: 40px;
    }

    .nav_menu {
        display: grid;
    }

    .nav_menu2 {
        display: flex;
    }

    .nav_left {
        font-size: 24px;
        line-height: 28.8px;
        padding-right: 25rem;
    }

    .nav_center {
        font-size: 16px;
        line-height: 19.2px;
        top: 0.5rem;
        padding-right: 20rem;
    }

    .nav_right {
        font-size: 18px;
        line-height: 21.6px;
        top: 0.5rem;
    }

    .container {
        display: flex;
        justify-content: space-between;
    }

    .content_h1 {
        position: relative;
        top: 70px;
        width: 400px;
        font-family: "Open Sans", serif;
        font-size: 32px;
        line-height: 38.4px;
        left: -251px;
    }

    .content_p1 {
        position: relative;
        top: 10px;
        width: 200px;
        font-family: "Open Sans", serif;
        font-size: 16px;
        line-height: 19px;
        left: -251px;
    }

    .div_border1 {
        background-color: #371B02;
        position: relative;
        top: 13px;
        width: 500px;
        left: -251px;
    }

    .content_p2 {
        position: relative;
        color: #FFFFFF;
        width: 200px;
        font-family: "Open Sans", serif;
        font-size: 16px;
        line-height: 19px;
        left: 14px;
        top: 10px;
    }

    .div_border2 {
        border: 1px solid #FFFFFF;
        position: relative;
        top: -15px;
        width: 360px;
        left: 131px;
    }

    .content_h1_2 {
        display: flex;
        justify-content: end;
        height: 5px;
        font-size: 90px;
        line-height: 72px;
        color: #995218;
    }

    .content_h1_3 {
        font-size: 90px;
        line-height: 72px;
        color: #995218;
    }

    .container_2 {
        height: 10px;
    }

    /*Main*/
    main {
        height: 661px;
        background-color: #371B02;
    }

    .About {
        position: relative;
        display: flex;
        height: 4rem;
        left: 1rem;
        color: white;
    }

    .img_2 {}

    .section_grid_1 {
        position: relative;
        display: grid;
        left: 1rem;
    }

    .section_grid_2 {
        display: grid;
        height: 0px;
    }

    .content_main_p {
        color: white;
        font-family: "Open Sans", serif;
    }

    .content_main_h2 {
        color: white;
        font-family: "Open Sans", serif;
    }

    .Advantages {
        display: flex;
        justify-content: end;
        gap: 8rem;
        background-color: #371B02;
    }

    .Advantages_h {
        height: 25px;
        font: Open Sans;
        font-size: 42px;
        color: #E4DCD6;
    }

    .Advantages_p {
        height: 52px;
        font: Open Sans;
        width: 90px;
        font-size: 22px;
        color: #FFFFFF;
    }

    .Advantages_resolved_issues {
        position: relative;
        right: 2rem;
    }
    </style>
</head>

<body>
    <header>
        <nav class="nav_menu">
            <ul class="nav_menu2">
                <li class="nav_left"><a href="">ТЕСТ Консалтинг</a></li>
                <li class="nav_center" style="padding-right: 2rem;"><a href="">О нас</a></li>
                <li class="nav_center" style="
    padding-right: 2rem;
"><a href="">Услуги</a></li>
                <li class="nav_center"><a href="">Контакты</a></li>
                <li class="nav_right"><a href="">+7&nbsp;(927)&nbsp;123-45-67</a></li>
            </ul>
        </nav>
        <div class="container">
            <img class="img_1" src="{{ asset('img/Rectangle 6864.png') }}" alt="">
            <div>
                <h1 class="content_h1">БИЗНЕС ДОВЕРЯЕТ НАМ СВОЮ БУХГАЛТЕРИЮ</h1>
                <br>
                <br>
                <p class="content_p1">Что нужно учесть при открытии ИП или ООО?</p>
                <div class="div_border1">
                    <p class="content_p2">Консультация</p>
                    <div class="div_border2"></div>
                </div>
            </div>
        </div>
        <div class="container_2">
            <h1 class="content_h1_2">АУТСОРСИНГ БУХГАЛ</h1>
            <h1 class="content_h1_3">ТЕРСКИХ УСЛУГ</h1>
        </div>
    </header>
    <main>
        <section class="About">
            <h1>О НАС</h1>
        </section>
        <section class="Advantages">
            <section class="section_grid_1">
                <img class="img_2" src="{{ asset('img/Tezza-2535 2.png') }}" alt="">
                <h2 class="content_main_h2">Анна Каренина</h2>
                <p class="content_main_p">Бухгалтер и основатель компании «ТЕСТ Консалтинг».</p>
            </section>
            <section class="section_grid_2">
                <p class="content_main_p">Вот уже 10 лет наша команда предоставляет услуги бухгалтерского, налогового и
                    кадрового <br>
                    сопровождения в разных сферах: IT, строительство, производство, общественное питание, <br>
                    оптовая и розничная торговля.</p>
                <section class="Advantages">
                    <div class="Advantages_div_years_of_experience">
                        <h1 class="Advantages_h">15</h1>
                        <p class="Advantages_p">лет опыта</p>
                    </div>
                    <div class="Advantages_regular_customers">
                        <h1 class="Advantages_h">50+</h1>
                        <p class="Advantages_p">постоянных клиентов</p>
                    </div>
                    <div class="Advantages_submitted_reports">
                        <h1 class="Advantages_h">15 000+</h1>
                        <p class="Advantages_p">сданных отчетов</p>
                    </div>
                    <div class="Advantages_resolved_issues">
                        <h1 class="Advantages_h">50 000+</h1>
                        <p class="Advantages_p">решенных вопросов</p>
                    </div>
                </section>
                <p class="content_main_p">За годы работы мы постоянно расширяли наши профессиональные компетенции, <br>
                    но не перестаем учиться и развиваться в профессиональном плане. <br>
                    <br>
                    Мы успешно работаем на всех участках бухгалтерского учёта и всегда в курсе последних изменений в
                    законодательстве. <br>
                    <br>
                    Мы помогаем компаниям разного уровня и частным лица эффективно выстроить и грамотно вести учёт, а
                    также корректно взаимодействовать с фискальными органами.
                </p>
            </section>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>