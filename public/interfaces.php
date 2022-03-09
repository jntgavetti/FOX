<?php
    $action = "list";
    require "interfaces.require.php";
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

    <div id="pesquisa">
        <!-- <input type="search" class="d-inline ml-5"> -->
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

            <tr class="acc_category tr_phy">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>LAN</span>
                    <span class="badge bg-secondary"></span>
                </td>
            </tr>


            <?php
            foreach ($interfaces as $key => $interface) {
                if ($interface['funcao'] == 'lan') {
                    if ($interface['tipo'] == 'fisica') {
            ?>

                        <tr class='tr_phy'>
                            <td class='td_collapse'></td>
                            <td class="table-iface">
                            <img src="_img/ethernet_lan.png" class='phy_icon lan_icon'>
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td><?= $interface["mac"] ?></td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>

                    <?php } else { ?>
                        <tr class='tr_vir tr_child <?= $interface["interface_pai"]?>'>
                        <td class='td_collapse'></td>
                            <td class="table-iface">
                                <img src="_img/hierarchy_lan.png" class="vir_icon child_icon">
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td>N/D</td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>

            <?php

                    }
                }
            }
            ?>



            <tr class="acc_category tr_phy">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>WAN</span>
                </td>
            </tr>

            <?php
            foreach ($interfaces as $key => $interface) {
                if ($interface['funcao'] == 'wan') {
                    if ($interface['tipo'] == 'fisica') {
            ?>

                        <tr class='tr_phy'>
                            <td class='td_collapse'></td>
                            <td class="table-iface">
                            <img src="_img/ethernet_wan.png" class='phy_icon wan_icon'>
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td><?= $interface["mac"] ?></td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>

                    <?php } else { ?>
                        <tr class='tr_vir tr_child'>
                            <td class='td_collapse'>
                           
                            </td>
                            
                            <td class="table-iface">
                            <img src="_img/hierarchy_wan.png" class="vir_icon child_icon">
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td>N/D</td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>

            <?php

                    }
                }
            }
            ?>

            <tr class="acc_category tr_phy">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>VPN</span>
                </td>
            </tr>

            <?php
            foreach ($interfaces as $key => $interface) {
                if ($interface['funcao'] == 'vpn') { ?>


                        <tr class='tr_vir'>
                            <td class='td_collapse'></td>
                            <td class="table-iface">
                            <img src="_img/vpn.png" class='vir_icon vpn_icon'>
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td><?= $interface["mac"] ?></td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>
                   
                     
            <?php
                   
                }
            }
            ?>

            <tr class="acc_category tr_phy">
                <td class="td_collapse"></td>
                <td colspan="9">
                    <span class='title_category'>DMZ</span>
                </td>
            </tr>

            <?php
            foreach ($interfaces as $key => $interface) {
                if ($interface['funcao'] == 'dmz') { ?>


                        <tr class='tr_vir'>
                            <td class='td_collapse'></td>
                            <td class="table-iface">
                            <img src="_img/dmz.png" class='vir_icon dmz_icon'>
                                <span>
                                <?= $interface["interface"] ?>
                                </span>
                            </td>
                            <td><?= $interface["tipo"] ?></td>
                            <td><?= $interface["mac"] ?></td>
                            <td><?= $interface["ipv4"] ?></td>
                            <td><?= $interface["ipv6"] ?></td>
                            <td><?= $interface["status"] ?></td>
                        </tr>
                   
                     
            <?php
                   
                }
            }
            ?>
        </tbody>

    </table>

</div>
<script src="_js/interfaces.js"></script>