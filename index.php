<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Curricular </title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Krub:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preload" href="css/styles.css" as="style">
    <link href="css/styles.css" rel="stylesheet">
    <link rel="icon" href="img/upalogo.ico" >
    
</head>

<body>


    <header>
        <div class="headings">
            <section class="head imgizq">
                <a href="https://www.upatlacomulco.edu.mx/">
                    <img src="img/upa_logo.png" alt="logoupa" width="250" height="60"></a>
            </section>
            <section class="head">
                <h1 class="titulo">SIIUPA</h1>
            </section>
            <section class="head imgder">
                <a href="http://201.132.104.6/sii/">
                    <img src="img/logoiscnew.png" alt="logoupa" width="120" height="100"></a>
            </section>
        </div>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">

            <a href="https://www.upatlacomulco.edu.mx/">Pagina Oficial</a>
        </nav>
    </div>



    <?php


    if (isset($_GET['matricula'])) {

        include("db.php");

        $matricula = $_GET['matricula'];
        // echo"matricula existente";
        $records = mysqli_query($conexion, "SELECT p.nombre as person, p.apellidopat,p.apellidomat, a.nocuenta, c.nombre  
FROM alumno a
inner join persona p  on p.idpersonas=a.idpersonas 
inner join curso c on a.idcurso=c.idcurso  
where nocuenta=$matricula");

        while ($user = mysqli_fetch_assoc($records)) {


    ?>
            <h1>MAPA CURRICULAR </h1>
            <h2 class="bienvenida">
        <?php
            echo $user['person']." ".$user['apellidopat']." ".$user['apellidomat'] ;
            ?>
            <br>
             <?php
            echo $user['nocuenta'];
            ?>
            <br>
            <?php
            echo $user['nombre'];
        }
    }
        ?>
            </h2>

            <table class="caltab">
            <thead class="thead"> <tr>
              <td class="tds">cuatrimestre 1</td>
              <td class="tds">cuatrimestre 2</td>
              <td class="tds">cuatrimestre 3</td>
              <td class="tds">cuatrimestre 4</td>
              <td class="tds">cuatrimestre 5</td>
              <td class="tds">cuatrimestre 6</td>
              <td class="tds">cuatrimestre 7</td>
              <td class="tds">cuatrimestre 8</td>
              <td class="tds">cuatrimestre 9</td>
              <td class="tds">cuatrimestre 10</td>
                   
              </tr>
              </thead>
                  
                <?php

                if (isset($_GET['matricula'])) {

                    include("db.php");

                    $matricula = $_GET['matricula'];
                    
                    // echo"matricula existente";
                    // $records = $conn->prepare('SELECT idpersonas FROM alumno WHERE nocuenta=:matricula');
                    $resultado = mysqli_query($conexion, " SELECT p.nombre, mp.cuatrimestre, mp.idmateria , nombre_certificado,MAX(r.calificacion) as calificacion, lugar, r.estatus ,r.idresultado
                    ,count(*) as repeticiones,
                    (CASE 
                    when count(*)=1 && MAX(r.calificacion>=70) then 'FIRMADO'
                    when count(*)=1 && MAX(r.calificacion>=60) && r.estatus='FIRMADO' then 'FIRMADO'
                    when count(*)=2 && MAX(r.calificacion>60) then 'FIRMADO'
                    when count(*)=2 && MAX(r.calificacion>=60) then 'CAPTURADO'
                    when count(*)=1 && MAX(r.calificacion>=60) && r.estatus='CAPTURADO' then 'CAPTURADO'
                    when r.estatus='PENDIENTE' then 'PENDIENTE'
                    end)
                    as stat
                    FROM alumno a
                    inner join persona p  on p.idpersonas=a.idpersonas
                    inner join plan_estudios pl on a.idplan_estudios=pl.idplan_estudios
                    inner join materiaplan mp on pl.idplan_estudios=mp.idplan_estudios
                    inner join resultado r on mp.idmateria=r.idmateria
                    where a.nocuenta=$matricula and r.nocuenta=a.nocuenta 
                    group by mp.idmateria
                    union all
                    SELECT  p.nombre,mp.cuatrimestre, mp.idmateria , nombre_certificado, null  as calificacion, lugar, 'no cursada' as status, null as idresultado,null as repeticiones, 'no cursada'  as stat
                    FROM alumno a
                    inner join persona p  on p.idpersonas=a.idpersonas 
                    inner join plan_estudios pl on a.idplan_estudios=pl.idplan_estudios
                    inner join materiaplan mp on pl.idplan_estudios=mp.idplan_estudios
                    where a.nocuenta=$matricula and mp.idmateria NOT IN (
                    SELECT mp.idmateria
                    FROM alumno a
                    inner join persona p  on p.idpersonas=a.idpersonas
                    inner join plan_estudios pl on a.idplan_estudios=pl.idplan_estudios
                    inner join materiaplan mp on pl.idplan_estudios=mp.idplan_estudios
                    inner join resultado r on mp.idmateria=r.idmateria
                    where a.nocuenta=$matricula and r.nocuenta=a.nocuenta 
                    GROUP BY nombre_certificado
                    )
                    ORDER by lugar, cuatrimestre ");

?>

<?php

$td='';
                    while ($consulta = mysqli_fetch_array($resultado)) {
        
                
                ?>

<?php
                        if($consulta['cuatrimestre']==1){
                            
                       ?>
                      <tr>
                      <?php
                     if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                     {
                         $td = "<td class='color-pasado'>";  
                     }
                     elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                     {
                      $td = "<td class='color-reprobado'>";
                      }
                      elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                      {
                       $td = "<td class='color-recursando'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                       {
                        $td = "<td class='color-cursando'>";
                        }
                        elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                        elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                       {
                        $td = "<td class='color-baja'>";
                        }
                        
                      else{
                         $td = "<td class='color-nocursada'>";
                      }
                        
                         echo $td;
                         ?>

                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        
                        elseif($consulta['cuatrimestre']==2){
                            ?> 
                           <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                      {
                          $td = "<td class='color-pasado'>";  
                      }
                      elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                      {
                       $td = "<td class='color-reprobado'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                       {
                        $td = "<td class='color-recursando'>";
                        }
                        elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                        {
                         $td = "<td class='color-baja'>";
                         }
                         
                       else{
                          $td = "<td class='color-nocursada'>";
                       }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==3){
                            ?> 
                            <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                      {
                          $td = "<td class='color-pasado'>";  
                      }
                      elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                      {
                       $td = "<td class='color-reprobado'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                       {
                        $td = "<td class='color-recursando'>";
                        }
                        elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                        {
                         $td = "<td class='color-baja'>";
                         }
                         
                       else{
                          $td = "<td class='color-nocursada'>";
                       }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==4){
                            ?> 
                            <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                        {
                            $td = "<td class='color-pasado'>";  
                        }
                        elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                        {
                         $td = "<td class='color-reprobado'>";
                         }
                         elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                         {
                          $td = "<td class='color-recursando'>";
                          }
                          elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                          {
                           $td = "<td class='color-cursando'>";
                           }
                           elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                           elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                          {
                           $td = "<td class='color-baja'>";
                           }
                           
                         else{
                            $td = "<td class='color-nocursada'>";
                         }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==5){
                            ?> 
                            <?php
                     if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                     {
                         $td = "<td class='color-pasado'>";  
                     }
                     elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                     {
                      $td = "<td class='color-reprobado'>";
                      }
                      elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                      {
                       $td = "<td class='color-recursando'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                       {
                        $td = "<td class='color-cursando'>";
                        }
                        elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                        elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                       {
                        $td = "<td class='color-baja'>";
                        }
                        
                      else{
                         $td = "<td class='color-nocursada'>";
                      }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                           
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==6){
                            ?> 
                            <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                      {
                          $td = "<td class='color-pasado'>";  
                      }
                      elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                      {
                       $td = "<td class='color-reprobado'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                       {
                        $td = "<td class='color-recursando'>";
                        }
                        elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                        {
                         $td = "<td class='color-baja'>";
                         }
                         
                       else{
                          $td = "<td class='color-nocursada'>";
                       }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==7){
                            ?> 
                            <?php
                     if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                     {
                         $td = "<td class='color-pasado'>";  
                     }
                     elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                     {
                      $td = "<td class='color-reprobado'>";
                      }
                      elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                      {
                       $td = "<td class='color-recursando'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                       {
                        $td = "<td class='color-cursando'>";
                        }
                        elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                        elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                       {
                        $td = "<td class='color-baja'>";
                        }
                        
                      else{
                         $td = "<td class='color-nocursada'>";
                      }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==8){
                            ?> 
                            <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                      {
                          $td = "<td class='color-pasado'>";  
                      }
                      elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                      {
                       $td = "<td class='color-reprobado'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                       {
                        $td = "<td class='color-recursando'>";
                        }
                        elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                         elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                        {
                         $td = "<td class='color-baja'>";
                         }
                         
                       else{
                          $td = "<td class='color-nocursada'>";
                       }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                            
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                        elseif($consulta['cuatrimestre']==9){
                            ?> 
                            <?php
                     if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                     {
                         $td = "<td class='color-pasado'>";  
                     }
                     elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                     {
                      $td = "<td class='color-reprobado'>";
                      }
                      elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                      {
                       $td = "<td class='color-recursando'>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                       {
                        $td = "<td class='color-cursando'>";
                        }
                        elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando'>";
                         }
                        elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                       {
                        $td = "<td class='color-baja'>";
                        }
                        
                      else{
                         $td = "<td class='color-nocursada'>";
                      }
                        
                         echo $td;
                         ?>
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                           
                            <?php echo $consulta['calificacion'] ?>
                            </td>
                            <?php
                        }
                           elseif($consulta['cuatrimestre']==10){
                            ?> 
                               <?php
                      if ($consulta['stat']=='FIRMADO'  && $consulta['calificacion']>=70 )
                      {
                          $td = "<td class='color-pasado' rowspan='7'>";  
                      }
                      elseif ($consulta['stat']=='FIRMADO' && $consulta['calificacion']==60)
                      {
                       $td = "<td class='color-reprobado rowspan='7''>";
                       }
                       elseif ($consulta['stat']=='CAPTURADO' && $consulta['calificacion']==60  && $consulta['repeticiones']==2  )
                       {
                        $td = "<td class='color-recursando' rowspan='7'>";
                        }
                        elseif ($consulta['stat']=='CAPTURADO'&& $consulta['calificacion']==60  && $consulta['repeticiones']==1 )
                        {
                         $td = "<td class='color-cursando' rowspan='7'>";
                         }
                         elseif ($consulta['stat']=='PENDIENTE')
                        {
                         $td = "<td class='color-cursando' rowspan='7'>";
                         }
                         elseif ($consulta['stat']=='FIRMASTE'&& $consulta['calificacion']==60  && $consulta['repeticiones']==2 )
                        {
                         $td = "<td class='color-baja' rowspan='7'>";
                         }
        
                       else{
                          $td = "<td class='color-nocursada' rowspan='7'>";
                       }
                         
                         echo $td;
                         ?>
    
                                <?php echo $consulta['nombre_certificado'] ?><br>
                            <?php echo $consulta['stat'] ?><br>
                           
                            <?php echo $consulta['calificacion'] ?>
                            </td>
  
                </tr>              


                   
                <?php
                        }
                    }
                    ?>
                    
                    </table>
                    <?php
                }

                ?>



</body>
<footer class="fot">
<p>Todos los Derechos Reservados &copy, Jesus de la Cruz, Lino Rodriguez.</p>
</footer>

</html>

