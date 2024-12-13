<?php
include '../modelo/conexion.php';

// Consultas para obtener datos
$sql_cuentas_abiertas = "SELECT COUNT(*) AS total FROM cotizacion WHERE Statusc = 'Cuenta Abierta'";
$result_cuentas_abiertas = $conn->query($sql_cuentas_abiertas);
$total_cuentas_abiertas = $result_cuentas_abiertas->fetch(PDO::FETCH_ASSOC)['total'];

$sql_viajes = "SELECT COUNT(*) AS total FROM viaje WHERE Status IN ('En Proceso', 'En Curso', 'Completado')";
$result_viajes = $conn->query($sql_viajes);
$total_viajes = $result_viajes->fetch(PDO::FETCH_ASSOC)['total'];

$sql_clientes_activos = "SELECT COUNT(*) AS total FROM cliente WHERE Status = 'Activo'";
$result_clientes_activos = $conn->query($sql_clientes_activos);
$total_clientes_activos = $result_clientes_activos->fetch(PDO::FETCH_ASSOC)['total'];

// Porcentajes (simulación, puedes calcularlos con datos históricos)
$porcentaje_cuentas = ($total_cuentas_abiertas / 1000) * 100; // Ajusta el denominador según tus métricas
$porcentaje_viajes = ($total_viajes / 1000) * 100;
$porcentaje_clientes = ($total_clientes_activos / 1000) * 100;

    // Consulta para obtener datos de los viajes
    $sql = "SELECT Status, COUNT(*) AS total FROM viaje WHERE Status IN ('En Proceso', 'Completado') GROUP BY Status";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
  <link rel="stylesheet" href="./assets/icons/font-awesome-4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="./assets/css/w3pro-4.13.css">
  <link rel="stylesheet" href="./assets/css/w3-theme.css">
  <link rel="stylesheet" href="./assets/css/admin-styles.css">
  <link rel="stylesheet" href="./assets/css/scrollbar.css">
</head>

