<?PHP

  if( isset( $_GET['errnum'] ) )
		$errnum = $_GET['errnum']
	else 
		exit;
		
	switch($errnum)
	{
	case 509: echo "D�sol�, CorqueCoaching est h�berg� chez un h�bergeur gratuit et celui-ci limite la bande passante. Si vous voyez ce message, c'est que vous �tes trop nombreux � vouloir profiter des services propos�s par Croquecoaching. D�sol�, il faut patienter."; brek;
		
	}

?> 