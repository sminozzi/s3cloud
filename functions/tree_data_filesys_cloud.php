<?php
//$json = '[{"text":"Root","icon":"","nodes":[{"text":"Palmeiras/","icon":"","nodes": [ { "icon": "", "text": "Palmeiras/teste/","nodes": [ { "icon": "", "text": "Palmeiras/teste/lixo/"}]}]},{"text":"calcio/","icon":""},{"text":"lixo/","icon":"","nodes": [ { "icon": "", "text": "lixo/sub_lixo1/"}]},{"text":"palmeiras/","icon":""},{"text":"test/","icon":""},{"text":"teste-sub1/","icon":"","nodes": [ { "icon": "", "text": "teste-sub1/sub-1-sub2/"}]},{"text":"teste-sub2/","icon":"","nodes": [ { "icon": "", "text": "teste-sub2/sub-sub2/"}]},{"text":"teste-sub3/","icon":"","nodes": [ { "icon": "", "text": "teste-sub3/sub-sub3/"}]},{"text":"teste-sub4/","icon":"","nodes": [ { "icon": "", "text": "teste-sub4/sub-sub4/"}]},{"text":"teste-sub5/","icon":""},{"text":"xixi/","icon":""},{"text":"xixi22/","icon":""},{"text":"xixi2233/","icon":""},{"text":"xixi2233333/","icon":""}]}]';
//die($json);
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('memory_limit', '512M');
set_time_limit(600);




// global $s3cloud_dir_for_search;

global $s3cloud_region;
global $s3cloud_secret_key;
global $s3cloud_access_key;
global $bucket_name;

if (isset($_POST['bucket_name'])) 
    $bucket_name = sanitize_text_field($_POST['bucket_name']);
else
    die('Missing Post bucket_name');


//global $bucket_name;

// die($bucket_name);

// $s3cloud_dir_for_search = getcwd() . '/';

// $s3cloud_dir_for_search = '/home/minozzi/public_html/wp-includes/';
// $s3cloud_dir_for_search = "/home/minozzi/public_html/teste/";
// $s3cloud_dir_for_search = "/home/minozzi/public_html/teste1/";
// $s3cloud_dir_for_search = '/home/minozzi/public_html/';
// $s3cloud_dir_for_search = '/home/minozzi/public_html/';
// $s3cloud_dir_for_search = "/home/minozzi/public_html/contabo/";
// $s3cloud_dir_for_search = "/home/minozzi/public_html/s3/";
// $s3cloud_dir_for_search = '/home/minozzi/public_html/wp-content/';
// $s3cloud_dir_for_search = '/home/minozzi/public_html/wp-admin/';
if (!defined('ABSPATH')) 
  define('ABSPATH', '');

// $s3cloud_dir_for_search = ABSPATH . '/';

// '/home/s3cloud/public_html/';

// Plugin path...
//$s3cloud_dir_for_search = S3CLOUDPATH; // . '/';
// die(ABSPATH);


// $s3cloud_data_filesys = s3cloud_fetch_files($s3cloud_dir_for_search);

// $s3cloud_data_filesys =

$endpoints = "https://" . $s3cloud_region . ".contabostorage.com";

use Aws\Exception\AwsException;

$path = S3CLOUDPATH . "/vendor/autoload.php";
require_once($path);
$baseurl = $endpoints;
$credjson = base64_decode($s3cloud_access_key);
$credarray = json_decode($credjson);



/*
if (!isset($bucket_name))
    $bucket_name = 'teste';
    */



/*
if (isset($_GET['prefix'])) $path = sanitize_text_field($_GET['prefix']);
else $path = '';
*/


