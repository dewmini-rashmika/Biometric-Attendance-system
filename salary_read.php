<?php
session_start();
require 'connectDB.php';

if (isset($_POST['calculate'])) {
    
    $serial = $_POST['user_serial'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];

    // Get all completed entries within the date range
    $sql = "SELECT * FROM users_logs WHERE serialnumber=? AND checkindate BETWEEN ? AND ? AND timeout != '' ORDER BY checkindate ASC, timein ASC";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo json_encode(["table_html" => "SQL Error", "summary_html" => "Error"]);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "dss", $serial, $date_from, $date_to);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Group data by Date
    $daily_records = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $date = $row['checkindate'];
            $in = new DateTime($row['timein']);
            $out = new DateTime($row['timeout']);
            $interval = $in->diff($out);
            $seconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

            if (!isset($daily_records[$date])) {
                $daily_records[$date] = 0;
            }
            $daily_records[$date] += $seconds;
        }
    }

    $table_rows = "";
    $month_grand_total = 0;
    $month_total_seconds = 0;

    // Process each day independently
    if (count($daily_records) > 0) {
        foreach ($daily_records as $date => $total_daily_seconds) {
            
            $month_total_seconds += $total_daily_seconds;
            
            // Calculate Hours for this day
            $total_hours = $total_daily_seconds / 3600;
            
            $normal_hours = 0;
            $ot_hours = 0;
            
            // Daily Rule: Max 8 hours normal
            if ($total_hours > 8) {
                $normal_hours = 8;
                $ot_hours = $total_hours - 8;
            } else {
                $normal_hours = $total_hours;
                $ot_hours = 0;
            }
            
            // Rates
            $base_rate = 1500 / 8; // 187.5 per hour
            $ot_rate = 1000;       // 1000 per hour
            
            $daily_pay = ($normal_hours * $base_rate) + ($ot_hours * $ot_rate);
            $month_grand_total += $daily_pay;

            // Format for Table Row
            $fmt_total = gmdate("H\h i\m s\s", $total_daily_seconds);
            $fmt_normal = number_format($normal_hours, 2) . " hrs";
            $fmt_ot = ($ot_hours > 0) ? number_format($ot_hours, 2) . " hrs" : "-";
            
            $table_rows .= '<tr>
                                <td>'.$date.'</td>
                                <td>'.$fmt_total.'</td>
                                <td>'.$fmt_normal.'</td>
                                <td>'.$fmt_ot.'</td>
                                <td><strong>Rs. '.number_format($daily_pay, 2).'</strong></td>
                            </tr>';
        }
    } else {
        $table_rows = '<tr><td colspan="5">No completed attendance records found for this period.</td></tr>';
    }

    // Format Monthly Summary
    $mh = floor($month_total_seconds / 3600);
    $mm = floor(($month_total_seconds / 60) % 60);

    $summary_html = "<div>Total Work Hours: <span>{$mh}h {$mm}m</span></div>";
    $summary_html .= "<div style='margin-top:10px; border-top:1px solid #fff; padding-top:5px; font-size:22px;'>MONTHLY SALARY: <span>Rs. ".number_format($month_grand_total, 2)."</span></div>";

    echo json_encode([
        "table_html" => $table_rows,
        "summary_html" => $summary_html
    ]);
    exit();
}
?>