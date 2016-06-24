<?php
session_start();
if ($_POST["palavra"] == $_SESSION["palavra"]){ //VERIFICA PALAVRA DO CAPTCHA SESSÃO

#=========================================================================
#
#										UPLOAD
#
#=========================================================================
	
	$uploaddir = "uploads/"; //DEFINE DIRETÓRIO DO UPLOAD
	
	$handle = fopen('uploads/base.txt', 'w+'); //CRIA E ABRE EDIÇÃO DO ARQUIVO BASE
	fwrite($handle , ''); //DEIXA O ARQUIVO EM BRANCO, PARA NÃO CONCATENAR UPLOADS
	fclose($handle); //FECHA EDIÇÃO
		
	$uploadfile = $uploaddir."base.txt"; //DEFINE NOME DO ARQUIVO RECEBIDO
	
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'],$uploadfile)){ //VERIFICA SE OCORREU O UPLOAD
		echo "<h2>Carregado com sucesso!</h2>";
	}else{ //RETORNA ERRO
		echo "ERRO";
	}
	echo "<strong>Nome do arquivo</strong>: ".$_FILES['arquivo']['name']."<br>"; //DEBUG	
	
$arquivo = "uploads/base.txt"; //BUSCA O ARQUIVO QUE FOI UPADO
$fp = fopen($arquivo, "r"); //LE O ARQUIVO
$conteudo = fread($fp, filesize($arquivo)); //ATRIBUI A VARIAVEL CONTEUDO
fclose($fp);

	echo "<strong>Upload: </strong>".$conteudo."<br>";
	$tipo = $_POST['tipo'];
	$texto = $conteudo;

	if($tipo <> 'RSA'){ //SE DIFERENTE DE RSA, RECEBE OS MODOS
	$acao = $_POST['acao'];
	$modo = $_POST['modo'];
	
	if($acao == "ENC"){//VERIFICA TIPO DE ACAO
		$ac = "<strong>Cifra</strong>: "; //FIRULA
	}else{
		$ac = "<strong>Decifra</strong>: ";//FIRULA
	}
	echo $ac.encryptOrDecrypt($texto, $acao, $tipo, $modo) . "\n"; //CHAMA FUNCÃO COM PARAM. RECEBIDOS
	
	
	}else{
		
		$plaintext = $texto;
		
		list($chave_pub_a, $chave_pub_b, $chave_priv) = get_rsa_keys(); // GERA AS CHAVES NECESSÁRIAS
		
		echo '<STRONG>PUBLIC KEY PT1:</STRONG> '.gmp_strval($chave_pub_a), '<br />'; // PRIMEIRO ELEMENTO DA PUB KEY
		
		echo '<STRONG>PUBLIC KEY PT2:</STRONG> '.gmp_strval($chave_pub_b), '<br />'; // SEGUNDO ELEMENTO DA PUB KEY
		
		echo '<STRONG>PRIVATE KEY:</STRONG> '.gmp_strval($chave_priv), '<br />'; // CHAVE PRIVADA
		
		$secret = gmp_init('0x'.bin2hex($plaintext)); // GERA SEGREDO
		
		$encrypted = rsa_encrypt($secret, $chave_pub_b, $chave_pub_a); // APLICA ENCRIPTAÇÃO
		
		echo '<STRONG>MENSAGEM ENCRIPTADA:</STRONG> '.gmp_strval($encrypted), '<br />'; // RESULTADO CIFRADO
		
		$decrypted = rsa_decrypt($encrypted, $chave_priv, $chave_pub_a); // REVELA UTILIZANDO CHAVE PRIVADA E CHAVE PUB A
		$decrypted_string = pack('H*', gmp_strval($decrypted, 16)); // BUSCA A STRING INICIAL
		
		
		echo '<STRONG>TEXTO PLANO:</STRONG> '.$decrypted_string, '<br />'; // REVELA RESULTADO PLANO
		

		
		array_map('unlink', glob("rsa/*.txt")); //DESTROI ARQUIVOS DA PASTA RSA
		
				//ESCREVE NOS ARQUIVOS
		
		$fp = fopen("rsa/chave_pub_a.txt", "a"); 
		$escreve = fwrite($fp, gmp_strval($chave_pub_a)); //CHAVE PUBLICA A
		fclose($fp);
		
		$fp = fopen("rsa/chave_pub_b.txt", "a");
		$escreve = fwrite($fp, gmp_strval($chave_pub_b)); //CHAVE PUBLICA B
		fclose($fp);
		
		$fp = fopen("rsa/chave_priv.txt", "a");
		$escreve = fwrite($fp, gmp_strval($chave_priv)); //CHAVE PUBLICA A
		fclose($fp);
		
		$fp = fopen("rsa/cyphertext.txt", "a");
		$escreve = fwrite($fp, gmp_strval($encrypted)); //TEXTO CIFRADO
		fclose($fp);
		
		$fp = fopen("rsa/plaintext.txt", "a");
		$escreve = fwrite($fp, $decrypted_string); //TEXTO PLANO
		fclose($fp);
		}
	}else{//MENSAGEM DE ERRO DO CAPTCHA
	echo "<h1>Voce não acertou o CAPTCHA!</h1>"; //MENSAGEM
	echo "<a href='index.php'>Retornar</a>"; //RETORNO PARA INDEX
}
#================================================================================================================
#
#                                                     FUNCOES AES/DES
#
#================================================================================================================



