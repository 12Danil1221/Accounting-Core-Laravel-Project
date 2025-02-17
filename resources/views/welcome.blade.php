<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    @media (max-width: 444px) {
        main {
            display: grid;
        }

        main .Advantages_h {
            height: 65px;
        }
    }

    @media (max-width: 768px) {
        main {
            display: grid;
        }

        main .Advantages_h {
            height: 65px;
        }
    }

    @media (max-width: 1200px) {
        main {
            display: grid;
        }

        main .Advantages_h {
            height: 65px;
        }
    }

    .Advantages {
        display: flex;
        justify-content: end;
        align-items: center;
        gap: 10rem;
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
    </style>
</head>

<body>
    <main>
        <section class="Advantages">
            @foreach ($Advantages as $item)
            <div class="Advantages_div_years_of_experience">
                <h1 class="Advantages_h">{{ $item->Advantages_integer }}</h1>
                <p class="Advantages_p">{{ $item->Advantages_description }}</p>
            </div>
            @endforeach
        </section>
    </main>
</body>

</html>