<?php

/*
 *  event.php
 *  
 *  A form which edits an existing event or creates a new event by posting to 
 *  event-exec.php
 */

$redirect_page = "event";
require($_SERVER['DOCUMENT_ROOT'].'/auth/auth.php');

//Ensure that the user is allowed to edit events
if (!auth_edit_events()) {
  echo "You're not allowed to edit events.";
}

// Default selections for the form
$title = "";
$date_year = 2012;
$date_month = 1;
$date_day = 1;
$time_hour = 5;
$time_minute = 30;
$time_ampm = "PM";
$location = "";
$description = "";

//If an event ID was specified, try to load that event
if (isset($_GET['event_id'])) {
  $event_id = intval($_GET['event_id']);

  //Get the event details from the database
  //If no row is found, print an error and exit.
  if (!($row = mysql_fetch_array( mysql_query("SELECT `title`,`date`,TIME_FORMAT(`start_time`, '%H%i%p'),`location`,`description` FROM `events` WHERE `event_id`='$event_id'")))) {
    echo "No such event with that event ID.";
    exit();
  }
  $title = $row['title'];
  $date = explode("-", $row['date']);
  $date_year = intval($date[0]);
  $date_month = intval($date[1]);
  $date_day = intval($date[2]);
  $time_hour = intval(substr($row[2],0,2));
  $time_minute = intval(substr($row[2],2,2));
  $time_ampm = substr($row[2],4,2);
  $location = $row['location'];
  $description = $row['description'];
}
?>

