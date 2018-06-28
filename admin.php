<?php
error_reporting(E_ALL);
session_status();

require_once ('./include/class.database.php');
$dbobj = new database();

if (isset($_POST['ajax']) && $_POST['ajax'] == "delete") {
   
    $dbobj->deletevalue($_POST);
    echo "success";
    exit;
} elseif (isset($_POST['ajax']) && $_POST['ajax'] == "export") {

    $contacts = array();
    $user_key = $_POST['key']; //"111913759580444214966"; //$_POST['key'];
    $infos = $dbobj->gettabledata();
    if (isset($_POST['list_id'])) {
        $list_id = $_POST['list_id'];
    }
    $user_data = array();
    foreach ($infos as $key => $values) {
        if ($key == $user_key) {
            $user_data = $values;
        }
    }
    if (isset($_POST['list_id']) && !empty($_POST['list_id'])) {
        $contacts = $user_data->records->$list_id;
    } else {
        $contacts = $user_data->records;
    }
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="contacts.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    $output = fopen("php://output", "w");
    fputcsv($output, array('title', 'phoneNumber', 'Email'));
    $data = array();

    foreach ($contacts as $contact) {
        $data = array(
            $contact->title,
            $contact->phone,
            $contact->email,
        );
        fputcsv($output, $data);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link href="//datatables.net/download/build/nightly/jquery.dataTables.css" rel="stylesheet" type="text/css" />
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>	
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

        <meta charset=utf-8 />
        <title>DataTables - JS Bin</title>
        <script type="text/javascript">
            $(document).ready(function () {
                var table = $('#example').DataTable(
                        {
                            dom: 'Bfrtip',
                            buttons: [
                                {
                                    extend: 'csvHtml5',
//                                    <i class="fa fa-file-text-o"></i>
                                    text: 'Export Data',
                                    filename: 'export_data',
                                    exportOptions: {
                                        stripHtml: false,
//                                        columns: [':not(:last-child)',':not(:first-child)']                                        
                                        columns: [1, 2, 3, 4, 5, 6]
                                    }

                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    exportOptions: {
                                        stripHtml: false
                                    }
                                }
                                , 'pdf'
                            ]

                        });
                $(document).on("click", ".delete", function ()
                {
                    var elementval = $(this);
                    var dataval = elementval.attr("dataval");
                    $.ajax({
                        method: "POST",
                        url: "admin.php",
                        data: {ajax: "delete", id: dataval}
                    })
                            .done(function (msg) {
                                table
                                        .row(elementval.parents("tr"))
                                        .remove()
                                        .draw();
                            });
                });
                $(document).on("click", ".export_contacts", function ()
                {
                    var elementval = $(this);
                    var indicator = 0;
                    indicator = elementval.attr("no_data");
                    if (indicator > 0) {
                        elementval.parent("form").submit();
                    }
                });
            });
        </script>
        <style>
            body {font-family: Arial, Helvetica, sans-serif;}

            /* Full-width input fields */
            input[type=text], input[type=password] {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                box-sizing: border-box;
            }

            /* Set a style for all buttons */
            button {
                background-color: #4CAF50;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                cursor: pointer;
                width: 11%;
                font-weight: bold;
            }

            button:hover {
                opacity: 0.8;
            }

            /* Extra styles for the cancel button */
            .cancelbtn {
                width: auto;
                padding: 10px 18px;
                background-color: #f44336;
            }

            /* Center the image and position the close button */
            .imgcontainer {
                text-align: center;
                margin: 24px 0 12px 0;
                position: relative;
            }

            img.avatar {
                width: 40%;
                border-radius: 50%;
            }

            .container {
                padding: 16px;
            }

            span.psw {
                float: right;
                padding-top: 16px;
            }

            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
                padding-top: 60px;
            }

            /* Modal Content/Box */
            .modal-content {
                background-color: #fefefe;
                margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
                border: 1px solid #888;
                width: 80%; /* Could be more or less, depending on screen size */
            }

            /* The Close Button (x) */
            .close {
                position: absolute;
                right: 25px;
                top: 0;
                color: #000;
                font-size: 35px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: red;
                cursor: pointer;
            }

            /* Add Zoom Animation */
            .animate {
                -webkit-animation: animatezoom 0.6s;
                animation: animatezoom 0.6s
            }

            @-webkit-keyframes animatezoom {
                from {-webkit-transform: scale(0)} 
                to {-webkit-transform: scale(1)}
            }

            @keyframes animatezoom {
                from {transform: scale(0)} 
                to {transform: scale(1)}
            }

            /* Change styles for span and cancel button on extra small screens */
            @media screen and (max-width: 300px) {
                span.psw {
                    display: block;
                    float: none;
                }
                .cancelbtn {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>

        <style type="text/css">
            .menu_container{
                margin-top: 26px;
            }
            .menu_container ul{
                list-style-type: none;
                padding: 0;
            }
            .menu_container ul li{

            }
            ul.action_box {                
                list-style-type: none;
            }
            ul.action_box li {                
                display: inline-block;
                margin-left: 5px;
            }
        </style>
        <div class="menu_container container">
            <div class="col-md-8">
                <h1>Welcome admin!!</h1>                   
            </div>               
        </div>
        <div class="container"> 
            <table id="example" class="display" width="100%">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th style="display:none;">Profile</th>
                        <th>Name</th>                        
                        <th>Gender</th>
                        <th>Birthday</th>
                        <th>Email / Phone</th>                                        
                        <th>Records</th>                                        
                        <th> Action</th>                                        
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $infos = $dbobj->gettabledata();
//                     echo "<pre>";
//                     print_r($infos);
//                     exit;
                    foreach ($infos as $key => $info) {
                        $user_list = (isset($info->list_info) ? $info->list_info : array());
                        $info = (array) $info->user;
                        
                        ?>
                        <tr>                           
                            <td><img src="<?php echo !empty($info['image']) ? $info['image'] : "not_found.jpg"; ?>" width="50px" height="50px" /></td>
                            <td style="display:none;"><?php echo $info['image']; ?></td>
                            <td><?php echo $info['displayName']; ?></td>                            
                            <td><?php echo $info['gender']; ?></td>
                            <td><?php echo isset($info['birthday'])?$info['birthday']:''; ?></td>
                            <td><?php echo $info['email']; ?></td>
                            <td><?php echo $info['record_count']; ?></td> 
                            <td>
                                <ul class="action_box">
                                        <li ><a class="delete" dataval="<?php echo $key; ?>" style="cursor: pointer;" >delete</a></li>
                                    <?php
                                    if (isset($user_list) && !empty($user_list)) {
                                        if (empty($user_list)) {
                                            echo "<li>No List Found</li>";
                                        }

                                        foreach ($user_list as $list) {
                                            ?>
                                            <li>
                                                <form class="frm-export" method="post" >
                                                    <input type="hidden" name="ajax" value="export" />
                                                    <input type="hidden" name="key" value="<?php echo $key; ?>" />
                                                    <input type="hidden" name="list_id" value="<?php echo $list->listid; ?>" />
                                                    <a class="export_contacts" dataval="<?php echo $key; ?>" style="cursor: pointer;" no_data="<?php echo $list->list_count; ?>" ><?php echo $list->list_name; ?></a>
                                                </form>

                                            </li>
                                            <?php
                                        }
                                    } else {
                                        if((int)$info['record_count']>0){
                                        ?>
                                        <li>
                                            <form class="frm-export" method="post" >
                                                <input type="hidden" name="ajax" value="export" />
                                                <input type="hidden" name="key" value="<?php echo $key; ?>" />
                                                <a class="export_contacts" dataval="<?php echo $key; ?>" no_data="1" style="cursor: pointer;" >export contacts</a>
                                            </form>

                                        </li>

                                        <?php }} ?>
                                </ul>
                            </td>
                        </tr>     
                    <?php } ?>
                </tbody>                                
            </table>
        </div>

    </body>
</html>