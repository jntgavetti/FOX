<?php
$action = "list";
require "interfaces.controller.php";
?>


<div class="body-interface main">

    <div class="div_btn">
        <button class="btn btn-outline-success btn-sm btn_add">
            <i class="fas fa-plus coll_icon"></i>
            Adicionar interface
        </button>

        <button class="btn btn-outline-secondary btn-sm btn_edit disabled">
            <i class="fas fa-pen coll_icon"></i>
            Editar
        </button>

        <button class="btn btn-outline-danger btn-sm btn_delete disabled">
            <i class="fas fa-trash coll_icon"></i>
            Remover
        </button>
    </div>

    <table class="">

        <thead class="text-center">
            <th></th>
            <th>Interface</th>
            <th>Tipo</th>
            <th>Mac Address</th>
            <th>IPv4</th>
            <th>IPv6</th>
            <th>Status</th>
        </thead>

        <tbody>

            <tr class="acc_category">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>LAN</span>
                    <span class="badge bg-secondary">2</span>
                </td>
            </tr>


            <?php
                foreach($interfaces as $interface){
                    
                    if($interface->funcao == 'lan'){
                        if($interface->tipo == 'fisica'){
                            $tr = 'tr_phy';
                            $icon = 'fas fa-ethernet phy_icon lan_icon';
                        }else{
                            $tr = 'tr_vir';
                            $icon = 'fas fa-sitemap vir_icon lan_icon';
                        }
            ?>

            <tr class=<?=$tr?>>
                <td class="td_collapse"></td>
                <td>
                    <i class="<?=$icon?>"></i>
                    <?=$interface->nome?>
                </td>
                <td><?=$interface->tipo?></td>
                <td><?=$interface->mac?></td>
                <td>
                    <?php 
                    
                    echo str_replace(',', '<br>', $interface->ipv4);
                        
                    ?>
                </td>
                <td><?=$interface->ipv6?></td>
                <td><?=$interface->status?></td>
            </tr>

            <?php
                        
                    }
                }
            ?>


            <tr class="acc_category tr_phy">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>WAN</span>
                    <span class="badge bg-secondary">2</span>
                </td>
            </tr>


            <?php
                foreach($interfaces as $interface){
                    
                    if($interface->funcao == 'wan'){
                        if($interface->tipo == 'fisica'){
                            $tr = 'tr_phy';
                            $icon = 'fas fa-ethernet phy_icon wan_icon';
                        }else{
                            $tr = 'tr_vir';
                            $icon = 'fas fa-sitemap vir_icon wan_icon';
                        }
            ?>

            <tr class=<?=$tr?>>
                <td class="td_collapse"></td>
                <td>
                    <i class="<?=$icon?>"></i>
                    <?=$interface->nome?>
                </td>
                <td><?=$interface->tipo?></td>
                <td><?=$interface->mac?></td>
                <td><?=$interface->ipv4?></td>
                <td><?=$interface->ipv6?></td>
                <td><?=$interface->status?></td>
            </tr>

            <?php
                        
                    }
                }
            ?>
        </tbody>

    </table>

</div>
<script src="_js/interfaces.js"></script>