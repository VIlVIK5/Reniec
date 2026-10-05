<?php
class cURL
{
    public $headers = array();
    public $user_agent;
    public $compression;
    public $cookie_file;
    public $proxy;
    public $referer;
    public $info;
    public $error;
    public $url = false;

    public $request_cookies = '';
    public $response_cookies = '';
    public $content = '';

    public function getInfo()
    {
        return $this->info;
    }

    public function __construct($cookies = TRUE, $referer = 'https://www.google.com/', $cookie = 'cookies.txt', $compression = 'gzip,deflate')
    {
        $this->user_agent   = 'Mozilla/5.0 (X11; Fedora; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36';
        $this->compression  = $compression;
        $this->cookies      = $cookies;
        $this->referer      = $referer;
        if($this->cookies == TRUE)
            $this->cookie($cookie);
    }

    public function cookie($cookie_file)
    {
        if (file_exists($cookie_file))
        {
            $this->cookie_file = $cookie_file;
        }
        else
        {
            file_put_contents($cookie_file, "");
            $this->cookie_file = $cookie_file;
        }
    }

    public function post($url, array $post = array(), array $options = array())
    {
        $defaults = array(
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_REFERER => $this->referer,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_COOKIEJAR => $this->cookie_file,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_TIMEOUT => 250,
            CURLOPT_ENCODING => $this->compression,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($post)
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if(!$result = curl_exec($ch))
        {
            curl_close($ch);
            return false;
        }
        $this->error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->url   = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        if($this->error < 400)
        {
            curl_close($ch);
            return $result;
        }
        curl_close($ch);
        return false;
    }

    public function get($url, array $options = array())
    {
        $defaults = array(
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_REFERER => $this->referer,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_COOKIEJAR => $this->cookie_file,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_TIMEOUT => 250,
            CURLOPT_ENCODING => $this->compression,
            CURLOPT_HTTPHEADER => $this->headers
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if(!$result = curl_exec($ch))
        {
            curl_close($ch);
            return false;
        }
        $this->error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->url   = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        if($this->error < 400)
        {
            curl_close($ch);
            return $result;
        }
        curl_close($ch);
        return false;
    }

    public function referer($url = "https://www.google.com/")
    {
        $this->referer = $url;
    }
}

class MyDB extends SQLite3
{
    function __construct()
    {
        $this->open('solver.db');
    }
}

class Reniec
{
    var $cc;
    var $db;
    function __construct()
    {
        date_default_timezone_set('America/Lima');
        if(!session_id()) {
            @session_start();
        }
        $this->cc = new cURL(true, 'https://cel.reniec.gob.pe/valreg/valreg.do', dirname(__FILE__).'/cookies.txt');
        $this->db = new MyDB();
    }

    function GetSession($indice)
    {
        return isset($_SESSION[$indice]) ? $_SESSION[$indice] : false;
    }

    function PutSession($indice, $valor)
    {
        $_SESSION[$indice] = $valor;
        return true;
    }

    function DescargaCaptcha($name)
    {
        $data = array();
        $ref = "https://cel.reniec.gob.pe/valreg/valreg.do";
        $url = "https://cel.reniec.gob.pe/valreg/codigo.do";
        $this->cc->referer($ref);
        $captcha = $this->cc->get($url, $data);
        if($captcha != false) {
            file_put_contents($name, $captcha);
            return true;
        }
        return false;
    }

    function ProcesaCaptha($name)
    {
        $captcha = $this->GetSession("captcha");
        $stime = $this->GetSession("stime");
        if( $captcha != false && $stime + (2 * 60) > time() ) {
            return $captcha;
        }

        $name = dirname(__FILE__)."/".$name;
        if($this->DescargaCaptcha($name)) {
            $image = @imagecreatefromjpeg($name);
            if($image) {
                imagefilter($image, IMG_FILTER_GRAYSCALE);
                imagefilter($image, IMG_FILTER_BRIGHTNESS, 100);
                imagefilter($image, IMG_FILTER_NEGATE);
                $L1 = imagecreatetruecolor(25, 20);
                $L2 = imagecreatetruecolor(25, 20);
                $L3 = imagecreatetruecolor(25, 20);
                $L4 = imagecreatetruecolor(25, 20);

                imagecopyresampled($L1, $image, 0, 0, 13, 10, 25, 20, 25, 20);
                imagecopyresampled($L2, $image, 0, 0, 43, 15, 25, 20, 25, 20);
                imagecopyresampled($L3, $image, 0, 0, 76, 10, 25, 20, 25, 20);
                imagecopyresampled($L4, $image, 0, 0, 106, 15, 25, 20, 25, 20);

                $query = "SELECT (SELECT Caracter FROM Diccionario WHERE Codigo1='".$this->ConvirteTexto($L1)."') AS c1,
                                 (SELECT Caracter FROM Diccionario WHERE Codigo2='".$this->ConvirteTexto($L2)."') AS c2,
                                 (SELECT Caracter FROM Diccionario WHERE Codigo3='".$this->ConvirteTexto($L3)."') AS c3,
                                 (SELECT Caracter FROM Diccionario WHERE Codigo4='".$this->ConvirteTexto($L4)."') AS c4";

                $rpt = $this->db->query($query);
                if( $row = $rpt->fetchArray(SQLITE3_ASSOC) ) {
                    $resCaptcha = $row["c1"].$row["c2"].$row["c3"].$row["c4"];
                    $this->PutSession("captcha", $resCaptcha);
                    $this->PutSession("stime", time());
                    return $resCaptcha;
                }
            }
        }
        return false;
    }

    function ConvirteTexto($image)
    {
        $rtn = "";
        $w = imagesx($image);
        $h = imagesy($image);
        for($y = 0; $y < $h; $y++) {
            for($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if((($r+$g+$b)/255) < 1) {
                    $rtn .= "0";
                } else {
                    $rtn .= "1";
                }
            }
        }
        return $rtn;
    }

    function BuscaDatosReniec($dni)
    {
        $rtn = array();
        $Captcha = $this->ProcesaCaptha("captcha.jpg");
        if( $dni != "" && $Captcha != false ) {
            $data = array(
                "accion" => "buscar",
                "nuDni" => $dni,
                "imagen" => $Captcha
            );
            $url = "https://cel.reniec.gob.pe/valreg/valreg.do";
            $this->cc->referer($url);
            $Page = $this->cc->post($url, $data);
            $Page = utf8_encode($Page);
            
            $posiN = strpos($Page, '<td height="63" class="style2" align="center">');
            if ($posiN === false) return false;
            
            $Page = substr($Page, $posiN + 48, 254);
            $posfN = strpos($Page, '<br>');
            $Nombre = substr($Page, 0, $posfN);
            $Separado = explode("\r\n", $Nombre);
            
            if(isset($Separado) && count($Separado) == 3) {
                $Nombre = trim($Separado[1])." ".trim($Separado[2]).", ".trim($Separado[0]);
            } else {
                $Nombre = preg_replace("[\s+]", " ", ($Nombre));
                $Nombre = trim($Nombre);
            }
            
            $Py = '/<font color=#ff0000>([A-Z0-9]+) <\/font>/';
            preg_match_all($Py, $Page, $matches, PREG_SET_ORDER);
            if(isset($matches[0])) {
                $rtn = array("DNI" => $dni, "Nombre" => $Nombre, "CodVerificacion" => trim($matches[0][1]));
            }
            if(count($rtn) > 0) {
                return $rtn;
            }
        }
        return false;
    }
}

echo "=== CONSULTA RENIEC DIRECTA ===\n";
echo "Introduce el DNI a consultar: ";
$dni = trim(fgets(STDIN));

if (strlen($dni) !== 8 || !is_numeric($dni)) {
    echo "Error: Debes ingresar un DNI válido de 8 dígitos.\n";
    exit;
}

echo "Consultando...\n";
$reniec = new Reniec();
$resultado = $reniec->BuscaDatosReniec($dni);

if ($resultado) {
    echo "\nResultado:\n";
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "\nNo se pudo obtener la información (el CAPTCHA o el servicio falló).\n";
}
?>