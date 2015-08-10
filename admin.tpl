<form action="{$uri}" method="post">
  <div class="margin-form">
    <label> First: </label>
    <input type="text" name="first_text" value="{$first_var}" >
    <label> Second: </label>
    <input type="text" name="second_text" value="{$second_var}" >
    <input type="submit" name="submit_text" value="Update" class="button" />
  </div>
</form>

{$saved_json}
