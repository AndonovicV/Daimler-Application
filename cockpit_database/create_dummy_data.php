<?php
require_once("conn.php");
require_once("helper_functions.php");

$script_start_time = microtime(true);  

class ModuleTeam {
    public $name;
    public $shortform;
    public $product_type;
    public $gfts;

    function __construct($name, $shortform, $product_type) {
        $this->name = $name;
        $this->shortform = $shortform;
        $this->product_type = $product_type;
        $this->gfts = [];
    }
}

// class GFT {
//     public $name;

//     function __construct($name) {
//         $this->name = $name;
//     }
// }



$product_types = [
    "Vehicle",
    "Axle",
    "Transmission"
];

$line_functions = [
    "Product Management" => 1,
    "Product Engineering" => 2,
    "Network Management" => 3,
    "Supplier Management" => 4,
    "Manufacturing Engineering" => 5,
    "Cost Engineering" => 6,
    "Procurement" => 7,
    "Controlling" => 8,
    "Quality Management" => 9,
    "Sales & Marketing" => 10,
    "After Sales" => 11
];

$boards = [
    "Product Team" => "Vehicle",
    "Change Management" => "Vehicle"
];

$module_teams_vehicle = [
    new ModuleTeam("Entire Vehicle", "EV", "Vehicle"),
    new ModuleTeam("MT Cab Structure", "CS", "Vehicle"),
    new ModuleTeam("MT Chassis", "CH", "Vehicle"),
    new ModuleTeam("MT Architecture", "AR", "Vehicle"),
    new ModuleTeam("MT Components", "CO", "Vehicle"),
    new ModuleTeam("MT Exterior", "EX", "Vehicle"),
    new ModuleTeam("MT Interior", "IN", "Vehicle"),
    new ModuleTeam("MT Mechatronics", "ME", "Vehicle"),
    new ModuleTeam("MT Thermomanagement", "TM", "Vehicle"),
    // new ModuleTeam("MT Inegration", "IG", "Vehicle"),
    new ModuleTeam("MT Test & Te/st", "TT", "Vehicle")

];

$module_teams_other = [
    new ModuleTeam("MT Axle A", "AA", "Axle"),
    new ModuleTeam("MT Axle B", "AB", "Axle"),
    new ModuleTeam("MT Axle C", "AC", "Axle"),
    new ModuleTeam("MT Axle D", "AD", "Axle"),
    new ModuleTeam("MT Axle E", "AE", "Axle"),
    new ModuleTeam("MT Axle F", "AF", "Axle"),
    new ModuleTeam("MT Axle G", "AG", "Axle"),
    new ModuleTeam("MT Axle H", "AH", "Axle"),
    new ModuleTeam("MT Axle I", "AI", "Axle"),
    new ModuleTeam("MT Axle J", "AJ", "Axle"),
    new ModuleTeam("MT Transmission A", "TA", "Transmission"),
    new ModuleTeam("MT Transmission B", "TB", "Transmission"),
    new ModuleTeam("MT Transmission C", "TC", "Transmission"),
    new ModuleTeam("MT Transmission D", "TD", "Transmission"),
    new ModuleTeam("MT Transmission E", "TE", "Transmission"),
    new ModuleTeam("MT Transmission F", "TF", "Transmission"),
    new ModuleTeam("MT Transmission G", "TG", "Transmission"),
    new ModuleTeam("MT Transmission H", "TH", "Transmission"),
    new ModuleTeam("MT Transmission I", "TI", "Transmission"),
    new ModuleTeam("MT Transmission J", "TJ", "Transmission")

];

$parts = file("parts.txt", FILE_IGNORE_NEW_LINES);
shuffle($parts);

$gfts = [];
$i = 0;
foreach ($module_teams_vehicle as $module_team) {
    for ($j = 0; $j < rand(2,8); $j++) {
        $gft = $module_team->shortform .  $i . $j . " - " . array_pop($parts);
        $module_team->gfts[] = $gft;
    } 
    $i++;
}

$names = file("random_names.txt", FILE_IGNORE_NEW_LINES);
shuffle($names);

$members = [];
$missing_members_percent = 10;
foreach ($boards as $board => $product_type) {
    echo "$board -> $product_type";
    foreach ($line_functions as $function => $ordering) {
        if (rand(1, 100) > $missing_members_percent) {
            $members[] = [array_pop($names), $board, $function, '', 0];
        }
        if (rand(1, 100) > $missing_members_percent) {
            $members[] = [array_pop($names), $board, $function, '', 1];
        }
    }
}
foreach ($module_teams_vehicle as $module_team) {
    foreach ($line_functions as $function => $ordering) {
        if (rand(1, 100) > $missing_members_percent) {
            $members[] = [array_pop($names), $module_team->name, $function, '', 0];
        }
    }
    foreach ($module_team->gfts as $gft) {
        foreach ($line_functions as $function => $ordering) {
            if (rand(1, 100) > $missing_members_percent) {
                $members[] = [array_pop($names), $module_team->name, $function, $gft, 0];
            }
        }
    }
}


