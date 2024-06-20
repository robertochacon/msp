<x-filament-panels::page>

<hr>

<div style="display: flex;flex-wrap:wrap;justify-content:space-around;">

    <div>
        <center>
            <h2><b>Carga de clientes</b></h2><hr><br>
            {{ $this->cargaClients }}
            <img src="https://st2.depositphotos.com/5266903/11817/v/450/depositphotos_118171126-stock-illustration-staff-flat-vector-icon.jpg" width="150" alt="">
        </center>
    </div>

    <div>
        <center>
            <h2><b>Carga de Creditos</b></h2><hr><br>
            {{ $this->cargaCredits }}
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT1RkEoSgQnRmnpMZ-35-ETmFyuTyKfn36-MA&s" width="150" alt="">
        </center>
    </div>

    <div>
        <center>
            <h2><b>Carga de Movimientos</b></h2><hr><br>
            {{ $this->cargaMovements }}
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFpWIfOuB_xiitU9KPkD7ZOtGK-cgW9zObtA&s" width="150" alt="">
        </center>
    </div>


</div>


</x-filament-panels::page>
