<form action="index.php" name="form" method="post" enctype="multipart/form-data">
	<label>Login:</label>
	<input type="text" name="login"  /><br><br>
	<label>Senha:</label>
	<input type="text" name="senha"  /><br><br>
	<input type="submit" value="Enviar" />
	
	</form>
	
	<?php 

	if(isset($_POST['login'])){ //VERIFICA SE O BOTAO ENVIAR FOI APERTADO
		$login = $_POST['login']; //RECEBE INPUT DE LOGIN E SENHA
		$pass = $_POST['senha']; // /\
		
		$usuario = "usuario/usuario.txt"; //DEFINE ENDEREÇO DO ARQUIVO DE USUARIO
		$fp = fopen($usuario, "r"); //ABRE ARQUIVO PARA LEITURA
		$usuario_conteudo = fread($fp, filesize($usuario)); //ATRIBUI CONTEUDO DO ARQUIVO A VARIAVEL
		fclose($fp); //FECHA ARQUIVO
		
		$senha = "usuario/senha.txt";//DEFINE ENDEREÇO DO ARQUIVO DE SENHA
		$fp = fopen($senha, "r");//ABRE ARQUIVO PARA LEITURA
		$senha_conteudo = fread($fp, filesize($senha));//ATRIBUI CONTEUDO DO ARQUIVO A VARIAVEL
		fclose($fp);//FECHA ARQUIVO
		
		if($login == $usuario_conteudo){ //VERIFICA SE O USUARIO INSERIDO É O MESMO DO ARQUIVO
			if(md5($pass) == $senha_conteudo){ //VERIFICA SE O MD DA SENHA É O MESMO DO ARQUIVO
				header("location:home.php"); //SE SIM, ENCAMINHA PARA HOME (SEM TRATAMENTOS DE SESSÃO)
			}else{ // SE NÃO, EXIBE MENSAGEM DE ERRO
				echo "Senha Incorreta"; 
			}
		} echo "Usuario Incorreto"; // EXIBE MENSAGEM DE ERRO 
	}
	?>