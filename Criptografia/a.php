<html>
<head></head>
<body>
<div> 
<h2>Decifra RSA</h2>
<form action="a.php" name="form" method="post" enctype="multipart/form-data">
    <label>Publica PT 1:</label>
  <input type="text" name="pub"  /><br>
  
    <label>Privada:</label>
  <input type="text" name="priv"  /><br>
  
  <label>Cifra:</label>
  <input type="text" name="cifra"  /><br>
	<input type="submit" value="Enviar" />
</form>
</div>
<a href="home.php">Voltar</a>
</body>
</html>

<?php 
#================================================================================================================
#
#                                                     FUNCOES RSA
#
#================================================================================================================

if(isset($_POST['cifra'])){ //VERIFICA SE O BOTÃO DECIFRA FOI APERTADO
	
	//RECEBE DADOS DO INPUT
	$cifra = $_POST['cifra'];
	$publica = $_POST['pub'];
	$privada = $_POST['priv'];
	
	$decrypted = rsa_decrypt($cifra, $privada, $publica); // REVELA UTILIZANDO CHAVE PRIVADA E CHAVE PUB A
	$decrypted_string = pack('H*', gmp_strval($decrypted, 16)); // CONVERTE RESULTADO PARA STRING
	
	
	echo '<br><br><STRONG>TEXTO PLANO:</STRONG> '.$decrypted_string, '<br />'; // EXIBE TEXTO PLANO
}


//CALCULOS
function get_rsa_keys() 
{
	$unu = gmp_init(1);
	$p = get_random_prime();
	$q = get_random_prime();
	$N = gmp_mul($p, $q);
	$fi_de_n = gmp_mul( gmp_sub($p, $unu), gmp_sub($q, $unu) );
	$e = gmp_random(4);
	$e = gmp_nextprime($e);
	while (gmp_cmp(gmp_gcd($e, $fi_de_n), $unu) != 0)
	{
		$e = gmp_add($e, $unu);
	}
	$d = modinverse($fi_de_n, $e );
	return array($N, $d, $e);
}
function modinverse ($A, $Z)
{
	$N=$A;
	$M=$Z;
	$u1=1;
	$u2=0;
	$u3=$A;
	$v1=0;
	$v2=1;
	$v3=$Z;
	while ( gmp_cmp($v3, 0) != 0)
	{
		$qq=gmp_div($u3,$v3);
		$t1=gmp_sub($u1, gmp_mul($qq,$v1));
		$t2=gmp_sub($u2, gmp_mul($qq,$v2));
		$t3=gmp_sub($u3, gmp_mul($qq,$v3));
		$u1=$v1;
		$u2=$v2;
		$u3=$v3;
		$v1=$t1;
		$v2=$t2;
		$v3=$t3;
		$z=1;
	}
	$uu=$u1;
	$vv=$u2;
	$zero = gmp_init(0);
	if (gmp_cmp($vv, $zero) < 0)
	{
		$I=gmp_add($vv,$A);
	}
	else
	{
		$I=$vv;
	}
	return $I;
}
function get_random_prime( $val = 4 ) 
{
	$seed = gmp_random( $val ); //ATRIBUI NUMERO PRIMO
	$prime = gmp_nextprime( $seed );  //BUSCA PROXIMO NUMERO PRIMO
	return $prime;
}

function rsa_decrypt($value, $chave_priv, $chave_pub_a)  //RECEBE CIFRA, CHAVE PRIVADA E CHAVE PUBLICA PT 1
{
	$resp = gmp_powm($value, $chave_priv, $chave_pub_a);
	return $resp;
}
