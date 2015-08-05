<form action="{$uri}" method="post" enctype="multipart/form-data">
  <div class="margin-form">
    <fieldset>
      <legend>Wybierz kolor</legend>
      <label> URL: </label>
      <input type="text" name="first_text" value="{$first_url}" >
      <label> Image: </label>
      <input type="file" name="{$first_image}"  style="width:200px"  />
      <input type="submit" name="submit_text" value="Update" class="button" />
    </fieldset>
  </div>
</form>