<body class="w3-light-grey">
  <input id="sidebar-control" type="checkbox" class="w3-hide">
  <div id="app">
    <div class="w3-top w3-card" style="height:54px">
      <div class="w3-flex-bar w3-theme w3-left-align">
        <div class="admin-logo w3-bar-item w3-hide-medium w3-hide-small">
          <h5 class="" style="line-height:1; margin:0!important; font-weight:300">
            <a href="./index.html" class="w3-button w3-bold">
              <img src="./assets/admin-logo.png" alt="w3mix" class="w3-image" width="26"> &nbsp; ANDUG </a>
          </h5>
        </div>
        <label for="sidebar-control" class="w3-button w3-large w3-opacity-min"><i class="fa fa-bars"></i></label>
        <div class="w3-container w3-display-container" style="width:40%">
          <div class="w3-display-right w3-padding-small w3-margin-right" onclick="this.parentNode.children[1].focus()">
            <i class="fa fa-search w3-opacity-max"></i>
          </div>
          <input type="text" class="w3-input w3-border w3-round w3-small w3-padding-small w3-gray-lighter w3-show-inline-block" placeholder="Enter keywords">
        </div>
        <div class="w3-right">
          <button type="button" class="w3-button w3-large w3-opacity-min"><i class="fa fa-envelope-open"></i></button>
          <button type="button" class="w3-button w3-large w3-opacity-min"><i class="fa fa-bell"></i></button>
        </div>
        <div class="text-right">
          <div class="w3-button">
            <div class="w3-circle w3-center w3-text-white w3-primary" style="width:38px; height:38px">
              <i class="fa fa-fw fa-user fa" style="margin-top:11px"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <nav id="sidebar" class="w3-sidebar w3-top w3-bottom w3-collapse w3-white w3-border-right w3-border-top scrollbar" style="z-index:3;width:230px;height:auto;margin-top:54px;border-color:rgba(0, 0, 0, .1)!important" id="mySidebar">
      <div class="w3-bar-item w3-border-bottom w3-hide-large" style="padding:6px 0">
        <label for="sidebar-control" class="w3-left w3-button w3-large w3-opacity-min" style="background:white!important"><i class="fa fa-bars"></i></label>
        <h5 class="" style="line-height:1; margin:0!important; font-weight:300">
          <a href="./index.html" class="w3-button" style="background:white!important">
            <img src="./assets/admin-logo.png" alt="w3mix" class="w3-image"> &nbsp; ANDUG</a>
        </h5>
      </div>
      <div class="w3-bar-block">
        <span class="w3-bar-item w3-padding w3-small w3-opacity" style="margin-top:8px"> NAVEGACIÓN PRINCIPAL </span>
        <a href="./index.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-bar-chart"></i>&nbsp; Dashboard </a>
        <a href="./icons.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-fire"></i>&nbsp; UI Icons </a>
        <a href="./forms.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-edit"></i>&nbsp; Forms </a>
        <a href="./tables.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-table"></i>&nbsp; Tables </a>
        <a href="./profile.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-user-circle"></i>&nbsp; Profile </a>
        <a href="./login.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-lock"></i>&nbsp; Login </a>
        <a href="./register.html" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-sign-in"></i>&nbsp; Registration </a>
        <span class="w3-bar-item w3-padding w3-small w3-opacity"> LABELS </span>
        <a href="#dashboard" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-coffee w3-text-danger"></i>&nbsp; Important </a>
        <a href="#dashboard" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-circle-o-notch w3-text-success"></i>&nbsp; Warning </a>
        <a href="#dashboard" class="w3-bar-item w3-button w3-padding-large w3-hover-text-primary">
          <i class="fa fa-fw fa-share-alt w3-text-info"></i>&nbsp; Information </a>
      </div>
    </nav>
    <div class="w3-main" style="margin-top:54px">
      <div style="padding:16px 32px">
        <div class="w3-white w3-round w3-margin-bottom w3-border" style="">
          <div class="w3-row">
            <div class="w3-col l3 w3-container w3-border-right">
              <div class="w3-padding">
              <h5><?php echo $total_cuentas_abiertas; ?> 
                            <span class="w3-right"><i class="fa fa-fw fa-shopping-cart"></i></span>
                        </h5>
                        <div class="progress w3-light" style="height:3px;">
                            <div class="progress-bar w3-info" style="width:<?php echo $porcentaje_cuentas; ?>%;height:3px;"></div>
                        </div>
                        <p>Cuentas abiertas <span class="w3-right">+<?php echo round($porcentaje_cuentas, 1); ?>% ↑</span></p>
                    </div>
                </div>
                <div class="w3-col l3 w3-container w3-border-right">
                    <div class="w3-padding">
                        <h5><?php echo $total_viajes; ?> 
                            <span class="w3-right"><i class="fa fa-fw fa-usd"></i></span>
                        </h5>
                        <div class="progress w3-light" style="height:3px;">
                            <div class="progress-bar w3-success" style="width:<?php echo $porcentaje_viajes; ?>%;height:3px;"></div>
                        </div>
                        <p>Viajes <span class="w3-right">+<?php echo round($porcentaje_viajes, 1); ?>% ↑</span></p>
                    </div>
                </div>
                <div class="w3-col l3 w3-container w3-border-right">
                    <div class="w3-padding">
                        <h5><?php echo $total_clientes_activos; ?> 
                            <span class="w3-right"><i class="fa fa-fw fa-eye"></i></span>
                        </h5>
                        <div class="progress w3-light" style="height:3px;">
                            <div class="progress-bar w3-warning" style="width:<?php echo $porcentaje_clientes; ?>%;height:3px;"></div>
                        </div>
                        <p>Clientes <span class="w3-right">+<?php echo round($porcentaje_clientes, 1); ?>% ↑</span></p>
                    </div>
                    </div>
            <div class="w3-col l3 w3-container">
              <div class="w3-padding">
                <h5>5630 <span class="w3-right"><i class="fa fa-fw fa-envira"></i></span></h5>
                <div class="progress w3-light" style="height:3px;">
                  <div class="progress-bar w3-danger" style="width:55%;height:3px;"></div>
                </div>
                <p>Facturas pagadas <span class="w3-right">+2.2% ↑</span></p>
              </div>
            </div>
          </div>
        </div>
