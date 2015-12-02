<?php //buscador de información
include("../includes/class.forms.php");
include("../includes/config.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('content-type:application/json');

$fechaIni=@$_POST["fechaInicio"];
$fechaFin=@$_POST["fechaFin"];
$tipo=(@$_POST["tipo"]!="")? $_POST["tipo"] : "";

## el formato de la matriz deberá ser el siguiente
# key1  key2   key3 dato1 dato2 dato3
# fecha ciudad pais 1     4     2
# fecha ciudad pais 2     3     1
# fecha ciudad pais 3     2     4
# fecha ciudad pais 4     1     3

#cada 1 sera un row

/*$mediciones=array("luminarias rotas","accidentes","Visitantes");
//$mediciones=array("luminarias rotas");

foreach ($mediciones as $key => $medicion) {
	# se listan los parámetros
	$datos=array();
	for ($i=0; $i < 21; $i++) { 
		# se genera una matriz de datos
		//$datos[$i]["fecha"]="2015-11-24";
		//$datos[$i]["ciudad"]="monterrey";
		//$datos[$i]["pais"]="mexico";
		$fecha=date("Y-m-d",rand(1448924400,1451516400));
		$datos[$fecha]=rand(0,100);
		$datos[$fecha]=rand(0,100);
		$datos[$fecha]=rand(0,100);
		$datos[$fecha]=rand(0,100);
	}

	ksort($datos);

	$r["data"]["plot".$key]["nombre"]=$medicion;
	$r["data"]["plot".$key]["serie"]=$datos;
	$r["data"]["plot".$key]["xmin"]=min(array_keys($datos));
	$r["data"]["plot".$key]["xmax"]=max(array_keys($datos));
	$r["data"]["plot".$key]["ymin"]=min($datos);
	$r["data"]["plot".$key]["ymax"]=max($datos);
}//*/

try {
	$bd=new formas($dsnReader);
} catch (PDOException $e) {
	$r["err"]=true;
	$r["msg"]=$e->getMessage();
	echo json_encode($r);
	exit;
}

$plots=array();
$nombres=array();
try {

	//checar si hay cambios si no hay mandar el error true

	//si hay cambios entonces...
	$sql="SELECT
		(SELECT NOMBRE FROM params a1 WHERE ID_PARAM=t1.ID_PARAM) as NOMBRE,
		DATE(t1.FECHA) as FECHA,
	    SUM(t1.VALOR) as SERIE1,
	    SUM(t1.VALOR)*3 as SERIE2
	FROM params_partidas t1
	WHERE 
		ID_PARAM IN (
			SELECT ID_PARAM FROM params a2 WHERE a2.ID_CUENTA=1 
		)
	AND
		t1.FECHA BETWEEN '$fechaIni' AND '$fechaFin'
	GROUP BY t1.FECHA;";

	$res=$bd->query($sql);

	foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $key => $row) {
		# se crea usa una array intermedio para pasarlo al $r["data"]
		if(in_array($row["NOMBRE"], $nombres)){
			## si está entonces se extrae su key
			$plotNo=array_search($row["NOMBRE"], $nombres);
		}else{
			## si no está entonces se guarda y se usa el count(array) y se agrega a la variable
			$plotNo=count($nombres);
			array_push($nombres, $row["NOMBRE"]);
		}

		$plots["plot".$plotNo]["nombre"]=$row["NOMBRE"];
		$plots["plot".$plotNo]["tipo"]=$tipo;
		$plots["plot".$plotNo]["serie"][0][$row["FECHA"]]=$row["SERIE1"];
		$plots["plot".$plotNo]["serie"][1][$row["FECHA"]]=$row["SERIE2"];
		
	}

	$r["err"]=false;
} catch (PDOException $e) {
	$r["err"]=true;
	$r["msg"]=$e->getMessage();
}
$r["data"]=$plots;//*/

echo json_encode($r);
?>