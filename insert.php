<?php

//insert.php

include('database_connection.php');

$form_data = json_decode(file_get_contents("php://input"));

$error = '';
$message = '';
$validation_error = '';
$first_name = '';
$last_name = '';

if(isset($form_data->cargo)){
	$cargo = $form_data->cargo;
}


if(isset($form_data->fechaingreso)){
	$fechaingreso = $form_data->fechaingreso;
}


if(isset($form_data->salario)){
	$salario = $form_data->salario;
}



if($form_data->action == 'fetch_single_data')
{
	$query = "SELECT * FROM tbl_sample WHERE id='".$form_data->id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['first_name'] = $row['first_name'];
		$output['last_name'] = $row['last_name'];
		$output['cargo'] = $row['cargo'];
		$output['fechaingreso'] = $row['fechaingreso'];
		$output['salario'] = $row['salario'];
	}
}
elseif($form_data->action == "Delete")
{
	$query = "
	DELETE FROM tbl_sample WHERE id='".$form_data->id."'
	";
	$statement = $connect->prepare($query);
	if($statement->execute())
	{
		$output['message'] = 'Registro Eliminado';
	}
}
else
{
	if(empty($form_data->first_name))
	{
		$error[] = 'First Name is Required';
	}
	else
	{
		$first_name = $form_data->first_name;
	}

	if(empty($form_data->last_name))
	{
		$error[] = 'Last Name is Required';
	}
	else
	{
		$last_name = $form_data->last_name;
	}

	if(empty($error))
	{
		if($form_data->action == 'Insert')
		{
			$data = array(
				':first_name'		=>	$first_name,
				':last_name'		=>	$last_name,
				':cargo'			=>	$cargo,
				':fechaingreso'		=>	$fechaingreso,
				':salario'			=>	$salario
			);
			$query = "
			INSERT INTO tbl_sample 
				(first_name, last_name, cargo, fechaingreso, salario) VALUES 
				(:first_name, :last_name, :cargo, :fechaingreso, :salario)
			";
			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Registro almacenado';
			}
		}
		if($form_data->action == 'Edit')
		{
			$data = array(
				':first_name'	=>	$first_name,
				':last_name'	=>	$last_name,
				':cargo'		=>	$cargo,
				':fechaingreso'	=>	$fechaingreso,
				':salario'		=>	$salario,
				':id'			=>	$form_data->id
			);

			$query = "
			UPDATE tbl_sample 
			SET first_name = :first_name, last_name = :last_name, cargo = :cargo, fechaingreso = :fechaingreso, salario = :salario
			WHERE id = :id
			";

			$statement = $connect->prepare($query);
			if($statement->execute($data))
			{
				$message = 'Dato editado';
			}

		}
	}
	else
	{
		$validation_error = implode(", ", $error);
	}

	$output = array(
		'error'		=>	$validation_error,
		'message'	=>	$message
	);

}



echo json_encode($output);

?>