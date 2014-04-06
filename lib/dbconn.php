<?

/* function que devuelve una conexion con la base de datos */
function get_connection () {
   /* conectar a la base de datos */
   $link = mysql_connect('localhost', 'prueba_login', '12test34');
   if (!$link) {
       die('Could not connect: ' . mysql_error());
   }
   /* echo 'Connected successfully'; */

   /* nos conectamos a nuestra base de datos */
   $db_selected = mysql_select_db('prueba_login', $link);
   if (!$db_selected) {
       die ("No se pudo acceder a la base de datos de la aplicación");
   }
   return $link;  
}

function do_query ($query_string, $link) {

   /* ejecutar la sentencia Sql en la base de datos */
   $result = mysql_query($query_string, $link);
   if (!$result) {
        die('Invalid query: ' . mysql_error());
   }

   return $result;
}

function do_new_user_query ($query_string, $link){

   /* ejecutar la sentencia Sql en la base de datos */
   $result = mysql_query($query_string, $link);
   if (!$result) {
        die('El usuario que está intentando crear ya existe');
   }

   return $result;
}

function list_table ($table, $link) {

   /* lista el contenido de una tabla de la base de datos */
   $result = mysql_query('SELECT * FROM ' . $table, $link);
   if (!$result) {
        die('Invalid query: ' . mysql_error());
   }

   return $result;
}

function close_conn ($link) {
   /* close mysql connection */
   mysql_close($link);

   return;
}

function next_row ($query) {
   return mysql_fetch_row($query);
}


?>
