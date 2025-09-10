<div class="row mt-5">


<form action="/register" method="post">  
<div class="col-md-4">
<div class="mb-3">
  <input type="text" class="form-control<?php echo isset($firstname) ? " is-invalid " : '' ; ?> mb-2" name="firstname" value="fabrizio" placeholder="fistname"></input>
  <input type="text" class="form-control<?php echo isset($lastname) ? " is-invalid " : '' ; ?>  mb-2" name="lastname" value="mercurio" placeholder="lastname"></input>
  <input type="email" class="form-control mb-2" name="email" value="mercurio.fabrizio@gmail.com" placeholder="email"></input>
  <input type="password" class="form-control mb-2" name="password" value="T4bUcchiolo!" placeholder="password"></input>
  <input type="password" class="form-control mb-2" name="confirmPassword" value="T4bUcchiolo!" placeholder="confirmPassword"></input>
</div>
</div>
<button class="btn btn-success">click</button>
</form>

</div>