function encryptOrDecrypt($mprhase, $crypt, $tipo, $modo) { //DEFINE FUNCAO RECEBENDO PARAMETROS
	$chavep = "UNA"; //DEFINE CHAVE
	if($tipo == 'AES'){ //VERIFICA TIPO DE CRIPT
		if ($modo == 'ecb'){ //VERIFICA MODO
			$td = mcrypt_module_open('rijndael-256', '', 'ecb', ''); //DEFINE PARAMETROS PARA ABERTURA DE MODULO AES COM RIJNDAEL 256 EBC
		}else{
			$td = mcrypt_module_open('rijndael-256', '', 'cbc', ''); //DEFINE PARAMETROS PARA ABERTURA DE MODULO AES COM RIJNDAEL 256 CBC
		}
	}
	if($tipo == 'DES'){
		if ($modo == 'ecb'){
			$td = mcrypt_module_open('des', '', 'ecb', ''); //DEFINE PARAMETROS PARA ABERTURA DE MODULO AES COM DES EBC
		}else{
			$td = mcrypt_module_open('des', '', 'cbc', ''); //DEFINE PARAMETROS PARA ABERTURA DE MODULO AES COM DES EBC
		}
	}

	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND); //GERA VETOR DE INICIALIZAÇÃO
	mcrypt_generic_init($td, $chavep, $iv); //GERA CRIPTOGRAFIA
	
	if ($crypt == 'ENC') //CASO SEJA PARA CRIPTOGRAFAR
	{
		$return_value = base64_encode(mcrypt_generic($td, $mprhase)); //BUSCA DADOS COM BASE NO MÓDULO, ATRIBUI A CIFRA DE ACORDO COM A PALAVRA JUNTAMENTE E MÓDULO DEFINIDO
		
		array_map('unlink', glob("aes_des/*.txt")); //DESTROI ARQUIVOS DA PASTA AES DES
		
		//ESCREVE NOS ARQUIVOS
		$fp = fopen("aes_des/plaintext.txt", "a"); //PLAINTEXT
		$escreve = fwrite($fp, $mprhase);
		fclose($fp);
		
		$fp = fopen("aes_des/cyphertext.txt", "a"); //CYPHERTEXT
		$escreve = fwrite($fp, $return_value);
		fclose($fp);
	
	}
	else
	{
		$return_value = mdecrypt_generic($td, base64_decode($mprhase)); //BUSCA DADOS COM BASE NO MÓDULO, ATRIBUI A DECIFRA DE ACORDO A CIFRA COM JUNTAMENTE E MÓDULO DEFINIDO
	}
	
	
	mcrypt_generic_deinit($td); //DESINICIALIZA MÓDULO DE ENCRIPTACAO
	mcrypt_module_close($td); //FECHA MODULO
	

	return $return_value; //RETORNA VALOR
}

#================================================================================================================
#
#                                                     FUNCOES RSA
#
#================================================================================================================



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
	$seed = gmp_random( $val );
	$prime = gmp_nextprime( $seed ); 
	return $prime;
}



function rsa_encrypt($message, $chave_pub_b, $chave_pub_a)
{
	$resp = gmp_powm($message, $chave_pub_b, $chave_pub_a);
	return $resp;
}
function rsa_decrypt($value, $chave_priv, $chave_pub_a) 
{
	$resp = gmp_powm($value, $chave_priv, $chave_pub_a);
	return $resp;
}
