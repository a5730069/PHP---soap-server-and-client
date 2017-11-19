<?php
require_once "src/nusoap.php";

//Connect to the database
function query_db($query_text){
$host = "127.0.0.1";
$user = "root";                                 //Your Cloud 9 username
$pass = "159357123";                                         //Remember, there is NO password by default!
$db = "van_operator";                                  //Your database name you want to connect to
$port = 3306;                                       //The port #. It is always 3306

$connection = mysqli_connect($host, $user, $pass, $db);
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
        //$query = "select name, tel, location from operator";
        $result = mysqli_query($connection,$query_text);
        

        
        $xml = "<table border='1' style='word-wrap:break-word'>";         // New table
        /* Column Title */
       $head = 0;
        while($data = mysqli_fetch_assoc($result)) { //Read all Rows
           if($head == 0){

                $xml .= "<tr>";                                                    // New Row for the titles

                foreach($data as $key => $value) {                                       // For each column, create a column
                    $xml .= "<td><b>$key</b></td>\r\n";
                }

                $xml .= "</tr>";    
                $head = 1;      
           }
           $xml .= "<tr>\r\n\r\n";                        //open inner Tag
            foreach($data as $key => $value) {      //each element data
               $xml .= "
                    <td style='max-width:400px;'>
                        <div style='max-height:300px;overflow-y:auto;'>
                            $value 
                        </div>
                    </td>";
            }

            
           $xml .= "</tr>";                 //close inner tag
            
        }
        $xml .= "</table>"; 
    return $xml;
}



function doAuthenticate($iv_r) {
    if (isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])) {
        

            $encryptionMethod = "AES-256-CBC";
            $secretHash = "encryptionhash";
            $iv = base64_decode($iv_r);
            $encryptedText = $_SERVER['PHP_AUTH_USER'];
             $encryptedText2 = $_SERVER['PHP_AUTH_PW'];
            //Decryption:
          
            $decrypteduser = openssl_decrypt($encryptedText, $encryptionMethod, $secretHash, 0,$iv);
            $decryptedpassword = openssl_decrypt($encryptedText2, $encryptionMethod, $secretHash, 0,$iv);
            
            //print "My Decrypted Text: ". $decryptedText;
    if ($decrypteduser == "tomopost" && $decryptedpassword == "1234")
        return true;
    else
        return false;
    }
}

function get_operators($id_array){
        //print_n($id_array["id"]);
         if (doAuthenticate($id_array) == false){
                        //Encryption:
           
             return "Invalid username or password";
         }

        $query = "select name, tel, location from operator"; 
        $result = query_db($query);
        return $result;                                //outputs all of elder data
}
function get_routes($id_array){
    if (doAuthenticate($id_array) == false){
             return "Invalid username or password";
         }
        $query = "select source,destination,name,tel,convert(price using utf8) as price,convert(distance using utf8) as distance from operator,opro,route where operator.id = op_id and ro_id = route.id order by source,destination" ;
        $result = query_db($query);
        return $result;  
}
function get_timetable($id_array){
    if (doAuthenticate($id_array) == false){
             return "Invalid username or password";
         }
        $query = "select convert(time using utf8) as time,source,destination,name,convert(distance using utf8) as distance from operator,opro,route,opro_time,time where operator.id = op_id and ro_id = route.id and opro_id = opro.id and t_id = time.id order by time,source,destination,name" ;
        $result = query_db($query);
        return $result;  
}


// function getProd($category) {
//     if ($category == "books") {
//         return join(",", array(
//             "The WordPress Anthology",
//             "PHP Master: Write Cutting Edge Code",
//             "Build Your Own Website the Right Way"));
//     }
//     else {
//         return "No products listed under that category";
//     }
// }

$server = new soap_server();
$server->configureWSDL("van_operator", "urn:van_operator");

$server->register("get_operators",
    array("id" => "xsd:string"),
    array("return" => "xsd:string"),
    "urn:van_operator",
    "urn:van_operator#get_opertors",
    "rpc",
    "encoded",
    "get opertors data in database");

$server->register("get_routes",
    array("id" => "xsd:string"),
    array("return" => "xsd:string"),
    "urn:van_routes",
    "urn:van_routes#get_routes",
    "rpc",
    "encoded",
    "get routes data in database");

$server->register("get_timetable",
    array("id" => "xsd:string"),
    array("return" => "xsd:string"),
    "urn:van_timetable",
    "urn:van_timetable#get_timetable",
    "rpc",
    "encoded",
    "get timetable data in database");

// $server->register("authenticate",
//     array("UserName"=>"xsd:string",
//           "Password"=>"xsd:string"),
//     array("return"=>"xsd:string")

// );
// $server->register("getProd",
//     array("category" => "xsd:string"),
//     array("return" => "xsd:string"),
//     "urn:productlist",
//     "urn:productlist#getProd",
//     "rpc",
//     "encoded",
//     "Get a listing of products by category");
    
 
@$server->service(file_get_contents("php://input"));