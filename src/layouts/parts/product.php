<?php 

echo $name . '</br></br>';

foreach ($rows as $key => $value) {
    echo $value['id'] .' '. $value['name'] . '</br>'; 
} 

?>