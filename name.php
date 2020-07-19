<?php
$servername = "localhost:3308";
$username = "dev07dbuser";
$password = "FM07web2020";
$dbname = "dev07db";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);



// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$idresult = mysqli_query($conn, "SELECT distinct userid from useringredients");

$data = array();
while($row = mysqli_fetch_assoc($idresult))
{
    $data[] = $row;
}
if(sizeof($data) == 0){
    $idDefiner = 1;
}else{
    $idDefiner =  sizeof($data) + 1;
}

$number = count($_POST["name"]);
if($number > 0)
{
	for($i=0; $i<$number; $i++)
	{
		if(trim($_POST["name"][$i] != ''))
		{
			$insertsql = "INSERT INTO useringredients(userid,ingredients) VALUES('$idDefiner','".mysqli_real_escape_string($conn, $_POST["name"][$i])."')";
            mysqli_query($conn, $insertsql);
            

		}
    }

}
else
{
	echo "Please Enter at least one ingredient";
}
$idDefinerString = strval($idDefiner);
$sqlstatement = "Select recipe.name, recipe.description, ingredients.name as 'ingredients', mainTable.amount,measurements.measurementType as 'unit of measure' from ( SELECT recipecombined.recipe_id , recipecombined.ingredient_id , recipecombined.measurement_id , recipecombined.amount FROM recipecombined WHERE recipecombined.recipe_id IN ( SELECT distinct recipecombined.recipe_id from recipecombined WHERE recipecombined.ingredient_id IN ( SELECT ingredients.id FROM ingredients JOIN useringredients ON useringredients.ingredients = ingredients.name where useringredients.userid = '$idDefinerString' ) ) ) as mainTable INNER JOIN recipe ON recipe.id = mainTable.recipe_id INNER JOIN ingredients ON ingredients.id = mainTable.ingredient_id INNER JOIN measurements ON measurements.id = mainTable.measurement_id ORDER BY name;";

$bigstatementresult = mysqli_query($conn,$sqlstatement);
$information = array();
while($row2 = mysqli_fetch_assoc($bigstatementresult)){
    $information[] = $row2;
}

echo json_encode($information);


$deletestatement = "DELETE FROM useringredients WHERE userid = '$idDefinerString'";
mysqli_query($conn, $deletestatement);
