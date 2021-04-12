<?php
// A sessão precisa ser iniciada em cada página diferente
if (!isset($_SESSION)) session_start();
// Verifica se não há a variável da sessão que identifica o usuário
if (!isset($_SESSION['id_usuario'])) {
    // Destrói a sessão por segurança
    session_destroy();
    // Redireciona o visitante de volta pro login
    header("Location: index.php");
    exit;
}
?>
<html>

<head>
    <link rel="stylesheet" href="_css/primary.css" />
</head>

<body>

    <nav class="nav-main nav-ident">
        <div class="p-0 logo">
            <a class="" href="#">
                <img src="_img/fox_icon.png" width="40" height="40" class="" alt="">
            </a>
        </div>

        <legend>Painel de controle</legend>
        <hr>
        <ul class="nav-ident">
            <!--
            <li class="nav-ident">
                <a class="link-item active nav-ident liDisabled" href="#">
                <img src="_img/grafico.svg" width="25px" alt="">
                    <span class="monitor">Monitoramento</span>
                </a>
            </li>

            <li class="nav-ident">
                <a class="link-item nav-ident liDisabled" href="#">
                <img src="_img/internet.svg" width="25px" alt="">
                    <span class="net">Internet</span>
                    <i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-down text-secondary nav-ident"></i>
                </a>
                <ul class="dropdown-ul">
                    <li>
                        <a href="status_internet.php">Status</a>
                    </li>

                    <li>
                        <a href="listagem_provedores.php">Provedores (ISP)</a>
                    </li>
                </ul>
            </li>
            -->
            <li class="nav-ident">
                <a class="link-item nav-ident liDisabled" href="#">
                    <img src="_img/proxy.svg" width="25px" alt="">
                    <span class="navega">Navegação</span>
                    <i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-down text-secondary nav-ident"></i>
                </a>
                <ul class="dropdown-ul">
                    <li>
                        <a href="listagem_dispositivos.php">Dispositivos</a>
                    </li>
                
                    <li>
                        <a href="listagem_grupos.php">Grupos</a>
                    </li>
                    
                </ul>
            </li>
            <li class="nav-ident">
                <a class="link-item nav-ident liDisabled" href="#">
                <img src="_img/firewall.svg" width="25px" alt="">
                    <span class="rede">Firewall</span>
                    <i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-down text-secondary nav-ident"></i>
                </a>
                <ul class="dropdown-ul">
                    <li>
                        <a href="redireciona_portas.php">Redirecionamento de portas</a>
                    </li>
                </ul>
            </li>
            

            <!--
            <li class="nav-ident">
                <a class="link-item active nav-ident liDisabled" href="#">
                    <img src="_img/interfaces.svg" width="25px" alt="">
                    <span class="monitor">Interfaces</span>
                    <i class="arrow-icon arrow-disabled icon-disabled fas fa-chevron-down nav-ident"></i>
                </a>
                <ul class="dropdown-ul">
                    <li>
                        <a href="listagem_lan.php">Local (Lan)</a>
                    </li>

                    <li>
                        <a href="listagem_wan.php">Externa (Wan)</a>
                    </li>
                </ul>
            </li>

            <li class="nav-ident">
                <a class="link-item nav-ident liDisabled" href="#">
                    <img src="_img/vpn.svg" width="25px" alt="">
                    <span class="vpn">VPN</span>
                </a>
            </li>
            -->

            
        </ul>


    </nav>

    <script src="_js/jquery/jquery.js"></script>
    <script src="_js/menu.js"></script>

</body>

</html>