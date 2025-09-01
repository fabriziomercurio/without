<?php 

echo $name . '</br></br>';

foreach ($rows as $key => $value) {
    echo $value['id'] .' '. $value['name'] . '</br>'; 
} 

?> 

<div class="row mt-5">



<form action="product" method="post">
  
<div class="col-md-6">
<div class="mb-3">
  <input type="text" class="form-control mb-2" name="name" placeholder="name"></input>
  <input type="text" class="form-control mb-2" name="surname" placeholder="surname"></input>
  <input type="text" class="form-control mb-2" name="age" placeholder="age"></input>
  <input type="text" class="form-control mb-2" name="city" placeholder="city"></input>
</div>
</div>
<button class="btn btn-success">click</button>
</form>
 
</div>