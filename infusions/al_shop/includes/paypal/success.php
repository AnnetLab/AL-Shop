<?php
require_once "../../../../maincore.php";
require_once THEMES."templates/header.php";
require_once AL_SHOP_DIR."infusion_db.php";

if (!isset($_GET['order']) || !isnum($_GET['order'])) redirect(BASEDIR);

opentable($locale['shp287']);
    echo sprintf($locale['shp288'],$_GET['order']);
closetable();

require_once THEMES."templates/footer.php";
?>