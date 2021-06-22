<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Highcharts Example</title>

		<style type="text/css">
.highcharts-figure, .highcharts-data-table table {
  min-width: 310px; 
  max-width: 800px;
  margin: 0 auto;
}

#container {
  height: 400px; 
}

.highcharts-data-table table {
  font-family: Verdana, sans-serif;
  border-collapse: collapse;
  border: 1px solid #EBEBEB;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 500px;
}
.highcharts-data-table caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}
.highcharts-data-table th {
  font-weight: 600;
  padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
  padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
  background: #f8f8f8;
}
.highcharts-data-table tr:hover {
  background: #f1f7ff;
}

		</style>
	</head>
	<body>
<script src="code/highcharts.js"></script>
<script src="code/highcharts-3d.js"></script>
<script src="code/modules/exporting.js"></script>
<script src="code/modules/export-data.js"></script>
<script src="code/modules/accessibility.js"></script>






<?php
include( "includes/pca.php" );


$pyscript = 'C:\\wamp64\\www\\graficas\\STD.py';
$python = 'C:\\Python39\\python.exe';
#$python = 'C:\\Users\\guill\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.8_qbz5n2kfra8p0\\python.exe';
#$python = 'C:\\Users\\guill\\AppData\\Local\\Microsoft\\WindowsApps\\python.exe';
$filePath = '';
/*
# 7 dimensions
$points = [
    [2.5, 0.5, 2.2, 1.9, 3.1, 2.3, 2, 1, 1.5, 1.1],
    [2.4, 0.7, 2.9, 2.2, 3.0, 2.7, 1.6, 1.1, 1.6, 0.9],
    [2.4, 0.7, 2.9, 2.2, 3.0, 2.7, 1.6, 1.1, 1.6, 0.9],
    [2.4, 0.7, 2.9, 2.2, 3.0, 2.7, 1.6, 1.1, 1.6, 0.9]
]; 

#too 2 dimensions
$result = [
    [2.5, 0.5, 2.2, 1.9, 3.1, 2.3, 2, 1, 1.5, 1.1],
    [2.4, 0.7, 2.9, 2.2, 3.0, 2.7, 1.6, 1.1, 1.6, 0.9]
]; 
*/
    $conn = mysqli_connect('localhost','root','','pacientes');
    if(!$conn){
        echo 'Connection error: ' . mysqli_connect_error();
    }
    $sql = "SELECT IDPeticion FROM `entrada`" ;
    $result = $conn->query($sql);
    

//montamos array de variables
    
    if ($result->num_rows > 0) {

    $index = 0;
    while($row = $result->fetch_assoc()){
    
        $Npeticion[$index] = $row["IDPeticion"];
        $index++;
    }
    
    }else {
        echo "0 results";
    }
    $lista_IDP = array_values(array_unique($Npeticion));

    
    
    
    $index = 0;
    
    $pacientes = [];
    #while($index < count($lista_IDP)){
    while($index < 50){
        $paciente = [];
        $sql = "SELECT Variable,Resultado FROM `entrada` WHERE IDPeticion = $lista_IDP[$index]" ;
        $result = $conn->query($sql);
        
        if($result->num_rows == 7){
        while($row = $result->fetch_assoc()){
            $valor=$row["Resultado"];
            $elem = str_replace(",",".",$valor);
            array_push($paciente, doubleval($elem));

        }
        array_push($pacientes, $paciente);
    }
        
        
        $index++;
    }
    
    
    #print_r($pacientes);
    
    $i = 0;
    while($i<count($pacientes)){
        $pacientes[$i] = PCA($pacientes[$i]);
        $i++;
    }
    #print_r($pacientes);


    $pyscript = 'C:\\wamp64\\www\\graficas\\apply_pca_3d.py';
    $python = 'C:\\Python39\\python.exe';
    $filePath = '';

    $i = 0;
    $pyscript.= " ";
    $pyscript.= count($pacientes);
    while($i<count($pacientes)){

        $pyscript.= " ";
        $strVAR = Get_Data($pacientes[$i]);
        $pyscript.= $strVAR;
        $i++;
    }


    


    $cmd = "$python $pyscript $filePath";
    
    
    exec($cmd, $output);
    #echo $cmd;


    #print_r(gettype($output));
    #print_r($output[0]);

    



    $i=0;
    $final_data="";
    while($i<count($output)){
        $final_data.=$output[$i];

    $i++;}
    

    #print_r ($final_data);



        function Get_Data($elemento)
        {
            $data = "[";
            for($i=0; $i<count($elemento);$i++){
                $data.=$elemento[$i].",";
            }
            $data=substr_replace($data ,"", -1); //elimino la coma final
            $data.="]";
            return $data;
    
        }
        
    function PCA($variable){

        $strvariable = Get_Data($variable);
    
        $pyscript = 'C:\\wamp64\\www\\graficas\\STD.py';
        $python = 'C:\\Python39\\python.exe';
        $filePath = '';

        $pyscript.= " ";
        $pyscript.= $strvariable;
    
        $cmd = "$python $pyscript $filePath";
        #echo $cmd;
        
        exec($cmd, $output);
    
        #print_r($output);
        $frase = $output[0];
        $output=substr($frase,1);
        $output=substr($output,0,-1);
        $output.=",";
        
        $next = False;
        $valor = "";
        $fin = [];
        $a = 0;
        while($a<strlen($output)){
            if($output[$a] == ","){
                $next = True;
            }
            if(!$next){
                $valor.=$output[$a];
            }
            elseif($next){
                $valor_fl = floatval($valor);
                array_push($fin,$valor_fl);
                $valor = "";
                $next = False;
            }
            $a= $a+1;
        }
        return $fin;

    }






