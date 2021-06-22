<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Grafico Mediciones 1</title>

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
$Paciente = '10589059';
    $conn = mysqli_connect('localhost','root','','pacientes');

    if(!$conn){
        echo 'Connection error: ' . mysqli_connect_error();

    }
    $sql = "SELECT * FROM `entrada` WHERE NHistorial =$Paciente" ;
    $result = $conn->query($sql);
    

//montamos array de variables
    
    if ($result->num_rows > 0) {

    // output data of each row
    $index1=0;
    $index2=0;
    $index3=0;
    $index4=0;
    $index5=0;
    $index6=0;
    $index7=0;
    $index8=0;
    while($row = $result->fetch_assoc()) {
        if($row["Variable"] == "Albumina"){
            $Albumina[$index1] = $row["Resultado"];
            $index1++;
        }
        else if($row["Variable"] == "Calci(II) "){
            $Calcio[$index2] = $row["Resultado"];
            $index2++;
        }
        else if($row["Variable"] == "Creatinina"){
            $Creatinina[$index3] = $row["Resultado"];
            $index3++;
        }
        else if($row["Variable"] == "Filtrado Glomerular"){
            $eFG[$index4] = $row["Resultado"];
            $index4++;
        }
        else if($row["Variable"] == "Fosfat "){
            $Fosfato[$index5] = $row["Resultado"];
            $index5++;
        }
        else if($row["Variable"] == "Fosfatasa alcalina"){
            $FA[$index6] = $row["Resultado"];
            $index6++;
        }
        else if($row["Variable"] == "Paratirina "){
            $Paratirina[$index7] = $row["Resultado"];
            $index7++;
        }
        $fechas[$index8] = $row["FechaRegistroPrueba"];
        $index8++;
        
    }
    } else {
        echo "0 results";
    }
    

    //sample montar variable data calcio
    $data_Calcio= Get_Data($Calcio);
    $data_Albumina= Get_Data($Albumina);
    $data_Fosfato= Get_Data($Fosfato);
    $data_FA= Get_Data($FA);
    $data_eFG= Get_Data($eFG);
    $data_Paratirina= Get_Data($Paratirina);
    $data_Creatinina= Get_Data($Creatinina);
    $data_fechas = Get_Data_fechas($fechas);
    
    

    

    $conn->close();
    
    function Get_Data($elemento)
    {
        $data = "[";
        for($i=0; $i<count($elemento);$i++){
            $elem = str_replace(",",".",$elemento[$i]);
            $data.=$elem.",";
        }
        $data=substr_replace($data ,"", -1); //elimino la coma final
        $data.="]";
        return $data;

    }
    function Get_Data_fechas($elemento)
    {
        $data = "[";
        for($i=0; $i<count($elemento);$i++){
            $data.="'".$elemento[$i]."'".",";
        }
        $data=substr_replace($data ,"", -1); //elimino la coma final
        $data.="]";
        return $data;

    }

?>

<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        Gráfica de resultados analíticos de valores en el paciente: (Calcio,Fosfato, PTH, FA, Albumina, Paratirina, eFG)
        <br>
        Para el paciente con número de historial: <?=$Paciente;?>
    </p>
</figure>





		<script type="text/javascript">
Highcharts.chart('container', {

    title: {
        text: 'Gráfico evolutivo'
    },

    subtitle: {
        text: 'Calcio,Fosfato, PTH, FA, Albumina, Paratirina, eFG'
    },

    yAxis: {
        title: {
            text: 'Niveles'
        }
    },

    xAxis: {
        categories: <?=$data_fechas;?>
    },

    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    

    

    series: [{
        name: 'Calcio',
        data: <?=$data_Calcio;?>
    }, {
        name: 'Fosfato',
        data: <?=$data_Fosfato;?>
    }, {
        name: 'eFG',
        data: <?=$data_eFG;?>
    }, {
        name: 'Creatinina',
        data: <?=$data_Creatinina;?>
    }, {
        name: 'Paratirina',
        data: <?=$data_Paratirina;?>
    }, {
        name: 'FA',
        data: <?=$data_FA;?>
    }, {
        name: 'Albumina',
        data: <?=$data_Albumina;?>
         
    }],

    

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});
		</script>
	</body>
</html>
