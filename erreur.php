<?PHP

  if( isset( $_GET['errnum'] ) )
		$errnum = $_GET['errnum']
	else 
		exit;
		
	switch($errnum)
	{
	case 509: echo "Désolé, CorqueCoaching est hébergé chez un hébergeur gratuit et celui-ci limite la bande passante. Si vous voyez ce message, c'est que vous êtes trop nombreux à vouloir profiter des services proposés par Croquecoaching. Désolé, il faut patienter."; brek;
		
	}

?> 