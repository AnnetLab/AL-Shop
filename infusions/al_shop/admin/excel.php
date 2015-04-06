<?php

if (isset($_POST['import'])) {

    if (is_uploaded_file($_FILES['import_file']['tmp_name'])) {
        if ($_FILES['import_file']['name'] != '' && $_FILES['import_file']['error'] == '') {

            require_once INCLUDES."infusions_include.php";
            set_time_limit(0);
            $temp_path = AL_SHOP_DIR."asset/temp/";
            $filename = filename_exists(AL_SHOP_DIR."asset/temp/","temp_import".strtolower(strrchr($_FILES['import_file']['name'],".")));


            if (move_uploaded_file($_FILES['import_file']['tmp_name'], $temp_path.$filename)) {

                chmod($temp_path.$filename,0777);

                echo "Wait, importing...";
                require_once AL_SHOP_DIR."includes/import.php";
                redirect(FUSION_SELF.$aidlink."&page=excel");

            } else {
                redirect(FUSION_SELF.$aidlink."&page=excel");
            }

        } else {
            redirect(FUSION_SELF.$aidlink."&page=excel");
        }
    } else {
        redirect(FUSION_SELF.$aidlink."&page=excel");
    }


} else {



    opentable($locale['shp320']);

    $result = dbquery("SELECT * FROM ".DB_AL_SHOP_CATS);
    if (dbrows($result)) {
        echo "<form method='post' action='".AL_SHOP_DIR."includes/export.php'>";
        echo "<table width='100%'>";
        $i = 0;
        echo "<tr><td colspan='4'><input type='checkbox' name='images_as_id' value='yes' id='images_as_id'><label for='images_as_id'>".$locale['shp347']."</label></td></tr>";
        echo "<tr>";
        while ($data = dbarray($result)) {
            if ($i%4==0 && $i!=0) echo "</tr><tr>";
            echo "<td class='tbl' width='25%'><input type='checkbox' class='textbox' name='export_cats[]' value='".$data['shp_cat_id']."' /> ".$data['shp_cat_title']."</td>";
            $i++;
        }
        echo "</tr><tr>";
            echo "<td class='tbl' colspan='4'><input type='submit' name='export' class='button' value='".$locale['shp319']."' /></td>";
        echo "</tr>";
        echo "</table>";
        echo "</form>";
    } else {
        echo $locale['shp32'];
    }

    closetable();


    opentable($locale['shp321']);

        echo "<form method='post' enctype='multipart/form-data'>";
        echo "<table width='100%'>";
        echo "<tr>";
            echo "<td class='tbl' width='250'>".$locale['shp322']."</td>";
            echo "<td class='tbl'><input type='file' name='import_file' class='textbox' /></td>";
        echo "</tr><tr>";
            echo "<td class='tbl'></td><td class='tbl'><input type='submit' name='import' class='button' value='".$locale['shp326']."' /></td>";
        echo "</tr>";
        echo "</table>";
        echo "</form>";

    closetable();

    opentable($locale['shp323']);

    echo "<img src='".AL_SHOP_DIR."asset/excel.jpg' />";
    echo "<h3>".$locale['shp324']."</h3>";
    echo $locale['shp325'];

    closetable();
}

?>
