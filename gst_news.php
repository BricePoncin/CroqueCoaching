<?PHP
		require_once("sql.php");

		if( isset($_POST['send']))
		{
				$arDate = explode ( '/', $_POST['date'] );
				$date = $arDate[2].'-'.$arDate[1].'-'.$arDate[0];
				if( $_POST['send'] == "maj" )
				{
						$news = $_POST['news'];
						//$date = $_POST['date'];
						$stmt1 = "UPDATE cm_news SET nw_text='".$news."' WHERE nw_date = '".$date."'";
						$stmt2 = "INSERT INTO cm_news (nw_text, nw_date) VALUES ('$news', '".$date."')";
						
						connect();
						$ret = insert($stmt1);
						if ($ret == 0 )
								$ret = insert($stmt2);
						disconnect();
				}
				else if ( $_POST['send'] == "lod" )
				{
						//$date = $_POST['date'];
						$stmt = "select nw_date, nw_text from cm_news where nw_date='".$date."'";
						connect();
						$ret = select($stmt, $res);
						echo $date;
						if( $ret != 0)
						{
								$news=utf8_decode($res[0]['nw_text']);
								$date=$res[0]['nw_date'];	
						
						}
								$arDate = explode ( '-', $date );
								$date = $arDate[2].'/'.$arDate[1].'/'.$arDate[0];
						
						echo $date;
						disconnect();
				}
		}
				?>
				<form name="frm_news" method="POST" action="#self">
				<input type="hidden" id="send" name="send" value="">
				<p>Date: <input type="text" id="datepicker" name="date" onChange="document.getElementById('send').value='lod';document.forms['frm_news'].submit()" value="<?PHP if(isset($date)) echo $date;?>"></p>
				<textarea name="news" cols="50" rows="30"><?PHP echo $news;?></textarea><div style="margin: 10px 0;">
							<a href="#" onClick="document.getElementById('send').value='maj';document.forms['frm_news'].submit()"
										style="border: 1px solid black; background-color: #EEEEEE; font-weight: bold; padding: 5px; margin-top: 5px;">Valider</a>
					</div>

				</form>
