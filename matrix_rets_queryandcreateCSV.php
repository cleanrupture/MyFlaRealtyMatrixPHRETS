<?php

$rets_login_url = "http://rets.mfrmls.com/contact/rets/login";
$rets_username = "<Matrix RETS username here>";
$rets_password = "<Matrix RETS password here>";

// use http://retsmd.com to help determine the SystemName of the DateTime field which
// designates when a record was last modified
$rets_modtimestamp_field = "MatrixModifiedDT ";

// use http://retsmd.com to help determine the names of the classes you want to pull.
// these might be something like RE_1, RES, RESI, 1, etc.
$property_classes = array("Listing");

// DateTime which is used to determine how far back to retrieve records.
// using a fairly recent data to test the query
$previous_start_time = "2014-01-01T00:00:00";

//////////////////////////////

require_once("phrets.php");

// start rets connection
$rets = new phRETS;

echo "+ Connecting to {$rets_login_url} as {$rets_username}<br>\n";
$connect = $rets->Connect($rets_login_url, $rets_username, $rets_password);

if ($connect) {
        echo "  + Connected<br>\n";
}
else {
        echo "  + Not connected:<br>\n";
        print_r($rets->Error());
        exit;
}

foreach ($property_classes as $class) {

        echo "+ Property:{$class}<br>\n";

        $file_name = strtolower("property_{$class}.csv");
        $fh = fopen($file_name, "w+");

        $maxrows = true;
        $offset = 1;
        $limit = 1000;
        $fields_order = array();

        while ($maxrows) {

                $query = "({$rets_modtimestamp_field}={$previous_start_time}+)";

                // run RETS search
                echo "   + Query: {$query}  Limit: {$limit}  Offset: {$offset}<br>\n";
                
                // notice the inclusion of the UsePost element in the options array....this is what tell phrets to do a 
                // POST not a GET...
                $search = $rets->SearchQuery("Property", $class, $query, array('Limit' => $limit, 'Offset' => $offset, 
                'Format' => 'COMPACT-DECODED', 'Count' => 1, "UsePost" => 1));

                if ($rets->NumRows() > 0) {

                        if ($offset == 1) {
                                // print filename headers as first line
                                $fields_order = $rets->SearchGetFields($search);
                                fputcsv($fh, $fields_order);
                        }

                        // process results
                        while ($record = $rets->FetchRow($search)) {
                                $this_record = array();
                                foreach ($fields_order as $fo) {
                                        $this_record[] = $record[$fo];
                                }
                                fputcsv($fh, $this_record);
                        }

                        $offset = ($offset + $rets->NumRows());

                }

                $maxrows = $rets->IsMaxrowsReached();
                echo "    + Total found: {$rets->TotalRecordsFound()}<br>\n";

                $rets->FreeResult($search);
        }

        fclose($fh);

        echo "  - done<br>\n";

}

echo "+ Disconnecting<br>\n";
$rets->Disconnect();

