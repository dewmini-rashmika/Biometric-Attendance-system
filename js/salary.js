$(document).ready(function(){
  $(document).on('click', '#btn_calc', function(){
    
    var user_serial = $('#user_select').val();
    var date_from = $('#date_from').val();
    var date_to = $('#date_to').val();
    
    if(user_serial == "" || date_from == "" || date_to == ""){
        alert("Please select a User and the Date Range.");
        return;
    }

    $.ajax({
      url: 'salary_read.php',
      type: 'POST',
      data: {
        'calculate': 1,
        'user_serial': user_serial,
        'date_from': date_from,
        'date_to': date_to
      },
      dataType: 'json',
      success: function(response){
        $('#salary_logs').html(response.table_html);
        $('#salary_output').html(response.summary_html);
        $('#salary_output').fadeIn();
      },
      error: function(xhr, status, error) {
          console.log(xhr.responseText);
          $('#salary_logs').html('<tr><td colspan="5" style="color:red; text-align:center;">Error fetching data.</td></tr>');
      }
    });
  });
});