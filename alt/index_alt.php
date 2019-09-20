<head>
<meta content="de" http-equiv="Content-Language" />
<meta content="text/html; charset=utf8" http-equiv="Content-Type" />
<title>LogIn</title>
</head>
<!-- used with github -->
<form method="post" action="login.php">
  <label>Jahr:
    <select name="Jahr">
      <option>2019</option>
      <!--<option>2019</option>-->
    </select>
    </label>
  <label>Monat:
    <select name="Monat">
      <option>Januar</option>
      <option>Februar</option>
      <!--<option>Februar</option>
      <option>März</option>
      <option>April</option>
      <option>Mai</option>-->
    </select>
  </label>
  <br>
    <!--<input type="text" name="Monat" placeholder="Monat...">
    <input type="text" name="Jahr" placeholder="Jahr...">-->
    <input type="text" name="firstname" placeholder="Vorname">
    <input type="text" name="lastname" placeholder="Nachname">
    <br>
    <input type="password" name="pw" placeholder="Passwort">
    <p>
    <input type="submit" value="Auswerten" name="submit">
  </p>
</form>
