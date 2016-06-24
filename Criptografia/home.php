<html>
<head></head>
<body>
<div> 
<form action="core.php" name="form" method="post" enctype="multipart/form-data">
  <label>Arquivo:</label>
<input type="file" name="arquivo" /><br><br>
  <label>Tipo:</label>
  <input type="radio" name="tipo" value="AES">AES
  <input type="radio" name="tipo" value="DES">DES
  <input type="radio" name="tipo" value="RSA">RSA<br>
  <label>Modo:</label>
  <input type="radio" name="modo" value="ecb">ECB
  <input type="radio" name="modo" value="cbc">CBC<br>
  <label>Ação:</label>
  <input type="radio" name="acao" value="ENC">Encriptar
  <input type="radio" name="acao" value="DEC">Decifrar<br><br>
  <label><a href="a.php"><strong>Decifra RSA</strong> Clique aqui</a></label><br><br>
<!--
l = Largura imagem
a = altura imagem
tf = tamanho fonte
ql = qtd letras
-->
	<img src="captcha.php?l=150&a=50&tf=20&ql=3"><br><br>
	<label>Captcha:</label>
	<input type="text" name="palavra"  /><br><br>
	<input type="submit" value="Enviar" />
</form>
</div>
</body>
</html>