// INSERT INTO package(ID, title, product_type_fk, current_phase, status, fasttrack, package_resp_fk, decision, project, lead_modul_team, lead_pif_team, datum_mt, datum_cmt, information, lmtNew)
// Package Stuff

$product_types = [
    "Axle",
    "Transmission",
    "Vehicle",
    "Bus",
    "Engine",
    "Component",
    "Software"
];

$phases = [
    "Initiation",
    "Implementation",
    "Evaluation",
    "Decision"
];

$Stati = [
    "Draft",
    "Approved",
    "In progress",
    "Completed",
    "Rejected",
    "Stopped",
    "Decided",
    "In Cancellation",
    "Cancelled",
    ""
    // "Active"
];

$decisions = [
    "Approved",
    "Conditionally approved",
    "Rejected",
    "Rework necessary in evaluation",
    "Rework necessary in initiation",
    "Cancel Package",
    "Escalated to next decision level"
];

$projects = [
    "01-Series",
    "02-Project",
    "03-Mixed",
    "Axle 1",
    "Axle 2",
    "Axle 3",
    "Bus A",
    "Bus B",
    "Bus C",
    "eActros 1",
    "eActros 2",
    "eActros 3",
    "eAtego 1",
    "eAtego 2",
    "eAtego 3",
    "Powertrain A",
    "Powertrain B",
    "Powertrain C"
];

$packages = [];

for ($year = 16; $year <= 24; $year++) {
    $packages_in_year = rand(1000, 3000);
    for ($i = 1; $i < $packages_in_year; $i++) {

        $package = [];
        $package['id'] = "I" . $year . sprintf("%05d", $i) . "01";

        $percentage_outside_product_type = 50;
        $is_outside_product_type = rand(1,100) <= $percentage_outside_product_type;

        if ($is_outside_product_type) {
            $package_module_team = $module_teams_other[rand(0, sizeof($module_teams_other) - 1)];
            $package['responsible'] = $names[rand(0, sizeof($names) - 1)];
            $package['lead_module_team'] = $package_module_team->name;
            $package['product_type'] = $package_module_team->product_type;
            $package['lead_gft'] = $module_team->shortform .  sprintf("%02d", rand(1, 5)) . " - something";

        } else {
            $package_module_team = $module_teams_vehicle[rand(0, sizeof($module_teams_vehicle) - 1)];
            $package['responsible'] = $members[rand(0, sizeof($members) - 1)][0];
            $package['lead_module_team'] = $package_module_team->name;
            $package['product_type'] = "Vehicle";
            $package['lead_gft'] = $package_module_team->gfts[rand(0, sizeof($package_module_team->gfts) - 1)];
        }

        $package['title'] = "title for " . $package['id'];
        $package['current_phase'] = $phases[rand(0, sizeof($phases) - 1)];
        $package['fasttrack'] = rand(1, 10) <= 1 ? "Yes" : "No";
        $package['project'] = $projects[rand(0, sizeof($projects) - 1)];
        $package['decision'] = $decisions[rand(0, sizeof($decisions) - 1)];
        $package['information'] = "information for " . $package['id'];
        $package['date_mt'] = randomDateInRange(new DateTime(20 . $year . "-01-01"), new DateTime(20 . $year . "-04-30"))->format("d.m.Y");
        $package['date_cmt'] = randomDateInRange(new DateTime(20 . $year . "-05-01"), new DateTime(20 . $year . "-08-31"))->format("d.m.Y");
        $package['start_of_production'] = randomDateInRange(new DateTime(20 . $year . "-09-01"), new DateTime(20 . $year . "-12-31"))->format("d.m.Y");;
        
        $packages[] = $package;
    }
}

$spec_books = [];

$signature_options = [
    "Yes",
    "Delay",
    "NULL"
];

$cis_options = [
    "Delay",
    "Overdue",
    "NULL"
];

for ($i = 0; $i < 50; $i++) {

    $year = rand(2020, 2024);
    $spec_book = [];
    $module_team = $module_teams_vehicle[rand(0, sizeof($module_teams_vehicle) - 1)];
    $spec_book['module_team'] = $module_team->name;
    $spec_book['gft'] = $module_team->gfts[rand(0, sizeof($module_team->gfts) - 1)];
    $spec_book['project'] = $projects[rand(0, sizeof($projects) - 1)];
    $spec_book['component'] = "2-$year-" . sprintf("%05d", $i) . ": something";
    $spec_book['crs_signature'] = randomDateInRange(new DateTime($year . "-01-01"), new DateTime($year . "-12-31"))->format("Y-m-d");
    $spec_book['crs_done'] = $signature_options[rand(0, sizeof($signature_options) -1)];
    $spec_book['supplier_awarding'] = "supplier $i";
    $spec_book['cis_alignment'] = randomDateInRange(new DateTime($year . "-01-01"), new DateTime($year . "-12-31"))->format("Y-m-d");
    $spec_book['cis_done'] = $cis_options[rand(0, sizeof($cis_options) - 1)];
    $spec_book['e_signing_completed'] = rand(1, 100) <= 10 ? '"' . (randomDateInRange(new DateTime($year . "-01-01"), new DateTime())->format("Y-m-d")) . '"' : "NULL";
    $spec_book['comment'] = rand(1, 100) <= 10 ? '"' . (randomDateInRange(new DateTime("2024-01-01"), new DateTime())->format("d.m.Y")) . ': something..."' : "NULL";

    $spec_books[] = $spec_book;
}



