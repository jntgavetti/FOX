<style>
    #lista {
        margin-top: 100px;
    }


    .teste .acc_header {
        background: rgb(35, 47, 62) !important;
        color: #fff !important;
    }

    .acc_coll {
        display: none !important;
    }

    thead:first-child,
    tbody:first-child {
        border-left: none !important;
    }
</style>

<div class="teste">

    <table class="table text-dark table-bordered">

        <thead class="">
            <th>Interface</th>
            <th>Tipo de placa</th>
            <th>Endereçamento</th>
            <th>Endereço IPv4</th>
            <th>Status</th>
        </thead>



        <tbody>
            <tr class="acc_header border-0">
                <td colspan="5">
                    <img src="_img/interfaces.svg" alt="" width="25px">
                    Interfaces físicas
                </td>
            </tr>
            <tr>
                <td>ETH0</td>
                <td>Física</td>
                <td>Estático</td>
                <td>192.168.0.1, 192.168.5.1</td>
                <td>Ativa</td>
            </tr>
        </tbody>

    </table>



    <div class="lista-interface">


    </div>
</div>