<?php
require_once "../../../maincore.php";
require_once INFUSIONS."al_shop/infusion_db.php";

if (isset($_POST['export']) && iADMIN && checkrights("SHP")) {

    if (!empty($_POST['export_cats'])) {

        $images_as_ids = isset($_POST['images_as_id']) && $_POST['images_as_id'] == 'yes';

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
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'Title')
                ->setCellValue('C1', 'Category')
                ->setCellValue('D1', 'Description')
                ->setCellValue('E1', 'Manufacturer')
                ->setCellValue('F1', 'Image')
                ->setCellValue('G1', 'Other images')
                ->setCellValue('H1', 'Cost')
                ->setCellValue('I1', 'Currency');

            $i = 2;
            while ($data = dbarray($result)) {

                $images = '';
                if (!empty($data['shp_good_images'])) {
                    if ($images_as_ids) {
                        $images = implode('||', explode('.',$data['shp_good_images']));
                    } else {
                        $images_result = dbquery("SELECT * FROM ".DB_AL_SHOP_IMAGES." WHERE shp_image_id IN (".implode(',',explode('.', $data['shp_good_images'])).")");
                        if (dbrows($images_result)) {
                            while($image = dbarray($images_result)) {
                                $images .= $images ? '||' : '';
                                $images .= $settings['siteurl']."infusions/al_shop/asset/goods/".$image['shp_image_file'];
                            }
                        }
                    }
                }

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $data['shp_good_id'])
                    ->setCellValue('B'.$i, iconv("Windows-1251","UTF-8",$data['shp_good_title']))
                    ->setCellValue('C'.$i, iconv("Windows-1251","UTF-8",$data['shp_cat_title']))
                    ->setCellValue('D'.$i, iconv("Windows-1251","UTF-8",$data['shp_good_desc']))
                    ->setCellValue('E'.$i, iconv("Windows-1251","UTF-8",$data['shp_manufacturer_title']))
                    ->setCellValue('F'.$i, $images_as_ids && $data['shp_good_cover'] > 0 ? $data['shp_good_cover'] : ($data['shp_image_file'] ? $settings['siteurl']."infusions/al_shop/asset/goods/".$data['shp_image_file'] : ""))
                    ->setCellValue('G'.$i, $images)
                    ->setCellValue('H'.$i, $data['shp_good_cost'])
                    ->setCellValue('I'.$i, $data['shp_good_currency']);
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