$config = ['s3-access' => ['key' => $s3cloud_access_key, 'secret' => $s3cloud_secret_key, 'bucket' => $bucket_name, 'region' => $s3cloud_region, 'version' => 'latest', 'endpoint' => $endpoints]];
try {
    $s3 = new Aws\S3\S3Client(['credentials' => ['key' => $config['s3-access']['key'], 'secret' => $config['s3-access']['secret']], 'use_path_style_endpoint' => true, 'force_path_style' => true, 'endpoint' => $config['s3-access']['endpoint'], 'version' => 'latest', 'region' => $config['s3-access']['region']]);
    // TEST

  //  $buckets = $s3->listBuckets();
  //  die(var_export($buckets));


    $objects = $s3->getIterator('ListObjects', array(
        'Bucket' => $config['s3-access']['bucket'],
    
       // 'Bucket' => 'documentos'
    ));
    // ));

  //  die(var_export($config['s3-access']['bucket']));


} catch (AWSException $e) {
    echo esc_attr($e->getStatusCode());
    echo esc_attr($e->getAwsErrorCode());
    echo esc_attr(explode(';', $e->getMessage())[1]);
    die();
}

//die(var_export($objects));




$files = [];
// $ctd = 0;
foreach( $objects as $ob ) {


    if(substr($ob[ 'Key' ], -1) != '/') {


        $pos = strrpos($ob[ 'Key' ],'/');
        $ob[ 'Key' ] = substr($ob[ 'Key' ],0, $pos).'/';


    }
 


    $atemp = explode( '/', $ob[ 'Key' ] );

        //$atemp = $split;
    $acont = count($atemp );

    if( $acont == 2) {

          if (!empty($atemp[0])){
             if (!in_array($atemp[0].'/', $files)) 
               $files[] = $atemp[0].'/';

              
          }
          continue;

    }

        // $ctd++;

       // var_export($atemp);
      //  die();


        $temp2 = '';


        for($i = 0; $i < ($acont-1); $i++) {

            //var_export($atemp[$i]);
            //die();


            $temp2 = $temp2 . $atemp[$i];

            /*
            if($i > 0){
              var_export($temp2);
              die();
            }
            */


            



               

            if (!in_array($temp2, $files)){ 
                if(substr($temp2, -1) != '/'){

                    $pos = strrpos($temp2,'/');
                    $ob[ 'Key' ] = substr($temp2,0, $pos-1);

                   //  continue;

                } 

                if($i < ($acont-1))
                  $temp2 .=  '/';
                 

                if (!in_array($temp2, $files))
                   $files[] = $temp2;


                // $ctd++;
            }


        }

        

}

 //$files2 = array_filter( $files );
 
//die(var_export($files));

//foreach( $dirs as $index => $dir ) {



//die(var_export($files));

// $dirs = [];


/*
foreach( $objects as $ob ) {
   
    
    if( preg_match( '/^.*\/$/', $ob[ 'Key' ] ) ) {
        $split = explode( '/', $ob[ 'Key' ] );
       // var_export($split);
       // $dirs[] = $split[0];
       // die();
        $dirs = nest_dir( $dirs, $split );
    }
    
    
   // if(substr($ob[ 'Key' ], -1) == '/') {

        $files[] = $ob[ 'Key' ];
        //echo $ob[ 'Key' ];
        // echo '<br>';
       // $split = explode( '/', $ob[ 'Key' ] );
       // $dirs = nest_dir( $dirs, $split );
   // }
}

var_export($files);
die();
*/

/*
function nest_dir( $ref, $dirs ) {
    $dirs = array_filter( $dirs );
    foreach( $dirs as $index => $dir ) {
        $parent = @$dirs[ $index - 1 ];

        if( $parent && isset( $ref[ $parent ] ) ) {
            $ref[ $parent ][ $dir ] = nest_dir( [], array_slice( $dirs, $index + 1 ) );
            continue;
        }
        if( !$parent || ( $parent && array_search( $parent, $dirs ) === 0 ) )
            $ref[ $dir ] = [];
    }
    return $ref;
}
// var_export(gettype($dirs));
*/



/////////////////////////


/*
$files = [];
foreach( $objects as $ob ) {
    //if( preg_match( '/^.*\/$/', $ob[ 'Key' ] ) ) {
    if(substr($ob[ 'Key' ], -1) == '/') {

        $files[] = $ob[ 'Key' ];
        //echo $ob[ 'Key' ];
        // echo '<br>';
       // $split = explode( '/', $ob[ 'Key' ] );
       // $dirs = nest_dir( $dirs, $split );
    }
}
*/

//  die(var_export($files));

sort($files, SORT_STRING);
$tot = count($files);

// die(var_export($files));

$s3cloud_data_filesys = array();

$k = 0;

