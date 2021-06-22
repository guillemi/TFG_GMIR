<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Highcharts Example</title>

		<style type="text/css">
.highcharts-figure, .highcharts-data-table table {
    min-width: 360px; 
    max-width: 800px;
    margin: 1em auto;
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
<script src="code/modules/series-label.js"></script>
<script src="code/modules/exporting.js"></script>
<script src="code/modules/export-data.js"></script>
<script src="code/modules/accessibility.js"></script>






<?php



$pyscript = 'C:\\wamp64\\www\\graficas\\STD.py';
$python = 'C:\\Python39\\python.exe';
$filePath = '';

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
    while($index < 60){ #para 60 pacientes
        $paciente = [];
        $sql = "SELECT Variable,Resultado,NHistorial FROM `entrada` WHERE IDPeticion = $lista_IDP[$index]" ;
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
    
    
    
    $i = 0;
    while($i<count($pacientes)){
        $pacientes[$i] = STD($pacientes[$i]);
        $i++;
    }
   ;
    $len = count($pacientes);
    $iterations = $len / 30;
    

    $i=0;
    $doo = [];
    $next = False;
    $output="[";
    $cont = 0;
    while($i<count($pacientes)){
        if($cont == 30){
            $valor = PCA($doo);
            $cadena = substr($valor[0], 0, -1);
            $cadena = substr($cadena, 1);
            $cadena.=",";
            $output.=$cadena;
            $doo = [];
            $cont = 0;
        }
        else{
            array_push($doo, $pacientes[$i]);
        }
        
    $i++;
$cont++;}
    $output = substr($output, 0, -1);
    $output.="]";
  
        
    $final_data = $output;




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
        
    function STD($variable){

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

    function PCA($pacientes){
        
    $pyscript = 'C:\\wamp64\\www\\graficas\\apply_pca.py';
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
    
    return $output;
    

    }
    





?>








<figure class="highcharts-figure">
    <div id="container"></div>
    
    <br>
    
    <br>
    <p class="highcharts-description">
    
    </p>
    <br>
</figure>



		<script type="text/javascript">
Highcharts.chart('container', {
    chart: {
        type: 'scatter',
        zoomType: 'xy'
    },
    title: {
        text: 'Comparación de Pacientes con reducción de dimensionalidad PCA'
    },
    subtitle: {
        text: 'Pacientes con Hiperfosfatemia'
    },
    xAxis: {
        title: {
            enabled: true,
            text: 'Componente Principal 1'
        },
        startOnTick: true,
        endOnTick: true,
        showLastLabel: true
    },
    yAxis: {
        title: {
            text: 'Componente Principal 2'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        verticalAlign: 'top',
        x: 100,
        y: 70,
        floating: true,
        backgroundColor: Highcharts.defaultOptions.chart.backgroundColor,
        borderWidth: 1
    },
    plotOptions: {
        scatter: {
            marker: {
                radius: 5,
                states: {
                    hover: {
                        enabled: true,
                        lineColor: 'rgb(100,100,100)'
                    }
                }
            },
            states: {
                hover: {
                    marker: {
                        enabled: false
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.x} u, {point.y} u'
            }
        }
    },
    series: [{
        name: 'Usted',
        color: 'rgba(223, 83, 83, .5)',
        data: [[0.1, -0.27]]

    }, {
        name: 'Todos los pacientes',
        color: 'rgba(119, 152, 191, .5)',
        data: <?=$final_data;?>
    }]
});

		</script>
	</body>
</html>

