	 <?php   
	 $con = mysqli_connect("localhost","swxzcxwujy","swxzcxwujy","swxzcxwujy");
	           $mobile = $_GET['mobile'];
	           $email = $_GET['email'];
		       $sql        ="SELECT * FROM users";
              $query = mysqli_query($con,$sql);
			  while($data =mysqli_fetch_array($query))
			  {
			  print_r($data);
			  }
			  ?>