for($i = 0; $i < $tot; $i++){

    $temp = explode('/',$files[$i]);

    if( empty($temp[count($temp) -1 ]))
      unset($temp[count($temp) -1 ]);

   // var_dump($temp);


    $parent = '-1';
    for($j = 0; $j < count($temp); $j++){

        /*
        if(count($temp) == 1) {
            $s3cloud_data_filesys[$k]['path'] = $temp[$j];
            $s3cloud_data_filesys[$k]['parent'] = $parent;
            $k++;
            break;
        }

        if(count($temp) == 2) {
            $parent = $temp[0];
            $s3cloud_data_filesys[$k]['path'] = $temp[$j];
            $s3cloud_data_filesys[$k]['parent'] = $parent;
            $k++;
            break;
        }
        */


       

            $s3cloud_data_filesys[$k]['path'] = $temp[$j];

            if(count($temp) > 1) {

            $temp_size = strlen($files[$i]) - 1;


            $pos = strrpos(substr($files[$i],0, $temp_size) ,'/');



            $parent_for_search = substr($files[$i],0,$pos+1);



            $indice_parent = array_search($parent_for_search, array_column($s3cloud_data_filesys, 'path') , true);

            
            }
            else
              $indice_parent = '-1';





            $s3cloud_data_filesys[$k]['parent'] = $indice_parent;
            $k++;
            break;

        continue;


        if(count($temp) == 3) {
            $s3cloud_data_filesys[$k]['path'] = $temp[0];
            $s3cloud_data_filesys[$k]['parent'] = '-1';
            $k++;
            continue;
        }

          $parent = $temp[0];

          if($j == 1){
            $s3cloud_data_filesys[$k]['parent'] = $parent;
            $s3cloud_data_filesys[$k]['path'] = $temp[$j];
          }

          $k++;

    }

    $s3cloud_data_filesys[$i]['path'] = $files[$i];

}

//var_export($s3cloud_data_filesys);
//die();






/*
echo '<pre>';
var_export($s3cloud_data_filesys);
echo '</pre>';
die('================');
*/

/*
$columns = array_column($s3cloud_data_filesys, "path");
array_multisort($columns, SORT_ASC, $s3cloud_data_filesys);
*/

/*
echo '<pre>';
var_export($s3cloud_data_filesys);
echo '</pre>';
die('================');
*/

// CREATE NODES //////////////////


/*
echo '<hr>';
$path_ant = $s3cloud_data_filesys[0]['path'];
$all = explode('/', $path_ant);
$tot_nodes = count($all);
echo '<hr>';
var_dump($tot_nodes);
echo '<hr>';
*/

/*
echo '<hr>';
var_dump($path_ant);
echo '<hr>';
*/