<div class="w3-row-padding w3-stretch">
          <div class="w3-col l8">
            <div class="w3-white w3-round w3-margin-bottom w3-border" style="">
              <header class="w3-padding-large w3-large w3-border-bottom" style="font-weight: 500">Viajes</header>
              <div class="w3-bar w3-padding">
                <div class="w3-bar-item w3-text-dark w3-padding-small"><i class="fa fa-circle" style="color: #14abef"></i>En ruta</div>
                <div class="w3-bar-item w3-text-dark w3-padding-small"><i class="fa fa-circle" style="color: #ade2f9"></i> Finalizados</div>
              </div>
              <div class="w3-padding-large" style="height: 256px;position:relative">
                <canvas id="chart1"></canvas>
              </div>
              <div class="w3-row w3-center w3-border-top">
                <div class="w3-col s4">
                  <div class="w3-padding-large">
                    <h5 style="margin:0">45.87M</h5>
                    <small class="w3-opacity-min">Overall Visitor <span> <i class="fa fa-arrow-up"></i> 2.43%</span></small>
                  </div>
                </div>
                <div class="w3-col s4">
                  <div class="w3-padding-large">
                    <h5 style="margin:0">15:48</h5>
                    <small class="w3-opacity-min">Visitor Duration <span> <i class="fa fa-arrow-up"></i> 12.65%</span></small>
                  </div>
                </div>
                <div class="w3-col s4">
                  <div class="w3-padding-large">
                    <h5 style="margin:0">245.65</h5>
                    <small class="w3-opacity-min">Pages/Visit <span> <i class="fa fa-arrow-up"></i> 5.62%</span></small>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="w3-col l4">
            <div class="w3-white w3-round w3-margin-bottom w3-border" style="">
              <header class="w3-padding-large w3-large w3-border-bottom" style="font-weight: 500">Rutas</header>
              <div class="w3-padding-large" style="height: 188px;position:relative">
                <canvas id="chart2"></canvas>
              </div>
              <table class="w3-table w3-bordered w3-border-top">
                <tr>
                  <td><i class="fa fa-circle mr-2" style="color: #14abef"></i> Mzo - Mty</td>
                  <td>$5856</td>
                  <td>+55%</td>
                </tr>
                <tr>
                  <td><i class="fa fa-circle mr-2" style="color: #02ba5a"></i> Mzo - Mty</td>
                  <td>$2602</td>
                  <td>+25%</td>
                </tr>
                <tr>
                  <td><i class="fa fa-circle mr-2" style="color: #d13adf"></i> Mzo - Mty</td>
                  <td>$1802</td>
                  <td>+15%</td>
                </tr>
                <tr>
                  <td><i class="fa fa-circle mr-2" style="color: #fba540"></i> Mzo - Mty</td>
                  <td>$1105</td>
                  <td>+5%</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="w3-white w3-round w3-margin-bottom w3-border" style="">
          <header class="w3-padding-large w3-large w3-border-bottom" style="font-weight: 500">Viajes</header>
          <div class="w3-responsive">
            <table class="w3-table w3-bordered">
              <tr>
                <th>ID Viaje</th>
                <th>Camión</th>
                <th>Operador</th>
                <th>Cliente</th>
                <th>Ruta</th>
                <th>Cotización</th>
                <th>Fecha Despacho</th>
                <th>Fecha Llegada</th>
                <th>Pedimentos</th>
                <th>Contenedores</th>
                <th>Gastos</th>
                <th>Status</th>
                <th>Acciones</th>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Headphone GL</td>
                <td>$1,840 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-danger"></i> pending </span>
                </td>
                <td>10 July 2018</td>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Clasic Shoes</td>
                <td>$1,520 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-success"></i> completed </span>
                </td>
                <td>12 July 2018</td>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Hand Watch</td>
                <td>$1,620 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-warning"></i> delayed </span>
                </td>
                <td>14 July 2018</td>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Hand Camera</td>
                <td>$2,220 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-info"></i> on schedule </span>
                </td>
                <td>16 July 2018</td>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Iphone-X Pro</td>
                <td>$9,890 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-success"></i> completed </span>
                </td>
                <td>17 July 2018</td>
              </tr>
              <tr>
                <td>
                  <i class="fa fa-image w3-opacity w3-margin-left"></i>
                </td>
                <td>Ladies Purse</td>
                <td>$3,420 USD</td>
                <td>
                  <span class="badge-dot">
                    <i class="w3-danger"></i> pending </span>
                </td>
                <td>18 July 2018</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <footer class="w3-padding w3-border-top w3-center w3-white w3-margin-top">
        <span class="w3-opacity"> <span class="w3-text-red"></span> <a href="" target="_blank"><strong></strong></a></span>
      </footer>
    </div>
  </div>
  <script src="./assets/plugins/chartjs/Chart.min.js"></script>
  <script src="./assets/plugins/chartjs/dashboard.js"></script>
</body>

</html>