<h1>Create/Edit Event</h1>
<br /><br />
<form action="/events/event-exec.php" method="POST">
<?php if (isset($event_id)) {
  echo '<input type="hidden" name="event_id" value="' . $event_id . '" />'; 
} ?>
  <table>
    <tr>
      <th>Title</th>
      <td><input type="text" name="title" maxlength="255" value="<?php echo $title; ?>" /></td>
    </tr>
    <tr class="alt" >
      <th>Date</th>
      <td>
        <select name="date_day">
          <option value="1" <?php selected(1,$date_day); ?>>1</option>
          <option value="2" <?php selected(2,$date_day); ?>>2</option>
          <option value="3" <?php selected(3,$date_day); ?>>3</option>
          <option value="4" <?php selected(4,$date_day); ?>>4</option>
          <option value="5" <?php selected(5,$date_day); ?>>5</option>
          <option value="6" <?php selected(6,$date_day); ?>>6</option>
          <option value="7" <?php selected(7,$date_day); ?>>7</option>
          <option value="8" <?php selected(8,$date_day); ?>>8</option>
          <option value="9" <?php selected(9,$date_day); ?>>9</option>
          <option value="10" <?php selected(10,$date_day); ?>>10</option>
          <option value="11" <?php selected(11,$date_day); ?>>11</option>
          <option value="12" <?php selected(12,$date_day); ?>>12</option>
          <option value="13" <?php selected(13,$date_day); ?>>13</option>
          <option value="14" <?php selected(14,$date_day); ?>>14</option>
          <option value="15" <?php selected(15,$date_day); ?>>15</option>
          <option value="16" <?php selected(16,$date_day); ?>>16</option>
          <option value="17" <?php selected(17,$date_day); ?>>17</option>
          <option value="18" <?php selected(18,$date_day); ?>>18</option>
          <option value="19" <?php selected(19,$date_day); ?>>19</option>
          <option value="20" <?php selected(20,$date_day); ?>>20</option>
          <option value="21" <?php selected(21,$date_day); ?>>21</option>
          <option value="22" <?php selected(22,$date_day); ?>>22</option>
          <option value="23" <?php selected(23,$date_day); ?>>23</option>
          <option value="24" <?php selected(24,$date_day); ?>>24</option>
          <option value="25" <?php selected(25,$date_day); ?>>25</option>
          <option value="26" <?php selected(26,$date_day); ?>>26</option>
          <option value="27" <?php selected(27,$date_day); ?>>27</option>
          <option value="28" <?php selected(28,$date_day); ?>>28</option>
          <option value="29" <?php selected(29,$date_day); ?>>29</option>
          <option value="30" <?php selected(30,$date_day); ?>>30</option>
          <option value="31" <?php selected(31,$date_day); ?>>31</option>
        </select> / 
        <select name="date_month">
          <option value="1" <?php selected(1,$date_month); ?>>January</option>
          <option value="2" <?php selected(2,$date_month); ?>>February</option>
          <option value="3" <?php selected(3,$date_month); ?>>March</option>
          <option value="4" <?php selected(4,$date_month); ?>>April</option>
          <option value="5" <?php selected(5,$date_month); ?>>May</option>
          <option value="6" <?php selected(6,$date_month); ?>>June</option>
          <option value="7" <?php selected(7,$date_month); ?>>July</option>
          <option value="8" <?php selected(8,$date_month); ?>>August</option>
          <option value="9" <?php selected(9,$date_month); ?>>September</option>
          <option value="10" <?php selected(10,$date_month); ?>>October</option>
          <option value="11" <?php selected(11,$date_month); ?>>November</option>
          <option value="12" <?php selected(12,$date_month); ?>>December</option>
        </select> / 
        <select name="date_year">
          <option value="2012" <?php selected(2012,$date_year); ?>>2012</option>
          <option value="2013" <?php selected(2013,$date_year); ?>>2013</option>
          <option value="2014" <?php selected(2014,$date_year); ?>>2014</option>
          <option value="2015" <?php selected(2015,$date_year); ?>>2015</option>
          <option value="2016" <?php selected(2016,$date_year); ?>>2016</option>
          <option value="2017" <?php selected(2017,$date_year); ?>>2017</option>
          <option value="2018" <?php selected(2018,$date_year); ?>>2018</option>
          <option value="2019" <?php selected(2019,$date_year); ?>>2019</option>
          <option value="2020" <?php selected(2020,$date_year); ?>>2020</option>
          <option value="2021" <?php selected(2021,$date_year); ?>>2021</option>
          <option value="2022" <?php selected(2022,$date_year); ?>>2022</option>
          <option value="2023" <?php selected(2023,$date_year); ?>>2023</option>
          <option value="2024" <?php selected(2024,$date_year); ?>>2024</option>
          <option value="2025" <?php selected(2025,$date_year); ?>>2025</option>
          <option value="2026" <?php selected(2026,$date_year); ?>>2026</option>
          <option value="2027" <?php selected(2027,$date_year); ?>>2027</option>
          <option value="2028" <?php selected(2028,$date_year); ?>>2028</option>
          <option value="2029" <?php selected(2029,$date_year); ?>>2029</option>
          <option value="2030" <?php selected(2030,$date_year); ?>>2030</option>
        </select>
        &nbsp &nbsp
        <input type="checkbox" name="no_date" value="true" /> Leave date blank
      </td>
    </tr>
    <tr>
      <th>Start Time</th>
      <td>
        <select name="time_hour">
          <option value="1" <?php selected(1,$time_hour); ?>>1</option>
          <option value="2" <?php selected(2,$time_hour); ?>>2</option>
          <option value="3" <?php selected(3,$time_hour); ?>>3</option>
          <option value="4" <?php selected(4,$time_hour); ?>>4</option>
          <option value="5" <?php selected(5,$time_hour); ?>>5</option>
          <option value="6" <?php selected(6,$time_hour); ?>>6</option>
          <option value="7" <?php selected(7,$time_hour); ?>>7</option>
          <option value="8" <?php selected(8,$time_hour); ?>>8</option>
          <option value="9" <?php selected(9,$time_hour); ?>>9</option>
          <option value="10" <?php selected(10,$time_hour); ?>>10</option>
          <option value="11" <?php selected(11,$time_hour); ?>>11</option>
          <option value="12" <?php selected(12,$time_hour); ?>>12</option>
        </select> : 
        <select name="time_minute">
          <option value="0" <?php selected(0,$time_minute); ?>>00</option>
          <option value="5" <?php selected(5,$time_minute); ?>>05</option>
          <option value="10" <?php selected(10,$time_minute); ?>>10</option>
          <option value="15" <?php selected(15,$time_minute); ?>>15</option>
          <option value="20" <?php selected(20,$time_minute); ?>>20</option>
          <option value="25" <?php selected(25,$time_minute); ?>>25</option>
          <option value="30" <?php selected(30,$time_minute); ?>>30</option>
          <option value="35" <?php selected(35,$time_minute); ?>>35</option>
          <option value="40" <?php selected(40,$time_minute); ?>>40</option>
          <option value="45" <?php selected(45,$time_minute); ?>>45</option>
          <option value="50" <?php selected(50,$time_minute); ?>>50</option>
          <option value="55" <?php selected(55,$time_minute); ?>>55</option>
        </select>
        <select name="time_ampm">
          <option value="AM" <?php selected("AM",$time_ampm); ?>>AM</option>
          <option value="PM" <?php selected("PM",$time_ampm); ?>>PM</option>
        </select>
        &nbsp &nbsp
        <input type="checkbox" name="no_time" value="true" /> Leave time blank
      </td>
    </tr>
    <tr class="alt" >
      <th>Location</th>
      <td><input type="text" name="location" maxlength="255" value="<?php echo $location; ?>"/></td>
    </tr>
    <tr>
      <th>Description</th>
      <td><textarea name="description" rows="6" cols="80"><?php echo $description; ?></textarea></td>
    </tr>
    <tr>
      <th></th>
      <td style="text-align:center"><input type="submit" value="Create/Update Event" /></td>
    </tr>
  </table>
</form>
<?php if (isset($event_id)) { ?>
<br /><br />
<div class="center">
  <form action="/events/deleteevent-exec.php" method="POST">
    <input type="hidden" name="event_id" value="<?php echo $event_id ?>" />
<?php if (isset($_GET['msg']) && ($_GET['msg'] == "confirmdelete")) { ?>
    <input type="hidden" name="confirm" value="true" />
<?php } ?>
    <input type="submit" value="Delete This Event" />
  </form>
</div>
<?php } ?>
