<?php
require_once "../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (isset($_POST['export']) && iADMIN && checkrights("SHP")) {

    if (!empty($_POST['export_cats'])) {

        require_once AL_SHOP_DIR."includes/classes/PHPExcel.php";
        $cats_str = "'".implode("','",$_POST['export_cats'])."'";
        $result = dbquery("SELECT g.*,c.*,m.*,i.* FROM ".DB_AL_SHOP_GOODS." g
                LEFT JOIN ".DB_AL_SHOP_CATS." c ON c.shp_cat_id=g.shp_good_cat
                LEFT JOIN ".DB_AL_SHOP_MANUFACTURES." m ON m.shp_manufacturer_id=g.shp_good_manufacturer
                LEFT JOIN ".DB_AL_SHOP_IMAGES." i ON i.shp_image_id=g.shp_good_cover
                WHERE shp_good_cat IN (".$cats_str.")");

        if (dbrows($result)) {

            $validLocale = PHPExcel_Settings::setLocale('ru');
            $objPHPExcel = new PHPExcel();



            $objPHPExcel->getProperties()->setCreator($settings['sitename'])
                ->setLastModifiedBy($settings['sitename'])
                ->setTitle($settings['sitename']." shop exported data")
                ->setSubject($settings['sitename']." shop exported data")
                ->setDescription($settings['sitename']." shop exported data")
                ->setKeywords($settings['sitename']." shop exported data")
                ->setCategory($settings['sitename']." shop exported data");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Title')
                ->setCellValue('B1', 'Category')
                ->setCellValue('C1', 'Description')
                ->setCellValue('D1', 'Manufacturer')
                ->setCellValue('E1', 'Image')
                ->setCellValue('F1', 'Cost')
                ->setCellValue('G1', 'Currency');

            $i = 2;
            while ($data = dbarray($result)) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, iconv("Windows-1251","UTF-8",$data['shp_good_title']))
                    ->setCellValue('B'.$i, iconv("Windows-1251","UTF-8",$data['shp_cat_title']))
                    ->setCellValue('C'.$i, iconv("Windows-1251","UTF-8",$data['shp_good_desc']))
                    ->setCellValue('D'.$i, iconv("Windows-1251","UTF-8",$data['shp_manufacturer_title']))
                    ->setCellValue('E'.$i, $data['shp_image_file'] ? $settings['siteurl']."infusions/al_shop/asset/goods/".$data['shp_image_file'] : "")
                    ->setCellValue('F'.$i, $data['shp_good_cost'])
                    ->setCellValue('G'.$i, $data['shp_good_currency']);
                $i++;
            }



            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="al_shop_exported.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        } else {
            echo "Nothing to export";
        }


    }

}


?>