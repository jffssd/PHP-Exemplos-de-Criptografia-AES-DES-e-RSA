<?php
   session_start(); // INICIA SESSÃO
   header("Content-type: image/jpeg"); // ARQUIVO NO FORMATO JPEG
    
    function captcha($largura,$altura,$tamanho_fonte,$quantidade_letras){
        $imagem = imagecreate($largura,$altura); // LARGURA/ALTURA IMG
        $fonte = "fontes/arial.ttf"; //BUSCA FONTE
        $preto  = imagecolorallocate($imagem,0,0,0); // DEFINE COR PRETA
        $branco = imagecolorallocate($imagem,255,255,255); // DEFINE COR BRANCA
        
        // DEFINE A PALAVRA CONFORME A QUANTIDADE DE LETRAS DEFINIDA NO PARAM $quantidade_letras
        $palavra = substr(str_shuffle("AaBbCcDdEeFfGgHhIiJjKkLlMmNnPpQqRrSsTtUuVvYyXxWwZz23456789"),0,($quantidade_letras)); 
        $_SESSION["palavra"] = $palavra; // ATRIBUI PARA A SESSÃO GERADA
        for($i = 1; $i <= $quantidade_letras; $i++){ 
            imagettftext($imagem,$tamanho_fonte,rand(-25,25),($tamanho_fonte*$i),($tamanho_fonte + 10),$branco,$fonte,substr($palavra,($i-1),1)); // DESENHA NA IMAGEM
        }
        imagejpeg($imagem); // GERA IMAGEM
        imagedestroy($imagem); // DESTROI IMAGEM IMAGEM
    }
    
    $largura = $_GET["l"]; // RECEBE LARGURA VIA PARAM
    $altura = $_GET["a"]; // RECEBE ALTURA VIA PARAM
    $tamanho_fonte = $_GET["tf"]; // RECEBE FONTE VIA PARAM
    $quantidade_letras = $_GET["ql"]; // RECEBE QTD LETRAS VIA PARAM
    captcha($largura,$altura,$tamanho_fonte,$quantidade_letras); // EXECUTA FUNCAO CAPTCHA COM OS PARAM RECEBIDOS
?>