// $item['path'] =
$i = 0;
foreach ($s3cloud_data_filesys as $key => $item)
{

    if ($item['parent'] == '-1') continue;

    if (!isset($item['parent'])) die('fail L 88');

    $s3cloud_data_filesys[$item['parent']]['nodes'][] = $item['path'];

    /*
    echo '<pre>';
    var_export($s3cloud_data_filesys);
    echo '</pre>';
    die('================');
    */

    continue;

    /*
    //$path_ant = $s3cloud_data_filesys[$i]['path']);
    $all = explode('/', $path_ant);
    $tot_nodes_old = count($all);
    echo '<hr>';
    var_dump('<<<<<< '.$tot_nodes_old);
    
    echo '<hr>';
    var_dump($path_ant);
    echo '<hr>';
    echo '<hr>';
    
    $all = explode('/', $item['path']);
    $tot_nodes_new = count($all);
    echo '<hr>';
    var_dump($tot_nodes_new);
    
    echo '<hr>';
    var_export('>>>>> '.$item['path']);
    echo '<hr>';
    */

    // if( ! $item['path'] =='/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons')
    //    die($item['path']);
    //   $i++;
    //die(var_export($item['parent']));
    //die(var_export($item['parent']));
    

    /*
    if( $item['path'] =='/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons')
    {
    
      die(   var_export($item['parent'])         );
      die(   var_export(isset($s3cloud_data_filesys[$item['parent']] )  )   );
      // die(var_export($item)); 
      die(var_export($item['parent'])); 
    
    }
    // die($item['path']);
    */

    /*
    if($i == 7){
    echo var_export($item['path']);
    echo '      --vv---';
    die();
    }
    */

    //if( trim($item['path']) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome'){
    /*
    if( trim($item['path']) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts'){
    
        $indice_node_node = array_search($item['path'], array_column($s3cloud_data_filesys, 'path') , true);
        var_export($indice_node_node);
    
        die();
    
        $indice_node_node = array_search($item['path'], array_column($s3cloud_data_filesys, 'path') , true);
        var_export($s3cloud_data_filesys[$indice_node_node]);
    
    echo 'ITEM: '.var_export($item);
    echo '      ---------vvbbbbb---------';
    
    var_export($s3cloud_data_filesys[$item['parent']]); // ['nodes'][] = $item['path'];
    die('cccccccccccccccccc');  
    }
    */

    /*
    $indice_node_node = array_search($item['path'], array_column($s3cloud_data_filesys, 'path') , true);
      
     // die('xxx-------------xxx'.var_export($indice_node_node));
    
    
     $s3cloud_data_filesys[$indice_node_node]['nodes'][] = $item['path'];
    */

    // var_export($s3cloud_data_filesys[$indice_node_node]['nodes']);
    // continue;
    //die();
    

    //  && isset($s3cloud_data_filesys[$indice_node_node]))
    if (isset($item['parent']))
    {

        /*
        if($i == 3){
        echo var_export($item['path']);
        echo '-----';
        die();
        }
        
        */

        // $s3cloud_data_filesys[$item['parent']]['nodes'][] = $item['path'];
        

        // die(var_export($item['path']));
        // if( $item['path'] =='/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css/fonts'){
        // if( $s3cloud_data_filesys[$item['parent']] =='/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css'){
        if ($item['path'] == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css/fonts/fontawesome')
        {

            //  /home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome
            // DIE(var_export(__LINE__));
            // die('iiiii  '.var_export($item));
            echo var_export($item['path']);
            echo '-----';
            die();

            die(var_export($s3cloud_data_filesys[$item['parent']]));
        }
        else
        {

            //die($item['path']);
            die(var_export($indice_node_node));

            // $s3cloud_data_filesys[$item['parent']]['nodes'][] = $item['path'];
            $s3cloud_data_filesys[$indice_node_node]['nodes'][] = $item['path'];

            var_export($s3cloud_data_filesys[$indice_node_node]['nodes']);
            die();

        }

        // die($item['path']);
        

        /*
        
        $all = explode('/', $item['path']);
        $tot_nodes_new = count($all);
        echo '<hr>';
        var_dump($tot_nodes_new);
        echo '<hr>';
        var_dump('>>>>> '.$item['path']);
        echo '<hr>';
        
        if($tot_nodes_new > $tot_nodes_old )
        {
        
        // die('AUMENTOU !');
        
        // Pegar o anterior c colocar um node nele.
        $s3cloud_data_filesys[$i]['nodes'][] = $item['path'];
        
        var_dump($s3cloud_data_filesys[$i]);
        // die();
        
        echo '<hr>AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
        echo '<hr>';
        
        
        }
        */

        /*
        echo '<pre>';
        var_dump($all);
        echo '</pre>';
        */

        // $itemsByReference [$item['parent']]['nodes'][] = &$item['path'];
        /* var_dump('path item ' . $item['path']);
        echo '<hr>';
        var_dump('Parent ' . $item['parent']);
        echo '<hr>';
        var_dump('path item ' . $item['path']);
        echo '<hr>';
        $indice_node = array_search($item, array_column($s3cloud_data_filesys, 'path') , true);
        var_dump('Parent---> ' . $item['parent']);
        echo '<hr>';
        var_dump('Path:::::: ' . $item['path']);
        echo '<hr>';
        // var_dump('Parent Path '.$s3cloud_data_filesys['path']);
        //echo '>>>>>';
        //echo '<br>';
        // var_dump($s3cloud_data_filesys[ '176' ]    );
        //var_dump($s3cloud_data_filesys[$item['parent']]);
        // echo '<hr>';
        // var_dump('Parent path ' . $s3cloud_data_filesys[$item['parent']]['path']);
        // die();
        */

        //if( $item['path'] =='/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons')
        //  die(   var_export(   $s3cloud_data_filesys[$item['parent'] ]   )   );
        //$s3cloud_data_filesys[$item['parent']]['nodes'][] = $item['path'];
        
    }

    $i++;

    $path_ant = $item['path'];

}
/*
echo '<pre>';
var_export($s3cloud_data_filesys);
echo '</pre>';
die();
*/


//  CREATE JSON ////////////////////////
function s3cloud_create_json_filesys($indice_node, $s3cloud_data_filesys)
{
    global $json;
    global $j;
    global $bucket_name;

    $columns = array_column($s3cloud_data_filesys, "path");
    array_multisort($columns, SORT_ASC, $s3cloud_data_filesys);

    //die(var_export($s3cloud_data_filesys));
    

    $tot = count($s3cloud_data_filesys);
    $ctd = 0;

    // $json = '[{"text":"Root","icon":"","nodes":[';

    // $json = '[{"text":"'.$bucket_name.'","icon":"","nodes":[';
    $json = '[{"text":"Root","icon":"","nodes":[';

    for ($i = 0;$i < $tot;$i++)
    {
        $ctd++;

        if ($s3cloud_data_filesys[$i]['parent'] !== '-1') continue;

        $item = trim($s3cloud_data_filesys[$i]['path']);
        if (empty($item)) continue;

        if ( substr($item, strlen($item)-1  ) == '/'  )
          $item2 = substr($item, 0, strlen($item)-1 );
        else
          $item2 = $item;

        $json .= '{';
        $json .= '"text":"';
        $json .= $item2;
        $json .= '","icon":""';

        // extra nodes
        $indice_node = array_search($item, array_column($s3cloud_data_filesys, 'path') , true);

        //if($item == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets')
        //   die($item);
        

        //   if(strpos($item , '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css') !== false)
        //    die($item);
        

        if (isset($s3cloud_data_filesys[$indice_node]['nodes'])) $json = s3transfer_create_nodes($indice_node, $s3cloud_data_filesys);

        $json .= '}';

        if ($ctd < $tot - 0)
        {
            $json .= ',';
        }
    } // end for
    

    while (substr($json, -1) == ',')
    {
        //if(substr($json, -1) == ',') {
        $json = substr($json, 0, strlen($json) - 1);
    }
    $json .= ']'; // end node
    $json .= '}';
    // $json .= ']'; // end sub main node
    $json .= ']'; // end MAIN node
    return $json;

} // end function Create Json


if (!isset($indice_node)) $indice_node = '-1';

$json = s3cloud_create_json_filesys($indice_node, $s3cloud_data_filesys);

// $json='[{"text":"Inbox","icon":"","nodes":[{"text":"Office","icon":"fa fa-inbox","nodes":[{"icon":"fa fa-inbox","text":"Customers"},{"icon":"fa fa-inbox","text":"Co-Workers"}]},{"icon":"fa fa-inbox","text":"Others"}]},{"icon":"fa fa-archive","text":"Drafts"},{"icon":"fa fa-calendar","text":"Calendar"},{"icon":"fa fa-address-book","text":"Contacts"},{"icon":"fa fa-trash","text":"Deleted Items"}]';
$json = str_replace("<br>", "", $json);
$json = str_replace(array(
    "\n",
    "\r\n",
    "\r",
    "\t"
) , "", $json);

//$json = '[{"text":"Root","icon":"","nodes":[{"text":"Palmeiras/","icon":"","nodes": [ { "icon": "", "text": "Palmeiras/teste/","nodes": [ { "icon": "", "text": "Palmeiras/teste/lixo/"}]}]},{"text":"calcio/","icon":""},{"text":"lixo/","icon":"","nodes": [ { "icon": "", "text": "lixo/sub_lixo1/"}]},{"text":"palmeiras/","icon":""},{"text":"test/","icon":""},{"text":"teste-sub1/","icon":"","nodes": [ { "icon": "", "text": "teste-sub1/sub-1-sub2/"}]},{"text":"teste-sub2/","icon":"","nodes": [ { "icon": "", "text": "teste-sub2/sub-sub2/"}]},{"text":"teste-sub3/","icon":"","nodes": [ { "icon": "", "text": "teste-sub3/sub-sub3/"}]},{"text":"teste-sub4/","icon":"","nodes": [ { "icon": "", "text": "teste-sub4/sub-sub4/"}]},{"text":"teste-sub5/","icon":""},{"text":"xixi/","icon":""},{"text":"xixi22/","icon":""},{"text":"xixi2233/","icon":""},{"text":"xixi2233333/","icon":""}]}]';


die($json);




// die(json_encode($json));
/*
// Remove items that were added to parents elsewhere:
foreach ($s3cloud_data_filesys as $key => & $item)
{
    if (isset($item['parent']) && isset($itemsByReference[$item['parent']])) unset($s3cloud_data_filesys[$key]);
}
*/

/*
 echo '<pre>';
  var_dump($s3cloud_data_filesys);
echo '</pre>';
*/

/*   END */

/*         FUNCTION  CREATE NODES            */

function s3transfer_create_nodes($indice_node, $s3cloud_data_filesys)
{
    // tem q achar ela na main array e ver se tem nodes...
    global $json;

    if (isset($s3cloud_data_filesys[$indice_node]['nodes']))
    {

        $s3cloud_data_filesys3 = $s3cloud_data_filesys[$indice_node]['nodes'];
        $tot3 = count($s3cloud_data_filesys3);

        if ($tot3 > 0) $json .= ',"nodes": [';

        for ($k = 0;$k < $tot3;$k++)
        {
            $item3 = trim($s3cloud_data_filesys3[$k]);
            $json .= ' {
            "icon": "",
            "text": "' . $item3 . '"';

            // if(strpos($item3 , '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts') !== false)
            // if($item3 == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css')
            //   die($item3);
            

            $indice_node_node = array_search($item3, array_column($s3cloud_data_filesys, 'path') , true);

            /*
            if ($item3 == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/css')
            {

                die(var_export($s3cloud_data_filesys[3]));

                die(var_export($indice_node_node));

            }
            */
            //   die($item3);
            

            if (isset($s3cloud_data_filesys[$indice_node_node]['nodes']))
            {
                // Node has node
                $json = s3transfer_create_nodes($indice_node_node, $s3cloud_data_filesys);
            }

            $json .= '}';

            if ($k < $tot3 - 1)
            {
                $json .= ',';
            }

        } //  end for
        

        if ($tot3 > 0) $json .= ']';

    } // end if tem nodes
    return $json;

} // end function


function s3cloud_fetch_files99999999999999999($dir)
{

    global $s3cloud_filesys_result;
    // global $s3cloud_dir_for_search;

    // die($dir);
    $i = 0;
    $x = scandir($dir);

    // // sort($x, SORT_STRING | SORT_FLAG_CASE);
    /*
    echo '<pre>';
    print_r($x);
    echo '</pre>';
    
    die();
    */

    if (!isset($s3cloud_filesys_result)) $s3cloud_filesys_result = array();

    foreach ($x as $filename)
    {
        if ($filename == '.') continue;
        if ($filename == '..') continue;

        //$s3cloud_filesys_result[] = $dir . $filename;
        $filePath = $dir . $filename;

        if (!is_dir($filePath)) continue;

        if (empty($filePath)) continue;

        if (is_dir($filePath))
        {
            // echo 'found: '.$filePath;
            // echo '<hr>';
            // die($filePath);
            

            if ($i == 0)
            {
                // Novo parente.
                $parent = $dir;

                $parent_for_search = trim(substr($dir, 0, strlen($dir) - 1));

                if ($parent_for_search == substr($s3cloud_dir_for_search, 0, strlen($s3cloud_dir_for_search) - 1))
                {
                    // echo '<<<<<<<< novodir: '.	substr($s3cloud_dir_for_search,0,strlen($s3cloud_dir_for_search)-1);
                    $indice_parent = '-1';
                }
                else
                {

                    //die($parent);
                    

                    // $parent_for_search = trim(substr($dir, 0, strlen($dir) - 1));
                    // die($parent_for_search);
                    //echo '<hr>';
                    //echo 'vou gravar linha: '.$filePath;
                    // echo '<hr>';
                    //echo 'i: ' . $i . '   Parent for search = ' . $parent_for_search;
                    //echo '<hr>';
                    //	if(array_search($parent_for_search, $s3cloud_filesys_result, true) == false) {
                    // 	  echo 'indice id parente '. var_dump(array_search($parent_for_search, $s3cloud_filesys_result, true));
                    //echo 'indice parente '.array_search(substr($dir,0,strlen($dir)-1), array_column($s3cloud_filesys_result, 'uid'));
                    //$key = array_search(40489, array_column($userdb, 'uid'));
                    /////var_dump('>>>> Array count '.count($s3cloud_filesys_result));
                    //die(var_export(count($s3cloud_filesys_result)));
                    if (gettype(count($s3cloud_filesys_result)) == 'integer' and count($s3cloud_filesys_result) > 0)
                    {

                        // if (array_search($parent_for_search, $s3cloud_filesys_result, true) == false)
                        /*
                        if ($parent_for_search == '/home/minozzi/public_html/wp-includes/Requests')
                        {
                        
                          $indice_parent = array_search($parent_for_search, array_column($s3cloud_filesys_result, 'path') , true);
                        
                          echo '<hr>';
                          echo 'indice id parente ' . var_dump($indice_parent);
                        
                          echo '<hr>';
                        
                          echo 'Looking for: ' . $parent_for_search;
                          echo '<br>';
                          echo '<pre>';
                          var_dump($s3cloud_filesys_result);
                          echo '<pre>';
                          echo '<br>';
                          echo 'Resultado: ';
                          echo '<br>';
                          //echo  var_dump(array_search($parent_for_search, $s3cloud_filesys_result, true));
                          echo var_dump(array_search($parent_for_search, array_column($s3cloud_filesys_result, 'path') , true));
                          echo '<hr>';
                          die();
                        
                        }
                        */

                        $indice_parent = array_search($parent_for_search, array_column($s3cloud_filesys_result, 'path') , true);
                        //	echo '<hr>';
                        ////// if(trim($filePath) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome' )
                        /////  die('xxxxxxxxk00000kkkxxxxxxxx' . var_export($s3cloud_filesys_result[$indice_parent]));
                        // die('xxxxxxxxkkkkxxxxxxxx' . var_export($parent_for_search));
                        

                        if ($indice_parent === false)
                        {

                            // die('IP: '.$indice_parent);
                            //die($parent_for_search);
                            echo 'indice id parente ' . var_dump($indice_parent);

                            echo '<hr>';

                            echo 'Looking for: ' . $parent_for_search;
                            echo '<br>';
                            echo '<pre>';
                            // var_dump($s3cloud_filesys_result);
                            echo '<pre>';
                            echo '<br>';
                            echo 'Resultado: ';
                            echo '<br>';
                            //echo  var_dump(array_search($parent_for_search, $s3cloud_filesys_result, true));
                            echo var_dump(array_search($parent_for_search, array_column($s3cloud_filesys_result, 'path') , true));

                            // array_column($people, 'fav_color')
                            //echo 'indice parente '.ar
                            

                            // Bill
                            if (count($s3cloud_filesys_result) == 0) $indice_parent;
                            else die('NAO ACHOU !!!!');

                        }

                        // $indice_parent = array_search($parent_for_search, $s3cloud_filesys_result, true);
                        $indice_parent = array_search($parent_for_search, array_column($s3cloud_filesys_result, 'path') , true);

                        //  if(trim($filePath) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome' )
                        //  die('xxxxxxxxxxxxxxxx' . var_export($parent_for_search));
                        //  if(trim($filePath) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome' )
                        //  die('xxxxxxxxk00000kkkxxxxxxxx' . var_export($s3cloud_filesys_result[$indice_parent]));
                        

                        //echo '<br>';
                        //die('Indice Parent: '.$indice_parent);
                        //echo '<br>';
                        
                    }
                    else
                    {

                        $indice_parent = 0;
                        // die('Indice Parent: 0');
                        
                    }
                }

            } // end I = 0
            

            // echo 'novodir: '.	substr($dir,0,strlen($dir)-1);
            /*
            if ($parent_for_search == substr($s3cloud_dir_for_search, 0, strlen($s3cloud_dir_for_search) - 1))
            {
                // echo '<<<<<<<< novodir: '.	substr($s3cloud_dir_for_search,0,strlen($s3cloud_dir_for_search)-1);
                $indice_parent = '-1';
            }
            else
            {
            
            
            
            }
            */

            //$s3cloud_filesys_result[$i][] = $dir . $filename;
            /*
            echo '<hr>';
            echo 'Found: '.$i . '   ' . $filePath;
            echo '<hr>';
            echo 'Indice Parent: '.$indice_parent;
            echo '<hr>';
            */

            $ctd = count($s3cloud_filesys_result);

            //echo '<pre>'. var_dump($s3cloud_filesys_result);
            // echo '</pre>';
            

            //echo '<<<<< ctd:   '.$ctd.'   ';
            /*
            echo '<pre>';
            var_dump($filePath);
            echo '</pre>';
            */

            // array_push($s3cloud_filesys_result, $ctd, trim($filePath));
            // $s3cloud_filesys_result[] = array(trim($filePath),$indice_parent);
            // array_push($s3cloud_filesys_result, 'id' => array(trim($filePath), 'parent' => $indice_parent));
            

            //if(trim($filePath) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome' )
            //    die(var_export($indice_parent));
            //if(trim($filePath) == '/home/s3cloud/public_html/wp-content/plugins/s3cloud/assets/bootstrap4-glyphicons/fonts/fontawesome' )
            //die('xxxxxxxxk00000kkkxxxxxxxx' . var_export($s3cloud_filesys_result[$indice_parent]));
            

            $s3cloud_filesys_result[] = array(
                'path' => trim($filePath) ,
                'parent' => $indice_parent
            );

            // die(var_export($s3cloud_filesys_result));
            /*
            echo '<br>';
            echo 'gravei: '.trim($filePath). '     '.$indice_parent;
            echo '<br>';
            var_dump('count' .count($s3cloud_filesys_result));
            echo '<br>';
            //var_dump($s3cloud_filesys_result);
            echo '<br>';
            */

            //if($parent_for_search == '/home/minozzi/public_html/wp-admin/css/colors')
            //  die('xxxxx');
            

            /*
            echo '<pre>';
            var_export($s3cloud_filesys_result);
            echo '</pre>';
            die();
            */

            $i++;

            $filePath = $dir . $filename . '/';

            foreach (s3cloud_fetch_files($filePath) as $childFilename)
            {

                // die(var_export($childFilename));
                // die(gettype($childFilename));
                if (gettype($childFilename) === 'object') continue;

                if (!isset($childFilename[0])) continue;

                // die(var_export($childFilename));
                // erro
                // $s3cloud_filesys_result->context;
                // if (!isset($childFilename->0)) continue;
                

                if ($childFilename[0] == '.') continue;
                if ($childFilename[0] == '..') continue;
                //$s3cloud_filesys_result[] = $dir . $filename;
                $filePath2 = $dir . $childFilename[0];

                if (!is_dir($filePath2)) continue;

                if (empty($filePath2)) continue;

                // $s3cloud_filesys_result[$i][] = $childFilename;
                $ctd = count($s3cloud_filesys_result);
                try
                {

                    // array_push($s3cloud_filesys_result, $ctd, $childFilename);
                    //array_push($s3cloud_filesys_result, $ctd, $filePath2);
                    //array_push($s3cloud_filesys_result, array('path' => $filePath2))
                    

                    $s3cloud_filesys_result[] = array(
                        'path' => trim($filePath2) ,
                        'parent' => '999'
                    );

                    //var_dump($childFilename);
                    $i++;

                }
                catch(Exception $e)
                {
                    echo 'Message: ' . $e->getMessage();
                }

            }

        } // end isdir
        //else
        //  $s3cloud_filesys_result[] = $dir . $filename;
        
    } // end for
    // die(var_export($s3cloud_filesys_result));
    return $s3cloud_filesys_result;

} // end function


/*
function sortArray(&$tree){
	foreach ($tree as &$value) {
		if (is_array($value))
			$this->sortArray($value);
	}
	return ksort($tree);
}
*/

