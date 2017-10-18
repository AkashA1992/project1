<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

//Class to load classes it finds the file when the progrm starts to fail for calling a missing class
class Manage {
    public static function autoload($class) {
        //you can put any file name or directory here
        include $class . '.php';
    }
}

spl_autoload_register(array('Manage', 'autoload'));

//instantiate the program object
$obj = new main();

class main {

    public function __construct()
    {        
        //set default page request when no parameters are in URL
        $pageRequest = 'uploadform';
        //check if there are parameters
        if(isset($_REQUEST['page'])) {
            //load the type of page the request wants into page request
            $pageRequest = $_REQUEST['page'];
        }
        
        //instantiate the class that is being requested
         $page = new $pageRequest;          

        if($_SERVER['REQUEST_METHOD'] == 'GET') {            
            $page->get();
            
        } else {            
            $page->post();
        }
    }

}

//Used to form an HTML Page Structure
abstract class page {
    protected $html;

    public function __construct()
    {        
        $this->html .= '<html>';        
        $this->html .= '<body>';
    }
    public function __destruct()
    {
        $this->html .= '</body></html>';
        die($this->html);
    }
}

class uploadform extends page
{
    //Creates a form to upload files
    public function get()
    {
        $form='<form action="index.php?page=uploadform" method="post" enctype="multipart/form-data">';      
        $form.='Select file to upload:';
        $form.='<input type="file" name="fileToUpload" id="fileToUpload">';
        $form.='<input type="submit" value="Upload" name="submit">';
        $form.='</form>';        
        $this->html .= $form;

    }
    //Uploads form to AFS Server
    public function post()
    {
        $target_dir = "/afs/cad/u/a/r/ara59/public_html/project1/uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;

        if ($uploadOk == 0) {
              $this->html .="Sorry, your file was not uploaded.";        
        } 
        // if everything is ok, try to upload file
        else {
              if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {        
              header('Location: https://web.njit.edu/~ara59/project1/index.php?page=displayHtml&filename='.$target_file);
        } 
        else {
            $this->html .="Sorry, there was an error uploading your file.";
        }
        }
    }
}

//Display the output from CSV file to HTML
class displayHtml extends page
{
    public function get(){
      $linecount = 0;
      //Opens CSV File from AFS Server
      $myfile = fopen($_GET['filename'], "r") or die("Unable to open file!");

      $html='';
      $html='<table border="1">';
      //Create HTML till end of CSV file
      while(!feof($myfile)){
        $html.='<tr>';
        //Gets lines from File
        $line = fgets($myfile);
        $string_length=strlen($line);               
        $array1= explode ( ',', $line);
        //Creates data cell
        foreach($array1 as $value){          
          if($linecount==0)
            $html.='<th>'.$value.'</th>';
          else
            $html.='<td>'.$value.'</td>';
          } 
        $linecount++;
        $html.='</tr>';
      }      
      $html.='</table>';
      $this->html .= $html;      
      fclose($myfile);
    }
}
?>