// ----- END ------ DATA CREATION -------------------------------------------------


// ----- START ---- DATABASE INSERTIONS -------------------------------------------

// Boards
$table = "org_boards";
$conn->query("TRUNCATE TABLE $table");
$values = [];
foreach ($boards as $board => $product_type) {
    $values[] = '("'.$board.'", "'.$product_type.'")';
}
$sql = "INSERT INTO $table (name, product_type) VALUES " . join(", ", $values);
$conn->query($sql);

// Line Functions 
$table = "org_line_functions";
$conn->query("TRUNCATE TABLE $table");
$values = [];
foreach ($line_functions as $function => $order) {
    $values[] = '("'.$function.'", '.$order.')';
}
$sql = "INSERT INTO $table (function, ordering) VALUES " . join(", ", $values);
$conn->query($sql);

// Module Teams
$table = "org_moduleteams";
$conn->query("TRUNCATE TABLE $table");
$values = [];
foreach ($module_teams_vehicle as $module_team) {
    $values[] = '("'. $module_team->name .'", "'. $module_team->product_type .'")';
}
$sql = "INSERT INTO $table (name, product_type) VALUES " . join(", ", $values);
$conn->query($sql);

// GFTs
$table = "org_gfts";
$conn->query("TRUNCATE TABLE $table");
$values = [];
foreach ($module_teams_vehicle as $module_team) {
    foreach ($module_team->gfts as $gft) {
        $values[] = '("'.$gft.'", "'.$module_team->name.'")';
    }
}
$sql = "INSERT INTO $table (name, moduleteam) VALUES " . join(", ", $values);
$conn->query($sql);

// Members/Roles
$table = "org_members_vehicle";
$conn->query("TRUNCATE TABLE $table");
$values = [];
foreach ($members as $member) {
    // $values[] = '("'.$member[0].'", "'.$module_team->name.'")';
    $values[] = '("' . join('", "', $member) . '")';
}
$sql = "INSERT INTO $table (name, team, line_function, gft, is_nominated_substitute) VALUES " . join(", ", $values);
$conn->query($sql);

// Package
// INSERT INTO package(ID, title, product_type_fk, current_phase, status, fasttrack, package_resp_fk, decision, project, lead_modul_team, lead_pif_team, datum_mt, datum_cmt, information, lmtNew)
// $table = "package";
$conn->query("TRUNCATE TABLE package");
foreach ($packages as $pack) {
        
    $sql = 'INSERT INTO package (
        ID, 
        title, 
        product_type, 
        current_phase, 
        fasttrack, 
        package_responsible, 
        decision, 
        start_of_production, 
        project, 
        lead_module_team, 
        lead_gft, 
        date_mt, 
        date_cmt, 
        information) 
        VALUES ("'.$pack['id'].'", 
        "'.$pack['title'].'", 
        "'.$pack['product_type'].'", 
        "'.$pack['current_phase'].'", 
        "'.$pack['fasttrack'].'", 
        "'.$pack['responsible'].'", 
        "'.$pack['decision'].'", 
        "'.$pack['start_of_production'].'", 
        "'.$pack['project'].'", 
        "'.$pack['lead_module_team'].'", 
        "'.$pack['lead_gft'].'", 
        "'.$pack['date_mt'].'", 
        "'.$pack['date_cmt'].'", 
        "'.$pack['information'].'")';
    $conn->query($sql);
}

//Spec_Books

$conn->query("TRUNCATE TABLE spec_book");
foreach ($spec_books as $spec_book) {
        
    $sql = 'INSERT INTO spec_book (
        Module_Team, 
        GFT, 
        Project, 
        Component, 
        CRS_Signature, 
        CRS_Done, 
        Supplier_Awarding, 
        CIS_Alignment, 
        CIS_Done, 
        E_Signing_Completed,
        Comment
        ) 
        VALUES ("'.$spec_book['module_team'].'", 
        "'.$spec_book['gft'].'", 
        "'.$spec_book['project'].'", 
        "'.$spec_book['component'].'", 
        "'.$spec_book['crs_signature'].'", 
        '. (($spec_book['crs_done'] == "NULL") ? "NULL" : ('"' . $spec_book['crs_done'] . '"')) .', 
        "'.$spec_book['supplier_awarding'].'", 
        "'.$spec_book['cis_alignment'].'", 
        '. (($spec_book['cis_done'] == "NULL") ? "NULL" : ('"' . $spec_book['crs_done'] . '"')) .', 
        '.$spec_book['e_signing_completed'].', 
        '.$spec_book['comment'].')';
    $conn->query($sql);
}


$script_end_time = microtime(true);
$script_execution_time = $script_end_time - $script_start_time;
echo '<br><br><b>create_dummy_data.php Execution Time:</b> '.$script_execution_time.' seconds<br>';

?>