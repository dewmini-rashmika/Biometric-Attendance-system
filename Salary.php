<!DOCTYPE html>
<html>
<head>
  <title>Calculate Salary</title>
  <link rel="stylesheet" type="text/css" href="css/manageusers.css">
  <link rel="stylesheet" type="text/css" href="css/Users.css">
  <script src="js/jquery-2.2.3.min.js"></script>
  <script src="js/salary.js"></script>
  <script>
    $(window).on("load resize ", function() {
      var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
      $('.tbl-header').css({'padding-right':scrollWidth});
    }).resize();
  </script>
  <style>
    /* Styling for the Result Box */
    .salary-result {
        margin-top: 20px;
        padding: 15px;
        background-color: #D2B48C;
        color: #fff;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        border-radius: 5px;
        display: none;
    }
    .salary-result span { color: #4a2c2a; font-weight: 900; }

    /* New CSS to make the Date Range look good */
    .date-row {
        display: flex;
        justify-content: space-between;
        gap: 15px; /* Space between the two date inputs */
    }
    .date-group {
        flex: 1; /* Makes both inputs take equal width */
    }
    /* Style the select box to match inputs */
    select#user_select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #e8eeef;
        border-radius: 4px;
        background: #F0EAD6;
        font-family: Georgia, "Times New Roman", Times, serif;
        font-size: 14px;
        color: #000;
    }
  </style>
</head>
<body>
<?php include 'header.php'; ?>
<main>
  <h1 class="slideInDown animated">Monthly Salary Calculator</h1>
  
  <div class="form-style-5 slideInDown animated">
    <form>
      <fieldset>
        <legend><span class="number">S</span> Employee & Period</legend>
        
        <label>Select User:</label>
        <select name="user_select" id="user_select">
            <option value="">-- Choose an Employee --</option>
            <?php
              require 'connectDB.php';
              $sql = "SELECT username, serialnumber FROM users WHERE username != ''";
              $result = mysqli_query($conn, $sql);
              while($row = mysqli_fetch_assoc($result)){
                  echo '<option value="'.$row['serialnumber'].'">'.$row['username'].' ('.$row['serialnumber'].')</option>';
              }
            ?>
        </select>
        
        <div class="date-row">
            <div class="date-group">
                <label>From Date:</label>
                <input type="date" name="date_from" id="date_from">
            </div>
            <div class="date-group">
                <label>To Date:</label>
                <input type="date" name="date_to" id="date_to">
            </div>
        </div>

      </fieldset>
      <button type="button" id="btn_calc">Calculate Salary</button>
    </form>
    
    <div id="salary_output" class="salary-result"></div>
  </div>

  <div class="section">
    <div class="tbl-header slideInRight animated">
      <table cellpadding="0" cellspacing="0" border="0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Total Work Hours</th>
            <th>Normal Hours</th>
            <th>OT Hours</th>
            <th>Daily Pay (Rs)</th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="tbl-content slideInRight animated">
      <table cellpadding="0" cellspacing="0" border="0">
        <tbody id="salary_logs">
            </tbody>
      </table>
    </div>
  </div>
</main>
</body>
</html>