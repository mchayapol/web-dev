<?php
// Create connection connect to mysql database
$con = mysqli_connect('localhost', 'root', 'bitnami','public_data') or die (mysqli_connect_error());

// Set encoding.
//printf("Initial character set: %s\n", $con->character_set_name());

/* change character set to utf8 */
if (!$con->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $con->error);
    exit();
}


header('Content-Type: application/json; charset=utf-8');
$sql = "SELECT * FROM province ORDER BY PROVINCE_NAME";
$result = mysqli_query($con,$sql);

$data = array();

/*
$province = mysqli_fetch_array($result,MYSQLI_ASSOC);
echo json_encode($province, JSON_UNESCAPED_UNICODE);
exit;
*/
while($province = mysqli_fetch_array($result,MYSQLI_ASSOC)) {

	
	$sql = "SELECT * FROM amphur WHERE province_id = ".$province['PROVINCE_ID']." ORDER BY AMPHUR_NAME";
	$result2 = mysqli_query($con,$sql);
	$amphur_arr = array();
	while($amphur = mysqli_fetch_array($result2,MYSQLI_ASSOC)) {

		
		$sql = "SELECT district.DISTRICT_CODE, district.DISTRICT_NAME, ZIPCODE FROM district, zipcode WHERE district.amphur_id = ".$amphur['AMPHUR_ID']. " AND district.DISTRICT_ID = zipcode.DISTRICT_ID ORDER BY DISTRICT_NAME";

		$result3 = mysqli_query($con,$sql);
		$district_arr = array();
		while($district = mysqli_fetch_array($result3,MYSQLI_ASSOC)) {
			$district_arr[] = array(
				'code' => $district['DISTRICT_CODE'],
				'name' => trim($district['DISTRICT_NAME']),
				'zipcode' => $district['ZIPCODE']
			);
		}
		
		$amphur_arr[] = array(
			'code' => $amphur['AMPHUR_CODE'],
			'name' => trim($amphur['AMPHUR_NAME']),
			'districts' => $district_arr
		);
	}
	
	$province_arr = array(
		'code' => $province['PROVINCE_CODE'],
		'name' => trim($province['PROVINCE_NAME']),
		'amphors' => $amphur_arr
	);
	$data[] = $province_arr;
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
