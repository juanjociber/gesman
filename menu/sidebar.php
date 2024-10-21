<style>
    @media screen and (max-width: 767px) {
        .user-hidden {
            display: none;
        }
    }
</style>
<header class="gpem-hide-print">
    <div style="display: flex; align-items: center;">
        <div style="margin-right: 10px; width: 25px;">
            <span id="button-menu" class="fas fa-bars" onclick="FnMenuToggle(); return false;"></span>
        </div>       
        <div>
            <img src="/mycloud/logos/logo-gpem.png" alt="" height="40">
        </div>
        <div style="align-self: center; flex: 1 0 0%;">
            <label class="menu-user m-0"><a class="btn btn-outline-danger btn-sm text-white font-weight-bold" href="/gesman/Salir.php" role="button"><i class="fas fa-sign-out-alt"></i><span class="user-hidden"><?php echo $_SESSION['UserName']; ?></span></a></label>
        </div>
    </div>
    <div id="overlay" onclick="FnOcultarFondoMenu(); return false;"></div>
    <nav class="navegacion">
        <ul class="menu ps-0">
            <li class="title-menu">Menú</li>
            <li><a href="/gesman/Empresas.php" id="MenuEmpresas"><i class="fas fa-building icon-menu"></i> Empresas</a></li>
            <li><a href="/gesman/Ordenes.php" id="MenuOrdenes"><i class="fas fa-list icon-menu"></i> Ordenes</a></li>
            <li><a href="/solicitudes/Solicitudes.php" id="MenuSolicitudes"><i class="fas fa-list icon-menu"></i> Solicitudes</a></li>
            <li><a href="/checklists/CheckLists.php" id="MenuCheckLists"><i class="fas fa-list icon-menu"></i> CheckLists</a></li>
            <?php 
                if($_SESSION['RolMan']>1){
                    echo '<li><a href="/informes/Informes.php" id="MenuInformes"><i class="fas fa-list icon-menu"></i> Informes</a></li>';
                }
            ?>
            <?php
                if($_SESSION['RolMan']>2){
                    echo '
                    <li class="item-submenu" menu="2">
                        <a href="#" id="MenuSistemas" onclick="FnMostrarSubmenu(this); return false;"><span class="fas fa-cog"></span> Sistema</a>
                        <ul class="submenu ps-0">
                            <li class="title-menu"><span class="fas fa-cog icon-menu"></span> Sistema</li>
                            <li class="go-back" onclick="FnOcultarSubmenu(this); return false;">Atras</li>
                            <li><a href="/gesman/Flotas.php" id="MenuSistemasFlotas"><span class="fas fa-hdd icon-menu"></span> Flotas</a></li>
                            <li><a href="/gesman/Equipos.php" id="MenuSistemasEquipos"><span class="fas fa-hdd icon-menu"></span> Equipos</a></li>
                            <li><a href="/gesman/Sistemas.php" id="MenuSistemasSistemas"><span class="fas fa-hdd icon-menu"></span> Sistemas</a></li>
                            <li><a href="/gesman/Origenes.php" id="MenuSistemasOrigenes"><span class="fas fa-hdd icon-menu"></span> Origenes</a></li>
                            <li><a href="/gesman/Clientes.php" id="MenuSistemasClientes"><span class="fas fa-hdd icon-menu"></span> Clientes</a></li>
                            <li><a href="/gesman/Contactos.php" id="MenuSistemasContactos"><span class="fas fa-hdd icon-menu"></span> Contactos</a></li>
                        </ul>
                    </li>';
                }
            ?>
            <li><a href="/gesman/Salir.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </nav>
</header>