?>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        En esta gráfica 3D podemos observar como ejes X,Y,Z las 3 primeras componentes principales del resultado de la reducción de dimensionalidad en la matriz de todos los pacientes.
        <br>
        Se puede rotar su área para ver la grafica desde diferentes ángulos.
    </p>
</figure>


		<script type="text/javascript">
// Give the points a 3D feel by adding a radial gradient
Highcharts.setOptions({
    colors: Highcharts.getOptions().colors.map(function (color) {
        return {
            radialGradient: {
                cx: 0.4,
                cy: 0.3,
                r: 0.5
            },
            stops: [
                [0, color],
                [1, Highcharts.color(color).brighten(-0.2).get('rgb')]
            ]
        };
    })
});

// Set up the chart
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'container',
        margin: 100,
        type: 'scatter3d',
        animation: false,
        options3d: {
            enabled: true,
            alpha: 10,
            beta: 30,
            depth: 250,
            viewDistance: 5,
            fitToPlot: false,
            frame: {
                bottom: { size: 1, color: 'rgba(0,0,0,0.02)' },
                back: { size: 1, color: 'rgba(0,0,0,0.04)' },
                side: { size: 1, color: 'rgba(0,0,0,0.06)' }
            }
        }
    },
    title: {
        text: 'Grafica 3D de todos los pacientes.'
    },
    subtitle: {
        text: 'Esta gráfica puede rotar toda su area para ver las diferentes perspectivas.'
    },
    plotOptions: {
        scatter: {
            width: 10,
            height: 10,
            depth: 10
        }
    },
    yAxis: {
        min: -1.5,
        max: 1.5,
        title: null
    },
    xAxis: {
        min: -1.5,
        max: 1.5,
        gridLineWidth: 1
    },
    zAxis: {
        min: -1.5,
        max: 1.5,
        showFirstLabel: false
    },
    legend: {
        enabled: false
    },
    series: [{
        name: 'Data',
        colorByPoint: true,
        accessibility: {
            exposeAsGroupOnly: true
        },
        data: <?=$final_data;?>
    }]
});


// Add mouse and touch events for rotation
(function (H) {
    function dragStart(eStart) {
        eStart = chart.pointer.normalize(eStart);

        var posX = eStart.chartX,
            posY = eStart.chartY,
            alpha = chart.options.chart.options3d.alpha,
            beta = chart.options.chart.options3d.beta,
            sensitivity = 5,  // lower is more sensitive
            handlers = [];

        function drag(e) {
            // Get e.chartX and e.chartY
            e = chart.pointer.normalize(e);

            chart.update({
                chart: {
                    options3d: {
                        alpha: alpha + (e.chartY - posY) / sensitivity,
                        beta: beta + (posX - e.chartX) / sensitivity
                    }
                }
            }, undefined, undefined, false);
        }

        function unbindAll() {
            handlers.forEach(function (unbind) {
                if (unbind) {
                    unbind();
                }
            });
            handlers.length = 0;
        }

        handlers.push(H.addEvent(document, 'mousemove', drag));
        handlers.push(H.addEvent(document, 'touchmove', drag));


        handlers.push(H.addEvent(document, 'mouseup', unbindAll));
        handlers.push(H.addEvent(document, 'touchend', unbindAll));
    }
    H.addEvent(chart.container, 'mousedown', dragStart);
    H.addEvent(chart.container, 'touchstart', dragStart);
}(Highcharts));

		</script>
	</body>
</html>
